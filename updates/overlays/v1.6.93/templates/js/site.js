(() => {
    const sizeSelector = '[data-component-width]';
    let sizeUpdate = 0;
    const resizeComponents = () => {
        cancelAnimationFrame(sizeUpdate);
        sizeUpdate = requestAnimationFrame(() => {
            const content = document.querySelector('.page-content');
            const contentBox = content?.getBoundingClientRect();
            document.querySelectorAll(sizeSelector).forEach((block) => {
                if (!block.getClientRects().length || block.closest('dialog')) return;
                const rightWidth = block.dataset.componentWidth === 'page' || !contentBox
                    ? { left: 0, width: document.documentElement.clientWidth } : contentBox;
                const currentLeft = parseFloat(getComputedStyle(block).left) || 0;
                const shift = rightWidth.left - (block.getBoundingClientRect().left - currentLeft);
                block.style.setProperty('--component-expanded-width', rightWidth.width + 'px');
                block.style.setProperty('--component-expanded-offset', shift + 'px');
            });
        });
    };
    if ('ResizeObserver' in window) {
        const sizeWatcher = new ResizeObserver(resizeComponents);
        sizeWatcher.observe(document.documentElement);
        const content = document.querySelector('.page-content');
        if (content) sizeWatcher.observe(content);
    }
    window.addEventListener('resize', resizeComponents, { passive: true });
    if (document.querySelector(sizeSelector)) resizeComponents();
    new MutationObserver((records) => {
        if (records.some((record) => [...record.addedNodes].some((node) => node.nodeType === 1
            && (node.matches(sizeSelector) || node.querySelector(sizeSelector))))) resizeComponents();
    }).observe(document.body, { childList: true, subtree: true });

    const motion = window.matchMedia('(prefers-reduced-motion: reduce)');
    const entrance = {
        fade: { opacity: 0 },
        slide: { opacity: 0, translate: '0 16px' },
        scale: { opacity: 0, scale: '.97' },
    }[document.body.dataset.scrollAnimation];
    if (entrance && 'IntersectionObserver' in window && Element.prototype.animate && !motion.matches) {
        const seen = new WeakSet();
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(({ isIntersecting, target }) => {
                if (!isIntersecting) return;
                observer.unobserve(target);
                target.classList.remove('site-scroll-pending');
                if (!motion.matches && !target.contains(document.activeElement) && !target.matches(':focus')) {
                    const end = { opacity: 1 };
                    if (entrance.translate) end.translate = '0 0';
                    if (entrance.scale) end.scale = '1';
                    target.animate([entrance, end], { duration: 550, easing: 'ease-out' });
                }
            });
        }, { rootMargin: '0px 0px -8% 0px' });
        const observeBlocks = () => {
            document.querySelectorAll('.site-main > h1, .page-content > *, .page-preset > *').forEach((block) => {
                if (seen.has(block) || block.matches('script, style, template, [hidden]')) return;
                seen.add(block);
                const bounds = block.getBoundingClientRect();
                if (bounds.height > 0 && bounds.top >= window.innerHeight) {
                    block.classList.add('site-scroll-pending');
                    observer.observe(block);
                }
            });
        };
        observeBlocks();
        const main = document.querySelector('.site-main');
        if (main) new MutationObserver(observeBlocks).observe(main, { childList: true, subtree: true });
    }

    window.siteFetchContent = async (path) => {
        const token = document.querySelector('meta[name="site-fragment-token"]');
        const url = new URL(path, window.location.href);
        if (!token || url.origin !== window.location.origin) {
            throw new Error('Фрагмент доступен в текущем сеансе сайта.');
        }
        url.searchParams.set('ajax', '1');
        const response = await fetch(url, {
            credentials: 'same-origin',
            headers: { Accept: 'text/html', 'X-Site-Fragment': token.content },
        });
        if (!response.ok) throw new Error('Не удалось загрузить страницу (' + response.status + ').');
        return response.text();
    };

    document.addEventListener('change', async (event) => {
        const picker = event.target.closest('[data-city-picker]');
        if (!picker || !event.target.matches('[data-city-select]')) return;
        const fields = [...document.querySelectorAll('[data-city-select]')];
        const previous = fields.map((field) => [...field.options].find((option) => option.defaultSelected)?.value || '');
        const token = document.querySelector('meta[name="site-fragment-token"]');
        const url = new URL(picker.dataset.cityUrl, window.location.href);
        const error = picker.querySelector('[data-city-error]');
        document.querySelectorAll('[data-city-error]').forEach((message) => { message.hidden = true; });
        fields.forEach((field) => { field.disabled = true; });
        try {
            if (!token || url.origin !== window.location.origin) throw new Error();
            url.searchParams.set('city_select', event.target.value);
            const response = await fetch(url, {
                credentials: 'same-origin',
                headers: { Accept: 'application/json', 'X-Site-Fragment': token.content },
            });
            if (!response.ok) throw new Error();
            const update = await response.json();
            fields.forEach((field) => {
                field.value = update.city;
                [...field.options].forEach((option) => { option.defaultSelected = option.selected; });
                const label = field.closest('[data-city-picker]').querySelector('[data-city-current]');
                if (label && field.selectedOptions[0]) label.textContent = field.selectedOptions[0].textContent;
            });
            document.querySelectorAll('[data-city-phones]').forEach((list) => { list.innerHTML = update.phones; });
            document.querySelectorAll('[data-city-token]').forEach((node) => {
                node.innerHTML = update.tokens[node.dataset.cityToken] ?? '';
            });
            document.querySelectorAll('[data-city-contact]').forEach((node) => {
                node.innerHTML = update.contacts[node.dataset.cityContact] ?? '';
            });
            if (update.header) document.querySelectorAll('[data-city-header-field]').forEach((node) => {
                node.innerHTML = update.header[node.dataset.cityHeaderField] ?? '';
            });
        } catch (exception) {
            fields.forEach((field, index) => { field.value = previous[index]; });
            error.hidden = false;
        } finally {
            fields.forEach((field) => { field.disabled = false; });
        }
    });

    document.addEventListener('click', (event) => {
        document.querySelectorAll('[data-language-dropdown][open]').forEach((menu) => {
            if (!menu.contains(event.target)) menu.open = false;
        });
        document.querySelectorAll('[data-breadcrumb-siblings][open]').forEach((menu) => {
            if (!menu.contains(event.target)) menu.open = false;
        });
    });
    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') return;
        const menu = event.target.closest?.('[data-breadcrumb-siblings][open], [data-language-dropdown][open]');
        if (menu) {
            event.preventDefault();
            menu.open = false;
            menu.querySelector('summary').focus();
        }
    });

    document.querySelectorAll('[data-breadcrumb-siblings]').forEach((menu) => {
        const align = () => {
            if (!menu.open) return;
            const list = menu.querySelector('ul');
            const available = document.documentElement.clientWidth - 8 - menu.getBoundingClientRect().left;
            list.style.setProperty('--breadcrumb-offset', Math.min(0, available - list.offsetWidth) + 'px');
        };
        menu.addEventListener('toggle', align);
        window.addEventListener('resize', align, { passive: true });
    });

    document.querySelectorAll('.mobile-header-menu').forEach((element, index) => {
        const toggle = element.querySelector('[data-mobile-toggle]');
        const drawer = element.querySelector('[data-mobile-drawer]');
        if (!toggle || !drawer) return;
        drawer.id = 'mobile-site-menu-' + (index + 1);
        toggle.setAttribute('aria-controls', drawer.id);
        toggle.hidden = false;
        const mobile = window.matchMedia('(max-width: 40rem)');
        const closeOnResize = () => { if (!mobile.matches && drawer.open) drawer.close(); };

        toggle.addEventListener('click', () => {
            if (!drawer.open) {
                drawer.showModal();
                toggle.setAttribute('aria-expanded', 'true');
            }
        });
        drawer.querySelector('[data-mobile-close]').addEventListener('click', () => drawer.close());
        drawer.addEventListener('click', (event) => {
            if (event.target.closest('[data-mobile-navigation] a')) {
                drawer.close();
            } else if (event.target === drawer) {
                const panel = drawer.getBoundingClientRect();
                if (event.clientX < panel.left || event.clientX > panel.right) drawer.close();
            }
        });
        drawer.addEventListener('close', () => toggle.setAttribute('aria-expanded', 'false'));
        if (mobile.addEventListener) mobile.addEventListener('change', closeOnResize);
        else mobile.addListener(closeOnResize);
    });

    const phoneParts = (input, raw) => {
        const original = raw.replace(/\D/g, '');
        const hasPlus = raw.trimStart().startsWith('+');
        const profile = JSON.parse(input.form.dataset.phoneProfile || '{"code":"7","prefixes":"78"}');
        let digits = original;
        if (!hasPlus && digits && profile.code) {
            digits = profile.code + (profile.prefixes.includes(digits[0]) ? digits.slice(1) : digits);
        }
        return { original, digits, international: Boolean(hasPlus || (digits && profile.code)), limit: digits[0] === '7' ? 11 : 15 };
    };

    const acceptedPhone = new WeakMap();
    const formatPhone = (input) => {
        const raw = input.value;
        const { original, digits, international, limit } = phoneParts(input, raw);
        if (!/^ *\+?[0-9 ().-]*$/.test(raw) || digits.length > limit) {
            input.value = acceptedPhone.get(input) ?? '';
            return;
        }
        let value = (international ? '+' : '') + digits;
        if (international && digits[0] === '7') {
            const local = digits.slice(1);
            value = '+7';
            if (local.length) value += '(' + local.slice(0, 3);
            if (local.length > 3) value += ')' + local.slice(3, 6);
            if (local.length > 6) value += '-' + local.slice(6, 8);
            if (local.length > 8) value += '-' + local.slice(8);
        }
        acceptedPhone.set(input, value);
        if (value === raw) return;

        const caret = input.selectionStart;
        input.value = value;
        if (caret === null || caret === raw.length) return;
        let digitsBeforeCaret = raw.slice(0, caret).replace(/\D/g, '').length;
        if (digitsBeforeCaret > 0) digitsBeforeCaret += digits.length - original.length;
        let position = 0;
        while (position < value.length && digitsBeforeCaret > 0) {
            if (/\d/.test(value[position])) digitsBeforeCaret -= 1;
            position += 1;
        }
        input.setSelectionRange(position, position);
    };

    const rejectExtraPhoneInput = (event, input, inserted) => {
        if (typeof inserted !== 'string' || input.selectionStart === null || input.selectionEnd === null) return;
        const candidate = input.value.slice(0, input.selectionStart) + inserted + input.value.slice(input.selectionEnd);
        const { digits, limit } = phoneParts(input, candidate);
        if (!/^ *\+?[0-9 ().-]*$/.test(candidate) || digits.length > limit) event.preventDefault();
    };

    const validatePhone = (input, showError) => {
        formatPhone(input);
        const value = input.value;
        const invalidCheckoutPrefix = input.form.matches('[data-shop-checkout-form]') && /^\+89/.test(value);
        const valid = !value || (!invalidCheckoutPrefix && /^\+7\([0-9]{3}\)[0-9]{3}-[0-9]{2}-[0-9]{2}$|^\+[1-68-9][0-9]{6,14}$/.test(value)
            && (input.maxLength < 0 || value.length <= input.maxLength));
        const message = input.form.dataset.phoneError || 'Укажите полный номер телефона, включая код страны.';
        input.setCustomValidity(valid ? '' : message);
        if (!showError) return;
        const label = input.closest('[data-message-field]');
        const hint = label?.querySelector('[data-field-error]');
        if (hint) {
            hint.textContent = valid ? '' : message;
            hint.hidden = valid;
        }
        if (valid) input.removeAttribute('aria-invalid');
        else input.setAttribute('aria-invalid', 'true');
    };

    const captchaWidgets = new WeakMap();
    let captchaScript;
    const loadCaptcha = () => {
        if (!captchaScript) {
            captchaScript = new Promise((resolve, reject) => {
                const script = document.createElement('script');
                const timer = window.setTimeout(reject, 15000);
                window.siteRecaptchaReady = () => {
                    window.clearTimeout(timer);
                    resolve(window.grecaptcha);
                };
                script.addEventListener('error', () => {
                    window.clearTimeout(timer);
                    reject();
                }, { once: true });
                script.async = true;
                script.src = 'https://www.google.com/recaptcha/api.js?onload=siteRecaptchaReady&render=explicit&hl=ru';
                document.head.append(script);
            });
        }
        return captchaScript;
    };

    const drawIblockChart = (element) => {
        const frame = element.querySelector('.iblock-chart-canvas');
        const canvas = frame.querySelector('canvas');
        const { type, labels, datasets } = JSON.parse(element.dataset.iblockChart);
        const horizontal = type === 'horizontal';
        const palette = ['#329681', '#5b7ed7', '#da7756', '#9973bf', '#c19832', '#3a9ab8', '#c26695', '#608750'];
        const sectors = ['pie', 'doughnut', 'polarArea'].includes(type);
        datasets.forEach((series, index) => {
            const color = palette[index];
            series.backgroundColor = sectors ? labels.map((_, position) => palette[position % palette.length]) : color + '55';
            series.borderColor = sectors ? getComputedStyle(element).getPropertyValue('--surface').trim() : color;
            series.borderWidth = 2;
            if (type === 'line') series.tension = .2;
        });
        const ink = () => getComputedStyle(element).color;
        const line = () => getComputedStyle(element).getPropertyValue('--line').trim();
        const axis = () => ({ ticks: { color: ink }, grid: { color: line } });
        const scales = sectors && type !== 'polarArea' ? {}
            : type === 'radar' || type === 'polarArea'
                ? { r: { ...axis(), angleLines: { color: line }, pointLabels: { color: ink } } }
                : { x: axis(), y: axis() };
        frame.hidden = false;
        try {
            new window.Chart(canvas, {
                type: horizontal ? 'bar' : type,
                data: { labels, datasets },
                options: {
                    indexAxis: horizontal ? 'y' : 'x',
                    responsive: true, maintainAspectRatio: false, animation: false,
                    locale: document.documentElement.lang || undefined,
                    scales, plugins: { legend: { labels: { color: ink, usePointStyle: true } } }
                }
            });
            element.querySelector('details').open = false;
        } catch (error) {
            window.Chart.getChart(canvas)?.destroy();
            frame.hidden = true;
        }
    };

    const initializeIblockFilter = (element) => {
        const controls = element.querySelector('[data-iblock-tag-buttons]');
        const buttons = controls.querySelectorAll('[data-iblock-tag]');
        const rows = element.querySelectorAll('[data-iblock-row-tags]');
        controls.addEventListener('click', (event) => {
            const selected = event.target.closest('[data-iblock-tag]');
            if (!selected) return;
            const tag = selected.dataset.iblockTag;
            buttons.forEach((button) => button.setAttribute('aria-pressed', String(button === selected)));
            rows.forEach((row) => {
                row.hidden = tag !== '' && !row.dataset.iblockRowTags.split(' ').includes(tag);
            });
        });
        controls.hidden = false;
    };

    const chartSelector = '[data-iblock-chart]';
    const initialize = (root) => {
        const selector = '[data-gallery]:not([data-ready]), [data-contact-form]:not([data-ready]), [data-shop-checkout-form]:not([data-ready]), [data-iblock-filter]:not([data-ready]), ' + chartSelector + ':not([data-ready])';
        const elements = [...(root.matches(selector) ? [root] : []), ...root.querySelectorAll(selector)];
        elements.forEach((element) => {
            if (element.closest('[data-banner-dialog]:not([open]), [data-action-dialog]:not([open])')) return;
            element.dataset.ready = 'true';
            if (element.matches('[data-iblock-filter]')) {
                initializeIblockFilter(element);
                return;
            }
            if (element.matches(chartSelector)) {
                if (typeof window.Chart === 'function') drawIblockChart(element);
                return;
            }
            if (element.matches('[data-contact-form], [data-shop-checkout-form]')) {
                const formatTelephone = (event) => {
                    if (event.target.matches('input[type="tel"]')) validatePhone(event.target, true);
                };
                element.addEventListener('input', formatTelephone);
                element.addEventListener('change', formatTelephone);
                element.addEventListener('beforeinput', (event) => {
                    if (event.target.matches('input[type="tel"]') && event.inputType?.startsWith('insert')) {
                        rejectExtraPhoneInput(event, event.target, event.data);
                    }
                });
                element.addEventListener('paste', (event) => {
                    if (event.target.matches('input[type="tel"]')) rejectExtraPhoneInput(event, event.target, event.clipboardData?.getData('text'));
                });
                element.querySelectorAll('input[type="tel"]').forEach((input) => validatePhone(input, false));
                if (element.matches('[data-shop-checkout-form]')) {
                    const feedback = (event) => {
                        const control = event.target;
                        if (!control.validity) return;
                        const hint = control.closest('[data-message-field]')?.querySelector('[data-field-error]');
                        if (!hint) return;
                        const valid = control.validity.valid;
                        if (valid) control.removeAttribute('aria-invalid');
                        else control.setAttribute('aria-invalid', 'true');
                        hint.textContent = valid ? '' : control.validationMessage;
                        hint.hidden = valid;
                    };
                    element.addEventListener('invalid', feedback, true);
                    element.addEventListener('focusout', feedback);
                    return;
                }
                const button = element.querySelector('button[type="submit"]');
                const widget = element.querySelector('[data-form-captcha]');
                if (!widget) {
                    button.disabled = false;
                    return;
                }
                const status = element.querySelector('[data-captcha-status]');
                loadCaptcha().then((api) => {
                    if (!element.isConnected) {
                        delete element.dataset.ready;
                        return;
                    }
                    const id = api.render(widget, {
                        sitekey: widget.dataset.sitekey,
                        size: 'compact',
                        'error-callback': () => { status.textContent = 'CAPTCHA недоступна. Обновите страницу.'; },
                        'expired-callback': () => { status.textContent = 'Подтвердите проверку ещё раз.'; },
                        callback: () => { status.textContent = 'Проверка пройдена.'; },
                    });
                    captchaWidgets.set(element, { api, id });
                    button.disabled = false;
                }).catch(() => {
                    status.textContent = 'Не удалось загрузить CAPTCHA. Обновите страницу и попробуйте снова.';
                });
                return;
            }
            const controls = element.querySelector('.gallery-controls');
            const track = element.querySelector('[data-gallery-track]');
            const images = [...track.children];
            if (images.length < 2) return;
            const status = controls && controls.querySelector('[data-gallery-status]');
            const effect = element.dataset.galleryEffect;
            const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

            let current = -1;
            const select = (index) => {
                if (index === current) return;
                current = index;
                if (status) status.textContent = (current + 1) + ' / ' + images.length;
                images.forEach((image, position) => {
                    image.classList.toggle('is-active', position === current);
                    if (position === current) image.removeAttribute('aria-hidden');
                    else image.setAttribute('aria-hidden', 'true');
                    if (element.hasAttribute('data-banners')) image.inert = position !== current;
                });
            };
            const move = (direction) => {
                const next = (current + direction + images.length) % images.length;
                if (effect === 'fade') {
                    select(next);
                } else {
                    track.scrollTo({ left: images[next].offsetLeft,
                        behavior: effect === 'slide' && !reducedMotion.matches ? 'smooth' : 'auto' });
                }
            };

            if (controls) {
                controls.hidden = false;
                const previous = controls.querySelector('[data-gallery-prev]');
                const next = controls.querySelector('[data-gallery-next]');
                if (previous && next) {
                    previous.addEventListener('click', () => move(-1));
                    next.addEventListener('click', () => move(1));
                }
            }
            if (effect === 'fade') {
                let start;
                track.addEventListener('touchstart', (event) => {
                    start = event.touches.length === 1 ? event.touches[0] : null;
                }, { passive: true });
                track.addEventListener('touchcancel', () => { start = null; }, { passive: true });
                track.addEventListener('touchend', (event) => {
                    if (!start) return;
                    const end = Array.from(event.changedTouches).find((touch) => touch.identifier === start.identifier);
                    if (!end) return;
                    const x = end.clientX - start.clientX;
                    const y = end.clientY - start.clientY;
                    start = null;
                    if (Math.abs(x) > Math.max(35, Math.abs(y))) move(x < 0 ? 1 : -1);
                }, { passive: true });
            } else {
                track.addEventListener('scroll', () => {
                    if (track.clientWidth > 0) {
                        select(Math.max(0, Math.min(images.length - 1, Math.round(track.scrollLeft / track.clientWidth))));
                    }
                }, { passive: true });
            }
            element.addEventListener('keydown', (event) => {
                if (event.key !== 'ArrowLeft' && event.key !== 'ArrowRight') return;
                if (event.target.matches('input, textarea, select, [contenteditable]')) return;
                event.preventDefault();
                move(event.key === 'ArrowRight' ? 1 : -1);
            });
            select(0);
        });
    };

    document.addEventListener('click', (event) => {
        const opener = event.target.closest('[data-banner-open], [data-action-open]');
        const closer = event.target.closest('[data-banner-close], [data-action-close]');
        if (opener) {
            const dialog = document.getElementById(opener.dataset.bannerOpen || opener.dataset.actionOpen);
            if (dialog?.matches('[data-banner-dialog], [data-action-dialog]') && !dialog.open) {
                dialog.showModal();
                initialize(dialog);
            }
        } else if (closer) {
            closer.closest('[data-banner-dialog], [data-action-dialog]').close();
        }
    });
    initialize(document.body);
    new MutationObserver((records) => {
        records.forEach((record) => {
            record.addedNodes.forEach((node) => {
                if (node.nodeType === 1) initialize(node);
            });
            if (typeof window.Chart !== 'function') return;
            record.removedNodes.forEach((node) => {
                if (node.nodeType !== 1 || node.isConnected) return;
                const charts = [...(node.matches(chartSelector) ? [node] : []), ...node.querySelectorAll(chartSelector)];
                charts.forEach((chart) => {
                    window.Chart.getChart(chart.querySelector('canvas'))?.destroy();
                    delete chart.dataset.ready;
                });
            });
        });
    }).observe(document.body, { childList: true, subtree: true });

    document.addEventListener('submit', async (event) => {
        const form = event.target;
        if (!form.matches('[data-contact-form]')) return;
        event.preventDefault();
        const button = form.querySelector('button[type="submit"]');
        if (button.disabled) return;

        const feedback = form.querySelector('[data-form-feedback]');
        const fields = [...form.querySelectorAll('[data-message-field]')];
        fields.forEach((field) => {
            field.querySelector('[data-field-error]').hidden = true;
            field.querySelectorAll('[aria-invalid]').forEach((input) => input.removeAttribute('aria-invalid'));
        });
        feedback.hidden = true;
        const captcha = captchaWidgets.get(form);
        const token = captcha ? captcha.api.getResponse(captcha.id) : '';
        if (captcha && !token) {
            feedback.textContent = 'Подтвердите, что вы не робот.';
            feedback.classList.add('error');
            feedback.hidden = false;
            return;
        }
        button.disabled = true;
        try {
            const body = new FormData(form);
            if (captcha) body.set('g-recaptcha-response', token);
            const response = await fetch(form.action, {
                method: 'POST',
                body,
                headers: { Accept: 'application/json' },
            });
            const result = await response.json();
            feedback.textContent = result.message;
            feedback.classList.toggle('error', !result.ok);
            if (result.ok) {
                if (form.matches('[data-purchase-form]') && result.payment?.url) {
                    if (result.payment.fields) {
                        const transfer = document.createElement('form');
                        transfer.method = 'POST';
                        transfer.action = result.payment.url;
                        Object.entries(result.payment.fields).forEach(([name,value]) => {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = name;
                            input.value = value;
                            transfer.append(input);
                        });
                        document.body.append(transfer);
                        transfer.submit();
                    } else location.assign(result.payment.url);
                    return;
                }
                form.insertAdjacentElement('afterend', feedback);
                form.hidden = true;
                feedback.hidden = false;
                feedback.tabIndex = -1;
                feedback.focus({ preventScroll: true });
            } else {
                fields.forEach((field) => {
                    const name = field.dataset.messageField;
                    if (!Object.prototype.hasOwnProperty.call(result.fields || {}, name)) return;
                    const fieldError = field.querySelector('[data-field-error]');
                    fieldError.textContent = result.fields[name];
                    fieldError.hidden = false;
                    field.querySelectorAll('input, textarea, select').forEach((input) => input.setAttribute('aria-invalid', 'true'));
                });
            }
        } catch (error) {
            feedback.textContent = 'Не удалось отправить сообщение. Попробуйте ещё раз.';
            feedback.classList.add('error');
        } finally {
            feedback.hidden = false;
            button.disabled = false;
            if (captcha && form.isConnected) {
                captcha.api.reset(captcha.id);
                form.querySelector('[data-captcha-status]').textContent = 'Подтвердите, что вы не робот.';
            }
        }
    });
})();

