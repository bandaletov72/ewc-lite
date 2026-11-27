<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}

function site_extensions(): array
{
    static $loaded;
    if ($loaded !== null) {
        return $loaded;
    }
    $extensions = [];
    foreach ([SITE_ROOT . '/app/extensions', SITE_ROOT . '/extensions', SITE_PHP_EDITOR_DIR] as $folder) {
        if (is_link($folder)) {
            continue;
        }
        foreach (glob($folder . '/*.php') ?: [] as $module) {
            if (is_link($module)) {
                continue;
            }
            try {
                $declared = require $module;
            } catch (Throwable $exception) {
                error_log('Site module declarations: ' . basename($module));
                continue;
            }
            if (!is_array($declared)) {
                continue;
            }
            foreach ($declared as $name => $definition) {
                if (!is_string($name) || !site_valid_snippet($name)
                    || isset(site_contact_tokens()[$name]) || isset(site_layout_blocks()[$name]) || site_valid_form_id($name)
                    || in_array($name, ['gal', 'fotos', 'files', 'banners', 'include', 'iblock', 'callback', 'map', 'php', 'button', 'reviews'], true)
                    || !is_array($definition) || !in_array($definition['kind'] ?? '', ['single', 'wrapped'], true)
                    || !is_callable($definition['render'] ?? null)) {
                    continue;
                }
                if ($folder === SITE_PHP_EDITOR_DIR && isset($extensions[$name]['label'])) {
                    $definition['label'] = $extensions[$name]['label'];
                }
                $extensions[$name] = $definition;
            }
        }
    }
    $loaded = $extensions;
    return $loaded;
}

function site_render_extension(string $name, string $argument, array $context): string
{
    static $settings;
    if ($settings === null) {
        $settings = site_component_settings();
    }
    try {
        return (string) $context['extensions'][$name]['render']($argument,
            ['base' => $context['base'], 'page' => $context['page'], 'settings' => $settings[$name] ?? []]);
    } catch (Throwable $exception) {
        error_log('Site module execution: ' . $name);
        return '';
    }
}

function site_component_asset_extensions(string $type): array
{
    return $type === 'files' ? ['zip', '7z', 'pdf', 'xls', 'xlsx', 'doc', 'docx', 'png', 'gif', 'jpg', 'jpeg', 'webp', 'mp4', 'mov', 'mp3']
        : ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif'];
}

function site_asset_path(string $argument, string $asset, bool $forWrite = false, bool $themeFirst = true): string
{
    if (!in_array($asset, ['images', 'files'], true)) return '';
    $argument = rtrim(trim($argument), '/');
    $theme = site_design_active();
    $prefix = $theme === '' ? '' : '/templates/themes/' . $theme . '/';
    $explicit = $prefix !== '' && strpos($argument, $prefix) === 0;
    if ($explicit) $argument = '/' . substr($argument, strlen($prefix));
    if (!preg_match('~\A/((?:templates/)?' . $asset . ')(?:/(.+))?\z~s', $argument, $match)) {
        return '';
    }
    $path = $match[1] . (isset($match[2]) ? '/' . $match[2] : '');
    foreach (explode('/', $path) as $part) {
        if ($part === '' || substr($part, -1) === '.' || substr($part, -1) === ' '
            || preg_match('~[\x00-\x1F\x7F\\\\:<>"|?*]~', $part)) {
            return '';
        }
    }
    if ($theme !== '' && $themeFirst) {
        $relative = preg_replace('~\Atemplates/~', '', $path);
        $themedPath = ltrim($prefix, '/') . $relative;
        $directory = SITE_ROOT . '/' . $themedPath;
        if ($forWrite || $explicit
            || (is_dir($directory) && realpath($directory) === str_replace('/', DIRECTORY_SEPARATOR, $directory))) {
            return $themedPath;
        }
    }

    if ($match[1] === $asset && !is_dir(SITE_ROOT . '/' . $path) && is_dir(SITE_ROOT . '/templates/' . $path)) {
        return 'templates/' . $path;
    }
    return $path;
}

