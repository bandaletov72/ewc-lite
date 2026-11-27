<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}

function admin_design_starter_names(): array
{
    return ['kontur' => 'КОНТУР', 'tihiy-sad' => 'Тихий сад', 'dezinfektori' => 'Дезинфекторы'];
}

function admin_design_themes(): array
{
    $themes = array_merge(['demo'], array_keys(admin_design_starter_names()));
    foreach (glob(SITE_DESIGN_ROOT . '/*', GLOB_ONLYDIR) ?: [] as $directory) {
        $id = basename($directory);
        if (!in_array($id, $themes, true) && site_design_directory($id) !== '') $themes[] = $id;
    }
    sort($themes, SORT_NATURAL);
    return $themes;
}

function admin_design_inventory(): array
{
    $relative = array_values(array_filter(site_layout_blocks()));
    $relative = array_merge($relative, ['layout.php', 'content.php', 'editor-bar.php', 'library-assets.php', 'navigation-links.php', 'search-results.php']);
    foreach (['components', 'iblock'] as $folder) {
        foreach (glob(SITE_ROOT . '/templates/' . $folder . '/*.php') ?: [] as $file) {
            $relative[] = $folder . '/' . basename($file);
        }
    }
    $relative = array_values(array_unique($relative));
    sort($relative, SORT_STRING);
    return array_merge($relative, ['css/theme.css', 'js/theme.js']);
}

function admin_design_prepare_directory(string $relative)
{
    $path = SITE_ROOT . '/templates';
    foreach (explode('/', $relative) as $part) {
        $path .= '/' . $part;
        if (is_link($path) || (!is_dir($path) && !@mkdir($path, 0755))
            || realpath($path) !== str_replace('/', DIRECTORY_SEPARATOR, $path)) {
            throw new RuntimeException('Не удалось создать каталог темы дизайна.');
        }
    }
}

function admin_design_protect()
{
    admin_design_prepare_directory('themes');
    $rules = [
        '.htaccess' => "<FilesMatch \"(?i)(?:\\.php(?:[0-9]+)?$|^\\.)\">\n    Require all denied\n</FilesMatch>\n",
        'web.config' => '<?xml version="1.0" encoding="UTF-8"?>'
            . '<configuration><system.webServer><security><requestFiltering><fileExtensions>'
            . '<add fileExtension=".php" allowed="false" /></fileExtensions></requestFiltering></security></system.webServer></configuration>',
    ];
    foreach ($rules as $file => $text) {
        $path = SITE_DESIGN_ROOT . '/' . $file;
        if (is_link($path) || (!is_file($path) && !admin_replace_file($path, $text))) {
            throw new RuntimeException('Не удалось защитить каталог темы дизайна.');
        }
    }
}

function admin_design_write(string $id, string $relative, string $source)
{
    if (strlen($source) > 262144 || strpos($source, "\0") !== false || preg_match('//u', $source) !== 1) {
        throw new RuntimeException('Проверьте длину и формат полей настроек.');
    }
    if (substr($relative, -4) === '.php') {
        $guard = "<?php if (!defined('SITE_ROOT')) { http_response_code(404); exit; } ?>";
        if (strpos($source, $guard) !== 0) $source = $guard . $source;
        foreach (admin_validate_php($source) as $token) {
            if (is_array($token) && $token[0] === T_HALT_COMPILER) {
                throw new RuntimeException('В шаблонах PHP нельзя использовать __halt_compiler().');
            }
        }
    }
    admin_design_protect();
    $parent = dirname($relative);
    admin_design_prepare_directory('themes/' . $id . ($parent !== '.' ? '/' . $parent : ''));
    $file = SITE_DESIGN_ROOT . '/' . $id . '/' . $relative;
    if (is_link($file) || !admin_replace_file($file, $source)) {
        throw new RuntimeException('Не удалось сохранить файл темы дизайна.');
    }
}

