<?php
if (!defined('SITE_ROOT')) {
    define('SITE_ROOT', dirname(__DIR__));
    require SITE_ROOT . '/app/site.php';
}
if (!site_shop_enabled()) { http_response_code(404); exit; }
require_once __DIR__ . '/module.php';

$base = $shopRouteBase ?? site_base('shop/index.php');
$path = $shopRoutePath ?? '';
$image = shop_input($_GET, 'image');
if ($image !== '') {
    $file = __DIR__ . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . $image;
    if (!shop_image_name($image) || !is_file($file) || realpath($file) !== $file) { http_response_code(404); exit; }
    $mime = ['jpg'=>'image/jpeg','jpeg'=>'image/jpeg','png'=>'image/png','webp'=>'image/webp','avif'=>'image/avif','gif'=>'image/gif'];
    header('Content-Type: ' . $mime[strtolower(pathinfo($file, PATHINFO_EXTENSION))]);
    header('X-Content-Type-Options: nosniff');
    header('Cache-Control: public, max-age=3600');
    readfile($file);
    return;
}
if (shop_input($_GET, 'asset') === 'css') {
    header('Content-Type: text/css; charset=UTF-8');
    header('X-Content-Type-Options: nosniff');
    header('Cache-Control: public, max-age=3600');
    readfile(__DIR__ . '/shop.css');
    return;
}
header('Content-Type: text/html; charset=UTF-8');
header('Cache-Control: private, no-store');
header('X-Content-Type-Options: nosniff');
header("Content-Security-Policy: default-src 'none'; style-src 'self'; img-src 'self' https:; font-src 'self'; form-action 'self'; base-uri 'none'; frame-ancestors 'none'");
require_once SITE_ROOT . '/admin/files.php';
if (!site_public_session($base)) { http_response_code(503); exit; }
$locale = site_public_language($base, '');
$index = shop_index();
$route = shop_current_route($index, $path);
if ($route === null) {
    $slug = strpos($path, 'product/') === 0 ? substr($path, 8) : '';
    $id = $index['aliases'][$slug] ?? null;
    session_write_close();
    if ($id !== null && ($index['products'][$id]['active'] ?? '') === '1') {
        header('Location: ' . shop_route_url($base, 'product/' . $index['products'][$id]['slug']), true, 301);
        exit;
    }
    http_response_code(404);
    exit;
}
$settings = shop_settings();
$staff = ($_GET['staff'] ?? '') === '1';
$roles = explode(',', shop_user()['roles'] ?? '');
if ($staff && !array_intersect(['catalog','orders'], $roles)) { http_response_code(403); session_write_close(); exit; }
if ($staff) {
    require_once __DIR__ . '/admin.php';
    require_once __DIR__ . '/import.php';
    $moduleAdminUrl = shop_endpoint($base, ['staff'=>1]);
    $moduleToken = shop_token();
    $moduleOwner = false;
}
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    try {
        if ($staff) {
            if (!hash_equals($moduleToken, shop_input($_POST, 'csrf'))) throw shop_error('session');
            shop_admin_action($_POST, $_FILES, false, $roles);
            $_SESSION['shopNotice'] = shop_label('saved');
            $redirect = $moduleAdminUrl . '&' . http_build_query(array_intersect_key($_GET, array_flip(['tab','sheet'])));
        } else $redirect = shop_submit($base, $_POST);
    } catch (RuntimeException $error) {
        $_SESSION['shopNotice'] = $error->getMessage();
        $redirect = $staff ? $moduleAdminUrl : shop_return($base, shop_input($_POST, 'return'));
    }
    session_write_close();
    header('Location: ' . $redirect, true, 303);
    return;
}
$state = shop_session($base);
$notice = $_SESSION['shopNotice'] ?? '';
unset($_SESSION['shopNotice']);
session_write_close();
$context = ['base'=>$base, 'shopRoute'=>$route, 'shopUrl'=>shop_route_url($base, $path)];
$view = shop_input($_GET, 'view');
$title = shop_label('catalog', $locale);
if ($staff) { $title = shop_label('staff', $locale); ob_start(); require __DIR__ . '/admin-view.php'; $markup = ob_get_clean(); }
elseif ($view === 'account') { header('Location: ' . site_url($base) . '?account=1', true, 302); return; }
elseif (in_array($view, ['cart','checkout'], true)) {
    $title = shop_label($view === 'cart' ? 'basket' : 'checkout', $locale);
    $markup = shop_component($view, [], $context);
} elseif ($route['product'] !== '') {
    $title = $index['products'][$route['product']]['name'];
    header_remove('Content-Security-Policy');
    site_render(SITE_ROOT . '/index.php', $base, [
        'title'=>$title, 'description'=>$index['products'][$route['product']]['summary'],
        'html'=>'[shop-breadcrumbs][shop-product]', 'context'=>$context,
    ]);
    return;
} else {
    if ($route['category'] !== '') $title = $index['categories'][$route['category']]['name'];
    if ($route['brand'] !== '') $title .= ' · ' . implode(' / ', array_keys($index['brands'][$route['brand']]));
    $markup = shop_component('breadcrumbs', [], $context) . shop_component('categories', [], $context);
    if ($route['category'] !== '' && $route['brand'] === '') {
        $brands = [];
        $group = $index['categories'][$route['category']]['descendants'] === '1' ? 'lists' : 'local';
        $members = array_fill_keys($index[$group][$route['category']] ?? [], true);
        foreach ($index['brands'] as $slug => $values) {
            foreach ($values as $name => $enabled) {
                foreach ($index['lookup']['brand'][$name] ?? [] as $offer) {
                    if (isset($members[$offer])) { $brands[$slug] = (string) $name; break 2; }
                }
            }
        }
        foreach ($brands as $slug => $name) {
            $markup .= '<a class="button" href="' . site_escape($context['shopUrl'] . $slug . '/') . '">' . site_escape($name) . '</a> ';
        }
    }
    $markup .= shop_component('search', [], $context) . shop_component('filters', [], $context)
        . shop_component('sort', [], $context) . shop_component('items', [], $context);
}
echo '<!doctype html><html lang="' . site_escape($locale) . '"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">'
    . '<title>' . site_escape($title) . '</title><link rel="stylesheet" href="' . site_escape(site_url($base,'templates/css/site.css')) . '">'
    . '<link rel="stylesheet" href="' . site_escape(shop_endpoint($base, ['asset'=>'css','v'=>site_shop_version()])) . '"></head><body>';
echo shop_template('page', ['home'=>'<a class="button" href="' . site_escape(site_url($base)) . '">' . site_escape(shop_label('home')) . '</a>',
    'basket'=>shop_component('basket', [], $context),
    'account'=>'<a class="button" href="' . site_escape(site_url($base) . '?account=1') . '">' . site_escape(shop_label('account')) . '</a>',
    'title'=>site_escape($title), 'notice'=>$notice === '' ? '' : '<p class="notice" role="status">' . site_escape($notice) . '</p>',
    'content'=>$markup], $settings);
echo '</body></html>';
