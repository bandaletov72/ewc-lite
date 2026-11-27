<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}

const ADMIN_BACKUP_ROOT = SITE_ROOT . '/data/.backups';
const ADMIN_BACKUP_LIMIT = 536870912;

function admin_backup_shop_media_path(string $path): bool
{
    return preg_match('~\A(?:admin/)?shop/images/[a-z0-9][a-z0-9._-]{0,110}\.(?:jpe?g|png|webp|avif|gif)\z~i', $path) === 1;
}

function admin_backup_shop_media_files(): array
{
    $files = [];
    foreach (['admin/shop/images', 'shop/images'] as $root) {
        $directory = SITE_ROOT . '/' . $root;
        if (realpath($directory) !== str_replace('/', DIRECTORY_SEPARATOR, $directory) || !is_dir($directory)) continue;
        foreach (new FilesystemIterator($directory, FilesystemIterator::SKIP_DOTS) as $item) {
            $path = $root . '/' . $item->getFilename();
            if (!$item->isLink() && $item->isFile() && admin_backup_shop_media_path($path) && admin_backup_allowed($path)) {
                $files[$path] = $directory . '/' . $item->getFilename();
            }
        }
    }
    return $files;
}

function admin_backup_allowed(string $path): bool
{
    if ($path === '' || strlen($path) > 320 || preg_match('~[<>:"|?*\\\\\x00-\x1f]~', $path)) {
        return false;
    }
    foreach (explode('/', $path) as $part) {
        if ($part === '' || rtrim($part, ' .') !== $part
            || preg_match('/\A(?:con|prn|aux|nul|com[1-9]|lpt[1-9])(?:\.|\z)/i', $part)) {
            return false;
        }
    }
    if (admin_backup_shop_media_path($path) || site_valid_path($path) || in_array($path, ['.htaccess', 'robots.txt', 'sitemap.xml', 'functions.php'], true)) {
        return true;
    }
    if (preg_match('~\Aadmin/\.(?:banners|callback-settings|captcha|cities|components|contacts|design-theme|domains|fonts|forms|integrations|layout-definitions|libraries|library-versions|reviews|maintenance-jobs|maps|page-custom-fields|page-templates|performance|php-modules|regional|site-assets|site-brand|site-headers|site-settings|snippets(?:-[a-f0-9]{64})?|spam|theme)\.php\z~', $path)) {
        return true;
    }
    if (preg_match('~\Adata/\.review-[0-9]{14}-[a-f0-9]{16}\.php\z~', $path)) return true;
    if (preg_match('~\A(?:data/[A-Za-z0-9_-]+\.(?:txt(?:\.php)?|csv|json)|error/[0-9]{3}\.html|extensions/(?:editor-modules/)?[a-z0-9_-]+\.php|templates/local/(?:components/|iblock/)?[a-z0-9_-]+\.php)\z~', $path)) {
        return true;
    }
    if (preg_match('~\Atemplates/(?:fonts/local/(?:[a-z0-9_-]+\.css|[a-f0-9]{64}\.woff2?)|vendor/local/[a-z]+-[a-f0-9]{16}/(?:manifest\.json|[a-f0-9]{64}\.(?:css|js)))\z~', $path)) return true;
    if (preg_match('~\Atemplates/themes/[a-z0-9_-]+/content/data/[a-z0-9_-]+\.txt\.php\z~i', $path)) return true;
    if (preg_match('~\Atemplates/themes/[a-z0-9_-]+/(?:images|files)/~i', $path)) {
        foreach (explode('/', dirname($path)) as $part) if ($part[0] === '.') return false;
        return basename($path) === '.asset-order.php' || (basename($path)[0] !== '.' &&
            in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), array_merge(site_component_asset_extensions('files'), ['webp', 'avif', 'ico', 'svg']), true));
    }
    if (preg_match('~\Atemplates/themes/(?:\.htaccess|web\.config|[a-z0-9_-]+/(?:(?:[a-z0-9_-]+/)*[a-z0-9_-]+\.(?:php|css|js|png|jpe?g|webp|gif|avif|ico|svg|woff2?)|\.(?:brand|layout-definitions|layouts(?:-[a-f0-9]{64})?)\.php))\z~i', $path)) {
        return true;
    }
    if (!preg_match('~\A(?:images|files|templates/images|templates/files|templates/icons/branding)/~', $path)) {
        return false;
    }
    foreach (explode('/', dirname($path)) as $part) {
        if ($part[0] === '.') return false;
    }
    return basename($path) === '.asset-order.php' || (basename($path)[0] !== '.' &&
        in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), array_merge(site_component_asset_extensions('files'), ['webp', 'avif', 'ico']), true));
}

