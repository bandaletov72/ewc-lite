<?php
if (!defined('SITE_ROOT')) { http_response_code(404); exit; }

const SHOP_COLUMNS = [
    'categories' => ['id', 'parent_id', 'slug', 'name', 'sort', 'descendants', 'description'],
    'products' => ['id', 'category_id', 'slug', 'name', 'summary', 'description', 'image', 'created', 'type', 'active', 'quick_pickup'],
    'offers' => ['id', 'product_id', 'sku', 'variant', 'price_minor', 'stock', 'active', 'old_price_minor'],
    'fields' => ['id', 'category_id', 'name', 'type', 'target', 'filter', 'sort', 'unit', 'icon'],
    'placements' => ['product_id', 'category_id'],
    'images' => ['product_id', 'image', 'alt'],
    'prices' => ['offer_id', 'type', 'price_minor'],
    'kits' => ['offer_id', 'component_id', 'quantity'],
    'aliases' => ['slug','product_id'],
    'values' => ['field_id', 'target_id', 'value'],
    'warehouses' => ['id', 'name'],
    'inventory' => ['offer_id', 'warehouse_id', 'quantity'],
    'barcodes' => ['offer_id', 'barcode'],
    'product_seo' => ['product_id', 'title', 'heading', 'description', 'keywords', 'noindex'],
    'category_media' => ['category_id', 'type', 'value'],
    'offer_media' => ['offer_id', 'image'],
];

const SHOP_SEO_LIMITS = ['title'=>240, 'heading'=>240, 'description'=>500, 'keywords'=>500];

function shop_csv_read(string $text, string $delimiter = '|'): array
{
    if (strlen($text) > SHOP_LIMIT || strpos($text, "\0") !== false || strpos($text, "\x1f") !== false || preg_match('//u', $text) !== 1) throw shop_error('csv');
    $stream = fopen('php://temp', 'w+b');
    fwrite($stream, substr($text, 0, 3) === "\xef\xbb\xbf" ? substr($text, 3) : $text);
    rewind($stream);
    $rows = [];
    try {
        while (($row = fgetcsv($stream, 0, $delimiter, '"', "\x1f")) !== false) {
            if ($row !== [null]) $rows[] = $row;
            if (count($rows) > 250001) throw shop_error('csv');
        }
    } finally {
        fclose($stream);
    }
    return $rows;
}

function shop_csv(array $rows): string
{
    $stream = fopen('php://temp', 'w+b');
    try {
        foreach ($rows as $row) {
            // Reserve a control byte as the escape character so legacy PHP uses doubled-quote CSV.
            fputcsv($stream, $row, '|', '"', "\x1f");
        }
        rewind($stream);
        return (string) stream_get_contents($stream);
    } finally {
        fclose($stream);
    }
}

function shop_rows(string $text, array $columns, string $sheet, string $delimiter = '|'): array
{
    $rows = shop_csv_read($text, $delimiter);
    if (!$rows) throw shop_error('header', $sheet);
    $header = array_shift($rows);
    $missing = count($columns) - count($header);
    $legacyField = $missing > 0 && $missing <= (['fields'=>2,'offers'=>1,'products'=>1][$sheet] ?? 0)
        && $header === array_slice($columns, 0, count($header));
    if ($header !== $columns && !$legacyField) throw shop_error('header', $sheet);
    foreach ($rows as $number => &$row) {
        if ($legacyField && count($row) === count($header)) $row = array_pad($row, count($columns), '');
        if (count($row) !== count($columns)) throw shop_error('row', $sheet . ' #' . ($number + 2));
        $row = array_combine($columns, array_map('trim', $row));
    }
    unset($row);
    return $rows;
}

function shop_generation(): string
{
    $revision = site_read_record(SHOP_DIR . '/catalog.php')['revision'] ?? '';
    return is_string($revision) && preg_match('/\A[a-f0-9]{32}\z/', $revision) ? $revision : '';
}

function shop_tables(?string $revision = null): array
{
    $revision = $revision ?? shop_generation();
    return shop_decode_tables($revision === '' ? '' : (site_read_payload(SHOP_DIR . '/catalog-' . $revision . '.php', SHOP_LIMIT) ?? ''));
}

