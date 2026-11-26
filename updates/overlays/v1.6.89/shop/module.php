<?php
if (!defined('SITE_ROOT')) { http_response_code(404); exit; }

const SHOP_DIR = SITE_ROOT . '/shop/data';
const SHOP_LIMIT = 33554432;
require_once __DIR__ . '/catalog.php';
require_once __DIR__ . '/orders.php';
require_once __DIR__ . '/actions.php';
require_once SITE_ROOT . '/app/users.php';
require_once __DIR__ . '/storefront.php';

function shop_settings(): array
{
    return site_read_record(SHOP_DIR . '/settings.php', 262144) + [
        'catalog' => '', 'guest' => true, 'pageSize' => 24, 'shipping' => 0, 'currency' => 'RUB', 'templates' => [],
    ];
}

function shop_locale(): string
{
    return $GLOBALS['adminLanguage'] ?? site_region_settings()['public'];
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
    $minor = $value % 100;
    $digits = (string) intdiv($value, 100) . ($minor ? '.' . str_pad((string) $minor, 2, '0', STR_PAD_LEFT) : '');
    return site_format_number($digits) . ' ' . ($settings['currency'] === 'RUB' ? '₽' : $settings['currency']);
}