function admin_design_seed_demo()
{
    $new = site_design_directory('demo') === '';
    admin_design_protect();
    if (!$new) return;
    foreach (require SITE_ROOT . '/app/design-theme-demo.php' as $path => $source) {
        admin_design_write('demo', $path, $source);
    }
}

function admin_design_starter_files(string $id): array
{
    static $files = [];
    if (!isset(admin_design_starter_names()[$id])) return [];
    if (!isset($files[$id])) $files[$id] = require SITE_ROOT . '/app/starter-' . $id . '.php';
    return $files[$id];
}

function admin_design_versions(string $id): array
{
    if ($id === '' || $id === 'demo') {
        $version = $id === '' ? site_update_version() : SITE_DESIGN_DEMO_VERSION;
        return ['installed' => $version, 'pages' => '', 'available' => $version, 'availablePages' => ''];
    }
    $bundle = admin_design_starter_files($id)['theme-version.php'] ?? '';
    $source = $bundle === '' ? false : base64_decode($bundle, true);
    $bundled = is_string($source) && strpos($source, SITE_RECORD_HEADER) === 0
        ? json_decode(substr($source, strlen(SITE_RECORD_HEADER)), true) : [];
    $file = site_design_file($id, 'theme-version.php');
    try {
        $installed = $file === '' ? [] : site_read_record($file);
    } catch (RuntimeException $exception) {
        $installed = [];
    }
    $version = static function ($value): string {
        return is_string($value) && preg_match('/\A[0-9]+\.[0-9]+\.[0-9]+\z/', $value) ? $value : '';
    };
    return [
        'installed' => $version($installed['version'] ?? ''),
        'pages' => $version($installed['pagesVersion'] ?? ''),
        'available' => $version($bundled['version'] ?? ''),
        'availablePages' => $version($bundled['pagesVersion'] ?? ''),
    ];
}