function shop_decode_tables(string $payload): array
{
    $tables = array_fill_keys(array_keys(SHOP_COLUMNS), []);
    foreach (shop_csv_read($payload) as $row) {
        $sheet = array_shift($row);
        $columns = SHOP_COLUMNS[$sheet] ?? [];
        $missing = count($columns) - count($row);
        if ($missing > 0 && $missing <= (['fields'=>2,'offers'=>1,'products'=>1][$sheet] ?? 0)) $row = array_pad($row, count($columns), '');
        if (!isset(SHOP_COLUMNS[$sheet]) || count($row) !== count(SHOP_COLUMNS[$sheet])) throw shop_error('csv');
        $tables[$sheet][] = array_combine(SHOP_COLUMNS[$sheet], $row);
    }
    return shop_migrate_old_prices($tables);
}

function shop_migrate_old_prices(array $tables): array
{
    $offerPositions = array_flip(array_column($tables['offers'], 'id'));
    foreach ($tables['prices'] as $key => $price) {
        if ($price['type'] !== 'old') continue;
        $position = $offerPositions[$price['offer_id']] ?? null;
        if ($position !== null && ($tables['offers'][$position]['old_price_minor'] ?? '') === '' && (int) $price['price_minor'] > 0) {
            $tables['offers'][$position]['old_price_minor'] = $price['price_minor'];
        }
        unset($tables['prices'][$key]);
    }
    $tables['prices'] = array_values($tables['prices']);
    return $tables;
}

function shop_field_label(array $field): string
{
    $unit = $field['unit'] ?? '';
    return $field['name'] . ($unit === '' ? '' : ', ' . $unit);
}

function shop_field_icon(array $field, string $base): string
{
    return site_page_icon(['fields'=>['AnnouncementIcon'=>$field['icon'] ?? '']], $base);
}

function shop_field_types(): array
{
    return site_iblock_types() + ['list'=>shop_label('view_list')];
}

function shop_field_type(string $type): string
{
    return $type === 'boolean' ? 'bool' : $type;
}

function shop_product_has_field(array $index, string $product, string $field): bool
{
    foreach (array_keys($index['placements'][$product] ?? []) as $category) {
        if (in_array($field, array_map('strval', $index['categoriesFields'][$category] ?? []), true)) return true;
    }
    return false;
}

function shop_index(bool $refresh = false): array
{
    static $index;
    if ($index === null || $refresh) {
        $revision = shop_generation();
        $cacheKey = 'ewc-shop:' . hash('sha256', SHOP_DIR . ':' . $revision);
        $sharedCache = $revision !== '' && function_exists('apcu_enabled') && apcu_enabled();
        $index = $sharedCache ? apcu_fetch($cacheKey) : false;
        if (!is_array($index)) {
            $index = $revision === '' ? [] : site_read_record(SHOP_DIR . '/index-' . $revision . '.php', SHOP_LIMIT);
            if ($sharedCache) apcu_store($cacheKey, $index, 3600);
        }
        $index += array_fill_keys(['categories', 'children', 'products', 'offers', 'fields', 'categoriesFields',
            'values', 'lookup', 'local', 'tree', 'lists', 'search', 'slugs', 'images', 'prices', 'kits', 'brands', 'placements', 'productOffers', 'aliases', 'seo', 'categoryMedia', 'offerMedia'], []);
        foreach ($index['prices'] as $id => &$prices) {
            if (!isset($prices['old'])) continue;
            if (isset($index['offers'][$id]) && !isset($index['offers'][$id]['old_price_minor'])) {
                $index['offers'][$id]['old_price_minor'] = (string) $prices['old'];
            }
            unset($prices['old']);
        }
        unset($prices);
        if (!isset($index['categoryFallbacks'])) $index['categoryFallbacks'] = shop_category_fallbacks($index);
    }
    return $index;
}

