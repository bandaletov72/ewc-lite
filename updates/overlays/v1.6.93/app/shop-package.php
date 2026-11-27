<?php
if (!defined('SITE_ROOT')) { http_response_code(404); exit; }

const SITE_SHOP_SWITCH = SITE_ROOT . '/admin/.shop-enabled.php';
const SITE_SHOP_REPOSITORY = 'bandaletov72/ewc-lite';
const SITE_SHOP_ASSET = 'marketplace.zip';
const SITE_SHOP_BASE_FILES = ['.htaccess', 'version.php', 'module.php', 'catalog.php', 'orders.php', 'storefront.php',
    'actions.php', 'index.php', 'shop.css', 'admin.php', 'admin-view.php', 'import.php', 'language.php'];
const SITE_SHOP_EDITOR_FILES = ['admin-products.php', 'admin-products-view.php'];
const SITE_SHOP_CATEGORY_FILES = ['admin-categories.php', 'admin-categories-view.php'];
const SITE_SHOP_OPERATIONS_FILES = ['admin-orders-view.php', 'order-print.php', 'admin-sales-view.php', 'integrations.php', 'admin-integrations-view.php'];
const SITE_SHOP_RECORD_FILES = ['admin-records.php', 'admin-records-view.php'];
const SITE_SHOP_EXPORT_FILES = ['export.php', 'export-view.php'];
const SITE_SHOP_CHECKOUT_FILES = ['checkout.php', 'checkout-view.php'];
const SITE_SHOP_WIDGET_FILES = ['widgets.php'];

function site_shop_build(string $destination): void
{
    require_once SITE_ROOT . '/app/site.php';
    $zip = new ZipArchive();
    if ($zip->open($destination, ZipArchive::CREATE | ZipArchive::EXCL) !== true) throw new RuntimeException('Choose a new ZIP file.');
    $directory = site_shop_directory();
    $manifest = ['module'=>'shop', 'version'=>site_shop_version(), 'layout'=>$directory === SITE_ROOT . '/admin/shop' ? 2 : 1, 'files'=>[]];
    $widgets = is_file($directory . '/widgets.php') ? SITE_SHOP_WIDGET_FILES : [];
    foreach (array_merge(SITE_SHOP_BASE_FILES, SITE_SHOP_EDITOR_FILES, SITE_SHOP_CATEGORY_FILES, SITE_SHOP_OPERATIONS_FILES, SITE_SHOP_RECORD_FILES, SITE_SHOP_EXPORT_FILES, SITE_SHOP_CHECKOUT_FILES, $widgets) as $name) {
        $path = 'shop/' . $name;
        $bytes = file_get_contents($directory . '/' . $name);
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
        $names = SITE_SHOP_BASE_FILES;
        if (isset($digests['shop/admin-products.php'])) $names = array_merge($names, SITE_SHOP_EDITOR_FILES);
        if (isset($digests['shop/admin-categories.php'])) $names = array_merge($names, SITE_SHOP_CATEGORY_FILES);
        if (isset($digests['shop/admin-orders-view.php'])) $names = array_merge($names, SITE_SHOP_OPERATIONS_FILES);
        if (isset($digests['shop/admin-records.php'])) $names = array_merge($names, SITE_SHOP_RECORD_FILES);
        if (isset($digests['shop/export.php'])) $names = array_merge($names, SITE_SHOP_EXPORT_FILES);
        if (isset($digests['shop/checkout.php'])) $names = array_merge($names, SITE_SHOP_CHECKOUT_FILES);
        if (isset($digests['shop/widgets.php'])) $names = array_merge($names, SITE_SHOP_WIDGET_FILES);
        $paths = array_map(static function (string $file): string { return 'shop/' . $file; }, $names);
        $layout = $manifest['layout'] ?? 1;
        if (($manifest['module'] ?? '') !== 'shop' || !in_array($layout, [1,2], true)
            || !is_string($version) || !preg_match('/\A[0-9]+\.[0-9]+\.[0-9]+\z/', $version)
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
            $files[($layout === 2 ? 'admin/' : '') . $path] = $bytes;
        }
    } finally {
        $zip->close();
    }
    return ['version'=>$version,'files'=>$files,'layout'=>$layout];
}

