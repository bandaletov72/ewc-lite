<?php
if (!defined('SITE_ROOT')) { http_response_code(404); exit; }

function shop_admin_product_change(array &$tables, array $input, bool $delete): void
{
    $id = shop_input($input, 'id');
    $copy = shop_input($input, 'copy');
    $positions = array_flip(array_column($tables['products'], 'id'));
    if (($id !== '' && !isset($positions[$id])) || ($copy !== '' && !isset($positions[$copy]))) throw shop_error('not_found');
    if ($delete) {
        if ($id === '' || shop_input($input, 'confirm') !== '1') throw shop_error('not_found');
        shop_admin_product_delete($tables, $id);
        return;
    }
    if (shop_input($input, 'complete') !== '1' || ($copy !== '' && $id !== '')) throw shop_error('row');

    $original = $tables['products'][$positions[$id] ?? -1] ?? [];
    $product = [];
    foreach (['category_id','slug','name','summary','description','image','type','active'] as $field) {
        $value = shop_input($input, $field);
        $limit = ['description'=>SITE_PAGE_LIMIT,'summary'=>5000,'name'=>1000][$field] ?? 2048;
        if (strlen($value) > $limit || preg_match('//u', $value) !== 1 || strpbrk($value, "\0\x1f") !== false) throw shop_error('row', $field);
        $product[$field] = $value;
    }
    if (strpos($product['description'], '<?') !== false) throw shop_error('row', shop_label('description'));
    $product['id'] = $id === '' ? 'product-' . bin2hex(random_bytes(12)) : $id;
    $product['created'] = $original['created'] ?? gmdate('Y-m-d');
    $occupied = array_column($tables['aliases'], 'product_id', 'slug');
    foreach ($tables['products'] as $row) $occupied[$row['slug']] = $row['id'];
    if ($product['slug'] === '') {
        $root = shop_slug($product['name']) ?: 'product';
        $product['slug'] = $root;
        for ($suffix = 2; isset($occupied[$product['slug']]); $suffix++) {
            $ending = '-' . $suffix;
            $product['slug'] = substr($root, 0, 64 - strlen($ending)) . $ending;
        }
    } elseif (isset($occupied[$product['slug']]) && $occupied[$product['slug']] !== $id) {
        throw shop_error('product_slug_used');
    }
    if ($original && $original['slug'] !== $product['slug']) {
        $aliases = array_column($tables['aliases'], null, 'slug');
        unset($aliases[$product['slug']]);
        $aliases[$original['slug']] = ['slug'=>$original['slug'], 'product_id'=>$id];
        $tables['aliases'] = array_values($aliases);
    }

    $offers = array_column($tables['offers'], null, 'id');
    $offerMap = [];
    $warehouseIds = array_fill_keys(array_column($tables['warehouses'], 'id'), true);
    $barcodes = array_column($tables['barcodes'], null, 'offer_id');
    $inventory = [];
    $editedOffers = [];
    foreach (is_array($input['offers'] ?? null) ? $input['offers'] : [] as $key => $draft) {
        if (!is_array($draft)) throw shop_error('row', shop_label('offers'));
        $offer = $offers[$key] ?? null;
        $sku = shop_input($draft, 'sku');
        $price = shop_input($draft, 'price');
        $variant = shop_input($draft, 'variant');
        $barcode = shop_input($draft, 'barcode');
        $quantities = is_array($draft['inventory'] ?? null) ? $draft['inventory'] : [];
        if ($key === '_new' && $sku === '' && $price === '' && $variant === '' && $barcode === ''
            && (int) shop_input($draft, 'stock') === 0 && !array_filter($quantities)) continue;
        if (($key !== '_new' && (!$offer || $offer['product_id'] !== ($copy !== '' ? $copy : $id)))
            || $sku === '' || strlen($sku) > 150 || strlen($variant) > 500 || preg_match('//u', $sku . $variant) !== 1
            || strpbrk($sku . $variant, "\0\x1f") !== false) throw shop_error('row', shop_label('offers'));
        $offerId = $key === '_new' || $copy !== '' ? 'offer-' . bin2hex(random_bytes(12)) : (string) $key;
        if ($copy !== '' && $offer) $offerMap[$key] = $offerId;
        $editedOffers[$offerId] = true;
        unset($barcodes[$offerId]);
        if ($barcode !== '') $barcodes[$offerId] = ['offer_id'=>$offerId,'barcode'=>$barcode];
        foreach ($quantities as $warehouseId => $quantity) {
            if (!isset($warehouseIds[$warehouseId]) || !is_string($quantity)
                || ($quantity !== '' && !preg_match('/\A[0-9]{1,8}\z/', $quantity))) throw shop_error('row', shop_label('product_stock'));
            if ((int) $quantity > 0) $inventory[] = ['offer_id'=>$offerId,'warehouse_id'=>$warehouseId,'quantity'=>(string) (int) $quantity];
        }
        $offers[$offerId] = ['id'=>$offerId,'product_id'=>$product['id'],'sku'=>$sku,'variant'=>$variant,
            'price_minor'=>shop_import_amount($price),'stock'=>shop_input($draft, 'stock'),'active'=>shop_input($draft, 'active')];
    }
    $tables['offers'] = array_values($offers);
    $tables['barcodes'] = array_values($barcodes);
    $tables['inventory'] = array_merge(array_values(array_filter($tables['inventory'], static function (array $row) use ($editedOffers): bool {
        return !isset($editedOffers[$row['offer_id']]);
    })), $inventory);
    if ($copy !== '') shop_admin_product_copy_data($tables, $copy, $product['id'], $offerMap);
    if ($original) $tables['products'][$positions[$id]] = $product;
    else array_unshift($tables['products'], $product);
}

