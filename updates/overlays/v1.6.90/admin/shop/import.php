<?php
if (!defined('SITE_ROOT')) { http_response_code(404); exit; }

function shop_import_csv(string $csv, string $delimiter, string $mapping): array
{
    $rows = shop_csv_read($csv, $delimiter);
    if (!$rows) throw shop_error('csv');
    $headers = array_map('trim', array_shift($rows));
    if (in_array('', $headers, true) || count(array_unique($headers)) !== count($headers)) throw shop_error('header', 'CSV');
    $map = array_combine($headers, $headers);
    foreach (preg_split('/\R/', $mapping) as $line) {
        if (trim($line) === '') continue;
        $parts = array_map('trim', explode('=', $line, 2));
        if (count($parts) !== 2 || !in_array($parts[1], $headers, true)) throw shop_error('header', $line);
        $map[$parts[0]] = $parts[1];
    }
    if (!isset($map['sku'], $map['name']) || (!isset($map['price']) && !isset($map['price_minor']))) throw shop_error('header', 'sku, name, price / price_minor');
    $offers = [];
    foreach ($rows as $n => $row) {
        if (count($row) !== count($headers)) throw shop_error('row', 'CSV #' . ($n + 2));
        $row = array_combine($headers, array_map('trim', $row));
        $offer = [];
        foreach ($map as $key => $column) $offer[$key] = $row[$column];
        $offers[] = $offer;
    }
    return ['categories'=>[], 'offers'=>$offers];
}

function shop_import_yml(string $text): array
{
    if (!class_exists('XMLReader') || !function_exists('simplexml_load_string')) throw shop_error('xml_extension');
    if (strlen($text) > SHOP_LIMIT || stripos($text, '<!ENTITY') !== false || strpos($text, "\0") !== false) throw shop_error('csv');
    $reader = new XMLReader();
    $previous = libxml_use_internal_errors(true);
    $catalog = ['categories'=>[], 'offers'=>[]];
    try {
        if (!$reader->XML($text, null, LIBXML_NONET)) throw shop_error('csv');
        $yml = false;
        while ($reader->read()) {
            if ($reader->nodeType !== XMLReader::ELEMENT) continue;
            if ($reader->name === 'yml_catalog') $yml = true;
            if (!in_array($reader->name, ['category', 'offer'], true)) continue;
            $xml = simplexml_load_string($reader->readOuterXml(), 'SimpleXMLElement', LIBXML_NONET);
            if ($xml === false) throw shop_error('csv');
            if ($reader->name === 'category') {
                $catalog['categories'][] = ['id'=>(string) $xml['id'], 'parent'=>(string) $xml['parentId'], 'name'=>trim((string) $xml)];
                continue;
            }
            $id = (string) $xml['id'];
            $offer = ['external'=>$id, 'group'=>(string) $xml['group_id'], 'sku'=>(string) ($xml->vendorCode ?: $id),
                'name'=>(string) ($xml->name ?: $xml->model), 'brand'=>(string) $xml->vendor, 'price'=>(string) $xml->price,
                'old_price'=>(string) $xml->oldprice, 'currency'=>(string) $xml->currencyId, 'category'=>(string) $xml->categoryId,
                'description'=>(string) $xml->description, 'stock'=>isset($xml->count) ? (string) $xml->count : '',
                'active'=>(string) $xml['available'] === 'false' ? '0' : '1',
                'pictures'=>array_map('strval', iterator_to_array($xml->picture, false))];
            foreach ($xml->param as $param) $offer['parameters'][(string) $param['name']] = (string) $param;
            $catalog['offers'][] = $offer;
            if (count($catalog['offers']) > 100000) throw shop_error('csv');
        }
        if (!$yml || libxml_get_last_error()) throw shop_error('csv');
    } finally {
        $reader->close();
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
    }
    return $catalog;
}

function shop_safe_description(string $text): string
{
    return nl2br(str_replace(['[', ']'], ['&#91;', '&#93;'], site_escape($text)), false);
}

function shop_import_amount(string $amount): string
{
    $amount = str_replace(',', '.', preg_replace('/[\p{Z}\s]+/u', '', $amount) ?? '');
    if (strpos($amount, '.') === 0) $amount = '0' . $amount;
    if (!preg_match('/\A([0-9]{1,7})(?:\.([0-9]{0,2}))?\z/', $amount, $parts)) throw shop_error('price_format');
    return (string) ((int) $parts[1] * 100 + (int) str_pad($parts[2] ?? '', 2, '0'));
}

