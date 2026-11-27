<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}

function site_content_shop_photo(string $image): bool
{
    if (shop_image_name($image)) {
        $file = SHOP_IMAGES_DIR . '/' . $image;
    } elseif (shop_library_image_path($image)) {
        $path = rawurldecode($image);
        admin_image_library_path(dirname($path));
        $file = SITE_ROOT . $path;
    } else {
        return false;
    }
    return is_file($file) && !is_link($file)
        && realpath($file) === str_replace('/', DIRECTORY_SEPARATOR, $file);
}

function site_content_shop_gallery(array $photos, array $current): array
{
    $gallery = array_column($current, null, 'image');
    foreach ($photos as $photo) {
        if (!is_object($photo) || !is_string($photo->image ?? null) || !site_content_shop_photo($photo->image)
            || !is_string($photo->caption ?? null)) {
            throw shop_error('photo_invalid');
        }
        $gallery[$photo->image] = ['image' => $photo->image, 'alt' => $photo->caption];
    }
    if (count($gallery) > 50) throw shop_error('photo_invalid');
    return array_values($gallery);
}

function site_content_shop_product(array $tables, string $id, string $base): ?array
{
    $product = array_column($tables['products'], null, 'id')[$id] ?? null;
    if ($product === null) return null;
    $product['gallery'] = [];
    $product['offers'] = [];
    if ($product['image'] !== '') $product['image_url'] = shop_image_url($base, $product['image']);
    foreach ($tables['images'] as $photo) {
        if ($photo['product_id'] === $id) {
            $product['gallery'][] = ['image' => $photo['image'], 'caption' => $photo['alt'], 'url' => shop_image_url($base, $photo['image'])];
        }
    }
    foreach ($tables['offers'] as $offer) {
        if ($offer['product_id'] === $id) {
            $product['offers'][] = ['id' => $offer['id'], 'sku' => $offer['sku'], 'variant' => $offer['variant'],
                'price_minor' => (int) $offer['price_minor'], 'old_price_minor' => $offer['old_price_minor'] === '' ? null : (int) $offer['old_price_minor']];
        }
    }
    return $product;
}

function site_content_shop_media(string $base, string $folder): array
{
    $items = [];
    if ($folder === '' || $folder === 'shop') {
        if (!is_dir(SHOP_IMAGES_DIR) || is_link(SHOP_IMAGES_DIR)) return [];
        foreach (new FilesystemIterator(SHOP_IMAGES_DIR, FilesystemIterator::SKIP_DOTS) as $file) {
            $name = $file->getFilename();
            if (shop_image_name($name) && site_content_shop_photo($name)) {
                $items[] = ['id' => $name, 'name' => $name, 'url' => shop_image_url($base, $name), 'type' => 'image'];
            }
        }
        foreach (admin_image_library_roots() as $root) {
            if (admin_image_library_has_directory($root)) $items[] = ['id' => $root, 'name' => $root, 'type' => 'directory'];
        }
    } else {
        $folder = admin_image_library_path($folder);
        foreach (admin_image_library_items($folder, $base) as $file) {
            $id = $folder . '/' . $file['name'];
            if ($file['type'] === 'directory') {
                $items[] = ['id' => $id, 'name' => $file['name'], 'type' => 'directory'];
                continue;
            }
            $image = '/' . implode('/', array_map('rawurlencode', explode('/', ltrim($id, '/'))));
            if (site_content_shop_photo($image)) $items[] = ['id' => $image, 'name' => $file['name'], 'url' => $file['url'], 'type' => 'image'];
        }
    }
    return $items;
}