function admin_backup_files(): array
{
    $paths = array_column(site_pages(true, SITE_ROOT), 'path');
    foreach (['admin/.*.php', 'data/*', 'data/.review-*.php', 'error/*.html', 'extensions/*.php', 'extensions/editor-modules/*.php',
        'templates/local/*.php', 'templates/local/components/*.php', 'templates/local/iblock/*.php'] as $pattern) {
        foreach (glob(SITE_ROOT . '/' . $pattern) ?: [] as $file) {
            $paths[] = str_replace(DIRECTORY_SEPARATOR, '/', substr($file, strlen(SITE_ROOT) + 1));
        }
    }
    foreach (['images', 'files', 'templates/images', 'templates/files', 'templates/icons/branding', 'templates/themes', 'templates/fonts/local', 'templates/vendor/local'] as $directory) {
        $source = SITE_ROOT . '/' . $directory;
        if (!is_dir($source) || is_link($source)) continue;
        $entries = new RecursiveCallbackFilterIterator(new RecursiveDirectoryIterator($source, FilesystemIterator::SKIP_DOTS),
            static function (SplFileInfo $item): bool {
                return !$item->isLink() && (!$item->isDir() || $item->getFilename()[0] !== '.');
            });
        foreach (new RecursiveIteratorIterator($entries) as $item) {
            $paths[] = str_replace(DIRECTORY_SEPARATOR, '/', substr($item->getPathname(), strlen(SITE_ROOT) + 1));
        }
    }
    $files = [];
    foreach (array_unique(array_merge($paths, ['.htaccess', 'robots.txt', 'sitemap.xml', 'functions.php'])) as $path) {
        $file = SITE_ROOT . '/' . $path;
        if (admin_backup_allowed($path) && is_file($file) && realpath($file) === str_replace('/', DIRECTORY_SEPARATOR, $file)) {
            $files[$path] = $file;
        }
    }
    ksort($files, SORT_STRING);
    return $files;
}

function admin_backup_directory(): string
{
    $path = ADMIN_BACKUP_ROOT;
    if (is_link($path) || (!is_dir($path) && !@mkdir($path, 0700))
        || realpath($path) !== str_replace('/', DIRECTORY_SEPARATOR, $path)) {
        throw new RuntimeException('Не удалось открыть закрытую папку резервных копий.');
    }
    return $path;
}

function admin_backup_path(string $name): string
{
    if (!preg_match('/\Abackup-[0-9]{8}T[0-9]{6}Z-[a-f0-9]{12}\.zip\z/', $name)) return '';
    $file = ADMIN_BACKUP_ROOT . '/' . $name;
    return realpath($file) === str_replace('/', DIRECTORY_SEPARATOR, $file) && is_file($file) ? $file : '';
}

function admin_backup_list(): array
{
    $files = [];
    foreach (glob(ADMIN_BACKUP_ROOT . '/backup-*.zip') ?: [] as $file) {
        $name = basename($file);
        if (admin_backup_path($name) !== '') $files[$name] = filesize($file);
    }
    krsort($files);
    return $files;
}

function admin_backup_needs_attention(): bool
{
    static $attention;
    if ($attention !== null) return $attention;
    $attention = true;
    if (!class_exists('ZipArchive')) return $attention;

    $today = new DateTimeImmutable('today');
    $month = $today->modify('first day of last month');
    $cutoff = $month->modify('+' . (min((int) $today->format('j'), (int) $month->format('t')) - 1) . ' days');
    $tomorrow = $today->modify('+1 day')->getTimestamp();
    foreach (admin_backup_list() as $name => $size) {
        $archive = new ZipArchive();
        if ($archive->open(admin_backup_path($name), ZipArchive::CHECKCONS) !== true) continue;
        $created = $archive->statName('site-backup.json')['mtime'] ?? 0;
        $archive->close();
        if ($created > 0 && $created < $tomorrow
            && (new DateTimeImmutable('@' . $created))->setTimezone($today->getTimezone())->setTime(0, 0) > $cutoff) {
            return $attention = false;
        }
    }
    return $attention;
}

function admin_backup_open(string $file, int $flags): ZipArchive
{
    if (!class_exists('ZipArchive')) throw new RuntimeException('Для резервных копий требуется расширение PHP Zip.');
    $zip = new ZipArchive();
    if ($zip->open($file, $flags) !== true) throw new RuntimeException('Не удалось открыть архив ZIP.');
    return $zip;
}

