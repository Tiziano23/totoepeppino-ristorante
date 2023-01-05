const dragImg = new Image();
dragImg.src = "data:image/gif;base64,R0lGODlhAQABAIAAAAUEBAAAACwAAAAAAQABAAACAkQBADs=";

const mouse = {
    x: 0,
    y: 0,
};

window.addEventListener("DOMContentLoaded", () => {
    const menu = document.getElementById("menu");
    const menuItems = document.querySelectorAll("#menu .menu-item");
    const dropIndicator = menu.querySelector(".drop-indicator");

    let sections = [...document.querySelectorAll(".section")];
    let draggedItem = null;

    menu.addEventListener("dragstart", e => {
        draggedItem = e.target;
        e.dataTransfer.setData("item/id", e.target.getAttribute("data-id"));
        e.dataTransfer.setData("item/element-id", e.target.id);
        e.dataTransfer.setDragImage(dragImg, 0, 0);
        dropIndicator.removeAttribute("disabled");
    });
    window.addEventListener("dragover", e => {
        if (draggedItem) {
            draggedItem.classList.add("dragging");
            draggedItem.style.setProperty("--mouse-x", e.clientX + "px");
            draggedItem.style.setProperty("--mouse-y", e.clientY + "px");
        }
    });
    window.addEventListener("dragend", e => {
        if (draggedItem) {
            draggedItem.classList.remove("dragging");
            draggedItem = null;
        }
        dropIndicator.setAttribute("disabled", true);
    });

    // ====================================================================

    sections.forEach(section => {
        const sectionId = section.getAttribute("data-id");

        const nameInput = section.querySelector("input[data-model=name]");
        const colorInput = section.querySelector("input[data-model=color]");
        const btnEdit = section.querySelector(".btn-edit");
        const btnPrev = section.querySelector(".btn-prev");
        const btnNext = section.querySelector(".btn-next");
        const btnDel = section.querySelector(".btn-del");

        nameInput.addEventListener("change", () => {
            updateValues("category", sectionId, [["name", nameInput.value]]);
        });
        window.addEventListener(
            "click",
            e => {
                if (!nameInput.disabled && e.target != nameInput && e.target != btnEdit) {
                    nameInput.disabled = true;
                    changeIcon(btnEdit, "fa-check", "fa-pencil");
                }
            },
            { passive: true }
        );

        colorInput.addEventListener("change", e => {
            updateValues("category", sectionId, [["color", String(colorInput.value).replace("#", "")]]);
        });

        btnEdit.addEventListener("click", () => {
            if (nameInput.disabled) {
                nameInput.disabled = false;
                nameInput.focus();
                nameInput.setSelectionRange(nameInput.value.length, nameInput.value.length);
                changeIcon(btnEdit, "fa-pencil", "fa-check");
            } else {
                nameInput.disabled = true;
                changeIcon(btnEdit, "fa-check", "fa-pencil");
            }
        });
        btnPrev.addEventListener("click", () => {
            const sectionIdx = sections.findIndex(el => el.getAttribute("data-id") == sectionId);
            if (sectionIdx >= 1) {
                menu.insertBefore(section, sections[sectionIdx - 1]);
                updateCategoriesOrder();
                sections = [...document.querySelectorAll(".section")];
            }
        });
        btnNext.addEventListener("click", () => {
            const sectionIdx = sections.findIndex(el => el.getAttribute("data-id") == sectionId);
            if (sectionIdx < sections.length - 1) {
                menu.insertBefore(section, sections[sectionIdx + 2]);
                updateCategoriesOrder();
                sections = [...document.querySelectorAll(".section")];
            }
        });
        btnDel.addEventListener("click", async () => {
            let id = section.getAttribute("data-id");
            let res = await fetch(`/api/category/${id}/delete`, {
                method: "GET",
                cache: "no-cache",
            });
            if (res.ok) section.remove();
            sections = [...document.querySelectorAll(".section")];
        });

        const subsectionsContainer = section.querySelector(".subsections");
        let subsections = [...section.querySelectorAll(".subsection")];
        subsections.forEach(subsection => {
            const subsectionId = subsection.getAttribute("data-id");
            const itemsContainer = subsection.querySelector(".items");

            if (subsectionId != -1) {
                const nameInput = subsection.querySelector("input[data-model=name]");
                const btnEdit = subsection.querySelector(".btn-edit");
                const btnPrev = subsection.querySelector(".btn-prev");
                const btnNext = subsection.querySelector(".btn-next");
                const btnDel = subsection.querySelector(".btn-del");

                nameInput.addEventListener("change", () => {
                    updateValues("subcategory", subsectionId, [["name", nameInput.value]]);
                });
                window.addEventListener(
                    "click",
                    e => {
                        if (!nameInput.disabled && e.target != nameInput && e.target != btnEdit) {
                            nameInput.disabled = true;
                            changeIcon(btnEdit, "fa-check", "fa-pencil");
                        }
                    },
                    { passive: true }
                );

                btnEdit.addEventListener("click", () => {
                    if (nameInput.disabled) {
                        nameInput.disabled = false;
                        nameInput.focus();
                        nameInput.setSelectionRange(nameInput.value.length, nameInput.value.length);
                        changeIcon(btnEdit, "fa-pencil", "fa-check");
                    } else {
                        nameInput.disabled = true;
                        changeIcon(btnEdit, "fa-check", "fa-pencil");
                    }
                });
                btnPrev.addEventListener("click", () => {
                    const subsectionIdx = subsections.findIndex(el => el.getAttribute("data-id") == subsectionId);
                    if (subsectionIdx >= 1) {
                        subsectionsContainer.insertBefore(subsection, subsections[subsectionIdx - 1]);
                        updateSubcategoriesOrder(section);
                        subsections = [...section.querySelectorAll(".subsection:not([data-id='-1'])")];
                    }
                });
                btnNext.addEventListener("click", () => {
                    const subsectionIdx = subsections.findIndex(el => el.getAttribute("data-id") == subsectionId);
                    if (subsectionIdx < subsections.length - 1) {
                        subsectionsContainer.insertBefore(subsection, subsections[subsectionIdx + 2]);
                        updateSubcategoriesOrder(section);
                        subsections = [...section.querySelectorAll(".subsection:not([data-id='-1'])")];
                    }
                });
                btnDel.addEventListener("click", async () => {
                    let id = subsection.getAttribute("data-id");
                    let res = await fetch(`/api/subcategory/${id}/delete`, {
                        method: "GET",
                        cache: "no-cache",
                    });
                    if (res.ok) subsection.remove();
                    subsections = [...section.querySelectorAll(".subsection:not([data-id='-1'])")];
                });
            }

            subsection.addEventListener("dragenter", e => {
                e.preventDefault();
                if (![...itemsContainer.children].includes(dropIndicator)) itemsContainer.appendChild(dropIndicator);
            });
            subsection.addEventListener("dragover", e => {
                e.preventDefault();
                let mouseY = e.clientY;
                let items = [...subsection.querySelectorAll(".menu-item:not(.dragging)")];

                if (items.length > 0) {
                    let nextItem = null;
                    let bboxFirst = items[0].getBoundingClientRect();
                    let bboxLast = items[items.length - 1].getBoundingClientRect();
                    if (mouseY < bboxFirst.top + bboxFirst.height / 2) nextItem = items[0];
                    else if (mouseY > bboxFirst.top + bboxFirst.height / 2) {
                        for (let i = 1; i < items.length; i++) {
                            let bbox1 = items[i - 1].getBoundingClientRect();
                            let bbox2 = items[i].getBoundingClientRect();
                            if (mouseY > bbox1.top + bbox1.height / 2 && mouseY < bbox2.top + bbox2.height / 2) nextItem = items[i];
                        }
                    }
                    if (nextItem) itemsContainer.insertBefore(dropIndicator, nextItem);
                    else if (mouseY > bboxLast.top + bboxLast.height / 2) itemsContainer.appendChild(dropIndicator);
                }
            });
            subsection.addEventListener("drop", async e => {
                e.preventDefault();
                let id = e.dataTransfer.getData("item/id");
                let elemId = e.dataTransfer.getData("item/element-id");
                dropIndicator.setAttribute("disabled", true);

                if (id && elemId && sectionId) {
                    let node = document.getElementById(elemId);
                    itemsContainer.insertBefore(node, dropIndicator);
                    menu.appendChild(dropIndicator);

                    await fetch(`/api/entry/${id}/update`, {
                        method: "POST",
                        cache: "no-cache",
                        body: new URLSearchParams([
                            ["id", id],
                            ["category_id", sectionId],
                            ["subcategory_id", subsectionId],
                        ]),
                    });
                    updateItemsOrder();
                }
            });
        });
    });

    menuItems.forEach(item => {
        const itemId = item.getAttribute("data-id");
        const editBtn = item.querySelector(".edit-btn");
        const deleteBtn = item.querySelector(".delete-btn");

        const titleInput = item.querySelector("[data-model=title]");
        const descrInput = item.querySelector("[data-model=descr]");
        const priceInput = item.querySelector("[data-model=price]");

        editBtn.addEventListener("click", () => {
            if (titleInput.disabled) {
                titleInput.disabled = false;
                descrInput.disabled = false;
                priceInput.disabled = false;
                changeIcon(editBtn.children[0], "fa-pencil", "fa-check");
            } else {
                titleInput.disabled = true;
                descrInput.disabled = true;
                priceInput.disabled = true;
                updateValues("entry", itemId, [
                    ["title", titleInput.value],
                    ["descr", descrInput.value],
                    ["price", priceInput.value],
                ]);
                changeIcon(editBtn.children[0], "fa-check", "fa-pencil");
            }
        });
        deleteBtn.addEventListener("click", async () => {
            let res = await fetch(`/api/entry/${itemId}/delete`);
            if (res.ok) item.remove();
        });
    });

    // ====================================================================

    const actions = {};
    document.querySelectorAll("form[data-menu-action]").forEach(el => {
        let actionId = el.getAttribute("data-menu-action");
        actions[actionId] = { ...actions[actionId] };
        actions[actionId].form = el;
    });
    document.querySelectorAll("button[data-menu-action]").forEach(el => {
        let actionId = el.getAttribute("data-menu-action");
        actions[actionId] = { ...actions[actionId] };
        actions[actionId].button = el;
    });
    for (let aId in actions) {
        let action = actions[aId];
        let icon = action.button.querySelector("i");
        let text = action.button.querySelector("span");
        action.enabled = false;
        action.enable = () => {
            action.enabled = true;
            action.form.reset();
            action.form.removeAttribute("disabled");
            action.form.elements[0].focus();
            icon.classList.replace("fa-plus", "fa-times");
            text.innerHTML = "Annulla";
            document.documentElement.scrollTo(0, document.body.scrollHeight);
        };
        action.disable = () => {
            action.enabled = false;
            action.form.setAttribute("disabled", true);
            icon.classList.replace("fa-times", "fa-plus");
            text.innerHTML = text.getAttribute("data-message");
        };
        action.toggle = () => {
            if (action.enabled) action.disable();
            else action.enable();
        };
        action.button.addEventListener("click", () => {
            action.toggle();
            for (let otherAId in actions) {
                if (otherAId != aId) actions[otherAId].disable();
            }
        });
        action.form.addEventListener('keydown', e => {
          if (document.activeElement.nodeName != 'TEXTAREA' && e.key == "Enter") action.form.requestSubmit();
        });
    }

    // ====================================================================

    [...document.forms].forEach(form => {
        let dynamicSelects = [...form.querySelectorAll("select[data-model]")];
        for (let s of dynamicSelects) {
            s.addEventListener("focus", async () => {
                let model = s.getAttribute("data-model");
                let key = s.getAttribute("data-key");
                let refKey = s.getAttribute("data-reference");

                let options = [];

                if (!refKey) options = await getModelData(model);
                else if (form.elements.namedItem(refKey).value) options = await getModelData(model, { key: refKey, value: form.elements.namedItem(refKey).value });

                s.querySelectorAll("option:not([value=''])").forEach(el => el.remove());
                for (let option of options) {
                    let optEl = document.createElement("option");
                    optEl.value = option.id;
                    optEl.innerText = option[key];
                    s.appendChild(optEl);
                }
            });
        }
    });
});

