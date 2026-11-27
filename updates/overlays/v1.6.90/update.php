<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
define('SITE_ROOT', __DIR__);
require __DIR__ . '/app/updater.php';

try {
    $action = $argv[1] ?? '';
    if ($action === '--github') {
        echo site_update_from_github(), PHP_EOL;
    } elseif ($action === '--scheduled') {
        require __DIR__ . '/app/site.php';
        require __DIR__ . '/admin/files.php';
        require __DIR__ . '/admin/maintenance-config.php';
        require __DIR__ . '/admin/content-backup.php';
        require __DIR__ . '/admin/config.php';
        require __DIR__ . '/admin/form-config.php';
        require __DIR__ . '/admin/design-themes.php';
        require __DIR__ . '/admin/update-overview.php';
        $timezone = site_region_settings()['timezone'];
        if ($timezone !== 'auto') date_default_timezone_set($timezone);
        require __DIR__ . '/admin/maintenance-jobs.php';
        foreach (admin_run_scheduled_jobs() as $message) {
            echo $message, PHP_EOL;
        }
    } elseif ($action === '--build-shop' && isset($argv[2]) && class_exists('ZipArchive')) {
        require_once __DIR__ . '/app/site.php';
        require __DIR__ . '/app/shop-package.php';
        site_shop_build($argv[2]);
        echo 'ZIP модуля создан.', PHP_EOL;
    } elseif ($action === '--build' && isset($argv[2]) && class_exists('ZipArchive')) {
        site_update_build($argv[2]);
        echo 'ZIP обновления создан.', PHP_EOL;
    } elseif ($action !== '' && substr($action, -4) === '.zip') {
        echo site_update_apply($action), PHP_EOL;
    } else {
        throw new RuntimeException('Использование: php update.php архив.zip | --github | --scheduled | --build архив.zip');
    }
} catch (RuntimeException $exception) {
    fwrite(STDERR, $exception->getMessage() . PHP_EOL);
    exit(1);
}
