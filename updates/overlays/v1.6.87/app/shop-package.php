<?php
if (!defined('SITE_ROOT')) { http_response_code(404); exit; }

const SITE_SHOP_SWITCH = SITE_ROOT . '/admin/.shop-enabled.php';
const SITE_SHOP_REPOSITORY = 'bandaletov72/ewc-lite';
const SITE_SHOP_ASSET = 'marketplace.zip';
const SITE_SHOP_BASE_FILES = ['.htaccess', 'version.php', 'module.php', 'catalog.php', 'orders.php', 'storefront.php',
    'actions.php', 'index.php', 'shop.css', 'admin.php', 'admin-view.php', 'import.php', 'language.php'];
const SITE_SHOP_EDITOR_FILES = ['admin-products.php', 'admin-products-view.php'];

function site_shop_build(string $destination): void
{
    $zip = new ZipArchive();
    if ($zip->open($destination, ZipArchive::CREATE | ZipArchive::EXCL) !== true) throw new RuntimeException('Choose a new ZIP file.');
    $manifest = ['module'=>'shop', 'version'=>require SITE_ROOT . '/shop/version.php', 'files'=>[]];
    foreach (array_merge(SITE_SHOP_BASE_FILES, SITE_SHOP_EDITOR_FILES) as $name) {
        $path = 'shop/' . $name;
        $bytes = file_get_contents(SITE_ROOT . '/' . $path);
        if ($bytes === false || !$zip->addFromString($path, $bytes)) throw new RuntimeException('Cannot build the Marketplace package.');
        $manifest['files'][$path] = hash('sha256', $bytes);
    }
    $zip->addFromString('manifest.json', json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    if (!$zip->close()) throw new RuntimeException('Cannot save the Marketplace package.');
}

function site_shop_read_package(string $archive): array
{
    $zip = new ZipArchive();
    if ($zip->open($archive, ZipArchive::CHECKCONS) !== true) throw new RuntimeException('Неверный архив модуля.');
    $files = [];
    try {
        $manifest = json_decode($zip->getFromName('manifest.json', 32768) ?: '', true);
        $version = $manifest['version'] ?? '';
        $digests = $manifest['files'] ?? null;
        $names = is_array($digests) && count($digests) === count(SITE_SHOP_BASE_FILES) ? SITE_SHOP_BASE_FILES
            : array_merge(SITE_SHOP_BASE_FILES, SITE_SHOP_EDITOR_FILES);
        $paths = array_map(static function (string $file): string { return 'shop/' . $file; }, $names);
        if (($manifest['module'] ?? '') !== 'shop' || !is_string($version) || !preg_match('/\A[0-9]+\.[0-9]+\.[0-9]+\z/', $version)
            || !is_array($manifest['files'] ?? null) || count($manifest['files']) !== count($paths)
            || $zip->numFiles !== count($paths) + 1) throw new RuntimeException('Неверный состав архива модуля.');
        $total = 0;
        foreach ($paths as $path) {
            $stat = $zip->statName($path);
            if ($stat === false || ($total += $stat['size']) > SITE_UPDATE_LIMIT) {
                throw new RuntimeException('Содержимое архива модуля слишком велико.');
            }
            $bytes = $zip->getFromName($path);
            $digest = $manifest['files'][$path] ?? '';
            if ($bytes === false || !is_string($digest) || !hash_equals($digest, hash('sha256', $bytes))) {
                throw new RuntimeException('Контрольная сумма файла модуля не совпала.');
            }
            $files[$path] = $bytes;
        }
    } finally {
        $zip->close();
    }
    return ['version'=>$version,'files'=>$files];
}

function site_shop_github_source(): array
{
    $api = 'https://api.github.com/repos/' . SITE_SHOP_REPOSITORY;
    $releases = json_decode(site_update_download($api . '/releases?per_page=30', 'application/vnd.github+json', 4194304), true);
    foreach (is_array($releases) ? $releases : [] as $release) {
        $tag = $release['tag_name'] ?? '';
        if (!empty($release['draft']) || !empty($release['prerelease']) || !is_string($tag) || $tag === '') continue;
        $assets = [];
        foreach ($release['assets'] ?? [] as $asset) {
            if (is_string($asset['name'] ?? null) && is_int($asset['id'] ?? null) && $asset['id'] > 0) $assets[$asset['name']] = $asset['id'];
        }
        if (!isset($assets[SITE_SHOP_ASSET], $assets[SITE_SHOP_ASSET . '.sig'])) continue;
        return ['archive'=>$api . '/releases/assets/' . $assets[SITE_SHOP_ASSET],
            'signature'=>$api . '/releases/assets/' . $assets[SITE_SHOP_ASSET . '.sig'], 'accept'=>'application/octet-stream',
            'notes'=>site_update_release_notes($release['body'] ?? ''),'tag'=>$tag];
    }
    throw new RuntimeException('В релизах ewc-lite нет подписанного пакета маркетплейса.');
}

function site_shop_check_update(): array
{
    $source = site_shop_github_source();
    $source['version'] = site_update_use_signed_archive($source, static function (string $archive): string {
        return site_shop_read_package($archive)['version'];
    });
    return $source;
}

function site_shop_update_from_github(array $source, ?callable $progress = null): string
{
    if (!isset($source['version'])) throw new RuntimeException('Сначала проверьте обновления маркетплейса.');
    if ($progress !== null) $progress('source');
    return site_update_use_signed_archive($source, static function (string $archive) use ($source, $progress): string {
        $package = site_shop_read_package($archive);
        if ($package['version'] !== $source['version']) throw new RuntimeException('Пакет изменился. Проверьте обновления ещё раз.');
        return site_shop_install_package($package, $progress);
    }, $progress);
}

function site_shop_install_package(array $package, ?callable $progress): string
{
    $version = $package['version'];
    $lock = admin_lock();
    try {
        $installed = site_shop_version();
        if ($installed !== '' && version_compare($version, $installed, '<=')) throw new RuntimeException('Установлена такая же или более новая версия модуля.');
        if ($progress !== null) $progress('engine-content');
        site_update_replace($package['files'], $progress);
    } finally {
        fclose($lock);
    }
    return 'Установлен маркетплейс ' . $version . '.';
}

function admin_shop_module(array $input): void
{
    $action = $input['action'] ?? '';
    if ($action === 'shop-check') {
        unset($_SESSION['shopRelease']);
        $_SESSION['shopRelease'] = site_shop_check_update();
        return;
    }
    if ($action === 'shop-package') {
        site_shop_update_from_github($_SESSION['shopRelease'] ?? []);
        return;
    }
    $lock = admin_lock();
    try {
        if ($action === 'shop-toggle') {
            $enabled = ($input['enabled'] ?? '') === '1';
            if ($enabled && site_shop_version() === '') throw new RuntimeException('Сначала установите модуль.');
            admin_write_record(SITE_SHOP_SWITCH, ['enabled'=>$enabled]);
        } elseif ($action === 'shop-remove') {
            if (site_shop_enabled() || ($input['confirm'] ?? '') !== '1') throw new RuntimeException('Сначала отключите модуль и подтвердите удаление.');
            foreach (array_reverse(array_merge(SITE_SHOP_BASE_FILES, SITE_SHOP_EDITOR_FILES)) as $name) {
                if ($name === '.htaccess') continue;
                $file = SITE_ROOT . '/shop/' . $name;
                if ((is_link($file) || is_file($file)) && !@unlink($file)) throw new RuntimeException('Не удалось удалить файл модуля.');
                if (function_exists('opcache_invalidate')) @opcache_invalidate($file, true);
            }
        }
    } finally {
        fclose($lock);
    }
}
