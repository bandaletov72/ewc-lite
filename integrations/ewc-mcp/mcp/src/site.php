<?php
declare(strict_types=1);

require __DIR__ . '/render.php';

final class EwcInput extends RuntimeException {}

function ewcJson($data): string
{
    $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_INVALID_UTF8_SUBSTITUTE);
    if (!is_string($json)) {
        throw new RuntimeException('Unable to encode JSON.');
    }
    return $json;
}

final class EwcSite
{
    private $state;
    private $url;

    public function __construct(string $root, string $state, string $url)
    {
        $root = realpath($root);
        if ($root === false || !is_file($root . '/app/site.php') || !is_file($root . '/admin/write-lock.php')) {
            throw new EwcInput('SITE_ROOT must point to the EWC-lite installation containing index.php.');
        }
        $parts = parse_url($url);
        if (!is_array($parts) || !isset($parts['host'], $parts['scheme']) ||
            !in_array($parts['scheme'], ['https', 'http'], true) ||
            array_intersect_key($parts, array_flip(['user', 'pass', 'query', 'fragment'])) ||
            preg_match('/[\x00-\x20\x7f\\\\]/', $url)) {
            throw new EwcInput('Provide the canonical site URL with http:// or https://, including any installation subdirectory.');
        }
        $this->url = rtrim($url, '/');
        if (!is_dir($state) || is_link($state) || ($state = realpath($state)) === false ||
            !is_writable($state) || $this->within($root, $state)) {
            throw new EwcInput('Create a writable private draft directory outside the site root.');
        }
        $this->state = $state;
        define('SITE_ROOT', $root);
        $version = require SITE_ROOT . '/app/version.php';
        if (!is_string($version) || version_compare($version, '1.6.93', '<')) {
            throw new EwcInput('This plugin requires EWC-lite 1.6.93 or newer.');
        }
        require SITE_ROOT . '/app/site.php';
        require_once SITE_ROOT . '/admin/files.php';
        require_once SITE_ROOT . '/admin/save.php';
    }

    private function within(string $root, string $path): bool
    {
        $root = str_replace('\\', '/', $root);
        $path = str_replace('\\', '/', $path);
        if (DIRECTORY_SEPARATOR === '\\') {
            $root = strtolower($root);
            $path = strtolower($path);
        }
        return $path === $root || strpos($path, rtrim($root, '/') . '/') === 0;
    }

    private function safePath(string $root, string $relative): string
    {
        // Inspect each existing component, including junctions on Windows.
        $file = $root;
        foreach (explode('/', $relative) as $segment) {
            if ($segment === '' || $segment === '.' || $segment === '..' ||
                !preg_match('/\A[a-z0-9_.-]+\z/i', $segment) ||
                preg_match('/\A(?:con|prn|aux|nul|com[1-9]|lpt[1-9])(?:\.|$)/i', $segment)) {
                throw new EwcInput('Unsupported file path.');
            }
            $file .= DIRECTORY_SEPARATOR . $segment;
            $resolved = realpath($file);
            if (is_link($file) || ($resolved !== false && (!$this->within($file, $resolved) || !$this->within($resolved, $file)))) {
                throw new EwcInput('The target contains a redirected filesystem path.');
            }
        }
        return $file;
    }

    private function contentRoot(): string
    {
        $content = site_design_content($this->activeTheme());
        $configured = str_replace('\\', '/', $content === '' ? SITE_ROOT : $content . '/pages');
        $root = str_replace('\\', '/', SITE_ROOT);
        if (!$this->within($root, $configured)) {
            throw new EwcInput('The active content set is outside this installation.');
        }
        return $configured === $root ? SITE_ROOT : $this->safePath(SITE_ROOT, substr($configured, strlen($root) + 1));
    }

