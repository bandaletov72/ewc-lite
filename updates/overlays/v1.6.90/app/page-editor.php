<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}

function site_editor_session(string $base): array
{
    $cookie = $_COOKIE['FILE_SITE_ADMIN'] ?? '';
    if (!is_string($cookie) || !preg_match('/\A[A-Za-z0-9,-]{16,128}\z/', $cookie)) {
        return [];
    }
    require_once SITE_ROOT . '/admin/access.php';
    try {
        $auth = admin_auth_state();
    } catch (RuntimeException $exception) {
        return [];
    }
    if (!isset($auth['password'])) {
        return [];
    }
    session_name('FILE_SITE_ADMIN');
    session_id($cookie);
    session_cache_limiter('');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_set_cookie_params(0, site_url($base), '', !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off', true);
    if (!@session_start(['read_and_close' => true])) {
        return [];
    }
    $state = $_SESSION;
    $_SESSION = [];
    if (!is_string($state['auth'] ?? null) || !hash_equals(admin_auth_key($auth), $state['auth'])
        || !is_string($state['csrf'] ?? null)) {
        return [];
    }
    $region = site_region_settings();
    $language = $state['ui_language'] ?? '';
    return ['csrf' => $state['csrf'], 'edit' => ($state['public_edit'] ?? false) === true,
        'language' => in_array($language, $region['languages'], true) ? $language : $region['interface']];
}

function site_inline_component_type(array $match, array $extensions, array $snippets): string
{
    if (!empty($match['banners'])) {
        return 'banners';
    }
    if (!empty($match['galleryTag'])) {
        return strtolower($match['galleryTag']);
    }
    if (!empty($match['map'])) {
        return strtolower($match['map']);
    }
    if (!empty($match['sectionMap'])) {
        return strtolower($match['sectionMap']);
    }
    foreach (['files', 'include', 'iblock', 'form', 'callback', 'phpModule', 'extensionTag', 'iconName', 'actionButton'] as $key) {
        if (!empty($match[$key])) {
            return $key;
        }
    }
    $name = strtolower(($match['sizeTag'] ?? '') ?: ($match['snippet'] ?? ''));
    if ($name === 'reviews' || !empty($match['reviewsCurrent'])) return 'reviews';
    if (isset(site_layout_blocks()[$name])) {
        return '';
    }
    return isset(site_component_schema()[$name]) || isset($snippets[$name]) || isset($extensions[$name]) ? 'snippet' : '';
}

function site_page_edit_groups(array $page, array $extensions, array $snippets): array
{
    $groups = ['components' => [], 'snippets' => []];
    $seen = [];
    foreach (site_inline_components($page['html'], $extensions, $snippets) as $index => $item) {
        $name = strtolower($item['arguments']['snippet'] ?? '');
        if ($item['kind'] === 'snippet' && isset($snippets[$name]) && !isset($extensions[$name])) {
            $groups['snippets'][$index] = $item;
            $seen[$name] = true;
        } else {
            $groups['components'][$index] = $item;
        }
    }
    $metadata = array_intersect_key($page['fields'], array_flip(['Title', 'Heading', 'Description', 'Keywords', 'Menu']));
    foreach ($metadata as $text) {
        preg_match_all('~' . SITE_SNIPPET_TOKEN . '~i', $text, $matches);
        foreach ($matches['snippet'] as $matched) {
            $name = strtolower($matched);
            if (isset($seen[$name]) || !isset($snippets[$name]) || isset(site_layout_blocks()[$name])) continue;
            $seen[$name] = true;
            $groups['snippets']['meta-' . $name] = ['code' => '[' . $name . ']', 'kind' => 'snippet', 'arguments' => ['snippet' => $name], 'offset' => null];
        }
    }
    return $groups;
}

function site_edit_layout_labels(string $language): array
{
    static $labels;
    if ($labels === null) $labels = require __DIR__ . '/help-editor-overlays.php';
    return $labels[$language] ?? $labels['ru'];
}

function site_public_page_edit_url(string $base, string $path): string
{
    return site_url($base, 'admin/') . '?' . http_build_query(['page' => site_page_url('', $path)]);
}

function site_mark_editable_page_content(string $markup, string $base, string $path): string
{
    if (trim($markup) === '') return $markup;
    $id = 'edit-page-content-' . bin2hex(random_bytes(6));
    return '<span hidden data-page-edit-start="' . $id . '" data-page-edit-url="'
        . site_escape(site_public_page_edit_url($base, $path)) . '"></span>'
        . $markup . '<span hidden data-page-edit-end="' . $id . '"></span>';
}

function site_mark_editable_layout(string $name, string $markup, array $context): string
{
    if (empty($context['layoutInventory']) || trim($markup) === '') return $markup;
    $query = isset($context['layoutDomainOverrides'][$name])
        ? ['domains' => '1', 'templates' => $context['layoutDomain']] : ['layout' => '1'];
    $base = site_url($context['base'], 'admin/');
    $anchor = '#layout-' . $name;
    $url = $base . '?' . http_build_query($query) . $anchor;
    $modal = $base . '?' . http_build_query($query + ['template' => $name, 'embed' => '1']) . $anchor;
    $id = 'edit-layout-' . bin2hex(random_bytes(6));
    return '<span hidden data-layout-edit-start="' . $id . '" data-layout-edit-name="' . site_escape($name) . '" data-layout-edit-url="' . site_escape($url)
        . '" data-layout-edit-modal="' . site_escape($modal) . '"></span>'
        . $markup . '<span hidden id="' . $id . '"></span>';
}

function site_mark_editable_snippet(string $name, string $markup, array $context): string
{
    if (empty($context['layoutEdit']) || !isset($context['snippetHost'])
        || trim($markup) === '' || !site_valid_snippet($name)) return $markup;
    $item = ['kind' => 'snippet', 'arguments' => ['snippet' => $name]];
    $target = site_inline_data_target($item, $context['base'], $context['snippetHost'], []);
    if (!$target || strpos($target['modal'], '?snippets=1&') === false) return $markup;

    $id = 'edit-snippet-' . bin2hex(random_bytes(6));
    return '<span hidden data-snippet-edit-start="' . $id . '" data-snippet-edit-name="' . site_escape($name)
        . '" data-snippet-edit-url="' . site_escape($target['modal']) . '"></span>'
        . $markup . '<span hidden id="' . $id . '"></span>';
}

function site_inline_components(string $html, array $extensions, array $snippets): array
{
    preg_match_all(site_content_pattern($extensions), $html, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
    $items = [];
    foreach ($matches as $match) {
        $flat = array_map(static function (array $entry): string { return $entry[0]; }, $match);
        if (!empty($flat['sizeTag'])) $flat['snippet'] = $flat['sizeTag'];
        $type = site_inline_component_type($flat, $extensions, $snippets);
        if ($type !== '') {
            list($sizeKind, $options) = site_component_match_size($flat);
            $items[] = ['code' => $match[0][0], 'offset' => $match[0][1], 'kind' => $type,
                'options' => $options, 'sizeKind' => $sizeKind,
                'arguments' => $flat];
        }
    }
    return $items;
}

function site_inline_editor_fields(array $item): array
{
    $kind = $item['kind'];
    $arg = $item['arguments'];
    if ($kind === 'route') {
        $options = site_shortcode_options($arg['mapOptions'] ?? '');
        $names = ['provider' => 'Провайдер', 'button' => 'Текст кнопки'];
    } elseif ($kind === 'maplist' || $kind === 'points') {
        $options = site_shortcode_options($arg['sectionMapOptions'] ?? '');
        $options['section'] = trim($arg['mapSection'] ?? '');
        $names = ['section' => 'Раздел'];
        if ($kind === 'maplist') $names += ['zoom' => 'Масштаб', 'provider' => 'Провайдер', 'mode' => 'Режим', 'center' => 'Центр карты'];
        if ($kind === 'maplist' && !isset($options['center']) && isset($options['center_lat'], $options['center_lng'])) {
            $options['center'] = $options['center_lat'] . ',' . $options['center_lng'];
        }
    } elseif (in_array($kind, ['map', 'maps', 'callback'], true)) {
        $options = site_shortcode_options($arg[$kind === 'callback' ? 'callbackOptions' : 'mapOptions'] ?? '');
        if ($kind === 'callback' && !empty($arg['callbackLabel'])) {
            $options['button'] = trim($arg['callbackLabel']);
        }
        $mapFields = ['center' => 'Центр карты', 'zoom' => 'Масштаб', 'provider' => 'Провайдер', 'mode' => 'Режим'];
        if (!isset($options['center']) && isset($options['center_lat'], $options['center_lng'])) {
            $options['center'] = $options['center_lat'] . ',' . $options['center_lng'];
        }
        $names = $kind !== 'callback' ? ($kind === 'maps' ? $mapFields + ['source' => 'Инфоблок'] : ['lat' => 'Широта', 'lng' => 'Долгота', 'title' => 'Название точки'] + $mapFields)
            : ['button' => 'Текст кнопки', 'title' => 'Заголовок формы', 'submit' => 'Текст кнопки отправки', 'placeholder' => 'Подсказка в поле телефона'];
    } elseif ($kind === 'iblock') {
        $names = ['template' => 'Вид инфоблока', 'path' => 'Путь'];
        $options = ['template' => $arg['iblockTemplate'], 'path' => trim($arg['iblock'])];
        if (strtolower($arg['iblockTemplate']) === 'charts') {
            $names += ['type' => 'Тип графика', 'labels' => 'Столбец подписей', 'values' => 'Столбцы значений', 'title' => 'Заголовок графика'];
            $options += site_shortcode_options($arg['iblockOptions'] ?? '');
        }
        if (in_array(strtolower($arg['iblockTemplate']), ['portfolio', 'reviews'], true)) {
            $names['limit'] = 'Количество записей';
            $options += site_shortcode_options($arg['iblockOptions'] ?? '');
        }
    } elseif (in_array($kind, ['include', 'gal', 'fotos', 'files'], true)) {
        $names = ['path' => 'Путь'];
        $options = ['path' => trim($arg[in_array($kind, ['gal', 'fotos'], true) ? 'gallery' : $kind])];
    } elseif (in_array($kind, ['form', 'phpModule', 'snippet', 'iconName'], true)) {
        $names = ['code' => 'Код'];
        $options = ['code' => $arg[$kind]];
        if ($kind === 'iconName') {
            $names['style'] = 'Стиль';
            $options['style'] = $arg['iconStyle'] ?? '';
        }
    } elseif ($kind === 'extensionTag') {
        $names = ['code' => 'Код', 'value' => 'Значение'];
        $options = ['code' => $arg['extensionTag'], 'value' => $arg['extensionArg']];
    } else {
        return [];
    }
    $fields = [];
    foreach ($names as $name => $label) {
        $fields[$name] = [$label, $options[$name] ?? ''];
    }
    return $fields;
}