function admin_backup_read(string $file, string $stage = ''): array
{
    if (!class_exists('ZipArchive')) throw new RuntimeException('Для резервных копий требуется расширение PHP Zip.');
    if (filesize($file) > ADMIN_BACKUP_LIMIT + 2097152) throw new RuntimeException('Размер резервной копии превышает 512 МиБ.');
    $zip = admin_backup_open($file, ZipArchive::CHECKCONS);
    try {
        $manifest = json_decode((string) $zip->getFromName('site-backup.json', 2097152), true);
        $files = $manifest['files'] ?? null;
        if (($manifest['format'] ?? null) !== 1 || !is_array($files) || !isset($files['index.php'])
            || count($files) > 10000 || $zip->numFiles !== count($files) + 1) {
            throw new RuntimeException('Выберите ZIP, созданный системой резервного копирования сайта.');
        }
        $staged = [];
        $size = 0;
        foreach ($files as $path => $hash) {
            $item = is_string($path) && admin_backup_allowed($path) ? $zip->statName($path) : false;
            if ($item === false || !is_string($hash) || !preg_match('/\A[a-f0-9]{64}\z/', $hash)
                || ($size += $item['size']) > ADMIN_BACKUP_LIMIT) {
                throw new RuntimeException('В архиве недопустимый файл или превышен лимит 512 МиБ.');
            }
            $stream = $zip->getStream($path);
            if ($stream === false) throw new RuntimeException('Не удалось прочитать архив.');
            try {
                if ($stage === '') {
                    $digest = hash_init('sha256');
                    $sizeRead = hash_update_stream($digest, $stream, $item['size'] + 1);
                    $actual = hash_final($digest);
                } else {
                    $staged[$path] = $stage . '/new-' . count($staged);
                    $target = @fopen($staged[$path], 'xb');
                    if ($target === false) throw new RuntimeException('Не удалось подготовить файлы восстановления.');
                    try {
                        $sizeRead = stream_copy_to_stream($stream, $target, $item['size'] + 1);
                    } finally {
                        fclose($target);
                    }
                    $actual = hash_file('sha256', $staged[$path]);
                }
                if ($sizeRead !== $item['size'] || $actual !== $hash) throw new RuntimeException('Контрольная сумма резервной копии не совпала.');
                $logicalPage = preg_match('~\Atemplates/themes/[a-z0-9_-]+/content/pages/(.+)\z~', $path, $themePage) ? $themePage[1] : $path;
                if ($stage !== '' && site_valid_path($logicalPage)) {
                    $header = site_page_header($logicalPage);
                    $prefix = (string) file_get_contents($staged[$path], false, null, 0, 2 * strlen($header) + 2);
                    if (strpos(str_replace("\r\n", "\n", $prefix), $header . "\n") !== 0) {
                        throw new RuntimeException('Архив содержит страницу с неизвестным форматом.');
                    }
                } elseif ($stage !== '' && !admin_backup_shop_media_path($path) && (strpos($path, 'admin/') === 0 || basename($path) === '.asset-order.php'
                    || (strpos($path, 'data/') === 0 && substr($path, -4) === '.php')
                    || preg_match('~\Atemplates/themes/[a-z0-9_-]+/content/(?:settings|data)/~', $path))) {
                    $prefix = file_get_contents($staged[$path], false, null, 0, strlen(SITE_RECORD_HEADER));
                    if ($prefix !== SITE_RECORD_HEADER) throw new RuntimeException('Архив содержит файл данных с неизвестным форматом.');
                }
            } finally {
                fclose($stream);
            }
        }
        return $staged;
    } finally {
        $zip->close();
    }
}