(() => {
    const meta = document.querySelector('meta[name=site-analytics]');
    if (!meta) return;
    const enabled = JSON.parse(meta.content);
    const load = (source) => {
        const script = document.createElement('script');
        script.src = source;
        script.async = true;
        document.head.append(script);
        return script;
    };
    if (enabled.metrica) {
        if (!window.ym) {
            window.ym = function () { (window.ym.a = window.ym.a || []).push(arguments); };
            window.ym.l = Date.now();
            load('https://mc.yandex.ru/metrika/tag.js');
        }
        window.ym(Number(enabled.metrica), 'init', { clickmap: true, trackLinks: true, accurateTrackBounce: true });
    }
    if (enabled.analytics) {
        if (!window.gtag) {
            window.dataLayer = window.dataLayer || [];
            window.gtag = function () { window.dataLayer.push(arguments); };
            window.gtag('js', new Date());
            load('https://www.googletagmanager.com/gtag/js?id=' + encodeURIComponent(enabled.analytics));
        }
        window.gtag('config', enabled.analytics);
    }
    if (enabled.pixel) {
        if (!window.fbq) {
            const pixel = function () {
                if (pixel.callMethod) pixel.callMethod.apply(pixel, arguments);
                else pixel.queue.push(arguments);
            };
            pixel.push = pixel;
            pixel.loaded = true;
            pixel.version = '2.0';
            pixel.queue = [];
            window.fbq = pixel;
            if (!window._fbq) window._fbq = pixel;
            load('https://connect.facebook.net/en_US/fbevents.js');
        }
        window.fbq('init', enabled.pixel);
        window.fbq('track', 'PageView');
    }
    if (enabled.cloudflare) {
        const beacon = document.createElement('script');
        beacon.src = 'https://static.cloudflareinsights.com/beacon.min.js';
        beacon.defer = true;
        beacon.dataset.cfBeacon = JSON.stringify({ token: enabled.cloudflare });
        document.head.append(beacon);
    }
    if (enabled.clarity) {
        if (!window.clarity) {
            window.clarity = function () { (window.clarity.q = window.clarity.q || []).push(arguments); };
            load('https://www.clarity.ms/tag/' + encodeURIComponent(enabled.clarity));
        }
        window.clarity('consentv2', {ad_Storage: 'denied', analytics_Storage: 'granted'});
    }
    if (enabled.umami) {
        const tracker = document.createElement('script');
        tracker.src = 'https://cloud.umami.is/script.js';
        tracker.dataset.websiteId = enabled.umami;
        tracker.defer = true;
        document.head.append(tracker);
    }
})();

