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
    $product['quick_pickup'] = shop_input($input, 'quick_pickup_present') === '1'
        ? (shop_input($input, 'quick_pickup') === '1' ? '1' : '0')
        : ($original['quick_pickup'] ?? ($tables['products'][$positions[$copy] ?? -1]['quick_pickup'] ?? ''));
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
    $offerMedia = array_column($tables['offer_media'], null, 'offer_id');
    $inventory = [];
    $editedOffers = [];
    foreach (is_array($input['offers'] ?? null) ? $input['offers'] : [] as $key => $draft) {
        if (!is_array($draft)) throw shop_error('row', shop_label('offers'));
        $offer = $offers[$key] ?? null;
        $sku = shop_input($draft, 'sku');
        $price = shop_input($draft, 'price');
        $oldPrice = shop_input($draft, 'old_price');
        $variant = shop_input($draft, 'variant');
        $barcode = shop_input($draft, 'barcode');
        $barcode = preg_replace('/[\p{Z}\s]+/u', '', $barcode) ?? $barcode;
        $image = shop_input($draft, 'image');
        $quantities = is_array($draft['inventory'] ?? null) ? $draft['inventory'] : [];
        if ($key === '_new' && $sku === '' && $price === '' && $oldPrice === '' && $variant === '' && $barcode === '' && $image === ''
            && (int) shop_input($draft, 'stock') === 0 && !array_filter($quantities)) continue;
        if (($key !== '_new' && (!$offer || $offer['product_id'] !== ($copy !== '' ? $copy : $id)))
            || $sku === '' || strlen($sku) > 150 || strlen($variant) > 500 || preg_match('//u', $sku . $variant) !== 1
            || strpbrk($sku . $variant, "\0\x1f") !== false) throw shop_error('row', shop_label('offers'));
        if ($barcode !== '' && !preg_match('/\A[0-9]{1,128}\z/', $barcode)
            && ($copy !== '' || $barcode !== ($barcodes[$key]['barcode'] ?? ''))) {
            throw shop_error('row', shop_label('product_barcode'));
        }
        $offerId = $key === '_new' || $copy !== '' ? 'offer-' . bin2hex(random_bytes(12)) : (string) $key;
        if ($copy !== '' && $offer) $offerMap[$key] = $offerId;
        if (array_key_exists('image', $draft)) {
            if ($image !== '' && (!shop_image_valid($image) || (shop_image_name($image) && !is_file(SHOP_IMAGES_DIR . '/' . $image)))) {
                throw shop_error('photo_invalid');
            }
            unset($offerMedia[$offerId]);
            if ($image !== '') $offerMedia[$offerId] = ['offer_id'=>$offerId,'image'=>$image];
        } elseif ($copy !== '' && isset($offerMedia[$key])) {
            $offerMedia[$offerId] = ['offer_id'=>$offerId,'image'=>$offerMedia[$key]['image']];
        }
        $editedOffers[$offerId] = true;
        unset($barcodes[$offerId]);
        if ($barcode !== '') $barcodes[$offerId] = ['offer_id'=>$offerId,'barcode'=>$barcode];
        foreach ($quantities as $warehouseId => $quantity) {
            if (!isset($warehouseIds[$warehouseId]) || !is_string($quantity)
                || ($quantity !== '' && !preg_match('/\A[0-9]{1,8}\z/', $quantity))) throw shop_error('row', shop_label('product_stock'));
            if ((int) $quantity > 0) $inventory[] = ['offer_id'=>$offerId,'warehouse_id'=>$warehouseId,'quantity'=>(string) (int) $quantity];
        }
        $offers[$offerId] = ['id'=>$offerId,'product_id'=>$product['id'],'sku'=>$sku,'variant'=>$variant,
            'price_minor'=>shop_positive_price(shop_import_amount($price), $sku),'stock'=>shop_input($draft, 'stock'),'active'=>shop_input($draft, 'active'),
            'old_price_minor'=>array_key_exists('old_price', $draft)
                ? ($oldPrice === '' ? '' : shop_positive_price(shop_import_amount($oldPrice), shop_label('old_price'))) : ($offer['old_price_minor'] ?? '')];
    }
    $tables['offers'] = array_values($offers);
    $tables['barcodes'] = array_values($barcodes);
    $tables['offer_media'] = array_values($offerMedia);
    $tables['inventory'] = array_merge(array_values(array_filter($tables['inventory'], static function (array $row) use ($editedOffers): bool {
        return !isset($editedOffers[$row['offer_id']]);
    })), $inventory);
    if ($copy !== '') shop_admin_product_copy_data($tables, $copy, $product['id'], $offerMap);
    if (shop_input($input, 'gallery_present') === '1') {
        $gallery = $input['gallery'] ?? [];
        if (!is_array($gallery) || count($gallery) > 50) throw shop_error('row', shop_label('record_gallery'));
        $photos = [];
        foreach ($gallery as $entry) {
            if (!is_array($entry)) throw shop_error('row', shop_label('record_gallery'));
            $image = shop_input($entry, 'image');
            $caption = shop_input($entry, 'alt');
            if ($image === '') continue;
            if (!shop_image_valid($image) || strlen($image) > 2048 || strlen($caption) > 480
                || preg_match('/\A[^\p{C}]*\z/u', $caption) !== 1
                || (shop_image_name($image) && !is_file(SHOP_IMAGES_DIR . '/' . $image))) throw shop_error('row', shop_label('record_gallery'));
            $photos[$image] = ['product_id'=>$product['id'],'image'=>$image,'alt'=>$caption];
        }
        $tables['images'] = array_merge(array_values(array_filter($tables['images'], static function (array $row) use ($product): bool {
            return $row['product_id'] !== $product['id'];
        })), array_values($photos));
    }
    shop_admin_product_characteristics($tables, $product, $input['characteristics'] ?? []);
    $feedBrand = shop_input($input, 'feed_brand');
    if ($feedBrand !== '') {
        $brandPosition = array_search('brand', array_column($tables['fields'], 'id'), true);
        if ($brandPosition === false) {
            $brandPosition = count($tables['fields']);
            $tables['fields'][] = ['id'=>'brand','category_id'=>'','name'=>shop_label('brand'),
                'type'=>'list','target'=>'product','filter'=>'1','sort'=>'0','unit'=>''];
        }
        $field = $tables['fields'][$brandPosition];
        $brandOffer = shop_input($input, 'feed_brand_offer');
        if (strlen($feedBrand) > 180 || preg_match('/[\p{C}]/u', $feedBrand) !== 0
            || !site_iblock_validate_cell($feedBrand, shop_field_type($field['type']))
            || ($field['target'] === 'offer' && ($offers[$brandOffer]['product_id'] ?? '') !== $product['id'])) {
            throw shop_error('row', shop_label('brand'));
        }
        $target = $field['target'] === 'offer' ? $brandOffer : $product['id'];
        if ($field['category_id'] !== '' && !shop_product_has_field(shop_index(true), $product['id'], 'brand')) {
            $tables['fields'][$brandPosition]['category_id'] = '';
        }
        $tables['values'] = array_values(array_filter($tables['values'], static function (array $row) use ($target): bool {
            return $row['field_id'] !== 'brand' || $row['target_id'] !== $target;
        }));
        $tables['values'][] = ['field_id'=>'brand','target_id'=>$target,'value'=>$feedBrand];
    }
    if (is_array($input['seo'] ?? null)) {
        $meta = ['product_id'=>$product['id']];
        foreach (SHOP_SEO_LIMITS as $key => $limit) {
            $value = shop_input($input['seo'], $key);
            $meta[$key] = preg_replace('/\s+/u', ' ', $value) ?? $value;
        }
        $meta['noindex'] = shop_input($input['seo'], 'noindex') === '1' ? '1' : '0';
        $seo = array_column($tables['product_seo'], null, 'product_id');
        if ($meta['noindex'] === '1' || array_filter(array_intersect_key($meta, SHOP_SEO_LIMITS), 'strlen')) $seo[$product['id']] = $meta;
        else unset($seo[$product['id']]);
        $tables['product_seo'] = array_values($seo);
    }
    if ($original) $tables['products'][$positions[$id]] = $product;
    else array_unshift($tables['products'], $product);
}

