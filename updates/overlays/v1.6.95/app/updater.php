<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}

const SITE_UPDATE_ASSET = 'ewc-lite-update.zip';
const SITE_UPDATE_LEGACY_ASSET = 'php-site-update.zip';
const SITE_UPDATE_LIMIT = 16777216;
const SITE_UPDATE_MARKER = SITE_ROOT . '/app/.update-in-progress';

function site_update_version(): string
{
    $file = SITE_ROOT . '/app/version.php';
    return is_file($file) ? require $file : '0.0.0';
}

function site_update_managed(string $path): bool
{
    return in_array($path, ['.htaccess', 'maintenance/github.php', 'maintenance/release-public.pem'], true)
        || ($path !== 'admin/write-lock.php'
        && preg_match('~\A(?:update\.php|router\.php|form/index\.php|app/(?:extensions/)?[a-z][a-z0-9_-]*\.php|admin/[a-z][a-z0-9_-]*\.php|templates/(?:(?:components|iblock)/)?[a-z][a-z0-9_-]*\.php|templates/(?:css/site\.css|js/(?:site|admin)\.js|icons/contact/[a-z]+\.png))\z~', $path) === 1);
}

function site_update_build(string $destination)
{
    $zip = new ZipArchive();
    if (file_exists($destination) || $zip->open($destination, ZipArchive::CREATE | ZipArchive::EXCL) !== true) {
        throw new RuntimeException('Нужен путь к новому ZIP-файлу.');
    }
    $manifest = ['version' => site_update_version(), 'files' => []];
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(SITE_ROOT, FilesystemIterator::SKIP_DOTS));
    foreach ($files as $file) {
        $path = str_replace(DIRECTORY_SEPARATOR, '/', substr($file->getPathname(), strlen(SITE_ROOT) + 1));
        if (preg_match('~\Aadmin/languages/([a-z]{2})\.php\z~', $path, $language)) {
            // Ship under an existing managed path so older engines can install the package.
            $path = 'app/language-' . $language[1] . '.php';
        }
        if ($file->isLink() || !$file->isFile() || !site_update_managed($path)) {
            continue;
        }
        $bytes = file_get_contents($file->getPathname());
        if ($bytes === false || !$zip->addFromString($path, $bytes)) {
            throw new RuntimeException('Не удалось добавить файл в ZIP.');
        }
        $manifest['files'][$path] = hash('sha256', $bytes);
    }
    ksort($manifest['files'], SORT_STRING);
    $zip->addFromString('manifest.json', json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    if (!$zip->close()) {
        throw new RuntimeException('Не удалось сохранить ZIP обновления.');
    }
}

function site_update_github_repo(): string
{
    $repo = trim((string) getenv('SITE_GITHUB_REPO'));
    if ($repo === '') {
        $config = SITE_ROOT . '/maintenance/github.php';
        $repo = is_file($config) ? (string) require $config : '';
    }
    if (strcasecmp($repo, 'bandaletov72/php-site-updates') === 0) {
        return 'bandaletov72/ewc-lite';
    }
    return preg_match('~\A[a-zA-Z0-9][a-zA-Z0-9-]{0,38}/[a-zA-Z0-9_.-]+\z~', $repo) ? $repo : '';
}

function site_update_download(string $url, string $accept, int $limit, ?callable $onBytes = null): string
{
    $token = (string) getenv('SITE_GITHUB_TOKEN');
    if ($token !== '' && !preg_match('/\A[a-zA-Z0-9_]+\z/', $token)) {
        throw new RuntimeException('Проверьте формат токена GitHub.');
    }
    for ($redirect = 0; $redirect < 4; $redirect++) {
        if (!preg_match('~\Ahttps://(api\.github\.com|raw\.githubusercontent\.com|release-assets\.githubusercontent\.com|objects\.githubusercontent\.com)/~', $url, $host)) {
            throw new RuntimeException('GitHub вернул неподдерживаемый адрес загрузки.');
        }
        $headers = "User-Agent: EWC-Lite-Updater\r\nAccept: " . $accept;
        if ($host[1] === 'api.github.com') {
            if ($token !== '') {
                $headers .= "\r\nAuthorization: Bearer " . $token;
            }
        }
        unset($http_response_header);
        $context = stream_context_create(['http' => ['header' => $headers, 'timeout' => 12,
            'ignore_errors' => true, 'follow_location' => 0]]);
        $stream = @fopen($url, 'rb', false, $context);
        $response = $http_response_header ?? [];
        try {
            if (!preg_match('~\AHTTP/\S+\s+([0-9]{3})~', $response[0] ?? '', $status)) {
                throw new RuntimeException('Нет ответа от GitHub. Проверьте HTTPS-соединение сервера.');
            }
            $code = (int) $status[1];
            if ($code >= 300 && $code < 400) {
                $url = '';
                foreach ($response as $line) {
                    if (stripos($line, 'Location: ') === 0) {
                        $url = substr($line, strlen('Location: '));
                        break;
                    }
                }
                continue;
            }
            if ($code !== 200 || $stream === false) {
                throw new RuntimeException('GitHub не отдал файл обновления. HTTP ' . $code . '.', $code);
            }
            $length = null;
            foreach ($response as $line) {
                if (preg_match('/\AContent-Length:\s*([0-9]+)\z/i', $line, $size)) {
                    $length = (int) $size[1];
                }
            }
            if ($length !== null && $length > $limit) {
                throw new RuntimeException('GitHub не отдал файл обновления. HTTP ' . $code . '.', $code);
            }
            $bytes = '';
            if ($onBytes !== null) $onBytes(0, $length);
            while (!feof($stream) && strlen($bytes) <= $limit) {
                $chunk = @fread($stream, min(131072, $limit + 1 - strlen($bytes)));
                if ($chunk === false || $chunk === '') break;
                $bytes .= $chunk;
                if ($onBytes !== null) $onBytes(strlen($bytes), $length);
            }
            if (!feof($stream) || !empty(stream_get_meta_data($stream)['timed_out'])
                || strlen($bytes) > $limit || ($length !== null && strlen($bytes) !== $length)) {
                throw new RuntimeException('GitHub не отдал файл обновления. HTTP ' . $code . '.', $code);
            }
            return $bytes;
        } finally {
            if (is_resource($stream)) fclose($stream);
        }
    }
    throw new RuntimeException('Слишком много перенаправлений GitHub.');
}

