<?php
if (!defined('SITE_ROOT')) { http_response_code(404); exit; }
if (!site_shop_enabled()) { http_response_code(404); exit; }
require_once __DIR__ . '/module.php';

$base = $shopRouteBase ?? site_base('shop/index.php');
$path = $shopRoutePath ?? '';
$shortProduct = $shopShortProduct ?? false;
if ($path === '' && isset($_GET['feed'])) {
    header('Cache-Control: private, no-store');
    header('Referrer-Policy: no-referrer');
    $method = $_SERVER['REQUEST_METHOD'] ?? '';
    if (!in_array($method, ['GET','HEAD'], true)) { http_response_code(405); header('Allow: GET, HEAD'); return; }
    require_once __DIR__ . '/export.php';
    $format = shop_input($_GET, 'feed');
    $key = isset(SHOP_EXPORT_FORMATS[$format]) ? shop_export_key(shop_settings(), $format) : '';
    if ($key === '' || !hash_equals($key, shop_input($_GET, 'key'))) { http_response_code(404); return; }
    if ($format === 'xlsx' && !class_exists('ZipArchive')) { http_response_code(503); return; }
    if ($method === 'HEAD') { shop_export_headers($format, true); return; }
    try {
        shop_export_send($base, $format, '', true);
    } catch (RuntimeException $error) {
        http_response_code(503);
    }
    return;
}
$image = shop_input($_GET, 'image');
if ($image !== '') {
    $file = str_replace('/', DIRECTORY_SEPARATOR, SHOP_IMAGES_DIR) . DIRECTORY_SEPARATOR . $image;
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
$route = shop_current_route($index, $path, $shortProduct);
if ($route === null) {
    session_write_close();
    $oldPath = ($shortProduct ? 'product' : site_shop_public_folder()) . '/' . $path;
    $product = $index['products'][$index['aliases'][$oldPath] ?? ''] ?? null;
    if ($product && $product['active'] === '1' && in_array($_SERVER['REQUEST_METHOD'] ?? 'GET', ['GET','HEAD'], true)) {
        header('Location: ' . shop_product_url($base, $index, $product), true, 301);
        return;
    }
    http_response_code(404);
    exit;
}
$brand = shop_input($_GET, 'b');
if ($route['product'] === '' && isset($index['brands'][$brand])) $route['brand'] = $brand;
$catalogFolder = site_shop_public_folder();
$settings = shop_settings();
$staff = ($_GET['staff'] ?? '') === '1';
$view = shop_input($_GET, 'view');
$shopCartJson = !$staff && strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false;
$roles = explode(',', shop_user()['roles'] ?? '');
if ($staff && !array_intersect(['catalog','orders'], $roles)) { http_response_code(403); session_write_close(); exit; }
if ($staff) {
    require_once __DIR__ . '/admin.php';
    require_once __DIR__ . '/import.php';
    $moduleAdminUrl = shop_endpoint($base, ['staff'=>1]);
    $moduleToken = shop_token();
    $moduleOwner = false;
}
if ($staff && isset($_GET['print_order'])) {
    if (!in_array('orders', $roles, true)) { http_response_code(403); return; }
    $order = shop_order(shop_input($_GET, 'print_order'));
    if (!$order) { http_response_code(404); return; }
    header("Content-Security-Policy: default-src 'none'; style-src 'self'; script-src 'self'; base-uri 'none'; frame-ancestors 'none'");
    require __DIR__ . '/order-print.php';
    return;
}
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $ajaxCartAction = $shopCartJson && in_array(shop_input($_POST, 'action'), ['add','quantity'], true);
    $cartStatus = 200;
    try {
        if ($staff) {
            if (!hash_equals($moduleToken, shop_input($_POST, 'csrf'))) throw shop_error('session');
            shop_admin_action($_POST, $_FILES, false, $roles);
            $_SESSION['shopNotice'] = shop_label('saved');
            $staffQuery = array_intersect_key($_GET, array_flip(['tab','sheet','p','q','order_status']));
            if (shop_input($_POST, 'action') === 'shop-status') $staffQuery['open_order'] = shop_input($_POST, 'order');
            $redirect = $moduleAdminUrl . '&' . http_build_query($staffQuery);
        } else $redirect = shop_submit($base, $_POST);
    } catch (RuntimeException $error) {
        $_SESSION['shopNotice'] = $error->getMessage();
        $cartStatus = 422;
        $redirect = $staff ? $moduleAdminUrl : shop_return($base, shop_input($_POST, 'return'));
    }
    if ($ajaxCartAction) {
        $message = $_SESSION['shopNotice'] ?? '';
        unset($_SESSION['shopNotice']);
        shop_cart_json($base, $settings, $index, $message, $cartStatus);
        return;
    }
    session_write_close();
    header('Location: ' . $redirect, true, 303);
    return;
}
if ($shopCartJson && $view === 'cart') {
    shop_cart_json($base, $settings, $index);
    return;
}
$state = shop_session($base);
$notice = $_SESSION['shopNotice'] ?? '';
unset($_SESSION['shopNotice']);
if (!$staff && $view === 'quick') {
    $offerId = shop_input($_GET, 'offer');
    $offer = $index['offers'][$offerId] ?? null;
    $product = $index['products'][$offer['product_id'] ?? ''] ?? null;
    $done = shop_input($_GET, 'done');
    $confirmed = $done !== '' && hash_equals($_SESSION['shopLastQuickOrder'][$offerId] ?? '', $done);
    if (!$confirmed && (!$offer || !$product || ($product['quick_pickup'] ?? '') !== '1'
        || $offer['active'] !== '1' || $product['active'] !== '1')) {
        session_write_close();
        http_response_code(404);
        return;
    }
    $nonce = $confirmed ? '' : ($_SESSION['shopQuickCheckout'][$offerId] ?? bin2hex(random_bytes(12)));
    if (!$confirmed) $_SESSION['shopQuickCheckout'] = array_slice([$offerId=>$nonce] + ($_SESSION['shopQuickCheckout'] ?? []), 0, 12, true);
    session_write_close();
    $title = shop_label($confirmed ? 'order_received' : 'checkout', $locale);
    $noticeHtml = $notice === '' ? '' : '<p class="notice error" role="alert">' . site_escape($notice) . '</p>';
    $markup = $confirmed
        ? '<p class="notice" role="status">' . site_escape(shop_label('order_received') . ' #' . substr($done, 0, 12)) . '</p>'
        : shop_quick_checkout_html($base, $index, $settings, $state, $offer, $product, $nonce);
    $reservation = '';
    if ($confirmed && ($order = shop_order($done)) !== null) {
        $expiry = shop_order_hold_expires($order);
        if ($expiry > time() && (shop_order_states()[$order['head'][2]]['phase'] ?? '') === 'reserved') {
            $reservation = ' data-reserved="' . site_escape(shop_label('reserved')) . '"';
            $markup .= '<p>' . site_escape(shop_label('reserve_until') . ' ' . shop_order_date(gmdate('c', $expiry))) . '</p>';
        }
    }
    $panel = '<div data-shop-quick-panel data-title="' . site_escape($title) . '"' . $reservation . '>' . $noticeHtml . $markup . '</div>';
    header_remove('Content-Security-Policy');
    site_render(SITE_ROOT . '/shop/index.php', $base, [
        'title'=>$title, 'html'=>$panel, 'noindex'=>true, 'canonicalPath'=>$catalogFolder . '/index.php',
        'context'=>['base'=>$base,'shopRoute'=>$route,'shopUrl'=>shop_endpoint($base)],
    ]);
    return;
}
session_write_close();
$context = ['base'=>$base, 'shopRoute'=>$route, 'shopUrl'=>$route['product'] === '' ? shop_route_url($base, $path)
    : shop_product_url($base, $index, $index['products'][$route['product']])];