    private function pagePath(string $path): string
    {
        if (!preg_match('~\A/(?:[a-z0-9_-]+/)*(?:[a-z0-9_-]+\.php)?\z~i', $path) ||
            !site_valid_path($file = admin_page_path($path)) ||
            preg_match('~\A(?:shop|error)/~i', $file) ||
            site_page_translation($file)[1] !== '') {
            throw new EwcInput('Use a valid logical page path, such as /catalog/item/.');
        }
        return $file;
    }

    private function pageUrl(string $path): string
    {
        return site_page_url('', $path);
    }

    private function parentPage(string $path): string
    {
        $directory = dirname($path);
        if (basename($path) === 'index.php') {
            $directory = dirname($directory);
        }
        return $directory === '.' ? 'index.php' : $directory . '/index.php';
    }

    private function themeId(string $id): string
    {
        if ($id !== 'system' && (!site_design_id($id) || strlen($id) > 48)) {
            throw new EwcInput('Use lowercase Latin letters, digits, hyphens or underscores for the theme ID.');
        }
        return $id;
    }

    private function fields(stdClass $page, bool $product): array
    {
        $visible = ($page->menuVisible ?? !$product) ? '1' : '0';
        $fields = [
            'Title' => ewcLine($page->title),
            'Heading' => ewcLine($page->heading ?? $page->title),
            'Description' => ewcLine($page->description ?? ''),
            'Menu' => ewcLine($page->menuLabel ?? $page->title),
            'MenuVisible' => $visible, 'MobileMenu' => $visible,
            'Order' => (string) ($page->order ?? 100), 'Hidden' => '0',
        ];
        if ($product) {
            $amount = site_sale_amount($page->price);
            if ($amount === '' || ewcLine($page->sku) === '') {
                throw new EwcInput('A product needs a SKU and a positive amount with up to two decimal places.');
            }
            $fields['Sale'] = base64_encode(ewcJson(['enabled' => false, 'notices' => 'off', 'offers' => [
                'default' => ['amount' => $amount, 'currency' => '', 'method' => '', 'target' => ''],
            ]]));
        }
        return $fields;
    }

    private function pageBytes(stdClass $page, string $path, bool $product, bool $inTheme): string
    {
        $fields = $this->fields($page, $product);
        if (!$inTheme) {
            $fields['CSS'] = admin_encode_asset(ewcComponentCss());
        }
        $html = ewcBlocks($page->blocks);
        if ($product) {
            $html .= '<p class="ewc-mcp-sku">SKU: ' . ewcHtml(ewcLine($page->sku)) . "</p>\n";
            $html .= "<p class=\"ewc-mcp-price\">[item-price]</p>\n[item-buy-button]\n";
        } elseif ($page->listChildren ?? false) {
            $html .= "[list-pages]\n";
        }
        return admin_page_bytes($path, $fields, $html);
    }

    private function fileHash(string $file): ?string
    {
        if (!file_exists($file)) {
            return null;
        }
        $hash = is_file($file) ? hash_file('sha256', $file) : false;
        if (!is_string($hash)) {
            throw new EwcInput('The target is occupied or cannot be read.');
        }
        return $hash;
    }

    private function record(array $data): string
    {
        return SITE_RECORD_HEADER . ewcJson($data) . "\n";
    }

