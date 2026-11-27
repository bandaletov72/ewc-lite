<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}

function admin_apply_inline_changes(string $original, string $html, array $changes, string $base): string
{
    $extensions = site_extensions();
    $settings = site_settings();
    $snippets = ['author' => '', 'site-name' => '', 'site-url' => ''] + site_date_snippets()
        + site_snippet_values(site_content_host($base)) + array_map('site_escape', $settings['variables']);
    $before = site_inline_components($original, $extensions, $snippets);
    $after = $original === $html ? $before : site_inline_components($html, $extensions, $snippets);
    $sameOrder = array_column($before, 'code') === array_column($after, 'code');
    $beforeCounts = $sameOrder ? [] : array_count_values(array_column($before, 'code'));
    $byCode = [];
    if (!$sameOrder) {
        foreach ($after as $item) $byCode[$item['code']][] = $item;
    }
    $edits = [];
    foreach ($changes as $index => $replacement) {
        $component = $before[$index] ?? null;
        $replacement = is_string($replacement) ? trim($replacement) : '';
        $new = strlen($replacement) <= 4000 && preg_match('//u', $replacement) === 1
            ? site_inline_components($replacement, $extensions, $snippets) : [];
        if ($component === null || count($new) !== 1 || $new[0]['code'] !== $replacement || $new[0]['kind'] !== $component['kind']) {
            throw new RuntimeException('Введите один код вставки того же компонента.');
        }
        $matches = $byCode[$component['code']] ?? [];
        $target = $sameOrder ? $after[$index] : (count($matches) === 1 && $beforeCounts[$component['code']] === 1 ? $matches[0] : null);
        if ($target === null || isset($edits[$target['offset']])) {
            $labels = require SITE_ROOT . '/app/help-page-component-settings.php';
            throw new RuntimeException(($labels[$GLOBALS['adminLanguage'] ?? 'ru'] ?? $labels['ru'])[1]);
        }
        $edits[$target['offset']] = [strlen($target['code']), $replacement];
    }
    krsort($edits, SORT_NUMERIC);
    foreach ($edits as $offset => list($length, $replacement)) {
        $html = substr_replace($html, $replacement, $offset, $length);
    }
    return $html;
}

function admin_live_edit(string $action, array $input, string $base, bool $focusedPage = false)
{
    $path = admin_input($input, 'path');
    $returnPath = $path === '' ? '' : $path;
    $returnToEditor = $action === 'live-component' && admin_input($input, 'return_editor') === '1';
    if ($returnPath !== '' && !site_valid_path($returnPath)) {
        throw new RuntimeException('Страница не найдена.');
    }
    if ($action === 'public-edit-mode') {
        $_SESSION['public_edit'] = admin_input($input, 'enabled') === '1';
    } else {
        $revision = admin_input($input, 'revision');
        $page = admin_current_page($path, $revision);
        if ($action === 'live-page-menu') {
            admin_update_page_menu($path, $revision, admin_input($input, 'visible') === '1' ? '1' : '0');
        } else {
            if ($action === 'live-component' && !$returnToEditor && empty($_SESSION['public_edit'])) {
                throw new RuntimeException('Включите режим правки.');
            }
            $fields = $page['fields'];
            $html = $page['html'];
            if ($action === 'live-page-asset') {
                $asset = admin_input($input, 'asset');
                if (!in_array($asset, ['CSS', 'JS'], true)) {
                    throw new RuntimeException('Выберите CSS или JS.');
                }
                $fields[$asset] = admin_encode_asset(admin_input($input, 'code'));
            } else {
                if (!preg_match('/\A[0-9]{1,6}\z/', admin_input($input, 'index'))) {
                    throw new RuntimeException('Не удалось выбрать вставку.');
                }
                $index = (int) admin_input($input, 'index');
                $html = admin_apply_inline_changes($html, $html, [$index => admin_input($input, 'code')], $base);
            }
            admin_save_page($path, $fields, $html, $revision, false);
        }
    }
    if ($returnToEditor && $focusedPage) $_SESSION['notice'] = 'Страница сохранена.';
    $location = $returnToEditor ? admin_page_edit_url(site_url($base, 'admin/'), $returnPath) . ($focusedPage ? '&embed=1' : '') . '#page-components'
        : site_page_url($base, $returnPath);
    $shopBack = admin_input($input, 'shop_back');
    if ($action === 'public-edit-mode' && site_shop_enabled()
        && preg_match('~\A' . preg_quote(site_url($base, 'shop/'), '~') . '(?:[a-z0-9_-]+/)*(?:\?[A-Za-z0-9._\x7e%&=+\-]*)?\z~D', $shopBack)) {
        $location = $shopBack;
    }
    header('Location: ' . $location, true, 303);
    exit;
}
