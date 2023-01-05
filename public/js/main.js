const menu = document.getElementById("menu");
const pages = [...document.querySelectorAll("#menu .section")];
const entries = [...document.querySelectorAll('.menu-entry')];
const indicator = document.querySelector('.page-indicator');
const ellipses = [...indicator.querySelectorAll('.ellipse')];

const swipeRadius = 70;
const scrollThreshold = 25;

let startPos;
let currentPage = 0;
const td = {
    x: null,
    y: null,
    dx: null,
    dy: null,
    startX: null,
    startY: null,
    gesture: null
};

menu.addEventListener('touchstart', e => {
    td.startX = e.touches[0].clientX;
    td.startY = e.touches[0].clientY;
    td.x = e.touches[0].clientX;
    td.y = e.touches[0].clientY;
    td.dx = null;
    td.dy = null;
    td.gesture = null;
    startPos = menu.scrollLeft;
}, { passive: true });
menu.addEventListener('touchmove', e => {
    td.dx = e.touches[0].clientX - td.x;
    td.dy = e.touches[0].clientY - td.y;
    td.x = e.touches[0].clientX;
    td.y = e.touches[0].clientY;

    let distX = (td.x - td.startX);
    let distY = (td.y - td.startY);

    if (Math.abs(distX) > scrollThreshold && Math.abs(distY) < 150) {
        menu.scrollLeft = startPos - distX;
        if (distX > swipeRadius) td.gesture = '+swipe';
        else if (distX < -swipeRadius) td.gesture = '-swipe';
    }
}, { passive: true });
menu.addEventListener('touchend', e => {
    if (td.gesture) pages[currentPage].scrollTop = 0;
    if (td.gesture == '+swipe' && currentPage >= 1) currentPage--;
    else if (td.gesture == '-swipe' && currentPage < pages.length - 1) currentPage++;
    updateIndicator();
    menu.scrollTo({
        left: currentPage * window.innerWidth,
        behavior: "smooth"
    });
});
menu.addEventListener("touchcancel", e => {
    menu.scrollTo({
        left: currentPage * window.innerWidth,
        behavior: "smooth"
    });
});

function updateIndicator() {
    ellipses.forEach(el => el.classList.remove('active'));
    ellipses[currentPage].classList.add('active');
}

window.addEventListener('DOMContentLoaded', () => {
    for (let e of entries) {
        e.style.setProperty('--height', `${e.clientHeight}px`);
    }
});