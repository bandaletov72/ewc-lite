<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}

function admin_media_cleanup_candidate(string $path): bool
{
    if (admin_backup_shop_media_path($path)) return true;
    return preg_match('~\A(?:templates/(?:themes/[a-z0-9_-]+/)?)?(?:images|files)/~', $path)
        && !preg_match('~\Atemplates/(?:themes/[a-z0-9_-]+/)?images/branding/~', $path)
        && !preg_match('~\Atemplates/themes/[a-z0-9_-]+/images/logo(?:-dark)?\.png\z~i', $path)
        && basename($path) !== '.asset-order.php';
}

const ADMIN_CLEANUP_IMAGE_TYPES = [
    'jpg'=>'image/jpeg', 'jpeg'=>'image/jpeg', 'png'=>'image/png', 'gif'=>'image/gif',
    'webp'=>'image/webp', 'avif'=>'image/avif', 'ico'=>'image/x-icon', 'svg'=>'image/svg+xml',
];

function admin_media_cleanup_preview(string $path): void
{
    $mime = ADMIN_CLEANUP_IMAGE_TYPES[strtolower(pathinfo($path, PATHINFO_EXTENSION))] ?? '';
    $file = str_replace('/', DIRECTORY_SEPARATOR, SITE_ROOT . '/' . $path);
    if ($mime === '' || !admin_backup_allowed($path) || !admin_media_cleanup_candidate($path)
        || realpath($file) !== $file || !is_file($file) || ($stream = @fopen($file, 'rb')) === false) {
        http_response_code(404);
        return;
    }
    session_write_close();
    header('Content-Type: ' . $mime);
    header('X-Content-Type-Options: nosniff');
    header('Cache-Control: private, no-store');
    header("Content-Security-Policy: sandbox; default-src 'none'; style-src 'unsafe-inline'");
    fpassthru($stream);
    fclose($stream);
}

function admin_media_cleanup_size(int $bytes, string $language): string
{
    $units = [
        'ru'=>['Б', 'КиБ', 'МиБ', 'ГиБ'],
        'uk'=>['Б', 'КіБ', 'МіБ', 'ГіБ'],
    ][$language] ?? ['B', 'KiB', 'MiB', 'GiB'];
    $unit = 0;
    $size = $bytes;
    while ($size >= 1024 && $unit < 3) {
        $size /= 1024;
        $unit++;
    }
    $value = $unit === 0 ? (string) $size : rtrim(rtrim(number_format($size, 1, '.', ''), '0'), '.');
    return site_format_number($value) . "\u{00a0}" . $units[$unit];
}

function admin_media_shop_references(callable $read, bool $archives = false): void
{
    foreach (['admin/shop/data', 'shop/data'] as $root) {
        $directory = SITE_ROOT . '/' . $root;
        if (!is_dir($directory)) continue;
        if (realpath($directory) !== str_replace('/', DIRECTORY_SEPARATOR, $directory)) {
            throw new RuntimeException('Не удалось полностью проверить ссылки на медиа. Очистка недоступна.');
        }
        $state = site_read_record($directory . '/catalog.php');
        $revision = $state['revision'] ?? '';
        if ($state && (!is_string($revision) || !preg_match('/\A[a-f0-9]{32}\z/', $revision)
            || !is_file($directory . '/catalog-' . $revision . '.php'))) {
            throw new RuntimeException('Не удалось полностью проверить ссылки на медиа. Очистка недоступна.');
        }
        $brands = $state['brandStore'] ?? 'brands';
        if (!is_string($brands) || !preg_match('/\Abrands(?:-[a-f0-9]{32})?\z/', $brands)) {
            throw new RuntimeException('Не удалось полностью проверить ссылки на медиа. Очистка недоступна.');
        }
        $sources = [$directory . '/settings.php', $directory . '/' . $brands . '.php'];
        if ($revision !== '') $sources[] = $directory . '/catalog-' . $revision . '.php';
        if ($archives) {
            $sources = [];
            foreach (['demo-backup-*.php', 'demo-brands-*.php', 'preview-*.php'] as $pattern) {
                $sources = array_merge($sources, glob($directory . '/' . $pattern) ?: []);
            }
        }
        foreach ($sources as $file) {
            $payload = site_read_payload($file, 33554432);
            if ($payload !== null) $read($payload);
        }
    }
}