function site_shop_github_source(): array
{
    $contents = 'https://raw.githubusercontent.com/' . SITE_SHOP_REPOSITORY . '/HEAD/updates/';
    try {
        $metadata = json_decode(site_update_download($contents . 'shop-latest.json', '*/*', 16384), true);
        $tag = $metadata['tag'] ?? '';
        $version = $metadata['version'] ?? '';
        if (!is_string($tag) || preg_match('/\Av[0-9]+\.[0-9]+\.[0-9]+\z/', $tag) !== 1
            || !is_string($version) || preg_match('/\A[0-9]+\.[0-9]+\.[0-9]+\z/', $version) !== 1) {
            throw new RuntimeException('Неверные сведения о версии маркетплейса на GitHub.');
        }
        $archive = $contents . $tag . '/' . SITE_SHOP_ASSET;
        return ['archive'=>$archive,'signature'=>$archive . '.sig','accept'=>'*/*',
            'notes'=>site_update_release_notes($metadata['notes'] ?? ''),'tag'=>$tag,'version'=>$version];
    } catch (RuntimeException $exception) {
        if ($exception->getCode() !== 404) throw $exception;
    }
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
    $version = site_update_use_signed_archive($source, static function (string $archive): string {
        return site_shop_read_package($archive)['version'];
    });
    if (isset($source['version']) && $source['version'] !== $version) {
        throw new RuntimeException('Пакет изменился. Проверьте обновления ещё раз.');
    }
    $source['version'] = $version;
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

function site_shop_upgrade_latest(?callable $progress = null): string
{
    if ($progress !== null) $progress('source');
    $source = site_shop_github_source();
    if (!isset($source['version'])) $source = site_shop_check_update();
    if (site_shop_version() !== '' && version_compare($source['version'], site_shop_version(), '<=')) {
        return 'Установлена актуальная версия.';
    }
    return site_shop_update_from_github($source, $progress);
}

function site_shop_install_package(array $package, ?callable $progress): string
{
    $version = $package['version'];
    $lock = admin_lock();
    try {
        $installed = site_shop_version();
        if ($installed !== '' && version_compare($version, $installed, '<=')) throw new RuntimeException('Установлена такая же или более новая версия модуля.');
        $files = $package['files'];
        $publicPage = SITE_ROOT . '/shop/index.php';
        if ($package['layout'] === 1 && (site_read_page('shop/index.php', SITE_ROOT) !== null || site_shop_directory() === SITE_ROOT . '/admin/shop')) {
            throw new RuntimeException('Эта версия модуля использует папку страницы /shop/. Выберите новую версию маркетплейса.');
        }
        if ($package['layout'] === 2 && site_read_page('shop/index.php', SITE_ROOT) === null) {
            if (is_link($publicPage) || (file_exists($publicPage) && ($installed === '' || site_shop_directory() !== SITE_ROOT . '/shop'))) {
                throw new RuntimeException('Не удалось создать страницу /shop/: файл уже существует.');
            }
            $files['shop/index.php'] = site_page_header('shop/index.php') . "\nTitle: Магазин\nHeading: Магазин\nMenu: Магазин\nOrder: 30\n\n[shop]\n";
        }
        if ($progress !== null) $progress('engine-content');
        site_update_replace($files, $progress);
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
            $directory = site_shop_directory();
            foreach (array_reverse(array_merge(SITE_SHOP_BASE_FILES, SITE_SHOP_EDITOR_FILES, SITE_SHOP_CATEGORY_FILES, SITE_SHOP_OPERATIONS_FILES, SITE_SHOP_RECORD_FILES, SITE_SHOP_EXPORT_FILES, SITE_SHOP_CHECKOUT_FILES, SITE_SHOP_WIDGET_FILES)) as $name) {
                if ($name === '.htaccess') continue;
                if ($name === 'index.php' && $directory === SITE_ROOT . '/shop' && site_read_page('shop/index.php', SITE_ROOT) !== null) continue;
                $file = $directory . '/' . $name;
                if ((is_link($file) || is_file($file)) && !@unlink($file)) throw new RuntimeException('Не удалось удалить файл модуля.');
                if (function_exists('opcache_invalidate')) @opcache_invalidate($file, true);
            }
        }
    } finally {
        fclose($lock);
    }
}
