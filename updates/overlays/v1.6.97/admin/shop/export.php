<?php
if (!defined('SITE_ROOT')) { http_response_code(404); exit; }

const SHOP_EXPORT_FORMATS = [
    'csv'=>['csv','text/csv; charset=UTF-8','file-csv'],
    'xlsx'=>['xlsx','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','file-excel'],
    'tsv'=>['tsv','text/tab-separated-values; charset=UTF-8','table'],
    'json'=>['json','application/json; charset=UTF-8','code'],
    'yml'=>['yml','application/xml; charset=UTF-8','rss'],
];

function shop_export_key(array $settings, string $format): string
{
    $secret = $settings['exportSecret'] ?? '';
    return !empty($settings['exportEnabled']) && is_string($secret) && preg_match('/\A[0-9a-f]{64}\z/', $secret)
        ? hash_hmac('sha256', 'marketplace-feed:' . $format, hex2bin($secret)) : '';
}

function shop_export_root(string $base): string
{
    $url = site_settings()['publicUrl'] ?: site_canonical_url($base, 'index.php');
    return rtrim($url, '/') . '/';
}

function shop_export_url(string $root, string $base, string $path): string
{
    if ($path === '' || $path[0] !== '/') return $path;
    $prefix = rtrim($base, '/') . '/';
    return $root . ltrim(strpos($path, $prefix) === 0 ? substr($path, strlen($prefix)) : $path, '/');
}

function shop_export_ids(array $rows): array
{
    $ids = [];
    foreach (array_keys($rows) as $id) $ids[$id] = sprintf('%.0F', 1 + hexdec(substr(hash('sha256', (string) $id), 0, 12)));
    if (count($ids) !== count(array_unique($ids))) throw shop_error('storage');
    return $ids;
}