function site_content_shop_lookup(string $base)
{
    $kind = $_GET['kind'] ?? 'products';
    $query = $_GET['q'] ?? '';
    $folder = $_GET['folder'] ?? '';
    $id = $_GET['id'] ?? '';
    $page = $_GET['page'] ?? '1';
    if (!in_array($kind, ['product', 'products', 'categories', 'media'], true) || !is_string($query)
        || strlen($query) > 240 || preg_match('//u', $query) !== 1 || !is_string($folder) || strlen($folder) > 1024
        || !is_string($id) || !is_string($page) || !preg_match('/\A[1-9][0-9]{0,6}\z/', $page)) {
        site_content_api_error(400, 'invalid_search');
        return;
    }
    $revision = shop_generation();
    $currency = shop_settings()['currency'];
    $matched = [];
    if ($kind === 'product') {
        $product = shop_id($id) ? site_content_shop_product(shop_tables($revision), $id, $base) : null;
        if ($product === null) {
            site_content_api_error(404, 'product_not_found');
            return;
        }
        site_content_gpt_response(['revision' => $revision, 'currency' => $currency, 'product' => $product]);
        return;
    }
    if ($kind === 'media') {
        try {
            $items = site_content_shop_media($base, $folder);
        } catch (RuntimeException $exception) {
            site_content_api_error(400, 'invalid_media_folder');
            return;
        }
    } else {
        $tables = shop_tables($revision);
        $columns = $kind === 'products' ? ['id', 'name', 'slug', 'category_id'] : ['id', 'name', 'slug', 'parent_id'];
        $items = array_map(static function (array $row) use ($columns): array {
            return array_intersect_key($row, array_flip($columns));
        }, $tables[$kind]);
        if ($kind === 'products' && $query !== '') {
            foreach ($tables['offers'] as $offer) {
                if (strpos(shop_lower($offer['sku']), shop_lower($query)) !== false) $matched[$offer['product_id']] = true;
            }
        }
    }
    $keyword = shop_lower(trim($query));
    $items = array_values(array_filter($items, static function (array $row) use ($keyword, $matched): bool {
        return isset($matched[$row['id']]) || strpos(shop_lower($row['id'] . ' ' . $row['name']), $keyword) !== false;
    }));
    site_content_gpt_response(['items' => array_slice($items, ((int) $page - 1) * 15, 15), 'page' => (int) $page,
        'per_page' => 15, 'total' => count($items), 'revision' => $revision, 'currency' => $currency]);
}

function site_content_shop_price($value): string
{
    if (!is_int($value)) throw shop_error('row');
    return shop_positive_price((string) $value);
}

function site_content_shop_price_changes(array &$tables, array $rows, string $productId): void
{
    $positions = array_flip(array_column($tables['offers'], 'id'));
    if (!$rows || count($rows) > 25) throw shop_error('row');
    foreach ($rows as $row) {
        if (!is_object($row) || !is_string($row->offer_id ?? null) || !isset($positions[$row->offer_id])
            || (!property_exists($row, 'price_minor') && !property_exists($row, 'old_price_minor'))) throw shop_error('row');
        $offer = &$tables['offers'][$positions[$row->offer_id]];
        if ($productId !== '' && $offer['product_id'] !== $productId) throw shop_error('row');
        if (property_exists($row, 'price_minor')) $offer['price_minor'] = site_content_shop_price($row->price_minor);
        if (property_exists($row, 'old_price_minor')) {
            $offer['old_price_minor'] = $row->old_price_minor === null ? '' : site_content_shop_price($row->old_price_minor);
        }
        unset($offer);
    }
}

function site_content_shop_reprice(array &$tables, object $data): array
{
    $ids = $data->offer_ids ?? null;
    $points = $data->basis_points ?? null;
    if (!is_array($ids) || !$ids || count($ids) > 25 || !is_int($points) || $points <= -10000 || $points > 100000
        || (isset($data->keep_old_price) && !is_bool($data->keep_old_price))) throw shop_error('row');
    $offers = array_column($tables['offers'], null, 'id');
    $rows = [];
    foreach ($ids as $id) {
        if (!is_string($id) || !isset($offers[$id]) || isset($rows[$id])) throw shop_error('row');
        $offer = $offers[$id];
        $price = (int) round((int) $offer['price_minor'] * (10000 + $points) / 10000);
        $row = (object) ['offer_id' => $id, 'price_minor' => $price];
        if (!empty($data->keep_old_price)) $row->old_price_minor = (int) $offer['price_minor'];
        $rows[$id] = $row;
    }
    site_content_shop_price_changes($tables, array_values($rows), '');
    return array_keys($rows);
}

