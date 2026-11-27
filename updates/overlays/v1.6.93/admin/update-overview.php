<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}

function admin_update_overview(string $repository, string $selected = ''): array
{
    $remote = [];
    if ($repository !== '') {
        try {
            $remote = site_update_github_metadata($repository);
        } catch (RuntimeException $exception) {
            // Older repositories can publish Releases without the metadata file.
        }
        try {
            if (!site_spam_valid_version($remote['version'] ?? null)) {
                $remote = site_update_github_source($repository) + $remote;
            }
        } catch (RuntimeException $exception) {
            $cached = admin_cached_update($repository);
            $remote['version'] = $cached['version'] ?? '';
            $remote['notes'] = $cached['notes'] ?? '';
            $remote['cached'] = !empty($cached);
        }
        if (site_spam_valid_version($remote['version'] ?? null)) {
            try {
                admin_remember_update($repository, ['version' => $remote['version'],
                    'notes' => site_update_release_notes($remote['notes'] ?? '')]);
            } catch (RuntimeException $exception) {
                // The live result can still be shown on a read-only host.
            }
        }
    }
    $active = site_design_active();
    $result = [
        'engine' => admin_update_item(site_update_version(), $remote['version'] ?? '', $remote['notes'] ?? ''),
        'theme' => admin_update_theme_item($active, admin_update_theme_remote($active, $remote)),
        'spam' => admin_update_item(site_spam_version(), $remote['spamVersion'] ?? '', $remote['spamNotes'] ?? ''),
    ];
    $result['engine']['cached'] = !empty($remote['cached']);
    $shop = [];
    require_once SITE_ROOT . '/app/shop-package.php';
    try {
        $shop = site_shop_github_source();
        if (!site_spam_valid_version($shop['version'] ?? '')) $shop = site_shop_check_update();
    } catch (RuntimeException $exception) {
        // Marketplace availability is independent of the engine.
    }
    $result['shop'] = admin_update_item(site_shop_version(), $shop['version'] ?? '', $shop['notes'] ?? '');
    $result['count'] = count(array_filter($result, static function (array $item, string $component): bool {
        return $item['outdated'] && ($component !== 'shop' || $item['installed'] !== '');
    }, ARRAY_FILTER_USE_BOTH));
    $selected = admin_update_theme_selection($selected, $active);
    if ($selected !== $active) {
        $result['theme'] = admin_update_theme_item($selected, admin_update_theme_remote($selected, $remote));
    }
    return $result;
}

function admin_update_theme_selection(string $selected, string $active): string
{
    if ($selected === 'system') return '';
    return site_design_directory($selected) !== '' ? $selected : $active;
}

function admin_update_theme_remote(string $theme, array $remote): array
{
    if ($theme === '') return ['version' => $remote['version'] ?? ''];
    $info = $remote['themes'][$theme] ?? [];
    return is_array($info) ? $info : [];
}

function admin_update_theme_item(string $theme, $remote): array
{
    $versions = admin_design_versions($theme);
    $item = admin_update_item($versions['installed'], $remote['version'] ?? '', $remote['notes'] ?? '');
    $design = admin_update_item($versions['installed'], $versions['available'], '');
    $pages = $theme === 'dezinfektori'
        && (admin_update_item($versions['pages'], $versions['availablePages'], '')['outdated']
            || admin_update_item($versions['pages'], $remote['pagesVersion'] ?? '', '')['outdated']);
    $item['outdated'] = $theme !== '' && ($item['outdated'] || $design['outdated'] || $pages);
    $item['requiresEngine'] = admin_update_item($versions['available'], $remote['version'] ?? '', '')['outdated']
        || ($theme === 'dezinfektori' && admin_update_item($versions['availablePages'], $remote['pagesVersion'] ?? '', '')['outdated']);
    return $item;
}

function admin_update_item(string $installed, $available, $notes): array
{
    $version = site_spam_valid_version($available) ? $available : '';
    return ['installed' => $installed, 'version' => $version, 'notes' => site_update_release_notes($notes),
        'outdated' => $version !== '' && ($installed === '' || version_compare($version, $installed, '>'))];
}
