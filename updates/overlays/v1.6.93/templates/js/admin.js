const adminMessages = JSON.parse(document.querySelector('#admin-messages')?.textContent || '{}');
const adminText = (key) => adminMessages[key] || key;
document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-gallery-add], [data-gallery-remove]');
    const gallery = button?.closest('[data-product-gallery]');
    if (!gallery) return;
    const rows = gallery.querySelector('[data-gallery-rows]');
    if (button.hasAttribute('data-gallery-remove')) {
        button.closest('[data-gallery-row]').remove();
        gallery.querySelector('[data-gallery-add]').focus();
    } else if (rows.children.length < 50) {
        const row = gallery.querySelector('[data-gallery-template]').content.cloneNode(true);
        const index = Number(gallery.dataset.galleryNext);
        gallery.dataset.galleryNext = String(index + 1);
        row.querySelectorAll('[name]').forEach((input) => { input.name = input.name.replace('_row_', index); });
        rows.append(row);
        rows.lastElementChild.querySelector('[data-shop-choose-image]')?.focus();
    }
});
if (typeof Intl.DisplayNames === 'function') {
    const regionNames = new Intl.DisplayNames([document.documentElement.lang || 'ru'], { type: 'region' });
    document.querySelectorAll('[data-phone-country] [data-calling]').forEach((option) => {
        option.textContent = regionNames.of(option.value) + ' (+' + option.dataset.calling + ')';
    });
}

document.querySelectorAll('[data-city-phone-input]').forEach((field) => {
    const error = field.parentElement.querySelector('[data-city-phone-error]');
    const validate = (showError) => {
        const value = field.value.trim();
        const digits = value.replace(/[^0-9]/g, '');
        const literal = /^\+[0-9 ().-]{6,31}$/.test(value) && /^[1-9][0-9]{6,14}$/.test(digits)
            && (!digits.startsWith('7') || digits.length === 11);
        const valid = !value || /^\[[a-z][a-z0-9_-]{0,63}\]$/i.test(value) || literal;
        field.setCustomValidity(valid ? '' : error.textContent);
        const announce = !valid && showError;
        error.hidden = !announce;
        if (announce) field.setAttribute('aria-invalid', 'true');
        else field.removeAttribute('aria-invalid');
    };
    field.addEventListener('input', () => validate(true));
    field.addEventListener('blur', () => validate(true));
    field.addEventListener('invalid', () => {
        field.closest('details').open = true;
        validate(true);
    });
    validate(false);
});

document.querySelectorAll('[data-page-template-catalog]').forEach((catalog) => {
    const search = catalog.querySelector('[data-template-search]');
    const kind = catalog.querySelector('[data-template-kind]');
    const empty = catalog.querySelector('[data-template-empty]');
    const rows = [...catalog.querySelectorAll('[data-template-row]')].map((row) => ({
        element: row,
        name: row.querySelector('[data-template-name]').textContent.trim().toLocaleLowerCase(),
    }));
    const filter = () => {
        const query = search.value.trim().toLocaleLowerCase();
        let visible = false;
        rows.forEach(({ element, name }) => {
            element.hidden = !name.includes(query) || (kind.value !== '' && element.dataset.templateRow !== kind.value);
            if (!element.hidden) visible = true;
        });
        empty.hidden = visible;
    };
    catalog.querySelector('[data-template-filters]').hidden = false;
    search.addEventListener('input', filter);
    kind.addEventListener('change', filter);
    filter();
});

document.querySelectorAll('[data-page-template-picker]').forEach((picker) => {
    const form = picker.closest('form');
    const editor = form.querySelector('[data-content-editor]');
    const source = editor.querySelector('[data-editor-source]');
    const select = picker.querySelector('[data-page-template-select]');
    const apply = picker.querySelector('[data-page-template-apply]');
    const open = picker.querySelector('[data-page-template-open]');
    const templateUrl = new URL(open.href);
    const status = picker.querySelector('[data-page-template-status]');
    const names = ['Title', 'Heading', 'Description', 'assets[CSS]', 'assets[JS]'];
    const fields = names.map((name) => form.elements.namedItem(name));
    const snapshot = () => [source.value, ...fields.map((field) => field.value)];
    let loaded = snapshot();
    const syncActions = () => {
        apply.disabled = !select.value;
        open.hidden = !select.value;
        templateUrl.searchParams.set('template', select.value);
        templateUrl.hash = 'template-edit';
        open.href = templateUrl.href;
    };
    picker.hidden = false;
    syncActions();
    select.addEventListener('change', syncActions);
    apply.addEventListener('click', async () => {
        editor.dispatchEvent(new Event('get-editor-source'));
        if (snapshot().some((value, index) => value !== loaded[index]) && !window.confirm(picker.dataset.confirm)) return;
        apply.disabled = true;
        select.disabled = true;
        status.hidden = true;
        const before = snapshot();
        try {
            const url = new URL(picker.dataset.url, location.href);
            url.searchParams.set('preset_data', select.value);
            const response = await fetch(url, { credentials: 'same-origin', headers: { Accept: 'application/json' } });
            if (!response.ok) throw new Error();
            const { preset } = await response.json();
            editor.dispatchEvent(new Event('get-editor-source'));
            if (snapshot().some((value, index) => value !== before[index]) && !window.confirm(picker.dataset.confirm)) return;
            editor.dispatchEvent(new CustomEvent('set-editor-source', { detail: preset.html }));
            [preset.Title, preset.Heading, preset.Description, preset.assets.CSS, preset.assets.JS].forEach((value, index) => {
                const field = fields[index];
                field.value = value;
                field.dispatchEvent(new Event('input', { bubbles: true }));
            });
            loaded = snapshot();
        } catch (error) {
            status.textContent = picker.dataset.error;
            status.hidden = false;
        } finally {
            select.disabled = false;
            syncActions();
        }
    });
});

