<?php
if (!defined('SITE_ROOT')) { http_response_code(404); exit; }

function admin_theme_store_labels(string $language): array
{
    $labels = require SITE_ROOT . '/app/help-theme-store.php';
    return explode('|', $labels[$language] ?? $labels['ru']);
}

function admin_theme_store_badges(array $info, array $labels): string
{
    $html = '<ul class="theme-store-badges">';
    foreach (['storefront'=>2, 'site'=>3, 'siteShop'=>4] as $key => $label) {
        $supported = !empty($info[$key]);
        $html .= '<li data-theme-feature="' . $key . '"' . ($supported ? ' data-supported' : '') . '><span>'
            . site_escape($labels[$label]) . '</span> · <span data-theme-feature-value>' . site_escape($labels[$supported ? 5 : 6]) . '</span></li>';
    }
    return $html . '</ul>';
}

function admin_theme_store_preview_urls(string $base, string $id): array
{
    if (!in_array($id, ['forno-pizza','flor-atelier','sever-trail','stal-doors'], true)) return [];
    $urls = [];
    foreach (['home','shop'] as $view) {
        $path = 'theme-store/previews/' . $id . '-' . $view . '.jpg';
        $urls[$view] = is_file(SITE_ROOT . '/' . $path) ? site_url($base, $path)
            : site_url($base, 'admin/') . '?design=1&theme_preview=' . $id . '-' . $view;
    }
    return $urls;
}

function admin_theme_store_preview_bytes(string $key): string
{
    $file = SITE_ROOT . '/app/theme-store-previews.php';
    if (!preg_match('/\A[a-z-]+-(?:home|shop)\z/', $key) || !is_file($file)) return '';
    $images = require $file;
    return base64_decode($images[$key] ?? '', true) ?: '';
}

function admin_theme_store_preview_labels(string $language): array
{
    $labels = [
        'ru'=>['Главная','Магазин','Букеты','Предыдущий снимок','Следующий снимок'],
        'en'=>['Home','Store','Bouquets','Previous screenshot','Next screenshot'],
        'uk'=>['Головна','Магазин','Букети','Попередній знімок','Наступний знімок'],
        'de'=>['Startseite','Shop','Blumensträuße','Vorherige Vorschau','Nächste Vorschau'],
        'fr'=>['Accueil','Boutique','Bouquets','Capture précédente','Capture suivante'],
        'es'=>['Inicio','Tienda','Ramos','Captura anterior','Captura siguiente'],
        'pt'=>['Início','Loja','Ramos','Captura anterior','Captura seguinte'],
        'ja'=>['ホーム','ショップ','花束','前の画面','次の画面'],
        'ko'=>['홈','상점','꽃다발','이전 화면','다음 화면'],
        'vi'=>['Trang chủ','Cửa hàng','Bó hoa','Ảnh trước','Ảnh tiếp theo'],
        'zh'=>['首页','商城','花束','上一张截图','下一张截图'],
    ];
    return $labels[$language] ?? $labels['en'];
}

function admin_theme_store_jpeg($value, int $limit): string
{
    $bytes = is_string($value) ? base64_decode($value, true) : false;
    $size = is_string($bytes) && strlen($bytes) <= $limit ? @getimagesizefromstring($bytes) : false;
    if (!$size || $size[2] !== IMAGETYPE_JPEG || $size[0] > 2000 || $size[1] > 1600) {
        throw new RuntimeException('Некорректные данные магазина тем.');
    }
    return $bytes;
}

function admin_theme_store_source(string $path, int $limit, ?callable $onBytes = null): string
{
    $repo = site_update_github_repo();
    if ($repo === '') throw new RuntimeException('Источник обновлений не настроен.');
    $path = 'theme-store/' . $path;
    if ((string) getenv('SITE_GITHUB_TOKEN') !== '') {
        return site_update_download('https://api.github.com/repos/' . $repo . '/contents/' . $path,
            'application/vnd.github.raw+json', $limit, $onBytes);
    }
    return site_update_download('https://raw.githubusercontent.com/' . $repo . '/HEAD/' . $path, '*/*', $limit, $onBytes);
}

function admin_theme_store_text($value, int $limit, bool $required = true): string
{
    if (!is_string($value) || strlen($value) > $limit || ($required && trim($value) === '')
        || preg_match('/[\x00-\x1f\x7f\[\]<>]/u', $value) !== 0) {
        throw new RuntimeException('Некорректные данные магазина тем.');
    }
    return trim($value);
}