function shop_admin_product_characteristics(array &$tables, array $product, $rows): void
{
    if (!is_array($rows) || count($rows) > 25) throw shop_error('row', shop_label('fields'));
    if (!$rows) return;
    $parents = array_column($tables['categories'], 'parent_id', 'id');
    $category = $product['category_id'];
    if ($category === '' || !isset($parents[$category])) throw shop_error('row', shop_label('categories'));
    $scopes = ['' => true];
    for ($id = $category; $id !== '' && !isset($scopes[$id]); $id = $parents[$id] ?? '') $scopes[$id] = true;
    $fields = [];
    $types = shop_field_types();
    foreach ($tables['fields'] as $field) {
        $scope = $field['category_id'];
        if ($field['target'] === 'product' && isset($scopes[$scope])) {
            $key = shop_lower($field['name']);
            if (!isset($fields[$key]) || $scope === $category) $fields[$key] = $field;
        }
    }
    foreach ($rows as $row) {
        if (!is_array($row)) throw shop_error('row', shop_label('fields'));
        $name = shop_input($row, 'name');
        $unit = shop_input($row, 'unit');
        $icon = shop_input($row, 'icon');
        $value = str_replace(["\r\n", "\r"], "\n", shop_input($row, 'value'));
        $type = shop_input($row, 'type');
        if ($name === '' && $unit === '' && $value === '' && $icon === '') continue;
        if ($name === '' || $value === '' || strlen($name) > 180 || strlen($unit) > 80 || strlen($value) > 500
            || ($type !== '' && !isset($types[$type])) || ($icon !== '' && site_parse_page_icon($icon) === null)
            || preg_match('/[\x00-\x1f\x7f]/u', $name . $unit) !== 0) {
            throw shop_error('row', shop_label('fields'));
        }
        $key = shop_lower($name);
        $field = $fields[$key] ?? null;
        if ($field === null) {
            $field = ['id'=>'field-' . bin2hex(random_bytes(12)), 'category_id'=>$category, 'name'=>$name,
                'type'=>$type === '' ? 'text' : $type, 'target'=>'product', 'filter'=>'0', 'sort'=>'0', 'unit'=>$unit, 'icon'=>$icon];
            $tables['fields'][] = $fields[$key] = $field;
        } elseif (($unit !== '' && $unit !== ($field['unit'] ?? '')) || ($icon !== '' && $icon !== ($field['icon'] ?? ''))
            || ($type !== '' && $type !== shop_field_type($field['type']))) {
            throw shop_error('row', shop_field_label($field));
        }
        $type = shop_field_type($field['type']);
        if ($type === 'price') $value = shop_price_input((int) shop_positive_price(shop_import_amount($value)));
        if ($type === 'number') $value = shop_characteristic_number($value);
        if (!site_iblock_validate_cell($value, $type)) throw shop_error('row', shop_field_label($field));
        foreach ($tables['values'] as $position => $stored) {
            if ($stored['field_id'] !== $field['id'] || $stored['target_id'] !== $product['id']) continue;
            if ($field['type'] !== 'list' || $stored['value'] === $value) unset($tables['values'][$position]);
        }
        $tables['values'][] = ['field_id'=>$field['id'], 'target_id'=>$product['id'], 'value'=>$value];
        $tables['values'] = array_values($tables['values']);
    }
}

