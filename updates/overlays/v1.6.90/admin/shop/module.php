<?php
if (!defined('SITE_ROOT')) { http_response_code(404); exit; }

define('SHOP_DIR', !is_dir(__DIR__ . '/data') && is_dir(SITE_ROOT . '/shop/data') ? SITE_ROOT . '/shop/data' : __DIR__ . '/data');
define('SHOP_IMAGES_DIR', !is_dir(__DIR__ . '/images') && is_dir(SITE_ROOT . '/shop/images') ? SITE_ROOT . '/shop/images' : __DIR__ . '/images');
const SHOP_LIMIT = 33554432;
require_once __DIR__ . '/catalog.php';
require_once __DIR__ . '/orders.php';
if (is_file(__DIR__ . '/integrations.php')) require_once __DIR__ . '/integrations.php';
require_once __DIR__ . '/actions.php';
require_once SITE_ROOT . '/app/users.php';
require_once __DIR__ . '/storefront.php';

function shop_settings(): array
{
    $settings = site_read_record(SHOP_DIR . '/settings.php', 262144) + [
        'catalog' => '', 'guest' => true, 'pageSize' => 24, 'shipping' => 0, 'currency' => 'RUB', 'templates' => [],
    ];
    if (!is_string($settings['rounding'] ?? null) || !isset(SHOP_ROUNDING_STEPS[$settings['rounding']])) {
        $settings['rounding'] = site_region_settings()['public'] === 'ru' ? '10' : '0.01';
    }
    return $settings;
}

const SHOP_ROUNDING_STEPS = ['100'=>10000, '10'=>1000, '1'=>100, '0.1'=>10, '0.01'=>1];

function shop_rounded_price(int $value, array $settings): int
{
    if ($value <= 0) return 0;
    $step = SHOP_ROUNDING_STEPS[$settings['rounding']] ?? 1;
    return max($step, intdiv($value + intdiv($step, 2), $step) * $step);
}

function shop_filter_settings(array $settings): array
{
    $saved = is_array($settings['filters'] ?? null) ? $settings['filters'] : [];
    return $saved + ['position'=>'top','price'=>true,'fields'=>true,'search'=>true,'sort'=>true,'live'=>false];
}

function shop_brand_profiles(): array
{
    return site_read_record(SHOP_DIR . '/brands.php', 262144);
}

function shop_locale(): string
{
    if (isset($GLOBALS['adminLanguage'])) return $GLOBALS['adminLanguage'];
    $region = site_region_settings();
    foreach ([$_GET['lang'] ?? '', $_COOKIE['FILE_SITE_LANG'] ?? ''] as $language) {
        if (in_array($language, $region['languages'], true)) return $language;
    }
    return $region['public'];
}

function shop_label(string $key, ?string $language = null): string
{
    static $labels;
    if ($labels === null) $labels = require __DIR__ . '/language.php';
    $language = $language ?? shop_locale();
    return $labels[$language][$key] ?? $labels['en'][$key] ?? $key;
}

function shop_error(string $key, string $detail = ''): RuntimeException
{
    return new RuntimeException(shop_label($key) . ($detail === '' ? '' : ' · ' . $detail));
}

function shop_input(array $input, string $key): string
{
    return is_string($input[$key] ?? null) ? trim($input[$key]) : '';
}

function shop_id(string $value): bool
{
    return preg_match('/\A[a-z0-9][a-z0-9_-]{0,63}\z/', $value) === 1;
}

function shop_directory(string $path): void
{
    $path = str_replace('/', DIRECTORY_SEPARATOR, $path);
    if (is_link($path) || (!is_dir($path) && !@mkdir($path, 0700)) || realpath($path) !== $path) {
        throw shop_error('storage');
    }
}

function shop_lock()
{
    shop_directory(SHOP_DIR);
    return admin_lock();
}

function shop_write(string $name, string $text, int $limit = SHOP_LIMIT): void
{
    shop_directory(SHOP_DIR);
    admin_write_payload(SHOP_DIR . '/' . $name . '.php', $text, $limit);
}

function shop_endpoint(string $base, array $params = []): string
{
    return site_url($base, 'shop/') . ($params ? '?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986) : '');
}

function shop_catalog_url(string $base, array $settings): string
{
    return $settings['catalog'] !== '' ? site_page_url($base, $settings['catalog']) : shop_endpoint($base);
}

function shop_money(int $value, array $settings): string
{
    return site_format_number(shop_price_input($value)) . ' ' . ($settings['currency'] === 'RUB' ? '₽' : $settings['currency']);
}

function shop_price_input(int $value): string
{
    return rtrim(rtrim(sprintf('%d.%02d', intdiv($value, 100), $value % 100), '0'), '.');
}

function shop_positive_price(string $minor, string $detail = ''): string
{
    if (!preg_match('/\A[0-9]{1,9}\z/', $minor) || (int) $minor < 1) {
        throw shop_error('positive_price', $detail);
    }
    return (string) (int) $minor;
}

function shop_price_range(array $prices, array $settings): string
{
    if (!$prices) return '—';
    $min = min($prices);
    $max = max($prices);
    return shop_money($min, $settings) . ($min === $max ? '' : ' – ' . shop_money($max, $settings));
}
