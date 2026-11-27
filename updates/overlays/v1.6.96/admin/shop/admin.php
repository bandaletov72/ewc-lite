<?php
if (!defined('SITE_ROOT')) { http_response_code(404); exit; }

function shop_admin_price(array $input): array
{
    $id = shop_input($input, 'offer');
    $previous = shop_input($input, 'previous');
    $old = shop_input($input, 'price_field') === 'old_price';
    $column = $old ? 'old_price_minor' : 'price_minor';
    if (!shop_id($id) || ((!$old || $previous !== '') && !preg_match('/\A[0-9]{1,9}\z/', $previous))) throw shop_error('row');
    $value = shop_input($input, 'price');
    $price = $old && $value === '' ? '' : shop_positive_price(shop_import_amount($value));
    $lock = shop_lock();
    try {
        $revision = shop_generation();
        $tables = shop_tables();
        $position = array_search($id, array_column($tables['offers'], 'id'), true);
        if ($position === false) throw new RuntimeException(shop_label('not_found'), 404);
        $offer = $tables['offers'][$position];
        $current = $old ? ($offer[$column] ?? '') : (string) (int) $offer[$column];
        if ($price !== $current) {
            if ($current !== ($old ? $previous : (string) (int) $previous)) throw new RuntimeException(shop_label('conflict'), 409);
            $tables['offers'][$position][$column] = $price;
            shop_publish($tables, shop_catalog_build($tables));
        }
        $prices = [];
        foreach ($tables['offers'] as $variant) {
            if ($variant['product_id'] === $offer['product_id']) $prices[] = (int) ($variant[$column] ?? 0);
        }
        return ['minor'=>$price, 'price'=>$price === '' ? '' : shop_price_input((int) $price),
            'range'=>$old && !array_filter($prices) ? '—' : shop_price_range($prices, shop_settings()),
            'message'=>shop_label('saved'), 'previousRevision'=>$revision, 'revision'=>shop_generation()];
    } finally {
        fclose($lock);
    }
}

function shop_admin_inline_value(array $input): array
{
    $target = shop_input($input, 'target');
    $kind = shop_input($input, 'kind');
    $previous = shop_input($input, 'previous');
    $value = shop_input($input, 'value');
    $detail = shop_input($input, 'detail');
    if (!shop_id($target) || !in_array($kind, ['attribute','stock','barcode'], true)
        || ($detail !== '' && !shop_id($detail))) throw shop_error('row');
    if ($kind !== 'attribute') $value = preg_replace('/[\p{Z}\s]+/u', '', $value) ?? $value;
    $lock = shop_lock();
    try {
        $revision = shop_generation();
        $tables = shop_tables($revision);
        $index = shop_index(true);
        $summary = null;
        if ($kind === 'attribute') {
            $field = $index['fields'][$detail] ?? [];
            $type = $field['type'] ?? '';
            $scope = $field['target'] ?? '';
            $product = $scope === 'offer' ? ($index['offers'][$target]['product_id'] ?? '') : $target;
            if (!in_array($scope, ['product','offer'], true) || !in_array($type, ['number','price'], true)
                || !shop_product_has_field($index, $product, $detail)) throw new RuntimeException(shop_label('not_found'), 404);
            if ($value !== '') {
                $value = $type === 'price' ? shop_price_input((int) shop_positive_price(shop_import_amount($value)))
                    : shop_characteristic_number($value);
            }
            $values = &$tables['values'];
            $current = $index['values'][$detail][$target][0] ?? '';
            $keys = ['field_id'=>$detail,'target_id'=>$target];
            $column = 'value';
        } else {
            $offer = $index['offers'][$target] ?? null;
            if ($offer === null) throw new RuntimeException(shop_label('not_found'), 404);
            if ($kind === 'barcode') {
                $values = &$tables['barcodes'];
                $current = array_column($values, 'barcode', 'offer_id')[$target] ?? '';
                if ($value !== $current && $value !== '' && !preg_match('/\A[0-9]{1,128}\z/', $value)) throw shop_error('digits_format');
                $keys = ['offer_id'=>$target];
                $column = 'barcode';
            } elseif ($detail === '') {
                $values = &$tables['offers'];
                $current = array_column($values, 'stock', 'id')[$target];
                $keys = ['id'=>$target];
                $column = 'stock';
            } else {
                if (!in_array($detail, array_column($tables['warehouses'], 'id'), true)) {
                    throw new RuntimeException(shop_label('not_found'), 404);
                }
                $values = &$tables['inventory'];
                $current = '0';
                foreach ($values as $row) {
                    if ($row['offer_id'] === $target && $row['warehouse_id'] === $detail) $current = $row['quantity'];
                }
                $keys = ['offer_id'=>$target,'warehouse_id'=>$detail];
                $column = 'quantity';
            }
            if ($kind === 'stock') {
                if (!preg_match('/\A[0-9]{1,8}\z/', $value)) throw shop_error('digits_format');
                $value = (string) (int) $value;
            }
        }
        if ($value !== $current) {
            if ($current !== $previous) throw new RuntimeException(shop_label('conflict'), 409);
            $found = false;
            foreach ($values as $i => $row) {
                if (array_intersect_key($row, $keys) !== $keys) continue;
                $found = true;
                if ($value === '' || ($column === 'quantity' && $value === '0')) unset($values[$i]);
                else $values[$i][$column] = $value;
                break;
            }
            if (!$found && $value !== '' && !($column === 'quantity' && $value === '0')) {
                $values[] = $keys + [$column=>$value];
            }
            $values = array_values($values);
            $index = shop_catalog_build($tables);
            shop_publish($tables, $index);
        }
        if ($kind === 'stock' || $kind === 'barcode') {
            $offers = $index['offers'];
            $productId = $offers[$target]['product_id'];
            $barcodes = array_column($tables['barcodes'], 'barcode', 'offer_id');
            $items = [];
            foreach ($offers as $id => $offer) {
                if ($offer['product_id'] === $productId) $items[] = $kind === 'stock' ? (int) $offer['stock'] : ($barcodes[$id] ?? '');
            }
            $summary = $kind === 'stock' ? (string) array_sum($items) : (implode(', ', array_filter($items, 'strlen')) ?: '—');
        }
        return ['value'=>$value, 'summary'=>$summary, 'message'=>shop_label('saved'),
            'previousRevision'=>$revision, 'revision'=>shop_generation()];
    } finally {
        fclose($lock);
    }
}