function site_content_shop_edit_product(array &$tables, string $action, object $data, string $base): array
{
    $creating = $action === 'create_product';
    $id = $creating ? '' : ($data->id ?? null);
    if (!is_string($id) || (!$creating && !shop_id($id))) throw shop_error('row');
    $before = $creating ? null : site_content_shop_product($tables, $id, $base);
    if (!$creating && $before === null) throw shop_error('not_found');
    $fields = ['category_id', 'slug', 'name', 'summary', 'description', 'image', 'type', 'active'];
    $input = ['id' => $id, 'complete' => '1'] + array_intersect_key($before ?? [], array_flip($fields));
    $input += ['category_id' => '', 'slug' => '', 'name' => '', 'summary' => '', 'description' => '', 'image' => '', 'type' => 'physical', 'active' => '0'];
    foreach ($fields as $field) {
        if (!property_exists($data, $field)) continue;
        $value = $data->$field;
        if ($field === 'active' && is_bool($value)) $value = $value ? '1' : '0';
        if (!is_string($value) || ($field === 'image' && $value !== '' && !site_content_shop_photo($value))) throw shop_error('row', $field);
        $input[$field] = $value;
    }
    if (property_exists($data, 'add_photos')) {
        if (!is_array($data->add_photos) || count($data->add_photos) > 50) throw shop_error('photo_invalid');
        $input['gallery_present'] = '1';
        $existing = array_values(array_filter($tables['images'], static function (array $row) use ($id): bool {
            return $row['product_id'] === $id;
        }));
        $input['gallery'] = site_content_shop_gallery($data->add_photos, $existing);
    }
    if ($creating) {
        if (!is_string($data->sku ?? null) || trim($data->sku) === '' || $input['category_id'] === '') throw shop_error('row');
        $input['offers']['_new'] = ['sku' => $data->sku, 'price' => shop_price_input((int) site_content_shop_price($data->price_minor ?? null)),
            'stock' => '0', 'active' => '1'];
        if (isset($data->old_price_minor)) $input['offers']['_new']['old_price'] = shop_price_input((int) site_content_shop_price($data->old_price_minor));
    }
    shop_admin_product_change($tables, $input, false);
    if ($creating) $id = $tables['products'][0]['id'];
    if (!$creating && property_exists($data, 'prices')) {
        if (!is_array($data->prices)) throw shop_error('row');
        site_content_shop_price_changes($tables, $data->prices, $id);
    }
    $after = site_content_shop_product($tables, $id, $base);
    if ($creating) {
        unset($after['id'], $after['created']);
        foreach ($after['offers'] as &$offer) unset($offer['id']);
        unset($offer);
    }
    $changes = [];
    foreach ($after as $field => $value) {
        if (($before[$field] ?? null) !== $value) $changes[$field] = ['before' => $before[$field] ?? null, 'after' => $value];
    }
    return [$id, $changes];
}

function site_content_shop_canonical($value)
{
    if (is_object($value)) {
        $properties = get_object_vars($value);
        ksort($properties);
        return (object) array_map('site_content_shop_canonical', $properties);
    }
    return is_array($value) ? array_map('site_content_shop_canonical', $value) : $value;
}

