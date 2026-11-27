<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}

function admin_rebuild_search(?callable $progress = null, bool $includeMarketplace = false): int
{
    $index = site_build_search_index($progress);
    if ($progress !== null) $progress('index-save');
    $json = json_encode($index, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($json === false) {
        throw new RuntimeException('Не удалось подготовить индекс поиска.');
    }
    admin_write_payload(site_search_file(), $json, SITE_SEARCH_LIMIT);
    if ($includeMarketplace && site_shop_version() !== '' && is_file(site_shop_directory() . '/widgets.php')) {
        require_once site_shop_directory() . '/module.php';
        $revision = shop_generation();
        if ($revision !== '') {
            if ($progress !== null) $progress('index-shop');
            $tables = shop_tables();
            $catalog = shop_catalog_build($tables);
            if ($catalog !== site_read_record(SHOP_DIR . '/index-' . $revision . '.php', SHOP_LIMIT)) shop_publish($tables, $catalog);
        }
    }
    return count($index['pages']);
}

function admin_refresh_index_after_edit(string $action)
{
    if (!in_array($action, ['save', 'save-page-custom-definitions', 'page-menu', 'page-order', 'page-move', 'folder-move', 'change-page-status',
        'live-component', 'live-page-asset', 'live-page-menu', 'save-iblock', 'save-iblock-text',
        'upload-media', 'delete-media', 'order-media', 'save-settings', 'save-region', 'save-contacts',
        'save-snippet', 'delete-snippet', 'restore-content-backup'], true)) {
        return;
    }
    register_shutdown_function(static function () {
        if (http_response_code() >= 400 || !empty($GLOBALS['error'])) {
            return;
        }
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_write_close();
        }
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
        try {
            $lock = admin_lock();
            try {
                admin_rebuild_search();
            } finally {
                fclose($lock);
            }
        } catch (Throwable $exception) {
            error_log('Search index: ' . $exception->getMessage());
        }
    });
}

function admin_maintenance(string $action, array $submitted, ?callable $progress = null): string
{
    $lock = admin_lock();
    try {
        if ($action === 'index-pages') {
            return admin_t('Страниц в индексе') . ' — ' . admin_rebuild_search($progress, true);
        }
        $settings = site_performance_settings();
        if ($action === 'save-cache') {
            $settings['cache'] = admin_input($submitted, 'cache') === '1';
        }
        $settings['generation'] = bin2hex(random_bytes(12));
        admin_write_record(SITE_PERFORMANCE_FILE, $settings);
        foreach (glob(SITE_ROOT . '/data/.page-cache-*.php') ?: [] as $file) {
            if (preg_match('/\A\.page-cache-[a-f0-9]{64}\.php\z/', basename($file)) && !is_link($file)) {
                @unlink($file);
            }
        }
        return $action === 'clear-cache' ? admin_l('Кеш очищен.') : admin_l('Настройки сохранены.');
    } finally {
        fclose($lock);
    }
}