function shop_characteristic_number(string $value): string
{
    $number = str_replace(',', '.', preg_replace('/[\p{Z}\s]+/u', '', $value) ?? '');
    if ($number === '' || strlen($number) > 500 || !site_iblock_validate_cell($number, 'number')) {
        throw shop_error('number_format');
    }
    return $number;
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
        'prices'=>'offer_id','kits'=>'offer_id','barcodes'=>'offer_id','inventory'=>'offer_id','offer_media'=>'offer_id','product_seo'=>'product_id'] as $sheet => $column) {
        $tables[$sheet] = array_values(array_filter($tables[$sheet], static function (array $row) use ($column, $id, $offers): bool {
            return $column === 'offer_id' ? !isset($offers[$row[$column]]) : $row[$column] !== $id;
        }));
    }
    $fieldTargets = array_column($tables['fields'], 'target', 'id');
    $tables['values'] = array_values(array_filter($tables['values'], static function (array $row) use ($fieldTargets, $id, $offers): bool {
        return ($fieldTargets[$row['field_id']] ?? '') === 'offer' ? !isset($offers[$row['target_id']]) : $row['target_id'] !== $id;
    }));
}

function shop_admin_product_bulk(array &$tables, array $input): void
{
    $operation = shop_input($input, 'operation');
    $ids = $input['products'] ?? null;
    if (!in_array($operation, ['activate','deactivate','delete'], true) || !is_array($ids)
        || !$ids || count($ids) > 20 || count(array_filter($ids, 'is_string')) !== count($ids)) {
        throw shop_error('row');
    }
    $selected = array_fill_keys($ids, true);
    $products = array_column($tables['products'], null, 'id');
    if (array_diff_key($selected, $products)) throw shop_error('not_found');
    if ($operation === 'delete') {
        if (shop_input($input, 'confirm') !== '1') throw shop_error('bulk_confirm');
        $offers = [];
        foreach ($tables['offers'] as $offer) {
            if (isset($selected[$offer['product_id']])) $offers[$offer['id']] = true;
        }
        $tables['kits'] = array_values(array_filter($tables['kits'], static function (array $kit) use ($offers): bool {
            return !isset($offers[$kit['offer_id']]);
        }));
        foreach (array_keys($selected) as $id) shop_admin_product_delete($tables, (string) $id);
        return;
    }
    foreach ($tables['products'] as &$product) {
        if (isset($selected[$product['id']])) $product['active'] = $operation === 'activate' ? '1' : '0';
    }
    unset($product);
}