function shop_admin_action(array $input, array $uploads, bool $owner, array $roles = []): void
{
    $action = shop_input($input, 'action');
    $catalog = $owner || in_array('catalog', $roles, true);
    $orders = $owner || in_array('orders', $roles, true);
    if (in_array($action, ['shop-export-access','shop-checkout-settings'], true) && !$owner) throw shop_error('forbidden');
    if (in_array($action, ['shop-record-save','shop-record-delete'], true) && !$catalog) throw shop_error('forbidden');
    if (strpos($action, 'shop-integration-') === 0) {
        if (!$owner || !function_exists('shop_integration_save')) throw shop_error('forbidden');
        $id = shop_input($input, 'integration');
        if ($action === 'shop-integration-check') {
            shop_integration_check($id);
            $_SESSION['shopIntegrationChecked'] = $id;
            return;
        }
        if ($action === 'shop-integration-refresh') {
            $_SESSION['shopIntegrationCatalog'] = shop_integration_github_catalog();
            return;
        }
        $lock = shop_lock();
        try {
            if ($action === 'shop-integration-save') shop_integration_save($id, $input);
            elseif ($action === 'shop-integration-install') shop_integration_install($id, $_SESSION['shopIntegrationCatalog'] ?? []);
            else throw shop_error('not_found');
        } finally {
            fclose($lock);
        }
        return;
    }
    if (($action === 'shop-status' && !$orders) || ($action === 'shop-status-add' && !$owner)
        || (in_array($action, ['shop-sheet','shop-import','shop-apply','shop-photo','shop-reindex'], true) && !$catalog)
        || (in_array($action, ['shop-settings','shop-fragment-save','shop-filter-settings','shop-brand-save','shop-description','shop-product-save','shop-product-delete','shop-product-bulk','shop-warehouse-add','shop-category-save','shop-category-delete','shop-category-move'], true) && !$owner)) throw shop_error('forbidden');
    $lock = shop_lock();
    $newPhotos = [];
    try {
        if ($action === 'shop-checkout-settings') {
            $settings = shop_settings();
            $form = shop_input($input, 'checkout_form');
            if ($form !== '' && (!site_valid_form_id($form) || !isset(site_forms()[$form]))) throw shop_error('row');
            $pickup = shop_input($input, 'pickup') === '1';
            $shipping = shop_input($input, 'shipping_enabled') === '1';
            if (!$pickup && !$shipping) throw shop_error('delivery_unavailable');
            $address = shop_input($input, 'pickup_address');
            $notes = shop_input($input, 'delivery_notes');
            if (strlen($address) > 500 || strlen($notes) > 4000 || preg_match('/[\p{C}]/u', $address) !== 0
                || preg_match('/[\x00-\x08\x0b\x0c\x0e-\x1f\x7f]/u', $notes) !== 0) throw shop_error('row');
            $settings['checkoutForm'] = $form;
            $settings['shipping'] = (int) shop_import_amount(shop_input($input, 'shipping_fee') ?: '0');
            $settings['deliveryRules'] = ['pickup'=>$pickup,'shipping'=>$shipping,'pickupAddress'=>$address,'notes'=>$notes,
                'minimum'=>(int) shop_import_amount(shop_input($input, 'shipping_minimum') ?: '0'),
                'freeFrom'=>(int) shop_import_amount(shop_input($input, 'shipping_free') ?: '0')];
            admin_write_record(SHOP_DIR . '/settings.php', $settings, 262144);
            return;
        }
        if ($action === 'shop-export-access') {
            $settings = shop_settings();
            $mode = shop_input($input, 'mode');
            if ($mode === 'disable') {
                unset($settings['exportEnabled'], $settings['exportSecret']);
            } elseif ($mode === 'rotate') {
                $settings['exportEnabled'] = true;
                $settings['exportSecret'] = bin2hex(random_bytes(32));
            } else throw shop_error('row');
            admin_write_record(SHOP_DIR . '/settings.php', $settings, 262144);
            return;
        }
        if ($action === 'shop-brand-save') {
            shop_admin_save_brand($input, $uploads['photo'] ?? []);
            return;
        }
        if ($action === 'shop-filter-settings') {
            $settings = shop_settings();
            $position = shop_input($input, 'position');
            if (!in_array($position, ['off','top','left'], true)) throw shop_error('row');
            $settings['filters'] = ['position'=>$position];
            foreach (['price','fields','search','sort','live'] as $key) {
                $settings['filters'][$key] = shop_input($input, $key) === '1';
            }
            admin_write_record(SHOP_DIR . '/settings.php', $settings, 262144);
            return;
        }
        if ($action === 'shop-fragment-save') {
            $settings = shop_settings();
            $key = shop_input($input, 'fragment');
            $defaults = shop_template_fragments();
            if (!isset($defaults[$key])) throw shop_error('not_found');
            $current = shop_fragment_source($key, $settings, true);
            if (!hash_equals(hash('sha256', $current), shop_input($input, 'revision'))) throw shop_error('conflict');
            $draft = shop_migrate_template(shop_input($input, 'markup'));
            if (strlen($draft) > 16000 || preg_match('//u', $draft) !== 1 || strpos($draft, '<?') !== false) throw shop_error('row', shop_label('template_' . $key));
            $default = shop_fragment_source($key, [], true);
            if ($draft === '' || ($draft === $default && ($key !== 'item-list' || empty($settings['templates']['item'])))) unset($settings['templates'][$key]);
            else $settings['templates'][$key] = $draft;
            admin_write_record(SHOP_DIR . '/settings.php', $settings, 262144);
            return;
        }
        if ($action === 'shop-settings') {
            $settings = shop_settings();
            if (array_key_exists('catalog_folder', $input)) {
                $address = shop_input($input, 'catalog_folder');
                $folder = site_shop_folder($address === '' ? 'catalog' : $address);
                if ($folder === '') throw shop_error('row', shop_label('catalog_folder'));
                $settings['catalogFolder'] = $folder;
            }
            if (array_key_exists('short_product_urls', $input)) {
                $settings['shortProductUrls'] = shop_input($input, 'short_product_urls') === '1';
            }
            if ($settings['shortProductUrls'] && strpos(($settings['catalogFolder'] ?? 'catalog') . '/', 'product/') === 0) {
                throw shop_error('row', shop_label('catalog_folder') . ' /product/');
            }
            $catalogPage = shop_input($input, 'catalog');
            if ($catalogPage !== '' && (!site_valid_path($catalogPage) || site_read_page($catalogPage) === null)) throw shop_error('row', 'catalog');
            $settings['catalog'] = $catalogPage;
            $settings['guest'] = shop_input($input, 'guest') === '1';
            if (array_key_exists('shipping', $input)) $settings['shipping'] = (int) shop_import_amount(shop_input($input, 'shipping'));
            $settings['pageSize'] = min(96, max(1, (int) shop_input($input, 'pageSize')));
            $currency = shop_input($input, 'currency');
            if (!in_array($currency, ['RUB','USD','EUR','GBP','UAH','BYN','PLN','KZT','CNY'], true)) throw shop_error('row', 'currency');
            $settings['currency'] = $currency;
            $rounding = shop_input($input, 'rounding');
            if ($rounding !== '' && !isset(SHOP_ROUNDING_STEPS[$rounding])) throw shop_error('row', shop_label('price_rounding'));
            if ($rounding !== '') $settings['rounding'] = $rounding;
            $settings['priceLabels'] = [];
            foreach (preg_split('/\R/', shop_input($input, 'priceLabels')) as $line) {
                if (trim($line) === '') continue;
                $parts = array_map('trim', explode('=', $line, 2));
                if (count($parts) !== 2 || !shop_id($parts[0]) || $parts[1] === '') throw shop_error('row', $line);
                if ($parts[0] === 'old') continue;
                $settings['priceLabels'][$parts[0]] = $parts[1];
            }
            if (is_array($input['mail'] ?? null)) $settings['mail'] = site_message_template($input['mail'], shop_mail_defaults());
            admin_write_record(SHOP_DIR . '/settings.php', $settings, 262144);
            return;
        }
        if ($action === 'shop-status') {
            shop_order_status(shop_input($input, 'order'), shop_input($input, 'status'), shop_input($input, 'current_status'));
            return;
        }
        if ($action === 'shop-status-add') {
            shop_add_order_status(shop_input($input, 'name'), shop_input($input, 'phase'));
            return;
        }
        if ($action === 'shop-photo') {
            shop_upload_photo($uploads['photo'] ?? []);
            return;
        }
        $tables = shop_tables();
        if (in_array($action, ['shop-record-save','shop-record-delete'], true)) {
            if (!hash_equals(shop_generation(), shop_input($input, 'revision'))) throw shop_error('conflict');
            require_once __DIR__ . '/admin-records.php';
            shop_admin_record_change($tables, $input);
        } elseif (in_array($action, ['shop-category-save','shop-category-delete','shop-category-move'], true)) {
            if (!hash_equals(shop_generation(), shop_input($input, 'revision'))) throw shop_error('conflict');
            require_once __DIR__ . '/admin-categories.php';
            if ($action === 'shop-category-move') {
                shop_admin_category_move($tables, $input);
            } else {
                $photo = $uploads['category_photo'] ?? [];
                if ($action === 'shop-category-save' && shop_input($input, 'visual_type') === 'image'
                    && ($photo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
                    $input['image'] = $newPhotos[] = shop_store_photo($photo);
                }
                shop_admin_category_change($tables, $input, $action === 'shop-category-delete');
            }
        } elseif (in_array($action, ['shop-product-save','shop-product-delete','shop-product-bulk','shop-warehouse-add'], true)) {
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
                if ($action === 'shop-product-bulk') shop_admin_product_bulk($tables, $input);
                else {
                    if ($action === 'shop-product-save') {
                        $uploadsByOffer = $uploads['offer_photo'] ?? [];
                        foreach ((array) ($uploadsByOffer['error'] ?? []) as $offerId => $error) {
                            if ($error === UPLOAD_ERR_NO_FILE) continue;
                            if (!is_array($input['offers'][$offerId] ?? null)) throw shop_error('photo_invalid');
                            $image = shop_store_photo(['error'=>$error,'tmp_name'=>$uploadsByOffer['tmp_name'][$offerId] ?? '',
                                'size'=>$uploadsByOffer['size'][$offerId] ?? 0]);
                            $newPhotos[] = $image;
                            $input['offers'][$offerId]['image'] = $image;
                        }
                    }
                    shop_admin_product_change($tables, $input, $action === 'shop-product-delete');
                }
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
            if (!isset(SHOP_COLUMNS[$sheet]) || in_array($sheet, ['warehouses','inventory','barcodes','product_seo','category_media','offer_media'], true)) throw shop_error('row', $sheet);
            $rows = shop_rows(shop_input($input, 'csv'), SHOP_COLUMNS[$sheet], $sheet);
            if ($sheet === 'offers' || $sheet === 'prices') {
                foreach ($rows as &$row) $row['price_minor'] = shop_positive_price($row['price_minor'], $row['sku'] ?? $row['offer_id']);
                unset($row);
            }
            if ($sheet === 'products') {
                $before = array_column($tables['products'], 'slug', 'id');
                $existingAliases = array_column($tables['aliases'], null, 'slug');
                foreach ($rows as $row) {
                    $id = $row['id'];
                    if (isset($before[$id]) && $before[$id] !== $row['slug']) $existingAliases[$before[$id]] = ['slug'=>$before[$id],'product_id'=>$id];
                }
                $currentIds = array_fill_keys(array_column($rows, 'id'), true);
                $currentSlugs = array_fill_keys(array_column($rows, 'slug'), true);
                $tables['product_seo'] = array_values(array_intersect_key(array_column($tables['product_seo'], null, 'product_id'), $currentIds));
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
                implode("\n", array_filter(explode("\n", $mapping), static function (string $line): bool { return strpos($line, 'field:') !== 0; })),
                is_array($input['mapping_columns'] ?? null) ? $input['mapping_columns'] : []);
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
            $tables = shop_migrate_old_prices($preview['tables']);
        } elseif ($action !== 'shop-reindex') throw shop_error('not_found');
        $index = shop_catalog_build($tables);
        if (in_array($action, ['shop-product-save','shop-category-save','shop-category-move','shop-apply'], true)
            || ($action === 'shop-sheet' && in_array($sheet, ['products','categories'], true))) {
            $previous = shop_index();
            $aliases = array_column($tables['aliases'], null, 'slug');
            foreach ($previous['products'] as $id => $product) {
                if (!isset($index['products'][$id])) continue;
                $old = substr(shop_product_page_path($previous, $product), 0, -strlen('/index.php'));
                $new = substr(shop_product_page_path($index, $index['products'][$id]), 0, -strlen('/index.php'));
                if ($old === $new || strlen($old) > 1500) continue;
                $aliases[$old] = ['slug'=>$old,'product_id'=>(string) $id];
                if (($aliases[$new]['product_id'] ?? '') === (string) $id) unset($aliases[$new]);
            }
            $tables['aliases'] = array_values($aliases);
            $index['aliases'] = array_diff_key(array_column($tables['aliases'], 'product_id', 'slug'), $index['slugs']);
        }
        shop_publish($tables, $index);
        if ($action === 'shop-apply') { @unlink($file); unset($_SESSION['shopImportPreview']); }
    } catch (RuntimeException $exception) {
        foreach ($newPhotos as $photo) @unlink(SHOP_IMAGES_DIR . '/' . $photo);
        throw $exception;
    } finally {
        fclose($lock);
    }
}

function shop_upload_photo(array $photo): void
{
    $_SESSION['shopUploaded'] = shop_store_photo($photo);
}

function shop_admin_save_brand(array $input, array $photo): void
{
    $id = shop_input($input, 'brand_id');
    $name = shop_input($input, 'name');
    $description = shop_input($input, 'description');
    $website = shop_input($input, 'website');
    $logo = shop_input($input, 'logo');
    if ($name === '' || strlen($name) > 180 || strlen($description) > 5000 || preg_match('//u', $name . $description) !== 1
        || strpbrk($name . $description, "\0\x1f") !== false || preg_match('/[\r\n]/', $name)
        || ($website !== '' && (strlen($website) > 1000 || !filter_var($website, FILTER_VALIDATE_URL) || strtolower((string) parse_url($website, PHP_URL_SCHEME)) !== 'https'))
        || ($logo !== '' && (!shop_image_name($logo) || !is_file(SHOP_IMAGES_DIR . '/' . $logo)))) throw shop_error('row', shop_label('brand'));
    $brands = shop_brand_profiles();
    if ($id === '') {
        $id = shop_slug($name);
        if ($id === '' || isset($brands[$id])) throw shop_error('row', shop_label('brand'));
    }
    if (!shop_id($id)) throw shop_error('row', shop_label('brand'));
    $existing = $brands[$id] ?? [];
    if (!hash_equals(hash('sha256', serialize($existing)), shop_input($input, 'revision'))) throw shop_error('conflict');
    $newLogo = '';
    if (($photo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) $newLogo = shop_store_photo($photo);
    $brands[$id] = ['name'=>$name,'description'=>$description,'website'=>$website,'logo'=>$newLogo ?: $logo];
    try {
        admin_write_record(SHOP_DIR . '/' . shop_brand_store() . '.php', $brands, 262144);
    } catch (RuntimeException $exception) {
        if ($newLogo !== '') @unlink(SHOP_IMAGES_DIR . '/' . $newLogo);
        throw $exception;
    }
}

function shop_store_photo(array $photo): string
{
    $file = $photo['tmp_name'] ?? '';
    if (($photo['error'] ?? null) !== UPLOAD_ERR_OK || !is_uploaded_file($file) || $photo['size'] > 12000000) throw shop_error('photo_invalid');
    $info = @getimagesize($file);
    if (!$info || $info[0] > 8000 || $info[1] > 8000) throw shop_error('photo_invalid');
    $extension = ['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp','image/gif'=>'gif','image/avif'=>'avif'][$info['mime']] ?? '';
    if ($extension === '') throw shop_error('photo_invalid');
    $path = SHOP_IMAGES_DIR;
    shop_directory($path);
    $name = bin2hex(random_bytes(12)) . '.' . $extension;
    if (!move_uploaded_file($file, $path . '/' . $name)) throw shop_error('storage');
    return $name;
}