(() => {
    let root = document.querySelector('[data-shop-category-editor]');
    if (!root) return;
    let dialog = root.querySelector('[data-shop-category-dialog]');
    let opener = null;
    let busy = false;
    const storageKey = 'ewc-shop-sections:' + new URL(root.dataset.url, location.href).pathname;
    let collapsed = new Set();
    try {
        const stored = JSON.parse(localStorage.getItem(storageKey));
        if (Array.isArray(stored)) collapsed = new Set(stored.filter((item) => typeof item === 'string'));
    } catch {}
    let rows;
    const redraw = () => {
        rows.forEach((row, key) => {
            let parent = row.dataset.shopCategoryParent;
            let shown = true;
            while (parent && rows.has(parent)) {
                if (collapsed.has(parent)) { shown = false; break; }
                parent = rows.get(parent).dataset.shopCategoryParent;
            }
            row.hidden = !shown;
            const expand = row.querySelector('[data-shop-category-expand]');
            if (expand) { expand.hidden = false; expand.setAttribute('aria-expanded', String(!collapsed.has(key))); }
            const handle = row.querySelector('[data-shop-category-drag]');
            if (handle) handle.hidden = false;
        });
    };
    const initializeTree = () => {
        rows = new Map(Array.from(root.querySelectorAll('[data-shop-category-id]'), (row) => [row.dataset.shopCategoryId, row]));
        redraw();
    };
    const remember = () => {
        try { localStorage.setItem(storageKey, JSON.stringify([...collapsed])); } catch {}
    };
    const close = () => {
        if (!dialog || busy) return;
        dialog.close();
        dialog.remove();
        dialog = null;
        if (new URL(location.href).searchParams.has('category_action')) history.replaceState(history.state, '', root.dataset.url);
        opener?.focus({preventScroll:true});
    };
    const show = (next) => {
        dialog = next;
        if (dialog.tagName === 'DIALOG') {
            dialog.removeAttribute('open');
            dialog.showModal();
        }
        const visual = dialog.querySelector('[data-category-visual-type]');
        if (visual) {
            const update = () => dialog.querySelectorAll('[data-category-visual]').forEach((panel) => {
                panel.hidden = panel.dataset.categoryVisual !== visual.value;
            });
            visual.addEventListener('change', update);
            dialog.querySelectorAll('[data-category-icon]').forEach((button) => {
                button.addEventListener('click', () => {
                    const input = dialog.querySelector('input[name="icon"]');
                    input.value = button.dataset.categoryIcon;
                    input.focus();
                });
            });
            update();
        }
        dialog.addEventListener('cancel', (event) => { event.preventDefault(); close(); });
    };
    if (dialog?.matches('.shop-focused-editor')) {
        show(dialog);
        return;
    }
    const load = async (url, form = null) => {
        if (busy) return;
        busy = true;
        const y = window.scrollY;
        const submit = form?.querySelector('button[type="submit"]');
        if (submit) submit.disabled = true;
        root.setAttribute('aria-busy', 'true');
        try {
            const response = await fetch(url, form ? {method:'POST', body:new FormData(form), cache:'no-store'} : {cache:'no-store'});
            if (!response.ok) throw new Error();
            const page = new DOMParser().parseFromString(await response.text(), 'text/html');
            const next = page.querySelector('[data-shop-category-editor]');
            if (!next) throw new Error();
            const nextDialog = next.querySelector('[data-shop-category-dialog]');
            if (!nextDialog && next.dataset.serverError) {
                const region = root.querySelector('[data-category-message]');
                region.textContent = next.dataset.serverError;
                region.hidden = false;
                return;
            }
            if (dialog) { dialog.close(); dialog.remove(); dialog = null; }
            if (nextDialog) {
                root.append(nextDialog);
                show(nextDialog);
            } else {
                const oldUrl = opener?.href;
                root.replaceWith(next);
                root = next;
                initializeTree();
                if (form) history.replaceState(history.state, '', root.dataset.url);
                if (oldUrl) Array.from(root.querySelectorAll('a')).find((link) => link.href === oldUrl)?.focus({preventScroll:true});
            }
            window.scrollTo({top:y, behavior:'instant'});
        } catch {
            const region = dialog?.querySelector('.notice.error') ?? root.querySelector('[data-category-message]');
            region.textContent = root.dataset.error;
            region.hidden = false;
        } finally {
            root.removeAttribute('aria-busy');
            if (submit?.isConnected) submit.disabled = false;
            busy = false;
        }
    };
    document.addEventListener('click', (event) => {
        const branch = event.target.closest('[data-shop-category-expand]');
        if (branch && root.contains(branch)) {
            const id = branch.closest('[data-shop-category-id]').dataset.shopCategoryId;
            if (collapsed.has(id)) collapsed.delete(id);
            else collapsed.add(id);
            remember();
            redraw();
            return;
        }
        const link = event.target.closest('a[href]');
        if (!link || !root.contains(link) || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
        if (link.matches('[data-category-close]')) { event.preventDefault(); close(); return; }
        const href = new URL(link.href);
        if (!href.searchParams.has('category_action')) return;
        event.preventDefault();
        opener = link;
        void load(href);
    });
    document.addEventListener('submit', (event) => {
        if (!dialog?.contains(event.target)) return;
        event.preventDefault();
        void load(event.target.getAttribute('action'), event.target);
    });
    const move = (id, target, spot) => {
        if (busy || id === target) return;
        const form = root.querySelector('[data-shop-category-sort-form]');
        form.elements.namedItem('id').value = id;
        form.elements.namedItem('target').value = target;
        form.elements.namedItem('position').value = spot;
        if (spot === 'inside') { collapsed.delete(target); remember(); }
        void load(root.dataset.url, form);
    };
    let drag = null;
    let targetRow = null;
    const clearTarget = () => {
        if (targetRow) delete targetRow.dataset.categoryDrop;
        targetRow = null;
    };
    document.addEventListener('pointerdown', (event) => {
        const handle = event.target.closest('[data-shop-category-drag]');
        if (!handle || !root.contains(handle) || event.button !== 0 || busy) return;
        const id = handle.closest('[data-shop-category-id]').dataset.shopCategoryId;
        drag = {id, pointer:event.pointerId};
        handle.setPointerCapture(event.pointerId);
        handle.dataset.moving = '';
    });
    document.addEventListener('pointermove', (event) => {
        if (!drag || event.pointerId !== drag.pointer) return;
        clearTarget();
        const candidate = document.elementFromPoint(event.clientX, event.clientY)?.closest('[data-shop-category-id]');
        if (!candidate || !root.contains(candidate)) return;
        for (let id = candidate.dataset.shopCategoryId; id && rows.has(id); id = rows.get(id).dataset.shopCategoryParent) {
            if (id === drag.id) return;
        }
        const bounds = candidate.getBoundingClientRect();
        const offset = (event.clientY - bounds.top) / bounds.height;
        targetRow = candidate;
        targetRow.dataset.categoryDrop = offset < .26 ? 'before' : (offset > .74 ? 'after' : 'inside');
    });
    document.addEventListener('pointerup', (event) => {
        if (!drag || event.pointerId !== drag.pointer || !targetRow) return;
        move(drag.id, targetRow.dataset.shopCategoryId, targetRow.dataset.categoryDrop);
    });
    document.addEventListener('lostpointercapture', (event) => {
        if (!drag || event.pointerId !== drag.pointer) return;
        delete event.target.dataset.moving;
        drag = null;
        clearTarget();
    });
    document.addEventListener('keydown', (event) => {
        const handle = event.target.closest('[data-shop-category-drag]');
        if (!handle || !root.contains(handle) || !['ArrowUp','ArrowDown'].includes(event.key)) return;
        event.preventDefault();
        const row = handle.closest('[data-shop-category-id]');
        const siblings = Array.from(rows.values()).filter((entry) => entry.dataset.shopCategoryParent === row.dataset.shopCategoryParent);
        const before = event.key === 'ArrowUp';
        const next = siblings[siblings.indexOf(row) + (before ? -1 : 1)];
        if (next) move(row.dataset.shopCategoryId, next.dataset.shopCategoryId, before ? 'before' : 'after');
    });
    initializeTree();
    if (dialog) show(dialog);
})();

const shopPriceWrites = new Set();
const shopPriceText = (value) => value.replace(/[\s\p{Z}]+/gu, '').replace(/,/g, '.');
const shopNumericSelector = '[data-shop-money], [data-shop-number], [data-shop-digits]';
const validateShopMoney = (input, finish = false) => {
    const previous = input.value;
    const cursor = input.selectionStart;
    const money = input.matches('[data-shop-money]');
    const digits = input.matches('[data-shop-digits]');
    const normalize = (value) => digits ? value.replace(/[\s\p{Z}]+/gu, '') : shopPriceText(value);
    let value = normalize(previous);
    if (finish && !digits) {
        value = value.replace(/^([+-]?)\./, (_, sign) => sign + '0.');
        if (value.endsWith('.')) value = value.slice(0, -1);
    }
    if (value !== previous) {
        input.value = value;
        if (!finish && cursor !== null) {
            const position = normalize(previous.slice(0, cursor)).length;
            input.setSelectionRange(position, position);
        }
    }
    const shape = money ? /^(?:[0-9]{1,7}(?:\.[0-9]{0,2})?|\.[0-9]{1,2})$/
        : (digits ? /^[0-9]+$/ : /^[+-]?(?:[0-9]+(?:\.[0-9]*)?|\.[0-9]+)$/);
    const partial = !finish && (money ? value === '.' : (!digits && /^[+-]?\.?$/.test(value)));
    const valid = shape.test(value) && (input.maxLength < 0 || value.length <= input.maxLength);
    const error = !value || partial ? '' : (!valid ? input.dataset.format : (money && Number(value) === 0 ? input.dataset.positive : ''));
    input.setCustomValidity(error);
    if (error) input.setAttribute('aria-invalid', 'true');
    else input.removeAttribute('aria-invalid');
    let feedback = input.closest('[data-shop-price-form]')?.querySelector('.shop-price-status')
        ?? input.parentElement.querySelector('.shop-price-feedback');
    if (!feedback && error) {
        feedback = document.createElement('small');
        feedback.className = 'shop-price-feedback';
        feedback.setAttribute('role', 'status');
        input.after(feedback);
    }
    if (feedback) {
        feedback.textContent = error;
        if (error) feedback.dataset.error = 'true';
        else delete feedback.dataset.error;
    }
    return !error && (value !== '' || !input.required);
};
document.addEventListener('input', (event) => {
    if (event.target.matches(shopNumericSelector)) validateShopMoney(event.target);
});
document.addEventListener('focusout', (event) => {
    if (event.target.matches(shopNumericSelector) && !event.target.closest('[data-shop-price-form]')) validateShopMoney(event.target, true);
});
const syncShopPriceFields = ({ target }) => {
    const form = target.form;
    const quick = form?.querySelector('[data-shop-price-quick]');
    if (!quick || !target.matches('[data-shop-money]')) return;
    const name = target.dataset.shopPriceMirror;
    const other = name ? form.elements.namedItem(name) : Array.from(quick.querySelectorAll('[data-shop-price-mirror]'))
        .find(input => input.dataset.shopPriceMirror === target.name);
    if (other) {
        other.value = target.value;
        validateShopMoney(other);
    }
};
document.addEventListener('input', syncShopPriceFields);
document.addEventListener('focusout', syncShopPriceFields);

const initializeShopPrices = (list) => list.querySelectorAll('[data-shop-price-form]').forEach((form) => {
    const price = form.elements.namedItem('price') ?? form.elements.namedItem('value');
    if (!price) return;
    const previous = form.elements.previous;
    const status = form.querySelector('[role="status"]');
    const cell = form.closest('td');
    const range = cell.querySelector('[data-shop-price-range]');
    const fallback = cell.querySelector('[data-shop-price-fallback]');
    if (fallback) fallback.hidden = true;
    form.hidden = false;
    let savedPrice = price.value;
    price.addEventListener('input', () => {
        price.removeAttribute('aria-invalid');
        status.textContent = '';
        delete status.dataset.error;
    });
    const savePrice = async () => {
        if (price.readOnly || price.value.trim() === savedPrice) return;
        if (!validateShopMoney(price, true) || price.value === savedPrice) return;
        status.textContent = '';
        price.readOnly = true;
        form.setAttribute('aria-busy', 'true');
        try {
            const response = await fetch(form.getAttribute('action'), {
                method: 'POST', body: new FormData(form), headers: {Accept: 'application/json'},
            });
            const result = await response.json();
            if (!response.ok || !result.ok) {
                if (response.status === 422) price.setAttribute('aria-invalid', 'true');
                throw new Error(result.message || form.dataset.error);
            }
            previous.value = result.minor ?? result.value;
            price.value = result.price ?? result.value;
            savedPrice = price.value;
            if (range) range.textContent = range.title = result.range;
            const summary = cell.querySelector('[data-shop-inline-summary]');
            if (summary && result.summary !== null) summary.textContent = result.summary;
            const revision = list.querySelector('[data-shop-bulk] [name="revision"]');
            if (revision?.value === result.previousRevision) revision.value = result.revision;
            status.textContent = result.message;
            delete status.dataset.error;
        } catch (error) {
            status.textContent = error.name === 'SyntaxError' ? form.dataset.error : (error.message || form.dataset.error);
            status.dataset.error = 'true';
        } finally {
            form.removeAttribute('aria-busy');
            price.readOnly = false;
        }
    };
    const commit = () => {
        const request = savePrice();
        shopPriceWrites.add(request);
        void request.finally(() => shopPriceWrites.delete(request));
    };
    price.addEventListener('blur', commit);
    form.addEventListener('submit', (event) => {
        event.preventDefault();
        commit();
    });
});

const initializeShopColumns = (list) => list.querySelectorAll('[data-shop-column-table]').forEach((table) => {
    const columns = Array.from(table.querySelectorAll('col[data-column]'));
    const group = table.querySelector('colgroup');
    const fixed = 1;
    const handles = Array.from(table.querySelectorAll('[data-shop-resize]'));
    const suffix = new URL(table.dataset.shopColumnTable, location.href).pathname;
    const storageKey = 'ewc-shop-column-widths:' + suffix;
    const orderKey = 'ewc-shop-column-order:' + suffix;
    const widths = {};
    try {
        const stored = JSON.parse(localStorage.getItem(storageKey)) || {};
        for (const [key, width] of Object.entries(stored)) {
            if (/^[a-z0-9_-]{1,75}$/.test(key) && Number.isFinite(width) && width >= 72 && width <= 1200) widths[key] = width;
        }
    } catch {}
    let measured;
    const measure = () => {
        measured = handles.map((handle) => Math.round(handle.closest('th').getBoundingClientRect().width));
        table.classList.add('is-column-sized');
    };
    const apply = () => {
        let total = table.querySelector('.shop-product-select').getBoundingClientRect().width
            + table.querySelector('.shop-actions-cell').getBoundingClientRect().width;
        columns.forEach((column, index) => {
            const width = widths[column.dataset.column] ?? measured[index];
            column.style.width = width + 'px';
            total += width;
        });
        table.style.width = total + 'px';
    };
    const persist = () => {
        try { localStorage.setItem(storageKey, JSON.stringify(widths)); } catch {}
    };
    if (columns.some((column) => widths[column.dataset.column] !== undefined)) {
        measure();
        apply();
    }
    const direction = getComputedStyle(table).direction === 'rtl' ? -1 : 1;
    const moveHandles = Array.from(table.querySelectorAll('[data-shop-move]'));
    const rows = [Array.from(group.children)];
    for (const row of table.rows) {
        if (row.cells.length === group.children.length) rows.push(Array.from(row.cells));
    }
    const keys = moveHandles.map((handle) => handle.dataset.shopMove);
    let order = keys;
    try {
        const saved = JSON.parse(localStorage.getItem(orderKey));
        if (Array.isArray(saved)) {
            order = [...new Set(saved.filter((key) => typeof key === 'string' && /^[a-z0-9_-]{1,75}$/.test(key)).concat(keys))];
        }
    } catch {}
    const reorder = () => {
        const indexes = order.filter((key) => keys.includes(key)).map((key) => keys.indexOf(key));
        for (const row of rows) {
            const parent = row[0].parentNode;
            for (const index of indexes) parent.insertBefore(row[index + fixed], row[keys.length + fixed]);
        }
    };
    const move = (key, target) => {
        if (target === key || !target) return;
        const placeAfter = order.indexOf(key) < order.indexOf(target);
        order = order.filter((item) => item !== key);
        order.splice(order.indexOf(target) + Number(placeAfter), 0, key);
        reorder();
        try { localStorage.setItem(orderKey, JSON.stringify(order)); } catch {}
    };
    reorder();
    moveHandles.forEach((handle) => {
        const key = handle.dataset.shopMove;
        let dragging = null;
        let highlight = null;
        handle.hidden = false;
        handle.addEventListener('pointerdown', (event) => {
            if (event.button !== 0) return;
            dragging = event.pointerId;
            handle.setPointerCapture(event.pointerId);
            handle.dataset.moving = '';
        });
        handle.addEventListener('pointermove', (event) => {
            if (dragging !== event.pointerId) return;
            const hit = document.elementFromPoint(event.clientX, event.clientY)?.closest('th');
            const target = hit?.closest('table') === table && hit.querySelector('[data-shop-move]') ? hit : null;
            if (highlight) delete highlight.dataset.shopDrop;
            highlight = target;
            if (highlight) highlight.dataset.shopDrop = '';
        });
        handle.addEventListener('pointerup', (event) => {
            if (dragging === event.pointerId) move(key, highlight?.querySelector('[data-shop-move]').dataset.shopMove);
        });
        handle.addEventListener('lostpointercapture', () => {
            dragging = null;
            delete handle.dataset.moving;
            if (highlight) delete highlight.dataset.shopDrop;
            highlight = null;
        });
        handle.addEventListener('keydown', (event) => {
            if (!['ArrowLeft', 'ArrowRight'].includes(event.key)) return;
            event.preventDefault();
            const visible = order.filter((item) => keys.includes(item));
            const step = (event.key === 'ArrowRight' ? 1 : -1) * direction;
            move(key, visible[visible.indexOf(key) + step]);
            handle.focus({preventScroll:true});
        });
    });
    handles.forEach((handle, index) => {
        const key = handle.dataset.shopResize;
        const minimum = key === 'price' ? 120 : 72;
        const current = () => widths[key] ?? measured[index];
        const setWidth = (width) => {
            widths[key] = Math.max(minimum, Math.min(1200, Math.round(width)));
            apply();
        };
        let drag;
        handle.hidden = false;
        handle.addEventListener('pointerdown', (event) => {
            if (event.button !== 0 || drag) return;
            if (!measured) measure();
            drag = {id: event.pointerId, x: event.clientX, width: current()};
            handle.setPointerCapture(event.pointerId);
            handle.dataset.resizing = '';
        });
        handle.addEventListener('pointermove', (event) => {
            if (drag?.id === event.pointerId) setWidth(drag.width + (event.clientX - drag.x) * direction);
        });
        handle.addEventListener('lostpointercapture', () => {
            if (!drag) return;
            drag = null;
            delete handle.dataset.resizing;
            persist();
        });
        handle.addEventListener('keydown', (event) => {
            if (!['ArrowLeft', 'ArrowRight'].includes(event.key)) return;
            event.preventDefault();
            if (!measured) measure();
            setWidth(current() + (event.key === 'ArrowRight' ? 16 : -16) * direction);
            persist();
        });
    });
});

const initializeShopCharacteristics = (dialog) => {
    const section = dialog.querySelector('[data-shop-characteristics]');
    if (!section) return;
    const category = dialog.querySelector('[name="category_id"]');
    const available = Array.from(section.querySelector('[data-characteristic-options]').content.querySelectorAll('option'));
    const parents = new Map(Array.from(category.options, (option) => [option.value, option.dataset.parent]));
    const choices = section.querySelector('datalist');
    const rows = section.querySelector('[data-characteristic-rows]');
    const add = section.querySelector('[data-characteristic-add]');
    const displayValue = (row) => {
        const select = row.querySelector('[data-characteristic-input="type"]');
        const current = row.querySelector('[data-characteristic-input="value"]');
        const type = select.value;
        const tag = type === 'textarea' ? 'textarea' : (type === 'bool' ? 'select' : 'input');
        const field = document.createElement(tag);
        field.name = current.name;
        field.dataset.characteristicInput = 'value';
        if (tag === 'textarea') field.rows = 3;
        if (tag !== 'select') field.maxLength = 500;
        if (tag === 'input' && ['date','url','email','tel'].includes(type)) field.type = type;
        if (type === 'bool') {
            field.append(new Option('—', ''), new Option(section.dataset.yes, '1'), new Option(section.dataset.no, '0'));
        } else if (type === 'price' || type === 'number') {
            field.inputMode = 'decimal';
            if (type === 'price') {
                field.dataset.shopMoney = '';
                field.dataset.format = section.dataset.priceFormat;
                field.dataset.positive = section.dataset.positive;
            } else {
                field.dataset.shopNumber = '';
                field.dataset.format = section.dataset.numberFormat;
            }
        }
        field.value = current.value;
        current.parentElement.querySelector('.shop-price-feedback')?.remove();
        current.replaceWith(field);
    };
    const fromCategory = () => {
        const scopes = new Set();
        for (let id = category.value; id && !scopes.has(id); id = parents.get(id)) scopes.add(id);
        scopes.add('');
        const fields = new Map();
        for (const option of available) {
            if (!scopes.has(option.dataset.scope)) continue;
            const key = option.value.toLowerCase();
            if (!fields.has(key) || option.dataset.scope === category.value) fields.set(key, option);
        }
        choices.replaceChildren(...Array.from(fields.values(), (option) => option.cloneNode(true)));
    };
    category.addEventListener('change', fromCategory);
    fromCategory();
    section.addEventListener('change', (event) => {
        const control = event.target.dataset.characteristicInput;
        const row = event.target.closest('.shop-characteristic-fields');
        if (control === 'type') { displayValue(row); return; }
        if (control !== 'name') return;
        const value = event.target.value.trim().toLowerCase();
        const field = Array.from(choices.options).find((option) => option.value.toLowerCase() === value);
        const unit = row.querySelector('[data-characteristic-input="unit"]');
        if (!unit.value || unit.value === unit.dataset.suggested) unit.value = unit.dataset.suggested = field?.dataset.unit ?? '';
        const type = row.querySelector('[data-characteristic-input="type"]');
        if (field && type.value !== field.dataset.type) {
            type.value = field.dataset.type;
            displayValue(row);
        }
    });
    Array.from(rows.children).forEach(displayValue);
    add.hidden = false;
    add.disabled = rows.children.length >= 25;
    add.addEventListener('click', () => {
        const row = section.querySelector('[data-characteristic-row]').content.firstElementChild.cloneNode(true);
        const index = rows.children.length;
        row.querySelectorAll('[data-characteristic-input]').forEach((input) => {
            input.name = 'characteristics[' + index + '][' + input.dataset.characteristicInput + ']';
        });
        rows.append(row);
        displayValue(row);
        row.querySelector('input').focus();
        add.disabled = rows.children.length >= 25;
    });
};

(() => {
    let list = document.querySelector('[data-shop-product-list]');
    let report = document.querySelector('[data-shop-feed-report]');
    let dialog = document.querySelector('[data-shop-product-dialog]');
    if (!list && !report && !dialog) return;
    const photo = document.querySelector('[data-shop-photo-dialog]');
    let photoOpener;
    if (photo) {
        photo.querySelector('[data-shop-photo-close]').addEventListener('click', () => photo.close());
        photo.addEventListener('click', (event) => { if (event.target === photo) photo.close(); });
        photo.addEventListener('close', () => {
            photo.querySelector('img').removeAttribute('src');
            photoOpener?.focus({preventScroll:true});
        });
    }
    let opener = null;
    let pending = false;
    const initialize = () => {
        if (!list) return;
        initializeShopColumns(list);
        initializeShopPrices(list);
        const bulk = list.querySelector('[data-shop-bulk]');
        if (!bulk) return;
        const all = list.querySelector('[data-shop-select-all]');
        const boxes = Array.from(list.querySelectorAll('[data-shop-select]'));
        const controls = Array.from(bulk.querySelectorAll('button[type="submit"]'));
        const update = () => {
            const count = boxes.filter((box) => box.checked).length;
            all.checked = count === boxes.length;
            all.indeterminate = count > 0 && !all.checked;
            controls.forEach((button) => { button.disabled = count === 0; });
        };
        all.hidden = false;
        all.addEventListener('change', () => {
            boxes.forEach((box) => { box.checked = all.checked; });
        });
        list.addEventListener('change', update);
        update();
    };
    const show = (message, error = false) => {
        const region = (report || list).querySelector('[data-shop-list-message]');
        region.textContent = message;
        region.classList.toggle('error', error);
        region.hidden = message === '';
    };
    const open = (node, dynamic = false) => {
        dialog = node;
        if (dialog.tagName === 'DIALOG') {
            dialog.removeAttribute('open');
            dialog.querySelector('[data-shop-product-close]').setAttribute('autofocus', '');
            dialog.showModal();
        }
        initializeShopCharacteristics(dialog);
        const tabs = dialog.querySelector('[data-shop-card-tabs]');
        if (tabs) {
            const buttons = Array.from(tabs.querySelectorAll('[data-shop-card-tab]'));
            const panels = Array.from(dialog.querySelectorAll('[data-shop-card-panel]'));
            const switchTab = (name, focus = false) => {
                panels.forEach((panel) => {
                    panel.setAttribute('role', 'tabpanel');
                    panel.hidden = panel.dataset.shopCardPanel !== name;
                });
                buttons.forEach((button) => {
                    const selected = button.dataset.shopCardTab === name;
                    button.setAttribute('aria-selected', String(selected));
                    button.tabIndex = selected ? 0 : -1;
                    if (selected && focus) button.focus();
                });
            };
            tabs.hidden = false;
            dialog.querySelector('[data-shop-price-quick]')?.removeAttribute('hidden');
            switchTab(tabs.dataset.selected);
            buttons.forEach((button, index) => {
                button.addEventListener('click', () => switchTab(button.dataset.shopCardTab));
                button.addEventListener('keydown', (event) => {
                    if (event.key !== 'ArrowLeft' && event.key !== 'ArrowRight') return;
                    event.preventDefault();
                    const direction = event.key === 'ArrowRight' ? 1 : -1;
                    switchTab(buttons[(index + direction + buttons.length) % buttons.length].dataset.shopCardTab, true);
                });
            });
            dialog.addEventListener('invalid', (event) => {
                const panel = event.target.closest('[data-shop-card-panel]');
                if (panel?.hidden) switchTab(panel.dataset.shopCardPanel);
                const offer = event.target.closest('.shop-product-offer');
                if (offer) offer.open = true;
            }, true);
        }
        dialog.addEventListener('cancel', (event) => {
            event.preventDefault();
            if (pending) return;
            if (!report && new URL(location.href).searchParams.has('feed_offer')) {
                location.assign(dialog.querySelector('[data-shop-product-close]').href);
            } else close();
        });
        if (dynamic) {
            dialog.querySelectorAll('[data-content-editor]').forEach((editor) => {
                editor.dispatchEvent(new Event('ewc:content-editor-added', {bubbles:true}));
            });
        }
    };
    if (dialog?.matches('.shop-focused-editor')) {
        open(dialog);
        return;
    }
    const closeFocus = () => {
        if (opener?.isConnected) opener.focus({preventScroll:true});
        else report?.querySelector('.admin-heading a')?.focus({preventScroll:true});
    };
    const close = (restoreFocus = true) => {
        if (!dialog) return;
        const current = new URL(location.href);
        if (restoreFocus && current.searchParams.has('product_action')) {
            const back = dialog.querySelector('[data-shop-product-close]').href;
            history.replaceState(history.state, '', back);
        }
        dialog.querySelectorAll('[data-content-editor]').forEach((editor) => editor.dispatchEvent(new Event('ewc:content-editor-remove')));
        dialog.close();
        dialog.remove();
        dialog = null;
        if (restoreFocus) closeFocus();
    };
    const load = async (url, {form, submitter, confirmed = false, modal = false, back = false} = {}) => {
        if (pending) return;
        pending = true;
        const y = window.scrollY;
        const tableScroll = list?.querySelector('.shop-product-table-wrap').scrollLeft;
        (report || list).setAttribute('aria-busy', 'true');
        try {
            await Promise.allSettled([...shopPriceWrites]);
            const body = form ? new FormData(form) : null;
            if (body && submitter?.name) body.set(submitter.name, submitter.value);
            if (body && confirmed) body.set('confirm', '1');
            if (submitter) submitter.disabled = true;
            const response = await fetch(url, body ? {method:'POST', body, cache:'no-store'} : {cache:'no-store'});
            if (!response.ok) throw new Error();
            const doc = new DOMParser().parseFromString(await response.text(), 'text/html');
            const nextList = doc.querySelector('[data-shop-product-list]');
            const nextReport = report ? doc.querySelector('[data-shop-feed-report]') : null;
            const nextDialog = doc.querySelector('[data-shop-product-dialog]');
            if ((!nextList && !nextReport) || (modal && !nextDialog) || (report && !nextDialog && !nextReport)) throw new Error();
            if (!nextDialog && nextList?.dataset.serverError) {
                show(nextList.dataset.serverError, true);
                return;
            }
            if (nextDialog) {
                close(false);
                (report || list).after(nextDialog);
                open(nextDialog, true);
            } else if (nextReport) {
                const href = opener?.getAttribute('href');
                close(false);
                report.replaceWith(nextReport);
                report = nextReport;
                opener = Array.from(report.querySelectorAll('[data-shop-feed-edit]')).find((link) => link.getAttribute('href') === href);
                closeFocus();
                show(report.dataset.notice);
            } else {
                close(false);
                const columnsOpen = list.querySelector('.shop-product-columns').open;
                list.replaceWith(nextList);
                list = nextList;
                initialize();
                list.querySelector('.shop-product-columns').open = columnsOpen;
                list.querySelector('.shop-product-table-wrap').scrollLeft = tableScroll;
                show(list.dataset.notice);
                if (!back) {
                    const next = new URL(response.url);
                    next.searchParams.delete('columns_set');
                    next.searchParams.delete('columns[]');
                    next.searchParams.delete('sku_control');
                    if (next.searchParams.has('p')) next.searchParams.set('p', list.dataset.page);
                    history[body ? 'replaceState' : 'pushState'](history.state, '', next);
                }
            }
            window.scrollTo({top:y, behavior:'instant'});
        } catch {
            if (form && dialog) {
                let error = dialog.querySelector('.notice.error');
                if (!error) {
                    error = document.createElement('p');
                    error.className = 'notice error';
                    error.setAttribute('role', 'alert');
                    dialog.querySelector('.editor-dialog-heading').after(error);
                }
                error.textContent = (report || list).dataset.error;
            } else {
                show((report || list).dataset.error, true);
            }
        } finally {
            if (submitter?.isConnected) submitter.disabled = false;
            (report || list).removeAttribute('aria-busy');
            pending = false;
        }
    };
    document.addEventListener('click', (event) => {
        const link = event.target.closest('a[href]');
        if (!link || event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
        if (dialog?.contains(link) && link.matches('[data-shop-product-close]')) {
            if (!report && new URL(location.href).searchParams.has('feed_offer')) return;
            event.preventDefault();
            if (!pending) close();
            return;
        }
        if (!list?.contains(link) && !(report?.contains(link) && link.matches('[data-shop-feed-edit]'))) return;
        if (link.matches('[data-shop-photo]') && photo && typeof photo.showModal === 'function') {
            event.preventDefault();
            const image = photo.querySelector('img');
            image.alt = link.querySelector('img').alt;
            image.src = link.href;
            photoOpener = link;
            photo.showModal();
            return;
        }
        const url = new URL(link.href);
        if (url.origin !== location.origin || url.searchParams.get('shop') !== '1') return;
        event.preventDefault();
        opener = link;
        void load(url, {modal: url.searchParams.has('product_action')});
    });
    document.addEventListener('submit', (event) => {
        const form = event.target;
        if (form.matches('[data-shop-price-form]')) return;
        if (dialog?.contains(form)) {
            form.querySelectorAll('[data-content-editor]').forEach((editor) => editor.dispatchEvent(new Event('get-editor-source')));
            if (!report && new URL(location.href).searchParams.has('feed_offer')) return;
            event.preventDefault();
            void load(form.getAttribute('action'), {form, submitter:event.submitter});
        } else if (list?.contains(form) && form.matches('[data-shop-bulk]')) {
            event.preventDefault();
            const deleting = event.submitter?.value === 'delete';
            if (deleting && !window.confirm(form.dataset.confirm)) return;
            void load(form.getAttribute('action'), {form, submitter:event.submitter, confirmed:deleting});
        } else if (list?.contains(form) && form.method === 'get') {
            event.preventDefault();
            const url = new URL(form.action);
            url.search = new URLSearchParams(new FormData(form));
            void load(url);
        }
    });
    if (list) window.addEventListener('popstate', () => { void load(location.href, {back:true}); });
    initialize();
    if (dialog) open(dialog);
})();

document.querySelectorAll('[data-media-cleanup]').forEach((form) => {
    const all = form.querySelector('[data-media-cleanup-all]');
    if (!all) return;
    const files = Array.from(form.querySelectorAll('[name="media[]"]'));
    const update = () => {
        const selected = files.filter((file) => file.checked).length;
        all.checked = selected === files.length;
        all.indeterminate = selected > 0 && selected < files.length;
    };
    all.closest('label').hidden = false;
    all.addEventListener('change', () => {
        files.forEach((file) => { file.checked = all.checked; });
        update();
    });
    files.forEach((file) => file.addEventListener('change', update));
    update();
});

document.querySelectorAll('[data-active-theme-select]').forEach((select) => {
    const badges = select.closest('.panel').querySelector('[data-active-theme-badges]');
    const update = () => {
        const features = JSON.parse(select.selectedOptions[0].dataset.themeFeatures);
        badges.querySelectorAll('[data-theme-feature]').forEach((badge) => {
            const active = features[badge.dataset.themeFeature];
            badge.toggleAttribute('data-supported', active);
            badge.querySelector('[data-theme-feature-value]').textContent = active ? badges.dataset.yes : badges.dataset.no;
        });
    };
    select.addEventListener('change', update);
    update();
});

document.querySelectorAll('[data-theme-demo]').forEach((form) => {
    const all = form.querySelector('[data-theme-demo-all]');
    const sections = Array.from(form.querySelectorAll('[name="sections[]"]'));
    const submit = form.querySelector('button[type="submit"]');
    const confirmation = form.querySelector('[data-theme-demo-confirm]');
    const confirm = confirmation.querySelector('input');
    const update = () => {
        const count = sections.filter((section) => section.checked).length;
        all.checked = count === sections.length;
        all.indeterminate = count > 0 && !all.checked;
        const replace = form.elements.catalog_mode.value === 'replace';
        confirmation.hidden = !replace;
        confirm.required = replace;
        if (!replace) confirm.checked = false;
        submit.disabled = count === 0 || submit.dataset.moduleDisabled === '1';
    };
    all.hidden = false;
    all.closest('label').hidden = false;
    all.addEventListener('change', () => {
        sections.forEach((section) => { section.checked = all.checked; });
        update();
    });
    form.addEventListener('change', update);
    update();
});

const pageTemplateDialog = document.querySelector('[data-page-template-dialog]');
if (pageTemplateDialog) {
    const picker = pageTemplateDialog.querySelector('[data-page-template-icon-picker]');
    const field = pageTemplateDialog.querySelector('[data-page-template-icon-code]');
    const showIcons = siteIconPicker(picker, (item, code) => {
        field.value = code;
        picker.open = false;
        field.focus();
    });
    picker.addEventListener('toggle', () => {
        if (picker.open) showIcons();
    });
}
document.querySelectorAll('[data-page-template-dialog], [data-shop-fragment-form]').forEach((editor) => {
    const source = editor.querySelector('textarea[name="html"], textarea[name="markup"]');
    editor.querySelectorAll('[data-shop-template-insert]').forEach((button) => {
        button.addEventListener('click', () => {
            const code = button.dataset.shopTemplateInsert;
            source.setRangeText(code, source.selectionStart, source.selectionEnd, 'end');
            source.dispatchEvent(new Event('input', {bubbles:true}));
            source.focus();
        });
    });
});
if (pageTemplateDialog && typeof pageTemplateDialog.showModal === 'function') {
    pageTemplateDialog.removeAttribute('open');
    pageTemplateDialog.showModal();
    pageTemplateDialog.addEventListener('cancel', (event) => {
        event.preventDefault();
        pageTemplateDialog.querySelector('[data-page-template-close]').click();
    });
}

document.querySelectorAll('[data-banner-action]').forEach((select) => {
    const sync = () => select.form.querySelectorAll('[data-banner-action-field]').forEach((field) => {
        field.hidden = field.dataset.bannerActionField !== select.value;
    });
    select.addEventListener('change', sync);
    sync();
});
document.querySelectorAll('[data-banner-color]').forEach((input) => {
    const value = input.nextElementSibling;
    input.addEventListener('input', () => { value.textContent = input.value.toUpperCase(); });
});
const frameEdited = () => {
    if (document.querySelector('[data-component-data-frame]') && window.parent !== window) {
        const target = document.querySelector('[data-component-data-saved]')?.dataset.target;
        window.parent.postMessage({type: 'eurowebcart:component-data-saved', target}, location.origin);
    }
};
if (document.querySelector('[data-component-data-saved]')) frameEdited();

document.querySelectorAll('[data-layout-editor]').forEach((editor) => {
    const source = document.getElementById(editor.dataset.layoutEditor);
    source.addEventListener('change', () => {
        editor.dataset.codeMode = source.value === 'custom' ? 'html' : 'php-mixed';
    });
    editor.addEventListener('input', () => {
        if (source.value === 'inherit' || source.value === 'stock') source.value = 'php';
    });
});

document.querySelectorAll('[data-disclosure-list]').forEach((list) => {
    const items = [...list.querySelectorAll('[data-disclosure-item]')];
    const button = list.querySelector('[data-disclosure-toggle]');
    const text = button.querySelector('[data-disclosure-text]');
    const allOpen = () => items.every((item) => item.open);
    const update = () => {
        const expanded = allOpen();
        button.setAttribute('aria-expanded', String(expanded));
        text.textContent = expanded ? button.dataset.collapse : button.dataset.expand;
    };
    const showLinked = () => {
        const item = items.find((entry) => location.hash === '#' + entry.id);
        if (item) {
            item.open = true;
            requestAnimationFrame(() => item.scrollIntoView({ block: 'start' }));
        }
    };
    button.hidden = items.length === 0;
    button.addEventListener('click', () => {
        const expand = !allOpen();
        items.forEach((item) => { item.open = expand; });
        update();
    });
    items.forEach((item) => item.addEventListener('toggle', update));
    window.addEventListener('hashchange', showLinked);
    showLinked();
    update();
});

const themeMode = document.getElementById('theme-mode');
if (themeMode) {
    const sheet = document.querySelector('[data-admin-theme]');
    themeMode.addEventListener('change', () => {
        const url = new URL(sheet.href, location.href);
        url.searchParams.set('preview_mode', themeMode.value);
        sheet.href = url.href;
    });
}

document.querySelectorAll('[data-editor-media]').forEach((manager) => {
    const editor = manager.closest('[data-content-editor], [data-media-context]');
    const page = manager.closest('[data-page-admin]');
    const select = manager.querySelector('[data-media-folder]');
    const input = manager.querySelector('[data-media-upload]');
    const upload = manager.querySelector('[data-media-upload-button]');
    const status = manager.querySelector('[data-media-feedback]');
    const list = manager.querySelector('[data-media-list]');
    const directories = manager.querySelector('[data-media-subfolders]');
    const parent = manager.querySelector('[data-media-parent]');
    const folderName = manager.querySelector('[data-media-new-folder]');
    const labels = JSON.parse(manager.dataset.mediaKinds || '{}');
    let files = [];
    let folders = [];
    let busy = false;
    let dragged = null;
    let subpath = [];

    const api = async (action, target, options = {}) => {
        const body = new FormData();
        Object.entries({ action, ajax: '1', csrf: page.dataset.csrf, type: target.type, folder: target.path,
            banner: target.set, browse: directories ? '1' : '', ...options })
            .forEach(([key, value]) => body.append(key, value));
        const reply = await fetch(page.dataset.adminUrl, { method: 'POST', body });
        const response = await reply.json();
        if (!reply.ok || !response.ok) throw new Error(response.message || adminText('Не удалось выполнить действие.'));
        if (action !== 'list-media') frameEdited();
        if (directories) {
            manager.querySelector('[data-media-path]').textContent = response.storage || target.path;
            parent.hidden = subpath.length === 0;
            directories.replaceChildren(...response.folders.map((name) => {
                const entry = document.createElement('div');
                entry.className = 'media-folder-entry';
                const button = document.createElement('button');
                button.type = 'button';
                button.dataset.mediaDirectory = name;
                button.className = 'media-folder-button';
                const icon = document.createElement('i');
                icon.className = 'fa-regular fa-folder';
                icon.setAttribute('aria-hidden', 'true');
                button.append(icon, name + '/');
                const remove = document.createElement('button');
                remove.type = 'button';
                remove.className = 'media-icon-button';
                remove.dataset.mediaRemoveFolder = name;
                remove.title = manager.dataset.folderDelete + ' ' + name;
                remove.setAttribute('aria-label', remove.title);
                const trash = document.createElement('i');
                trash.className = 'fa-regular fa-trash-can';
                trash.setAttribute('aria-hidden', 'true');
                remove.append(trash);
                entry.append(button, remove);
                return entry;
            }));
        }
        return response.files;
    };
    const show = (items) => {
        files = items;
        list.replaceChildren();
        files.forEach(({ name, url, edit }, index) => {
            const item = document.createElement('li');
            item.dataset.position = index;
            const row = document.createElement('div');
            row.className = 'media-file-row';
            const handle = document.createElement('button');
            handle.type = 'button';
            handle.className = 'media-drag media-icon-button';
            handle.draggable = true;
            handle.textContent = '⠿';
            handle.setAttribute('aria-label', adminText('Перетащить ') + name);
            const link = document.createElement('a');
            link.href = url;
            link.target = '_blank';
            link.rel = 'noopener';
            link.textContent = name;
            link.className = 'media-file-link';
            const position = document.createElement('input');
            position.type = 'number';
            position.min = '1';
            position.max = String(files.length);
            position.value = String(index + 1);
            position.dataset.mediaPosition = '';
            position.setAttribute('aria-label', adminText('Позиция файла ') + name);
            const remove = document.createElement('button');
            remove.type = 'button';
            remove.dataset.mediaRemove = name;
            remove.className = 'media-icon-button';
            remove.title = adminText('Удалить') + ' ' + name;
            remove.setAttribute('aria-label', remove.title);
            const trash = document.createElement('i');
            trash.className = 'fa-regular fa-trash-can';
            trash.setAttribute('aria-hidden', 'true');
            remove.append(trash);
            row.append(handle, link);
            if (edit) {
                const editSlide = document.createElement('a');
                editSlide.href = edit;
                editSlide.target = '_blank';
                editSlide.rel = 'noopener';
                editSlide.dataset.mediaEditSlide = '';
                editSlide.textContent = manager.dataset.editSlide;
                item.append(editSlide);
            }
            row.append(position, remove);
            item.append(row);
            list.append(item);
        });
    };
    const run = async (operation) => {
        const selected = folders[Number(select.value)];
        if (busy || !selected) return;
        const target = subpath.length ? { ...selected, path: selected.path.replace(/\/$/, '') + '/' + subpath.join('/') + '/' } : selected;
        busy = true;
        select.disabled = true;
        upload.disabled = true;
        manager.setAttribute('aria-busy', 'true');
        try {
            await operation(target);
        } catch (error) {
            status.textContent = error.message || adminText('Не удалось выполнить действие.');
        } finally {
            busy = false;
            select.disabled = false;
            upload.disabled = false;
            manager.removeAttribute('aria-busy');
        }
    };
    const load = () => run(async (target) => {
        status.textContent = adminText('Загружаем список…');
        show(await api('list-media', target));
        status.textContent = files.length ? '' : adminText('Папка пуста.');
    });
    const scan = (source) => {
        if (busy) return;
        const selected = folders[Number(select.value)];
        const found = new Map();
        for (const match of source.matchAll(/\[(gal|fotos|files|banners)(?:[\t ][^\]\r\n]*)?\]([^\[\]]+)\[\/\1\]/gi)) {
            const type = match[1].toLowerCase();
            const path = match[2].trim();
            const opening = match[0].slice(0, match[0].indexOf(']'));
            const set = type === 'banners' ? (opening.match(/[ \t]set=([a-z0-9_-]+)(?=[ \t]|$)/i)?.[1] || '').toLowerCase() : '';
            if (path.startsWith('/')) found.set(type + ':' + set + ':' + path, { type, path, set });
        }
        folders = [...found.values()];
        manager.hidden = folders.length === 0;
        select.replaceChildren(...folders.map((entry, i) => new Option(
            [labels[entry.type] || entry.type, entry.set, entry.path].filter(Boolean).join(' · '), String(i))));
        const index = folders.findIndex((entry) => selected && entry.type === selected.type && entry.path === selected.path && entry.set === selected.set);
        if (folders.length) select.value = String(Math.max(0, index));
        if (manager.open && index === -1) load();
    };
    scan(editor.querySelector('textarea').value);
    editor.querySelector('textarea').addEventListener('input', (event) => scan(event.target.value));
    editor.addEventListener('content-source', (event) => scan(event.detail));
    manager.addEventListener('toggle', () => { if (manager.open) load(); });
    select.addEventListener('change', load);
    if (directories) {
        directories.addEventListener('click', (event) => {
            const folder = event.target.closest('[data-media-directory]');
            const remove = event.target.closest('[data-media-remove-folder]');
            if (busy) return;
            if (remove && window.confirm(manager.dataset.folderConfirm + '\n' + remove.dataset.mediaRemoveFolder)) {
                run(async (target) => {
                    show(await api('delete-media-folder', target, { file: remove.dataset.mediaRemoveFolder, confirm: '1' }));
                    status.textContent = manager.dataset.folderRemoved;
                });
            } else if (folder) {
                subpath.push(folder.dataset.mediaDirectory);
                load();
            }
        });
        parent.addEventListener('click', () => {
            if (busy) return;
            subpath.pop();
            load();
        });
        manager.querySelector('[data-media-mkdir]').addEventListener('click', () => {
            if (!folderName.reportValidity()) return;
            run(async (target) => {
                const name = folderName.value;
                const path = target.path.replace(/\/$/, '') + '/' + name + '/';
                await api('create-media-folder', { ...target, path });
                subpath.push(name);
                folderName.value = '';
                show(await api('list-media', { ...target, path }));
                status.textContent = '';
            });
        });
    }
    upload.addEventListener('click', () => run(async (target) => {
        const selected = [...input.files];
        if (!selected.length) {
            status.textContent = adminText('Выберите файлы.');
            return;
        }
        const failures = [];
        let saved = 0;
        for (const file of selected) {
            status.textContent = adminText('Загрузка ') + (saved + failures.length + 1) + adminText(' из ') + selected.length + '…';
            try {
                show(await api('upload-media', target, { upload: file }));
                saved += 1;
            } catch (error) {
                failures.push(file.name + ' — ' + error.message);
            }
        }
        input.value = '';
        status.textContent = adminText('Загружено: ') + saved + adminText(' из ') + selected.length + '.' + (failures.length ? ' ' + failures.join(' ') : '');
    }));
    list.addEventListener('click', (event) => {
        const button = event.target.closest('[data-media-remove]');
        if (!button || busy || !window.confirm(adminText('Удалить файл «') + button.dataset.mediaRemove + '»?')) return;
        run(async (target) => {
            show(await api('delete-media', target, { file: button.dataset.mediaRemove }));
            status.textContent = adminText('Файл удалён.');
        });
    });
    const move = (from, to) => {
        if (!Number.isInteger(to) || to < 0 || to >= files.length || from === to) return;
        run(async (target) => {
            const order = files.map((file) => file.name);
            order.splice(to, 0, order.splice(from, 1)[0]);
            show(await api('order-media', target, { order: JSON.stringify(order) }));
            status.textContent = adminText('Порядок сохранён.');
        });
    };
    list.addEventListener('change', (event) => {
        if (event.target.matches('[data-media-position]')) {
            move(Number(event.target.closest('li').dataset.position), Number(event.target.value) - 1);
        }
    });
    list.addEventListener('dragstart', (event) => {
        if (!event.target.matches('.media-drag') || busy) return;
        dragged = Number(event.target.closest('li').dataset.position);
        event.dataTransfer.setData('text/plain', String(dragged));
    });
    list.addEventListener('dragend', () => { dragged = null; });
    list.addEventListener('dragover', (event) => {
        if (dragged !== null && event.target.closest('li')) event.preventDefault();
    });
    list.addEventListener('drop', (event) => {
        const target = event.target.closest('li');
        if (dragged === null || !target) return;
        event.preventDefault();
        move(dragged, Number(target.dataset.position));
        dragged = null;
    });
});

const updateBadge = document.querySelector('[data-update-badge]');
if (updateBadge) {
    const cards = document.querySelectorAll('[data-update-item]');
    const check = document.querySelector('[data-update-check]');
    const endpoint = new URL(updateBadge.dataset.updateBadge, location.href);
    const theme = new URLSearchParams(location.search).get('theme');
    if (cards.length && theme) endpoint.searchParams.set('theme', theme);

    const loadUpdates = async (fresh = false) => {
        if (check) check.disabled = true;
        try {
            const response = await fetch(endpoint, {headers: {Accept: 'application/json'}, cache: fresh ? 'reload' : 'default'});
            const data = await response.json();
            if (!response.ok || !data.ok) throw new Error();
            updateBadge.textContent = String(data.count);
            updateBadge.hidden = data.count === 0;
            cards.forEach((card) => {
                const item = data[card.dataset.updateItem];
                card.querySelector('[data-update-version]').textContent = item.version || card.dataset.unavailable;
                const label = card.querySelector('[data-update-remote-label]');
                label.textContent = item.cached ? label.dataset.cached : label.dataset.live;
                const status = card.querySelector('[data-update-status]');
                status.toggleAttribute('data-update-available', item.outdated === true);
                status.textContent = item.outdated
                    ? card.dataset.newer
                    : (item.version && !item.cached ? card.dataset.current : '');
                if (item.installed) card.querySelector('[data-update-installed]').textContent = item.installed;
                const notes = card.querySelector('[data-update-notes]');
                const note = notes.querySelector('[data-update-note-text]');
                if (note) note.textContent = item.notes || note.dataset.empty;
                const changes = notes.querySelector('[data-update-note-list]');
                if (changes) {
                    const entries = (item.notes || changes.dataset.empty).split(/\r?\n/).filter((line) => line.trim());
                    changes.replaceChildren(...entries.map((line) => {
                        const entry = document.createElement('li');
                        entry.textContent = line.replace(/^\s*[-*]\s+/, '');
                        return entry;
                    }));
                }
                const update = card.querySelector('[data-update-when-newer]');
                if (update) update.disabled = !item.outdated;
                notes.hidden = !item.version;
                const requirement = card.querySelector('[data-theme-awaits-engine]');
                if (requirement) requirement.hidden = !item.requiresEngine;
            });
        } catch {
            cards.forEach((card) => {
                card.querySelector('[data-update-version]').textContent = card.dataset.unavailable;
                const status = card.querySelector('[data-update-status]');
                status.removeAttribute('data-update-available');
                status.textContent = '';
                card.querySelector('[data-update-notes]').hidden = true;
                const update = card.querySelector('[data-update-when-newer]');
                if (update) update.disabled = true;
            });
        } finally {
            if (check) check.disabled = false;
        }
    };
    check?.addEventListener('click', () => loadUpdates(true));
    document.addEventListener('admin:updates-changed', () => loadUpdates(true));
    loadUpdates(cards.length > 0);
}

document.querySelectorAll('[data-font-settings]').forEach((form) => {
    const language = form.querySelector('[data-font-preview-language]');
    const phrase = form.querySelector('[data-font-preview-text]');
    const edited = new Map();
    const preview = () => form.querySelectorAll('[data-font-sample]').forEach((sample) => { sample.textContent = phrase.value; });
    phrase.addEventListener('input', () => { edited.set(language.value, phrase.value); preview(); });
    language.addEventListener('change', () => { phrase.value = edited.get(language.value) ?? language.selectedOptions[0].dataset.sample; preview(); });
    form.addEventListener('change', (event) => {
        if (event.target.matches('[data-font-role]') && event.target.value) {
            const selected = event.target.selectedOptions[0];
            const checkbox = form.querySelector('input[type="checkbox"][value="' + CSS.escape(selected.value) + '"]');
            if (checkbox) checkbox.checked = true;
        }
        if (event.target.matches('input[name="enabled[]"]') && !event.target.checked) {
            form.querySelectorAll('[data-font-role]').forEach((role) => { if (role.value === event.target.value) role.value = ''; });
        }
    });
});

document.addEventListener('beforeinput', (event) => {
    if (!event.data || !(event.target instanceof HTMLInputElement)) return;
    const field = event.target;
    if ((field.matches('[data-identifier-input]') && /[^A-Za-z0-9_-]/.test(event.data))
        || (field.matches('[data-address-input]') && /[^A-Za-z0-9_./-]/.test(event.data))) {
        event.preventDefault();
    }
});

(() => {
    const admin = document.querySelector('[data-page-admin]');
    if (!admin) return;
    const tree = admin.querySelector('[data-page-tree]');
    const hideSidebar = admin.querySelector('[data-page-sidebar-toggle="hide"]');
    const showSidebar = admin.querySelector('[data-page-sidebar-toggle="show"]');
    if (hideSidebar && showSidebar) {
        const key = 'ewc-page-sidebar:' + admin.dataset.adminUrl;
        const setCollapsed = (collapsed) => {
            admin.toggleAttribute('data-page-sidebar-collapsed', collapsed);
            hideSidebar.setAttribute('aria-expanded', String(!collapsed));
            showSidebar.setAttribute('aria-expanded', String(!collapsed));
            admin.querySelectorAll('.CodeMirror').forEach(node => node.CodeMirror?.refresh());
        };
        try { setCollapsed(localStorage.getItem(key) === '1'); } catch {}
        hideSidebar.hidden = false;
        showSidebar.hidden = false;
        admin.setAttribute('data-page-sidebar-ready', '');
        const toggle = (collapsed) => {
            setCollapsed(collapsed);
            try { localStorage.setItem(key, collapsed ? '1' : '0'); } catch {}
            (collapsed ? showSidebar : hideSidebar).focus();
        };
        hideSidebar.addEventListener('click', () => toggle(true));
        showSidebar.addEventListener('click', () => toggle(false));
    }
    const status = document.querySelector('[data-page-feedback]');
    const editor = admin.querySelector('[data-page-editor]');
    const tabs = admin.querySelector('[data-page-editor-tabs]');
    let savedFields;
    let unsavedDraft = editor?.hasAttribute('data-page-unsaved-draft');
    const byFieldName = ([left], [right]) => left.localeCompare(right);
    const currentFields = () => {
        const fields = new FormData(editor);
        for (const key of ['csrf', 'revision', 'editor_tab']) fields.delete(key);
        editor.querySelectorAll('[data-content-editor]').forEach((field) => {
            field.dispatchEvent(new CustomEvent('get-editor-comparable-source', {detail: (name, value) => fields.set(name, value)}));
        });
        return [...fields].sort(byFieldName);
    };
    const hasUnsavedChanges = () => unsavedDraft || (savedFields && JSON.stringify(savedFields) !== JSON.stringify(currentFields()));
    const confirmDiscard = () => !hasUnsavedChanges() || window.confirm(editor.dataset.pageLeaveMessage);
    const quickSaveButtons = editor ? [...editor.querySelectorAll('[data-editor-quick-save]')] : [];
    let saveButtonTimer;
    const refreshQuickSave = () => {
        const enabled = Boolean(hasUnsavedChanges());
        quickSaveButtons.forEach(button => { button.disabled = !enabled; });
    };
    const scheduleQuickSave = () => {
        clearTimeout(saveButtonTimer);
        saveButtonTimer = setTimeout(refreshQuickSave, 50);
    };
    if (editor && tabs) {
        const panels = [...admin.querySelectorAll('[data-page-tab-panel]')];
        const links = [...tabs.querySelectorAll('a')];
        const tabField = editor.elements.editor_tab;
        const supportedTabs = links.map(link => link.hash.slice(6));
        const showTab = (tab) => {
            tabField.value = tab;
            panels.forEach(panel => { panel.hidden = panel.dataset.pageTabPanel !== tab; });
            links.forEach(link => {
                if (link.hash === '#page-' + tab) link.setAttribute('aria-current', 'location');
                else link.removeAttribute('aria-current');
            });
            admin.querySelectorAll('.CodeMirror').forEach(node => {
                if (node.CodeMirror && !node.closest('[hidden]')) node.CodeMirror.refresh();
            });
        };
        const tabFromHash = () => {
            if (location.hash === '#section-parent') return 'settings';
            if (location.hash === '#page-custom-definitions') return 'custom-fields';
            if (location.hash === '#page-heading') return 'seo';
            const tab = location.hash.slice(6);
            return location.hash.startsWith('#page-') && supportedTabs.includes(tab) ? tab : '';
        };
        const focusCustomDefinition = () => {
            if (location.hash !== '#page-custom-definitions') return;
            const settings = admin.querySelector('#page-custom-definitions');
            if (!settings) return;
            settings.open = true;
            (settings.querySelector('[data-new-custom-definition]') || settings.querySelector('summary')).focus();
        };
        links.forEach(link => link.addEventListener('click', (event) => {
            if (event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
            event.preventDefault();
            history.replaceState(history.state, '', link.hash);
            showTab(tabFromHash());
        }));
        editor.addEventListener('formdata', ({ formData }) => {
            admin.querySelectorAll('#page-components [data-inline-editor]').forEach(form => {
                const code = form.elements.code;
                if (code.value !== (code.dataset.inlineOriginal ?? code.defaultValue)) formData.set('inline_changes[' + form.elements.index.value + ']', code.value);
            });
        });
        editor.addEventListener('invalid', (event) => {
            const panel = event.target.closest('[data-page-tab-panel]');
            if (panel && panel.hidden) {
                history.replaceState(history.state, '', '#' + panel.id);
                showTab(panel.dataset.pageTabPanel);
            }
        }, true);
        window.addEventListener('hashchange', () => {
            showTab(tabFromHash() || 'edit');
            focusCustomDefinition();
        });
        showTab(tabFromHash() || tabField.value || (editor.elements.new.value === '1' ? 'settings' : 'edit'));
        tabs.hidden = false;
        focusCustomDefinition();
        if (location.hash === '#page-heading') editor.elements.Heading.focus();
        if (location.hash === '#section-parent') document.getElementById('section-parent')?.focus();
        const definitionForm = admin.querySelector('[data-custom-field-definitions]');
        if (definitionForm) {
            const newType = definitionForm.querySelector('[data-new-custom-type]');
            if (newType) {
                newType.addEventListener('change', () => {
                    const previous = definitionForm.querySelector('[data-new-custom-value]');
                    const template = definitionForm.querySelector('[data-custom-value-type="' + newType.value + '"]');
                    const field = template.content.querySelector('[data-new-custom-value]').cloneNode(true);
                    let value = previous.value;
                    if (field.type === 'number') value = value.replace(',', '.').replace(/^\+/, '');
                    field.value = value;
                    if (field instanceof HTMLSelectElement && field.selectedIndex === -1) field.selectedIndex = 0;
                    previous.replaceWith(field);
                });
            }
            const deletes = [...definitionForm.querySelectorAll('[data-custom-field-delete]')];
            deletes.forEach((box) => {
                const update = () => {
                    const row = box.closest('tr');
                    row.querySelectorAll('input:not([data-custom-field-delete]), select').forEach(field => { field.disabled = box.checked; });
                    row.toggleAttribute('data-custom-field-marked', box.checked);
                };
                box.addEventListener('change', update);
                update();
            });
            definitionForm.addEventListener('submit', (event) => {
                if (deletes.some(box => box.checked) && !window.confirm(definitionForm.dataset.confirmRemoval)) event.preventDefault();
            });
        }
        document.addEventListener('DOMContentLoaded', () => {
            savedFields = currentFields();
            refreshQuickSave();
        }, {once: true});
        admin.addEventListener('input', scheduleQuickSave);
        admin.addEventListener('change', scheduleQuickSave);
        editor.querySelectorAll('[data-content-editor]').forEach(field => field.addEventListener('content-source', scheduleQuickSave));
        let skipUnload = false;
        let resetUnload;
        const allowNavigation = () => {
            skipUnload = true;
            clearTimeout(resetUnload);
            resetUnload = setTimeout(() => { skipUnload = false; }, 3000);
        };
        if (admin.hasAttribute('data-focused-page-editor')) {
            document.addEventListener('ewc:page-editor-close', (event) => {
                if (!confirmDiscard()) event.preventDefault();
                else allowNavigation();
            });
        }
        window.addEventListener('pageshow', () => { skipUnload = false; });
        window.addEventListener('beforeunload', (event) => {
            if (skipUnload) return;
            if (!hasUnsavedChanges()) return;
            event.preventDefault();
            event.returnValue = true;
        });
        window.addEventListener('submit', (event) => {
            if (event.target === editor && !event.defaultPrevented) allowNavigation();
        });
        window.addEventListener('click', (event) => {
            if (event.defaultPrevented || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
            const link = event.target.closest('a[href]');
            if (!link || (link.target && link.target !== '_self') || link.hasAttribute('download')) return;
            const destination = new URL(link.href, location.href);
            if (!['http:', 'https:'].includes(destination.protocol)) return;
            if (destination.hash && destination.origin === location.origin && destination.pathname === location.pathname && destination.search === location.search) return;
            if (!confirmDiscard()) event.preventDefault();
            else allowNavigation();
        });
    }
    const mapNotice = editor?.querySelector('[data-page-map-notice]');
    if (mapNotice) {
        const updateNotice = () => { mapNotice.hidden = !editor.elements.Lat.value.trim() && !editor.elements.Lng.value.trim(); };
        editor.elements.Lat.addEventListener('input', updateNotice);
        editor.elements.Lng.addEventListener('input', updateNotice);
        updateNotice();
    }
    const mapPicker = editor?.querySelector('[data-map-picker]');
    if (mapPicker) {
        const options = JSON.parse(mapPicker.dataset.mapPicker);
        const canvas = mapPicker.querySelector('[data-map-picker-canvas]');
        const result = mapPicker.querySelector('[data-map-picker-value]');
        const apply = mapPicker.querySelector('[data-use-map-picker]');
        const error = mapPicker.querySelector('[data-map-picker-error]');
        let loading;
        let mapControl;
        let position;

        const readPoint = () => {
            const values = ['Lat','Lng'].map(key => editor.elements[key].value.trim().replace(',', '.'));
            if (values.some(value => !/^[+-]?\d+(?:\.\d+)?$/.test(value))) return null;
            const point = values.map(Number);
            return Math.abs(point[0]) <= 90 && Math.abs(point[1]) <= 180 ? point : null;
        };
        const selectPoint = (point) => {
            position = point;
            result.value = point ? point.map(value => value.toFixed(6)).join(', ') : '';
            apply.disabled = !point;
        };
        const loadProvider = () => {
            const yandex = options.provider === 'yandex';
            if (yandex && window.ymaps?.ready) return Promise.resolve();
            if (!yandex && window.google?.maps?.importLibrary) return Promise.resolve();
            return new Promise((resolve, reject) => {
                const script = document.createElement('script');
                const timeout = setTimeout(() => { script.remove(); reject(new Error('Map timeout')); }, 12000);
                const params = yandex ? {apikey:options.key,lang:options.language === 'ru' ? 'ru_RU' : 'en_US',csp:'true'}
                    : {key:options.key,v:'weekly',language:options.language};
                script.src = (yandex ? 'https://api-maps.yandex.ru/2.1.79/?' : 'https://maps.googleapis.com/maps/api/js?') + new URLSearchParams(params);
                script.async = true;
                script.onload = () => { clearTimeout(timeout); resolve(); };
                script.onerror = () => { clearTimeout(timeout); script.remove(); reject(new Error('Map provider unavailable')); };
                document.head.append(script);
            });
        };
        const setup = async (point) => {
            await loadProvider();
            const center = point || [55.7558, 37.6173];
            if (options.provider === 'yandex') {
                await new Promise((resolve, reject) => window.ymaps.ready(resolve, reject));
                const api = window.ymaps;
                const map = new api.Map(canvas, {center, zoom:options.zoom,
                    type:({map:'yandex#map',satellite:'yandex#satellite',hybrid:'yandex#hybrid'})[options.mode],
                    controls:['zoomControl','searchControl']});
                const marker = new api.Placemark(center, {}, {draggable:true});
                const set = (chosen) => {
                    if (chosen) {
                        marker.geometry.setCoordinates(chosen);
                        if (map.geoObjects.indexOf(marker) === -1) map.geoObjects.add(marker);
                    } else map.geoObjects.remove(marker);
                    selectPoint(chosen);
                };
                map.events.add('click', event => set(event.get('coords')));
                marker.events.add('dragend', () => selectPoint(marker.geometry.getCoordinates()));
                mapControl = {set, resize:() => map.container.fitToViewport()};
            } else {
                const [maps, markers] = await Promise.all([google.maps.importLibrary('maps'), google.maps.importLibrary('marker')]);
                const locationOf = ([lat,lng]) => ({lat,lng});
                const map = new maps.Map(canvas, {center:locationOf(center), zoom:options.zoom, mapId:options.mapId || 'DEMO_MAP_ID',
                    mapTypeId:options.mode === 'map' ? 'roadmap' : options.mode});
                const marker = new markers.AdvancedMarkerElement({position:locationOf(center),gmpDraggable:true});
                const set = (chosen) => {
                    if (chosen) marker.position = locationOf(chosen);
                    marker.map = chosen ? map : null;
                    selectPoint(chosen);
                };
                map.addListener('click', event => {
                    if (event.latLng) set([event.latLng.lat(), event.latLng.lng()]);
                });
                marker.addListener('dragend', event => {
                    if (event.latLng) selectPoint([event.latLng.lat(), event.latLng.lng()]);
                });
                mapControl = {set, resize:() => {}};
            }
            mapControl.set(point);
        };
        editor.querySelector('[data-open-map-picker]').addEventListener('click', async () => {
            mapPicker.showModal();
            error.hidden = true;
            const point = readPoint();
            selectPoint(point);
            try {
                if (!loading) loading = setup(point);
                await loading;
                mapControl.set(point);
                mapControl.resize();
            } catch {
                loading = null;
                error.textContent = mapPicker.dataset.loadError;
                error.hidden = false;
            }
        });
        mapPicker.querySelector('[data-close-map-picker]').addEventListener('click', () => mapPicker.close());
        apply.addEventListener('click', () => {
            if (!position) return;
            ['Lat','Lng'].forEach((key, index) => {
                const input = editor.elements[key];
                input.value = position[index].toFixed(6);
                input.dispatchEvent(new Event('input', {bubbles:true}));
            });
            mapPicker.close();
        });
    }
    const move = admin.querySelector('[data-page-move-form]');
    const folderMove = admin.querySelector('[data-folder-move-form]');
    const folders = () => [...tree.querySelectorAll('[data-page-folder]')].map((node) => node.dataset.pageFolder).sort();
    let dragged = null;
    let saving = false;

    const feedback = (message, error = false) => {
        status.textContent = message;
        status.classList.toggle('error', error);
        status.hidden = false;
    };
    const request = async (fields) => {
        if (saving) return false;
        saving = true;
        admin.setAttribute('aria-busy', 'true');
        const oldPath = editor ? editor.dataset.pagePath : '';
        const body = new URLSearchParams({
            ajax: '1', csrf: admin.dataset.csrf,
            current_path: oldPath, current_revision: oldPath ? editor.elements.revision.value : '',
            ...fields,
        });
        try {
            const response = await fetch(admin.dataset.adminUrl, {
                method: 'POST', body, headers: { Accept: 'application/json' },
            });
            const result = await response.json();
            if (!response.ok || !result.ok) {
                feedback(result.message || adminText('Действие не выполнено.'), true);
                return false;
            }
            const open = new Map([...tree.querySelectorAll('details[data-page-folder]')].map((folder) => [
                folder.dataset.pageFolder, folder.open,
            ]));
            tree.innerHTML = result.tree;
            tree.querySelectorAll('details[data-page-folder]').forEach((folder) => {
                const path = folder.dataset.pageFolder;
                if (result.openFolder === path || (result.openFolder || '').startsWith(path + '/')) {
                    folder.open = true;
                    return;
                }
                if (result.page && result.page.path !== oldPath && result.page.path.startsWith(path + '/')) return;
                if (open.has(path)) folder.open = open.get(path);
            });
            if (oldPath && result.page === null) {
                editor.closest('[data-page-editor-wrapper]').hidden = true;
                editor.dataset.pagePath = '';
                savedFields = undefined;
                unsavedDraft = false;
                window.history.replaceState(null, '', admin.dataset.adminUrl);
            } else if (oldPath && result.page) {
                const page = result.page;
                editor.dataset.pagePath = page.path;
                editor.elements.path.value = page.address;
                editor.elements.revision.value = page.revision;
                editor.elements.MenuVisible.checked = page.menu;
                if (editor.elements.Order.value === editor.elements.Order.defaultValue) {
                    editor.elements.Order.value = page.order;
                }
                editor.elements.Order.defaultValue = page.order;
                if (savedFields) {
                    const persisted = {path: page.address, Order: String(page.order)};
                    savedFields = savedFields
                        .filter(([name]) => name !== 'MenuVisible')
                        .map(([name, value]) => [name, Object.hasOwn(persisted, name) ? persisted[name] : value]);
                    if (page.menu) savedFields.push(['MenuVisible', '1']);
                    savedFields.sort(byFieldName);
                }
                const link = editor.querySelector('[data-page-open]');
                link.href = page.publicUrl;
                link.hidden = !page.available;
                const visibility = editor.querySelector('[data-current-page-action="visibility"]');
                if (visibility) {
                    visibility.dataset.pageAction = page.hidden ? 'show' : 'hide';
                    visibility.href = page.editUrl + '&confirm=' + visibility.dataset.pageAction;
                    visibility.title = page.hidden ? visibility.dataset.showText : visibility.dataset.hideText;
                    visibility.querySelector('span').textContent = visibility.title;
                    visibility.querySelector('i').classList.toggle('fa-eye', page.hidden);
                    visibility.querySelector('i').classList.toggle('fa-eye-slash', !page.hidden);
                }
                const remove = editor.querySelector('[data-current-page-action="delete"]');
                if (remove) remove.href = page.editUrl + '&confirm=delete';
                if (move) {
                    move.setAttribute('action', page.editUrl);
                    move.elements.path.value = page.path;
                    move.elements.revision.value = page.revision;
                }
                if (oldPath !== page.path) window.history.replaceState(null, '', page.editUrl);
            }
            if (move) {
                const list = move.elements.existing_folder;
                const currentFolder = (editor?.dataset.pagePath || '').split('/').slice(0, -1).join('/');
                list.replaceChildren(new Option(adminText('Выберите папку'), ''), ...folders().filter(folder => folder !== currentFolder)
                    .map(folder => new Option('/' + folder + '/', '/' + folder + '/')));
            }
            feedback(result.message);
            scheduleQuickSave();
            if (fields.action === 'folder-move' && result.openFolder && folderMove) {
                const url = new URL(admin.dataset.adminUrl, location.href);
                if (folderMove.dataset.hasPage === '1') url.searchParams.set('page', '/' + result.openFolder + '/');
                else url.search = new URLSearchParams({new: '1', parent: result.openFolder, section: '1'}).toString();
                location.assign(url);
            }
            return true;
        } catch (error) {
            feedback(adminText('Не удалось получить ответ. Обновите страницу и проверьте результат.'), true);
            return false;
        } finally {
            saving = false;
            admin.removeAttribute('aria-busy');
        }
    };
    const rowOf = (element) => element.closest('li[data-page-node]');
    const targetOf = (row) => ({ path: row.dataset.pagePath, revision: row.dataset.pageRevision });
    const ask = {
        hide: adminText('Скрыть страницу?'),
        show: adminText('Опубликовать страницу?'),
        delete: adminText('Удалить страницу? Это действие нельзя отменить.'),
    };

    admin.addEventListener('click', (event) => {
        if (event.target.closest('[data-page-drag]')) event.preventDefault();
        const link = event.target.closest('[data-page-action]');
        if (!link || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
        event.preventDefault();
        const operation = link.dataset.pageAction;
        if (!saving) {
            const target = editor && editor.contains(link)
                ? {path: editor.dataset.pagePath, revision: editor.elements.revision.value} : targetOf(rowOf(link));
            if (operation === 'delete' && target.path === editor?.dataset.pagePath && !confirmDiscard()) return;
            if (!window.confirm(ask[operation])) return;
            request({ action: 'change-page-status', operation, ...target });
        }
    });
    admin.addEventListener('change', async (event) => {
        const box = event.target;
        if (!box.matches('[data-page-menu]')) return;
        const checked = box.checked;
        const target = editor && editor.contains(box)
            ? { path: editor.dataset.pagePath, revision: editor.elements.revision.value }
            : targetOf(rowOf(box));
        box.disabled = true;
        const saved = await request({ action: 'page-menu', ...target, visible: checked ? '1' : '0' });
        if (!saved) box.checked = !checked;
        box.disabled = false;
    });
    admin.addEventListener('submit', async (event) => {
        if (saving) {
            event.preventDefault();
            return;
        }
        if (event.target === folderMove) {
            event.preventDefault();
            if (await request(Object.fromEntries(new FormData(folderMove)))) folderMove.hidden = true;
            return;
        }
        if (event.target !== move) return;
        event.preventDefault();
        const values = Object.fromEntries(new FormData(move));
        values.folder = values.folder || values.existing_folder;
        if (await request(values)) {
            move.elements.folder.value = '';
            move.elements.existing_folder.value = '';
            move.closest('[data-page-move]').open = false;
        }
    });

    const siblings = (row) => [...row.parentElement.children].filter((item) =>
        item.dataset.pagePath && item.dataset.pagePath !== 'index.php'
    );
    const reorder = async (row, to) => {
        const items = siblings(row);
        const from = items.indexOf(row);
        if (from === to || to < 0 || to >= items.length) return;
        const folder = row.parentElement.dataset.pageGroup;
        const path = row.dataset.pagePath;
        items.splice(from, 1);
        items.splice(to, 0, row);
        if (await request({ action: 'page-order', folder, items: JSON.stringify(items.map(targetOf)) })) {
            const moved = [...tree.querySelectorAll('[data-page-node]')].find((item) => item.dataset.pagePath === path);
            if (moved) {
                const handle = [...moved.querySelectorAll('[data-page-drag]')].find((item) => rowOf(item) === moved);
                if (handle) handle.focus();
            }
        }
    };
    tree.addEventListener('keydown', (event) => {
        const handle = event.target.closest('[data-page-drag]');
        if (!handle || saving || !['ArrowUp', 'ArrowDown'].includes(event.key)) return;
        event.preventDefault();
        const row = rowOf(handle);
        reorder(row, siblings(row).indexOf(row) + (event.key === 'ArrowUp' ? -1 : 1));
    });
    tree.addEventListener('dragstart', (event) => {
        const handle = event.target.closest('[data-page-drag]');
        if (!handle || saving) {
            event.preventDefault();
            return;
        }
        dragged = rowOf(handle);
        event.dataTransfer.effectAllowed = 'move';
        event.dataTransfer.setData('text/plain', dragged.dataset.pagePath);
        dragged.classList.add('is-moving');
    });
    tree.addEventListener('dragover', (event) => {
        const row = rowOf(event.target);
        if (!dragged || !siblings(dragged).includes(row)) return;
        event.preventDefault();
        event.dataTransfer.dropEffect = 'move';
    });
    tree.addEventListener('drop', (event) => {
        if (!dragged) return;
        const to = siblings(dragged).indexOf(rowOf(event.target));
        if (to === -1) return;
        event.preventDefault();
        reorder(dragged, to);
    });
    tree.addEventListener('dragend', () => {
        if (dragged) dragged.classList.remove('is-moving');
        dragged = null;
    });
})();

document.querySelectorAll('[data-theme-presets]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        if (event.submitter && event.submitter.value === 'delete-theme-preset' && !window.confirm(adminText('Удалить сохранённую тему?'))) {
            event.preventDefault();
        }
    });
});