function site_content_shop_dispatch(string $route, string $base, string $tokenHash, string $access)
{
    if (!in_array($route, ['lookup', 'preview', 'apply'], true)) {
        site_content_api_error(404, 'not_found');
        return;
    }
    $read = $route === 'lookup';
    $method = $read ? 'GET' : 'POST';
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== $method) {
        header('Allow: ' . $method);
        site_content_api_error(405, 'method_not_allowed');
        return;
    }
    if (!$read && $access !== 'write') {
        site_content_api_error(403, 'write_access_required');
        return;
    }
    require_once SITE_ROOT . '/admin/files.php';
    require_once SITE_ROOT . '/admin/image-library.php';
    require_once site_shop_directory() . '/module.php';
    if ($read) {
        site_content_shop_lookup($base);
        return;
    }
    require_once site_shop_directory() . '/admin-products.php';
    require_once site_shop_directory() . '/import.php';
    $applying = $route === 'apply';
    $body = site_content_api_json(SITE_CONTENT_GPT_LIMIT);
    if ($body === null) return;
    if (!in_array($body->operation ?? null, ['create_product', 'edit_product', 'reprice'], true)
        || !is_string($body->revision ?? null) || !preg_match('/\A(?:[a-f0-9]{32})?\z/', $body->revision)
        || !is_object($body->data ?? null) || !get_object_vars($body->data)) {
        site_content_api_error(422, 'invalid_catalog_request');
        return;
    }
    if ($applying && (!is_string($body->preview_token ?? null)
        || !preg_match('/\A([0-9]{1,11})\.[a-f0-9]{64}\z/', $body->preview_token, $expiry))) {
        site_content_api_error(428, 'preview_required');
        return;
    }
    $operation = $body->operation;
    $allowed = [
        'create_product' => ['category_id','slug','name','summary','description','image','type','active','sku','price_minor','old_price_minor','add_photos'],
        'edit_product' => ['id','category_id','slug','name','summary','description','image','type','active','add_photos','prices'],
        'reprice' => ['offer_ids','basis_points','keep_old_price'],
    ];
    $fields = array_keys(get_object_vars($body->data));
    if (array_diff($fields, $allowed[$operation])) {
        site_content_api_error(422, 'unknown_field');
        return;
    }
    $data = site_content_shop_canonical($body->data);
    $lock = $applying ? shop_lock() : null;
    try {
        if ($applying && !site_content_api_write_access($tokenHash, 'gpt_shop')) return;
        if (!hash_equals(shop_generation(), $body->revision)) {
            site_content_api_error(412, 'catalog_has_changed');
            return;
        }
        $until = $applying ? (int) $expiry[1] : time() + SITE_CONTENT_GPT_PREVIEW_LIFETIME;
        $settings = shop_settings();
        $receipt = site_content_gpt_preview_token($until, $tokenHash, SHOP_DIR, 'shop/' . $operation, $body->revision,
            ['data' => $data, 'currency' => $settings['currency'], 'rounding' => $settings['rounding']]);
        if ($applying && ($until < time() || !hash_equals($receipt, $body->preview_token))) {
            site_content_api_error(412, 'preview_expired_or_changed');
            return;
        }
        $tables = shop_tables($body->revision);
        $productId = '';
        try {
            if ($operation === 'reprice') {
                $before = array_column($tables['offers'], null, 'id');
                $ids = site_content_shop_reprice($tables, $data);
                $after = array_column($tables['offers'], null, 'id');
                $diff = [];
                foreach ($ids as $id) {
                    if ($before[$id] === $after[$id]) continue;
                    $fields = ['price_minor' => true, 'old_price_minor' => true];
                    $diff[] = ['id' => $id, 'sku' => $after[$id]['sku'], 'product_id' => $after[$id]['product_id'],
                        'before' => array_intersect_key($before[$id], $fields), 'after' => array_intersect_key($after[$id], $fields)];
                }
            } else {
                list($productId, $diff) = site_content_shop_edit_product($tables, $operation, $data, $base);
            }
            $index = shop_catalog_build($tables);
        } catch (RuntimeException $exception) {
            site_content_api_error(422, 'invalid_catalog_change', ['detail' => $exception->getMessage()]);
            return;
        }
        if (!$diff) {
            site_content_api_error(422, 'no_changes');
            return;
        }
        if (!$applying) {
            site_content_gpt_response(['revision' => $body->revision, 'operation' => $operation,
                'currency' => $settings['currency'], 'rounding' => $settings['rounding'], 'diff' => $diff,
                'preview_token' => $receipt, 'expires_at' => gmdate('c', $until)]);
            return;
        }
        shop_publish($tables, $index);
        site_content_api_response(200, ['saved' => true, 'product_id' => $productId, 'revision' => shop_generation()]);
    } finally {
        if ($lock !== null) fclose($lock);
    }
}