function shop_category_fallbacks(array $index): array
{
    $rank = [];
    $photos = [];
    $order = 0;
    $existingPhotos = [];
    $isPresent = static function (string $photo) use (&$existingPhotos): bool {
        if ($photo === '') return false;
        return $existingPhotos[$photo] ?? ($existingPhotos[$photo] = !shop_image_name($photo) || is_file(SHOP_IMAGES_DIR . '/' . $photo));
    };
    foreach ($index['products'] as $id => $product) {
        $rank[$id] = [$product['created'], -$order++];
        $photos[$id] = '';
        foreach (array_merge([$product['image']], array_column($index['images'][$id] ?? [], 'image')) as $photo) {
            if (!$isPresent($photo)) continue;
            $photos[$id] = $photo;
            break;
        }
    }
    $fallbacks = [];
    foreach ($index['categories'] as $categoryId => $category) {
        $source = $category['descendants'] === '1' ? 'lists' : 'local';
        $latest = null;
        foreach ($index[$source][$categoryId] ?? [] as $offerId) {
            $offer = $index['offers'][$offerId];
            if ((int) $offer['price_minor'] < 1) continue;
            $productId = $offer['product_id'];
            $photo = $photos[$productId] ?: ($index['offerMedia'][$offerId] ?? '');
            if ($isPresent($photo) && ($latest === null || $rank[$productId] > $latest)) {
                $latest = $rank[$productId];
                $fallbacks[$categoryId] = $photo;
            }
        }
    }
    return $fallbacks;
}

function shop_image_name(string $name): bool
{
    return preg_match('/\A[a-zA-Z0-9][a-zA-Z0-9._-]{0,110}\.(?:jpg|jpeg|png|webp|avif|gif)\z/i', $name) === 1;
}

function shop_image_valid(string $name): bool
{
    return $name === '' || shop_image_name($name) || shop_library_image_path($name)
        || (filter_var($name, FILTER_VALIDATE_URL) && strtolower((string) parse_url($name, PHP_URL_SCHEME)) === 'https');
}

function shop_library_image_path(string $path): bool
{
    if (!preg_match('~\A/(?:images/|templates/(?:images/|themes/[a-zA-Z0-9_-]+/images/))[^?#]+\.(?:jpe?g|png|webp|gif|avif)\z~i', $path)) return false;
    foreach (explode('/', substr($path, 1)) as $part) {
        $name = rawurldecode($part);
        if ($name === '' || $name[0] === '.' || strpos($name, '/') !== false || strpos($name, chr(92)) !== false
            || preg_match('/[\x00-\x1f\x7f]|%[0-9a-f]{2}/iu', $name) !== 0) return false;
    }
    return true;
}