document.querySelectorAll('[data-component-code]').forEach((component) => {
    const example = component.querySelector('[data-component-example]');
    const fields = [...component.querySelectorAll('[data-component-param]')];
    const updateExample = () => {
        const name = component.dataset.componentCode;
        const params = fields.map((field) => field.dataset.componentParam + '=' + field.value).join(' ');
        example.textContent = '[' + name + ' ' + params + ']' + example.dataset.componentExample + '[/' + name + ']';
    };
    component.addEventListener('change', updateExample);
});

(() => {
    const form = document.querySelector('[data-form-config]');
    if (!form) return;

    const rows = form.querySelector('[data-form-rows]');
    const template = form.querySelector('[data-empty-field]');
    let nextIndex = rows.children.length;
    const updateOptions = (row) => {
        const select = row.querySelector('[data-field-type]');
        const option = select.selectedOptions[0];
        const type = select.value;
        const length = row.querySelector('[data-field-length]');
        length.max = option.dataset.maxLength;
        length.placeholder = option.dataset.defaultLength;
        row.querySelector('[data-field-options]').hidden = type !== 'radio' && type !== 'select';
    };
    [...rows.children].forEach(updateOptions);

    form.querySelector('[data-add-field]').addEventListener('click', () => {
        const row = template.content.firstElementChild.cloneNode(true);
        row.querySelectorAll('[name]').forEach((field) => {
            field.name = field.name.replace('__INDEX__', String(nextIndex));
        });
        nextIndex += 1;
        rows.append(row);
        updateOptions(row);
        row.querySelector('input').focus();
    });
    rows.addEventListener('change', (event) => {
        if (event.target.matches('[data-field-type]')) updateOptions(event.target.closest('[data-form-row]'));
    });
    rows.addEventListener('click', (event) => {
        if (event.target.matches('[data-remove-field]')) {
            event.target.closest('[data-form-row]').remove();
        }
    });
})();

