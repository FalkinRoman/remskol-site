(() => {
    'use strict';

    const $ = (s, root = document) => root.querySelector(s);
    const $$ = (s, root = document) => [...root.querySelectorAll(s)];
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const goal = (name) => {
        if (window.ym && window.SITE_METRIKA_ID) window.ym(window.SITE_METRIKA_ID, 'reachGoal', name);
    };

    let locks = 0;
    const lockScroll = (on) => {
        locks = Math.max(0, locks + (on ? 1 : -1));
        document.body.classList.toggle('is-locked', locks > 0);
    };

    /* ================= Sticky header ================= */
    const header = $('.header');
    if (header && 'IntersectionObserver' in window) {
        const sentinel = document.createElement('div');
        sentinel.setAttribute('aria-hidden', 'true');
        sentinel.style.cssText = 'position:absolute;top:0;left:0;width:1px;height:1px;pointer-events:none';
        document.body.prepend(sentinel);
        new IntersectionObserver(([e]) => header.classList.toggle('is-stuck', !e.isIntersecting)).observe(sentinel);
    }

    /* ================= Drawer ================= */
    const drawer = $('[data-drawer]');
    const burger = $('[data-drawer-open]');
    let lastFocus = null;

    const setDrawer = (open) => {
        if (!drawer || drawer.classList.contains('is-open') === open) return;
        drawer.classList.toggle('is-open', open);
        drawer.setAttribute('aria-hidden', String(!open));
        burger.setAttribute('aria-expanded', String(open));
        burger.setAttribute('aria-label', open ? 'Закрыть меню' : 'Открыть меню');
        lockScroll(open);
        if (open) {
            lastFocus = document.activeElement;
            setTimeout(() => $('.drawer__close', drawer).focus(), 50);
        } else if (lastFocus) {
            lastFocus.focus({ preventScroll: true });
        }
    };

    if (drawer) {
        burger.addEventListener('click', () => setDrawer(true));
        $$('[data-drawer-close]', drawer).forEach((el) => el.addEventListener('click', () => setDrawer(false)));
        drawer.addEventListener('click', (e) => {
            if (e.target.closest('a')) setDrawer(false);
        });
        $$('[data-submenu-toggle]', drawer).forEach((btn) => btn.addEventListener('click', () => {
            btn.setAttribute('aria-expanded', String(btn.getAttribute('aria-expanded') !== 'true'));
        }));
        drawer.addEventListener('keydown', (e) => {
            if (e.key !== 'Tab') return;
            const items = $$('a[href], button:not([disabled])', $('.drawer__panel', drawer)).filter((el) => el.offsetParent);
            const first = items[0];
            const last = items[items.length - 1];
            if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
            else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
        });
        window.matchMedia('(min-width: 981px)').addEventListener('change', (e) => { if (e.matches) setDrawer(false); });
    }

    /* ================= Slider ================= */
    const slider = $('[data-slider]');
    if (slider) {
        const slides = $$('[data-slide]', slider);
        const dots = $$('[data-slider-dot]', slider);
        const delay = parseInt(getComputedStyle(slider).getPropertyValue('--slider-delay'), 10) || 6000;
        let index = 0;
        let timer = null;
        let startedAt = 0;
        let remaining = delay;
        let paused = false;
        const autoplay = !reducedMotion && slides.length > 1;

        if (!autoplay) slider.classList.add('no-autoplay');

        const restartDot = (dot) => {
            const bar = dot.querySelector('span');
            bar.style.animation = 'none';
            void bar.offsetWidth;
            bar.style.animation = '';
        };

        const schedule = (ms) => {
            clearTimeout(timer);
            if (!autoplay || paused) return;
            startedAt = Date.now();
            remaining = ms;
            timer = setTimeout(() => go(index + 1), ms);
        };

        const go = (n) => {
            const next = (n + slides.length) % slides.length;
            if (next !== index) {
                slides[index].classList.remove('is-active');
                slides[index].setAttribute('aria-hidden', 'true');
                $$('a, button', slides[index]).forEach((el) => el.setAttribute('tabindex', '-1'));
                dots[index].classList.remove('is-active');
                dots[index].setAttribute('aria-selected', 'false');

                index = next;
                slides[index].classList.add('is-active');
                slides[index].removeAttribute('aria-hidden');
                $$('a, button', slides[index]).forEach((el) => el.removeAttribute('tabindex'));
                dots[index].classList.add('is-active');
                dots[index].setAttribute('aria-selected', 'true');
            }
            restartDot(dots[index]);
            schedule(delay);
        };

        const pause = () => {
            if (paused || !autoplay) return;
            paused = true;
            slider.classList.add('is-paused');
            clearTimeout(timer);
            remaining = Math.max(400, remaining - (Date.now() - startedAt));
        };
        const resume = () => {
            if (!paused) return;
            paused = false;
            slider.classList.remove('is-paused');
            schedule(remaining);
        };

        $('[data-slider-prev]', slider).addEventListener('click', () => go(index - 1));
        $('[data-slider-next]', slider).addEventListener('click', () => go(index + 1));
        dots.forEach((dot) => dot.addEventListener('click', () => go(Number(dot.dataset.sliderDot))));

        slider.addEventListener('mouseenter', pause);
        slider.addEventListener('mouseleave', resume);
        slider.addEventListener('focusin', pause);
        slider.addEventListener('focusout', (e) => { if (!slider.contains(e.relatedTarget)) resume(); });
        document.addEventListener('visibilitychange', () => (document.hidden ? pause() : resume()));
        slider.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') go(index - 1);
            if (e.key === 'ArrowRight') go(index + 1);
        });

        // Свайп: горизонтальный жест листает, вертикальный — скроллит страницу (touch-action: pan-y).
        let sx = 0, sy = 0, tracking = false;
        slider.addEventListener('pointerdown', (e) => {
            if (e.pointerType === 'mouse' && e.button !== 0) return;
            tracking = true; sx = e.clientX; sy = e.clientY;
        });
        slider.addEventListener('pointerup', (e) => {
            if (!tracking) return;
            tracking = false;
            const dx = e.clientX - sx;
            const dy = e.clientY - sy;
            if (Math.abs(dx) > 45 && Math.abs(dx) > Math.abs(dy) * 1.2) {
                go(index + (dx < 0 ? 1 : -1));
                if (e.pointerType === 'mouse') slider.addEventListener('click', (ev) => ev.preventDefault(), { once: true, capture: true });
            }
        });
        slider.addEventListener('pointercancel', () => { tracking = false; });

        schedule(delay);
    }

    /* ================= FAB ================= */
    const fab = $('[data-fab]');
    if (fab) {
        const toggle = $('[data-fab-toggle]', fab);
        const setFab = (open) => {
            fab.classList.toggle('is-open', open);
            toggle.setAttribute('aria-expanded', String(open));
            toggle.setAttribute('aria-label', open ? 'Закрыть' : 'Связаться с нами');
            if (open) goal('fab_open');
        };
        toggle.addEventListener('click', () => setFab(!fab.classList.contains('is-open')));
        document.addEventListener('click', (e) => { if (!fab.contains(e.target)) setFab(false); });
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') setFab(false); });
        $$('.fab__item', fab).forEach((a) => a.addEventListener('click', () => setTimeout(() => setFab(false), 150)));
    }

    /* ================= Cookie ================= */
    const cookie = $('[data-cookie]');
    if (cookie) {
        let accepted = false;
        try { accepted = localStorage.getItem('cookie_ok') === '1'; } catch (_) { /* приватный режим */ }
        if (!accepted) setTimeout(() => { cookie.hidden = false; }, 1200);
        $('[data-cookie-accept]', cookie).addEventListener('click', () => {
            cookie.hidden = true;
            try { localStorage.setItem('cookie_ok', '1'); } catch (_) { /* noop */ }
        });
    }

    /* ================= UTM (first touch) ================= */
    const UTM_KEYS = ['utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term', 'yclid', 'gclid'];
    const params = new URLSearchParams(location.search);
    const fresh = {};
    UTM_KEYS.forEach((k) => { if (params.get(k)) fresh[k] = params.get(k); });
    try { if (Object.keys(fresh).length) localStorage.setItem('utm', JSON.stringify(fresh)); } catch (_) { /* noop */ }
    const getUtm = () => { try { return localStorage.getItem('utm') || ''; } catch (_) { return ''; } };
    const pageLoadedAt = Date.now();

    /* ================= Modal ================= */
    const modal = $('#lead');
    const modalForm = $('[data-lead-form]', modal);

    const openModal = (service, comment) => {
        const select = $('[data-service-select]', modalForm);
        if (service && [...select.options].some((o) => o.value === service)) select.value = service;
        if (comment && !modalForm.elements.comment.value) modalForm.elements.comment.value = comment;
        setDrawer(false);
        if (typeof modal.showModal === 'function') modal.showModal(); else modal.setAttribute('open', '');
        document.body.classList.add('modal-open');
        lockScroll(true);
        const fields = $('[data-fields]', modalForm);
        setTimeout(() => { if (!fields.hidden) modalForm.elements.name.focus(); }, 60);
        goal('form_open');
    };

    const closeModal = () => {
        if (modal.open && typeof modal.close === 'function') modal.close(); else modal.removeAttribute('open');
    };

    modal.addEventListener('close', () => {
        document.body.classList.remove('modal-open');
        lockScroll(false);
        if (location.hash === '#lead') history.replaceState(null, '', location.pathname + location.search);
    });
    modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });

    document.addEventListener('click', (e) => {
        const trigger = e.target.closest('[data-open-lead]');
        if (trigger) {
            e.preventDefault();
            openModal(trigger.dataset.service, trigger.dataset.comment);
            return;
        }
        if (e.target.closest('[data-close-lead]')) { e.preventDefault(); closeModal(); }
        const link = e.target.closest('a[href^="tel:"], a[href*="t.me"], a[href*="wa.me"]');
        if (link) goal(link.dataset.goal || (link.href.startsWith('tel:') ? 'call_click' : 'messenger_click'));
    });

    document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && drawer?.classList.contains('is-open')) setDrawer(false); });

    if (modal.hasAttribute('data-autoopen') || location.hash === '#lead') openModal();

    /* ================= Lightbox ================= */
    const lb = $('[data-lightbox]');
    if (lb && typeof lb.showModal === 'function') {
        const lbImg = $('[data-lightbox-img]', lb);
        const lbAlt = $('[data-lightbox-alt]', lb);
        const lbCount = $('[data-lightbox-count]', lb);
        const webp = document.createElement('canvas').toDataURL('image/webp').startsWith('data:image/webp');
        let items = [];
        let index = 0;

        const preload = (a) => { if (a) (new Image()).src = webp ? a.dataset.webp : a.href; };
        const show = (i) => {
            index = (i + items.length) % items.length;
            const a = items[index];
            const img = $('img', a);
            lbImg.src = webp ? a.dataset.webp : a.href;
            lbImg.width = +a.dataset.w;
            lbImg.height = +a.dataset.h;
            lbImg.alt = img.alt;
            lbAlt.textContent = img.alt.replace(/, фото \d+$/, '');
            lbCount.textContent = `${index + 1} / ${items.length}`;
            preload(items[(index + 1) % items.length]);
            preload(items[(index - 1 + items.length) % items.length]);
        };

        document.addEventListener('click', (e) => {
            const a = e.target.closest('[data-lightbox-item]');
            if (!a) return;
            e.preventDefault();
            items = $$('[data-lightbox-item]', a.closest('[data-gallery]'));
            show(items.indexOf(a));
            lb.showModal();
            lockScroll(true);
            goal('gallery_open');
        });

        lb.addEventListener('close', () => { lockScroll(false); lbImg.removeAttribute('src'); });
        $('[data-lightbox-close]', lb).addEventListener('click', () => lb.close());
        $('[data-lightbox-prev]', lb).addEventListener('click', () => show(index - 1));
        $('[data-lightbox-next]', lb).addEventListener('click', () => show(index + 1));
        lb.addEventListener('click', (e) => { if (e.target === lb || e.target.classList.contains('lightbox__figure')) lb.close(); });
        lb.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') show(index - 1);
            if (e.key === 'ArrowRight') show(index + 1);
        });

        let sx = null;
        lb.addEventListener('pointerdown', (e) => { sx = e.clientX; });
        lb.addEventListener('pointerup', (e) => {
            if (sx === null) return;
            const dx = e.clientX - sx;
            sx = null;
            if (Math.abs(dx) > 50) show(index + (dx < 0 ? 1 : -1));
        });
    }

    /* ================= Forms ================= */
    const formatPhone = (value) => {
        let d = value.replace(/\D/g, '');
        if (!d) return '';
        if (d[0] === '8') d = '7' + d.slice(1);
        if (d[0] !== '7') d = '7' + d;
        d = d.slice(0, 11);
        let out = '+7';
        if (d.length > 1) out += ' (' + d.slice(1, 4);
        if (d.length >= 4) out += ')';
        if (d.length > 4) out += ' ' + d.slice(4, 7);
        if (d.length > 7) out += '-' + d.slice(7, 9);
        if (d.length > 9) out += '-' + d.slice(9, 11);
        return out;
    };

    $$('[data-lead-form]').forEach((form) => {
        const fields = $('[data-fields]', form);
        const success = $('[data-success]', form);
        const phone = $('[data-phone]', form);
        const fileInput = $('[data-file-input]', form);
        const fileBox = $('[data-file]', form);
        const fileText = $('[data-file-text]', form);
        const fileDefault = fileText.innerHTML;

        phone.addEventListener('input', () => { phone.value = formatPhone(phone.value); });
        phone.addEventListener('focus', () => { if (!phone.value) phone.value = '+7 ('; });
        phone.addEventListener('blur', () => { if (phone.value.replace(/\D/g, '').length <= 1) phone.value = ''; });

        fileInput.addEventListener('change', () => {
            const f = fileInput.files[0];
            fileBox.classList.toggle('has-file', !!f);
            fileText.textContent = f ? f.name : '';
            if (!f) fileText.innerHTML = fileDefault;
        });

        const showErrors = (errors) => {
            $$('[data-error]', form).forEach((el) => { el.textContent = ''; });
            $$('.is-invalid', form).forEach((el) => el.classList.remove('is-invalid'));
            Object.entries(errors).forEach(([key, msgs]) => {
                const box = $(`[data-error="${key}"]`, form);
                if (box) box.textContent = Array.isArray(msgs) ? msgs[0] : msgs;
                const input = form.elements[key];
                if (input && input.classList) input.classList.add('is-invalid');
            });
            const first = $('.is-invalid', form);
            if (first) first.focus();
        };

        const validate = () => {
            const errors = {};
            if (form.elements.name.value.trim().length < 2) errors.name = 'Укажите имя';
            if (phone.value.replace(/\D/g, '').length < 11) errors.phone = 'Укажите телефон полностью';
            const f = fileInput.files[0];
            if (f && f.size > 10 * 1024 * 1024) errors.photo = 'Фото не больше 10 МБ';
            if (!form.elements.agree.checked) errors.agree = 'Нужно согласие на обработку персональных данных';
            return errors;
        };

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const errors = validate();
            if (Object.keys(errors).length) { showErrors(errors); return; }
            showErrors({});

            form.elements.ts.value = String(pageLoadedAt);
            form.elements.utm.value = getUtm();

            const btn = $('[type="submit"]', form);
            const label = btn.textContent;
            btn.disabled = true;
            btn.textContent = 'Отправляем…';

            try {
                const res = await fetch(form.action, {
                    method: 'POST',
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: new FormData(form),
                    credentials: 'same-origin',
                });
                const data = await res.json().catch(() => ({}));

                if (res.ok && data.ok) {
                    fields.hidden = true;
                    success.hidden = false;
                    form.reset();
                    fileBox.classList.remove('has-file');
                    fileText.innerHTML = fileDefault;
                    goal('lead_submit');
                } else if (res.status === 422) {
                    showErrors(data.errors || {});
                } else if (res.status === 413) {
                    showErrors({ photo: 'Фото слишком большое' });
                } else if (res.status === 429) {
                    showErrors({ phone: 'Слишком много попыток. Попробуйте через минуту или позвоните нам.' });
                } else if (res.status === 419) {
                    showErrors({ phone: 'Сессия устарела — обновите страницу и отправьте ещё раз.' });
                } else {
                    showErrors({ phone: 'Не удалось отправить. Позвоните нам, пожалуйста.' });
                }
            } catch (_) {
                showErrors({ phone: 'Нет соединения. Проверьте интернет или позвоните нам.' });
            } finally {
                btn.disabled = false;
                btn.textContent = label;
            }
        });
    });
})();