(() => {
    const providers = new Map();
    const loadScript = (url) => new Promise((resolve, reject) => {
        const script = document.createElement('script');
        script.src = url;
        script.async = true;
        const timer = setTimeout(() => reject(new Error('Map script timeout')), 12000);
        script.addEventListener('load', () => { clearTimeout(timer); resolve(); }, { once: true });
        script.addEventListener('error', () => { clearTimeout(timer); reject(new Error('Map script failed')); }, { once: true });
        document.head.append(script);
    });
    const provider = (data) => {
        if (!providers.has(data.provider)) {
            const google = data.provider === 'google';
            const params = google ? { key: data.key, v: 'weekly', language: data.locale }
                : { apikey: data.key, lang: data.locale === 'ru' ? 'ru_RU' : 'en_US' };
            const url = google ? 'https://maps.googleapis.com/maps/api/js?' : 'https://api-maps.yandex.ru/2.1/?';
            providers.set(data.provider, loadScript(url + new URLSearchParams(params)).then(() => {
                if (google) return Promise.all([window.google.maps.importLibrary('maps'), window.google.maps.importLibrary('marker')]);
                return new Promise((resolve, reject) => window.ymaps.ready(resolve, reject));
            }));
        }
        return providers.get(data.provider);
    };
    const balloon = (point) => {
        const content = document.createElement('div');
        if (point.card) {
            // Page cards are rendered from escaped fields and a formatted announcement on the server.
            content.innerHTML = point.card;
            return content;
        }
        const title = document.createElement('strong');
        title.textContent = point.title;
        content.append(title);
        if (point.url) {
            const link = document.createElement('a');
            link.href = point.url;
            link.textContent = point.button || point.title;
            link.className = 'button';
            content.append(document.createElement('br'), link);
        }
        return content;
    };
    const start = async (block) => {
        block.dataset.mapReady = 'true';
        const canvas = block.querySelector('[data-map-canvas]');
        const status = block.querySelector('[data-map-status]');
        try {
            const data = JSON.parse(block.dataset.siteMap);
            const center = data.center || data.points[0];
            const modules = await provider(data);
            if (!block.isConnected) return;
            if (data.provider === 'yandex') {
                const api = window.ymaps;
                const map = new api.Map(canvas, { center: [center.lat, center.lng], zoom: data.zoom,
                    type: { map: 'yandex#map', satellite: 'yandex#satellite', hybrid: 'yandex#hybrid' }[data.mode],
                    controls: ['zoomControl'] });
                const group = data.cluster && data.points.length > 1 ? new api.Clusterer() : new api.GeoObjectCollection();
                data.points.forEach((point) => {
                    const name = document.createElement('span');
                    name.textContent = point.title;
                    group.add(new api.Placemark([point.lat, point.lng], {
                        iconCaption: name.innerHTML, hintContent: name.innerHTML, balloonContent: balloon(point).outerHTML,
                    }, { preset: 'islands#blueDotIconWithCaption' }));
                });
                map.geoObjects.add(group);
                if (data.fit && data.points.length > 1) map.setBounds(group.getBounds(), { checkZoomRange: true, zoomMargin: 36 });
            } else {
                const [maps, markerApi] = modules;
                const map = new maps.Map(canvas, { center: { lat: center.lat, lng: center.lng },
                    zoom: data.zoom, mapId: data.mapId, mapTypeId: data.mode === 'map' ? 'roadmap' : data.mode });
                const info = new maps.InfoWindow();
                const bounds = new google.maps.LatLngBounds();
                const markers = data.points.map((point) => {
                    const position = { lat: point.lat, lng: point.lng };
                    bounds.extend(position);
                    const marker = new markerApi.AdvancedMarkerElement({ position, title: point.title });
                    marker.addListener('click', () => {
                        info.setContent(balloon(point));
                        info.open({ map, anchor: marker });
                    });
                    return marker;
                });
                if (data.cluster && markers.length > 1) {
                    try {
                        const { MarkerClusterer } = await import('https://cdn.jsdelivr.net/npm/@googlemaps/markerclusterer@2.6.2/+esm');
                        new MarkerClusterer({ map, markers });
                    } catch {
                        markers.forEach((marker) => { marker.map = map; });
                    }
                } else {
                    markers.forEach((marker) => { marker.map = map; });
                }
                if (data.fit && markers.length > 1) map.fitBounds(bounds, 36);
            }
        } catch {
            canvas.hidden = true;
            status.textContent = 'Не удалось загрузить карту.';
            status.hidden = false;
        }
    };
    const scan = (root) => {
        const selector = '[data-site-map]:not([data-map-ready])';
        if (root.matches(selector)) start(root);
        root.querySelectorAll(selector).forEach(start);
    };
    scan(document.body);
    new MutationObserver((records) => records.forEach((record) =>
        record.addedNodes.forEach((node) => { if (node.nodeType === 1) scan(node); })
    )).observe(document.body, { childList: true, subtree: true });
})();