const mailEditor = document.querySelector('[data-mail-editor]');
if (mailEditor) {
    let mailTarget = mailEditor.elements.mail_body;
    mailEditor.addEventListener('focusin', (event) => {
        if (event.target.matches('[data-mail-template]')) mailTarget = event.target;
    });
    mailEditor.addEventListener('click', (event) => {
        const button = event.target.closest('[data-mail-variable]');
        if (!button) return;
        mailTarget.setRangeText(button.dataset.mailVariable, mailTarget.selectionStart, mailTarget.selectionEnd, 'end');
        mailTarget.focus();
    });
}

(() => {
    const form = document.querySelector('[data-contacts-config]');
    if (!form) return;

    const rows = form.querySelector('[data-phone-rows]');
    const template = form.querySelector('[data-phone-template]');
    let nextIndex = rows.children.length;
    form.querySelector('[data-add-phone]').addEventListener('click', () => {
        const row = template.content.firstElementChild.cloneNode(true);
        row.querySelectorAll('[name]').forEach((input) => {
            input.name = input.name.replace('__INDEX__', String(nextIndex));
        });
        nextIndex += 1;
        rows.append(row);
        row.querySelector('input').focus();
    });
    rows.addEventListener('click', (event) => {
        if (event.target.matches('[data-remove-phone]')) event.target.closest('[data-phone-row]').remove();
    });
})();