function admin_media_usage(array $files, string $directory = '', ?bool &$directoryUsed = null, ?array &$archiveOnly = null): array
{
    $media = array_filter($files, static function (string $path): bool {
        return admin_media_cleanup_candidate($path);
    }, ARRAY_FILTER_USE_KEY);
    if (!$media && $directory === '') return [];
    $aliases = [];
    if ($directory !== '') {
        $logical = preg_replace('~\Atemplates/(?:themes/[a-z0-9_-]+/)?~', '', $directory);
        $aliases = array_unique([$directory, $logical, 'templates/' . $logical]);
    }
    $directoryUsed = false;

    $folders = [];
    $addFolder = static function (string $path) use (&$folders) {
        $path = trim($path, '/');
        $folders[$path] = true;
        $logical = preg_replace('~\Atemplates/(?:themes/[a-z0-9_-]+/)?~', '', $path);
        $folders[$logical] = true;
        $folders['templates/' . $logical] = true;
    };
    $read = static function (string $text) use (&$media, $addFolder, $aliases, &$directoryUsed) {
        $text = html_entity_decode(rawurldecode(str_replace(['\/', '\\"'], ['/', '"'], $text)), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        foreach ($aliases as $alias) {
            if (preg_match('~(?:^|[^a-z0-9_-])' . preg_quote($alias, '~') . '(?=[/?#\s"\x27\[\]<>)]|\z)~i', $text)) $directoryUsed = true;
        }
        foreach ($media as $path => $file) {
            if (stripos($text, basename($path)) !== false) unset($media[$path]);
        }
        if (preg_match_all('~\[(gal|fotos|files|banners)(?:[\t ][^\]\r\n]*)?\]\s*(/[^\[\]\r\n]+)\[/\1\]~i', $text, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $path = site_asset_path(trim($match[2]), strtolower($match[1]) === 'files' ? 'files' : 'images');
                if ($path !== '') $addFolder($path);
            }
        }
    };
    foreach (array_diff_key($files, $media) as $path => $file) {
        if (basename($path) === '.asset-order.php' || !preg_match('~(?:\.(?:php|css|js|html|txt|csv|json|xml|config)|/\.htaccess|\A\.htaccess)\z~i', $path)) continue;
        $text = @file_get_contents($file, false, null, 0, 8388609);
        if (!is_string($text) || strlen($text) > 8388608) {
            throw new RuntimeException('Не удалось полностью проверить ссылки на медиа. Очистка недоступна.');
        }
        $read($text);
        $pagePath = $path;
        $pageRoot = SITE_ROOT;
        if (preg_match('~\A(templates/themes/[a-z0-9_-]+/content/pages)/(.+)\z~', $path, $pageMatch)) {
            $pageRoot .= '/' . $pageMatch[1];
            $pagePath = $pageMatch[2];
        }
        if (site_valid_path($pagePath)) {
            $page = site_read_page($pagePath, $pageRoot);
            foreach (['CSS','JS'] as $asset) {
                $code = site_asset_decode($page['fields'][$asset] ?? '');
                if ($code !== null && $code !== '') $read($code);
            }
        }
        if (preg_match('~\A(?:admin/\.site-assets|templates/themes/[a-z0-9_-]+/content/settings/site-assets)\.php\z~', $path)) {
            $assets = site_read_record($file, SITE_ASSET_RECORD_LIMIT);
            foreach (['CSS', 'JS'] as $asset) {
                $code = site_asset_decode($assets[$asset] ?? '');
                if ($code !== null && $code !== '') $read($code);
            }
        }
        if (preg_match('~\A(?:admin/\.banners|templates/themes/[a-z0-9_-]+/content/settings/banners)\.php\z~', $path)) {
            foreach (site_read_record($file, SITE_BANNERS_LIMIT) as $set) {
                $folder = site_asset_path($set['folder'] ?? '', 'images');
                if ($folder !== '') $addFolder($folder);
            }
        }
    }
    foreach (site_global_assets() as $asset) {
        $code = site_asset_decode($asset);
        if ($code !== null && $code !== '') $read($code);
    }
    foreach (['templates/*.php', 'templates/components/*.php', 'templates/css/*.css', 'templates/js/*.js'] as $pattern) {
        foreach (glob(SITE_ROOT . '/' . $pattern) ?: [] as $source) {
            $text = @file_get_contents($source, false, null, 0, 8388609);
            if (!is_string($text) || strlen($text) > 8388608) throw new RuntimeException('Не удалось полностью проверить ссылки на медиа. Очистка недоступна.');
            $read($text);
        }
    }
    admin_media_shop_references($read);
    if ($directory !== '') {
        foreach ($aliases as $alias) {
            if (isset($folders[$alias])) $directoryUsed = true;
        }
    }
    foreach ($media as $path => $file) {
        $folder = dirname($path);
        while ($folder !== '.') {
            $logical = preg_replace('~\Atemplates/themes/[a-z0-9_-]+/~', '', $folder);
            if (isset($folders[$folder]) || isset($folders[$logical])) {
                unset($media[$path]);
                continue 2;
            }
            if ($directory !== '') break;
            $folder = dirname($folder);
        }
    }
    $withoutArchives = $media;
    admin_media_shop_references($read, true);
    $archiveOnly = array_diff_key($withoutArchives, $media);
    return $media;
}

function admin_unused_media(array $files, ?array &$archiveOnly = null): array
{
    $directoryUsed = false;
    $unused = admin_media_usage($files + admin_backup_shop_media_files(), '', $directoryUsed, $archiveOnly);
    ksort($unused, SORT_STRING);
    if ($archiveOnly !== null) ksort($archiveOnly, SORT_STRING);
    return $unused;
}

function admin_remove_media_directory(string $path, array $extensions, string $language)
{
    $labels = site_media_folder_labels($language);
    $folder = SITE_ROOT . '/' . $path;
    if (!preg_match('~\A(?:templates/(?:themes/[a-z0-9_-]+/)?)?(?:images|files)/[^/]+(?:/[^/]+)*\z~', $path)
        || preg_match('~\Atemplates/(?:themes/[a-z0-9_-]+/)?images/branding(?:/|$)~', $path)
        || is_link($folder) || !is_dir($folder) || realpath($folder) !== str_replace('/', DIRECTORY_SEPARATOR, $folder)) {
        throw new RuntimeException($labels[3]);
    }
    $count = 0;
    $filePaths = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($folder, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($iterator as $entry) {
        $absolute = $entry->getPathname();
        $relative = str_replace(DIRECTORY_SEPARATOR, '/', substr($absolute, strlen(SITE_ROOT) + 1));
        if (++$count > 10000 || $entry->isLink() || realpath($absolute) !== str_replace('/', DIRECTORY_SEPARATOR, $absolute)
            || $entry->getFilename()[0] === '.' && $entry->getFilename() !== '.asset-order.php'
            || (!$entry->isDir() && (!$entry->isFile() || !admin_backup_allowed($relative)
                || ($entry->getFilename() !== '.asset-order.php' && !in_array(strtolower($entry->getExtension()), $extensions, true))))) {
            throw new RuntimeException($labels[3]);
        }
        if ($entry->isFile() && $entry->getFilename() !== '.asset-order.php') $filePaths[$relative] = $absolute;
    }
    $files = admin_backup_files();
    $used = false;
    $unused = admin_media_usage($files, $path, $used);
    if ($used || array_diff_key($filePaths, $unused)) throw new RuntimeException($labels[2]);
    if ($count === 0) {
        if (!@rmdir($folder)) throw new RuntimeException($labels[3]);
        return;
    }
    admin_backup_capture($files);
    $staged = admin_backup_directory() . '/.removed-media-' . bin2hex(random_bytes(12));
    if (!@rename($folder, $staged)) throw new RuntimeException($labels[3]);
    // Keep the verified backup even if filesystem cleanup is interrupted.
    $children = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($staged, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($children as $entry) {
        if ($entry->isDir() && !$entry->isLink()) @rmdir($entry->getPathname());
        else @unlink($entry->getPathname());
    }
    @rmdir($staged);
}

function admin_remove_unused_media(array $input): int
{
    $selected = $input['media'] ?? null;
    if (!is_array($selected) || !$selected || count($selected) > 100 || admin_input($input, 'confirm') !== '1') {
        throw new RuntimeException('Выберите файлы и подтвердите очистку.');
    }
    $lock = admin_lock();
    $staged = [];
    $directory = '';
    $keepFiles = false;
    try {
        $files = admin_backup_files();
        $unused = admin_unused_media($files);
        foreach ($selected as $path) {
            if (!is_string($path) || !isset($unused[$path])) {
                throw new RuntimeException('Список неиспользуемых файлов изменился. Повторите поиск.');
            }
        }
        admin_backup_capture($files + array_intersect_key($unused, array_fill_keys($selected, true)));
        $directory = admin_backup_directory() . '/.cleanup-' . bin2hex(random_bytes(8));
        if (!@mkdir($directory, 0700)) throw new RuntimeException('Не удалось подготовить папку очистки.');
        foreach (array_unique($selected) as $path) {
            $file = $unused[$path];
            $temporary = $directory . '/' . count($staged);
            if (!@rename($file, $temporary)) throw new RuntimeException('Не удалось убрать один из выбранных файлов.');
            $staged[$file] = $temporary;
        }
        return count($staged);
    } catch (Throwable $exception) {
        foreach (array_reverse($staged, true) as $file => $temporary) {
            if (!@rename($temporary, $file)) $keepFiles = true;
        }
        if ($keepFiles) throw new RuntimeException('Очистка прервана. Файлы сохранены в закрытой папке резервных копий.', 0, $exception);
        throw $exception;
    } finally {
        if ($directory !== '' && !$keepFiles) {
            foreach ($staged as $temporary) {
                if (is_file($temporary)) @unlink($temporary);
            }
            @rmdir($directory);
        }
        fclose($lock);
    }
}