function admin_theme_store_catalog(): array
{
    $catalog = json_decode(admin_theme_store_source('catalog.json', 65536), true, 8);
    if (($catalog['schema'] ?? null) !== 1 || !is_array($catalog['themes'] ?? null) || count($catalog['themes']) > 60) {
        throw new RuntimeException('Некорректные данные магазина тем.');
    }
    $items = [];
    foreach ($catalog['themes'] as $id => $item) {
        if (!is_string($id) || !site_design_id($id) || strlen($id) > 48 || !is_array($item)
            || !site_spam_valid_version($item['version'] ?? null)
            || !is_string($item['sha256'] ?? null) || !preg_match('/\A[a-f0-9]{64}\z/', $item['sha256'])
            || !is_bool($item['site'] ?? null) || !is_bool($item['storefront'] ?? null)
            || !is_bool($item['siteShop'] ?? null) || ($item['siteShop'] && !$item['site'])
            || !is_string($item['accent'] ?? null) || !site_theme_color($item['accent'])) {
            throw new RuntimeException('Некорректные данные магазина тем.');
        }
        $item['name'] = admin_theme_store_text($item['name'] ?? null, 120);
        $item['description'] = admin_theme_store_text($item['description'] ?? null, 650);
        $items[$id] = $item;
    }
    return $items;
}

function admin_theme_store_package(string $id, array $item, ?callable $onBytes = null): array
{
    $bytes = admin_theme_store_source('packages/' . $id . '.json', 950000, $onBytes);
    if (!hash_equals($item['sha256'], hash('sha256', $bytes))) {
        throw new RuntimeException('Пакет темы изменился. Обновите каталог и попробуйте снова.');
    }
    $package = json_decode($bytes, true, 10);
    if (($package['schema'] ?? null) !== 1 || ($package['id'] ?? null) !== $id
        || ($package['version'] ?? null) !== $item['version']
        || !in_array($package['style'] ?? null, ['trattoria', 'atelier', 'expedition'], true)) {
        throw new RuntimeException('Некорректные данные магазина тем.');
    }
    foreach (['light', 'dark'] as $mode) {
        foreach (array_keys(site_theme_fields()) as $key) {
            $color = $package[$mode][$key] ?? null;
            if (!is_string($color) || !site_theme_color($color)) throw new RuntimeException('Некорректные данные магазина тем.');
            $data[$mode][$key] = $color;
        }
    }
    $data['image'] = admin_theme_store_jpeg($package['coverJpeg'] ?? null, 350000);
    $data['style'] = $package['style'];
    foreach (['heading'=>180, 'tagline'=>150, 'intro'=>900, 'catalogTitle'=>130, 'detailsTitle'=>130, 'details'=>1400] as $key => $limit) {
        $data[$key] = admin_theme_store_text($package[$key] ?? null, $limit);
    }
    $products = $package['products'] ?? null;
    if (!is_array($products) || count($products) < 1 || count($products) > 60 || array_values($products) !== $products) {
        throw new RuntimeException('Некорректные данные магазина тем.');
    }
    foreach ($products as $product) {
        if (!is_int($product['price'] ?? null) || $product['price'] < 1 || $product['price'] > 999999) {
            throw new RuntimeException('Некорректные данные магазина тем.');
        }
        $data['products'][] = [
            'name'=>admin_theme_store_text($product['name'] ?? null, 180),
            'description'=>admin_theme_store_text($product['description'] ?? null, 500),
            'price'=>$product['price'],
            'image'=>admin_theme_store_jpeg($product['jpeg'] ?? null, 60000),
            'section'=>admin_theme_store_text($product['section'] ?? '', 130, false),
            'sku'=>admin_theme_store_text($product['sku'] ?? '', 120, false),
        ];
    }
    return $data;
}

function admin_theme_store_page(string $path, string $title, string $heading, int $order, string $content): string
{
    return site_page_header($path) . "\nTitle: " . $title . "\nHeading: " . $heading . "\nMenu: " . $title
        . "\nMenuVisible: 1\nMobileMenu: 1\nOrder: " . $order . "\n\n" . $content . "\n";
}

