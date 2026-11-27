<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}

const SITE_LIBRARIES_FILE = SITE_ROOT . '/admin/.libraries.php';
const SITE_LIBRARY_VERSIONS_FILE = SITE_ROOT . '/admin/.library-versions.php';

function site_library_catalog(): array
{
    $cdn = 'https://cdn.jsdelivr.net/npm/';
    return [
        'jquery' => ['label' => 'jQuery 4.1.0', 'hint' => 'Работа с DOM, событиями и плагинами.',
            'js' => ['https://code.jquery.com/jquery-4.1.0.min.js', 'UGSi5euAzE1p4sK41+yKs3vtdPcidmaW6fY2NUITZkjIPyMVUUYB/BV4A5Oa8uu2']],
        'bootstrap' => ['label' => 'Bootstrap 5.3.8', 'hint' => 'Сетка, кнопки, модальные окна и другие компоненты.',
            'css' => [$cdn . 'bootstrap@5.3.8/dist/css/bootstrap.min.css', 'sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB'],
            'js' => [$cdn . 'bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js', 'FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI']],
        'fontawesome' => ['label' => 'Font Awesome Free 7.3.1', 'hint' => 'Локальные иконки для классов fa-* и кода [fa-icon=house].'],
        'mui' => ['label' => 'Material UI 9.5.0', 'hint' => 'Компоненты React. Доступны через import из @mui/material.'],
        'alpine' => ['label' => 'Alpine.js 3.17.5', 'hint' => 'Интерактивные элементы через атрибуты x-data и x-on.',
            'js' => [$cdn . 'alpinejs@3.17.5/dist/cdn.min.js', 'FYLKjwG3aohNhNZoVG8sY/5ntunm+MqylL90Q+rT3OM7AwV9/BKFjk/RjoIpIgsI']],
        'htmx' => ['label' => 'htmx 2.0.11', 'hint' => 'Частичная загрузка страниц через атрибуты hx-get и hx-target.',
            'js' => [$cdn . 'htmx.org@2.0.11/dist/htmx.min.js', '2OatzQy1H+Zd/IIrjr1TcuDGqLXeHhbooAyJY1KdQMKnr4LZ22k31GBLdYKHmVjg']],
        'chartjs' => ['label' => 'Chart.js 4.6.1', 'hint' => 'Графики и диаграммы.',
            'js' => [$cdn . 'chart.js@4.6.1/dist/chart.umd.min.js', 'Emf5ZVIUiarAdhkgtqK+5/Z5qIvM6QoVrS+qAVtfyZVuD5lIHrac0me485+Antp0']],
        'glightbox' => ['label' => 'GLightbox 3.3.1', 'hint' => 'Просмотр фотографий и видео в окне поверх страницы.',
            'css' => [$cdn . 'glightbox@3.3.1/dist/css/glightbox.min.css', 'GPAzSuZc0kFvdIev6wm9zg8gnafE8tLso7rsAYQfc9hAdWCpOcpcNI5W9lWkYcsd'],
            'js' => [$cdn . 'glightbox@3.3.1/dist/js/glightbox.min.js', 'MZZbZ6RXJudK43v1qY1zOWKOU2yfeBPatuFoKyHAaAgHTUZhwblRTc9CphTt4IGQ']],
        'codemirror' => ['label' => 'CodeMirror 5.65.21', 'hint' => 'Подсветка PHP, CSS, JS и HTML в админке и на панели правки. Есть режимы «Авто» и RAW.'],
    ];
}

function site_code_editor_on(): bool
{
    try {
        return site_library_settings(site_read_record(SITE_LIBRARIES_FILE))['enabled']['codemirror'];
    } catch (RuntimeException $exception) {
        return false;
    }
}

function site_library_versions(): array
{
    try {
        return site_read_record(SITE_LIBRARY_VERSIONS_FILE);
    } catch (RuntimeException $exception) {
        return [];
    }
}

function site_active_libraries(): array
{
    $catalog = site_library_catalog();
    $versions = site_library_versions();
    foreach ($catalog as $key => $library) {
        if (is_array($versions[$key]['active'] ?? null)) {
            $catalog[$key] = $versions[$key]['active'];
        }
    }
    return $catalog;
}

function site_library_rollback(string $id, array $active, array $versions): array
{
    $baseline = require __DIR__ . '/library-baselines.php';
    $previous = $versions[$id]['previous'] ?? ($baseline[$id] ?? []);
    return is_array($previous) && ($previous['label'] ?? '') !== $active['label'] ? $previous : [];
}