    private function planPage(string $operation, stdClass $args): array
    {
        $root = $this->contentRoot();
        $path = $this->pagePath($args->path);
        $this->checkShopRoute($path);
        $file = $this->safePath($root, $path);
        $hash = $this->fileHash($file);
        $append = $operation === 'draft_add_content';
        if ($append) {
            $current = $hash === null ? null : site_read_page($path, $root);
            $bytes = $current === null ? false : file_get_contents($file);
            if (!is_string($bytes) || !hash_equals($hash, hash('sha256', $bytes))) {
                throw new EwcInput('The target page is missing or has changed. Refresh the page list.');
            }
            $addition = ewcBlocks($args->blocks);
            $bytes .= "\n\n" . $addition;
            if (strlen($current['html']) + strlen($addition) + 2 > SITE_PAGE_LIMIT ||
                strlen($bytes) > SITE_PAGE_RECORD_LIMIT) {
                throw new EwcInput('The new page content would exceed the CMS size limit.');
            }
        } else {
            if ($hash !== null) {
                throw new EwcInput('This page already exists. Choose a new path or append content to the existing page.');
            }
            if ($path !== 'index.php' && site_read_page($this->parentPage($path), $root) === null) {
                throw new EwcInput('The parent section is missing. Create it first.');
            }
            $bytes = $this->pageBytes($args, $path, $operation === 'draft_product', false);
        }
        return ['kind' => 'file', 'relative' => $path, 'root' => 'content', 'scope' => $root,
            'activeTheme' => $this->activeTheme(), 'expected' => $hash, 'bytes' => $bytes,
            'target' => $this->url . $this->pageUrl($path)];
    }

    private function planTheme(string $operation, stdClass $args): array
    {
        $theme = $this->themeId($args->themeId);
        if ($theme === 'system' || file_exists($this->safePath(SITE_ROOT, 'templates/themes/' . $theme))) {
            throw new EwcInput('Choose a new theme ID.');
        }
        $name = ewcLine($args->name);
        if ($name === '') {
            throw new EwcInput('Theme name cannot be empty.');
        }
        $files = ['theme-info.php' => $this->record(['name' => $name]), 'css/theme.css' => ewcManagedCss($args)];
        if ($operation === 'draft_site') {
            $files['content/settings/site-settings.php'] = $this->record([
                'name' => $name, 'description' => ewcLine($args->siteDescription ?? ''),
            ]);
            // A safe, empty data directory is required for CMS-owned content mode.
            $files['content/data/.gitkeep'] = '';
            $pages = [];
            foreach ([[$args->pages, false], [$args->products ?? [], true]] as [$group, $product]) {
                foreach ($group as $page) {
                    $path = $this->pagePath($page->path);
                    $this->checkShopRoute($path);
                    $key = strtolower($path);
                    if (isset($pages[$key]) || ($product && $path === 'index.php')) {
                        throw new EwcInput('Each site path must be unique, and the home page must be a regular page.');
                    }
                    $this->safePath(SITE_ROOT, 'templates/themes/' . $theme . '/content/pages/' . $path);
                    $pages[$key] = $path;
                    $files['content/pages/' . $path] = $this->pageBytes($page, $path, $product, true);
                }
            }
            if (!isset($pages['index.php'])) {
                throw new EwcInput('A complete site requires a home page at /.');
            }
            foreach ($pages as $path) {
                $parent = $this->parentPage($path);
                if (!isset($pages[strtolower($parent)]) || $pages[strtolower($parent)] !== $parent) {
                    throw new EwcInput('Include a parent section for ' . $this->pageUrl($path));
                }
            }
        }
        return ['kind' => 'theme', 'theme' => $theme, 'files' => $files, 'target' => $theme];
    }

    private function activeTheme(): string
    {
        $file = $this->safePath(SITE_ROOT, 'admin/.design-theme.php');
        $id = site_read_record($file)['theme'] ?? '';
        return is_string($id) && site_design_directory($id) !== '' ? $id : '';
    }

    public function listPages(string $prefix, int $offset): array
    {
        $this->pagePath($prefix);
        $pages = [];
        foreach (site_pages(false, $this->contentRoot()) as $page) {
            if (preg_match('~\A(?:shop|error)/~i', $page['path'])) {
                continue;
            }
            $logical = $this->pageUrl($page['path']);
            if (strpos($logical, $prefix) === 0) {
                $pages[] = ['path' => $logical, 'title' => $page['fields']['Title'],
                    'url' => $this->url . $logical, 'visibleInMenu' => $page['fields']['MenuVisible'] === '1'];
            }
        }
        return ['site' => $this->url, 'activeTheme' => $this->activeTheme() ?: 'system',
            'pages' => array_slice($pages, $offset, 50), 'nextOffset' => $offset < count($pages) - 50 ? $offset + 50 : null];
    }