function site_update_release_notes($notes): string
{
    return is_string($notes) && strlen($notes) <= 4000 && preg_match('//u', $notes) === 1 ? trim($notes) : '';
}

function site_update_github_contents(string $repository): array
{
    return (string) getenv('SITE_GITHUB_TOKEN') === ''
        ? ['https://raw.githubusercontent.com/' . $repository . '/HEAD/updates/', '*/*']
        : ['https://api.github.com/repos/' . $repository . '/contents/updates/', 'application/vnd.github.raw+json'];
}

function site_update_github_metadata(string $repository): array
{
    list($contents, $accept) = site_update_github_contents($repository);
    $metadata = json_decode(site_update_download($contents . 'latest.json', $accept, 16384), true);
    return is_array($metadata) ? $metadata : [];
}

function site_update_github_spam_version(string $repository): string
{
    $version = site_update_github_metadata($repository)['spamVersion'] ?? '';
    return site_spam_valid_version($version) ? $version : '';
}

function site_update_github_source(string $repository, bool $includeCurrent = false, ?array $metadata = null): array
{
    $api = 'https://api.github.com/repos/' . $repository;
    try {
        $release = json_decode(site_update_download($api . '/releases/latest', 'application/vnd.github+json', 1048576), true);
    } catch (RuntimeException $exception) {
        $anonymous = (string) getenv('SITE_GITHUB_TOKEN') === '';
        if ($exception->getCode() !== 404
            && !($anonymous && in_array($exception->getCode(), [403, 429], true))) {
            throw $exception;
        }
        list($contents, $accept) = site_update_github_contents($repository);
        $latest = $metadata ?? site_update_github_metadata($repository);
        $version = $latest['version'] ?? '';
        if (!is_string($version) || !preg_match('/\A[0-9]+\.[0-9]+\.[0-9]+\z/', $version)) {
            throw new RuntimeException('В репозитории указан неверный номер версии.');
        }
        $name = $latest['updateAsset'] ?? SITE_UPDATE_LEGACY_ASSET;
        if (!in_array($name, [SITE_UPDATE_ASSET, SITE_UPDATE_LEGACY_ASSET], true)) throw new RuntimeException('Версия недоступна');
        $asset = $contents . 'v' . $version . '/' . $name;
        return ['version' => $version, 'notes' => site_update_release_notes($latest['notes'] ?? ''),
            'archive' => $asset, 'signature' => $asset . '.sig', 'accept' => $accept, 'filename'=>$name];
    }

    $tag = $release['tag_name'] ?? '';
    if (!is_string($tag) || !preg_match('/\Av([0-9]+\.[0-9]+\.[0-9]+)\z/', $tag, $version)) {
        throw new RuntimeException('В GitHub Releases нужен опубликованный релиз с тегом vX.Y.Z.');
    }
    $info = ['version' => $version[1], 'notes' => site_update_release_notes($release['body'] ?? '')];
    if (!$includeCurrent && !version_compare($version[1], site_update_version(), '>')) {
        return $info;
    }
    $assets = [];
    foreach ($release['assets'] ?? [] as $asset) {
        if (isset($asset['name'], $asset['id']) && is_int($asset['id']) && $asset['id'] > 0) {
            $assets[$asset['name']] = $asset['id'];
        }
    }
    $name = isset($assets[SITE_UPDATE_ASSET], $assets[SITE_UPDATE_ASSET . '.sig']) ? SITE_UPDATE_ASSET : SITE_UPDATE_LEGACY_ASSET;
    if (!isset($assets[$name], $assets[$name . '.sig'])) {
        throw new RuntimeException('В релизе должны быть ZIP обновления и файл его подписи.');
    }
    return $info + [
        'archive' => $api . '/releases/assets/' . $assets[$name],
        'signature' => $api . '/releases/assets/' . $assets[$name . '.sig'],
        'accept' => 'application/octet-stream', 'filename'=>$name];
}

