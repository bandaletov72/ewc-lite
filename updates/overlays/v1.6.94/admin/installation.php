<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}

function admin_installation_revision(): string
{
    return admin_settings_revision([site_settings(), site_theme_state(), site_brand_settings()]);
}

function admin_finish_installation(array $auth, array $draft, array $logoFile, string $revision, bool $skip)
{
    $logo = '';
    $favicon = '';
    if (!$skip) {
        $settings = admin_site_text_settings(array_intersect_key($draft, ['name' => true, 'description' => true, 'footer' => true]));
        if (!in_array($draft['mode'], ['auto', 'light', 'dark'], true)) {
            throw new RuntimeException('Выберите режим оформления.');
        }
        $original = admin_brand_upload($logoFile);
        if ($original !== '') {
            if (!function_exists('imagecreatefromstring') || !function_exists('imagepng')) {
                throw new RuntimeException('Для загрузки логотипа требуется расширение PHP GD.');
            }
            $logo = admin_brand_png($original, false);
            $favicon = admin_brand_png($original, true);
        }
    }

    $lock = admin_lock();
    try {
        $current = admin_auth_state();
        if (($current['setupPending'] ?? false) !== true || !hash_equals(admin_auth_key($current), admin_auth_key($auth))) {
            throw new RuntimeException('Первичная настройка уже завершена. Обновите страницу.');
        }
        if (!$skip) {
            if (!hash_equals(admin_installation_revision(), $revision)) {
                throw new RuntimeException('Настройки изменились. Откройте мастер заново.');
            }
            $stored = site_read_record(site_content_record(SITE_SETTINGS_FILE));
            site_read_record(site_theme_storage_file());
            $currentTheme = site_theme_state();
            $theme = $currentTheme;
            if ($draft['preset'] !== '') {
                if (!isset($theme['presets'][$draft['preset']])) {
                    throw new RuntimeException('Выберите палитру из списка.');
                }
                $theme['theme'] = $theme['presets'][$draft['preset']]['theme'];
            }
            $theme['theme']['mode'] = $draft['mode'];
            if ($logo !== '') {
                $brand = site_read_record(SITE_BRAND_FILE);
                $filename = $logoFile['name'];
                $brand['logo'] = admin_brand_write('logo', $logo, $filename);
                $brand['favicon'] = admin_brand_write('favicon', $favicon, $filename);
            }

            admin_write_record(site_content_record(SITE_SETTINGS_FILE), array_replace($stored, $settings));
            if ($theme !== $currentTheme) {
                admin_write_theme_state($theme);
            }
            if ($logo !== '') {
                admin_write_record(SITE_BRAND_FILE, $brand);
            }
        }
        unset($current['setupPending'], $current['chatgptSetupPending']);
        admin_write_record(ADMIN_ACCESS_FILE, $current);
    } finally {
        fclose($lock);
    }
}