function shop_import_merge(array $tables, array $source, string $supplier, array $settings, bool $descriptions, string $mapping): array
{
    $idPrefix = 'imp-' . substr(hash('sha256', $supplier), 0, 8) . '-';
    $existing = [];
    foreach (['products','offers','categories','fields'] as $sheet) {
        $existing[$sheet] = array_column($tables[$sheet], null, 'id');
    }
    $root = $idPrefix . 'root';
    $existing['categories'][$root] = $existing['categories'][$root] ?? ['id'=>$root,'parent_id'=>'','slug'=>$root,'name'=>$supplier,
        'sort'=>'0','descendants'=>'1','description'=>''];
    foreach ($source['categories'] as $category) {
        $id = $idPrefix . substr(hash('sha256', $category['id']), 0, 16);
        $parent = $category['parent'] === '' ? $root : $idPrefix . substr(hash('sha256', $category['parent']), 0, 16);
        $existing['categories'][$id] = $existing['categories'][$id] ?? ['id'=>$id,'parent_id'=>$parent,
            'slug'=>substr(shop_slug($category['name']) ?: 'section',0,55) . '-' . substr($id, -6), 'name'=>$category['name'],
            'sort'=>'0', 'descendants'=>'1', 'description'=>''];
    }
    $fieldMapping = [];
    foreach (preg_split('/\R/', $mapping) as $line) {
        if (strpos($line, 'field:') !== 0) continue;
        $pair = array_map('trim', explode('=', substr($line, 6), 2));
        if (count($pair) !== 2 || !isset($existing['fields'][$pair[0]])) throw shop_error('header', $line);
        $fieldMapping[$pair[0]] = $pair[1];
    }
    $skus = [];
    foreach ($existing['offers'] as $id => $offer) $skus[shop_lower($offer['sku'])] = (string) $id;
    $values = [];
    foreach ($tables['values'] as $value) $values[$value['field_id']][$value['target_id']][] = $value['value'];
    $prices = [];
    foreach ($tables['prices'] as $price) $prices[$price['offer_id']][$price['type']] = $price['price_minor'];
    $photos = [];
    foreach ($tables['images'] as $picture) $photos[$picture['product_id']][$picture['image']] = $picture['alt'];
    $first = [];
    $seen = [];
    $report = ['new'=>0,'updated'=>0];
    foreach ($source['offers'] as $row) {
        $sku = $row['sku'] ?? '';
        $name = $row['name'] ?? '';
        if ($sku === '' || $name === '' || isset($seen[shop_lower($sku)])) throw shop_error('row', $sku);
        $seen[shop_lower($sku)] = true;
        $currency = $row['currency'] ?? '';
        if ($currency !== '' && ($currency === 'RUR' ? 'RUB' : $currency) !== $settings['currency']) throw shop_error('currency_mismatch', $sku);
        $external = ($row['external'] ?? '') ?: $sku;
        $id = $idPrefix . substr(hash('sha256', $external), 0, 18);
        if (!isset($existing['offers'][$id])) $id = $skus[shop_lower($sku)] ?? $id;
        $old = $existing['offers'][$id] ?? null;
        $report[$old ? 'updated' : 'new']++;
        $key = ($row['group'] ?? '') ?: $external;
        $productId = $old['product_id'] ?? $idPrefix . 'p' . substr(hash('sha256', $key), 0, 18);
        $cat = $row['category'] ?? '';
        $category = isset($existing['categories'][$cat]) ? $cat : $idPrefix . substr(hash('sha256', $cat), 0, 16);
        if (!isset($existing['categories'][$category])) $category = $root;
        $pictures = array_values(array_filter($row['pictures'] ?? [($row['image'] ?? '')], static function (string $src): bool {
            return $src !== '' && shop_image_valid($src);
        }));
        $product = $existing['products'][$productId] ?? null;
        if (!$product || ($descriptions && !isset($first[$productId]))) {
            $product = ['id'=>$productId, 'category_id'=>$product['category_id'] ?? $category,
                'slug'=>$product['slug'] ?? (substr(shop_slug($name) ?: 'item',0,55) . '-' . substr($productId, -8)),
                'name'=>$name, 'summary'=>strip_tags($row['summary'] ?? ''), 'description'=>shop_safe_description(strip_tags($row['description'] ?? '')),
                'image'=>array_shift($pictures) ?? ($product['image'] ?? ''), 'created'=>$product['created'] ?? gmdate('Y-m-d'),
                'type'=>$product['type'] ?? 'physical', 'active'=>$product['active'] ?? '1'];
        }
        if ($descriptions || !$old) foreach ($pictures as $picture) $photos[$productId][$picture] = $name;
        $first[$productId] = true;
        $existing['products'][$productId] = $product;
        $existing['offers'][$id] = ['id'=>$id,'product_id'=>$productId,'sku'=>$sku,
            'variant'=>$row['variant'] ?? ($old['variant'] ?? ''),
            'price_minor'=>shop_positive_price($row['price_minor'] ?? shop_import_amount($row['price'] ?? ''), $sku),
            'stock'=>($row['stock'] ?? '') !== '' ? $row['stock'] : ($old['stock'] ?? '0'), 'active'=>($row['active'] ?? '') ?: (($row['active'] ?? '') === '0' ? '0' : '1')];
        if (($row['old_price'] ?? '') !== '') $prices[$id]['old'] = shop_positive_price(shop_import_amount($row['old_price']), $sku);
        if (($row['brand'] ?? '') !== '') {
            $existing['fields']['brand'] = $existing['fields']['brand'] ?? ['id'=>'brand','category_id'=>'','name'=>shop_label('brand'),'type'=>'list',
                'target'=>'product','filter'=>'1','sort'=>'0'];
            $values['brand'][$productId] = [$row['brand']];
        }
        foreach ($fieldMapping as $field => $column) {
            $value = $row['parameters'][$column] ?? $row[$column] ?? '';
            if ($value !== '') $values[$field][$existing['fields'][$field]['target'] === 'product' ? $productId : $id] = [$value];
        }
    }
    foreach ($existing as $sheet => $entries) $tables[$sheet] = array_values($entries);
    $tables['values'] = $tables['prices'] = $tables['images'] = [];
    foreach ($values as $field => $targets) foreach ($targets as $target => $entries) foreach ($entries as $value) {
        $tables['values'][] = ['field_id'=>(string) $field,'target_id'=>(string) $target,'value'=>$value];
    }
    foreach ($prices as $id => $entries) foreach ($entries as $kind => $value) {
        $tables['prices'][] = ['offer_id'=>(string) $id,'type'=>(string) $kind,'price_minor'=>$value];
    }
    foreach ($photos as $id => $entries) foreach ($entries as $url => $alt) {
        $tables['images'][] = ['product_id'=>(string) $id,'image'=>(string) $url,'alt'=>$alt];
    }
    return [$tables, $report];
}