function shop_catalog_build(array $tables): array
{
    $index = array_fill_keys(['categories', 'children', 'products', 'offers', 'fields', 'categoriesFields',
        'values', 'lookup', 'local', 'tree', 'lists', 'search', 'slugs', 'images', 'prices', 'kits', 'brands', 'placements', 'productOffers', 'aliases', 'seo', 'categoryMedia', 'offerMedia'], []);
    foreach (['categories', 'products', 'offers', 'fields'] as $sheet) {
        foreach ($tables[$sheet] as $row) {
            $id = $row['id'];
            if (!shop_id($id) || isset($index[$sheet][$id])) throw shop_error('row', $sheet . ' · ' . $id);
            $index[$sheet][$id] = $row;
        }
    }
    $paths = [];
    foreach ($index['categories'] as $id => $category) {
        $parent = $category['parent_id'];
        if (!shop_id($category['slug']) || $category['name'] === '' || !in_array($category['descendants'], ['0', '1'], true)
            || ($parent === '' && $category['slug'] === 'product') || ($parent !== '' && !isset($index['categories'][$parent])) || isset($paths[$parent][$category['slug']])) {
            throw shop_error('row', 'categories · ' . $id);
        }
        $paths[$parent][$category['slug']] = true;
        $index['children'][$parent][] = $id;
        $ancestors = [];
        $next = (string) $id;
        while ($next !== '') {
            if (isset($ancestors[$next]) || !isset($index['categories'][$next])) throw shop_error('tree', (string) $id);
            $ancestors[$next] = true;
            $next = $index['categories'][$next]['parent_id'];
        }
        $index['tree'][$id] = array_reverse(array_keys($ancestors));
    }
    foreach ($index['children'] as &$childIds) {
        usort($childIds, static function ($a, $b) use ($index): int {
            return ((int) $index['categories'][$a]['sort'] <=> (int) $index['categories'][$b]['sort']) ?: strcmp((string) $a, (string) $b);
        });
    }
    unset($childIds);
    foreach ($tables['category_media'] ?? [] as $media) {
        $id = $media['category_id'];
        if (!isset($index['categories'][$id]) || isset($index['categoryMedia'][$id])
            || !in_array($media['type'], ['icon','image'], true)
            || ($media['type'] === 'icon' ? site_parse_page_icon($media['value']) === null : !shop_image_name($media['value']))) {
            throw shop_error('row', shop_label('categories'));
        }
        $index['categoryMedia'][$id] = $media;
    }
    $fieldTypes = shop_field_types();
    foreach ($index['fields'] as $id => $field) {
        if (!isset($fieldTypes[shop_field_type($field['type'])])
            || !in_array($field['target'], ['product', 'offer'], true) || $field['name'] === ''
            || strlen($field['unit'] ?? '') > 80 || preg_match('/[\x00-\x1f\x7f]/u', $field['unit'] ?? '') !== 0
            || (($field['icon'] ?? '') !== '' && site_parse_page_icon($field['icon']) === null)
            || ($field['category_id'] !== '' && !isset($index['categories'][$field['category_id']]))
            || !in_array($field['filter'], ['0', '1'], true) || !in_array($field['sort'], ['0', '1'], true)
            || ($field['filter'] === '1' && in_array($field['type'], ['text','textarea','hidden','form','video'], true))
            || ($field['sort'] === '1' && $field['type'] !== 'number')) throw shop_error('row', 'fields · ' . $id);
    }
    foreach ($index['tree'] as $id => $ancestors) {
        foreach ($index['fields'] as $fieldId => $field) {
            if ($field['category_id'] === '' || in_array($field['category_id'], array_map('strval', $ancestors), true)) {
                $index['categoriesFields'][$id][] = $fieldId;
            }
        }
    }
    $slugs = [];
    foreach ($index['products'] as $id => $product) {
        if (!isset($index['categories'][$product['category_id']]) || !shop_id($product['slug']) || isset($slugs[$product['slug']])
            || !in_array($product['type'], ['physical', 'digital', 'service', 'kit'], true) || $product['name'] === '' || !shop_image_valid($product['image']) || !in_array($product['active'], ['0', '1'], true)
            || !in_array($product['quick_pickup'] ?? '', ['', '0', '1'], true)
            || !preg_match('/\A\d{4}-\d{2}-\d{2}\z/', $product['created']) || strtotime($product['created']) === false) {
            throw shop_error('row', 'products · ' . $id);
        }
        $slugs[$product['slug']] = true;
        $index['slugs'][$product['slug']] = $id;
        $index['placements'][$id][$product['category_id']] = true;
    }
    foreach ($tables['aliases'] as $row) {
        $slug = $row['slug'];
        $id = $row['product_id'];
        $route = strlen($slug) <= 1500 && preg_match('~\A[a-z0-9_-]+(?:/[a-z0-9_-]+)+\z~', $slug);
        if ((!shop_id($slug) && !$route) || !isset($index['products'][$id]) || isset($index['aliases'][$slug])) throw shop_error('row','aliases');
        if (!isset($index['slugs'][$slug])) $index['aliases'][$slug] = $id;
    }
    foreach ($tables['product_seo'] ?? [] as $meta) {
        $id = $meta['product_id'];
        if (!isset($index['products'][$id]) || isset($index['seo'][$id]) || !in_array($meta['noindex'], ['0','1'], true)) throw shop_error('row', 'SEO');
        foreach (SHOP_SEO_LIMITS as $field => $limit) {
            if (strlen($meta[$field]) > $limit || preg_match('/[\x00-\x1f\x7f]/u', $meta[$field]) !== 0) throw shop_error('row', 'SEO');
        }
        $index['seo'][$id] = $meta;
    }
    foreach ($tables['placements'] as $row) {
        if (!isset($index['products'][$row['product_id']], $index['categories'][$row['category_id']])) throw shop_error('row', 'placements');
        $index['placements'][$row['product_id']][$row['category_id']] = true;
    }
    foreach ($tables['images'] as $row) {
        if (!isset($index['products'][$row['product_id']]) || $row['image'] === '' || !shop_image_valid($row['image'])) throw shop_error('row', 'images');
        $index['images'][$row['product_id']][] = $row;
    }
    $skus = [];
    foreach ($index['offers'] as $id => $offer) {
        if (!isset($index['products'][$offer['product_id']]) || $offer['sku'] === '' || strlen($offer['sku']) > 150
            || (($offer['old_price_minor'] ?? '') !== '' && !preg_match('/\A[1-9][0-9]{0,8}\z/', $offer['old_price_minor']))
            || isset($skus[shop_lower($offer['sku'])]) || !preg_match('/\A[0-9]{1,9}\z/', $offer['price_minor'])
            || !preg_match('/\A[0-9]{1,8}\z/', $offer['stock']) || !in_array($offer['active'], ['0', '1'], true)) {
            throw shop_error('row', 'offers · ' . $id);
        }
        $skus[shop_lower($offer['sku'])] = true;
        if ($offer['active'] === '1') $index['productOffers'][$offer['product_id']][] = $id;
    }
    $warehouses = [];
    foreach ($tables['offer_media'] ?? [] as $media) {
        if (!isset($index['offers'][$media['offer_id']]) || isset($index['offerMedia'][$media['offer_id']])
            || $media['image'] === '' || !shop_image_valid($media['image'])) throw shop_error('photo_invalid');
        $index['offerMedia'][$media['offer_id']] = $media['image'];
    }
    foreach ($tables['warehouses'] as $warehouse) {
        if (!shop_id($warehouse['id']) || isset($warehouses[$warehouse['id']]) || trim($warehouse['name']) === ''
            || strlen($warehouse['name']) > 240) throw shop_error('row', shop_label('warehouses'));
        $warehouses[$warehouse['id']] = true;
    }
    $inventory = [];
    foreach ($tables['inventory'] as $row) {
        $offerId = $row['offer_id'];
        $warehouseId = $row['warehouse_id'];
        if (!isset($index['offers'][$offerId], $warehouses[$warehouseId]) || isset($inventory[$offerId][$warehouseId])
            || !preg_match('/\A[0-9]{1,8}\z/', $row['quantity'])) throw shop_error('row', shop_label('product_stock'));
        $inventory[$offerId][$warehouseId] = true;
        $totalStock = (int) $index['offers'][$offerId]['stock'] + (int) $row['quantity'];
        if ($totalStock > 99999999) throw shop_error('row', shop_label('product_stock'));
        $index['offers'][$offerId]['stock'] = (string) $totalStock;
    }
    $barcodes = [];
    $hasBarcode = [];
    foreach ($tables['barcodes'] as $row) {
        $barcode = $row['barcode'];
        if (!isset($index['offers'][$row['offer_id']]) || isset($hasBarcode[$row['offer_id']]) || isset($barcodes[$barcode])
            || preg_match('/\A[[:graph:]]{1,128}\z/u', $barcode) !== 1) throw shop_error('row', shop_label('product_barcode'));
        $barcodes[$barcode] = true;
        $hasBarcode[$row['offer_id']] = true;
    }
    foreach ($tables['prices'] as $row) {
        if (!isset($index['offers'][$row['offer_id']]) || $row['type'] === 'old' || !shop_id($row['type']) || !preg_match('/\A[0-9]{1,9}\z/', $row['price_minor'])) throw shop_error('row', 'prices');
        $index['prices'][$row['offer_id']][$row['type']] = (int) $row['price_minor'];
    }
    foreach ($tables['kits'] as $row) {
        [$parent, $child, $quantity] = [$row['offer_id'], $row['component_id'], $row['quantity']];
        if (!isset($index['offers'][$parent], $index['offers'][$child]) || $parent === $child
            || $index['products'][$index['offers'][$parent]['product_id']]['type'] !== 'kit'
            || $index['products'][$index['offers'][$child]['product_id']]['type'] === 'kit'
            || !preg_match('/\A[1-9][0-9]{0,3}\z/', $quantity) || isset($index['kits'][$parent][$child])) throw shop_error('row', 'kits');
        $index['kits'][$parent][$child] = (int) $quantity;
    }
    $productFields = [];
    foreach ($index['placements'] as $productId => $locations) {
        foreach (array_keys($locations) as $categoryId) {
            foreach ($index['categoriesFields'][$categoryId] ?? [] as $fieldId) $productFields[$productId][$fieldId] = true;
        }
    }
    foreach ($tables['values'] as $entry) {
        $field = $index['fields'][$entry['field_id']] ?? null;
        $id = $entry['target_id'];
        $product = $field && $field['target'] === 'offer'
            ? ($index['products'][$index['offers'][$id]['product_id'] ?? ''] ?? null) : ($index['products'][$id] ?? null);
        $value = $entry['value'];
        if (!$field || !$product
            || $value === '' || strlen($value) > 500 || !site_iblock_validate_cell($value, shop_field_type($field['type']))
            || ($field['type'] !== 'list' && isset($index['values'][$field['id']][$id]))) {
            throw shop_error('row', 'values · ' . $entry['field_id'] . '/' . $id);
        }
        $index['values'][$field['id']][$id][] = $value;
    }
    foreach ($index['offers'] as $id => $offer) {
        $product = $index['products'][$offer['product_id']];
        if ($offer['active'] !== '1' || $product['active'] !== '1') continue;
        $ancestors = [];
        foreach (array_keys($index['placements'][$offer['product_id']]) as $placement) {
            $index['local'][$placement][] = $id;
            foreach ($index['tree'][$placement] as $categoryId) $ancestors[$categoryId] = true;
        }
        foreach (array_keys($ancestors) as $categoryId) $index['lists'][$categoryId][] = $id;
        $index['lists'][''][] = $id;
        $brands = $index['values']['brand'][($index['fields']['brand']['target'] ?? '') === 'offer' ? $id : $offer['product_id']] ?? [];
        foreach ($brands as $brand) {
            $brandSlug = shop_slug($brand);
            if ($brandSlug !== '') $index['brands'][$brandSlug][$brand] = true;
        }
        $index['search'][$id] = shop_lower(implode(' ', array_merge([$product['name'], $offer['sku'], $offer['variant']], $brands)));
        foreach (array_keys($productFields[$offer['product_id']] ?? []) as $fieldId) {
            $field = $index['fields'][$fieldId];
            if (($field['filter'] !== '1' && $fieldId !== 'brand') || !in_array($field['type'], ['list', 'boolean'], true)) continue;
            $target = $field['target'] === 'offer' ? $id : $offer['product_id'];
            foreach ($index['values'][$fieldId][$target] ?? [] as $value) $index['lookup'][$fieldId][$value][] = $id;
        }
    }
    $index['categoryFallbacks'] = shop_category_fallbacks($index);
    return $index;
}