function shop_export_plain(string $html): string
{
    $text = preg_replace('~</(?:p|div|li|h[1-6])>|<br\s*/?>~i', "$0\n", $html);
    return trim(html_entity_decode(strip_tags($text ?? $html), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
}

function shop_export_pictures(array $index, string $product, string $main, string $variant): array
{
    return array_values(array_unique(array_filter(array_merge([$variant, $main],
        array_column($index['images'][$product] ?? [], 'image')), 'strlen')));
}

function shop_export_rows(array $index, array $tables, array $settings, array $committed, string $base, string $root, string $category, bool $storefront): Generator
{
    $barcodes = array_column($tables['barcodes'], 'barcode', 'offer_id');
    $stock = array_column($tables['offers'], 'stock', 'id');
    $attributes = [];
    foreach ($tables['values'] as $entry) {
        $field = $index['fields'][$entry['field_id']];
        if (in_array($field['type'], ['hidden','form'], true)) continue;
        $attributes[$field['target']][$entry['target_id']][] = [
            'name'=>$field['name'], 'unit'=>$field['unit'], 'value'=>$entry['value'],
        ];
    }
    foreach ($index['offers'] as $id => $offer) {
        $product = $index['products'][$offer['product_id']];
        $active = $product['active'] === '1' && $offer['active'] === '1';
        if ($storefront && (!$active || $product['type'] === 'service' || (int) $offer['price_minor'] < 1)) continue;
        if ($category !== '') {
            $matches = false;
            foreach (array_keys($index['placements'][$product['id']]) as $placement) {
                if (in_array($category, array_map('strval', $index['tree'][$placement]), true)) { $matches = true; break; }
            }
            if (!$matches) continue;
        }
        $quantity = $active ? shop_stock($index, $offer, $committed) : 0;
        $price = $storefront ? shop_rounded_price((int) $offer['price_minor'], $settings) : (int) $offer['price_minor'];
        $old = (int) ($offer['old_price_minor'] ?? 0);
        if ($storefront) $old = shop_rounded_price($old, $settings);
        $pictures = shop_export_pictures($index, $product['id'], $product['image'], $index['offerMedia'][$id] ?? '');
        $pictures = array_map(static function (string $picture) use ($root, $base): string {
            return shop_export_url($root, $base, shop_image_url($base, $picture));
        }, $pictures);
        $brand = $index['values']['brand'][($index['fields']['brand']['target'] ?? '') === 'offer' ? $id : $product['id']][0] ?? '';
        yield $id => [
            'sku'=>$offer['sku'], 'name'=>$product['name'], 'variant'=>$offer['variant'], 'group'=>$product['id'],
            'category'=>$product['category_id'], 'category_name'=>$index['categories'][$product['category_id']]['name'],
            'type'=>$product['type'], 'brand'=>$brand, 'barcode'=>$barcodes[$id] ?? '', 'price'=>shop_price_input($price),
            'old_price'=>$old > 0 && (!$storefront || $old > $price) ? shop_price_input($old) : '', 'currency'=>$settings['currency'],
            'stock'=>$storefront ? $quantity : (int) $stock[$id], 'available_quantity'=>$quantity,
            'active'=>$active ? '1' : '0', 'product_active'=>$product['active'], 'offer_active'=>$offer['active'],
            'summary'=>shop_export_plain($product['summary']),
            'description'=>shop_export_plain($product['description']), 'image'=>$pictures[0] ?? '',
            'url'=>shop_export_url($root, $base, shop_product_url($base, $index, $product)),
            'pictures'=>$pictures, 'attributes'=>array_merge($attributes['product'][$product['id']] ?? [], $attributes['offer'][$id] ?? []),
        ];
    }
}

function shop_export_xml(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_XML1 | ENT_SUBSTITUTE | ENT_DISALLOWED, 'UTF-8');
}

function shop_export_yml(array $categories, array $products, iterable $rows, string $root, string $name, string $currency): void
{
    $categoryIds = shop_export_ids($categories);
    $groupIds = shop_export_ids($products);
    $xml = 'shop_export_xml';
    echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<yml_catalog date=\"", gmdate('Y-m-d\TH:i:sP'), "\"><shop>\n";
    echo '<name>', $xml($name), '</name><company>', $xml($name), '</company><url>', $xml($root), "</url>\n";
    echo '<currencies><currency id="', $xml($currency), '" rate="1"/></currencies>', "\n<categories>\n";
    foreach ($categories as $id => $category) {
        echo '<category id="', $categoryIds[$id], '"';
        if ($category['parent_id'] !== '') echo ' parentId="', $categoryIds[$category['parent_id']], '"';
        echo '>', $xml($category['name']), "</category>\n";
    }
    echo "</categories>\n<offers>\n";
    foreach ($rows as $row) {
        echo '<offer id="', $xml($row['sku']), '" group_id="', $groupIds[$row['group']], '" available="',
            $row['available_quantity'] > 0 ? 'true' : 'false', '">';
        $elements = ['name'=>trim($row['name'] . ($row['variant'] === '' ? '' : ' — ' . $row['variant'])),
            'url'=>$row['url'], 'price'=>$row['price'], 'oldprice'=>$row['old_price'], 'currencyId'=>$row['currency'],
            'categoryId'=>$categoryIds[$row['category']], 'vendor'=>$row['brand'], 'barcode'=>$row['barcode'],
            'description'=>$row['description'] ?: $row['summary'], 'count'=>(string) $row['available_quantity']];
        if ($row['type'] === 'digital') $elements['downloadable'] = 'true';
        foreach ($elements as $tag => $value) {
            if ($value !== '') echo '<', $tag, '>', $xml($value), '</', $tag, '>';
        }
        foreach ($row['pictures'] as $picture) echo '<picture>', $xml($picture), '</picture>';
        foreach ($row['attributes'] as $attribute) {
            echo '<param name="', $xml($attribute['name']), '"';
            if ($attribute['unit'] !== '') echo ' units="', $xml($attribute['unit']), '"';
            echo '>', $xml($attribute['value']), '</param>';
        }
        echo "</offer>\n";
    }
    echo "</offers></shop></yml_catalog>\n";
}

const SHOP_EXPORT_COLUMNS = ['sku','name','variant','group','category','category_name','type','brand','barcode','price',
    'old_price','currency','stock','available_quantity','active','product_active','offer_active','summary','description','image','url','pictures','attributes'];

function shop_export_cells(array $row): array
{
    return array_map(static function (string $key) use ($row): string {
        return is_array($row[$key]) ? json_encode($row[$key], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : (string) $row[$key];
    }, SHOP_EXPORT_COLUMNS);
}

function shop_export_delimited(iterable $rows, string $separator): void
{
    $output = fopen('php://output', 'wb');
    fwrite($output, "\xef\xbb\xbf");
    $write = static function (array $values) use ($output, $separator): void {
        fwrite($output, '"' . implode('"' . $separator . '"', array_map(static function (string $value): string {
            return str_replace('"', '""', $value);
        }, $values)) . "\"\r\n");
    };
    $write(SHOP_EXPORT_COLUMNS);
    foreach ($rows as $row) {
        $cells = array_map(static function (string $cell): string {
            return preg_match('/\A(?:[\p{Z}\s\x00-\x20]*[=+\-@]|[\t\r\n])/u', $cell) ? "'" . $cell : $cell;
        }, shop_export_cells($row));
        $write($cells);
    }
    fclose($output);
}

function shop_export_xlsx(iterable $rows): string
{
    if (!class_exists('ZipArchive')) throw shop_error('export_zip');
    $path = tempnam(sys_get_temp_dir(), 'ewc-export-');
    $sheet = tmpfile();
    if ($path === false || $sheet === false) {
        if ($path !== false) unlink($path);
        if ($sheet !== false) fclose($sheet);
        throw shop_error('storage');
    }
    $main = 'http://schemas.openxmlformats.org/spreadsheetml/2006/main';
    $relationships = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships';
    $package = 'http://schemas.openxmlformats.org/package/2006/relationships';
    try {
        fwrite($sheet, '<?xml version="1.0" encoding="UTF-8"?><worksheet xmlns="' . $main . '"><sheetData>');
        $writeRow = static function (array $cells, int $number) use ($sheet): void {
            $xml = '<row r="' . $number . '">';
            foreach ($cells as $column => $value) {
                $ref = chr(65 + $column) . $number;
                if ($number > 1 && in_array(SHOP_EXPORT_COLUMNS[$column], ['price','old_price','stock','available_quantity'], true) && $value !== '') {
                    $xml .= '<c r="' . $ref . '"><v>' . shop_export_xml($value) . '</v></c>';
                } else $xml .= '<c r="' . $ref . '" t="inlineStr"><is><t xml:space="preserve">' . shop_export_xml($value) . '</t></is></c>';
            }
            if (fwrite($sheet, $xml . '</row>') !== strlen($xml) + 6) throw shop_error('storage');
        };
        $writeRow(SHOP_EXPORT_COLUMNS, 1);
        $number = 1;
        foreach ($rows as $row) $writeRow(shop_export_cells($row), ++$number);
        fwrite($sheet, '</sheetData></worksheet>');
        fflush($sheet);
        $zip = new ZipArchive();
        if ($zip->open($path, ZipArchive::OVERWRITE) !== true) throw shop_error('storage');
        $zip->addFromString('[Content_Types].xml', '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">'
            . '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>'
            . '<Default Extension="xml" ContentType="application/xml"/>'
            . '<Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>'
            . '<Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/></Types>');
        $zip->addFromString('_rels/.rels', '<Relationships xmlns="' . $package . '"><Relationship Id="rId1" Type="' . $relationships . '/officeDocument" Target="xl/workbook.xml"/></Relationships>');
        $zip->addFromString('xl/workbook.xml', '<workbook xmlns="' . $main . '" xmlns:r="' . $relationships . '"><sheets><sheet name="Catalog" sheetId="1" r:id="rId1"/></sheets></workbook>');
        $zip->addFromString('xl/_rels/workbook.xml.rels', '<Relationships xmlns="' . $package . '"><Relationship Id="rId1" Type="' . $relationships . '/worksheet" Target="worksheets/sheet1.xml"/></Relationships>');
        $added = $zip->addFile(stream_get_meta_data($sheet)['uri'], 'xl/worksheets/sheet1.xml');
        if (!$zip->close() || !$added) throw shop_error('storage');
        return $path;
    } catch (Throwable $error) {
        unlink($path);
        throw $error;
    } finally {
        fclose($sheet);
    }
}

function shop_export_headers(string $format, bool $public): void
{
    [$extension, $mime] = SHOP_EXPORT_FORMATS[$format];
    header('Content-Type: ' . $mime);
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: no-referrer');
    header('X-Robots-Tag: noindex, nofollow');
    header('Cache-Control: private, no-store');
    if (!$public || in_array($format, ['csv','tsv','xlsx'], true)) {
        header('Content-Disposition: attachment; filename="marketplace-catalog.' . $extension . '"');
    }
}

function shop_export_diagnostics(string $base): array
{
    $settings = shop_settings();
    $tables = shop_tables();
    $index = shop_catalog_build($tables);
    $root = shop_export_root($base);
    $counts = ['feed_missing_image'=>0,'feed_missing_barcode'=>0,'feed_missing_brand'=>0];
    $sample = [];
    $total = 0;
    foreach (shop_export_rows($index, $tables, $settings, [], $base, $root, '', true) as $id => $row) {
        $total++;
        $issues = [];
        foreach (['image'=>'feed_missing_image','barcode'=>'feed_missing_barcode','brand'=>'feed_missing_brand'] as $field => $label) {
            if ($row[$field] !== '') continue;
            $counts[$label]++;
            $issues[] = $label;
        }
        if ($issues && count($sample) < 40) $sample[] = [$row['sku'], $row['name'], $issues, $row['group'], (string) $id];
    }
    return ['total'=>$total,'counts'=>$counts,'sample'=>$sample];
}

function shop_export_send(string $base, string $format, string $category, bool $public): void
{
    $settings = shop_settings();
    $tables = shop_tables();
    $index = shop_catalog_build($tables);
    if ($category !== '' && !isset($index['categories'][$category])) throw shop_error('not_found');
    $root = shop_export_root($base);
    $storefront = $public || $format === 'yml';
    $rows = shop_export_rows($index, $tables, $settings, shop_cached_stock()['committed'],
        $base, $root, $category, $storefront);
    $xlsx = $format === 'xlsx' ? shop_export_xlsx($rows) : null;
    shop_export_headers($format, $public);
    if ($xlsx !== null) {
        try { readfile($xlsx); } finally { unlink($xlsx); }
    } elseif ($format === 'yml') {
        shop_export_yml($index['categories'], $index['products'], $rows, $root, site_settings()['name'], $settings['currency']);
    } elseif ($format === 'json') {
        $flags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE;
        $categories = array_map(static function (array $item): array {
            return array_intersect_key($item, array_flip(['id','parent_id','name']));
        }, array_values($index['categories']));
        echo '{"version":1,"currency":', json_encode($settings['currency']), ',"categories":', json_encode($categories, $flags), ',"offers":[';
        $separator = '';
        foreach ($rows as $row) {
            echo $separator, json_encode($row, $flags);
            $separator = ',';
        }
        echo "]}\n";
    } else {
        shop_export_delimited($rows, $format === 'tsv' ? "\t" : ';');
    }
}