function admin_theme_store_files(string $id, array $item, array $data, bool $withPages): array
{
    $record = static function (array $value): string {
        return SITE_RECORD_HEADER . json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n";
    };
    $info = ['name'=>$item['name'], 'storefront'=>$item['storefront'], 'site'=>$withPages,
        'siteShop'=>$withPages && $item['siteShop'], 'storePackage'=>true];
    $files = [
        'theme-info.php'=>$record($info),
        'theme-version.php'=>$record(['version'=>$item['version']]),
        'images/cover.jpg'=>$data['image'],
        'css/theme.css'=>admin_theme_store_css($data, $item['storefront']),
    ];
    if (!$withPages) return $files;
    $asset = '/templates/themes/' . $id . '/images/cover.jpg';
    $title = site_escape($data['catalogTitle']);
    $detailsTitle = site_escape($data['detailsTitle']);
    $home = '<section class="theme-store-hero"><div><p class="theme-store-kicker">' . site_escape($data['tagline'])
        . '</p><h2>' . site_escape($data['heading']) . '</h2><p>' . site_escape($data['intro'])
        . '</p><a class="button button-primary" href="/menu/">' . $title . '</a></div>'
        . '<img src="' . $asset . '" width="1200" height="800" alt="' . site_escape($item['name']) . '"></section>';
    $cards = '<div class="theme-store-demo-grid">';
    foreach ($data['products'] as $number => $product) {
        $path = 'images/item-' . ($number + 1) . '.jpg';
        $files[$path] = $product['image'];
        $name = site_escape($product['name']);
        $cards .= '<article><img src="/templates/themes/' . $id . '/' . $path . '" alt="' . $name
            . '" width="240" height="240" loading="lazy"><h2>' . $name . '</h2><p>' . site_escape($product['description'])
            . '</p><strong>' . number_format($product['price'], 0, '', ' ') . ' ₽</strong></article>';
    }
    $cards .= '</div>';
    $details = '<section class="theme-store-details"><p>' . site_escape($data['details'])
        . '</p><a class="button" href="/menu/">' . $title . '</a></section>';
    $files += [
        'content/pages/index.php'=>admin_theme_store_page('index.php', 'Главная', $item['name'], 10, $home),
        'content/pages/menu/index.php'=>admin_theme_store_page('menu/index.php', $data['catalogTitle'], $data['catalogTitle'], 20, $cards),
        'content/pages/details/index.php'=>admin_theme_store_page('details/index.php', $data['detailsTitle'], $data['detailsTitle'], 30, $details),
        'content/settings/site-settings.php'=>$record(['name'=>$item['name'], 'subtitle'=>$data['tagline'],
            'description'=>$data['intro'], 'footer'=>'© [year] ' . $item['name'], 'searchMode'=>'compact']),
    ];
    if ($info['siteShop']) {
        $category = 'demo-' . $id;
        $info['shopCategory'] = $category;
        $files['theme-info.php'] = $record($info);
        $files['content/pages/shop/index.php'] = admin_theme_store_page('shop/index.php', 'Магазин', 'Магазин', 25, '[shop category=' . $category . ']');
    }
    return $files;
}

function admin_theme_store_catalog_backups(): array
{
    $backups = [];
    foreach (glob(SHOP_DIR . '/demo-backup-*.php') ?: [] as $file) {
        if (!is_link($file) && preg_match('/\Ademo-backup-([0-9]{8}T[0-9]{6}Z-[a-f0-9]{12})\.php\z/', basename($file), $match)) {
            $metadata = admin_theme_store_backup_metadata($match[1]);
            $backups[$match[1]] = ['time'=>filemtime($file), 'name'=>$metadata['name'] ?? '', 'count'=>$metadata['count'] ?? null];
        }
    }
    krsort($backups, SORT_STRING);
    return $backups;
}

function admin_theme_store_backup_metadata(string $id): array
{
    $file = SHOP_DIR . '/demo-brands-' . $id . '.php';
    $data = site_read_record($file, 270336);
    return ($data['schema'] ?? null) === 1 ? $data : ['brands'=>$data];
}