function site_code_editor_url(string $base): string
{
    $label = site_active_libraries()['codemirror']['label'];
    return site_url($base, 'templates/code-editor.php') . '?v=' . rawurlencode(substr($label, strlen('CodeMirror ')));
}

function site_google_fonts(): array
{
    return [
        'andika' => ['Andika', '400;700', 'sans-serif'],
        'arimo' => ['Arimo', '400;700', 'sans-serif'],
        'bad-script' => ['Bad Script', '', 'cursive'],
        'comfortaa' => ['Comfortaa', '400;700', 'sans-serif'],
        'didact-gothic' => ['Didact Gothic', '', 'sans-serif'],
        'golos' => ['Golos Text', '400;500;600;700', 'sans-serif'],
        'lobster' => ['Lobster', '', 'cursive'],
        'manrope' => ['Manrope', '400;500;600;700', 'sans-serif'],
        'marck-script' => ['Marck Script', '', 'cursive'],
        'inter' => ['Inter', '400;500;600;700', 'sans-serif'],
        'montserrat' => ['Montserrat', '400;500;600;700', 'sans-serif'],
        'noto-sans' => ['Noto Sans', '400;700', 'sans-serif'],
        'open-sans' => ['Open Sans', '400;700', 'sans-serif'],
        'open-sans-condensed' => ['Open Sans Condensed', '', 'sans-serif', 'Open Sans', 'wdth,wght@75,400;75,700'],
        'philosopher' => ['Philosopher', '400;700', 'serif'],
        'play' => ['Play', '400;700', 'sans-serif'],
        'poiret-one' => ['Poiret One', '', 'sans-serif'],
        'pt-sans' => ['PT Sans', '400;700', 'sans-serif'],
        'pt-sans-narrow' => ['PT Sans Narrow', '400;700', 'sans-serif'],
        'raleway' => ['Raleway', '400;500;600;700', 'sans-serif'],
        'pt-serif' => ['PT Serif', '400;700', 'serif'],
        'lora' => ['Lora', '400;500;600;700', 'serif'],
        'playfair' => ['Playfair Display', '400;500;600;700', 'serif'],
        'roboto-slab' => ['Roboto Slab', '400;500;600;700', 'serif'],
        'roboto-condensed' => ['Roboto Condensed', '400;500;600;700', 'sans-serif'],
        'roboto' => ['Roboto', '400;700', 'sans-serif'],
        'rubik' => ['Rubik', '400;700', 'sans-serif'],
        'ruslan-display' => ['Ruslan Display', '', 'serif'],
        'russo-one' => ['Russo One', '', 'sans-serif'],
        'tenor-sans' => ['Tenor Sans', '', 'sans-serif'],
        'ubuntu' => ['Ubuntu', '400;700', 'sans-serif'],
        'ubuntu-condensed' => ['Ubuntu Condensed', '', 'sans-serif'],
        'ubuntu-mono' => ['Ubuntu Mono', '400;700', 'monospace'],
        'yeseva' => ['Yeseva One', '', 'serif'],
    ];
}

function site_library_settings(array $stored = []): array
{
    $enabled = [];
    foreach (site_library_catalog() as $key => $library) {
        $enabled[$key] = ($stored['enabled'][$key] ?? in_array($key, ['fontawesome', 'codemirror', 'chartjs'], true)) === true;
    }
    $font = $stored['font'] ?? '';
    return ['enabled' => $enabled, 'font' => is_string($font) && isset(site_google_fonts()[$font]) ? $font : ''];
}

function site_material_imports(array $library = []): array
{
    $cdn = 'https://esm.sh/';
    $react = $library['react'] ?? '19.3.1';
    $mui = $library['mui'] ?? '9.5.0';
    return ['imports' => [
        'react' => $cdn . 'react@' . $react,
        'react/jsx-runtime' => $cdn . 'react@' . $react . '/jsx-runtime',
        'react-dom' => $cdn . 'react-dom@' . $react . '?external=react',
        'react-dom/client' => $cdn . 'react-dom@' . $react . '/client?external=react',
        '@mui/material' => $cdn . '@mui/material@' . $mui . '?external=react,react-dom',
    ]];
}

function site_library_bundled(string $id, array $library): bool
{
    return $id === 'codemirror' || ($id === 'fontawesome' && !isset($library['css']));
}

