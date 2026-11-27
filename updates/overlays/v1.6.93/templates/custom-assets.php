<?php
define('SITE_ROOT', dirname(__DIR__));
require SITE_ROOT . '/app/site.php';

$type = $_GET['type'] ?? '';
$path = $_GET['page'] ?? '';
$shopTemplate = $_GET['shop_template'] ?? '';
$types = ['css' => 'text/css', 'js' => 'application/javascript'];
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-cache');
header("Content-Security-Policy: default-src 'none'; sandbox");
if (!is_string($type) || !isset($types[$type]) || !is_string($path) || !is_string($shopTemplate) || !empty($_SERVER['PATH_INFO'])) {
    http_response_code(404);
    exit;
}

try {
    if ($shopTemplate !== '') {
        require_once SITE_ROOT . '/admin/page-templates.php';
        $record = site_shop_widgets_enabled() ? admin_marketplace_page_template($shopTemplate) : [];
        $code = $record['assets'][strtoupper($type)] ?? '';
    } elseif ($path === '') {
        $assets = site_global_assets();
    } else {
        $page = site_read_page($path);
        $assets = $page !== null && $page['fields']['Hidden'] === '0' ? $page['fields'] : [];
    }
    if ($shopTemplate === '') $code = site_asset_decode($assets[strtoupper($type)] ?? '');
} catch (RuntimeException $exception) {
    $code = null;
}
if (!is_string($code) || $code === '') {
    http_response_code(404);
    exit;
}

header('Content-Type: ' . $types[$type] . '; charset=UTF-8');
echo $code;