function admin_theme_store_backup_catalog(?string $revision = null): void
{
    $revision = $revision ?? shop_generation();
    $brands = shop_brand_profiles();
    if ($revision === '' && !$brands) return;
    $catalog = $revision === '' ? '' : site_read_payload(SHOP_DIR . '/catalog-' . $revision . '.php', SHOP_LIMIT);
    if ($catalog === null) throw shop_error('storage');
    $tables = shop_decode_tables($catalog);
    $sections = array_filter($tables['categories'], static function (array $category): bool { return $category['parent_id'] === ''; });
    $id = gmdate('Ymd\THis\Z') . '-' . bin2hex(random_bytes(6));
    $label = implode(', ', array_column(array_slice($sections, 0, 3), 'name'));
    admin_write_record(SHOP_DIR . '/demo-brands-' . $id . '.php',
        ['schema'=>1, 'name'=>$label, 'count'=>count($tables['products']), 'brands'=>$brands], 270336);
    shop_write('demo-backup-' . $id, $catalog);
}

function admin_theme_store_restore_catalog(array $input): void
{
    if (!site_shop_enabled()) throw new RuntimeException('Модуль отключён');
    require_once site_shop_directory() . '/module.php';
    if (!function_exists('shop_decode_tables')) throw new RuntimeException(admin_theme_store_labels($GLOBALS['adminLanguage'] ?? 'ru')[27]);
    $lock = shop_lock();
    try {
        $id = admin_input($input, 'backup');
        if (admin_input($input, 'confirm_restore') !== '1' || !isset(admin_theme_store_catalog_backups()[$id])
            || !hash_equals(shop_generation(), admin_input($input, 'catalog_revision'))) throw shop_error('conflict');
        $brandsFile = SHOP_DIR . '/demo-brands-' . $id . '.php';
        $catalog = site_read_payload(SHOP_DIR . '/demo-backup-' . $id . '.php', SHOP_LIMIT);
        if ($catalog === null || is_link($brandsFile) || !is_file($brandsFile)) throw shop_error('storage');
        $brands = admin_theme_store_backup_metadata($id)['brands'];
        $tables = shop_decode_tables($catalog);
        $index = shop_catalog_build($tables);
        admin_theme_store_backup_catalog();
        shop_publish($tables, $index, $brands);
    } finally {
        fclose($lock);
    }
}

function admin_theme_store_demo_sections(array $products, string $defaultName): array
{
    $sections = [];
    foreach ($products as $key => $product) {
        $name = $product['section'] ?? '';
        $id = $name === '' ? 'catalog' : 's-' . substr(hash('sha256', $name), 0, 14);
        $sections[$id]['name'] = $name === '' ? $defaultName : $name;
        $sections[$id]['products'][$key] = $product;
    }
    return $sections;
}