function site_local_library_pack(string $id, array $library): string
{
    return $id . '-' . substr(hash('sha256', ($id === 'mui' ? 'local-esm-v1:' : '') . json_encode($library)), 0, 16);
}

function site_local_library_file(string $pack, string $name): string
{
    if (!preg_match('/\A[a-z]+-[a-f0-9]{16}\z/', $pack)
        || ($name !== 'manifest.json' && !preg_match('/\A[a-f0-9]{64}\.(?:js|css)\z/', $name))) return '';
    $file = SITE_ROOT . '/templates/vendor/local/' . $pack . '/' . $name;
    return is_file($file) && realpath($file) === str_replace('/', DIRECTORY_SEPARATOR, $file) ? $file : '';
}

function site_local_library(string $id, array $library): array
{
    $pack = site_local_library_pack($id, $library);
    $file = site_local_library_file($pack, 'manifest.json');
    if ($file === '') return [];
    $saved = json_decode((string) file_get_contents($file, false, null, 0, 32769), true);
    if (!is_array($saved)) return [];
    foreach (['css', 'js'] as $type) {
        if (isset($library[$type]) && (!is_string($saved[$type] ?? null) || site_local_library_file($pack, $saved[$type]) === '')) return [];
    }
    if ($id === 'mui') {
        foreach (site_material_imports($library)['imports'] as $name => $value) {
            if (!is_string($saved['imports'][$name] ?? null) || site_local_library_file($pack, $saved['imports'][$name]) === '') return [];
        }
    }
    return $saved;
}

function site_local_library_url(string $base, string $pack, string $name): string
{
    return site_url($base, 'templates/local-library.php') . '?' . http_build_query(['pack' => $pack, 'file' => $name]);
}

function site_library_assets(string $id, array $library, string $base): string
{
    $html = '';
    $local = site_local_library($id, $library);
    $pack = site_local_library_pack($id, $library);
    foreach (['css', 'js'] as $type) {
        if (isset($library[$type])) {
            $integrity = ' integrity="sha384-' . site_escape($library[$type][1]) . '" crossorigin="anonymous"';
            $url = site_escape(isset($local[$type]) ? site_local_library_url($base, $pack, $local[$type]) : $library[$type][0]);
            $html .= $type === 'css' ? '<link rel="stylesheet" href="' . $url . '"' . $integrity . ">\n"
                : '<script src="' . $url . '"' . $integrity . " defer></script>\n";
        }
    }
    if ($id === 'fontawesome' && !isset($library['css'])) {
        $html .= '<link rel="stylesheet" href="' . site_escape(site_font_awesome_stylesheet($base)) . "\">\n";
    } elseif ($id === 'mui') {
        $imports = site_material_imports($library);
        if ($local) {
            foreach ($imports['imports'] as $name => &$url) $url = site_local_library_url($base, $pack, $local['imports'][$name]);
            unset($url);
        }
        $html .= '<script type="importmap">' . json_encode($imports, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) . "</script>\n";
    } elseif ($id === 'codemirror') {
        $html .= '<script src="' . site_escape(site_code_editor_url($base)) . '" defer></script>' . "\n";
    }
    return $html;
}

function site_library_example(string $id): string
{
    $examples = [
        'jquery' => '<script>document.addEventListener("DOMContentLoaded", () => jQuery(".example").addClass("ready"));</script>',
        'bootstrap' => '<button class="btn btn-primary">Example</button>',
        'fontawesome' => '<i class="fa-solid fa-star" aria-hidden="true"></i> [fa-icon=star]',
        'mui' => '<script type="module">import {Button} from "@mui/material"; console.log(Button);</script>',
        'alpine' => '<div x-data="{count: 0}"><button @click="count++" x-text="count"></button></div>',
        'htmx' => '<button hx-get="/contacts/" hx-target="#result" hx-select="#content">Open</button><div id="result"></div>',
        'chartjs' => '<script>document.addEventListener("DOMContentLoaded", () => new Chart(document.querySelector("canvas"), {type: "bar", data: {labels: ["A"], datasets: [{data: [3]}]}}));</script><canvas></canvas>',
        'glightbox' => '<a href="/images/photo.jpg" class="glightbox">Open photo</a><script>document.addEventListener("DOMContentLoaded", () => GLightbox());</script>',
        'codemirror' => '<textarea data-code-mode="html"></textarea>',
    ];
    return $examples[$id] ?? '';
}