    public function listThemes(): array
    {
        $active = $this->activeTheme();
        $themes = [['id' => 'system', 'name' => 'System', 'content' => 'shared', 'active' => $active === '']];
        $root = $this->safePath(SITE_ROOT, 'templates/themes');
        foreach (new DirectoryIterator($root) as $item) {
            $id = $item->getFilename();
            if (site_design_directory($id) !== '') {
                $themes[] = ['id' => $id, 'name' => site_design_name($id),
                    'content' => site_design_content($id) === '' ? 'shared' : 'theme', 'active' => $id === $active];
            }
        }
        return ['site' => $this->url, 'themes' => $themes];
    }

    private function loadShop(): void
    {
        $flag = $this->safePath(SITE_ROOT, 'admin/.shop-enabled.php');
        $directory = site_shop_directory();
        if (empty(site_read_record($flag)['enabled']) || !is_file($directory . '/module.php') ||
            !is_file($directory . '/admin-products.php') || !is_file($directory . '/admin-categories.php')) {
            throw new EwcInput('Enable the native Shop module in the EWC-lite admin panel first.');
        }
        require_once $directory . '/module.php';
        require_once $directory . '/admin-products.php';
        require_once $directory . '/admin-categories.php';
        require_once $directory . '/import.php';
    }

    private function shopRoutes(?string $directory = null): array
    {
        if (!is_file(site_shop_directory() . '/widgets.php')) {
            return ['shop', false];
        }
        if ($directory === null) {
            $directory = site_shop_directory() . '/data';
            if (!is_dir($directory) && is_dir(SITE_ROOT . '/shop/data')) {
                $directory = SITE_ROOT . '/shop/data';
            }
        }
        $settings = site_read_record($directory . '/settings.php', 262144);
        $folder = site_shop_folder(is_string($settings['catalogFolder'] ?? null) ? $settings['catalogFolder'] : '') ?: 'catalog';
        return [$folder, ($settings['shortProductUrls'] ?? false) === true && strpos($folder . '/', 'product/') !== 0];
    }

    private function checkShopRoute(string $path): void
    {
        $flag = $this->safePath(SITE_ROOT, 'admin/.shop-enabled.php');
        if (empty(site_read_record($flag)['enabled'])) {
            return;
        }
        [$folder, $short] = $this->shopRoutes();
        $url = $this->pageUrl($path);
        if (strpos(strtolower($url), '/' . $folder . '/') === 0 ||
            ($short && preg_match('~\A/product/[a-z0-9_-]+/\z~i', $url))) {
            throw new EwcInput('This address is reserved by the enabled Shop module. Use a shop category or choose another page path.');
        }
    }

    private function shopUrl(array $index, string $kind, array $row, array $routes): string
    {
        [$folder, $short] = $routes;
        if ($kind === 'products' && $short) {
            return $this->url . '/product/' . $row['slug'] . '/';
        }
        $path = $kind === 'products' ? shop_product_path($index, $row) : shop_category_path($index, $row['id']);
        return $this->url . '/' . $folder . '/' . $path . '/';
    }

    public function listShopCategories(int $offset): array
    {
        $lock = admin_lock();
        try {
            $this->loadShop();
            $index = shop_catalog_build(shop_tables());
            $routes = $this->shopRoutes(SHOP_DIR);
            $categories = [];
            foreach ($index['categories'] as $category) {
                $categories[] = ['id' => $category['id'], 'name' => $category['name'],
                    'parentId' => $category['parent_id'] === '' ? null : $category['parent_id'],
                    'url' => $this->shopUrl($index, 'categories', $category, $routes)];
            }
            return ['site' => $this->url, 'currency' => shop_settings()['currency'],
                'categories' => array_slice($categories, $offset, 50),
                'nextOffset' => $offset < count($categories) - 50 ? $offset + 50 : null];
        } finally {
            fclose($lock);
        }
    }