$templateId = in_array($view, ['cart','checkout'], true) ? 'shop-' . $view : ($route['product'] !== '' ? 'shop-product' : 'shop-category');
require_once SITE_ROOT . '/admin/page-templates.php';
try {
    $pageTemplate = admin_marketplace_page_template($templateId);
} catch (RuntimeException $error) {
    $pageTemplate = [];
}
$context['shopPageTemplate'] = $templateId;
$context['shopTemplateAssets'] = $pageTemplate['assets'] ?? [];
$title = shop_label('catalog', $locale);
$noticeHtml = $notice === '' ? '' : '<p class="notice" role="status">' . site_escape($notice) . '</p>';
if ($staff) { $title = shop_label('staff', $locale); ob_start(); require __DIR__ . '/admin-view.php'; $markup = ob_get_clean(); }
elseif ($view === 'account') { header('Location: ' . site_url($base) . '?account=1', true, 302); return; }
elseif (in_array($view, ['cart','checkout'], true)) {
    $title = shop_label($view === 'cart' ? 'basket' : 'checkout', $locale);
    header_remove('Content-Security-Policy');
    site_render(SITE_ROOT . '/shop/index.php', $base, ['title'=>$title,'html'=>$noticeHtml . ($pageTemplate['html'] ?? '[shop-' . $view . ']'),
        'context'=>$context,'canonicalPath'=>$catalogFolder . '/index.php']);
    return;
} elseif ($route['product'] !== '') {
    $product = $index['products'][$route['product']];
    $seo = $index['seo'][$route['product']] ?? [];
    $title = $product['name'];
    $defaultMarkup = admin_marketplace_product_default($settings);
    $context['shopProductMarkup'] = admin_marketplace_product_markup($pageTemplate['html'] ?? $defaultMarkup);
    header_remove('Content-Security-Policy');
    site_render(SITE_ROOT . '/shop/index.php', $base, [
        'title'=>($seo['title'] ?? '') ?: $title, 'heading'=>($seo['heading'] ?? '') ?: $title,
        'description'=>($seo['description'] ?? '') ?: $product['summary'], 'keywords'=>$seo['keywords'] ?? '',
        'noindex'=>($seo['noindex'] ?? '') === '1',
        'html'=>$noticeHtml . '[shop-product]', 'context'=>$context,
        'canonicalPath'=>shop_product_page_path($index, $product),
    ]);
    return;
} else {
    if ($route['category'] !== '') $title = $index['categories'][$route['category']]['name'];
    if ($route['brand'] !== '') $title .= ' · ' . implode(' / ', array_keys($index['brands'][$route['brand']]));
    $theme = site_design_info(site_design_active());
    $themeCategory = $theme['shopCategory'] ?? '';
    $scoped = $path === '' && !empty($theme['siteShop']) && is_string($themeCategory) && shop_id($themeCategory);
    $catalog = $scoped ? '[shop category=' . $themeCategory . ']' : '[shop]';
    if ($path !== '' && isset($pageTemplate['html'])) $catalog = $pageTemplate['html'];
    if ($path === '') {
        $source = site_read_page($settings['catalog'] ?: $catalogFolder . '/index.php') ?? site_read_page('shop/index.php');
        if ($source !== null) {
            $catalog = $source['html'];
            $title = $source['fields']['Heading'] ?: $title;
        }
        unset($context['shopPageTemplate'], $context['shopTemplateAssets']);
    }
    header_remove('Content-Security-Policy');
    site_render(SITE_ROOT . '/shop/index.php', $base, ['title'=>$title,'html'=>$noticeHtml . $catalog,
        'context'=>$context,'canonicalPath'=>$catalogFolder . '/' . ($path === '' ? '' : $path . '/') . 'index.php']);
    return;
}
echo '<!doctype html><html lang="' . site_escape($locale) . '"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">'
    . '<title>' . site_escape($title) . '</title><link rel="stylesheet" href="' . site_escape(site_url($base,'templates/css/site.css')) . '">'
    . '<link rel="stylesheet" href="' . site_escape(shop_endpoint($base, ['asset'=>'css','v'=>site_shop_version()])) . '"></head><body>';
echo shop_template('page', ['home'=>'<a class="button" href="' . site_escape(site_url($base)) . '">' . site_escape(shop_label('home')) . '</a>',
    'basket'=>shop_component('basket', [], $context),
    'account'=>'<a class="button" href="' . site_escape(site_url($base) . '?account=1') . '">' . site_escape(shop_label('account')) . '</a>',
    'title'=>site_escape($title), 'notice'=>$noticeHtml,
    'content'=>$markup], $settings);
echo '</body></html>';
