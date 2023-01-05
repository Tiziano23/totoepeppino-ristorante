[...document.forms].forEach(form => {
    let dynamicSelects = [...form.querySelectorAll("select[data-model]")];
    for (let s of dynamicSelects) {
        s.addEventListener("focus", async () => {
            let model = s.getAttribute("data-model");
            let key = s.getAttribute("data-key");
            let options = await getModelData(model);

            s.querySelectorAll("option:not([value=''])").forEach(el => el.remove());
            for (let option of options) {
                let optEl = document.createElement("option");
                optEl.value = option[key];
                optEl.innerText = option[key];
                s.appendChild(optEl);
            }
        });
    }
});

const table = document.getElementById("users-table");
const rows = [...table.querySelectorAll("tr.user")];
for (let row of rows) {
    const uid = row.getAttribute("data-uid");
    row.querySelector("button").addEventListener("click", async e => {
        let res = await fetch(`/account/${uid}/delete`);
        if (res.ok) table.tBodies[0].removeChild(row);
    });
}

async function getModelData(model, reference = null) {
    let res;
    if (reference) res = await fetch(`/api/${model}/?${reference.key}=${reference.value}`);
    else res = await fetch(`/api/${model}/`);
    return await res.json();
}