document.addEventListener('click', (event) => {
    const opener = event.target.closest('[data-show-page-dialog]');
    const component = event.target.closest('[data-inline-component]');
    const closer = event.target.closest('[data-close-page-dialog]');
    if (opener) {
        const dialog = document.getElementById(opener.dataset.showPageDialog);
        if (dialog && !dialog.open) dialog.showModal();
    } else if (component) {
        const entry = document.getElementById(component.hash.slice(1));
        const dialog = entry && entry.closest('dialog');
        if (!dialog) return;
        event.preventDefault();
        entry.open = true;
        if (!dialog.open) dialog.showModal();
        entry.scrollIntoView({block: 'nearest'});
        (entry.querySelector('[data-inline-field], [data-inline-option], [data-editor-data-url]') || entry.querySelector('summary'))?.focus();
    } else if (closer) {
        const dialog = closer.closest('dialog');
        if (dialog.dispatchEvent(new Event('cancel', {cancelable: true}))) dialog.close();
    }
});

(() => {
    const selector = '[data-page-menu-branch]';
    const branches = (element) => {
        const items = [];
        for (let branch = element.closest?.(selector); branch; branch = branch.parentElement.closest(selector)) items.push(branch);
        return items;
    };
    for (const type of ['pointerover', 'pointerout']) {
        document.addEventListener(type, (event) => {
            if (event.pointerType !== 'mouse') return;
            const entering = type === 'pointerover';
            branches(event.target).forEach((branch) => {
                if (!branch.closest('.mobile-drawer') && !branch.contains(event.relatedTarget)
                    && (entering || !branch.contains(document.activeElement))) {
                    branch.querySelector(':scope > details').open = entering;
                }
            });
        });
    }
    document.addEventListener('toggle', (event) => {
        const disclosure = event.target;
        if (!disclosure.matches('details[open]') || !disclosure.parentElement.matches(selector)) return;
        const submenu = disclosure.querySelector(':scope > ul');
        submenu.classList.remove('is-end-aligned');
        if (submenu.getBoundingClientRect().right > document.documentElement.clientWidth - 8) submenu.classList.add('is-end-aligned');
    }, true);
    document.addEventListener('focusout', (event) => {
        if (!event.relatedTarget) return;
        branches(event.target).forEach((branch) => {
            if (!branch.contains(event.relatedTarget)) branch.querySelector(':scope > details').open = false;
        });
    });
    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') return;
        const branch = branches(event.target).find((item) => item.querySelector(':scope > details[open]'));
        if (!branch) return;
        event.preventDefault();
        const disclosure = branch.querySelector(':scope > details');
        disclosure.open = false;
        disclosure.querySelector(':scope > summary').focus();
    });
})();

