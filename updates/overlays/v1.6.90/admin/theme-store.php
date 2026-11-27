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
        $html .= '<li' . ($supported ? ' data-supported' : '') . '>' . site_escape($labels[$label] . ' · ' . $labels[$supported ? 5 : 6]) . '</li>';
    }
    return $html . '</ul>';
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

function admin_theme_store_source(string $path, int $limit): string
{
    $repo = site_update_github_repo();
    if ($repo === '') throw new RuntimeException('Источник обновлений не настроен.');
    $path = 'theme-store/' . $path;
    if ((string) getenv('SITE_GITHUB_TOKEN') !== '') {
        return site_update_download('https://api.github.com/repos/' . $repo . '/contents/' . $path,
            'application/vnd.github.raw+json', $limit);
    }
    return site_update_download('https://raw.githubusercontent.com/' . $repo . '/HEAD/' . $path, '*/*', $limit);
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

function admin_theme_store_package(string $id, array $item): array
{
    $bytes = admin_theme_store_source('packages/' . $id . '.json', 950000);
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

function admin_theme_store_seed_products(string $id, array $input): int
{
    if (!site_shop_enabled()) throw new RuntimeException('Модуль отключён');
    $item = admin_theme_store_catalog()[$id] ?? null;
    if ($item === null || !hash_equals($item['sha256'], admin_input($input, 'revision'))) {
        throw new RuntimeException('Пакет темы изменился. Обновите каталог и попробуйте снова.');
    }
    $data = admin_theme_store_package($id, $item);
    $requested = $input['products'] ?? [];
    if (!is_array($requested) || !$requested || count($requested) > count($data['products'])) {
        throw new RuntimeException('Выберите демо-товары для загрузки.');
    }
    $selection = [];
    foreach ($requested as $key) {
        if (!is_string($key) || !ctype_digit($key) || !isset($data['products'][$key])) {
            throw new RuntimeException('Выберите демо-товары для загрузки.');
        }
        $selection[(int) $key] = $data['products'][$key];
    }
    require_once site_shop_directory() . '/module.php';
    $lock = shop_lock();
    $createdImages = [];
    try {
        $tables = shop_tables();
        $category = 'demo-' . $id;
        $categoryRow = array_search($category, array_column($tables['categories'], 'id'), true);
        if ($categoryRow === false) {
            $tables['categories'][] = array_combine(SHOP_COLUMNS['categories'], [
                $category, '', $category, $item['name'], '100', '1', '',
            ]);
        } elseif ($tables['categories'][$categoryRow]['slug'] !== $category) {
            throw new RuntimeException('Имя раздела каталога уже занято.');
        }
        $products = array_fill_keys(array_column($tables['products'], 'id'), true);
        shop_directory(SHOP_IMAGES_DIR);
        $added = 0;
        foreach ($selection as $key => $product) {
            $productId = $category . '-' . ($key + 1);
            if (isset($products[$productId])) continue;
            $photo = $productId . '-' . substr(hash('sha256', $product['image']), 0, 12) . '.jpg';
            $path = SHOP_IMAGES_DIR . '/' . $photo;
            if (is_link($path) || (is_file($path) && hash_file('sha256', $path) !== hash('sha256', $product['image']))) {
                throw new RuntimeException('Не удалось сохранить файл темы дизайна.');
            }
            if (!is_file($path)) {
                if (!admin_replace_file($path, $product['image'])) throw new RuntimeException('Не удалось сохранить файл темы дизайна.');
                $createdImages[] = $path;
            }
            $tables['products'][] = array_combine(SHOP_COLUMNS['products'], [$productId, $category, $productId,
                $product['name'], $product['description'], $product['description'], $photo, date('Y-m-d'), 'physical', '1']);
            $tables['offers'][] = array_combine(SHOP_COLUMNS['offers'], [$productId, $productId, strtoupper($productId), '',
                (string) ($product['price'] * 100), '20', '1']);
            ++$added;
        }
        if ($added > 0) shop_publish($tables, shop_catalog_build($tables));
        return $added;
    } catch (RuntimeException $exception) {
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
