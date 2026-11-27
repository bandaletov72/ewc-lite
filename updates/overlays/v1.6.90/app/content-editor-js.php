<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}
return <<<'JS'
const initializeEwcContentEditor = (editor) => {
    const listeners = new AbortController();
    editor.addEventListener('ewc:content-editor-remove', () => listeners.abort(), {once:true});
    const html = editor.querySelector('[data-editor-source]');
    const frame = editor.querySelector('[data-editor-frame]');
    const surface = editor.querySelector('[data-editor-surface]');
    const form = editor.closest('form');
    const format = editor.querySelector('[data-editor-format]');
    const feedback = editor.querySelector('[data-editor-feedback]');
    const historyKey = editor.dataset.editorHistoryKey;
    const historyButtons = Object.fromEntries([...editor.querySelectorAll('[data-editor-history-action]')].map(button => [button.dataset.editorHistoryAction, button]));
    const component = editor.querySelector('[data-editor-component]');
    const componentError = editor.querySelector('[data-editor-component-error]');
    let visual = false;
    let editorDocument = null;
    let range = null;
    let dirty = false;
    let imageTarget = '';
    let iconTarget = '';
    let visualImage = null;
    let movingImage = null;
    let editingLink = null;

    editor.querySelector('[data-editor-toolbar]').hidden = false;
    format.hidden = false;

    const formatSource = (source) => {
        const block = new Set(['address','article','aside','blockquote','div','dl','dt','dd','fieldset','figcaption',
            'figure','footer','form','h1','h2','h3','h4','h5','h6','header','hr','li','main','nav','ol','p','section',
            'table','tbody','thead','tfoot','tr','td','th','ul','pre','script','style']);
        const tags = /<!--[\s\S]*?-->|<(script|style|pre|textarea|code)\b(?:[^>"']|"[^"]*"|'[^']*')*>[\s\S]*?<\/\1\s*>|<\/?([a-z][a-z0-9:-]*)(?:[^>"']|"[^"]*"|'[^']*')*>/gi;
        let result = '';
        let offset = 0;
        let previousBlock = false;
        for (const match of source.matchAll(tags)) {
            const name = (match[1] || match[2] || '').toLowerCase();
            const currentBlock = block.has(name) || /^<span\s[^>]*data-content-image-align\b/i.test(match[0]);
            const between = source.slice(offset, match.index);
            const newLine = offset !== 0 && (previousBlock || currentBlock) && /^[\t ]*$/.test(between);
            result += (newLine ? '\n' : between) + match[0];
            offset = match.index + match[0].length;
            previousBlock = currentBlock;
        }
        return result + source.slice(offset);
    };
    const sync = () => {
        if (editorDocument && dirty) html.value = formatSource(editorDocument.body.innerHTML);
        dirty = false;
    };
    let savedSource = editor.hasAttribute('data-editor-history-draft') ? null : formatSource(html.value);
    let currentSource = html.value;
    let visualBaseline = '';
    let pendingHistory = [];
    let serverUndo = 0;
    let serverRedo = 0;
    let historyRequest = 0;
    let historyBusy = false;
    let historyGroup = '';
    let historyTime = 0;
    const source = () => visual && editorDocument && dirty ? formatSource(editorDocument.body.innerHTML) : html.value;
    editor.addEventListener('get-editor-comparable-source', (event) => event.detail(html.name, formatSource(source())));
    const updateHistoryButtons = () => {
        if (!historyKey) return;
        historyButtons.undo.disabled = historyBusy || (!pendingHistory.length && !serverUndo);
        historyButtons.redo.disabled = historyBusy || pendingHistory.length > 0 || !serverRedo;
        if (historyButtons.restore) {
            historyButtons.restore.disabled = historyBusy || formatSource(source()) === savedSource;
        }
    };
    const rememberEdit = (inputType) => {
        if (!historyKey) return;
        const next = source();
        if (next === currentSource || (visual && next === visualBaseline && currentSource === html.value && !pendingHistory.length)) return;
        const group = /^(insertText|insertCompositionText|deleteContentBackward|deleteContentForward)$/.test(inputType) ? inputType : '';
        const now = Date.now();
        if (!group || historyGroup !== group || now - historyTime > 650) {
            pendingHistory.push(currentSource);
            if (pendingHistory.length > 10) pendingHistory.shift();
        }
        historyGroup = group;
        historyTime = now;
        currentSource = next;
        updateHistoryButtons();
    };
    const replaceSource = (value) => {
        html.value = value;
        dirty = false;
        range = null;
        selectVisualImage(null);
        if (editorDocument) {
            editorDocument.body.innerHTML = value;
            visualBaseline = formatSource(editorDocument.body.innerHTML);
        }
        html.dispatchEvent(new Event('input', {bubbles: true}));
        editor.dispatchEvent(new CustomEvent('content-source', {detail: value}));
    };
    const changeHistory = async (operation) => {
        if (!historyButtons[operation] || historyButtons[operation].disabled) return;
        const before = source();
        const body = new FormData();
        body.set('action', 'editor-history');
        body.set('csrf', editor.dataset.editorCsrf);
        body.set('key', historyKey);
        body.set('request', String(++historyRequest));
        body.set('operation', operation);
        body.set('field', html.name);
        body.set('path', form.dataset.pagePath);
        body.set('draft', JSON.stringify({current: before, pending: pendingHistory}));
        historyBusy = true;
        historyGroup = '';
        editor.inert = true;
        editor.setAttribute('aria-busy', 'true');
        updateHistoryButtons();
        try {
            const send = () => fetch(editor.dataset.editorAdminUrl, {method: 'POST', body, headers: {Accept: 'application/json'}});
            const response = await send().catch(send);
            const result = await response.json();
            if (!response.ok || !result.ok || typeof result.source !== 'string') {
                throw new Error(result.message || adminText('Не удалось выполнить действие.'));
            }
            pendingHistory = [];
            serverUndo = result.undo;
            serverRedo = result.redo;
            currentSource = result.source;
            if (operation === 'restore') savedSource = formatSource(currentSource);
            replaceSource(currentSource);
            announce('');
        } catch (error) {
            announce(error.message || adminText('Не удалось выполнить действие.'));
        } finally {
            historyBusy = false;
            editor.inert = false;
            editor.removeAttribute('aria-busy');
            updateHistoryButtons();
            if (visual && editorDocument) visualSelection();
            else html.focus();
        }
    };
    const historyShortcut = (event) => {
        if (!(event.ctrlKey || event.metaKey) || event.altKey) return;
        const key = event.key.toLowerCase();
        if (key !== 'z' && key !== 'y') return;
        event.preventDefault();
        changeHistory(key === 'y' || event.shiftKey ? 'redo' : 'undo');
    };
    const historyInput = (event) => {
        if (!['historyUndo', 'historyRedo'].includes(event.inputType)) return;
        event.preventDefault();
        changeHistory(event.inputType === 'historyUndo' ? 'undo' : 'redo');
    };
    if (historyKey) {
        html.addEventListener('input', (event) => rememberEdit(event.inputType));
        html.addEventListener('keydown', historyShortcut);
        html.addEventListener('beforeinput', historyInput);
        html.addEventListener('pointerdown', () => { historyGroup = ''; });
        updateHistoryButtons();
    }
    const updateMode = (next) => {
        visual = next === 'visual';
        if (!visual) {
            sync();
            html.value = formatSource(html.value);
            currentSource = html.value;
            html.dispatchEvent(new Event('input', {bubbles:true}));
        }
        html.hidden = visual;
        frame.hidden = !visual;
        surface.hidden = !visual;
        editor.querySelector('[data-editor-visual-controls]').hidden = !visual;
        selectVisualImage(null);
        editor.querySelectorAll('[data-editor-mode]').forEach((button) =>
            button.setAttribute('aria-pressed', String(button.dataset.editorMode === next)));
        if (visual && !frame.hasAttribute('src')) {
            frame.src = frame.dataset.src;
        } else if (visual && editorDocument) {
            range = null;
            editorDocument.body.innerHTML = html.value;
            visualBaseline = formatSource(editorDocument.body.innerHTML);
        }
        historyGroup = '';
        updateHistoryButtons();
    };
    frame.addEventListener('load', () => {
        if (!frame.hasAttribute('src') || !frame.contentDocument?.body) return;
        editorDocument = frame.contentDocument;
        editorDocument.body.innerHTML = html.value;
        visualBaseline = formatSource(editorDocument.body.innerHTML);
        editorDocument.body.contentEditable = 'true';
        if (historyKey) {
            editorDocument.addEventListener('keydown', historyShortcut);
            editorDocument.addEventListener('beforeinput', historyInput);
            editorDocument.addEventListener('pointerdown', () => { historyGroup = ''; });
        }
        editorDocument.addEventListener('input', (event) => {
            dirty = true;
            rememberEdit(event.inputType);
            selectVisualImage(null);
            editor.dispatchEvent(new CustomEvent('content-source', { detail: editorDocument.body.innerHTML }));
        });
        editorDocument.addEventListener('click', (event) => {
            const image = event.target.closest?.('img');
            const link = event.target.closest?.('a');
            if (link) event.preventDefault();
            selectVisualImage(image || null);
            if (!image && link) openEditorTool('link', link);
        });
        editorDocument.addEventListener('scroll', positionImageSelection, true);
        editorDocument.addEventListener('load', positionImageSelection, true);
        editorDocument.addEventListener('dragstart', (event) => {
            movingImage = event.target.closest?.('img') || null;
            if (movingImage) {
                event.dataTransfer.setData('application/x-eurowebcart-image', 'image');
                event.dataTransfer.effectAllowed = 'move';
            }
        });
        editorDocument.addEventListener('dragover', (event) => {
            if (movingImage || event.dataTransfer.types.includes('Files')) event.preventDefault();
        });
        editorDocument.addEventListener('drop', (event) => {
            if (!movingImage) {
                if (event.dataTransfer.files.length) event.preventDefault();
                return;
            }
            event.preventDefault();
            const image = movingImage;
            movingImage = null;
            const caret = editorDocument.caretRangeFromPoint?.(event.clientX, event.clientY)
                || editorDocument.caretPositionFromPoint?.(event.clientX, event.clientY);
            const target = caret?.startContainer || caret?.offsetNode;
            if (!target || target === image || !editorDocument.body.contains(target)) return;
            const position = editorDocument.createRange();
            position.setStart(target, caret.startOffset ?? caret.offset);
            position.collapse(true);
            const movable = imageMovableNode(image);
            if (movable.contains(target)) return;
            position.insertNode(movable);
            selectVisualImage(image);
            changed();
        });
        editorDocument.addEventListener('dragend', () => { movingImage = null; });
        editorDocument.addEventListener('paste', (event) => {
            if (event.clipboardData?.files.length) event.preventDefault();
        });
        editorDocument.addEventListener('selectionchange', () => {
            const selection = editorDocument.getSelection();
            if (selection.rangeCount > 0 && editorDocument.body.contains(selection.anchorNode)) {
                range = selection.getRangeAt(0).cloneRange();
            }
        });
    });
    form.addEventListener('submit', (event) => {
        if (historyBusy) event.preventDefault();
        else sync();
    });
    editor.addEventListener('get-editor-source', sync);
    editor.addEventListener('set-editor-source', (event) => {
        replaceSource(event.detail);
        rememberEdit();
    });
    const visualSelection = () => {
        const selection = editorDocument.getSelection();
        frame.contentWindow.focus();
        editorDocument.body.focus();
        if (range && editorDocument.body.contains(range.startContainer)) {
            selection.removeAllRanges();
            selection.addRange(range);
        }
        if (!selection.rangeCount || !editorDocument.body.contains(selection.anchorNode)) {
            const end = editorDocument.createRange();
            end.selectNodeContents(editorDocument.body);
            end.collapse(false);
            selection.removeAllRanges();
            selection.addRange(end);
        }
        return selection;
    };
    const selectParameters = () => {
        if (!component) return;
        const current = component.value.startsWith('extension:') ? 'extension' : component.value;
        const pathField = editor.querySelector('[data-editor-param=path]');
        if (pathField) {
            const mediaBase = editor.dataset.editorMediaBase || '';
            pathField.placeholder = current === 'include' ? '/contacts/' : mediaBase + (current === 'files' ? '/files/myfiles/' : '/images/myfotos/');
        }
        editor.querySelectorAll('[data-editor-show]').forEach((field) => {
            field.hidden = !field.dataset.editorShow.split(' ').includes(current)
                || (field.dataset.editorShow === 'extension' && component.selectedOptions[0].dataset.kind !== 'paired');
        });
        editor.querySelectorAll('[data-editor-dimension]').forEach(field => { field.hidden = current === 'route'; });
        const buttonText = editor.querySelector('[data-editor-route-placeholder]');
        if (buttonText) buttonText.placeholder = current === 'route' ? buttonText.dataset.editorRoutePlaceholder : buttonText.dataset.editorDefaultPlaceholder;
        feedback.textContent = '';
        componentError.hidden = true;
    };
    selectParameters();
    if (component) component.addEventListener('change', selectParameters);
    const blockField = editor.querySelector('[data-editor-param="iblock-data"]');
    const chartLabels = editor.querySelector('[data-editor-param="chart-labels"]');
    const chartValues = editor.querySelector('[data-editor-param="chart-values"]');
    const chartType = editor.querySelector('[data-editor-param="chart-type"]');
    const singleChartSeries = () => ['pie', 'doughnut', 'polarArea'].includes(chartType.value);
    if (blockField) {
        blockField.addEventListener('change', () => {
            const columns = JSON.parse(blockField.selectedOptions[0]?.dataset.columns || '[]').filter(column => !['hidden', 'form'].includes(column.type));
            const numeric = columns.filter(column => ['number', 'price'].includes(column.type));
            chartLabels.replaceChildren(...columns.map(column => new Option(column.label, column.key)));
            const label = columns.find(column => !numeric.includes(column));
            if (label) chartLabels.value = label.key;
            chartValues.replaceChildren(...numeric.map((column, index) => new Option(column.label, column.key, false, index === 0)));
        });
        chartType.addEventListener('change', () => {
            chartValues.multiple = !singleChartSeries();
            chartValues.size = chartValues.multiple ? 4 : 1;
        });
    }

    const parameter = (key) => editor.querySelector('[data-editor-param="' + key + '"]').value.trim();
    const quote = (value) => '"' + value.replaceAll('"', '”') + '"';
    const componentCode = () => {
        const tag = component.value;
        if (['list-pages', 'reviews'].includes(tag)) return '[' + tag + ']';
        if (['table', 'charts', 'iblock'].includes(tag)) {
            const id = blockField.value;
            if (!/^[A-Za-z0-9_-]{1,64}$/.test(id)) return '';
            let opening = tag === 'iblock' ? parameter('layout') : tag;
            if (tag === 'charts') {
                const values = Array.from(chartValues.selectedOptions, option => option.value);
                if (!values.length || values.length > 8 || !chartLabels.value) return '';
                opening += ' type=' + chartType.value + ' labels=' + chartLabels.value + ' values=' + values.join(',');
                const title = parameter('chart-title');
                if (title) opening += ' title="' + escapeMarkup(title).replaceAll('[', '&#91;').replaceAll(']', '&#93;') + '"';
            }
            return '[iblock=' + opening + ']/data/' + id + '.txt[/iblock]';
        }
        if (tag === 'banners') {
            const set = parameter('banner-set').toLowerCase();
            const folder = parameter('banner-folder');
            if (set && !/^[a-z_-]{1,64}$/.test(set)) return '';
            if (folder && !/^\/(?:templates\/(?:themes\/[a-z0-9_-]+\/)?)?images(?:\/[^\[\]]*)?$/.test(folder)) return '';
            const options = [...editor.querySelectorAll('[data-editor-option]')].filter(input => !input.closest('label').hidden && input.value)
                .map(input => input.dataset.editorOption + '=' + input.value);
            if (set) options.unshift('set=' + set);
            return '[banners' + (options.length ? ' ' + options.join(' ') : '') + ']' + (folder ? folder + '[/banners]' : '');
        }
        if (tag.startsWith('extension:')) {
            const key = tag.slice('extension:'.length);
            const value = parameter('extension-value');
            if (component.selectedOptions[0].dataset.kind !== 'paired') return '[' + key + ']';
            return value && !/[\[\]]/.test(value) ? '[' + key + ']' + value + '[/' + key + ']' : '';
        }
        if (tag === 'php') {
            const name = parameter('module');
            return name ? '[php]' + name + '[/php]' : '';
        }
        if (tag === 'form') {
            const name = parameter('form');
            return /^form[1-9][0-9]{0,8}$/.test(name) ? '[' + name + ']' : '';
        }
        if (tag === 'callback') {
            const fields = ['button', 'title', 'submit', 'placeholder'].filter(parameter)
                .map((field) => field + '=' + quote(parameter(field)));
            return '[callback' + (fields.length ? ' ' + fields.join(' ') : '') + ']';
        }
        if (tag === 'route') {
            const provider = parameter('provider');
            const button = parameter('button');
            return '[route' + (provider ? ' provider=' + quote(provider) : '') + (button ? ' button=' + quote(button) : '') + ']';
        }
        if (tag === 'points') {
            const section = parameter('map-section');
            return section ? '[points]' + section + '[/points]' : '[points]';
        }
        if (['map', 'maps', 'maplist'].includes(tag)) {
            const lat = parameter('lat');
            const lng = parameter('lng');
            const fields = [];
            if (tag === 'map' && (lat || lng)) {
                if (!/^-?\d{1,3}(\.\d+)?$/.test(lat) || !/^-?\d{1,3}(\.\d+)?$/.test(lng)
                    || Math.abs(Number(lat)) > 90 || Math.abs(Number(lng)) > 180 || !parameter('title')) return '';
                fields.push('lat=' + lat, 'lng=' + lng);
            }
            const pointOptions = tag === 'map' ? ['title'] : (tag === 'maps' ? ['source'] : []);
            if (tag === 'maps' && parameter('source') && !/^[A-Za-z0-9_-]{1,64}$/.test(parameter('source'))) return '';
            for (const field of ['zoom', 'center', 'provider', 'mode', ...pointOptions]) {
                if (parameter(field)) fields.push(field + '=' + quote(parameter(field)));
            }
            const section = tag === 'maplist' ? parameter('map-section') : '';
            return '[' + tag + (fields.length ? ' ' + fields.join(' ') : '') + ']' + (section ? section + '[/' + tag + ']' : '');
        }
        const path = parameter('path');
        if (!path.startsWith('/') || /[\[\]]/.test(path)) return '';
        const params = Array.from(editor.querySelectorAll('[data-editor-option]')).filter((input) => !input.closest('label').hidden && input.value)
            .map((input) => ' ' + input.dataset.editorOption + '=' + input.value).join('');
        return '[' + tag + params + ']' + path + '[/' + tag + ']';
    };
    const build = () => {
        const code = componentCode();
        if (component.value === 'route') return code;
        const size = [...editor.querySelectorAll('[data-editor-size]')]
            .filter((field) => field.value).map((field) => ' ' + field.dataset.editorSize + '=' + field.value).join('');
        return size ? code.replace(/^\[((?:[^"\]]+|"[^"]*")+)\]/, (_, opening) => '[' + opening + size + ']') : code;
    };
    const escapeMarkup = (value) => value.replaceAll('&', '&amp;').replaceAll('"', '&quot;').replaceAll('<', '&lt;').replaceAll('>', '&gt;');
    const webUrl = (value) => /^(?:https?:\/\/|\/(?![\/\\]))/i.test(value) && !/[\x00-\x1f\x7f\\]/.test(value);
    const announce = (message) => {
        feedback.textContent = message;
        feedback.hidden = !message;
    };
    const changed = () => {
        if (visual) dirty = true;
        else html.dispatchEvent(new Event('input', {bubbles: true}));
        rememberEdit();
        editor.dispatchEvent(new CustomEvent('content-source', {detail: visual ? editorDocument.body.innerHTML : html.value}));
    };
    const insert = (markup, literal = false) => {
        if (!markup || (visual && !editorDocument)) {
            announce(adminText('Проверьте параметры компонента.'));
            return false;
        }
        historyGroup = '';
        if (visual) {
            const selection = visualSelection();
            if (!editorDocument.execCommand('insertHTML', false, literal ? escapeMarkup(markup) : markup)) {
                const cursor = selection.getRangeAt(0);
                cursor.deleteContents();
                cursor.insertNode(literal ? editorDocument.createTextNode(markup) : cursor.createContextualFragment(markup));
                cursor.collapse(false);
            }
        } else {
            html.setRangeText(markup, html.selectionStart, html.selectionEnd, 'end');
            html.focus();
        }
        changed();
        announce(adminText('Код вставлен.'));
        return true;
    };
    const formatHtml = (command, value) => {
        const start = html.selectionStart;
        const end = html.selectionEnd;
        const selected = html.value.slice(start, end);
        const inlineTags = {bold: 'strong', italic: 'em', underline: 'u', strikeThrough: 's', superscript: 'sup', subscript: 'sub'};
        const listTag = {insertUnorderedList: 'ul', insertOrderedList: 'ol'}[command];
        const tag = listTag || (command === 'formatBlock' ? value : inlineTags[command]);
        const opening = '<' + tag + '>';
        const inner = listTag ? selected.split(/\r?\n/).map(line => '<li>' + line.trim() + '</li>').join('\n') : selected;
        html.setRangeText(opening + inner + '</' + tag + '>', start, end, 'end');
        if (!selected) html.setSelectionRange(start + opening.length + (listTag ? 4 : 0), start + opening.length + (listTag ? 4 : 0));
        html.focus();
        changed();
    };
    const togglePicker = (button) => {
        const id = button.dataset.editorOpen;
        const expanded = button.getAttribute('aria-expanded') !== 'true';
        editor.querySelectorAll('[data-editor-open]').forEach((item) => item.setAttribute('aria-expanded', String(item === button && expanded)));
        editor.querySelectorAll('[data-editor-picker]').forEach((panel) => { panel.hidden = panel.dataset.editorPicker !== id || !expanded; });
        announce('');
        if (id === 'icon') {
            iconTarget = expanded ? button.dataset.editorIconTarget || '' : '';
            if (expanded) showIcons(!iconTarget);
        }
        if (expanded && id === 'image') {
            imageTarget = button.dataset.editorImageTarget || '';
            const imageButton = editor.querySelector('[data-editor-add-image]');
            imageButton.textContent = imageTarget ? imageButton.dataset.useLabel : imageButton.dataset.insertLabel;
            const imageUrl = editor.querySelector('[data-editor-image-url]');
            if (imageTarget) imageUrl.value = document.getElementById(imageTarget).value;
            if (!imageLoaded) browseImages('browse');
        }
        if (expanded) {
            const panel = editor.querySelector('[data-editor-picker="' + id + '"]');
            panel.querySelector('.editor-picker-body').scrollTop = 0;
            const firstField = panel.querySelector('input, select') || panel.querySelector('[data-editor-close-picker]');
            firstField.focus({preventScroll: true});
            panel.querySelector('.editor-picker-heading').scrollIntoView({block: 'nearest'});
        }
    };
    const dismissPicker = () => {
        const opener = editor.querySelector('[data-editor-open][aria-expanded="true"]');
        if (!opener) return;
        togglePicker(opener);
        opener.focus();
    };
    editor.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape' || !event.target.closest('[data-editor-picker]')) return;
        event.preventDefault();
        event.stopPropagation();
        dismissPicker();
    });
    const typography = editor.querySelector('[data-editor-picker="typography"]');
    const textStyles = [...typography.querySelectorAll('[data-editor-text-style]')];
    const applyTypography = typography.querySelector('[data-editor-apply-typography]');
    const typographyHint = typography.querySelector('[data-editor-typography-hint]');
    const typographyStyles = () => Object.fromEntries(textStyles
        .filter(input => input.value && (input.type !== 'color'
            || typography.querySelector('[data-editor-text-color-enabled="' + input.dataset.editorTextStyle + '"]').checked))
        .map(input => [input.dataset.editorTextStyle, input.value]));
    typography.addEventListener('input', ({target}) => {
        if (target.type === 'color') {
            typography.querySelector('[data-editor-text-color-enabled="' + target.dataset.editorTextStyle + '"]').checked = true;
        }
        applyTypography.disabled = Object.keys(typographyStyles()).length === 0;
    });
    applyTypography.addEventListener('click', () => {
        const styles = typographyStyles();
        const style = Object.entries(styles).map(([name, value]) => name + ': ' + value).join('; ');
        if (!style || (visual && !editorDocument)) return;
        let applied = false;
        historyGroup = '';
        if (visual) {
            const selection = visualSelection();
            const cursor = selection.getRangeAt(0);
            const root = cursor.commonAncestorContainer;
            const nodes = [];
            if (!cursor.collapsed) {
                if (root.nodeType === Node.TEXT_NODE) nodes.push(root);
                else {
                    const walker = editorDocument.createTreeWalker(root, NodeFilter.SHOW_TEXT);
                    while (walker.nextNode()) {
                        const text = walker.currentNode;
                        if (cursor.intersectsNode(text) && !text.parentElement.closest('script, style')) nodes.push(text);
                    }
                }
            }
            const spans = nodes.map(text => {
                const start = text === cursor.startContainer ? cursor.startOffset : 0;
                const end = text === cursor.endContainer ? cursor.endOffset : text.length;
                return [text, start, end];
            }).filter(([, start, end]) => start < end).map(([text, start, end]) => {
                const portion = editorDocument.createRange();
                portion.setStart(text, start);
                portion.setEnd(text, end);
                const span = editorDocument.createElement('span');
                Object.entries(styles).forEach(([name, value]) => span.style.setProperty(name, value));
                portion.surroundContents(span);
                return span;
            });
            if (spans.length) {
                const styled = editorDocument.createRange();
                styled.setStartBefore(spans[0]);
                styled.setEndAfter(spans[spans.length - 1]);
                selection.removeAllRanges();
                selection.addRange(styled);
                range = styled.cloneRange();
                selectVisualImage(null);
                applied = true;
            }
        } else {
            const start = html.selectionStart;
            const end = html.selectionEnd;
            if (start < end) {
                const opening = '<span style="' + escapeMarkup(style) + '">';
                html.setRangeText(opening + html.value.slice(start, end) + '</span>', start, end, 'select');
                html.focus();
                applied = true;
            }
        }
        typographyHint.classList.toggle('error', !applied);
        if (applied) {
            changed();
            togglePicker(editor.querySelector('[data-editor-open="typography"]'));
        } else {
            applyTypography.focus();
        }
    });
    const imageMarkup = (url, alt) => webUrl(url) ? '<img src="' + escapeMarkup(url) + '" alt="' + escapeMarkup(alt) + '">' : '';
    const imageProperties = editor.querySelector('[data-editor-image-properties]');
    const imageOutline = editor.querySelector('[data-editor-image-selection]');
    const resizeHandle = imageOutline.querySelector('[data-editor-image-resize]');
    const imageProperty = (name) => imageProperties.querySelector('[data-image-property="' + name + '"]');
    const marginProperties = ['margin-top','margin-right','margin-bottom','margin-left'];
    const imageContentNode = (image) => {
        const parent = image.parentElement;
        return parent.matches('a') && parent.children.length === 1 && !parent.textContent.trim() ? parent : image;
    };
    const imageMovableNode = (image) => image.closest('[data-content-image-align]') || imageContentNode(image);
    const fillImageProperty = (name, value) => {
        const field = imageProperty(name);
        field.value = value;
        field.dataset.initial = field.value;
    };
    const positionImageSelection = () => {
        if (!visualImage?.isConnected || !visual) {
            imageOutline.hidden = true;
            return;
        }
        const box = visualImage.getBoundingClientRect();
        imageOutline.hidden = !box.width || !box.height || box.bottom < 0 || box.top > frame.clientHeight;
        Object.assign(imageOutline.style, {left:(frame.clientLeft + box.left) + 'px', top:(frame.clientTop + box.top) + 'px',
            width:box.width + 'px', height:box.height + 'px'});
    };
    const selectVisualImage = (image) => {
        visualImage = image;
        imageProperties.hidden = !image;
        if (image) {
            fillImageProperty('url', image.getAttribute('src') || '');
            fillImageProperty('alt', image.getAttribute('alt') || '');
            fillImageProperty('style', image.getAttribute('style') || '');
            fillImageProperty('alignment', image.closest('[data-content-image-align]')?.dataset.contentImageAlign || '');
            ['width','height'].forEach(name => fillImageProperty(name, image.getAttribute(name) || ''));
            marginProperties.forEach(name => fillImageProperty(name, image.style.getPropertyValue(name)));
        }
        positionImageSelection();
    };
    const updateImageSize = (name, value) => {
        visualImage.style.removeProperty(name);
        if (!visualImage.style.length) visualImage.removeAttribute('style');
        if (value) visualImage.setAttribute(name, value);
        else visualImage.removeAttribute(name);
    };
    const setImageAlignment = (alignment) => {
        const content = imageContentNode(visualImage);
        let wrapper = content.closest('[data-content-image-align]');
        if (!alignment) {
            if (wrapper) wrapper.replaceWith(...wrapper.childNodes);
            return;
        }
        if (!wrapper) {
            wrapper = editorDocument.createElement('span');
            content.before(wrapper);
            wrapper.append(content);
        }
        wrapper.dataset.contentImageAlign = alignment;
        wrapper.style.display = 'flex';
        wrapper.style.justifyContent = {left:'flex-start',center:'center',right:'flex-end'}[alignment];
    };
    editor.querySelector('[data-editor-apply-image]').addEventListener('click', () => {
        if (!visualImage?.isConnected) return;
        const url = imageProperty('url').value.trim();
        const sizes = ['width','height'].map(name => imageProperty(name));
        const margins = marginProperties.map((name) => {
            const field = imageProperty(name);
            const raw = field.value.trim();
            const length = /^-?\d+(?:\.\d+)?$/.test(raw) && raw !== '0' ? raw + 'px' : raw;
            return {field,name,length};
        });
        if (!webUrl(url) || sizes.some(field => field.value && !field.checkValidity())
            || margins.some(({length,name}) => length && !CSS.supports(name,length))) {
            announce(adminText('Проверьте параметры компонента.'));
            return;
        }
        visualImage.setAttribute('src', url);
        visualImage.setAttribute('alt', imageProperty('alt').value);
        const style = imageProperty('style');
        if (style.value !== style.dataset.initial) visualImage.style.cssText = style.value;
        sizes.forEach(field => {
            if (field.value !== field.dataset.initial) updateImageSize(field.dataset.imageProperty, field.value);
        });
        margins.forEach(({field,name,length}) => {
            if (field.value !== field.dataset.initial) {
                if (length) visualImage.style.setProperty(name,length);
                else visualImage.style.removeProperty(name);
            }
        });
        const alignment = imageProperty('alignment');
        if (alignment.value !== alignment.dataset.initial) setImageAlignment(alignment.value);
        selectVisualImage(visualImage);
        changed();
    });
    editor.querySelector('[data-editor-close-image]').addEventListener('click', () => selectVisualImage(null));
    let sizeDrag = null;
    resizeHandle.addEventListener('pointerdown', (event) => {
        if (!visualImage?.isConnected || event.button !== 0) return;
        const box = visualImage.getBoundingClientRect();
        sizeDrag = {id:event.pointerId, x:event.clientX, y:event.clientY, width:box.width, height:box.height};
        surface.setAttribute('data-editor-resizing', '');
        event.preventDefault();
    });
    window.addEventListener('pointermove', (event) => {
        if (!sizeDrag || event.pointerId !== sizeDrag.id || !visualImage?.isConnected) return;
        const clamp = (size) => String(Math.min(6000, Math.max(1, Math.round(size))));
        const width = sizeDrag.width + event.clientX - sizeDrag.x;
        const height = event.shiftKey ? sizeDrag.height + event.clientY - sizeDrag.y : width * sizeDrag.height / sizeDrag.width;
        updateImageSize('width', clamp(width));
        updateImageSize('height', clamp(height));
        ['width','height'].forEach(name => { imageProperty(name).value = visualImage.getAttribute(name); });
        positionImageSelection();
        changed();
    }, {signal:listeners.signal});
    const stopResize = () => {
        if (!sizeDrag) return;
        sizeDrag = null;
        surface.removeAttribute('data-editor-resizing');
    };
    ['pointerup','pointercancel','blur'].forEach(type => window.addEventListener(type, stopResize, {signal:listeners.signal}));
    window.addEventListener('resize', positionImageSelection, {signal:listeners.signal});

    const linkDialog = editor.querySelector('[data-editor-tool-dialog="link"]');
    const buttonDialog = editor.querySelector('[data-editor-tool-dialog="button"]');
    const linkField = (key) => linkDialog.querySelector('[data-editor-link-' + key + ']');
    const showToolError = (dialog, message) => {
        const output = dialog.querySelector('[data-editor-tool-error]');
        output.hidden = !message;
        output.textContent = message;
    };
    const updateButtonAction = () => {
        const action = buttonDialog.querySelector('[data-editor-button-action]').value;
        buttonDialog.querySelectorAll('[data-editor-button-when]').forEach(panel => { panel.hidden = panel.dataset.editorButtonWhen !== action; });
    };
    buttonDialog.querySelector('[data-editor-button-action]').addEventListener('change', updateButtonAction);
    const openEditorTool = (tool, anchor = null) => {
        const dialog = tool === 'link' ? linkDialog : buttonDialog;
        if (dialog.open) return;
        showToolError(dialog, '');
        if (tool === 'link') {
            editingLink = anchor;
            const href = anchor?.getAttribute('href') || '';
            linkField('type').value = href.startsWith('mailto:') ? 'mailto' : href.startsWith('tel:') ? 'tel' : href.startsWith('#') ? 'anchor' : 'url';
            linkField('address').value = href.replace(/^(?:mailto:|tel:|#)/, '');
            linkField('text').value = anchor?.textContent || (visual ? range?.toString() || '' : html.value.slice(html.selectionStart, html.selectionEnd));
            linkField('title').value = anchor?.title || '';
            linkField('tab').checked = anchor?.target === '_blank';
        } else updateButtonAction();
        dialog.showModal();
    };
    editor.querySelectorAll('[data-editor-tool-close]').forEach(button => button.addEventListener('click', () => button.closest('dialog').close()));
    linkDialog.querySelector('[data-editor-insert-link]').addEventListener('click', () => {
        const type = linkField('type').value;
        let input = linkField('address').value.trim();
        if (type === 'mailto') input = input.replace(/^mailto:/i, '');
        if (type === 'tel') input = input.replace(/^tel:/i, '');
        let href = '';
        if (type === 'url' && webUrl(input)) href = input;
        if (type === 'mailto' && /^[^\s:@?]+@[^\s:@?]+\.[^\s:@?]+$/.test(input)) href = 'mailto:' + input;
        if (type === 'tel' && /^\+?[\d ()-]+$/.test(input) && /\d/.test(input)) href = 'tel:' + input.replace(/[ ()-]/g, '');
        if (type === 'anchor' && /^#?[^#\s]+$/.test(input)) href = '#' + input.replace(/^#/, '');
        if (!href) return showToolError(linkDialog, adminText('Проверьте параметры компонента.'));
        const text = linkField('text').value || input;
        const title = linkField('title').value;
        const blank = linkField('tab').checked;
        linkDialog.close();
        if (visual && editingLink?.isConnected) {
            editingLink.setAttribute('href', href);
            if (text !== editingLink.textContent) editingLink.textContent = text;
            editingLink.title = title;
            if (blank) {
                editingLink.target = '_blank';
                editingLink.rel = [...new Set(editingLink.rel.split(/\s+/).filter(Boolean).concat('noopener','noreferrer'))].join(' ');
            } else editingLink.removeAttribute('target');
            changed();
        } else {
            insert('<a href="' + escapeMarkup(href) + '"' + (title ? ' title="' + escapeMarkup(title) + '"' : '') +
                (blank ? ' target="_blank" rel="noopener noreferrer"' : '') + '>' + escapeMarkup(text) + '</a>');
        }
    });
    buttonDialog.querySelector('[data-editor-insert-action]').addEventListener('click', () => {
        const action = buttonDialog.querySelector('[data-editor-button-action]').value;
        const read = (key) => buttonDialog.querySelector('[data-editor-action-field="' + key + '"]').value.trim();
        const options = {action, label:read('label')};
        const invalid = () => showToolError(buttonDialog, adminText('Проверьте параметры компонента.'));
        if (!options.label) return invalid();
        if (action === 'url') {
            options.url = read('url');
            if (!webUrl(options.url)) return invalid();
        } else if (action === 'form') {
            options.form = read('form');
            if (!options.form) return invalid();
        } else if (action === 'payment') {
            options.amount = read('amount').replace(',', '.');
            options.currency = read('currency');
            if (!/^\d{1,12}(?:\.\d{1,2})?$/.test(options.amount) || Number(options.amount) <= 0) return invalid();
            let count = 0;
            for (const field of buttonDialog.querySelectorAll('[data-payment-hosts]')) {
                const value = field.value.trim();
                const provider = field.dataset.editorActionField;
                if (!value) continue;
                const wallet = provider === 'yoomoney' && options.currency === 'RUB' && /^\d{10,20}$/.test(value);
                if (!wallet) {
                    let url;
                    try { url = new URL(value); } catch { return invalid(); }
                    if (url.protocol !== 'https:' || !field.dataset.paymentHosts.split(' ').includes(url.hostname)
                        || url.username || url.password || (url.port && url.port !== '443') || url.pathname === '/') return invalid();
                }
                options[provider] = value;
                count++;
            }
            if (!count) return invalid();
        }
        buttonDialog.close();
        const parameters = Object.entries(options).map(([key,value]) => key + '="' + escapeMarkup(value) + '"').join(' ');
        insert('[button ' + parameters + ']', true);
    });
    const library = editor.querySelector('[data-image-library]');
    const imageField = editor.querySelector('[data-editor-image-url]');
    const imageError = editor.querySelector('[data-editor-image-error]');
    const roots = library.querySelector('[data-image-root]');
    const parentFolder = library.querySelector('[data-image-parent]');
    const pathLabel = library.querySelector('[data-image-path]');
    const imageList = library.querySelector('[data-image-list]');
    const preview = library.querySelector('[data-image-preview]');
    const thumbnail = library.querySelector('[data-image-thumbnail]');
    const dimensions = library.querySelector('[data-image-size]');
    const imageStatus = library.querySelector('[data-image-library-status]');
    const valueIn = (key) => library.querySelector('[data-image-' + key + ']').value;
    let folder = roots.value;
    let activeImage = null;
    let imageLoaded = false;
    let imageBusy = false;

    const imageFeedback = (message) => {
        imageStatus.textContent = message;
        imageStatus.hidden = !message;
    };
    const selectImage = (item) => {
        if (!item && activeImage && imageField.value === activeImage.url) imageField.value = '';
        activeImage = item;
        preview.hidden = !item;
        imageList.querySelectorAll('[aria-pressed]').forEach(button => button.setAttribute('aria-pressed', String(button.dataset.name === item?.name)));
        if (!item) return;
        imageError.hidden = true;
        imageField.value = item.url;
        thumbnail.src = item.url;
        dimensions.textContent = '';
        ['width', 'height'].forEach(key => { library.querySelector('[data-image-' + key + ']').value = ''; });
        library.querySelector('[data-image-angle]').value = '0';
    };
    imageField.addEventListener('input', () => { imageError.hidden = true; });
    thumbnail.addEventListener('load', () => { dimensions.textContent = thumbnail.naturalWidth + ' × ' + thumbnail.naturalHeight; });
    const renderImages = (result) => {
        folder = result.path;
        pathLabel.textContent = folder + '/';
        parentFolder.hidden = folder === roots.value;
        const selected = result.selected || activeImage?.name;
        imageList.replaceChildren(...result.items.map(item => {
            const row = document.createElement('li');
            const choose = document.createElement('button');
            choose.type = 'button';
            if (item.type === 'directory') {
                choose.classList.add('media-folder-button');
                const icon = document.createElement('i');
                icon.className = 'fa-regular fa-folder';
                icon.setAttribute('aria-hidden', 'true');
                choose.append(icon, item.name + '/');
            } else {
                choose.textContent = item.name;
            }
            choose.dataset.name = item.name;
            if (item.type === 'image') choose.setAttribute('aria-pressed', 'false');
            choose.addEventListener('click', () => {
                if (item.type === 'directory') {
                    selectImage(null);
                    browseImages('browse', {}, folder + '/' + item.name);
                } else selectImage(item);
            });
            const remove = document.createElement('button');
            remove.type = 'button';
            remove.className = 'media-icon-button';
            const trash = document.createElement('i');
            trash.className = 'fa-regular fa-trash-can';
            trash.setAttribute('aria-hidden', 'true');
            remove.append(trash);
            remove.title = adminText('Удалить') + ' ' + item.name;
            remove.setAttribute('aria-label', remove.title);
            remove.addEventListener('click', () => {
                const prompt = item.type === 'directory' ? library.dataset.folderConfirm : library.dataset.deleteTitle;
                if (window.confirm(prompt + '\n' + item.name)) browseImages('delete', {name:item.name, confirm:'1'});
            });
            row.append(choose, remove);
            return row;
        }));
        selectImage(result.items.find(item => item.type === 'image' && item.name === selected) || null);
    };
    const requestImages = async (operation, fields, path) => {
        const data = new FormData();
        Object.entries({action:'image-library', csrf:editor.dataset.editorCsrf, operation, path, ...fields})
            .forEach(([key, value]) => data.append(key, value));
        const response = await fetch(editor.dataset.editorAdminUrl, {method:'POST', body:data, headers:{Accept:'application/json'}});
        if (!response.headers.get('Content-Type')?.includes('application/json')) throw new Error(library.dataset.errorText);
        const payload = await response.json();
        if (!response.ok || !payload.ok) throw new Error(payload.message || library.dataset.errorText);
        imageLoaded = true;
        renderImages(payload);
    };
    async function browseImages(operation, fields = {}, path = folder, files = []) {
        if (imageBusy) return;
        imageBusy = true;
        library.setAttribute('aria-busy', 'true');
        try {
            imageFeedback('');
            if (files.length) {
                for (const file of files) await requestImages(operation, {upload:file}, path);
            } else await requestImages(operation, fields, path);
        } catch (error) {
            imageFeedback(error.message || library.dataset.errorText);
        } finally {
            imageBusy = false;
            library.removeAttribute('aria-busy');
        }
    }
    roots.addEventListener('change', () => { selectImage(null); browseImages('browse', {}, roots.value); });
    parentFolder.addEventListener('click', () => { selectImage(null); browseImages('browse', {}, folder.slice(0, folder.lastIndexOf('/'))); });
    library.querySelector('[data-image-mkdir]').addEventListener('click', () => {
        const name = valueIn('folder-name').trim();
        if (name) browseImages('mkdir', {name});
    });
    library.querySelector('[data-image-upload-button]').addEventListener('click', () => {
        const upload = library.querySelector('[data-image-upload]');
        const files = [...upload.files];
        if (files.length) browseImages('upload', {}, folder, files);
    });
    library.querySelector('[data-image-transform]').addEventListener('click', () => {
        if (activeImage) browseImages('transform', {name:activeImage.name, width:valueIn('width'), height:valueIn('height'), rotate:valueIn('angle')});
    });

    const insertComponent = async (trigger) => {
        const report = (message) => {
            componentError.textContent = message;
            componentError.hidden = !message;
        };
        const type = component.value;
        const media = ['gal','fotos','files','banners'].includes(type);
        const folderPath = media ? editor.querySelector('[data-editor-param="' + (type === 'banners' ? 'banner-folder' : 'path') + '"]').value : '';
        const folderPattern = type === 'files'
            ? /^\/(?:templates\/(?:themes\/[a-z0-9_-]+\/)?)?files(?:\/[A-Za-z0-9_-]+)*\/?$/
            : /^\/(?:templates\/(?:themes\/[a-z0-9_-]+\/)?)?images(?:\/[A-Za-z0-9_-]+)*\/?$/;
        if (media && (folderPath || type !== 'banners') && !folderPattern.test(folderPath)) {
            report(type === 'files' ? componentError.dataset.filesError : componentError.dataset.imagesError);
            return;
        }
        const markup = build();
        if (!markup) {
            const messages = {charts:componentError.dataset.chartError, banners:componentError.dataset.bannerError, files:componentError.dataset.filesError,
                gal:componentError.dataset.imagesError, fotos:componentError.dataset.imagesError};
            report(messages[type] || adminText('Проверьте параметры компонента.'));
            return;
        }
        report('');
        if (folderPath && media) {
            trigger.disabled = true;
            try {
                const body = new URLSearchParams({action:'create-media-folder',ajax:'1',csrf:editor.dataset.editorCsrf,type,folder:folderPath});
                const response = await fetch(editor.dataset.editorAdminUrl, {method:'POST',body,headers:{Accept:'application/json'}});
                if (!response.headers.get('Content-Type')?.includes('application/json')) throw new Error(library.dataset.errorText);
                const result = await response.json();
                if (!response.ok || !result.ok) throw new Error(result.message || library.dataset.errorText);
            } catch (error) {
                report(error.message || library.dataset.errorText);
                return;
            } finally {
                trigger.disabled = false;
            }
        }
        if (insert(markup, true)) {
            editor.querySelector('[data-editor-picker="component"]').hidden = true;
            editor.querySelector('[data-editor-open="component"]').setAttribute('aria-expanded', 'false');
        }
    };

    const showIcons = siteIconPicker(editor.querySelector('[data-editor-picker="icon"]'), (item, code) => {
        if (iconTarget) {
            const target = document.getElementById(iconTarget);
            target.value = code;
            target.dispatchEvent(new Event('input', {bubbles: true}));
            togglePicker(editor.querySelector('[data-editor-icon-target][aria-expanded="true"]'));
        } else if (item.image) {
            insert(imageMarkup(item.image, ''));
        } else {
            insert(code, true);
        }
    });
    editor.addEventListener('click', (event) => {
        const mode = event.target.closest('[data-editor-mode]');
        const command = event.target.closest('[data-editor-command]');
        const picker = event.target.closest('[data-editor-open]');
        const tool = event.target.closest('[data-editor-dialog-trigger]');
        const historyAction = event.target.closest('[data-editor-history-action]');
        if (historyAction) {
            changeHistory(historyAction.dataset.editorHistoryAction);
        } else if (mode) {
            updateMode(mode.dataset.editorMode);
        } else if (tool) {
            openEditorTool(tool.dataset.editorDialogTrigger);
        } else if (picker) {
            togglePicker(picker);
        } else if (event.target.closest('[data-editor-close-picker]')) {
            dismissPicker();
        } else if (event.target.closest('[data-editor-custom-settings]:not([target])')
            && !event.ctrlKey && !event.metaKey && !event.shiftKey && !event.altKey) {
            dismissPicker();
        } else if (event.target.closest('[data-editor-directory]')) {
            if (insert(event.target.closest('[data-editor-directory]').dataset.editorDirectory, true)) {
                togglePicker(editor.querySelector('[data-editor-open="directory"]'));
            }
        } else if (event.target.closest('[data-editor-code]')) {
            insert(event.target.closest('[data-editor-code]').dataset.editorCode, true);
        } else if (command && (!visual || editorDocument)) {
            const value = command.dataset.editorValue || null;
            const name = command.dataset.editorCommand;
            if (visual) {
                const alignment = {justifyLeft:'left',justifyCenter:'center',justifyRight:'right'}[name];
                if (alignment && visualImage?.isConnected) {
                    setImageAlignment(alignment);
                    fillImageProperty('alignment', alignment);
                    positionImageSelection();
                    changed();
                } else {
                    visualSelection();
                    editorDocument.execCommand(name, false, value);
                    changed();
                }
            } else {
                formatHtml(name, value);
            }
        } else if (event.target.closest('[data-editor-insert-button]')) {
            insertComponent(event.target.closest('[data-editor-insert-button]'));
        } else if (event.target.closest('[data-editor-add-template]')) {
            const value = editor.querySelector('[data-editor-template]').value;
            insert(value ? '[' + value + ']' : '', true);
        } else if (event.target.closest('[data-editor-add-custom-field]')) {
            const value = editor.querySelector('[data-editor-custom-field]').value;
            if (insert(value, true)) togglePicker(editor.querySelector('[data-editor-open="custom-fields"]'));
        } else if (event.target.closest('[data-editor-add-image]')) {
            const url = imageField.value.trim();
            if (!webUrl(url)) {
                imageError.textContent = url ? imageError.dataset.invalid : imageError.dataset.required;
                imageError.hidden = false;
                return;
            }
            imageError.hidden = true;
            if (imageTarget) {
                const prefix = editor.dataset.editorBase;
                const target = document.getElementById(imageTarget);
                target.value = prefix && url.startsWith(prefix + '/') ? url.slice(prefix.length) : url;
                target.dispatchEvent(new Event('input', {bubbles: true}));
                togglePicker(editor.querySelector('[data-editor-image-target][aria-expanded="true"]'));
            } else insert(imageMarkup(url, editor.querySelector('[data-editor-image-alt]').value));
        }
    });
};
document.querySelectorAll('[data-content-editor]').forEach(initializeEwcContentEditor);
document.addEventListener('ewc:content-editor-added', (event) => initializeEwcContentEditor(event.target));
JS;