function site_update_from_github(?callable $progress = null): string
{
    $repository = site_update_github_repo();
    if ($repository === '') {
        throw new RuntimeException('Укажите репозиторий в SITE_GITHUB_REPO или maintenance/github.php.');
    }
    if (!class_exists('ZipArchive') || !function_exists('openssl_verify')) {
        throw new RuntimeException('Для обновления с GitHub нужны PHP-расширения Zip и OpenSSL.');
    }
    if ($progress !== null) $progress('source');
    $source = site_update_github_source($repository);
    if (!version_compare($source['version'], site_update_version(), '>')) {
        return 'Установлена актуальная версия.';
    }
    return site_update_use_signed_archive($source, static function (string $archive) use ($source, $progress): string {
        return site_update_apply($archive, $source['version'], $progress);
    }, $progress);
}

function site_update_use_signed_archive(array $source, callable $read, ?callable $progress = null)
{
    if (!class_exists('ZipArchive') || !function_exists('openssl_verify')) {
        throw new RuntimeException('Для обновления с GitHub нужны PHP-расширения Zip и OpenSSL.');
    }
    $keyFile = (string) getenv('SITE_UPDATE_PUBLIC_KEY');
    $key = @file_get_contents($keyFile !== '' ? $keyFile : SITE_ROOT . '/maintenance/release-public.pem');
    if ($key === false) {
        throw new RuntimeException('Укажите доверенный открытый ключ подписи обновлений.');
    }
    $filename = $source['filename'] ?? '';
    if ($progress !== null) $progress('archive', null, null, $filename);
    $bytes = site_update_download($source['archive'], $source['accept'], SITE_UPDATE_LIMIT,
        $progress === null ? null : static function (int $received, ?int $total) use ($progress, $filename) {
            $progress('archive', $received, $total, $filename);
        });
    $signatureName = $filename === '' ? '' : $filename . '.sig';
    if ($progress !== null) $progress('signature', null, null, $signatureName);
    $signature = site_update_download($source['signature'], $source['accept'], 8192,
        $progress === null ? null : static function (int $received, ?int $total) use ($progress, $signatureName) {
            $progress('signature', $received, $total, $signatureName);
        });
    if ($progress !== null) $progress('verify');
    if (@openssl_verify($bytes, $signature, $key, OPENSSL_ALGO_SHA256) !== 1) {
        throw new RuntimeException('Подпись обновления GitHub не прошла проверку.');
    }
    $temporary = tempnam(sys_get_temp_dir(), 'site-update-');
    if ($temporary === false) {
        throw new RuntimeException('Не удалось создать временный файл обновления.');
    }
    try {
        if (file_put_contents($temporary, $bytes) !== strlen($bytes)) {
            throw new RuntimeException('Не удалось записать загруженное обновление.');
        }
        return $read($temporary);
    } finally {
        unlink($temporary);
    }
}

