<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}

const SITE_DESIGN_FILE = SITE_ROOT . '/admin/.design-theme.php';
const SITE_DESIGN_ROOT = SITE_ROOT . '/templates/themes';
const SITE_DESIGN_DEMO_VERSION = '1.0.0';

function site_design_id(string $id): bool
{
    return $id !== 'system' && site_valid_part($id) && preg_match('/\A[a-z0-9_-]+\z/', $id) === 1;
}

function site_design_directory(string $id): string
{
    if (!site_design_id($id)) return '';
    $directory = SITE_DESIGN_ROOT . '/' . $id;
    return is_dir($directory) && realpath($directory) === str_replace('/', DIRECTORY_SEPARATOR, $directory) ? $directory : '';
}

function site_design_active(): string
{
    static $id;
    if ($id !== null) return $id;
    try {
        $value = site_read_record(SITE_DESIGN_FILE)['theme'] ?? '';
        $id = is_string($value) && site_design_directory($value) !== '' ? $value : '';
    } catch (RuntimeException $exception) {
        $id = '';
    }
    return $id;
}

function site_design_content(string $id): string
{
    $theme = site_design_directory($id);
    if ($theme === '') return '';
    $root = $theme . '/content';
    foreach (['pages', 'data', 'settings'] as $directory) {
        $path = $root . '/' . $directory;
        if (realpath($path) !== str_replace('/', DIRECTORY_SEPARATOR, $path) || !is_dir($path)) return '';
    }
    $home = $root . '/pages/index.php';
    return is_file($home) && realpath($home) === str_replace('/', DIRECTORY_SEPARATOR, $home) ? $root : '';
}

function site_content_root(): string
{
    $content = site_design_content(site_design_active());
    return $content === '' ? SITE_ROOT : $content . '/pages';
}

function site_content_record(string $shared): string
{
    $content = site_design_content(site_design_active());
    return $content === '' ? $shared : $content . '/settings/' . ltrim(basename($shared), '.');
}

function site_content_data_directory(): string
{
    $content = site_design_content(site_design_active());
    return $content === '' ? SITE_ROOT . '/data' : $content . '/data';
}

function site_theme_request_path(string $base, string $request): string
{
    $prefix = rtrim($base, '/') . '/';
    if (strpos($request, $prefix) !== 0) return '';
    $relative = rawurldecode(substr($request, strlen($prefix)));
    if ($relative === '' || substr($relative, -1) === '/') return $relative . 'index.php';
    if (preg_match('~\A(?:[A-Za-z0-9_-]+/)*[A-Za-z0-9_-]+\z~', $relative)) return $relative . '/index.php';
    return $relative;
}

function site_design_info(string $id): array
{
    $file = site_design_file($id, 'theme-info.php');
    if ($file === '') return [];
    try {
        return site_read_record($file);
    } catch (RuntimeException $exception) {
        return [];
    }
}

function site_design_name(string $id): string
{
    $name = site_design_info($id)['name'] ?? '';
    return is_string($name) && $name !== '' ? $name : $id;
}

function site_design_file(string $id, string $path): string
{
    $directory = site_design_directory($id);
    if ($directory === '' || !preg_match('~\A(?:[a-z0-9_-]+/)*[a-z0-9_-]+\.[a-z0-9]+\z~i', $path)) return '';
    $file = $directory . '/' . $path;
    return is_file($file) && realpath($file) === str_replace('/', DIRECTORY_SEPARATOR, $file) ? $file : '';
}

function site_design_asset(string $base, string $path): string
{
    $id = site_design_active();
    $file = site_design_file($id, $path);
    if ($file === '' || !preg_match('/\.(?:css|js|png|jpe?g|webp|gif|avif|ico|svg|woff2?)\z/i', $path)) return '';
    return site_url($base, 'templates/themes/' . $id . '/' . $path) . '?v=' . substr((string) hash_file('sha256', $file), 0, 16);
}

function site_design_layout_file(string $id, string $host = ''): string
{
    $directory = site_design_directory($id);
    if ($directory === '') throw new RuntimeException('Тема дизайна недоступна.');
    return $directory . '/.layouts' . ($host === '' ? '' : '-' . hash('sha256', $host)) . '.php';
}

function site_layout_definitions_file(): string
{
    $directory = site_design_directory(site_design_active());
    return $directory === '' ? SITE_LAYOUT_DEFINITIONS : $directory . '/.layout-definitions.php';
}

function site_read_layout_snippets(string $host = ''): array
{
    $theme = site_design_active();
    return $theme === '' ? site_read_snippets($host)
        : site_read_record(site_design_layout_file($theme, $host), SITE_SNIPPET_RECORD_LIMIT);
}

function site_design_labels(string $language): array
{
    static $labels;
    if ($labels === null) $labels = require __DIR__ . '/help-design-themes.php';
    return $labels[$language] ?? $labels['ru'];
}