function getCategoriesOrder() {
    let order = {};
    document.querySelectorAll(".section").forEach((el, idx) => {
        order[Number(el.getAttribute("data-id"))] = idx;
    });
    return order;
}
function getSubcategoriesOrder(section) {
    let order = {};
    section.querySelectorAll(".subsection").forEach((el, idx) => {
        order[Number(el.getAttribute("data-id"))] = idx;
    });
    return order;
}
function getItemsOrder() {
    let order = {};
    document.querySelectorAll(".menu-item").forEach((el, idx) => {
        order[Number(el.getAttribute("data-id"))] = idx;
    });
    return order;
}

function updateCategoriesOrder() {
    fetch("/api/category/sort/", {
        method: "POST",
        cache: "no-cache",
        body: new URLSearchParams([["order", JSON.stringify(getCategoriesOrder())]]),
    });
}
function updateSubcategoriesOrder(section) {
    fetch("/api/subcategory/sort/", {
        method: "POST",
        cache: "no-cache",
        body: new URLSearchParams([["order", JSON.stringify(getSubcategoriesOrder(section))]]),
    });
}
function updateItemsOrder() {
    fetch("/api/entry/sort/", {
        method: "POST",
        cache: "no-cache",
        body: new URLSearchParams([["order", JSON.stringify(getItemsOrder())]]),
    });
}

function updateValues(model, id, payload) {
    fetch(`/api/${model}/${id}/update`, {
        method: "POST",
        cache: "no-cache",
        body: new URLSearchParams(payload),
    });
}

async function getModelData(model, reference = null) {
    let res;
    if (reference) res = await fetch(`/api/${model}/?${reference.key}=${reference.value}`);
    else res = await fetch(`/api/${model}/`);
    return await res.json();
}

function changeIcon(el, prevIcon, newIcon) {
    let frames = [{ opacity: 1 }, { opacity: 0 }];
    let effectTo = new KeyframeEffect(el, frames, {
        duration: 150,
        fill: "both",
    });
    let effectFrom = new KeyframeEffect(el, frames, {
        duration: 150,
        direction: "reverse",
        fill: "both",
    });

    let animTo = new Animation(effectTo, document.timeline);
    let animFrom = new Animation(effectFrom, document.timeline);
    animTo.onfinish = () => {
        el.classList.replace(prevIcon, newIcon);
        animFrom.play();
    };
    animTo.play();
}
