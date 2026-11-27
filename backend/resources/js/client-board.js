// Bảng việc làm: lật chữ như bảng giờ tàu khi tải trang và khi rê chuột, đồng hồ giờ Việt Nam.
const GLYPHS = 'ABCDEĐGHIKLMNOPQRSTUVXY0123456789';
const FIRST_LOAD_ROWS = 8;
const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

document.querySelectorAll('[data-toast]').forEach((toast) => {
    let timeout;

    const dismiss = () => {
        clearTimeout(timeout);
        toast.classList.add('toast-out');
        setTimeout(() => toast.remove(), reduceMotion ? 0 : 250);
    };

    const scheduleDismiss = () => {
        clearTimeout(timeout);
        timeout = setTimeout(dismiss, 5000);
    };

    toast.querySelector('[data-toast-close]')?.addEventListener('click', dismiss);
    toast.addEventListener('mouseenter', () => clearTimeout(timeout));
    toast.addEventListener('mouseleave', scheduleDismiss);
    scheduleDismiss();
});

function flip(element, delay = 0) {
    if (element.dataset.flipping) {
        return;
    }

    element.dataset.flipping = '1';
    const text = element.textContent;
    const chars = Array.from(text);
    const start = performance.now() + delay;

    const timer = setInterval(() => {
        const elapsed = performance.now() - start;

        if (elapsed >= chars.length * 24 + 220) {
            clearInterval(timer);
            element.textContent = text;
            delete element.dataset.flipping;

            return;
        }

        if (elapsed >= 0) {
            element.textContent = chars
                .map((char, index) => (char === ' ' || elapsed >= index * 24 + 220 ? char : GLYPHS[Math.floor(Math.random() * GLYPHS.length)]))
                .join('');
        }
    }, 50);
}

if (!reduceMotion) {
    document.querySelectorAll('[data-flap]:not([data-board-row] *)').forEach((element) => flip(element));

    document.querySelectorAll('[data-board-row]').forEach((row, rowIndex) => {
        if (rowIndex < FIRST_LOAD_ROWS) {
            row.querySelectorAll('[data-flap]').forEach((element) => flip(element, rowIndex * 70));
        }

        const title = row.querySelector('[data-flap-title]');
        row.addEventListener('pointerenter', (event) => {
            if (event.pointerType === 'mouse') {
                flip(title);
            }
        });
    });
}

const clock = document.querySelector('[data-clock]');

if (clock) {
    const format = new Intl.DateTimeFormat('vi-VN', { hour: '2-digit', minute: '2-digit', hour12: false, timeZone: 'Asia/Ho_Chi_Minh' });
    const tick = () => {
        clock.textContent = format.format(new Date());
    };

    tick();
    setInterval(tick, 10000);
}