function admin_backup_capture(array $files, ?callable $progress = null): string
{
    if (!isset($files['index.php']) || count($files) > 10000) throw new RuntimeException('Не удалось собрать страницы для резервной копии.');
    $directory = admin_backup_directory();
    $temporary = $directory . '/.build-' . bin2hex(random_bytes(8));
    $name = 'backup-' . gmdate('Ymd\THis\Z') . '-' . bin2hex(random_bytes(6)) . '.zip';
    try {
        if (!class_exists('ZipArchive')) throw new RuntimeException('Для резервных копий требуется расширение PHP Zip.');
        $zip = admin_backup_open($temporary, ZipArchive::CREATE | ZipArchive::EXCL);
        $manifest = ['format' => 1, 'files' => []];
        $total = 0;
        try {
            $fileCount = count($files);
            foreach ($files as $path => $file) {
                $size = filesize($file);
                $hash = hash_file('sha256', $file);
                if ($size === false || ($total += $size) > ADMIN_BACKUP_LIMIT || $hash === false || !$zip->addFile($file, $path)) {
                    throw new RuntimeException('Не удалось добавить файл в архив. Общий лимит — 512 МиБ.');
                }
                $manifest['files'][$path] = $hash;
                if ($progress !== null) $progress('backup', count($manifest['files']), $fileCount);
            }
            $json = json_encode($manifest, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if (!is_string($json) || !$zip->addFromString('site-backup.json', $json)) {
                throw new RuntimeException('Не удалось создать список файлов архива.');
            }
        } finally {
            $closed = $zip->close();
        }
        if (!$closed) throw new RuntimeException('Не удалось записать резервную копию.');
        if ($progress !== null) $progress('backup-verify');
        admin_backup_read($temporary);
        if (!@rename($temporary, $directory . '/' . $name)) throw new RuntimeException('Не удалось сохранить резервную копию.');
        return $name;
    } finally {
        if (is_file($temporary)) @unlink($temporary);
    }
}

function admin_backup_restore(string $archive, array $current)
{
    $stage = admin_backup_directory() . '/.restore-' . bin2hex(random_bytes(8));
    if (!@mkdir($stage, 0700)) throw new RuntimeException('Не удалось подготовить папку восстановления.');
    $originals = [];
    $replaced = [];
    $keepStage = false;
    $marked = false;
    try {
        $incoming = admin_backup_read($archive, $stage);
        $current += array_intersect_key(admin_backup_shop_media_files(), $incoming);
        admin_backup_capture($current);
        if (@file_put_contents(SITE_UPDATE_MARKER, '') === false) throw new RuntimeException('Не удалось включить режим обслуживания.');
        $marked = true;
        foreach (array_unique(array_merge(array_keys($current), array_keys($incoming))) as $path) {
            $file = SITE_ROOT . '/' . $path;
            admin_make_page_directory($path);
            if (is_link($file) || (file_exists($file) && (!is_file($file) || !isset($current[$path])))) {
                throw new RuntimeException('При восстановлении обнаружен занятый путь.');
            }
            if (isset($current[$path], $incoming[$path]) && hash_file('sha256', $file) === hash_file('sha256', $incoming[$path])) {
                continue;
            }
            if (is_file($file)) {
                $saved = $stage . '/old-' . count($originals);
                if (!@rename($file, $saved)) throw new RuntimeException('Не удалось подготовить замену файла сайта.');
                $originals[$file] = $saved;
            }
            if (isset($incoming[$path])) {
                if (!@rename($incoming[$path], $file)) throw new RuntimeException('Не удалось восстановить файл сайта.');
                $replaced[] = $file;
            }
            if (function_exists('opcache_invalidate')) @opcache_invalidate($file, true);
        }
    } catch (Throwable $exception) {
        foreach (array_reverse($replaced) as $file) {
            if (!isset($originals[$file]) && !@unlink($file)) $keepStage = true;
        }
        foreach (array_reverse($originals, true) as $file => $saved) {
            if (!@rename($saved, $file)) $keepStage = true;
            elseif (function_exists('opcache_invalidate')) @opcache_invalidate($file, true);
        }
        if ($keepStage) throw new RuntimeException('Восстановление прервано. Исходные файлы сохранены в закрытой папке резервных копий.', 0, $exception);
        throw $exception;
    } finally {
        if ($marked) @unlink(SITE_UPDATE_MARKER);
        if (!$keepStage) {
            foreach (glob($stage . '/*') ?: [] as $file) @unlink($file);
            @rmdir($stage);
        }
        clearstatcache(true);
    }
}

function admin_backup_action(string $action, array $input, array $upload): string
{
    $lock = admin_lock();
    try {
        if ($action === 'create-content-backup') {
            return admin_backup_capture(admin_backup_files());
        }
        if ($action === 'import-content-backup') {
            $file = $upload['tmp_name'] ?? '';
            if (($upload['error'] ?? null) !== UPLOAD_ERR_OK || !is_string($file) || !is_uploaded_file($file)) {
                throw new RuntimeException('Выберите архив ZIP и проверьте лимит загрузки PHP.');
            }
            admin_backup_read($file);
            $name = 'backup-' . gmdate('Ymd\THis\Z') . '-' . bin2hex(random_bytes(6)) . '.zip';
            if (!@move_uploaded_file($file, admin_backup_directory() . '/' . $name)) {
                throw new RuntimeException('Не удалось загрузить резервную копию.');
            }
            return $name;
        }
        $file = admin_backup_path(admin_input($input, 'backup'));
        if ($file === '' || admin_input($input, 'confirm') !== '1') {
            throw new RuntimeException('Выберите резервную копию и подтвердите восстановление.');
        }
        admin_backup_restore($file, admin_backup_files());
        return basename($file);
    } finally {
        fclose($lock);
    }
}