    private function planShop(string $operation, stdClass $args): array
    {
        $this->loadShop();
        $revision = shop_generation();
        $tables = shop_tables($revision);
        if (!shop_id($args->slug)) {
            throw new EwcInput('Use lowercase Latin letters, digits, hyphens or underscores for the shop slug.');
        }
        try {
            if ($operation === 'draft_shop_category') {
                shop_admin_category_change($tables, [
                    'name' => ewcLine($args->name), 'slug' => $args->slug, 'parent_id' => $args->parentId ?? '',
                    'description' => ewcLine($args->description ?? ''),
                    'sort' => '100', 'descendants' => '1',
                ], false);
                $target = end($tables['categories']);
                $sheet = 'categories';
                $add = ['categories' => $target];
                $pricing = [];
            } else {
                if (in_array(ewcLine($args->sku), array_column($tables['offers'], 'sku'), true)) {
                    throw new EwcInput('This SKU is already in use.');
                }
                if (site_sale_amount($args->price) === '') {
                    throw new EwcInput('Provide a positive price with up to two decimal places.');
                }
                shop_admin_product_change($tables, [
                    'complete' => '1', 'category_id' => $args->categoryId, 'slug' => $args->slug,
                    'name' => ewcLine($args->title), 'summary' => ewcLine($args->summary ?? ''),
                    'description' => isset($args->blocks) ? ewcBlocks($args->blocks) : '',
                    'image' => isset($args->imageUrl) ? ewcPublicLink($args->imageUrl) : '',
                    'type' => $args->type, 'active' => $args->active ? '1' : '0',
                    'offers' => ['_new' => ['sku' => ewcLine($args->sku), 'price' => $args->price,
                        'stock' => (string) $args->stock, 'active' => '1']],
                ], false);
                $target = $tables['products'][0];
                $sheet = 'products';
                $offer = end($tables['offers']);
                $add = ['products' => $target, 'offers' => $offer];
                $settings = shop_settings();
                $pricing = ['currency' => $settings['currency'],
                    'displayPrice' => shop_price_input(shop_rounded_price((int) $offer['price_minor'], $settings))];
            }
            $index = shop_catalog_build($tables);
        } catch (RuntimeException $error) {
            throw new EwcInput($error->getMessage());
        }
        return ['kind' => 'shop', 'revision' => $revision, 'settings' => $this->fileHash(SHOP_DIR . '/settings.php'),
            'add' => $add, 'target' => $this->shopUrl($index, $sheet, $target, $this->shopRoutes(SHOP_DIR))] + $pricing;
    }

    private function publishShop(array $plan): void
    {
        $this->loadShop();
        $revision = shop_generation();
        $tables = shop_tables($revision);
        $missing = [];
        foreach ($plan['add'] as $sheet => $row) {
            $stored = array_column($tables[$sheet], null, 'id')[$row['id']] ?? null;
            if ($stored !== null && $stored != $row) {
                throw new EwcInput('The shop entry has changed.');
            }
            if ($stored === null) {
                $missing[$sheet] = $row;
            }
        }
        if (!$missing) {
            return;
        }
        if ($revision !== $plan['revision'] || $this->fileHash(SHOP_DIR . '/settings.php') !== $plan['settings']) {
            throw new EwcInput('The shop catalog or settings changed. Refresh the categories and create a new draft.');
        }
        foreach ($missing as $sheet => $row) {
            if ($sheet === 'products') {
                array_unshift($tables[$sheet], $row);
            } else {
                $tables[$sheet][] = $row;
            }
        }
        shop_publish($tables, shop_catalog_build($tables));
    }

