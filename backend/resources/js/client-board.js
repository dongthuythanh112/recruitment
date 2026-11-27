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

const showSaveJobToast = (message, isError = false) => {
    const toast = document.createElement('div');
    toast.className = `fixed right-4 top-4 z-[100] flex max-w-sm items-center gap-3 rounded-xl border px-4 py-3 text-sm font-semibold shadow-xl transition duration-200 ${isError ? 'border-rose-200 bg-white text-rose-800' : 'border-emerald-200 bg-white text-emerald-800'}`;
    toast.setAttribute('role', isError ? 'alert' : 'status');
    toast.setAttribute('aria-live', 'polite');

    const mark = document.createElement('span');
    mark.className = `grid size-8 shrink-0 place-items-center rounded-full ${isError ? 'bg-rose-100' : 'bg-emerald-100'}`;
    mark.textContent = isError ? '!' : '✓';

    const text = document.createElement('span');
    text.textContent = message;
    toast.append(mark, text);
    document.body.append(toast);

    setTimeout(() => {
        toast.classList.add('translate-y-2', 'opacity-0');
        setTimeout(() => toast.remove(), 220);
    }, 2800);
};

document.querySelectorAll('[data-save-job-form]').forEach((form) => {
    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        if (form.dataset.pending === 'true') {
            return;
        }

        const button = form.querySelector('[data-save-job-button]');
        const icon = form.querySelector('[data-save-job-icon]');
        const wasSaved = form.dataset.saved === 'true';
        form.dataset.pending = 'true';
        button.disabled = true;
        button.classList.add('cursor-wait', 'opacity-60');

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                credentials: 'same-origin',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });
            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'Không thể cập nhật việc làm đã lưu.');
            }

            const isSaved = result.saved;
            form.dataset.saved = String(isSaved);
            form.action = isSaved ? form.dataset.destroyUrl : form.dataset.storeUrl;

            const methodField = form.querySelector('[data-save-method]');
            if (isSaved && !methodField) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = '_method';
                input.value = 'DELETE';
                input.dataset.saveMethod = '';
                form.append(input);
            } else if (!isSaved && methodField) {
                methodField.remove();
            }

            button.setAttribute('aria-label', `${isSaved ? 'Bỏ lưu' : 'Lưu'} ${button.getAttribute('aria-label').replace(/^(Bỏ lưu|Lưu) /, '')}`);
            button.title = isSaved ? 'Bỏ lưu việc làm' : 'Lưu việc làm';
            button.classList.toggle('border-rose-200', isSaved);
            button.classList.toggle('bg-rose-50', isSaved);
            button.classList.toggle('text-rose-600', isSaved);
            button.classList.toggle('border-slate-200', !isSaved);
            button.classList.toggle('bg-white', !isSaved);
            button.classList.toggle('text-slate-500', !isSaved);
            icon.setAttribute('fill', isSaved ? 'currentColor' : 'none');

            showSaveJobToast(result.message || (wasSaved ? 'Đã bỏ lưu việc làm.' : 'Đã lưu việc làm.'));
        } catch (error) {
            showSaveJobToast(error.message || 'Không thể cập nhật việc làm đã lưu. Vui lòng thử lại.', true);
        } finally {
            delete form.dataset.pending;
            button.disabled = false;
            button.classList.remove('cursor-wait', 'opacity-60');
        }
    });
});