function site_asset_files(string $argument, string $base, string $asset, array $extensions, bool $includeSize = false): array
{
    $path = site_asset_path($argument, $asset);
    if ($path === '') {
        return [];
    }
    $folder = SITE_ROOT . '/' . $path;
    $resolved = realpath($folder);
    if ($resolved !== str_replace('/', DIRECTORY_SEPARATOR, $folder) || !is_dir($folder)) {
        return [];
    }

    $files = @scandir($folder, SCANDIR_SORT_NONE);
    if ($files === false) {
        return [];
    }
    $assets = [];
    $url = site_url($base, implode('/', array_map('rawurlencode', explode('/', $path)))) . '/';
    foreach ($files as $file) {
        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (!in_array($extension, $extensions, true)
            || !is_file($folder . '/' . $file) || is_link($folder . '/' . $file)) {
            continue;
        }
        $assets[$file] = [
            'url' => $url . rawurlencode($file), 'name' => $file,
            'alt' => pathinfo($file, PATHINFO_FILENAME), 'extension' => $extension,
        ];
        if ($includeSize) {
            $assets[$file]['size'] = @filesize($folder . '/' . $file);
        }
    }
    uksort($assets, 'strnatcasecmp');
    try {
        $order = site_read_record($folder . '/.asset-order.php', 131072);
    } catch (RuntimeException $exception) {
        $order = [];
    }
    $sorted = [];
    foreach ($order as $file) {
        if (is_string($file) && isset($assets[$file])) {
            $sorted[$file] = $assets[$file];
        }
    }
    return array_values($sorted + $assets);
}

function site_component_gallery(string $argument, string $base, bool $carousel, string $overrides = ''): string
{
    $images = site_asset_files($argument, $base, 'images', site_component_asset_extensions('gal'));
    if (!$images) {
        return '';
    }
    $options = site_component_options($carousel ? 'gal' : 'fotos', $overrides);
    ob_start();
    require site_template_file('components/gallery.php');
    return (string) ob_get_clean();
}

function site_file_size(int $bytes): string
{
    $value = $bytes;
    $unit = 0;
    $units = ['Б', 'КБ', 'МБ', 'ГБ'];
    while ($value >= 1024 && $unit < count($units) - 1) {
        $value /= 1024;
        $unit++;
    }
    return number_format($value, $unit === 0 || floor($value) == $value ? 0 : 1, ',', ' ') . ' ' . $units[$unit];
}

function site_component_files(string $argument, string $base, string $overrides = ''): string
{
    $options = site_component_options('files', $overrides);
    $files = site_asset_files($argument, $base, 'files', site_component_asset_extensions('files'), $options['size'] === 'on');
    if (!$files) {
        return '';
    }
    if ($options['order'] === 'desc') {
        $files = array_reverse($files);
    }
    ob_start();
    require site_template_file('components/files.php');
    return (string) ob_get_clean();
}

function site_component_include(string $argument, array $snippets, array &$context): string
{
    if (count($context['active']) - 1 >= SITE_MAX_INCLUDE_DEPTH) {
        return '';
    }
    $argument = trim($argument);
    if (substr($argument, 0, 1) !== '/') {
        return '';
    }

    $path = substr($argument, 1);
    if (substr($path, -4) !== '.php') {
        $path = $path === '' ? 'index.php' : rtrim($path, '/') . '/index.php';
    }
    list($basePath, $explicit) = site_page_translation($path);
    $language = $explicit !== '' ? $explicit : ($context['locale'] ?? 'ru');
    if (isset($context['active'][$basePath]) || !in_array($language, site_region_settings()['languages'], true)) {
        return '';
    }
    $page = site_localized_page($basePath, $language);
    if ($page === null) {
        return '';
    }
    $context['active'][$basePath] = true;
    $previousFieldPage = $context['customFieldPage'] ?? null;
    $context['customFieldPage'] = $page;
    try {
        return site_expand_content($page['html'], $snippets, $context);
    } finally {
        if ($previousFieldPage === null) unset($context['customFieldPage']);
        else $context['customFieldPage'] = $previousFieldPage;
        unset($context['active'][$basePath]);
    }
}