function shop_admin_product_copy_data(array &$tables, string $source, string $id, array $offers): void
{
    $fieldTargets = array_column($tables['fields'], 'target', 'id');
    foreach (['placements'=>'product_id','images'=>'product_id','prices'=>'offer_id','kits'=>'offer_id','values'=>'target_id'] as $sheet => $column) {
        foreach ($tables[$sheet] as $row) {
            $target = $row[$column];
            $isProduct = $column === 'product_id' || ($sheet === 'values' && ($fieldTargets[$row['field_id']] ?? '') === 'product');
            $newTarget = $isProduct ? ($target === $source ? $id : null) : ($offers[$target] ?? null);
            if ($newTarget !== null) {
                $row[$column] = $newTarget;
                $tables[$sheet][] = $row;
            }
        }
    }
}

function shop_admin_product_delete(array &$tables, string $id): void
{
    $offers = [];
    foreach ($tables['offers'] as $offer) if ($offer['product_id'] === $id) $offers[$offer['id']] = true;
    foreach ($tables['kits'] as $kit) {
        if (isset($offers[$kit['component_id']]) && !isset($offers[$kit['offer_id']])) throw shop_error('product_in_bundle');
    }
    foreach (['products'=>'id','offers'=>'product_id','placements'=>'product_id','images'=>'product_id','aliases'=>'product_id',
        'prices'=>'offer_id','kits'=>'offer_id','barcodes'=>'offer_id','inventory'=>'offer_id'] as $sheet => $column) {
        $tables[$sheet] = array_values(array_filter($tables[$sheet], static function (array $row) use ($column, $id, $offers): bool {
            return $column === 'offer_id' ? !isset($offers[$row[$column]]) : $row[$column] !== $id;
        }));
    }
    $fieldTargets = array_column($tables['fields'], 'target', 'id');
    $tables['values'] = array_values(array_filter($tables['values'], static function (array $row) use ($fieldTargets, $id, $offers): bool {
        return ($fieldTargets[$row['field_id']] ?? '') === 'offer' ? !isset($offers[$row['target_id']]) : $row['target_id'] !== $id;
    }));
}