(() => {
    const copy = document.querySelector('[data-copy-iblock]');
    if (!copy) return;

    const csv = document.getElementById('iblock-csv');
    const status = document.querySelector('[data-copy-status]');
    copy.addEventListener('click', async () => {
        try {
            await navigator.clipboard.writeText(csv.value);
            status.textContent = adminText('CSV скопирован.');
        } catch (error) {
            csv.focus();
            csv.select();
            status.textContent = adminText('Текст выделен. Нажмите Ctrl+C.');
        }
    });
})();

(() => {
    const form = document.querySelector('[data-iblock-editor]');
    if (!form) return;
    const status = form.querySelector('[data-iblock-status]');
    const submit = form.querySelector('[type="submit"]');
    let saving = false;
    let queued = false;
    const feedback = (message, error = false) => {
        status.textContent = message;
        status.classList.toggle('error', error);
        status.hidden = false;
    };
    const save = async () => {
        if (saving) {
            queued = true;
            return;
        }
        if (!form.reportValidity()) return;

        saving = true;
        submit.disabled = true;
        feedback(adminText('Сохраняем инфоблок…'));
        const values = new URLSearchParams({ ajax: '1' });
        for (const [name, value] of new FormData(form)) {
            values.append(name, value);
        }
        try {
            const response = await fetch(form.action, {
                method: 'POST', body: values, headers: { Accept: 'application/json' },
            });
            if (!response.headers.get('Content-Type')?.includes('application/json')) {
                feedback(response.redirected
                    ? adminText('Сессия завершена. Войдите снова в отдельной вкладке и повторите сохранение.')
                    : adminText('Сервер вернул неожиданный ответ (HTTP ') + response.status + adminText('). Правки остались в редакторе.'), true);
                queued = false;
                return;
            }
            const result = await response.json();
            if (!result.ok) {
                feedback(result.message, true);
                queued = false;
                return;
            }

            form.elements.revision.value = result.revision;
            if (form.dataset.iblockNew === '1') {
                form.dataset.iblockNew = '0';
                form.setAttribute('action', result.url);
                form.elements.id.value = result.id;
                form.elements.id.readOnly = true;
                form.querySelector('[data-iblock-heading-title]').textContent = adminText('Редактирование инфоблока');
                form.querySelector('[data-iblock-mode-link]').href = result.modeUrl;
                const shortcode = form.querySelector('[data-iblock-shortcode]');
                shortcode.querySelector('code').textContent = result.shortcode;
                shortcode.hidden = false;
                const list = document.querySelector('[data-iblock-list]');
                list.querySelectorAll('[aria-current]').forEach((link) => link.removeAttribute('aria-current'));
                const item = document.createElement('li');
                const link = document.createElement('a');
                link.href = result.url;
                link.textContent = result.id;
                link.setAttribute('aria-current', 'page');
                item.append(link);
                list.append(item);
                const empty = document.querySelector('[data-iblock-empty]');
                if (empty) empty.hidden = true;
                history.replaceState(null, '', result.url);
            }
            feedback(result.message);
            frameEdited();
        } catch (error) {
            feedback(adminText('Ответ о сохранении не получен. Правки остались в редакторе.'), true);
            queued = false;
        } finally {
            saving = false;
            submit.disabled = false;
            if (queued) {
                queued = false;
                save();
            }
        }
    };
    form.addEventListener('submit', (event) => {
        event.preventDefault();
        save();
    });
    if (!form.matches('[data-iblock-config]')) return;

    const columns = form.querySelector('[data-iblock-columns]');
    const heading = form.querySelector('[data-iblock-heading]');
    const rows = form.querySelector('[data-iblock-rows]');
    const template = form.querySelector('[data-iblock-column-template]');
    const formOptions = form.querySelector('[data-iblock-form-options]');
    const table = heading.closest('table');
    let nextColumn = columns.children.length;
    let nextRow = rows.children.length;

    const updateTableWidth = () => {
        if (!table.classList.contains('is-column-sized')) return;
        const width = [...heading.cells].reduce((sum, cell) => sum + (parseFloat(cell.style.width) || 160), 0);
        table.style.width = width + 'px';
    };
    const startColumnSizing = () => {
        if (table.classList.contains('is-column-sized')) return;
        const widths = [...heading.cells].map(cell => cell.getBoundingClientRect().width);
        [...heading.cells].forEach((cell, index) => { cell.style.width = widths[index] + 'px'; });
        table.classList.add('is-column-sized');
        updateTableWidth();
    };
    const sizeColumn = (cell, width) => {
        cell.style.width = Math.max(92, Math.min(1200, width)) + 'px';
        updateTableWidth();
    };
    const setColumnLabel = (cell, name) => {
        let label = cell.querySelector('[data-iblock-column-title]');
        let handle = cell.querySelector('.iblock-column-resize');
        if (!handle) {
            label = document.createElement('span');
            label.dataset.iblockColumnTitle = '';
            handle = document.createElement('button');
            handle.type = 'button';
            handle.className = 'iblock-column-resize';
            cell.replaceChildren(label, handle);
            let drag;
            handle.addEventListener('pointerdown', (event) => {
                if (event.button !== 0 || drag) return;
                startColumnSizing();
                drag = { id: event.pointerId, x: event.clientX, width: parseFloat(cell.style.width) };
                handle.setPointerCapture(event.pointerId);
                handle.dataset.resizing = '';
            });
            handle.addEventListener('pointermove', (event) => {
                if (drag?.id === event.pointerId) sizeColumn(cell, drag.width + event.clientX - drag.x);
            });
            handle.addEventListener('lostpointercapture', () => {
                drag = null;
                delete handle.dataset.resizing;
            });
            handle.addEventListener('keydown', (event) => {
                if (!['ArrowLeft', 'ArrowRight'].includes(event.key)) return;
                event.preventDefault();
                startColumnSizing();
                sizeColumn(cell, parseFloat(cell.style.width) + (event.key === 'ArrowLeft' ? -16 : 16));
            });
        }
        label.textContent = name;
        handle.title = heading.dataset.resizeLabel + ' · ' + name;
        handle.setAttribute('aria-label', handle.title);
    };

    const cellInput = (cell, column, value = '') => {
        const type = column.querySelector('[data-column-type]').value;
        const row = cell.closest('tr');
        const input = document.createElement({ bool: 'select', form: 'select', textarea: 'textarea' }[type] || 'input');
        input.dataset.cellType = type;
        input.name = 'rows[' + row.dataset.rowIndex + '][' + column.dataset.columnIndex + ']';
        if (type === 'textarea') {
            input.rows = 3;
        } else if (type === 'form') {
            input.append(formOptions.content.cloneNode(true));
            if (/^form[1-9][0-9]{0,8}$/.test(value) && ![...input.options].some((option) => option.value === value)) {
                input.add(new Option(value, value));
            }
        } else if (type === 'bool') {
            [['', '—'], ['1', adminText('Да')], ['0', adminText('Нет')]].forEach(([id, label]) => {
                const option = document.createElement('option');
                option.value = id;
                option.textContent = label;
                input.append(option);
            });
        } else {
            input.type = ['price', 'hidden'].includes(type) ? 'text' : type;
            if (type === 'price') {
                input.inputMode = 'decimal';
                input.pattern = '[0-9]{1,15}([.,][0-9]{1,2})?';
            }
            if (type === 'number') {
                input.step = 'any';
                value = value.replace(',', '.');
            }
        }
        input.value = value;
        if (value && input.value === '') {
            input.dataset.previousValue = value;
            input.setCustomValidity(adminText('Замените прежнее значение значением нового типа.'));
            input.addEventListener('input', () => {
                delete input.dataset.previousValue;
                input.setCustomValidity('');
            }, { once: true });
        }
        cell.replaceChildren(input);
        return input;
    };

    const sync = (column) => {
        const index = column.dataset.columnIndex;
        const label = column.querySelector('[data-column-label]').value || adminText('Столбец');
        const type = column.querySelector('[data-column-type]').value;
        setColumnLabel(heading.querySelector('[data-cell-column="' + index + '"]'), label);
        rows.querySelectorAll('[data-cell-column="' + index + '"]').forEach((cell) => {
            let input = cell.firstElementChild;
            if (input.dataset.cellType !== type) {
                input = cellInput(cell, column, input.dataset.previousValue || input.value);
            }
            input.setAttribute('aria-label', label);
        });
    };
    const addCell = (row, column) => {
        const index = column.dataset.columnIndex;
        const cell = document.createElement('td');
        cell.dataset.cellColumn = index;
        row.insertBefore(cell, row.querySelector('[data-iblock-actions]'));
        cellInput(cell, column);
    };

    [...columns.children].forEach(sync);
    form.querySelector('[data-add-column]').addEventListener('click', () => {
        const column = template.content.firstElementChild.cloneNode(true);
        column.dataset.columnIndex = String(nextColumn);
        column.querySelectorAll('[name]').forEach((input) => {
            input.name = input.name.replace('__INDEX__', String(nextColumn));
        });
        const cell = document.createElement('th');
        cell.scope = 'col';
        cell.dataset.cellColumn = String(nextColumn++);
        if (table.classList.contains('is-column-sized')) cell.style.width = '160px';
        heading.insertBefore(cell, heading.querySelector('[data-iblock-actions]'));
        updateTableWidth();
        columns.append(column);
        [...rows.children].forEach((row) => addCell(row, column));
        sync(column);
        column.querySelector('input').focus();
    });
    form.querySelector('[data-add-row]').addEventListener('click', () => {
        if (!columns.children.length) return;
        const row = document.createElement('tr');
        row.dataset.rowIndex = String(nextRow++);
        const order = document.createElement('td');
        order.className = 'iblock-order';
        const handle = document.createElement('button');
        handle.className = 'iblock-drag';
        handle.type = 'button';
        handle.draggable = true;
        handle.dataset.dragRow = '';
        handle.title = adminText('Перетащить строку');
        handle.setAttribute('aria-label', adminText('Переместить строку. Используйте стрелки вверх и вниз'));
        handle.textContent = '⠿';
        order.append(handle);
        row.append(order);
        const actions = document.createElement('td');
        actions.dataset.iblockActions = '';
        const remove = document.createElement('button');
        remove.className = 'iblock-remove-row';
        remove.type = 'button';
        remove.dataset.removeRow = '';
        remove.title = adminText('Удалить строку');
        remove.setAttribute('aria-label', remove.title);
        const icon = document.createElement('i');
        icon.className = 'fa-solid fa-trash';
        icon.setAttribute('aria-hidden', 'true');
        remove.append(icon);
        actions.append(remove);
        row.append(actions);
        rows.append(row);
        [...columns.children].forEach((column) => {
            addCell(row, column);
            sync(column);
        });
        row.querySelector('[data-cell-type]').focus();
    });
    columns.addEventListener('input', (event) => {
        if (event.target.matches('[data-column-label], [data-column-type]')) {
            sync(event.target.closest('[data-column-index]'));
        }
    });
    columns.addEventListener('click', (event) => {
        if (!event.target.matches('[data-remove-column]')) return;
        const column = event.target.closest('[data-column-index]');
        const index = column.dataset.columnIndex;
        form.querySelectorAll('[data-cell-column="' + index + '"]').forEach((cell) => cell.remove());
        column.remove();
        updateTableWidth();
    });
    rows.addEventListener('click', (event) => {
        const remove = event.target.closest('[data-remove-row]');
        if (remove) remove.closest('tr').remove();
    });

    let dragging;
    let startOrder;
    const order = () => [...rows.children].map((row) => row.dataset.rowIndex).join(',');
    const saveOrder = () => {
        if (form.dataset.iblockNew === '0') save();
    };
    rows.addEventListener('dragstart', (event) => {
        const handle = event.target.closest('[data-drag-row]');
        if (!handle) return;
        dragging = handle.closest('tr');
        startOrder = order();
        dragging.classList.add('is-dragging');
        event.dataTransfer.effectAllowed = 'move';
        event.dataTransfer.setData('text/plain', dragging.dataset.rowIndex);
    });
    rows.addEventListener('dragover', (event) => {
        const over = event.target.closest('tr');
        if (!dragging || !over || over === dragging) return;
        event.preventDefault();
        event.dataTransfer.dropEffect = 'move';
        const box = over.getBoundingClientRect();
        if (event.clientY < box.top + box.height / 2) rows.insertBefore(dragging, over);
        else rows.insertBefore(dragging, over.nextElementSibling);
    });
    rows.addEventListener('drop', (event) => {
        if (dragging) event.preventDefault();
    });
    rows.addEventListener('dragend', () => {
        if (!dragging) return;
        dragging.classList.remove('is-dragging');
        dragging = null;
        if (startOrder !== order()) saveOrder();
    });
    rows.addEventListener('keydown', (event) => {
        const handle = event.target.closest('[data-drag-row]');
        if (!handle || !['ArrowDown', 'ArrowUp'].includes(event.key)) return;
        event.preventDefault();
        const row = handle.closest('tr');
        const down = event.key === 'ArrowDown';
        const neighbor = down ? row.nextElementSibling : row.previousElementSibling;
        if (!neighbor) return;
        rows.insertBefore(row, down ? neighbor.nextElementSibling : neighbor);
        handle.focus();
        saveOrder();
    });
})();