function site_update_apply(string $archive, string $expectedVersion = '', ?callable $progress = null): string
{
    if (!class_exists('ZipArchive') || !is_file($archive) || filesize($archive) > SITE_UPDATE_LIMIT) {
        throw new RuntimeException('Укажите доступный ZIP обновления. Требуется PHP-расширение Zip.');
    }
    $zip = new ZipArchive();
    if ($zip->open($archive, ZipArchive::CHECKCONS) !== true) {
        throw new RuntimeException('ZIP обновления повреждён.');
    }
    $files = [];
    try {
        $raw = $zip->getFromName('manifest.json', 262144);
        $manifest = $raw === false ? null : json_decode($raw, true);
        $version = $manifest['version'] ?? '';
        if (!preg_match('/\A[0-9]+\.[0-9]+\.[0-9]+\z/', $version)
            || ($expectedVersion !== '' && $expectedVersion !== $version)
            || !is_array($manifest['files'] ?? null) || count($manifest['files']) !== $zip->numFiles - 1
            || !isset($manifest['files']['app/version.php'])) {
            throw new RuntimeException('Список файлов обновления не прошёл проверку.');
        }
        $total = 0;
        $fileCount = count($manifest['files']);
        foreach ($manifest['files'] as $path => $digest) {
            if (!is_string($path) || !site_update_managed($path) || !is_string($digest)) {
                throw new RuntimeException('Архив содержит файл за пределами движка.');
            }
            $stat = $zip->statName($path);
            if ($stat === false || ($total += $stat['size']) > SITE_UPDATE_LIMIT) {
                throw new RuntimeException('Не удалось проверить размер файлов обновления.');
            }
            $bytes = $zip->getFromName($path);
            if ($bytes === false || !hash_equals($digest, hash('sha256', $bytes))) {
                throw new RuntimeException('Контрольная сумма файла обновления не совпала.');
            }
            $files[$path] = $bytes;
            if ($progress !== null) $progress('verify', count($files), $fileCount);
        }
    } finally {
        $zip->close();
    }

    $lock = @fopen(SITE_ROOT . '/admin/write-lock.php', 'r+b');
    if ($lock === false || !flock($lock, LOCK_EX)) {
        if (is_resource($lock)) {
            fclose($lock);
        }
        throw new RuntimeException('Не удалось получить блокировку движка.');
    }
    try {
        if (!version_compare($version, site_update_version(), '>')) {
            return 'Установлена актуальная версия.';
        }
        if ($progress !== null) $progress('engine-content');
        site_update_replace($files, $progress);
        return 'Движок обновлён до версии ' . $version . '.';
    } finally {
        fclose($lock);
    }
}

function site_update_replace(array $files, ?callable $progress = null)
{
    $staged = [];
    $backups = [];
    $changed = [];
    $marked = false;
    try {
        $fileCount = count($files);
        $prepared = 0;
        foreach ($files as $path => $bytes) {
            if ($progress !== null) $progress('prepare', $prepared++, $fileCount);
            $file = SITE_ROOT . '/' . $path;
            if (in_array($path, ['.htaccess', 'maintenance/github.php', 'maintenance/release-public.pem'], true)
                && (file_exists($file) || is_link($file))) {
                continue;
            }
            $parent = dirname($file);
            if ((!is_dir($parent) && !@mkdir($parent, 0755, true))
                || realpath($parent) !== str_replace('/', DIRECTORY_SEPARATOR, $parent)
                || is_link($file) || (file_exists($file) && !is_file($file))) {
                throw new RuntimeException('Недопустимый путь файла обновления.');
            }
            if (is_file($file) && filesize($file) === strlen($bytes) && hash_file('sha256', $file) === hash('sha256', $bytes)) {
                continue;
            }
            $mode = is_file($file) ? fileperms($file) & 0777 : 0644;
            if (is_file($file)) {
                $backup = @tempnam($parent, '.backup-');
                if ($backup === false) {
                    throw new RuntimeException('Не удалось сделать резервную копию файла движка.');
                }
                $backups[$file] = $backup;
                if (!@copy($file, $backup)) {
                    throw new RuntimeException('Не удалось сделать резервную копию файла движка.');
                }
                @chmod($backup, $mode);
            }
            $temporary = @tempnam($parent, '.prepare-');
            if ($temporary === false) {
                throw new RuntimeException('Не удалось подготовить файлы обновления.');
            }
            $staged[$file] = $temporary;
            if (@file_put_contents($temporary, $bytes) !== strlen($bytes)) {
                throw new RuntimeException('Не удалось подготовить файлы обновления.');
            }
            @chmod($temporary, $mode);
        }
        if ($progress !== null) $progress('prepare', $fileCount, $fileCount);
        if (@file_put_contents(SITE_UPDATE_MARKER, '') === false) {
            throw new RuntimeException('Не удалось включить режим обслуживания.');
        }
        $marked = true;
        $stagedCount = count($staged);
        if ($progress !== null) $progress('install', 0, $stagedCount);
        foreach ($staged as $file => $temporary) {
            if (!@rename($temporary, $file)) {
                throw new RuntimeException('Не удалось заменить файл движка.');
            }
            $changed[] = $file;
            if ($progress !== null) $progress('install', count($changed), $stagedCount);
            if (function_exists('opcache_invalidate')) {
                @opcache_invalidate($file, true);
            }
        }
    } catch (RuntimeException $exception) {
        $restored = true;
        foreach (array_reverse($changed) as $file) {
            if (!(isset($backups[$file]) ? @rename($backups[$file], $file) : @unlink($file))) {
                unset($backups[$file]);
                $restored = false;
            }
        }
        if (!$restored) {
            throw new RuntimeException('Обновление прервано, восстановление файлов завершилось с ошибкой.');
        }
        throw $exception;
    } finally {
        foreach (array_merge(array_values($staged), array_values($backups)) as $temporary) {
            if (is_file($temporary)) {
                @unlink($temporary);
            }
        }
        if ($marked) {
            @unlink(SITE_UPDATE_MARKER);
        }
        clearstatcache(true);
    }
}