function shop_slug(string $value): string
{
    return substr(trim(preg_replace('/[^a-z0-9]+/', '-', strtolower(ew_translit($value))), '-'), 0, 64);
}

function shop_lower(string $text): string
{
    return function_exists('mb_strtolower') ? mb_strtolower($text, 'UTF-8')
        : strtr(strtr($text, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz'), array_combine(
            preg_split('//u', 'АБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ', -1, PREG_SPLIT_NO_EMPTY),
            preg_split('//u', 'абвгдеёжзийклмнопрстуфхцчшщъыьэюя', -1, PREG_SPLIT_NO_EMPTY)));
}

function shop_publish(array $tables, array $index, ?array $brands = null): void
{
    $revision = bin2hex(random_bytes(16));
    $rows = [];
    foreach (SHOP_COLUMNS as $sheet => $columns) {
        foreach ($tables[$sheet] as $row) $rows[] = array_merge([$sheet], array_map(static function ($column) use ($row) {
            return $row[$column] ?? '';
        }, $columns));
    }
    $json = json_encode($index, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($json === false) throw shop_error('storage');
    shop_write('catalog-' . $revision, shop_csv($rows));
    shop_write('index-' . $revision, $json);
    $brandStore = shop_brand_store();
    if ($brands !== null) {
        $brandStore = 'brands-' . $revision;
        admin_write_record(SHOP_DIR . '/' . $brandStore . '.php', $brands, 262144);
    }
    shop_write('catalog', json_encode(['revision' => $revision, 'brandStore' => $brandStore]));
    $cutoff = time() - 86400;
    foreach (glob(SHOP_DIR . '/{catalog,index}-*.php', GLOB_BRACE) ?: [] as $path) {
        if (strpos($path, $revision) === false && filemtime($path) < $cutoff && !is_link($path)) @unlink($path);
    }
}