function admin_theme_store_merge_demo(array $tables, array $sections, string $id, string $theme, array &$createdImages, ?callable $progress = null): array
{
    $categories = array_column($tables['categories'], null, 'id');
    $products = array_column($tables['products'], null, 'id');
    $offers = array_column($tables['offers'], null, 'id');
    $slugs = array_fill_keys(array_merge(array_column($products, 'slug'), array_column($tables['aliases'], 'slug')), true);
    $placements = [];
    foreach ($tables['placements'] as $row) $placements[$row['product_id']][$row['category_id']] = true;
    $root = 'demo-' . $id;
    $addSection = static function (string $key, string $parent, string $name) use (&$categories): void {
        if (isset($categories[$key])) return;
        $siblings = [];
        foreach ($categories as $category) if ($category['parent_id'] === $parent) $siblings[$category['slug']] = true;
        $slug = $key;
        for ($suffix = 2; isset($siblings[$slug]); $suffix++) $slug = substr($key, 0, 58) . '-' . $suffix;
        $categories[$key] = ['id'=>$key,'parent_id'=>$parent,'slug'=>$slug,'name'=>$name,'sort'=>'100','descendants'=>'1','description'=>''];
    };
    $addSection($root, '', $theme);
    $total = array_sum(array_map(static function (array $section): int { return count($section['products']); }, $sections));
    shop_directory(SHOP_IMAGES_DIR);
    $done = 0;
    if ($progress !== null) $progress('demo-products', 0, $total);
    foreach ($sections as $key => $section) {
        $category = $key === 'catalog' ? $root : substr($root, 0, 46) . '-' . $key;
        $addSection($category, $root, $section['name']);
        foreach ($section['products'] as $number => $item) {
            $legacy = $root . '-' . ($number + 1);
            $sku = ($item['sku'] ?? '') ?: strtoupper($legacy);
            $name = shop_lower($item['name']);
            $named = $local = [];
            foreach ($products as $productId => $product) {
                if (shop_lower($product['name']) !== $name) continue;
                $named[] = (string) $productId;
                if ($product['category_id'] === $category || isset($placements[$productId][$category])) $local[] = (string) $productId;
            }
            $match = null;
            foreach ($offers as $offer) {
                if (shop_lower($offer['sku']) === shop_lower($sku)
                    && (!empty($item['sku']) || in_array($offer['product_id'], $named, true))) {
                    $match = $offer;
                    break;
                }
            }
            $productId = $match['product_id'] ?? ($local[0] ?? (count($named) === 1 ? $named[0] : ''));
            if ($productId === '') {
                $productId = $legacy;
                if (isset($products[$productId]) || isset($slugs[$productId])) {
                    $productId = substr($root, 0, 42) . '-' . substr(hash('sha256', $category . ':' . $name), 0, 16);
                }
                if (isset($products[$productId]) || isset($slugs[$productId])) throw shop_error('row', $item['name']);
            }
            $existing = $products[$productId] ?? [];
            if ($existing && $existing['category_id'] !== $category && !isset($placements[$productId][$category])) {
                $placements[$productId][$category] = true;
                $tables['placements'][] = ['product_id'=>$productId,'category_id'=>$category];
            }
            $digest = hash('sha256', $item['image']);
            $photo = $productId . '-' . substr($digest, 0, 12) . '.jpg';
            $path = SHOP_IMAGES_DIR . '/' . $photo;
            if (is_link($path) || (is_file($path) && hash_file('sha256', $path) !== $digest)) throw shop_error('storage');
            if (!is_file($path)) {
                if (!admin_replace_file($path, $item['image'])) throw shop_error('storage');
                $createdImages[] = $path;
            }
            $slug = $existing['slug'] ?? $productId;
            $products[$productId] = ['id'=>$productId,'category_id'=>$existing['category_id'] ?? $category,'slug'=>$slug,
                'name'=>$item['name'],'summary'=>$item['description'],'description'=>$item['description'],'image'=>$photo,
                'created'=>$existing['created'] ?? date('Y-m-d'),'type'=>$existing['type'] ?? 'physical','active'=>$existing['active'] ?? '1',
                'quick_pickup'=>$existing['quick_pickup'] ?? ''];
            $slugs[$slug] = true;
            if ($match === null && $existing) {
                $variants = array_filter($offers, static function (array $offer) use ($productId): bool { return $offer['product_id'] === $productId; });
                if (count($variants) === 1) $match = reset($variants);
                else foreach ($variants as $variant) if ($variant['sku'] === $sku || $variant['id'] === 'demo-offer-' . substr(hash('sha256', $productId), 0, 16)) { $match = $variant; break; }
            }
            if ($match !== null) {
                $match['price_minor'] = (string) ($item['price'] * 100);
                $offers[$match['id']] = $match;
            } else {
                $offerId = isset($offers[$productId]) || $existing ? 'demo-offer-' . substr(hash('sha256', $productId), 0, 16) : $productId;
                $occupied = array_map('shop_lower', array_column($offers, 'sku'));
                if (in_array(shop_lower($sku), $occupied, true)) $sku = strtoupper($offerId);
                $offers[$offerId] = ['id'=>$offerId,'product_id'=>$productId,'sku'=>$sku,'variant'=>'',
                    'price_minor'=>(string) ($item['price'] * 100),'stock'=>'20','active'=>'1'];
            }
            if ($progress !== null) $progress('demo-products', ++$done, $total);
        }
    }
    $tables['categories'] = array_values($categories);
    $tables['products'] = array_values($products);
    $tables['offers'] = array_values($offers);
    return $tables;
}

function admin_theme_store_builtin_demo(string $id): ?array
{
    $file = SITE_ROOT . '/app/stal-doors-demo.php';
    return $id === 'stal-doors' && is_file($file) ? [
        'name'=>admin_design_offer_info($id)['name'] ?? 'СТАЛ · Готовые двери',
        'sha256'=>hash_file('sha256', $file), 'file'=>$file,
    ] : null;
}