document.querySelectorAll('[data-library-dialog]').forEach((dialog) => {
    const frame = dialog.querySelector('iframe');
    document.querySelectorAll('[data-library-test]').forEach((button) => {
        button.addEventListener('click', () => {
            const name = button.dataset.libraryTest;
            const url = new URL(dialog.dataset.testUrl, window.location.href);
            url.searchParams.set('id', name);
            url.searchParams.set('lang', dialog.dataset.interface);
            dialog.querySelectorAll('[data-library-code]').forEach((code) => { code.hidden = code.dataset.libraryCode !== name; });
            frame.src = url.href;
            if (!dialog.open) dialog.showModal();
        });
    });
    dialog.querySelector('[data-library-close]').addEventListener('click', () => dialog.close());
    dialog.addEventListener('close', () => frame.removeAttribute('src'));
});

const shopReleaseCheck = document.querySelector('[data-shop-release-check]');
if (shopReleaseCheck) {
    shopReleaseCheck.addEventListener('submit', () => {
        shopReleaseCheck.querySelector('progress').hidden = false;
        shopReleaseCheck.querySelector('button').disabled = true;
        shopReleaseCheck.setAttribute('aria-busy', 'true');
    });
}

document.querySelectorAll('[data-update-stream]').forEach((form) => {
    if (typeof ReadableStream === 'undefined' || typeof TextDecoder === 'undefined') return;
    const panel = form.querySelector('[data-update-progress]');
    const submit = form.querySelector('button[type="submit"], button:not([type]), input[type="submit"]');
    if (!panel || !submit) return;
    const bar = panel.querySelector('progress');
    const current = panel.querySelector('[data-update-current]');
    const stateLabel = panel.querySelector('[data-update-state-label]');
    const percent = panel.querySelector('[data-update-percent]');
    const installed = form.closest('.panel')?.querySelector('[data-update-installed]');
    const log = panel.querySelector('[data-update-log]');
    const reload = panel.querySelector('[data-update-reload]');
    const settings = form.querySelector('fieldset');
    const confirm = form.querySelector('[name="confirm_replace"]');
    const integer = new Intl.NumberFormat(document.documentElement.lang || undefined);
    const bytes = new Intl.NumberFormat(document.documentElement.lang || undefined, {style: 'unit', unit: 'byte'});

    const percentFormat = new Intl.NumberFormat(document.documentElement.lang || undefined, {style: 'percent'});
    const setProgress = (value) => {
        bar.value = value;
        percent.textContent = percentFormat.format(bar.value / 100);
    };
    const setState = (state, message) => {
        panel.dataset.state = state;
        stateLabel.textContent = panel.dataset[state];
        current.textContent = message;
    };

    reload.addEventListener('click', () => location.reload());
    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        if (submit.disabled) return;
        const data = new FormData(form);
        data.set('update_progress', '1');
        submit.disabled = true;
        if (settings) settings.disabled = true;
        if (confirm) confirm.disabled = true;
        submit.setAttribute('aria-busy', 'true');
        panel.hidden = false;
        if (form.hasAttribute('data-theme-demo')) panel.scrollIntoView({block: 'nearest'});
        reload.hidden = true;
        setProgress(0);
        setState('running', panel.dataset.start);
        log.replaceChildren();
        let lastStep = '';
        let finished = false;
        let succeeded = false;
        try {
            const response = await fetch(form.getAttribute('action'), {
                method: 'POST', body: data, headers: {Accept: 'application/x-ndjson'},
            });
            if (!response.headers.get('Content-Type')?.includes('application/x-ndjson')) {
                const problem = response.headers.get('Content-Type')?.includes('application/json') ? await response.json() : {};
                if (!problem.message) throw new Error();
                setState('failed', problem.message);
                return;
            }
            const reader = response.body.getReader();
            const decoder = new TextDecoder();
            let pending = '';
            const show = (line) => {
                if (finished || !line.trim()) return;
                const entry = JSON.parse(line);
                if (Number.isInteger(entry.percent)) setProgress(entry.percent);
                let message = entry.message;
                if (Number.isInteger(entry.done)) {
                    const display = entry.bytes ? bytes : integer;
                    message += ' · ' + display.format(entry.done);
                    if (Number.isInteger(entry.total)) message += ' / ' + display.format(entry.total);
                }
                if (entry.result) {
                    finished = true;
                    const success = entry.result === 'success';
                    succeeded = success;
                    setState(success ? 'done' : 'failed', message);
                    if (success && installed && typeof entry.version === 'string') installed.textContent = entry.version;
                    reload.hidden = !success;
                } else {
                    current.textContent = message;
                    if (entry.step !== lastStep) log.append(document.createElement('li'));
                    log.lastElementChild.textContent = message;
                    lastStep = entry.step;
                }
            };
            for (;;) {
                const {value, done} = await reader.read();
                pending += decoder.decode(value, {stream: !done});
                const lines = pending.split('\n');
                pending = lines.pop();
                lines.forEach(show);
                if (done || finished) break;
            }
            if (pending.trim()) show(pending);
            if (!finished) throw new Error();
            if (succeeded && form.closest('[data-update-item]')) document.dispatchEvent(new Event('admin:updates-changed'));
        } catch {
            setState('unknown', panel.dataset.failure);
            reload.hidden = false;
        } finally {
            const completedDemo = form.hasAttribute('data-theme-demo') && succeeded;
            submit.disabled = completedDemo;
            if (settings) settings.disabled = completedDemo;
            if (confirm) confirm.disabled = completedDemo;
            submit.removeAttribute('aria-busy');
        }
    });
});
document.querySelector('[data-shop-print]')?.addEventListener('click', () => window.print());
document.addEventListener('DOMContentLoaded', () => {
    if (typeof window.Chart !== 'function') return;
    document.querySelectorAll('[data-shop-sales-chart]').forEach((canvas) => {
        const report = JSON.parse(canvas.dataset.shopSalesChart);
        const colors = getComputedStyle(canvas);
        const ink = colors.getPropertyValue('--ink').trim();
        const line = colors.getPropertyValue('--line').trim();
        const accent = colors.getPropertyValue('--accent').trim();
        const currency = new Intl.NumberFormat(document.documentElement.lang, { style: 'currency', currency: report.currency });
        canvas.parentElement.hidden = false;
        new window.Chart(canvas, {
            type: 'bar',
            data: {
                labels: report.dates,
                datasets: [
                    { label: report.labels[0], data: report.orders, backgroundColor: '#7b90ba' },
                    { label: report.labels[1], data: report.sales, backgroundColor: accent },
                    { label: report.labels[2], data: report.amounts, type: 'line', yAxisID: 'amount', borderColor: '#d69055' },
                ],
            },
            options: {
                color: ink, maintainAspectRatio: false,
                plugins: { tooltip: { callbacks: { label: ({ dataset, parsed }) =>
                    dataset.label + ' · ' + (dataset.yAxisID === 'amount' ? currency.format(parsed.y) : parsed.y) } } },
                scales: {
                    x: { ticks: { color: ink, maxTicksLimit: 10 }, grid: { display: false } },
                    y: { beginAtZero: true, ticks: { color: ink, precision: 0 }, grid: { color: line } },
                    amount: { position: 'right', beginAtZero: true, ticks: { color: ink, callback: (value) => currency.format(value) },
                        grid: { drawOnChartArea: false } },
                },
            },
        });
    });
});
document.querySelectorAll('[data-shop-record-sheet="values"] [name="field_id"]').forEach((field) => {
    const form = field.form;
    const target = form.elements.target_id;
    const text = form.querySelector('[data-record-text]');
    const boolean = form.querySelector('[data-record-bool]');
    const sync = () => {
        const {target: scope, type} = field.selectedOptions[0]?.dataset || {};
        for (const option of target.options) {
            if (!option.value) continue;
            const incompatible = !scope || !option.value.startsWith(scope + ':');
            option.disabled = option.hidden = incompatible;
            if (incompatible && option.selected) target.value = '';
        }
        const isBoolean = type === 'bool';
        text.hidden = text.disabled = isBoolean;
        boolean.hidden = boolean.disabled = !isBoolean;
    };
    field.addEventListener('change', sync);
    sync();
});
document.querySelectorAll('[data-shop-record-sheet="fields"] [name="type"]').forEach((type) => {
    const {filter, sort} = type.form.elements;
    const sync = () => {
        filter.disabled = ['text','textarea','hidden','form'].includes(type.value);
        sort.disabled = type.value !== 'number';
    };
    type.addEventListener('change', sync);
    sync();
});
document.querySelectorAll('[data-shop-import-format]').forEach((format) => {
    const separator = format.form.querySelector('[data-shop-csv-separator]');
    const file = format.form.elements.catalog_file;
    const columns = format.form.querySelector('[data-shop-import-columns]');
    const targets = JSON.parse(columns.dataset.columns);
    const delimitedHeader = (text, delimiter) => {
        const result = [];
        let quoted = false;
        let cell = '';
        for (let index = 0; index < text.length; index++) {
            const character = text[index];
            if (character === '"') {
                if (quoted && text[index + 1] === '"') { cell += '"'; index++; }
                else quoted = !quoted;
            } else if (!quoted && character === delimiter) {
                result.push(cell.trim());
                cell = '';
            } else if (!quoted && (character === '\n' || character === '\r')) break;
            else cell += character;
        }
        return quoted ? [] : [...result, cell.trim()];
    };
    let version = 0;
    const sync = async () => {
        const current = ++version;
        separator.hidden = format.value !== 'csv';
        columns.hidden = true;
        columns.replaceChildren();
        if (separator.hidden || !file.files.length) return;
        const text = await file.files[0].slice(0, 65536).text();
        if (current !== version) return;
        const delimiter = {pipe:'|',semicolon:';',comma:',',tab:'\t'}[format.form.elements.separator.value];
        const headers = [...new Set(delimitedHeader(text, delimiter).filter(Boolean))];
        if (headers.length < 2 || headers.length > 300) return;
        const key = (name) => name.toLowerCase().replace(/[^\p{L}\p{N}_]+/gu, '');
        for (const [field, target] of Object.entries(targets)) {
            const label = document.createElement('label');
            label.className = 'field';
            label.textContent = target.label;
            const select = document.createElement('select');
            select.name = 'mapping_columns[' + field + ']';
            select.add(new Option(columns.dataset.auto, ''));
            for (const header of headers) select.add(new Option(header, header));
            select.value = headers.find((header) => header === field || [field, ...target.aliases].includes(key(header))) || '';
            label.append(select);
            columns.append(label);
        }
        columns.hidden = false;
    };
    [format, file, format.form.elements.separator].forEach((field) => field.addEventListener('change', () => void sync().catch(() => {})));
    void sync().catch(() => {});
});
(() => {
    const dialog = document.querySelector('[data-shop-media-dialog]');
    if (!dialog || typeof dialog.showModal !== 'function') return;
    const image = dialog.querySelector('img');
    const caption = dialog.querySelector('[data-shop-media-caption]');
    const count = dialog.querySelector('[data-shop-media-count]');
    const controls = dialog.querySelectorAll('[data-shop-media-step]');
    let items = [];
    let current = 0;
    let opener;
    const show = (index) => {
        if (!items.length) return;
        current = (index + items.length) % items.length;
        const item = items[current];
        image.alt = item.querySelector('img').alt;
        image.src = item.href;
        caption.textContent = image.alt;
        count.textContent = (current + 1) + ' / ' + items.length;
        controls.forEach((button) => { button.disabled = items.length < 2; });
    };
    document.addEventListener('click', (event) => {
        const item = event.target.closest('[data-shop-media-item]');
        if (!item || event.button !== 0 || event.altKey || event.ctrlKey || event.metaKey || event.shiftKey) return;
        event.preventDefault();
        opener = item;
        items = [...item.closest('[data-shop-lightbox-group]').querySelectorAll('[data-shop-media-item]')];
        show(items.indexOf(item));
        dialog.showModal();
    });
    controls.forEach((button) => button.addEventListener('click', () => show(current + Number(button.dataset.shopMediaStep))));
    dialog.querySelector('[data-shop-media-close]').addEventListener('click', () => dialog.close());
    dialog.addEventListener('keydown', (event) => {
        if (event.key !== 'ArrowLeft' && event.key !== 'ArrowRight') return;
        event.preventDefault();
        show(current + (event.key === 'ArrowRight' ? 1 : -1));
    });
    dialog.addEventListener('click', (event) => { if (event.target === dialog) dialog.close(); });
    dialog.addEventListener('close', () => {
        image.removeAttribute('src');
        opener?.focus({preventScroll:true});
    });
})();
