<?php
if (!defined('SITE_ROOT')) { http_response_code(404); exit; }

function shop_admin_action(array $input, array $uploads, bool $owner, array $roles = []): void
{
    $action = shop_input($input, 'action');
    $catalog = $owner || in_array('catalog', $roles, true);
    $orders = $owner || in_array('orders', $roles, true);
    if (($action === 'shop-status' && !$orders)
        || (in_array($action, ['shop-sheet','shop-import','shop-apply','shop-photo','shop-reindex'], true) && !$catalog)
        || (in_array($action, ['shop-settings','shop-description','shop-product-save','shop-product-delete','shop-warehouse-add'], true) && !$owner)) throw shop_error('forbidden');
    $lock = shop_lock();
    try {
        if ($action === 'shop-settings') {
            $settings = shop_settings();
            $catalogPage = shop_input($input, 'catalog');
            if ($catalogPage !== '' && (!site_valid_path($catalogPage) || site_read_page($catalogPage) === null)) throw shop_error('row', 'catalog');
            $settings['catalog'] = $catalogPage;
            $settings['guest'] = shop_input($input, 'guest') === '1';
            $settings['shipping'] = (int) shop_import_amount(shop_input($input, 'shipping'));
            $settings['pageSize'] = min(96, max(1, (int) shop_input($input, 'pageSize')));
            $currency = shop_input($input, 'currency');
            if (!in_array($currency, ['RUB','USD','EUR','GBP','UAH','BYN','PLN','KZT','CNY'], true)) throw shop_error('row', 'currency');
            $settings['currency'] = $currency;
            $settings['templates'] = [];
            foreach (shop_defaults() as $key => $default) {
                $draft = shop_input(is_array($input['template'] ?? null) ? $input['template'] : [], $key);
                if ($draft !== '' && $draft !== $default) {
                    if (strlen($draft) > 16000 || strpos($draft, '<?') !== false) throw shop_error('row', $key);
                    $settings['templates'][$key] = $draft;
                }
            }
            $settings['priceLabels'] = [];
            foreach (preg_split('/\R/', shop_input($input, 'priceLabels')) as $line) {
                if (trim($line) === '') continue;
                $parts = array_map('trim', explode('=', $line, 2));
                if (count($parts) !== 2 || !shop_id($parts[0]) || $parts[1] === '') throw shop_error('row', $line);
                $settings['priceLabels'][$parts[0]] = $parts[1];
            }
            $settings['mail'] = site_message_template(is_array($input['mail'] ?? null) ? $input['mail'] : [], shop_mail_defaults());
            admin_write_record(SHOP_DIR . '/settings.php', $settings, 262144);
            return;
        }
        if ($action === 'shop-status') {
            shop_order_status(shop_input($input, 'order'), shop_input($input, 'status'));
            return;
        }
        if ($action === 'shop-photo') {
            shop_upload_photo($uploads['photo'] ?? []);
            return;
        }
        $tables = shop_tables();
        if (in_array($action, ['shop-product-save','shop-product-delete','shop-warehouse-add'], true)) {
            if (!hash_equals(shop_generation(), shop_input($input, 'revision'))) throw shop_error('conflict');
            if ($action === 'shop-warehouse-add') {
                $name = shop_input($input, 'warehouse');
                if ($name === '' || strlen($name) > 240 || preg_match('/\A[^\p{C}]+\z/u', $name) !== 1) throw shop_error('row', shop_label('warehouses'));
                foreach ($tables['warehouses'] as $warehouse) {
                    if (shop_lower($warehouse['name']) === shop_lower($name)) throw shop_error('warehouse_exists');
                }
                $tables['warehouses'][] = ['id'=>'warehouse-' . bin2hex(random_bytes(12)), 'name'=>$name];
            } else {
                require_once __DIR__ . '/admin-products.php';
                shop_admin_product_change($tables, $input, $action === 'shop-product-delete');
            }
        } elseif (in_array($action, ['shop-sheet','shop-description'], true)) {
            if (!hash_equals(shop_generation(), shop_input($input, 'revision'))) throw shop_error('conflict');
            if ($action === 'shop-description') {
                $id = shop_input($input,'id');
                $found = false;
                foreach ($tables['products'] as &$product) {
                    if ($product['id'] !== $id) continue;
                    $description = shop_input($input,'description');
                    if (strlen($description) > SITE_PAGE_LIMIT || preg_match('//u', $description) !== 1 || strpos($description, "<?") !== false) throw shop_error('row');
                    $product['description'] = $description;
                    $product['summary'] = substr(shop_input($input,'summary'),0,5000);
                    $found = true;
                    break;
                }
                unset($product);
                if (!$found) throw shop_error('not_found');
                shop_publish($tables, shop_catalog_build($tables));
                return;
            }
            $sheet = shop_input($input, 'sheet');
            if (!isset(SHOP_COLUMNS[$sheet]) || in_array($sheet, ['warehouses','inventory','barcodes'], true)) throw shop_error('row', $sheet);
            $rows = shop_rows(shop_input($input, 'csv'), SHOP_COLUMNS[$sheet], $sheet);
            if ($sheet === 'products') {
                $before = array_column($tables['products'], 'slug', 'id');
                $existingAliases = array_column($tables['aliases'], null, 'slug');
                foreach ($rows as $row) {
                    $id = $row['id'];
                    if (isset($before[$id]) && $before[$id] !== $row['slug']) $existingAliases[$before[$id]] = ['slug'=>$before[$id],'product_id'=>$id];
                }
                $currentIds = array_fill_keys(array_column($rows, 'id'), true);
                $currentSlugs = array_fill_keys(array_column($rows, 'slug'), true);
                $tables['aliases'] = array_values(array_filter($existingAliases, static function (array $alias) use ($currentIds, $currentSlugs): bool {
                    return isset($currentIds[$alias['product_id']]) && !isset($currentSlugs[$alias['slug']]);
                }));
            }
            if ($sheet === 'products' && !$owner) {
                $original = array_column($tables['products'], 'description', 'id');
                foreach ($rows as &$row) if (($original[$row['id']] ?? '') !== $row['description']) $row['description'] = shop_safe_description($row['description']);
                unset($row);
            }
            $tables[$sheet] = $rows;
        } elseif ($action === 'shop-import') {
            $upload = $uploads['catalog_file'] ?? [];
            if (($upload['error'] ?? null) !== UPLOAD_ERR_OK || !is_uploaded_file($upload['tmp_name'])
                || $upload['size'] > SHOP_LIMIT) throw shop_error('csv');
            $bytes = file_get_contents($upload['tmp_name']);
            $supplier = shop_input($input, 'supplier');
            if ($bytes === false || $supplier === '' || strlen($supplier) > 120) throw shop_error('csv');
            $format = shop_input($input, 'format');
            $separator = ['pipe'=>'|','comma'=>',','semicolon'=>';','tab'=>"\t"][shop_input($input, 'separator')] ?? '|';
            $mapping = shop_input($input, 'mapping');
            $source = $format === 'yml' ? shop_import_yml($bytes) : shop_import_csv($bytes, $separator,
                implode("\n", array_filter(explode("\n", $mapping), static function (string $line): bool { return strpos($line, 'field:') !== 0; })));
            [$tables, $report] = shop_import_merge($tables, $source, $supplier, shop_settings(), shop_input($input,'descriptions') === '1', $mapping);
            shop_catalog_build($tables);
            $id = bin2hex(random_bytes(16));
            foreach (glob(SHOP_DIR . '/preview-*.php') ?: [] as $file) if (filemtime($file) < time() - 7200 && !is_link($file)) @unlink($file);
            admin_write_record(SHOP_DIR . '/preview-' . $id . '.php', ['revision'=>shop_generation(),'tables'=>$tables], SHOP_LIMIT);
            $_SESSION['shopImportPreview'] = ['id'=>$id,'report'=>$report];
            return;
        } elseif ($action === 'shop-apply') {
            $id = $_SESSION['shopImportPreview']['id'] ?? '';
            if (!preg_match('/\A[a-f0-9]{32}\z/', $id)) throw shop_error('not_found');
            $file = SHOP_DIR . '/preview-' . $id . '.php';
            $preview = site_read_record($file, SHOP_LIMIT);
            if (!$preview || !hash_equals(shop_generation(), $preview['revision'])) throw shop_error('conflict');
            $tables = $preview['tables'];
        } elseif ($action !== 'shop-reindex') throw shop_error('not_found');
        shop_publish($tables, shop_catalog_build($tables));
        if ($action === 'shop-apply') { @unlink($file); unset($_SESSION['shopImportPreview']); }
    } finally {
        fclose($lock);
    }
}

function shop_upload_photo(array $photo): void
{
    $file = $photo['tmp_name'] ?? '';
    if (($photo['error'] ?? null) !== UPLOAD_ERR_OK || !is_uploaded_file($file) || $photo['size'] > 12000000) throw shop_error('photo_invalid');
    $info = @getimagesize($file);
    if (!$info || $info[0] > 8000 || $info[1] > 8000) throw shop_error('photo_invalid');
    $extension = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','image/gif'=>'gif','image/avif'=>'avif'][$info['mime']] ?? '';
    if ($extension === '') throw shop_error('photo_invalid');
    $path = __DIR__ . '/images';
    shop_directory($path);
    $name = bin2hex(random_bytes(12)) . '.' . $extension;
    if (!move_uploaded_file($file, $path . '/' . $name)) throw shop_error('storage');
    $_SESSION['shopUploaded'] = $name;
}