function site_component_form(string $id, array &$context): string
{
    if ($context['forms'] === null) {
        try {
            $context['forms'] = site_forms();
        } catch (RuntimeException $exception) {
            $context['forms'] = [];
            return '<p>Форма временно недоступна.</p>';
        }
    }
    if (!isset($context['forms'][$id])) {
        return '[' . $id . ']';
    }
    $form = $context['forms'][$id];
    $session = $context['formSession'];
    $base = $context['base'];
    $page = $context['page'];
    $instance = 'form-' . bin2hex(random_bytes(6));
    ob_start();
    require site_template_file('components/form.php');
    return (string) ob_get_clean();
}

function site_shortcode_options(string $argument): array
{
    $options = [];
    preg_match_all('~(?:\A|[\x20\t])([a-z][a-z0-9_-]*)=(?:"([^"]*)"|([^\s"]+))(?=\s|\z)~i', trim($argument), $pairs, PREG_SET_ORDER);
    foreach ($pairs as $pair) {
        $value = html_entity_decode($pair[3] ?? $pair[2], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        if (strlen($value) <= 500 && preg_match('/[\x00-\x1f\x7f]/', $value) !== 1) {
            $options[strtolower($pair[1])] = trim($value);
        }
    }
    return $options;
}

function site_component_action_button(string $arguments, array &$context): string
{
    return site_render_action_button(site_shortcode_options($arguments), $context);
}

function site_render_action_button(array $options, array &$context): string
{
    $label = $options['label'] ?? '';
    $action = $options['action'] ?? '';
    if ($label === '' || strlen($label) > 360) return '';
    $body = '';
    $target = '';
    $locale = $context['locale'] ?? 'ru';
    if ($action === 'url') {
        $target = site_banner_url($context['base'], site_map_url($options['url'] ?? ''));
    } elseif ($action === 'form' && site_valid_form_id($options['form'] ?? '')) {
        $body = site_component_form($options['form'], $context);
        if (strpos($body, '<form') === false) return '';
    } elseif ($action === 'callback') {
        $body = site_component_callback($label, $context, '', true);
        if (strpos($body, '<form') === false) return '';
    } elseif ($action === 'payment') {
        $amount = str_replace(',', '.', $options['amount'] ?? '');
        $currency = $options['currency'] ?? site_region_settings()['currency'];
        if (!preg_match('/\A[0-9]{1,12}(?:\.[0-9]{1,2})?\z/', $amount) || (float) $amount <= 0
            || !isset(site_region_options()['currency'][$currency])) return '';
        $methods = [];
        $wallet = '';
        foreach (site_payment_methods() as $key => list($name, $hosts)) {
            $url = $options[$key] ?? '';
            if ($key === 'yoomoney' && $currency === 'RUB' && preg_match('/\A[0-9]{10,20}\z/', $url)) {
                $wallet = $url;
            } elseif ($url !== '' && site_valid_payment_url($url, $hosts)) {
                $methods[$name] = $url;
            }
        }
        if (!$methods && $wallet === '') return '';
        ob_start();
        require site_template_file('components/payment-options.php');
        $body = (string) ob_get_clean();
    }
    if ($target !== '') return '<a class="button button-primary" href="' . site_escape($target) . '">' . site_escape($label) . '</a>';
    if ($body === '') return '';
    $dialogId = 'action-' . bin2hex(random_bytes(6));
    ob_start();
    require site_template_file('components/action-button.php');
    return (string) ob_get_clean();
}

function site_component_callback(string $label, array $context, string $arguments = '', bool $bare = false): string
{
    try {
        $settings = site_callback_settings();
    } catch (RuntimeException $exception) {
        return '<p>Форма обратного звонка временно недоступна.</p>';
    }
    $params = site_shortcode_options($arguments);
    foreach (['button', 'submit', 'title', 'placeholder'] as $field) {
        if (($params[$field] ?? '') !== '') {
            $settings[$field] = $params[$field];
        }
    }
    $label = $label !== '' ? $label : $settings['button'];
    $form = site_callback_form($settings);
    $id = 'callback';
    $base = $context['base'];
    $page = $context['page'];
    $session = $context['formSession'];
    $instance = 'callback-' . bin2hex(random_bytes(6));
    ob_start();
    require site_template_file($bare ? 'components/form.php' : 'components/callback.php');
    return (string) ob_get_clean();
}