function admin_theme_store_demo_content(string $id, array $item, ?callable $onBytes = null): array
{
    if (isset($item['file'])) return require $item['file'];
    $data = admin_theme_store_package($id, $item, $onBytes);
    return admin_theme_store_demo_sections($data['products'], $data['catalogTitle']);
}

function admin_theme_store_merge_builtin_demo(array $tables, array $section, ?callable $progress): array
{
    $category = $section['category'];
    $categories = array_column($tables['categories'], null, 'id');
    $categories[$category['id']] = $categories[$category['id']] ?? $category;
    $fields = array_column($tables['fields'], null, 'id') + array_column($section['fields'], null, 'id');
    $products = array_column($tables['products'], null, 'id');
    $offers = array_column($tables['offers'], null, 'id');
    $bySku = array_column($offers, null, 'sku');
    $slugs = array_fill_keys(array_merge(array_column($products, 'slug'), array_column($tables['aliases'], 'slug')), true);
    $placements = $gallery = $knownValues = [];
    foreach ($tables['placements'] as $row) $placements[$row['product_id']][$row['category_id']] = true;
    foreach ($tables['images'] as $row) $gallery[$row['product_id']] = true;
    foreach ($tables['values'] as $row) $knownValues[$row['target_id']][$row['field_id']] = true;
    $total = count($section['products']);
    $done = 0;
    if ($progress !== null) $progress('demo-products', $done, $total);
    foreach ($section['products'] as $item) {
        $id = $item['id'];
        $sku = $item['sku'];
        $offer = $bySku[$sku] ?? $offers[$id] ?? [];
        $productId = $offer['product_id'] ?? $id;
        $existing = $products[$productId] ?? [];
        if ($existing && $existing['category_id'] !== $category['id'] && !isset($placements[$productId][$category['id']])) {
            $tables['placements'][] = ['product_id'=>$productId,'category_id'=>$category['id']];
            $placements[$productId][$category['id']] = true;
        }
        if (!$existing) {
            $slug = $item['slug'];
            for ($suffix = 2; isset($slugs[$slug]); $suffix++) $slug = $item['slug'] . '-' . $suffix;
            $slugs[$slug] = true;
            $products[$productId] = [
                'id'=>$productId,'category_id'=>$category['id'],'slug'=>$slug,'name'=>$item['name'],'summary'=>$item['summary'],
                'description'=>$item['description'],'image'=>$item['photos'][0]['image'],'created'=>gmdate('Y-m-d'),
                'type'=>'physical','active'=>'1','quick_pickup'=>'1',
            ];
        }
        $offerId = $offer['id'] ?? $id;
        $offers[$offerId] = [
            'price_minor'=>$item['price_minor'], 'old_price_minor'=>$item['old_price_minor'],
        ] + $offer + ['id'=>$offerId,'product_id'=>$productId,'sku'=>$sku,'stock'=>'1','variant'=>'','active'=>'1'];
        if (!isset($gallery[$productId])) {
            foreach ($item['photos'] as $photo) $tables['images'][] = ['product_id'=>$productId] + $photo;
            $gallery[$productId] = true;
        }
        foreach ($item['values'] as $field => $value) {
            if (!isset($knownValues[$productId][$field])) {
                $tables['values'][] = ['field_id'=>$field,'target_id'=>$productId,'value'=>$value];
                $knownValues[$productId][$field] = true;
            }
        }
        if ($progress !== null) $progress('demo-products', ++$done, $total);
    }
    $tables['categories'] = array_values($categories);
    $tables['fields'] = array_values($fields);
    $tables['products'] = array_values($products);
    $tables['offers'] = array_values($offers);
    return $tables;
}