let openLayoutEditor;
let openPageEditor;
document.addEventListener('click', (event) => {
    const link = event.target.closest('[data-video-source]');
    if (!link || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey
        || typeof HTMLDialogElement === 'undefined' || !HTMLDialogElement.prototype.showModal) return;
    event.preventDefault();
    const dialog = document.createElement('dialog');
    dialog.className = 'site-video-dialog';
    dialog.setAttribute('aria-label', link.textContent.trim());
    const close = document.createElement('button');
    close.type = 'button';
    close.className = 'dialog-close';
    close.setAttribute('aria-label', link.dataset.videoClose);
    close.addEventListener('click', () => dialog.close());
    const player = document.createElement(link.dataset.videoKind === 'file' ? 'video' : 'iframe');
    if (player.tagName === 'VIDEO') {
        player.controls = true;
        player.playsInline = true;
    } else {
        player.title = link.textContent.trim();
        player.allow = 'autoplay; fullscreen; picture-in-picture';
        player.referrerPolicy = 'strict-origin-when-cross-origin';
        player.allowFullscreen = true;
    }
    player.src = link.dataset.videoSource;
    const original = document.createElement('a');
    original.href = link.href;
    original.target = '_blank';
    original.rel = 'noopener noreferrer';
    original.textContent = link.dataset.videoLink;
    dialog.append(close, player, original);
    document.body.append(dialog);
    dialog.addEventListener('close', () => { dialog.remove(); link.focus({preventScroll:true}); }, {once:true});
    dialog.showModal();
    close.focus();
    if (player.tagName === 'VIDEO') void player.play().catch(() => {});
});
document.addEventListener('dblclick', (event) => {
    if (event.ctrlKey || event.metaKey || event.shiftKey || event.altKey
        || event.target.closest('a, button, input, select, textarea, label, summary, [contenteditable]')) return;
    const region = event.target.closest('[data-shop-edit-region]');
    const editor = region?.querySelector(':scope > .layout-edit-actions [data-shop-live-editor]');
    if (editor) {
        event.preventDefault();
        editor.click();
    }
});
document.querySelectorAll('#page-component-data, #page-banner-editor, #page-layout-editor, #page-content-editor, #page-shop-editor').forEach((componentDataDialog) => {
    const frame = componentDataDialog.querySelector('[data-component-data-iframe]');
    const wide = componentDataDialog.querySelector('[data-component-data-wide]');
    const isBanner = componentDataDialog.id === 'page-banner-editor';
    const isLayout = componentDataDialog.id === 'page-layout-editor';
    const isPage = componentDataDialog.id === 'page-content-editor';
    const isShop = componentDataDialog.id === 'page-shop-editor';
    const selector = isLayout ? '[data-layout-edit-control][data-layout-edit-modal]'
        : (isShop ? '[data-shop-live-editor]' : (isPage ? '[data-page-editor-link]' : (isBanner ? '[data-banner-editor-url]' : '[data-editor-data-url]')));
    let saved = false;
    let opener;
    let target;
    const heading = isShop || isLayout || wide ? componentDataDialog.querySelector('h2') : null;
    const defaultTitle = heading?.textContent;
    const status = componentDataDialog.querySelector('[data-component-frame-status]');
    const message = status?.querySelector('[data-component-frame-message]');
    const retry = status?.querySelector('[data-component-frame-retry]');
    let loadTimer;
    const showLoadFailure = () => {
        clearTimeout(loadTimer);
        if (!componentDataDialog.open) return;
        status.hidden = false;
        message.textContent = message.dataset.error;
        retry.hidden = false;
        frame.hidden = true;
    };
    const loadFrame = (url) => {
        if (status) {
            clearTimeout(loadTimer);
            status.hidden = false;
            retry.hidden = true;
            frame.hidden = false;
            message.textContent = message.dataset.loading;
            loadTimer = setTimeout(showLoadFailure, 15000);
        }
        frame.src = url;
    };
    if (status) {
        retry.addEventListener('click', () => loadFrame(frame.getAttribute('src')));
        frame.addEventListener('load', () => {
            if (!componentDataDialog.open || !frame.hasAttribute('src')) return;
            const loaded = frame.contentDocument;
            if (loaded?.URL === 'about:blank') return;
            if (!loaded?.querySelector('[data-component-data-frame]')) {
                showLoadFailure();
                return;
            }
            clearTimeout(loadTimer);
            status.hidden = true;
            frame.hidden = false;
        });
    }
    const open = (url, wideUrl, title) => {
        if (componentDataDialog.open) return;
        if (heading) frame.title = heading.textContent = title || defaultTitle;
        loadFrame(url);
        if (wide) wide.href = wideUrl;
        componentDataDialog.showModal();
    };
    if (isLayout) openLayoutEditor = open;
    if (isPage) {
        openPageEditor = (href) => {
            const url = new URL(href, location.href);
            url.searchParams.set('embed', '1');
            open(url.href);
        };
        componentDataDialog.addEventListener('cancel', (event) => {
            if (frame.contentDocument?.dispatchEvent(new Event('ewc:page-editor-close', {cancelable: true})) === false) {
                event.preventDefault();
            }
        });
    }
    document.addEventListener('click', (event) => {
        const button = event.target.closest(selector);
        if (!button || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
        event.preventDefault();
        if (isShop) {
            opener = button;
            open(button.href, null, button.dataset.editorModalLabel);
        } else if (isPage) openPageEditor(button.href);
        else open(isLayout ? button.dataset.layoutEditModal : (isBanner ? button.dataset.bannerEditorUrl : button.dataset.editorDataUrl),
            button.dataset.editorDataWide, button.dataset.editorModalLabel || (wide ? button.textContent.trim() : undefined));
    });
    window.addEventListener('message', (event) => {
        if (!componentDataDialog.open || event.origin !== location.origin || event.source !== frame.contentWindow
            || event.data?.type !== 'eurowebcart:component-data-saved') return;
        saved = true;
        if (isShop) {
            if (opener?.hasAttribute('data-shop-edit-current') && typeof event.data.target === 'string'
                && event.data.target.startsWith('/') && !event.data.target.startsWith('//')) {
                const next = new URL(event.data.target, location.href);
                if (next.origin === location.origin && next.pathname !== location.pathname) target = next.href;
            }
            componentDataDialog.close();
        }
    });
    componentDataDialog.addEventListener('close', () => {
        if (componentDataDialog.open) return;
        clearTimeout(loadTimer);
        frame.removeAttribute('src');
        if (target) location.assign(target);
        else if (saved) location.reload();
        else if (isShop) opener?.focus({preventScroll:true});
    });
});

document.querySelectorAll('[data-inline-editor]').forEach((form) => {
    const source = form.elements.code;
    const preview = form.querySelector('[data-copy-code-text]');
    const manualCode = form.querySelector('[data-copy-code-editor]');
    const pencil = form.querySelector('[data-copy-code-pencil]');
    const publishCode = () => {
        preview.textContent = source.value;
        if (!manualCode.hidden && manualCode.value !== source.value) manualCode.value = source.value;
        form.querySelector('[data-copy-code-status]').textContent = '';
    };
    pencil.addEventListener('click', () => {
        const editing = manualCode.hidden;
        manualCode.hidden = !editing;
        preview.hidden = editing;
        pencil.setAttribute('aria-expanded', String(editing));
        if (editing) {
            manualCode.value = source.value;
            manualCode.focus();
        }
    });
    manualCode.addEventListener('input', () => {
        source.value = manualCode.value;
        publishCode();
    });
    const openingTag = /^\[(?:[^"\]]+|"[^"]*")+\]/;
    const attribute = /[ \t]+([a-z][a-z0-9_-]*)=(?:"[^"]*"|[^ \t\]]+)/gi;
    const dimensions = new Set(['width', 'height', 'mobile-height', 'height-mobile']);
    const modifyOpening = (code, name, value) => code.replace(openingTag, (opening) => {
        const clear = opening.replace(attribute, (part, key) => {
            return key === name || (name === 'mobile-height' && key === 'height-mobile') ? '' : part;
        });
        return clear.slice(0, -1) + (value ? ' ' + name + '=' + value : '') + ']';
    });
    form.addEventListener('change', (event) => {
        const kind = form.dataset.inlineEditor;
        if (event.target.matches('[data-inline-field]')) {
            const original = source.value;
            const preserved = Array.from((original.match(openingTag)?.[0] || '').matchAll(attribute))
                .filter((part) => dimensions.has(part[1]) || (kind === 'map' && ['url', 'button'].includes(part[1])));
            const fields = Object.fromEntries(Array.from(form.querySelectorAll('[data-inline-field]'),
                (input) => [input.dataset.inlineField, input.value.trim()]));
            if (kind === 'iblock') {
                const field = event.target.dataset.inlineField;
                if (fields.path === ':new:') return;
                if (field === 'limit' && !event.target.validity.valid) return;
                let code = (original.match(openingTag)?.[0] || '[iblock=' + fields.template + ']')
                    + fields.path + '[/iblock]';
                if (field === 'template') {
                    code = code.replace(/^\[iblock=[^\s\]]+/i, '[iblock=' + fields.template);
                } else if (field !== 'path') {
                    const value = fields[field];
                    const encoded = !value || field === 'limit' ? value : '"'
                        + value.replace(/[&"\[\]]/g, char => '&#' + char.charCodeAt(0) + ';') + '"';
                    code = modifyOpening(code, field, encoded);
                }
                source.value = code;
                publishCode();
                return;
            }
            if (['map', 'maps', 'maplist', 'points', 'route', 'callback'].includes(kind)) {
                const section = fields.section;
                delete fields.section;
                const options = Object.entries(fields).filter(([, value]) => value !== '')
                    .map(([name, value]) => name + '="' + value.replace(/&/g, '&amp;').replace(/"/g, '&quot;') + '"');
                source.value = '[' + kind + (options.length ? ' ' + options.join(' ') : '') + ']';
                if (section) source.value += section + '[/' + kind + ']';
            } else if (['gal', 'fotos', 'files'].includes(kind)) {
                const opening = source.value.match(/^\[[^\]]+\]/)?.[0] || '[' + kind + ']';
                source.value = opening + fields.path + '[/' + kind + ']';
            } else if (kind === 'include') {
                source.value = '[include]' + fields.path + '[/include]';
            } else if (kind === 'phpModule') {
                source.value = '[php]' + fields.code + '[/php]';
            } else if (kind === 'iconName') {
                source.value = '[fa-icon=' + (fields.style ? fields.style + ':' : '') + fields.code + ']';
            } else if (kind === 'extensionTag') {
                source.value = '[' + fields.code + ']' + fields.value + '[/' + fields.code + ']';
            } else {
                source.value = '[' + fields.code + ']';
            }
            if (!['gal', 'fotos', 'files', 'route'].includes(kind)) {
                for (const part of preserved) {
                    source.value = modifyOpening(source.value, part[1], part[0].slice(part[0].indexOf('=') + 1));
                }
            }
            publishCode();
            return;
        }
        if (!event.target.matches('[data-inline-option]')) return;
        if (kind === 'callback') {
            source.value = source.value.replace(/^\[callback=([^\]]+)\]/i, (_, label) =>
                '[callback button="' + label.replaceAll('&', '&amp;').replaceAll('"', '&quot;') + '"]');
        }
        source.value = modifyOpening(source.value, event.target.dataset.inlineOption, event.target.value);
        publishCode();
    });
});

const editableLinks = document.querySelectorAll('[data-inline-component]');
const layoutMarkers = [...document.querySelectorAll('[data-layout-edit-start]')];
const layoutList = document.querySelector('[data-page-layout-list]');
if (layoutList) {
    const found = new Set();
    layoutMarkers.forEach((marker) => {
        const name = marker.dataset.layoutEditName;
        if (found.has(name)) return;
        found.add(name);
        const item = document.createElement('li');
        const link = document.createElement('a');
        link.href = marker.dataset.layoutEditUrl;
        link.dataset.layoutEditControl = '';
        link.dataset.layoutEditModal = marker.dataset.layoutEditModal;
        link.setAttribute('aria-haspopup', 'dialog');
        link.textContent = '[' + name + ']';
        item.append(link);
        layoutList.append(item);
    });
    layoutList.previousElementSibling.hidden = found.size > 0;
}

const layoutOutline = document.querySelector('[data-layout-edit-outline]');
if (layoutOutline && openLayoutEditor) {
    const edit = layoutOutline.querySelector('[data-layout-edit-control]');
    const hint = layoutOutline.querySelector('[data-layout-edit-hint]');
    const templateLabel = edit.textContent;
    const templateHint = hint?.textContent || '';
    const markers = [...document.querySelectorAll('[data-layout-edit-start], [data-page-edit-start], [data-snippet-edit-start]')];
    const pageEnds = [...document.querySelectorAll('[data-page-edit-end]')];
    const spans = markers.reverse().flatMap((start) => {
        const { pageEditStart, layoutEditStart, pageEditUrl, layoutEditUrl, layoutEditModal,
            snippetEditStart, snippetEditUrl, snippetEditName } = start.dataset;
        const end = pageEditStart ? pageEnds.find((marker) =>
            marker.dataset.pageEditEnd === pageEditStart && (start.compareDocumentPosition(marker) & Node.DOCUMENT_POSITION_FOLLOWING))
            : document.getElementById(snippetEditStart || layoutEditStart);
        if (!end) return [];
        const range = document.createRange();
        range.setStartAfter(start);
        range.setEndBefore(end);
        const label = snippetEditName ? layoutOutline.dataset.snippetLabel + ' [' + snippetEditName + ']' : '';
        return [{ range, href: snippetEditUrl || pageEditUrl || layoutEditUrl, modal: snippetEditUrl || layoutEditModal, label }];
    });
    const pageLink = document.querySelector('.editor-bar [data-page-editor-link]');
    const heading = document.querySelector('#content > h1');
    const contentStart = document.querySelector('#content [data-page-edit-start]');
    if (pageLink && heading && (!contentStart || (heading.compareDocumentPosition(contentStart) & Node.DOCUMENT_POSITION_FOLLOWING))) {
        const range = document.createRange();
        range.selectNode(heading);
        const url = new URL(pageLink.href);
        url.hash = 'page-heading';
        spans.unshift({ range, href: url.href });
    }
    const priorityControls = '.banner-edit-tools, [data-inline-component], [data-reviews-edit], dialog[open], .editor-bar';
    const containsPoint = (box, x, y) => box.width > 0 && box.height > 0
        && x >= box.left && x < box.right && y >= box.top && y < box.bottom;
    // The inner content range takes priority over its enclosing layout.
    spans.sort((left, right) => Number(Boolean(right.label)) - Number(Boolean(left.label)));
    const layoutAt = (x, y) => spans.find(({ range }) => containsPoint(range.getBoundingClientRect(), x, y)
        && Array.from(range.getClientRects()).some((box) => containsPoint(box, x, y)));
    let request = 0;
    let active = null;
    const hide = () => { cancelAnimationFrame(request); active = null; layoutOutline.hidden = true; };
    let pointer;
    const show = (candidate) => {
        active = candidate;
        const { range, href, modal, label } = candidate;
        const box = range.getBoundingClientRect();
        const x = Math.max(2, box.left);
        const y = Math.max(2, box.top);
        Object.assign(layoutOutline.style, {
            left: x + 'px', top: y + 'px', width: Math.max(0, Math.min(innerWidth - 2, box.right) - x) + 'px',
            height: Math.max(0, Math.min(innerHeight - 2, box.bottom) - y) + 'px',
        });
        edit.href = href;
        if (modal) edit.dataset.layoutEditModal = modal;
        else delete edit.dataset.layoutEditModal;
        edit.toggleAttribute('data-page-editor-link', !modal);
        layoutOutline.toggleAttribute('data-snippet-highlight', Boolean(label));
        edit.dataset.editorModalLabel = label || '';
        edit.textContent = label || (modal ? templateLabel : (layoutOutline.dataset.pageLabel || templateLabel));
        if (hint) {
            hint.hidden = Boolean(label);
            hint.textContent = modal ? templateHint : (layoutOutline.dataset.pageHint || '');
        }
        layoutOutline.hidden = false;
    };
    document.addEventListener('dblclick', (event) => {
        if (event.ctrlKey || event.metaKey || event.shiftKey || event.altKey
            || event.target.closest(priorityControls + ', a, button, input, select, textarea, label, summary, [contenteditable]')) return;
        const candidate = layoutAt(event.clientX, event.clientY);
        if (!candidate) return;
        event.preventDefault();
        hide();
        if (candidate.modal) openLayoutEditor(candidate.modal, null, candidate.label);
        else if (openPageEditor) openPageEditor(candidate.href);
        else location.assign(candidate.href);
    });
    document.addEventListener('pointermove', (event) => {
        if (event.pointerType === 'touch') { pointer = null; return; }
        pointer = [event.clientX, event.clientY];
        const overEdit = edit.contains(event.target);
        if (overEdit && active?.label) return;
        const underEdit = overEdit && document.elementsFromPoint(event.clientX, event.clientY)
            .find((element) => !layoutOutline.contains(element));
        if ((underEdit || event.target).closest(priorityControls)) { hide(); return; }
        cancelAnimationFrame(request);
        request = requestAnimationFrame(() => {
            const candidate = layoutAt(event.clientX, event.clientY);
            if (overEdit && active && (!candidate || spans.indexOf(active) <= spans.indexOf(candidate))) return;
            if (!candidate) { hide(); return; }
            show(candidate);
        });
    });
    window.addEventListener('scroll', () => {
        hide();
        if (!pointer) return;
        request = requestAnimationFrame(() => {
            if (!pointer || document.elementFromPoint(...pointer)?.closest(priorityControls)) return;
            const candidate = layoutAt(...pointer);
            if (candidate?.label) show(candidate);
        });
    }, { passive: true });
    window.addEventListener('resize', hide);
    document.documentElement.addEventListener('mouseleave', () => { pointer = null; hide(); });
}
if (editableLinks.length) {
    const outline = document.createElement('div');
    outline.hidden = true;
    outline.className = 'inline-component-highlight';
    outline.setAttribute('aria-hidden', 'true');
    document.body.appendChild(outline);
    let activeLink = null;
    const bounds = (link) => {
        const end = document.getElementById(link.hash.slice(1) + '-preview-end');
        if (!end) return null;
        const range = document.createRange();
        range.setStartAfter(link);
        range.setEndBefore(end);
        const rect = range.getBoundingClientRect();
        if (rect.width && rect.height) return rect;
        if (link.dataset.inlineKind === 'map') {
            return document.querySelector('#content .site-map')?.getBoundingClientRect() || null;
        }
        return null;
    };
    const draw = () => {
        const rect = activeLink && bounds(activeLink);
        outline.hidden = !rect;
        if (!rect) return;
        outline.style.left = (rect.left - 3) + 'px';
        outline.style.top = (rect.top - 3) + 'px';
        outline.style.width = (rect.width + 6) + 'px';
        outline.style.height = (rect.height + 6) + 'px';
    };
    editableLinks.forEach((link) => {
        const show = () => { activeLink = link; draw(); };
        const hide = () => {
            if (activeLink === link) { activeLink = null; draw(); }
        };
        link.addEventListener('mouseenter', show);
        link.addEventListener('focus', show);
        link.addEventListener('mouseleave', hide);
        link.addEventListener('blur', hide);
        link.addEventListener('click', hide);
    });
    window.addEventListener('resize', draw, {passive: true});
    window.addEventListener('scroll', draw, {passive: true, capture: true});
}

document.addEventListener('click', (event) => {
    const top = event.target.closest('[data-scroll-top]');
    if (top) {
        event.preventDefault();
        window.scrollTo({top: 0, behavior: window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth'});
        return;
    }
    const mapConsent = event.target.closest('[data-map-consent]');
    if (mapConsent) {
        const current = mapConsent.dataset.mapConsent.split('-');
        const permissions = ['analytics', 'maps', 'chat'].filter((feature) => feature === 'maps' || current.includes(feature)).join('-');
        const selected = permissions === 'analytics-maps' ? 'all' : permissions;
        const path = /^\/[^;\s]*$/.test(mapConsent.dataset.cookiePath || '') ? mapConsent.dataset.cookiePath : '/';
        document.cookie = 'FILE_SITE_CONSENT=' + selected + '; Path=' + path + '; Max-Age=15552000; SameSite=Lax'
            + (location.protocol === 'https:' ? '; Secure' : '');
        window.location.reload();
        return;
    }
    const button = event.target.closest('[data-cookie-choice]');
    const menu = button && button.closest('[data-cookie-preferences]');
    if (!menu) return;
    const choice = button.dataset.cookieChoice;
    if (!['all', 'selection', 'essential'].includes(choice)) return;
    const features = ['analytics', 'maps', 'chat'].filter(feature => {
        const option = menu.querySelector('[data-cookie-option="' + feature + '"]');
        return option && (choice === 'all' || (choice === 'selection' && option.checked));
    });
    const selection = features.join('-');
    const selected = selection === 'analytics-maps' ? 'all' : selection || 'essential';
    const path = /^\/[^;\s]*$/.test(menu.dataset.cookiePath || '') ? menu.dataset.cookiePath : '/';
    document.cookie = 'FILE_SITE_CONSENT=' + selected + '; Path=' + path + '; Max-Age=15552000; SameSite=Lax'
        + (location.protocol === 'https:' ? '; Secure' : '');
    window.location.reload();
});

(() => {
    const selectShopPhoto = (thumb) => {
        const gallery = thumb.closest('[data-shop-gallery]');
        const main = gallery.querySelector('[data-shop-gallery-open]');
        main.href = thumb.href;
        main.title = thumb.dataset.photoAlt;
        main.dataset.photoIndex = thumb.dataset.shopGalleryThumb;
        const image = main.querySelector('img');
        image.src = thumb.href;
        image.alt = thumb.dataset.photoAlt;
        const caption = main.querySelector('[data-shop-gallery-caption]');
        if (caption) {
            caption.textContent = thumb.dataset.photoCaption;
            caption.hidden = caption.textContent === '';
        }
        gallery.querySelector('[data-shop-gallery-thumb][aria-current]')?.removeAttribute('aria-current');
        thumb.setAttribute('aria-current', 'true');
    };
    document.addEventListener('pointerover', (event) => {
        const thumb = event.target.closest('[data-shop-gallery-thumb]');
        if (thumb && event.pointerType !== 'touch') selectShopPhoto(thumb);
    });
    document.addEventListener('focusin', (event) => {
        if (event.target.matches('[data-shop-gallery-thumb]')) selectShopPhoto(event.target);
    });
    document.addEventListener('click', function (event) {
        const thumb = event.target.closest('[data-shop-gallery-thumb]');
        if (thumb && !event.ctrlKey && !event.metaKey && !event.shiftKey && !event.altKey) {
            event.preventDefault();
            selectShopPhoto(thumb);
            return;
        }
        const link = event.target.closest('[data-kt-photo], [data-shop-gallery-open]');
        if (link && !event.ctrlKey && !event.metaKey && !event.shiftKey && !event.altKey && typeof HTMLDialogElement !== 'undefined') {
            const dialog = document.createElement('dialog');
            dialog.className = link.matches('[data-shop-gallery-open]') ? 'kt-lightbox shop-gallery-dialog' : 'kt-lightbox';
            if (typeof dialog.showModal !== 'function') return;
            event.preventDefault();
            const bar = document.createElement('div');
            bar.className = 'kt-lightbox-bar';
            const caption = document.createElement('strong');
            const close = document.createElement('button');
            close.type = 'button';
            const collection = link.closest('[data-kt-collection], [data-shop-gallery]');
            close.className = 'dialog-close';
            close.setAttribute('aria-label', collection.dataset.photoClose);
            close.addEventListener('click', function () { dialog.close(); });
            const image = document.createElement('img');
            const fallback = link.dataset.caption ?? link.querySelector('img')?.alt ?? '';
            const photos = JSON.parse(link.dataset.photos || '[]').map((photo) => typeof photo === 'string' ? {url:photo, alt:fallback} : photo);
            if (!photos.length) photos.push({url:link.href, alt:fallback});
            let position = Math.max(0, Math.min(photos.length - 1, Number(link.dataset.photoIndex) || 0));
            const showPhoto = () => {
                const photo = photos[position];
                image.src = photo.url;
                image.alt = caption.textContent = photo.alt;
                dialog.setAttribute('aria-label', photo.alt);
            };
            showPhoto();
            bar.append(caption, close);
            dialog.append(bar, image);
            if (photos.length > 1) {
                const controls = document.createElement('div');
                controls.className = 'kt-lightbox-controls kt-lightbox-bar';
                const status = document.createElement('span');
                status.setAttribute('role', 'status');
                const show = function (offset) {
                    position = (position + offset + photos.length) % photos.length;
                    showPhoto();
                    status.textContent = `${position + 1} / ${photos.length}`;
                };
                const control = function (label, offset) {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.textContent = label;
                    button.addEventListener('click', function () { show(offset); });
                    return button;
                };
                controls.append(control(collection.dataset.photoPrev, -1), status, control(collection.dataset.photoNext, 1));
                dialog.append(controls);
                dialog.addEventListener('keydown', function (key) {
                    if (key.key === 'ArrowLeft' || key.key === 'ArrowRight') { key.preventDefault(); show(key.key === 'ArrowLeft' ? -1 : 1); }
                });
                show(0);
            }
            dialog.addEventListener('close', function () { dialog.remove(); link.focus({preventScroll:true}); }, { once: true });
            dialog.addEventListener('click', function (click) { if (click.target === dialog) dialog.close(); });
            document.body.append(dialog);
            dialog.showModal();
        }

        const filter = event.target.closest('[data-kt-filter]');
        if (!filter) return;
        const collection = filter.closest('[data-kt-collection]');
        if (!collection) return;
        collection.querySelectorAll('[data-kt-filter]').forEach(function (button) {
            button.setAttribute('aria-pressed', String(button === filter));
        });
        collection.querySelectorAll('[data-kt-category]').forEach(function (card) {
            card.hidden = filter.dataset.ktFilter !== '' && card.dataset.ktCategory !== filter.dataset.ktFilter;
        });
    });

    function konturEnableFilters() {
        document.querySelectorAll('[data-kt-filters]').forEach(function (filters) { filters.hidden = false; });
    }
    konturEnableFilters();
    new MutationObserver(konturEnableFilters).observe(document.body, { childList: true, subtree: true });

})();
document.querySelectorAll('.editor-bar').forEach((bar) => {
    const tooltip = document.createElement('span');
    tooltip.className = 'editor-bar-tooltip';
    tooltip.setAttribute('aria-hidden', 'true');
    tooltip.hidden = true;
    bar.append(tooltip);
    let hovered = null;
    let focused = null;
    const update = () => {
        const control = hovered || focused;
        tooltip.hidden = !control;
        if (!control) return;
        tooltip.textContent = control.dataset.navTooltip;
        const bounds = bar.getBoundingClientRect();
        const item = control.getBoundingClientRect();
        const width = tooltip.offsetWidth;
        const center = item.left - bounds.left + (item.width - width) / 2;
        tooltip.style.left = Math.max(6, Math.min(center, bounds.width - width - 6)) + 'px';
        tooltip.style.top = (item.bottom - bounds.top + 5) + 'px';
    };
    const findControl = (node) => node?.closest?.('.toolbar-item[data-nav-tooltip]') || null;
    bar.addEventListener('pointerover', (event) => {
        if (event.pointerType === 'touch') return;
        hovered = findControl(event.target);
        update();
    });
    bar.addEventListener('pointerleave', () => { hovered = null; update(); });
    bar.addEventListener('focusin', (event) => {
        focused = event.target.matches(':focus-visible') ? findControl(event.target) : null;
        if (focused) hovered = null;
        update();
    });
    bar.addEventListener('focusout', () => { focused = null; update(); });
    window.addEventListener('resize', update, {passive: true});
});



document.addEventListener('click', async (event) => {
    const link = event.target.closest('[data-reviews-more]');
    if (!link || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
    event.preventDefault();
    if (link.getAttribute('aria-busy') === 'true') return;
    const section = link.closest('.site-reviews');
    const feedback = section.querySelector('[data-reviews-feedback]');
    link.setAttribute('aria-busy', 'true');
    feedback.hidden = true;
    try {
        const response = await fetch(link.href, {cache: 'no-store'});
        if (!response.ok) throw new Error('Review page unavailable');
        const markup = new DOMParser().parseFromString(await response.text(), 'text/html');
        const nextSection = markup.getElementById(section.id);
        const incoming = nextSection?.querySelector('[data-review-list]');
        if (!incoming) throw new Error('Review list unavailable');
        const list = section.querySelector('[data-review-list]');
        const first = incoming.firstElementChild;
        list.append(...incoming.children);
        if (first) {
            first.tabIndex = -1;
            first.focus();
        }
        const nextLink = nextSection.querySelector('[data-reviews-more]');
        if (nextLink) link.href = nextLink.getAttribute('href');
        else if (!first && document.activeElement === link) {
            section.tabIndex = -1;
            section.focus();
        }
        if (!nextLink) link.remove();
    } catch (error) {
        feedback.textContent = link.dataset.loadError;
        feedback.hidden = false;
    } finally {
        link.removeAttribute('aria-busy');
    }
});
(() => {
    if (typeof HTMLDialogElement === 'undefined' || !HTMLDialogElement.prototype.showModal) return;
    let dialog;
    let title;
    let close;
    let status;
    let content;
    let pending = false;

    const openCart = (result) => {
        if (!dialog) {
            dialog = document.createElement('dialog');
            dialog.className = 'editor-dialog shop-cart-dialog';
            dialog.setAttribute('aria-labelledby', 'shop-cart-dialog-title');
            const header = document.createElement('div');
            header.className = 'shop-cart-dialog-heading';
            title = document.createElement('h2');
            title.id = 'shop-cart-dialog-title';
            close = document.createElement('button');
            close.type = 'button';
            close.className = 'dialog-close';
            close.addEventListener('click', () => dialog.close());
            status = document.createElement('p');
            status.setAttribute('role', 'status');
            content = document.createElement('div');
            header.append(title, close);
            dialog.append(header, status, content);
            document.body.append(dialog);
        }
        title.textContent = result.title;
        close.setAttribute('aria-label', result.close);
        status.textContent = result.message;
        status.hidden = !result.message;
        content.innerHTML = result.html;
        document.querySelectorAll('.shop-basket strong').forEach((badge) => {
            badge.textContent = String(result.count);
        });
        if (!dialog.open) dialog.showModal();
        else if (!dialog.contains(document.activeElement)) close.focus();
    };

    const fetchCart = async (endpoint, options, source) => {
        if (pending) return;
        pending = true;
        source.setAttribute('aria-busy', 'true');
        try {
            const response = await fetch(endpoint, {...options, headers: {Accept: 'application/json'}});
            if (!response.headers.get('Content-Type')?.includes('application/json')) throw new Error();
            const result = await response.json();
            openCart(result);
            status.classList.toggle('error', !response.ok);
        } catch {
            if (source.matches('a')) {
                location.href = source.href;
            } else {
                let message = source.querySelector('[data-shop-cart-error]');
                if (!message) {
                    message = document.createElement('p');
                    message.dataset.shopCartError = '1';
                    message.className = 'notice error';
                    message.setAttribute('role', 'status');
                    source.append(message);
                }
                message.textContent = source.dataset.error;
            }
        } finally {
            source.removeAttribute('aria-busy');
            pending = false;
        }
    };
    document.addEventListener('submit', (event) => {
        const form = event.target.closest('[data-shop-cart-form]');
        if (!form) return;
        event.preventDefault();
        const data = new FormData(form);
        const button = event.submitter;
        if (button?.name) data.append(button.name, button.value);
        fetchCart(form.getAttribute('action'), {method: 'POST', body: data}, form);
    });
    document.addEventListener('click', (event) => {
        const link = event.target.closest('a.shop-basket');
        if (!link || event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
        if (new URL(link.href).origin !== location.origin) return;
        event.preventDefault();
        fetchCart(link.href, {}, link);
    });
})();

document.addEventListener('change', (event) => {
    if (event.target.matches('[data-shop-auto-sort]')) event.target.form.requestSubmit();
});

document.querySelectorAll('[data-shop-carousel]').forEach((carousel) => {
    const track = carousel.querySelector('.shop-carousel-track');
    const controls = carousel.querySelector('[data-shop-carousel-controls]');
    const buttons = [...controls.querySelectorAll('button')];
    const update = () => {
        const end = track.scrollWidth - track.clientWidth;
        controls.hidden = end <= 1;
        buttons[0].disabled = track.scrollLeft <= 1;
        buttons[1].disabled = track.scrollLeft >= end - 1;
    };
    controls.addEventListener('click', (event) => {
        const button = event.target.closest('[data-shop-carousel-step]');
        if (!button) return;
        const card = track.firstElementChild;
        const gap = parseFloat(getComputedStyle(track).columnGap) || 0;
        const width = card.getBoundingClientRect().width + gap;
        const distance = Math.max(1, Math.floor((track.clientWidth + gap + 1) / width)) * width;
        track.scrollBy({left:Number(button.dataset.shopCarouselStep) * distance,
            behavior:matchMedia('(prefers-reduced-motion: reduce)').matches ? 'instant' : 'smooth'});
    });
    track.addEventListener('scroll', update, {passive:true});
    if (typeof ResizeObserver === 'function') new ResizeObserver(update).observe(track);
    else window.addEventListener('resize', update);
    update();
});

document.querySelectorAll('[data-shop-live-filter]').forEach((form) => {
    const apply = form.querySelector('button[type="submit"]');
    const status = form.querySelector('[data-shop-filter-status]');
    const catalog = form.closest('[data-shop-catalog]');
    const listing = catalog?.querySelector('[data-shop-listing]');
    let timer;
    let pending;
    apply.hidden = true;
    form.querySelector('[data-shop-filter-reset]')?.addEventListener('click', (event) => {
        if (event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
        event.preventDefault();
        form.querySelectorAll('fieldset select').forEach((select) => { select.value = ''; });
        form.querySelectorAll('fieldset input[type="checkbox"]').forEach((box) => { box.checked = false; });
        form.requestSubmit();
    });
    form.addEventListener('input', (event) => {
        clearTimeout(timer);
        pending?.abort();
        const delay = event.target.type === 'number' ? 350 : 0;
        timer = setTimeout(() => form.requestSubmit(), delay);
    });
    if (!listing) return;
    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        clearTimeout(timer);
        pending?.abort();
        const request = new AbortController();
        pending = request;
        const url = new URL(form.getAttribute('action'), location.href);
        url.search = new URLSearchParams(new FormData(form)).toString();
        const index = Array.from(document.querySelectorAll('[data-shop-catalog]')).indexOf(catalog);
        listing.setAttribute('aria-busy', 'true');
        status.hidden = true;
        try {
            const response = await fetch(url, {signal: request.signal});
            if (!response.ok) throw new Error();
            const documentCopy = new DOMParser().parseFromString(await response.text(), 'text/html');
            const updated = documentCopy.querySelectorAll('[data-shop-catalog]')[index]?.querySelector('[data-shop-listing]');
            if (!updated) throw new Error();
            if (request.signal.aborted) return;
            listing.replaceChildren(...updated.childNodes);
            history.replaceState(history.state, '', url);
            apply.hidden = true;
        } catch (error) {
            if (request.signal.aborted) return;
            status.textContent = form.dataset.error;
            status.hidden = false;
            apply.hidden = false;
        } finally {
            if (pending === request) listing.removeAttribute('aria-busy');
        }
    });
});

document.addEventListener('toggle', (event) => {
    const current = event.target;
    if (!current.matches?.('[data-shop-popover]') || !current.open) return;
    const area = current.closest('[data-shop-catalog]') || current.closest('.shop-controls');
    area?.querySelectorAll('[data-shop-popover][open]').forEach((other) => {
        if (other !== current) other.open = false;
    });
    current.classList.remove('shop-popover-end');
    const panel = current.querySelector('.shop-category-panel, .shop-filter-popover');
    if (panel && panel.getBoundingClientRect().right > document.documentElement.clientWidth - 12) {
        current.classList.add('shop-popover-end');
    }
}, true);
document.addEventListener('click', (event) => {
    if (event.target.closest('[data-shop-popover]')) return;
    document.querySelectorAll('[data-shop-popover][open]').forEach((details) => { details.open = false; });
});
document.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape') return;
    const details = event.target.closest('[data-shop-popover][open]');
    if (details) {
        details.open = false;
        details.querySelector('summary').focus();
    }
});
document.querySelectorAll('details.cookie-preferences').forEach((menu) => {
    const panel = menu.querySelector(':scope > .panel');
    if (!panel) return;
    const fit = () => {
        if (!menu.open) return;
        panel.style.removeProperty('--cookie-panel-offset');
        panel.style.setProperty('--cookie-panel-height', Math.max(96, menu.getBoundingClientRect().top - 24) + 'px');
        const box = panel.getBoundingClientRect();
        const right = document.documentElement.clientWidth - 12;
        const offset = box.right > right ? right - box.right : Math.max(0, 12 - box.left);
        panel.style.setProperty('--cookie-panel-offset', (getComputedStyle(menu).direction === 'rtl' ? -offset : offset) + 'px');
    };
    menu.addEventListener('toggle', fit);
    window.addEventListener('resize', fit);
    menu.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && menu.open) {
            menu.open = false;
            menu.querySelector('summary').focus();
        }
    });
    fit();
});

document.querySelectorAll('[data-shop-checkout-method]').forEach((method) => {
    const checkout = method.closest('.shop-checkout-layout');
    const address = checkout.querySelector('[data-shop-checkout-address]');
    const shipping = checkout.querySelector('[data-shop-checkout-shipping]');
    const pickup = checkout.querySelector('[data-shop-checkout-pickup]');
    const total = checkout.querySelector('[data-shop-checkout-total]');
    const sync = () => {
        const delivery = method.value === 'shipping';
        address.hidden = !delivery;
        address.querySelector('input').required = delivery;
        address.querySelector('input').disabled = !delivery;
        shipping.hidden = !delivery;
        if (pickup) pickup.hidden = delivery;
        total.textContent = delivery ? total.dataset.shipping : total.dataset.pickup;
    };
    method.addEventListener('change', sync);
    window.addEventListener('pageshow', sync);
    sync();
});

document.addEventListener('click', async (event) => {
    const link = event.target.closest('a[data-shop-quick-order]');
    if (!link || event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey
        || typeof HTMLDialogElement === 'undefined' || link.origin !== location.origin || link.hasAttribute('aria-busy')) return;
    event.preventDefault();
    link.setAttribute('aria-busy', 'true');
    const loadPanel = async (url, options) => {
        const response = await fetch(url, options);
        if (!response.ok || new URL(response.url).origin !== location.origin) throw new Error();
        const page = new DOMParser().parseFromString(await response.text(), 'text/html');
        const panel = page.querySelector('[data-shop-quick-panel]');
        if (!panel) throw new Error();
        return document.importNode(panel, true);
    };
    try {
        const dialog = document.createElement('dialog');
        dialog.className = 'shop-quick-dialog';
        const header = document.createElement('div');
        header.className = 'header-actions';
        const title = document.createElement('h2');
        const close = document.createElement('button');
        close.type = 'button';
        close.className = 'dialog-close';
        close.setAttribute('aria-label', link.dataset.close);
        close.addEventListener('click', () => dialog.close());
        header.append(title, close);
        dialog.append(header);
        let panel = await loadPanel(link.href);
        const update = (next) => {
            title.textContent = next.dataset.title;
            dialog.setAttribute('aria-label', next.dataset.title);
            if (panel.isConnected) panel.replaceWith(next);
            else dialog.append(next);
            panel = next;
        };
        update(panel);
        dialog.addEventListener('submit', async (submission) => {
            const form = submission.target;
            if (submission.defaultPrevented || !form.matches('[data-shop-checkout-form]')) return;
            submission.preventDefault();
            if (form.hasAttribute('aria-busy')) return;
            const button = form.querySelector('button[type=submit]');
            const payload = new FormData(form);
            form.setAttribute('aria-busy', 'true');
            button.disabled = true;
            try {
                const next = await loadPanel(form.getAttribute('action'), {method:'POST', body:payload});
                update(next);
                if (next.dataset.reserved) {
                    document.querySelectorAll('a[data-shop-quick-order]').forEach((target) => {
                        if (target.href !== link.href) return;
                        const badge = document.createElement('small');
                        badge.className = 'shop-stock-status shop-stock-reserved';
                        badge.textContent = next.dataset.reserved;
                        target.replaceWith(badge);
                    });
                }
                (next.querySelector('[type=tel]') ?? close).focus();
            } catch {
                let notice = panel.querySelector('[role=alert]');
                if (!notice) {
                    notice = document.createElement('p');
                    notice.className = 'notice error';
                    notice.setAttribute('role', 'alert');
                    panel.prepend(notice);
                }
                notice.textContent = link.dataset.error;
            } finally {
                form.removeAttribute('aria-busy');
                button.disabled = false;
            }
        });
        dialog.addEventListener('close', () => {
            dialog.remove();
            if (link.isConnected) link.focus();
        }, {once:true});
        document.body.append(dialog);
        dialog.showModal();
        (panel.querySelector('[type=tel]') ?? close).focus();
    } catch {
        location.assign(link.href);
    } finally {
        link.removeAttribute('aria-busy');
    }
});