    private function planAppearance(string $operation, stdClass $args): array
    {
        if ($operation === 'draft_activate_theme') {
            $id = $this->themeId($args->themeId);
            if ($id !== 'system' && site_design_directory($id) === '') {
                throw new EwcInput('The theme is not installed.');
            }
            $theme = $id === 'system' ? '' : $id;
            $relative = 'admin/.design-theme.php';
            $bytes = $this->record(['theme' => $theme]);
            $extra = ['previousTheme' => $this->activeTheme() ?: 'system', 'activateTheme' => $theme,
                'activatedContent' => site_design_content($theme) === '' ? 'shared' : 'theme'];
        } else {
            $id = $this->activeTheme();
            if ($id === '') {
                throw new EwcInput('Install and activate a design theme before editing its styling.');
            }
            $relative = 'templates/themes/' . $id . '/css/theme.css';
            $file = $this->safePath(SITE_ROOT, $relative);
            $original = is_file($file) ? file_get_contents($file) : '';
            if (!is_string($original) || strlen($original) > SITE_ASSET_LIMIT) {
                throw new EwcInput('Theme stylesheet is unreadable or too large.');
            }
            $pattern = '~\n?/\* EWC MCP: BEGIN \*/\R.*?/\* EWC MCP: END \*/\R?~s';
            $bytes = (string) preg_replace($pattern, '', $original);
            $bytes = ($bytes === '' ? '' : rtrim($bytes) . "\n\n") . ewcManagedCss($args);
            if (strlen($bytes) > SITE_ASSET_LIMIT) {
                throw new EwcInput('The resulting stylesheet exceeds the CMS size limit.');
            }
            $extra = ['activeTheme' => $id];
        }
        $file = $this->safePath(SITE_ROOT, $relative);
        return ['kind' => 'file', 'relative' => $relative, 'root' => 'site', 'scope' => SITE_ROOT,
            'expected' => $this->fileHash($file), 'bytes' => $bytes, 'target' => $id] + $extra;
    }

    private function draftFile(string $id): string
    {
        if (!preg_match('/\A[a-f0-9]{32}\z/', $id)) {
            throw new EwcInput('Invalid draft ID.');
        }
        return $this->safePath($this->state, $id . '.json');
    }

    private function loadDraft(string $id): array
    {
        $file = $this->draftFile($id);
        $bytes = is_file($file) ? file_get_contents($file, false, null, 0, 12 * 1024 * 1024) : false;
        $draft = is_string($bytes) ? json_decode($bytes, true) : null;
        if (!is_array($draft) || ($draft['instance'] ?? null) !== SITE_ROOT || ($draft['url'] ?? null) !== $this->url) {
            throw new EwcInput('Draft not found for this installation.');
        }
        return $draft;
    }

    public function readDraft(string $id): array
    {
        return $this->loadDraft($id)['preview'];
    }

    public function draft(string $operation, stdClass $args): array
    {
        $lock = admin_lock();
        try {
            switch ($operation) {
                case 'draft_design':
                case 'draft_site':
                    $plan = $this->planTheme($operation, $args);
                    break;
                case 'draft_restyle':
                case 'draft_activate_theme':
                    $plan = $this->planAppearance($operation, $args);
                    break;
                case 'draft_shop_product':
                case 'draft_shop_category':
                    $plan = $this->planShop($operation, $args);
                    break;
                default:
                    $plan = $this->planPage($operation, $args);
            }
            $id = bin2hex(random_bytes(16));
            $preview = ['draftId' => $id, 'operation' => $operation, 'target' => $plan['target'],
                'site' => $this->url, 'status' => 'draft', 'proposed' => $args] +
                array_intersect_key($plan, array_flip(['previousTheme', 'activatedContent', 'currency', 'displayPrice']));
            $record = ['instance' => SITE_ROOT, 'url' => $this->url, 'plan' => $plan, 'preview' => $preview];
            if (!admin_replace_file($this->draftFile($id), ewcJson($record))) {
                throw new RuntimeException('Unable to save draft.');
            }
            return $preview;
        } finally {
            fclose($lock);
        }
    }