document.querySelectorAll('[data-industry-carousel]').forEach((carousel) => {
    const pages = [...carousel.querySelectorAll('[data-industry-page]')];
    const section = carousel.closest('section');
    const dots = [...(section?.querySelectorAll('[data-industry-dot]') ?? [])];
    const currentPage = section?.querySelector('[data-industry-current]');

    if (pages.length < 2) {
        return;
    }

    let activePage = 0;
    let timer;
    const interval = Number(carousel.dataset.interval) || 5500;
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const showPage = (index) => {
        const direction = index >= pages.length ? 1 : index < 0 ? -1 : index >= activePage ? 1 : -1;
        activePage = (index + pages.length) % pages.length;

        pages.forEach((page, pageIndex) => {
            const active = pageIndex === activePage;
            page.classList.toggle('hidden', !active);
            page.setAttribute('aria-hidden', String(!active));
        });

        if (!reduceMotion) {
            pages[activePage].animate(
                [
                    { opacity: 0, transform: `translateX(${direction * 18}px)` },
                    { opacity: 1, transform: 'translateX(0)' },
                ],
                { duration: 320, easing: 'ease-out' },
            );
        }

        dots.forEach((dot, dotIndex) => {
            const active = dotIndex === activePage;
            dot.setAttribute('aria-current', String(active));
            dot.classList.toggle('w-7', active);
            dot.classList.toggle('bg-sky-700', active);
            dot.classList.toggle('w-2.5', !active);
            dot.classList.toggle('bg-sky-200', !active);
        });

        if (currentPage) {
            currentPage.textContent = String(activePage + 1);
        }
    };

    const start = () => {
        clearInterval(timer);
        timer = setInterval(() => showPage(activePage + 1), interval);
    };

    section?.querySelector('[data-industry-prev]')?.addEventListener('click', () => {
        showPage(activePage - 1);
        start();
    });

    section?.querySelector('[data-industry-next]')?.addEventListener('click', () => {
        showPage(activePage + 1);
        start();
    });

    dots.forEach((dot, index) => dot.addEventListener('click', () => {
        showPage(index);
        start();
    }));

    section?.addEventListener('mouseenter', () => clearInterval(timer));
    section?.addEventListener('mouseleave', start);
    section?.addEventListener('focusin', () => clearInterval(timer));
    section?.addEventListener('focusout', start);

    start();
});

document.querySelectorAll('[data-company-carousel]').forEach((carousel) => {
    const pages = [...carousel.querySelectorAll('[data-company-page]')];
    const section = carousel.closest('section');
    const dots = [...(section?.querySelectorAll('[data-company-dot]') ?? [])];
    const currentPage = section?.querySelector('[data-company-current]');

    if (pages.length < 2) {
        return;
    }

    let activePage = 0;
    let timer;
    const interval = Number(carousel.dataset.interval) || 5500;
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const showPage = (index) => {
        const direction = index >= pages.length ? 1 : index < 0 ? -1 : index >= activePage ? 1 : -1;
        activePage = (index + pages.length) % pages.length;

        pages.forEach((page, pageIndex) => {
            const active = pageIndex === activePage;
            page.classList.toggle('hidden', !active);
            page.setAttribute('aria-hidden', String(!active));
        });

        if (!reduceMotion) {
            pages[activePage].animate(
                [
                    { opacity: 0, transform: `translateX(${direction * 18}px)` },
                    { opacity: 1, transform: 'translateX(0)' },
                ],
                { duration: 320, easing: 'ease-out' },
            );
        }

        dots.forEach((dot, dotIndex) => {
            const active = dotIndex === activePage;
            dot.setAttribute('aria-current', String(active));
            dot.classList.toggle('w-7', active);
            dot.classList.toggle('bg-sky-700', active);
            dot.classList.toggle('w-2.5', !active);
            dot.classList.toggle('bg-sky-200', !active);
        });

        if (currentPage) {
            currentPage.textContent = String(activePage + 1);
        }
    };

    const start = () => {
        clearInterval(timer);
        timer = setInterval(() => showPage(activePage + 1), interval);
    };

    section?.querySelector('[data-company-prev]')?.addEventListener('click', () => {
        showPage(activePage - 1);
        start();
    });

    section?.querySelector('[data-company-next]')?.addEventListener('click', () => {
        showPage(activePage + 1);
        start();
    });

    dots.forEach((dot, index) => dot.addEventListener('click', () => {
        showPage(index);
        start();
    }));

    section?.addEventListener('mouseenter', () => clearInterval(timer));
    section?.addEventListener('mouseleave', start);
    section?.addEventListener('focusin', () => clearInterval(timer));
    section?.addEventListener('focusout', start);

    start();
});