function admin_design_seed_starter(string $id)
{
    if (site_design_directory($id) !== '') return;
    admin_design_protect();
    $stage = SITE_DESIGN_ROOT . '/.install-' . bin2hex(random_bytes(8));
    if (!mkdir($stage, 0755)) throw new RuntimeException('Не удалось подготовить каталог темы дизайна.');
    try {
        foreach (admin_design_starter_files($id) as $relative => $encoded) {
            if (!preg_match('~\A(?:[a-z0-9_-]+/)*[a-z0-9_-]+\.(?:txt\.php|php|css|js|jpg|png)\z~', $relative)) {
                throw new RuntimeException('Недопустимое имя файла темы.');
            }
            $file = $stage . '/' . $relative;
            $directory = dirname($file);
            $bytes = base64_decode($encoded, true);
            if ($bytes === false || (!is_dir($directory) && !@mkdir($directory, 0755, true))
                || @file_put_contents($file, $bytes) !== strlen($bytes)) {
                throw new RuntimeException('Не удалось установить файлы темы дизайна.');
            }
        }
        if (!@rename($stage, SITE_DESIGN_ROOT . '/' . $id)) throw new RuntimeException('Не удалось сохранить тему дизайна.');
    } finally {
        if (is_dir($stage)) {
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($stage, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
            foreach ($iterator as $item) {
                if ($item->isDir() && !$item->isLink()) @rmdir($item->getPathname());
                else @unlink($item->getPathname());
            }
            @rmdir($stage);
        }
    }
}

function admin_design_merge_price_columns(string $file, string $starterBytes): ?string
{
    $block = site_read_iblock_file($file);
    $starter = site_iblock_from_csv(substr($starterBytes, strlen(SITE_RECORD_HEADER)));
    $existingTypes = array_column($block['columns'], 'type');
    $keys = array_column($block['columns'], 'key');
    $servicePosition = array_search('service', $keys, true);
    $starterRows = array_column($starter['rows'], null, 0);
    $changed = false;

    foreach ($starter['columns'] as $starterPosition => $column) {
        if (!in_array($column['type'], ['hidden', 'form'], true) || in_array($column['type'], $existingTypes, true)) continue;
        $key = $column['key'];
        for ($suffix = 2; in_array($key, $keys, true); $suffix++) {
            $key = $column['key'] . '-' . $suffix;
        }
        $keys[] = $key;
        $block['columns'][] = ['key' => $key] + $column;
        $default = $column['type'] === 'form' ? ($starter['rows'][0][$starterPosition] ?? '') : '';
        foreach ($block['rows'] as &$row) {
            $service = $servicePosition === false ? '' : ($row[$servicePosition] ?? '');
            $row[] = $starterRows[$service][$starterPosition] ?? $default;
        }
        unset($row);
        $changed = true;
    }
    if (!$changed) return null;
    $bytes = SITE_RECORD_HEADER . site_iblock_csv($block);
    if (strlen($bytes) > SITE_IBLOCK_LIMIT) {
        throw new RuntimeException('Размер файла превышает допустимый предел.');
    }
    return $bytes;
}

function admin_dezinfektori_missing_files(): array
{
    $files = [];
    foreach (['prices', 'business', 'promotions', 'certificates', 'payment'] as $section) {
        $files['content/pages/' . $section . '/index.php'] = true;
    }
    foreach (['uhod-za-uchastkom', 'obrabotka-derevev', 'posle-zatopleniya', 'posle-pozhara'] as $service) {
        $files['content/pages/services/' . $service . '/index.php'] = true;
        $files['content/data/dez-price-' . $service . '.txt.php'] = true;
    }
    return $files;
}

function admin_dezinfektori_review_page(string $root, string $path, string $pattern): ?string
{
    $page = site_read_page($path, $root);
    if ($page === null) return null;
    $html = $page['html'];
    $basePage = site_page_translation($path)[0];
    $service = strpos($basePage, 'services/') === 0;
    $reviewCode = $service ? '[reviews page=current]' : '[reviews]';
    preg_match_all($pattern, $html, $matches);
    $hasReviews = in_array('reviews', array_map('strtolower', $matches['snippet']), true)
        || in_array('reviews', array_map('strtolower', $matches['sizeTag']), true)
        || count(array_filter($matches['reviewsCurrent'])) > 0;
    $updated = preg_replace_callback($pattern, static function (array $match) use (&$hasReviews, $reviewCode, $basePage): string {
        if ($basePage === 'reviews/index.php' && strtolower($match['form'] ?? '') === 'form87') {
            if ($hasReviews) return '';
            $hasReviews = true;
            return $reviewCode;
        }
        if (strtolower($match['iblockTemplate'] ?? '') !== 'reviews'
            || site_iblock_id_from_argument($match['iblock']) !== 'dez-reviews') return $match[0];
        if ($hasReviews) return '';
        $hasReviews = true;
        return $reviewCode;
    }, $html);
    if ($updated === null) return null;
    if (!$hasReviews) $updated .= "\n\n" . $reviewCode . "\n";
    if ($basePage === 'index.php') {
        $updated = str_replace('<a class="dz-button dz-button-soft" href="/reviews/">Оставить отзыв <span aria-hidden="true">↗</span></a>',
            '', $updated);
    } elseif ($basePage === 'reviews/index.php' && $updated !== $html) {
        $updated = str_replace('class="dz-request" id="leave-review"', 'class="dz-section" id="leave-review"', $updated);
    }
    if ($updated === $html) return null;

    $file = $root . '/' . $path;
    if (is_link($file)) return null;
    $source = @file_get_contents($file);
    if ($source === false || !hash_equals($page['revision'], hash('sha256', $source))) {
        throw new RuntimeException('Подтвердите изменения страницы и запустите обновление темы заново.');
    }
    return substr($source, 0, strlen($source) - strlen($html)) . $updated;
}

function admin_design_update_starter(string $id, string $mode, ?callable $progress = null, bool $designFilesOnly = false)
{
    $versions = admin_design_versions($id);
    $waitingForReviewPages = $id === 'dezinfektori' && $mode === 'design'
        && ($versions['pages'] === '' || version_compare($versions['pages'], '1.0.0', '<'));
    $replacePages = $mode === 'pages';
    $missingFiles = $mode === 'missing' ? admin_dezinfektori_missing_files() : [];
    $templates = array_fill_keys(admin_design_inventory(), true);
    $prefix = 'templates/themes/' . $id . '/';
    $files = [];
    foreach (admin_design_starter_files($id) as $path => $encoded) {
        if ($designFilesOnly && !isset($templates[$path])) continue;
        $isBundledPage = $replacePages && strpos($path, 'content/pages/') === 0
            && site_valid_path(substr($path, strlen('content/pages/')));
        $destination = $prefix . $path;
        $installedFile = SITE_ROOT . '/' . $destination;
        if ($waitingForReviewPages && $path === 'content.php') {
            $source = site_design_file($id, $path);
            if ($source !== '' && strpos((string) file_get_contents($source, false, null, 0, 262144), 'site_component_reviews(') !== false) {
                continue;
            }
        }
        $isMissingFile = isset($missingFiles[$path]) && !file_exists($installedFile) && !is_link($installedFile);
        $isExistingPrice = $id === 'dezinfektori' && preg_match('~\Acontent/data/dez-price-[a-z0-9_-]+\.txt\.php\z~', $path)
            && is_file($installedFile) && !is_link($installedFile);
        $isNewData = $replacePages && preg_match('~\Acontent/data/[a-z0-9_-]+\.txt\.php\z~', $path)
            && !file_exists(SITE_ROOT . '/' . $destination) && !is_link(SITE_ROOT . '/' . $destination);
        if (!isset($templates[$path]) && $path !== 'form-defaults.php' && !$isBundledPage && !$isNewData && !$isExistingPrice && !$isMissingFile) continue;
        $bytes = base64_decode($encoded, true);
        if ($bytes === false) throw new RuntimeException('Не удалось прочитать файл из комплекта темы.');
        if ($isExistingPrice) {
            $bytes = admin_design_merge_price_columns($installedFile, $bytes);
            if ($bytes === null) continue;
        }
        $files[$destination] = $bytes;
    }
    if ($id === 'dezinfektori' && $mode === 'missing') {
        $root = SITE_ROOT . '/' . $prefix . 'content/pages';
        if (is_dir($root)) {
            $pattern = site_content_pattern(site_extensions());
            foreach (site_pages(true, $root) as $page) {
                $path = $page['path'];
                $basePage = site_page_translation($path)[0];
                if ($basePage !== 'index.php' && $basePage !== 'reviews/index.php' && strpos($basePage, 'services/') !== 0) continue;
                $updated = admin_dezinfektori_review_page($root, $path, $pattern);
                if ($updated !== null) $files[$prefix . 'content/pages/' . $path] = $updated;
            }
        }
    }
    if ($versions['available'] !== '') {
        $files[$prefix . 'theme-version.php'] = SITE_RECORD_HEADER . json_encode([
            'version' => $versions['available'],
            'pagesVersion' => $mode === 'design' ? $versions['pages'] : $versions['availablePages'],
        ]) . "\n";
    }
    if (!$files) return;

    require_once SITE_ROOT . '/app/updater.php';
    if ($replacePages) {
        if ($progress !== null) $progress('backup');
        $backup = admin_backup_capture(admin_backup_files(), $progress);
        if ($progress !== null) $progress('backup-saved', null, null, $backup);
    }
    site_update_replace($files, $progress);
    if ($mode !== 'design') @unlink(SITE_ROOT . '/data/.search-theme-' . $id . '.php');
}

function admin_design_use_file(string $id, string $path)
{
    $keys = array_keys(site_layout_blocks(), $path, true);
    if (!$keys || $path === '') return;
    $name = $keys[0];
    $file = site_design_layout_file($id);
    $layout = site_read_record($file, SITE_SNIPPET_RECORD_LIMIT);
    if (!isset($layout[$name])) return;
    $history = $layout['_layoutHistory'][$name] ?? [];
    $history[] = $layout[$name];
    $layout['_layoutHistory'][$name] = array_slice($history, -8);
    unset($layout[$name]);
    admin_write_record($file, $layout, SITE_SNIPPET_RECORD_LIMIT);
}

function admin_design_action(string $action, array $input, string $language, ?callable $progress = null): array
{
    $id = admin_input($input, 'theme');
    $path = admin_input($input, 'file');
    $labels = site_design_labels($language);
    if (($id !== '' && !site_design_id($id)) || ($action !== 'design-apply' && $id === '')) {
        throw new RuntimeException($labels[10]);
    }
    $lock = admin_lock();
    try {
        if ($id === 'demo') admin_design_seed_demo();
        if ($action === 'design-apply' && isset(admin_design_starter_names()[$id])) admin_design_seed_starter($id);
        if ($action === 'design-create') {
            if (in_array($id, admin_design_themes(), true)) throw new RuntimeException($labels[10]);
            admin_design_protect();
            admin_design_prepare_directory('themes/' . $id);
            admin_write_record(SITE_DESIGN_FILE, ['theme' => $id]);
        } elseif ($action === 'design-apply') {
            if ($id !== '' && site_design_directory($id) === '') throw new RuntimeException($labels[10]);
            admin_write_record(SITE_DESIGN_FILE, ['theme' => $id]);
        } elseif ($action === 'design-update') {
            $mode = admin_input($input, 'update_mode');
            if (!isset(admin_design_starter_names()[$id]) || site_design_directory($id) === ''
                || !in_array($mode, $id === 'dezinfektori' ? ['design', 'pages', 'missing'] : ['design', 'pages'], true)
                || ($mode === 'pages' && admin_input($input, 'confirm_replace') !== '1')) {
                throw new RuntimeException($labels[10]);
            }
            if ($progress !== null) $progress('theme-' . $mode);
            admin_design_update_starter($id, $mode, $progress);
        } else {
            if (site_design_directory($id) === '' || !in_array($path, admin_design_inventory(), true)) throw new RuntimeException($labels[10]);
            $existing = site_design_file($id, $path);
            if ($action === 'design-reset') {
                if ($existing === '' || admin_input($input, 'confirm') !== '1'
                    || !hash_equals((string) hash_file('sha256', $existing), admin_input($input, 'revision'))) {
                    throw new RuntimeException($labels[10]);
                }
                $factory = admin_design_starter_files($id)[$path] ?? null;
                if ($factory !== null) {
                    $bytes = base64_decode($factory, true);
                    if ($bytes === false || !admin_replace_file($existing, $bytes)) throw new RuntimeException($labels[10]);
                } elseif (!@unlink($existing)) {
                    throw new RuntimeException($labels[10]);
                }
                if (function_exists('opcache_invalidate')) @opcache_invalidate($existing, true);
                admin_design_use_file($id, $path);
                return [$id, $path];
            }
            if ($action === 'design-copy') {
                if ($existing !== '') throw new RuntimeException($labels[10]);
                $source = substr($path, -4) === '.php' ? @file_get_contents(SITE_ROOT . '/templates/' . $path) : '';
            } else {
                $source = admin_input($input, 'source');
                if ($existing === '' || !hash_equals((string) hash_file('sha256', $existing), admin_input($input, 'revision'))) {
                    throw new RuntimeException($labels[10]);
                }
            }
            if (!is_string($source)) throw new RuntimeException($labels[10]);
            admin_design_write($id, $path, $source);
            admin_design_use_file($id, $path);
        }
        return [$id, $path];
    } finally {
        fclose($lock);
    }
}