function admin_theme_store_seed_products(string $id, array $input, ?callable $progress = null): int
{
    if (!site_shop_enabled()) throw new RuntimeException('Модуль отключён');
    $labels = admin_theme_store_labels($GLOBALS['adminLanguage'] ?? 'ru');
    $mode = admin_input($input, 'catalog_mode');
    if (!in_array($mode, ['merge','replace'], true)) throw new RuntimeException($labels[30]);
    if ($mode === 'replace' && admin_input($input, 'confirm_replace') !== '1') throw new RuntimeException($labels[14]);
    require_once site_shop_directory() . '/module.php';
    if (!function_exists('shop_decode_tables')) throw new RuntimeException($labels[27]);
    if ($progress !== null) $progress('demo-source');
    $builtin = admin_theme_store_builtin_demo($id);
    $item = $builtin ?? (admin_theme_store_catalog()[$id] ?? null);
    if ($item === null || !hash_equals($item['sha256'], admin_input($input, 'revision'))) {
        throw new RuntimeException('Пакет темы изменился. Обновите каталог и попробуйте снова.');
    }
    if ($progress !== null) $progress('demo-package');
    $sections = admin_theme_store_demo_content($id, $item, $progress === null ? null :
        static function (int $done, ?int $total) use ($progress): void { $progress('demo-package', $done, $total); });
    $requested = $input['sections'] ?? [];
    if (!is_array($requested) || !$requested || count($requested) > count($sections)) throw new RuntimeException($labels[29]);
    $selection = [];
    foreach ($requested as $key) {
        if (!is_string($key) || !isset($sections[$key])) throw new RuntimeException($labels[29]);
        $selection[$key] = $sections[$key];
    }
    $lock = shop_lock();
    $createdImages = [];
    try {
        if (!hash_equals(shop_generation(), admin_input($input, 'catalog_revision'))) throw shop_error('conflict');
        $tables = $mode === 'replace' ? shop_decode_tables('') : shop_tables();
        $tables = $builtin !== null ? admin_theme_store_merge_builtin_demo($tables, reset($selection), $progress)
            : admin_theme_store_merge_demo($tables, $selection, $id, $item['name'], $createdImages, $progress);
        $index = shop_catalog_build($tables);
        if ($progress !== null) $progress('demo-backup');
        admin_theme_store_backup_catalog();
        if ($progress !== null) $progress('demo-save');
        shop_publish($tables, $index, $mode === 'replace' ? [] : null);
        return array_sum(array_map(static function (array $section): int { return count($section['products']); }, $selection));
    } catch (Throwable $exception) {
        foreach ($createdImages as $path) @unlink($path);
        throw $exception;
    } finally {
        fclose($lock);
    }
}

function admin_theme_store_install(array $input, ?callable $progress = null): string
{
    $id = admin_input($input, 'theme');
    if (!site_design_id($id) || strlen($id) > 48 || in_array($id, admin_design_themes(), true)) {
        throw new RuntimeException('Имя темы уже занято или недоступно.');
    }
    if ($progress !== null) $progress('theme-design');
    $item = admin_theme_store_catalog()[$id] ?? null;
    if ($item === null) throw new RuntimeException('Тема дизайна недоступна.');
    $files = admin_theme_store_files($id, $item, admin_theme_store_package($id, $item),
        $item['site'] && admin_input($input, 'with_pages') === '1');
    $lock = admin_lock();
    $stage = '';
    try {
        admin_design_protect();
        $target = SITE_DESIGN_ROOT . '/' . $id;
        if (file_exists($target) || is_link($target)) throw new RuntimeException('Имя темы уже занято или недоступно.');
        $stage = SITE_DESIGN_ROOT . '/.install-' . bin2hex(random_bytes(8));
        if (!@mkdir($stage, 0755)) throw new RuntimeException('Не удалось подготовить каталог темы дизайна.');
        if (isset($files['content/pages/index.php']) && !@mkdir($stage . '/content/data', 0755, true)) {
            throw new RuntimeException('Не удалось подготовить каталог темы дизайна.');
        }
        if ($progress !== null) $progress('prepare', 0, count($files));
        $done = 0;
        foreach ($files as $relative => $bytes) {
            $file = $stage . '/' . $relative;
            $dir = dirname($file);
            if ((!is_dir($dir) && !@mkdir($dir, 0755, true)) || @file_put_contents($file, $bytes) !== strlen($bytes)) {
                throw new RuntimeException('Не удалось установить файлы темы дизайна.');
            }
            if ($progress !== null) $progress('prepare', ++$done, count($files), $relative);
        }
        if ($progress !== null) $progress('install', 0, 1, $id);
        if (!@rename($stage, $target)) throw new RuntimeException('Не удалось сохранить тему дизайна.');
        $stage = '';
        return $id;
    } finally {
        if ($stage !== '' && is_dir($stage)) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($stage, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
            foreach ($iterator as $file) {
                if ($file->isDir() && !$file->isLink()) @rmdir($file->getPathname());
                else @unlink($file->getPathname());
            }
            @rmdir($stage);
        }
        fclose($lock);
    }
}