    public function publish(string $id): array
    {
        $lock = admin_lock();
        try {
            $draft = $this->loadDraft($id);
            if (isset($draft['receipt'])) {
                return $draft['receipt'];
            }
            $plan = $draft['plan'];
            switch ($plan['kind']) {
                case 'theme':
                    $this->publishTheme($plan);
                    break;
                case 'shop':
                    $this->publishShop($plan);
                    break;
                default:
                    $this->publishFile($plan);
            }
            $draft['receipt'] = ['draftId' => $id, 'status' => 'published', 'target' => $plan['target']];
            $draft['preview']['status'] = 'published';
            admin_replace_file($this->draftFile($id), ewcJson($draft));
            return $draft['receipt'];
        } finally {
            fclose($lock);
        }
    }

    private function publishFile(array $plan): void
    {
        $root = $plan['root'] === 'content' ? $this->contentRoot() : SITE_ROOT;
        if ($root !== $plan['scope'] || (isset($plan['activeTheme']) && $plan['activeTheme'] !== $this->activeTheme())) {
            throw new EwcInput('The active theme changed. Create a new draft.');
        }
        if ($plan['root'] === 'content') {
            $this->checkShopRoute($plan['relative']);
        }
        if (!empty($plan['activateTheme']) && (site_design_directory($plan['activateTheme']) === '' ||
            (site_design_content($plan['activateTheme']) === '' ? 'shared' : 'theme') !== $plan['activatedContent'])) {
            throw new EwcInput('The selected theme or its content scope changed. Create a new draft.');
        }
        $file = $this->safePath($root, $plan['relative']);
        $hash = $this->fileHash($file);
        if ($hash === hash('sha256', $plan['bytes'])) {
            return;
        }
        if ($hash !== $plan['expected']) {
            throw new EwcInput('The target changed after this draft was prepared. Create a new draft.');
        }
        if ($plan['root'] === 'content' && $hash === null && $plan['relative'] !== 'index.php' &&
            site_read_page($this->parentPage($plan['relative']), $root) === null) {
            throw new EwcInput('The parent section has changed. Create a new draft.');
        }
        admin_make_page_directory($plan['relative'], $root);
        if (!admin_replace_file($file, $plan['bytes'])) {
            throw new RuntimeException('Unable to replace CMS file.');
        }
    }

    private function publishTheme(array $plan): void
    {
        $root = $this->safePath(SITE_ROOT, 'templates/themes');
        $installed = $this->safePath($root, $plan['theme']);
        if (file_exists($installed)) {
            foreach ($plan['files'] as $relative => $bytes) {
                if ($this->fileHash($this->safePath($installed, $relative)) !== hash('sha256', $bytes)) {
                    throw new EwcInput('This theme ID is already in use. Choose a new ID.');
                }
            }
            return;
        }
        $stage = $this->safePath($root, '.ewc-mcp-' . bin2hex(random_bytes(12)));
        if (!mkdir($stage, 0755)) {
            throw new RuntimeException('Unable to prepare theme directory.');
        }
        try {
            foreach ($plan['files'] as $relative => $bytes) {
                $file = $this->safePath($stage, $relative);
                admin_make_page_directory($relative, $stage);
                if (!admin_replace_file($file, $bytes)) {
                    throw new RuntimeException('Unable to write theme file.');
                }
            }
            if (!rename($stage, $installed)) {
                throw new RuntimeException('Unable to install theme.');
            }
        } finally {
            if (is_dir($stage)) {
                $items = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($stage, FilesystemIterator::SKIP_DOTS),
                    RecursiveIteratorIterator::CHILD_FIRST);
                foreach ($items as $item) {
                    $item->isDir() && !$item->isLink() ? rmdir($item->getPathname()) : unlink($item->getPathname());
                }
                rmdir($stage);
            }
        }
    }
}
