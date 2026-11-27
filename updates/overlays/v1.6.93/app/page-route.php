<?php
define('SITE_ROOT', dirname(__DIR__));
require __DIR__ . '/site.php';

$base = rtrim(site_base('app/page-route.php'), '/');
$request = explode('?', $_SERVER['REQUEST_URI'] ?? '', 2)[0];
if ($request === '' || strpos($request, $base . '/') !== 0) {
    http_response_code(404);
    exit;
}
$catalogRequest = site_shop_request($base, $request);
if ($catalogRequest !== null) {
    $shopRouteBase = $base;
    $shopRoutePath = $catalogRequest['path'];
    $shopShortProduct = $catalogRequest['shortProduct'];
    require site_shop_directory() . '/index.php';
    return;
}
$path = site_theme_request_path($base, $request);
$plainFolder = substr($request, -1) !== '/' && substr($request, -4) !== '.php';
if (!site_valid_path($path)) $path = 'error/404/index.php';
if ($plainFolder && site_content_root() !== SITE_ROOT) {
    $page = site_localized_page($path, site_public_language($base, ''));
    if ($page !== null && in_array($_SERVER['REQUEST_METHOD'] ?? 'GET', ['GET', 'HEAD'], true)) {
        $query = $_SERVER['QUERY_STRING'] ?? '';
        header('Location: ' . site_page_url($base, $path) . ($query === '' ? '' : '?' . $query), true, 301);
        exit;
    }
}
if (site_content_root() === SITE_ROOT) $path = 'error/404/index.php';
unset($_SERVER['PATH_INFO']);
site_render(SITE_ROOT . '/' . $path, $base);
