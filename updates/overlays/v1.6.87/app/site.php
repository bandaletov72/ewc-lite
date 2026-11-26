<?php

if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}

if (is_file(__DIR__ . '/.update-in-progress')) {
    http_response_code(503);
    header('Retry-After: 10');
    header('Content-Type: text/plain; charset=UTF-8');
    exit('Обновляется движок сайта. Откройте страницу через несколько секунд.');
}

require __DIR__ . '/components.php';
require __DIR__ . '/banners.php';
require __DIR__ . '/forms.php';
require __DIR__ . '/form-mail.php';
require __DIR__ . '/user-mail.php';
require __DIR__ . '/regional.php';
require __DIR__ . '/maps.php';
require __DIR__ . '/integrations.php';
require __DIR__ . '/php-modules.php';
require __DIR__ . '/captcha.php';
require __DIR__ . '/performance.php';
require __DIR__ . '/page-editor.php';
require __DIR__ . '/data-editor.php';
require __DIR__ . '/privacy.php';
require __DIR__ . '/layout-placement.php';
require __DIR__ . '/content-urls.php';
require __DIR__ . '/error-pages.php';
require __DIR__ . '/functions.php';
require __DIR__ . '/libraries.php';
require __DIR__ . '/page-custom-fields.php';
require __DIR__ . '/fonts.php';
require __DIR__ . '/copy-code.php';
require __DIR__ . '/spam.php';
require __DIR__ . '/iblocks.php';
require __DIR__ . '/appearance.php';
require __DIR__ . '/component-settings.php';
require __DIR__ . '/callbacks.php';
require __DIR__ . '/reviews.php';
require __DIR__ . '/sales.php';
require __DIR__ . '/cities.php';
require __DIR__ . '/theme-design.php';

const SITE_NAME = 'Мой сайт';
const SITE_PRODUCT_NAME = 'Eurowebcart CMS Lite';
const SITE_PRODUCT_AUTHOR = 'Бандалетов Павел';
const SITE_PRODUCT_REPOSITORY = 'https://github.com/bandaletov72/ewc-lite';
const SITE_PAGE_LIMIT = 524288;
const SITE_IDENTIFIER_PATTERN = '[A-Za-z0-9_\-]{1,64}';
const SITE_ASSET_LIMIT = 65536;
const SITE_ASSET_RECORD_LIMIT = 180000;
const SITE_PAGE_RECORD_LIMIT = SITE_PAGE_LIMIT + SITE_ASSET_RECORD_LIMIT + 2 * 87384; // Includes the announcement and custom fields.
const SITE_ASSETS_FILE = SITE_ROOT . '/admin/.site-assets.php';
const SITE_HEADERS_FILE = SITE_ROOT . '/admin/.site-headers.php';
const SITE_HEADER_INSERT_LIMIT = 32768;
const SITE_HEADERS_RECORD_LIMIT = 100000;
const SITE_MAX_INCLUDE_DEPTH = 10;
const SITE_RECORD_HEADER = "<?php http_response_code(404); exit; __halt_compiler();\n";
const SITE_SETTINGS_FILE = SITE_ROOT . '/admin/.site-settings.php';
const SITE_DOMAINS_FILE = SITE_ROOT . '/admin/.domains.php';
const SITE_SNIPPET_RECORD_LIMIT = 4194304;
const SITE_SCROLL_ANIMATIONS = ['off', 'fade', 'slide', 'scale'];

$siteReporting = site_error_reporting_levels()[site_settings()['errorReporting']];
if ($siteReporting !== null) {
    error_reporting($siteReporting);
}
unset($siteReporting);

/** @return string|null */
function site_read_payload(string $file, int $limit)
{
    if (is_link($file)) {
        throw new RuntimeException('Недопустимый путь к файлу настроек.');
    }
    if (!file_exists($file)) {
        return null;
    }

    $bytes = @file_get_contents($file, false, null, 0, $limit + 1);
    if ($bytes === false || strlen($bytes) > $limit || strpos($bytes, SITE_RECORD_HEADER) !== 0) {
        throw new RuntimeException('Не удалось прочитать файл настроек.');
    }

    return substr($bytes, strlen(SITE_RECORD_HEADER));
}

function site_read_record(string $file, int $limit = 32768): array
{
    $payload = site_read_payload($file, $limit);
    if ($payload === null) {
        return [];
    }
    $record = json_decode($payload, true);
    if (!is_array($record)) {
        throw new RuntimeException('Неверный формат файла настроек.');
    }

    return $record;
}

function site_shop_version(): string
{
    $file = SITE_ROOT . '/shop/version.php';
    return is_file($file) && !is_link($file) ? (string) require $file : '';
}

function site_shop_enabled(): bool
{
    static $enabled;
    if ($enabled !== null) return $enabled;
    try {
        $enabled = !empty(site_read_record(SITE_ROOT . '/admin/.shop-enabled.php')['enabled'])
            && is_file(SITE_ROOT . '/shop/module.php') && is_file(SITE_ROOT . '/shop/index.php') && site_shop_version() !== '';
    } catch (RuntimeException $error) {
        $enabled = false;
    }
    return $enabled;
}

function site_shop_tags(): array
{
    return ['categories','items','basket','search','filters','sort','pages','product','breadcrumbs','cart','checkout','account','orders'];
}

function site_default_settings(): array
{
    return ['name' => SITE_NAME, 'subtitle' => '', 'description' => '', 'footer' => '', 'variables' => [], 'breadcrumbs' => true,
        'searchMode' => 'expanded', 'publicUrl' => '', 'errorReporting' => 'server', 'scrollAnimation' => 'off',
        'messageRecipients' => ''];
}

function site_scroll_animation_labels(string $language): array
{
    static $labels;
    if ($labels === null) $labels = require __DIR__ . '/help-scroll-animation.php';
    return $labels[$language] ?? $labels['ru'];
}

function site_error_reporting_levels(): array
{
    $fatal = E_ERROR | E_PARSE | E_CORE_ERROR | E_COMPILE_ERROR | E_USER_ERROR | E_RECOVERABLE_ERROR;
    $warnings = E_WARNING | E_CORE_WARNING | E_COMPILE_WARNING | E_USER_WARNING;
    return ['server' => null, 'all' => E_ALL, 'warnings' => $fatal | $warnings, 'fatal' => $fatal, 'off' => 0];
}

function site_php_admin_labels(string $language): array
{
    static $labels;
    if ($labels === null) $labels = require __DIR__ . '/help-php-settings.php';
    return $labels[$language] ?? $labels['ru'];
}

function site_footer_pattern(string $text): string
{
    if (strpos($text, '[year]') !== false) {
        return $text;
    }
    return preg_replace('/\A(©\s*(?:[0-9]{4}\s*[-–—]\s*)?)[0-9]{4}(?=\s|$)/u', '$1[year]', $text) ?? $text;
}

function site_settings(): array
{
    $settings = site_default_settings();
    try {
        $stored = site_read_record(site_content_record(SITE_SETTINGS_FILE));
    } catch (RuntimeException $exception) {
        return $settings;
    }

    foreach ($settings as $key => $value) {
        if (isset($stored[$key]) && gettype($stored[$key]) === gettype($value)) {
            $settings[$key] = $stored[$key];
        }
    }

    $settings['footer'] = site_footer_pattern($settings['footer']);
    if (!in_array($settings['searchMode'], ['off', 'compact', 'expanded'], true)) {
        $settings['searchMode'] = 'expanded';
    }
    if (!in_array($settings['scrollAnimation'], SITE_SCROLL_ANIMATIONS, true)) {
        $settings['scrollAnimation'] = 'off';
    }
    if (!array_key_exists($settings['errorReporting'], site_error_reporting_levels())) {
        $settings['errorReporting'] = 'server';
    }
    return $settings;
}

function site_valid_snippet(string $name): bool
{
    return preg_match('/\A[a-z][a-z0-9_-]{0,63}\z/', $name) === 1;
}

function site_normalize_domain(string $domain): string
{
    $domain = strtolower($domain);
    if (substr($domain, -1) === '.') {
        $domain = substr($domain, 0, -1);
    }
    if (strlen($domain) > 253) {
        return '';
    }
    foreach (explode('.', $domain) as $label) {
        if (!preg_match('/\A[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\z/', $label)) {
            return '';
        }
    }
    return $domain;
}

function site_host(): string
{
    $header = $_SERVER['HTTP_HOST'] ?? '';
    if (!preg_match('/\A(\[[a-f0-9:.]+\]|[a-z0-9.-]+)(?::[0-9]{1,5})?\z/i', $header, $match)) {
        return '';
    }
    $host = strtolower($match[1]);
    if ($host[0] === '[') {
        return filter_var(substr($host, 1, -1), FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) ? $host : '';
    }
    return site_normalize_domain($host);
}

function site_snippet_file(string $host = ''): string
{
    $suffix = $host === '' ? '' : '-' . hash('sha256', $host);
    return site_content_record(SITE_ROOT . '/admin/.snippets' . $suffix . '.php');
}

function site_content_host(string $base, bool $remember = false): string
{
    $host = site_host();
    try {
        $domains = site_read_record(SITE_DOMAINS_FILE);
    } catch (RuntimeException $exception) {
        return $host;
    }
    if ($host === '' || in_array($host, $domains, true)) {
        return $host;
    }
    $root = strpos($host, 'www.') === 0 ? substr($host, 4) : $host;
    $suffix = '.' . $root;
    $valid = static function (string $source) use ($domains, $suffix): bool {
        return substr($source, -strlen($suffix)) === $suffix && in_array($source, $domains, true);
    };
    $referer = $_SERVER['HTTP_REFERER'] ?? '';
    $source = strlen($referer) < 4096 ? parse_url($referer) : false;
    if (is_array($source) && in_array($source['scheme'] ?? '', ['http', 'https'], true)) {
        $name = site_normalize_domain($source['host'] ?? '');
        if ($valid($name)) {
            if ($remember) {
                setcookie('FILE_SITE_REGION_HOST', $name, time() + 86400, site_url($base), '',
                    !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off', true);
            }
            return $name;
        }
    }
    $saved = $_COOKIE['FILE_SITE_REGION_HOST'] ?? '';
    return is_string($saved) && $valid($saved) ? $saved : $host;
}

function site_read_snippets(string $host = ''): array
{
    return site_read_record(site_snippet_file($host), SITE_SNIPPET_RECORD_LIMIT);
}

function site_snippet_values(string $host, ?array &$stock = null, ?array &$php = null, ?array &$visibility = null): array
{
    $shared = [];
    $themed = site_design_active() !== '';
    $layoutKeys = [];
    if ($themed) {
        $layoutKeys = site_layout_blocks() + site_system_layout_definitions() + ['_layoutVisibility' => true, '_layoutHistory' => true];
    }
    try {
        $shared = site_read_snippets();
        if ($themed) $shared = array_diff_key($shared, $layoutKeys) + site_read_layout_snippets();
    } catch (RuntimeException $exception) {
        // Pages remain available if the shared snippets cannot be read.
    }
    $visibility = is_array($shared['_layoutVisibility'] ?? null) ? $shared['_layoutVisibility'] : [];
    if ($host !== '') {
        try {
            $own = site_read_snippets($host);
            if ($themed) $own = array_diff_key($own, $layoutKeys) + site_read_layout_snippets($host);
            $shared = array_replace($shared, $own);
            if (is_array($own['_layoutVisibility'] ?? null)) {
                $visibility = array_replace($visibility, $own['_layoutVisibility']);
            }
        } catch (RuntimeException $exception) {
            // Shared values still apply if one domain's data cannot be read.
        }
    }

    $values = [];
    $layoutNames = site_layout_blocks();
    foreach ($shared as $name => $snippet) {
        if (isset($layoutNames[$name])) {
            if (($snippet['mode'] ?? '') === 'stock') {
                $stock[$name] = true;
                continue;
            }
            if (($snippet['mode'] ?? '') === 'php' && is_string($snippet['value'] ?? null)) {
                $php[$name] = $snippet['value'];
                continue;
            }
        }
        if (is_array($snippet) && isset($snippet['value']) && is_string($snippet['value'])) {
            $values[$name] = $snippet['value'];
        }
    }
    return $values;
}

const SITE_SNIPPET_TOKEN = '\[(?P<snippet>u-[a-z0-9_-]{1,64}|[a-z][a-z0-9_-]{0,63})\]';

function site_date_snippets(): array
{
    $time = $_SERVER['REQUEST_TIME'] ?? time();
    $regional = site_region_settings();
    $date = date($regional['date'], $time);
    return ['year' => date('Y', $time), 'month' => date('m', $time), 'day' => date('d', $time),
        'time' => date($regional['time'], $time), 'dmy' => date('d.m.Y', $time), 'date' => $date,
        'datetime' => date($regional['date'] . ' ' . $regional['time'], $time)];
}

function site_expand_text(string $text, array $snippets): string
{
    return preg_replace_callback('~' . SITE_SNIPPET_TOKEN . '~i', static function (array $match) use ($snippets): string {
        $name = strtolower($match[1]);
        if (!isset($snippets[$name])) {
            return strpos($name, 'u-') === 0 ? '' : $match[0];
        }
        return html_entity_decode(strip_tags($snippets[$name]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }, $text) ?? $text;
}

function site_content_pattern(array $extensions): string
{
    // Keep tags, attributes, and literal HTML regions intact.
    $pattern = <<<'REGEX'
~
  <!-- .*? (?: --> | $ )
  | <(script|style|textarea|pre|code)\b (?: [^>"']++ | "[^"]*" | '[^']*' )* >
    .*? (?: </\1\s*> | $ )
  | </?[a-z][a-z0-9:-]*\b (?: [^>"']++ | "[^"]*" | '[^']*' )* >
  | \[ (?P<galleryTag>gal|fotos) (?P<galleryOptions>(?:[\x20\t]+[a-z0-9_-]+=[a-z0-9_-]+%?)*) \]
    (?P<gallery>[^\[\]]+) \[/\k<galleryTag>\]
  | \[(?P<banners>banners)(?P<bannerOptions>(?:[\x20\t]+[a-z0-9_-]+=[a-z0-9_-]+%?)*)\]
    (?:(?P<bannerFolder>\s*/[^\[\]]+)\[/banners\])?
  | \[files (?P<fileOptions>(?:[\x20\t]+[a-z0-9_-]+=[a-z0-9_-]+%?)*) \] (?P<files>[^\[\]]+) \[/files\]
  | \[include(?P<includeOptions>__SIZES__)\] (?P<include>[^\[\]]+) \[/include\]
  | \[dir\] (?P<directoryList>[^\[\]]+) \[/dir\]
  | \[iblock=(?P<iblockTemplate>[a-z][a-z0-9_-]{0,63})(?P<iblockOptions>(?:[\x20\t]+[a-z][a-z0-9_-]*=(?:[a-z0-9_,\-]+%?|"[^"\[\]\r\n]*"))*)\]
    (?P<iblock>[^\[\]]+) \[/?iblock\]
  | \[ (?P<callback>callback) (?:=(?P<callbackLabel>[^\[\]\r\n]+)|[\x20\t]+(?P<callbackOptions>(?:[^"\]\r\n]++|"[^"\r\n]*")+))? \]
  | \[php(?P<phpOptions>__SIZES__)\]\s*(?P<phpModule>[a-z][a-z0-9_-]{0,63})\s*\[/php\]
  | \[func=(?P<functionName>[a-z][a-z0-9_-]{0,63})(?:[\x20\t]+value="(?P<functionArg>[^"\]\r\n]{0,500})")?\]
  | \[ (?P<form>form[1-9][0-9]{0,8}) (?P<formOptions>__SIZES__) \]
  | \[(?P<sectionMap>maplist|points)(?:[\x20\t]+(?P<sectionMapOptions>(?:[^"\]\r\n]++|"[^"\r\n]*")+))?\]
    (?: (?P<mapSection>[^\[\]]*) \[/(?:\k<sectionMap>|mapliist)\] )?
  | \[(?P<map>maps?|route)(?:[\x20\t]+(?P<mapOptions>(?:[^"\]\r\n]++|"[^"\r\n]*")+))?\]
  | \[(?P<actionButton>button)[\x20\t]+(?P<actionButtonOptions>(?:[^"\]\r\n]++|"[^"\r\n]*")+)\]
  | \[fa-icon=(?:(?P<iconStyle>solid|regular|brands):)?(?P<iconName>[a-z0-9][a-z0-9-]{0,63})\]
  | \[ (?P<reviewsCurrent>reviews) [\x20\t]+ page=current [\x20\t]* \]
  | \[(?P<shopTag>shop-[a-z][a-z-]{1,30})(?P<shopArgs>(?:[\x20\t]+[a-z][a-z_-]*=[a-z0-9_-]+)*)\]
  | \[u-(?P<userField>[a-z0-9_-]{1,64})\]
REGEX;
    $pattern = str_replace('__SIZES__', SITE_COMPONENT_SIZE_OPTIONS, $pattern);
    $wrapped = [];
    $sized = ['list-pages', 'sections', 'payment', 'item-price', 'item-buy-button', 'cities', 'phones', 'reviews'];
    foreach ($extensions as $name => $extension) {
        if ($extension['kind'] === 'wrapped') {
            $wrapped[] = preg_quote($name, '~');
        } else {
            $sized[] = preg_quote($name, '~');
        }
    }
    if ($wrapped) {
        $pattern .= ' | \[ (?P<extensionTag>' . implode('|', $wrapped) . ') (?P<extensionOptions>' . SITE_COMPONENT_SIZE_OPTIONS . ') \]'
            . ' (?P<extensionArg>[^\[\]]*) \[/\k<extensionTag>\]';
    }
    $pattern .= ' | \[(?P<sizeTag>' . implode('|', array_unique($sized)) . ')(?P<sizeOptions>'
        . substr(SITE_COMPONENT_SIZE_OPTIONS, 0, -1) . '+)\]';
    return $pattern . ' | ' . SITE_SNIPPET_TOKEN . '~six';
}

function site_expand_content(string $html, array $snippets, array &$context, bool $inline = false): string
{
    if (strpos($html, '[') === false) {
        return $html;
    }
    $index = 0;
    $expanded = preg_replace_callback(site_content_pattern($context['extensions']), static function (array $match) use ($snippets, &$context, $inline, &$index): string {
        $rendered = site_render_content_match($match, $snippets, $context);
        if (empty($context['searching']) && $rendered !== '') {
            list($sizedComponent, $options) = site_component_match_size($match);
            $rendered = site_component_size_markup($rendered, $sizedComponent, $options);
        }
        $kind = $inline ? site_inline_component_type($match, $context['extensions'], $snippets) : '';
        if ($kind === '') return $rendered;
        if ($kind === 'reviews' || ($kind === 'snippet' && strpos($rendered, '<span hidden data-snippet-edit-start=') === 0)) {
            // These editors handle the value or settings directly. Preserve subsequent source indices.
            $index++;
            return $rendered;
        }
        $label = site_translation('Править компонент', $context['editorLocale'] ?? $context['locale']);
        $id = 'inline-component-' . $index++;
        return '<a class="inline-component-link" data-inline-component href="#' . $id . '" data-inline-kind="'
            . site_escape($kind) . '">' . site_escape($label) . '</a>' . $rendered
            . '<span hidden id="' . $id . '-preview-end"></span>';
    }, $html);
    return $expanded === null ? $html : $expanded;
}

function site_render_content_match(array $match, array $snippets, array &$context): string
{
    $searching = !empty($context['searching']);
    if (!empty($match['shopTag'])) {
        $tag = substr(strtolower($match['shopTag']), 5);
        if (!in_array($tag, site_shop_tags(), true)) return $match[0];
        if ($searching || !site_shop_enabled()) return '';
        require_once SITE_ROOT . '/shop/module.php';
        preg_match_all('/([a-z][a-z_-]*)=([a-z0-9_-]+)/i', $match['shopArgs'], $args);
        return shop_component($tag, array_combine($args[1], $args[2]) ?: [], $context);
    }
    $accountTag = strtolower($match['snippet'] ?? '');
    if (in_array($accountTag, ['register','account'], true)) {
        if ($searching) return '';
        require_once __DIR__ . '/users.php';
        return site_account_markup($context['base'], $accountTag === 'register');
    }
    if (!empty($match['userField']) || ($match['userField'] ?? '') === '0') {
        $id = strtolower($match['userField']);
        $page = $context['customFieldPage'] ?? $context['pageData'] ?? [];
        $context['customFieldDefinitions'] = $context['customFieldDefinitions'] ?? site_page_custom_definitions();
        $fields = array_change_key_case(site_page_custom_fields($page, $context['customFieldDefinitions']));
        if (isset($fields[$id])) {
            $field = $fields[$id];
            return $searching || $field['type'] === 'textarea'
                ? nl2br(site_escape($field['value']), false)
                : site_iblock_cell($field['value'], $field['type']);
        }
        $match['snippet'] = 'u-' . $id;
    }
    if (!empty($match['actionButton'])) {
        return $searching ? '' : site_component_action_button($match['actionButtonOptions'], $context);
    }
    if (!empty($match['banners'])) {
        return $searching ? '' : site_component_banners($match['bannerOptions'], $match['bannerFolder'] ?? '', $context);
    }
    if ($searching && (!empty($match['form']) || !empty($match['callback']) || !empty($match['phpModule'])
        || !empty($match['extensionTag']) || !empty($match['iconName']) || !empty($match['functionName']))) {
        return '';
    }
    if (!empty($match['gallery'])) {
        return site_component_gallery($match['gallery'], $context['base'], strtolower($match['galleryTag']) === 'gal', $match['galleryOptions']);
    }
    if (!empty($match['files'])) {
        return site_component_files($match['files'], $context['base'], $match['fileOptions']);
    }
    if (!empty($match['include'])) {
        return site_component_include($match['include'], $snippets, $context);
    }
    if (isset($match['directoryList']) && $match['directoryList'] !== '') {
        $directory = trim($match['directoryList']);
        if ($searching || !preg_match('~\A/(?:[a-z0-9_-]+/)*\z~i', $directory)
            || !site_valid_path(substr($directory, 1) . 'index.php') || strpos($directory, '/error/') === 0) {
            return '';
        }
        return site_section_list($context, true, $directory);
    }
    if (!empty($match['iblock'])) {
        $template = strtolower($match['iblockTemplate']);
        $iblockContext = null;
        if (!$searching) $iblockContext =& $context;
        return site_component_iblock($searching && $template === 'charts' ? 'table' : $template,
            $match['iblock'], site_shortcode_options($match['iblockOptions'] ?? ''), $context['locale'], $context['base'], $iblockContext);
    }
    if (!empty($match['form'])) {
        return site_component_form(strtolower($match['form']), $context);
    }
    if (!empty($match['sectionMap'])) {
        return $searching ? ' ' : site_component_section_map(strtolower($match['sectionMap']), $match['mapSection'] ?? '',
            site_shortcode_options($match['sectionMapOptions'] ?? ''), $context);
    }
    if (!empty($match['map'])) {
        $options = site_shortcode_options($match['mapOptions'] ?? '');
        $kind = strtolower($match['map']);
        if ($searching) {
            return ' ' . site_escape(($options['title'] ?? '') . ' ' . ($options['button'] ?? '')) . ' ';
        }
        if ($kind === 'route') return site_component_route($options, $context);
        return $kind === 'maps' ? site_component_all_maps($options, $context) : site_component_map($options, $context);
    }
    if (isset($match['iconName']) && $match['iconName'] !== '') {
        $style = strtolower($match['iconStyle'] ?: 'solid');
        return '<i class="fa-icon fa-' . $style . ' fa-' . strtolower($match['iconName']) . '" aria-hidden="true"></i>';
    }
    if (!empty($match['callback'])) {
        return site_component_callback(trim($match['callbackLabel'] ?? ''), $context, $match['callbackOptions'] ?? '');
    }
    if (!empty($match['phpModule'])) {
        $module = strtolower($match['phpModule']);
        return isset($context['extensions'][$module]) && site_php_module_enabled($module)
            ? site_render_extension($module, '', $context) : '';
    }
    if (!empty($match['functionName'])) {
        $args = isset($match['functionArg']) ? [html_entity_decode($match['functionArg'], ENT_QUOTES | ENT_HTML5, 'UTF-8')] : [];
        try {
            $result = ew_call(strtolower($match['functionName']), ...$args);
            return is_scalar($result) ? site_escape((string) $result) : '';
        } catch (Throwable $exception) {
            error_log('functions.php: ' . $exception->getMessage());
            return '';
        }
    }
    if (!empty($match['extensionTag'])) {
        return site_render_extension(strtolower($match['extensionTag']), $match['extensionArg'], $context);
    }
    $name = strtolower(($match['sizeTag'] ?? '') ?: ($match['snippet'] ?? ''));
    if ($name === 'reviews' || !empty($match['reviewsCurrent'])) {
        return $searching ? '' : site_component_reviews($context, !empty($match['reviewsCurrent']));
    }
    if ($name === 'conf-policy') {
        return '';
    }
    if ($name === 'payment') {
        return $searching ? '' : site_payment_markup();
    }
    if ($name === 'item-price' || $name === 'item-buy-button') {
        return $searching ? '' : site_sale_markup($name, $context);
    }
    if ($name === 'sections' || $name === 'list-pages') {
        return $searching ? '' : site_section_list($context, $name === 'list-pages');
    }
    if (isset(SITE_MOVABLE_LAYOUTS[$name])) {
        if ($searching || $context['fragment']) {
            return '';
        }
        if ($name === 'search') {
            foreach (['main-menu', 'mobile-menu'] as $slot) {
                if (isset($context['activeBlocks'][$slot])) {
                    return site_layout_placeholder('search-' . $slot);
                }
            }
        }
        if ($name === 'lang' && isset($context['activeBlocks']['mobile-menu'])) {
            return site_layout_placeholder('lang-mobile-menu');
        }
        if ($name === 'lang' && isset($context['activeBlocks']['main-menu'])) {
            return site_layout_placeholder('lang-main-menu');
        }
        return isset($context['embeddedLayout']) ? '' : site_layout_placeholder($name);
    }
    if ($name === 'privacy-content') {
        return $searching ? '' : site_layout_block('privacy', $context);
    }
    if ($name === 'privacy') {
        $name = 'conf-policy';
    }
    if (isset(site_layout_blocks()[$name])) {
        return $searching || $context['fragment'] ? '' : site_layout_block($name, $context);
    }
    if (isset($context['extensions'][$name]) && $context['extensions'][$name]['kind'] === 'single') {
        return $searching ? '' : site_render_extension($name, '', $context);
    }
    if (!$searching && isset($context['cities'])) {
        if ($name === 'cities') return site_city_picker($context);
        if ($name === 'phones') {
            return '<div data-city-phones>' . site_city_display_phones($context['cityId'], $context['cityPhones'], $context['base'], $snippets) . '</div>';
        }
        if (in_array($name, site_city_live_tokens(), true)) {
            $value = $snippets[$name] ?? ($name === 'city' ? $match[0] : '');
            return '<span data-city-token="' . site_escape($name) . '">' . $value . '</span>';
        }
        if (isset($context['contactTokens'][$name])) {
            return '<span data-city-contact="' . site_escape($name) . '">'
                . site_city_contact_markup($name, $context['base'], $context['contacts'], $snippets) . '</span>';
        }
    }
    if (isset($context['contactTokens'][$name])) {
        if ($context['contacts'] === null) {
            $context['contacts'] = site_contact_settings();
        }
        $markup = site_contact_markup($context['contactTokens'][$name], $context['base'], $context['contacts']);
        return $markup !== '' ? $markup : ($snippets[$name] ?? '');
    }
    if (isset($snippets[$name]) && is_string($snippets[$name])) {
        return site_mark_editable_snippet($name, $snippets[$name], $context);
    }
    return strpos($name, 'u-') === 0 ? '' : $match[0];
}

function site_escape(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function site_author_credit(string $locale): string
{
    $link = '<a class="product-credit-link" href="' . site_escape(SITE_PRODUCT_REPOSITORY) . '" aria-label="'
        . site_escape(SITE_PRODUCT_NAME . ' · GitHub') . '">' . site_product_logo() . '</a>';
    return '<span class="product-credit">' . $link . '</span>';
}

function site_product_logo(bool $symbolOnly = false): string
{
    static $logos = [];
    $key = $symbolOnly ? 'symbol' : 'full';
    if (!isset($logos[$key])) $logos[$key] = require SITE_ROOT . '/templates/product-logo.php';
    return $logos[$key];
}

function site_base(string $entry): string
{
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    $suffix = '/' . $entry;
    if (substr($script, -strlen($suffix)) !== $suffix) {
        return '';
    }

    $prefix = substr($script, 0, -strlen($suffix));
    $segments = array_map(static function (string $segment): string {
        return rawurlencode(rawurldecode($segment));
    }, explode('/', $prefix));

    return '/' . ltrim(implode('/', $segments), '/');
}

function site_url(string $base, string $path = ''): string
{
    return rtrim($base, '/') . '/' . $path;
}

function site_font_awesome_stylesheet(string $base): string
{
    return site_url($base, 'templates/font-awesome.php') . '?asset=all.min.css&v=7.3.1';
}

function site_template_file(string $name): string
{
    $theme = site_design_active();
    if ($theme !== '') {
        return site_design_file($theme, $name) ?: SITE_ROOT . '/templates/' . $name;
    }
    $local = SITE_ROOT . '/templates/local/' . $name;
    return is_file($local) ? $local : SITE_ROOT . '/templates/' . $name;
}

function site_layout_blocks(): array
{
    return ['header' => 'header.php', 'mobile-menu' => 'mobile-menu.php', 'menu' => 'menu.php', 'footer' => 'footer.php',
        'home' => 'home.php', 'privacy' => 'privacy.php', 'conf-policy' => 'conf-policy.php',
        'top' => 'top.php', 'cookies' => 'cookies.php', 'points-output' => 'components/points.php']
        + SITE_MOVABLE_LAYOUTS + array_fill_keys(array_keys(site_custom_layouts()), '');
}

function site_layout_block(string $name, array &$context): string
{
    $visibility = $context['layoutVisibility'][$name] ?? site_layout_visibility_default($name);
    if ($visibility === 0 || isset($context['activeBlocks'][$name])) {
        return '';
    }
    $context['activeBlocks'][$name] = true;
    try {
        $markup = site_render_layout_block($name, $context);
        if (trim($markup) !== '' && ($visibility === 1 || $visibility === 2)) {
            $markup = '<div class="' . ($visibility === 1 ? 'layout-mobile-only' : 'layout-desktop-only') . '">' . $markup . '</div>';
        }
        return site_mark_editable_layout($name, $markup, $context);
    } finally {
        unset($context['activeBlocks'][$name]);
    }
}

function site_render_layout_block(string $name, array &$context): string
{
    if (($name === 'search' && $context['settings']['searchMode'] === 'off')
        || ($name === 'bread-crumbs' && (!$context['settings']['breadcrumbs'] || !empty($context['searchMode'])
            || $context['pageData']['path'] === '' || ($context['originalPath'] === 'index.php' && empty($context['embeddedLayout']))))) {
        return '';
    }
    $snippets = $context['snippets'];
    if (array_key_exists($name, $snippets)) {
        return site_expand_content($snippets[$name], $snippets, $context);
    }

    $base = $context['base'];
    $page = $context['pageData'];
    $settings = $context['settings'];
    $brand = $context['brand'];
    $locale = $context['locale'] ?? 'ru';
    $originalPath = $context['originalPath'] ?? $page['path'];
    $searchMode = $context['searchMode'] ?? false;
    $searchQuery = $context['searchQuery'] ?? '';
    $title = $context['title'] ?? '';
    $navKey = $name === 'mobile-menu' ? 'mobileNavigation' : 'navigation';
    if (in_array($name, ['header', 'mobile-menu', 'main-menu'], true) && !isset($context[$navKey])) {
        $context[$navKey] = site_navigation_pages($name === 'mobile-menu');
        if ($locale !== 'ru') {
            foreach ($context[$navKey] as &$item) {
                $translation = site_read_page(site_language_page_path($item['path'], $locale));
                if ($translation !== null && site_page_is_public($translation['fields'])) {
                    $item['fields'] = $translation['fields'];
                }
            }
            unset($item);
        }
    }
    $navigation = $context[$navKey] ?? null;
    $layoutFile = site_layout_blocks()[$name];
    if (isset($context['phpLayouts'][$name])) {
        $level = ob_get_level();
        ob_start();
        try {
            // Executable source saved through the authenticated, syntax-checked layout editor.
            (static function (string $source) use ($name, &$context, $snippets, $base, $page, $settings, $brand, $locale, $originalPath, $navigation, $layoutFile, $searchMode, $searchQuery, $title) {
                eval('?>' . $source);
            })($context['phpLayouts'][$name]);
            return site_expand_content((string) ob_get_clean(), $snippets, $context);
        } catch (Throwable $exception) {
            while (ob_get_level() > $level) {
                ob_end_clean();
            }
            error_log('Layout ' . $name . ': ' . $exception->getMessage());
        }
    }
    if ($layoutFile === '') {
        return '';
    }
    ob_start();
    require !empty($context['stockLayouts'][$name]) ? SITE_ROOT . '/templates/' . $layoutFile : site_template_file($layoutFile);
    return (string) ob_get_clean();
}

function site_valid_part(string $part): bool
{
    return preg_match('/\A' . SITE_IDENTIFIER_PATTERN . '\z/', $part) === 1
        && preg_match('/\A(?:con|prn|aux|nul|com[1-9]|lpt[1-9])\z/i', $part) !== 1;
}

function site_valid_path(string $path): bool
{
    if (strlen($path) > 240 || substr($path, -4) !== '.php') {
        return false;
    }

    $parts = explode('/', substr($path, 0, -4));
    if (strtolower($parts[0]) === 'error') {
        return preg_match('~\Aerror/404/index(?:_[a-z]{2})?\.php\z~', $path) === 1;
    }
    if (count($parts) === 1 && site_page_translation($path)[0] !== 'index.php') {
        return false;
    }
    if (in_array(strtolower($parts[0]), ['admin', 'app', 'assets', 'css', 'data', 'docs', 'extensions', 'files', 'fonts', 'form', 'icons',
        'images', 'img', 'js', 'maintenance', 'media', 'router', 'scripts', 'shop', 'static', 'styles', 'templates', 'update', 'uploads', 'vendor'], true)) {
        return false;
    }

    foreach ($parts as $part) {
        if (!site_valid_part($part)) {
            return false;
        }
    }

    return true;
}

function site_page_url(string $base, string $path): string
{
    if (basename($path) === 'index.php') {
        $path = substr($path, 0, -strlen('index.php'));
    }

    return site_url($base, $path);
}

function site_canonical_url(string $base, string $path): string
{
    $host = site_host();
    if ($path === '' || $host === '') {
        return '';
    }
    $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    $scheme = $https ? 'https' : 'http';
    $port = preg_match('/:([0-9]{1,5})\z/', $_SERVER['HTTP_HOST'] ?? '', $match) === 1 ? (int) $match[1] : 0;
    if ($port > 65535) {
        return '';
    }
    $suffix = $port !== 0 && $port !== ($https ? 443 : 80) ? ':' . $port : '';
    return $scheme . '://' . $host . $suffix . site_page_url($base, $path);
}

function site_header_inserts(): array
{
    $record = site_read_record(site_content_record(SITE_HEADERS_FILE), SITE_HEADERS_RECORD_LIMIT);
    $inserts = ['head' => '', 'bodyEnd' => ''];
    foreach ($inserts as $key => $default) {
        $value = $record[$key] ?? $default;
        if (!is_string($value) || strlen($value) > SITE_HEADER_INSERT_LIMIT) {
            throw new RuntimeException('Не удалось прочитать служебные заголовки.');
        }
        $inserts[$key] = $value;
    }
    return $inserts;
}

function site_page_header(string $path): string
{
    if ($path === 'index.php') {
        return <<<'PHP'
<?php
define('SITE_ROOT', __DIR__);
require SITE_ROOT . '/app/site.php';
site_render(isset($pageFile) ? $pageFile : __FILE__);
__halt_compiler();
PHP;
    }

    $toRoot = str_repeat('../', substr_count($path, '/'));
    return "<?php\n\$pageFile = __FILE__;\nrequire __DIR__ . '/" . $toRoot
        . "index.php';\n__halt_compiler();";
}

function site_page_fields(): array
{
    return ['Title', 'Heading', 'Description', 'Keywords', 'Menu', 'Order'];
}

function site_schedule_date_valid(string $value): bool
{
    if ($value === '') {
        return true;
    }
    if (!preg_match('/\A[1-9][0-9]{3}-[0-9]{2}-[0-9]{2}T[0-9]{2}:[0-9]{2}\z/', $value)) {
        return false;
    }
    $date = DateTimeImmutable::createFromFormat('!Y-m-d\TH:i', $value);
    return $date !== false && $date->format('Y-m-d\TH:i') === $value;
}

function site_page_is_public(array $fields): bool
{
    static $now;
    if ($now === null) {
        $now = date('Y-m-d\TH:i');
    }
    $from = $fields['ActiveFrom'] ?? '';
    $until = $fields['ActiveUntil'] ?? '';
    return ($fields['Hidden'] ?? '0') === '0' && ($fields['Active'] ?? '1') === '1'
        && ($from === '' || $from <= $now) && ($until === '' || $now < $until);
}

function site_page_metadata_defaults(): array
{
    return ['Title' => '', 'Heading' => '', 'Description' => '', 'Keywords' => '', 'Menu' => '', 'Order' => '100', 'Hidden' => '0',
        'MenuVisible' => '0', 'MenuExpand' => '0', 'MobileMenu' => '', 'Sitemap' => '1', 'Lat' => '', 'Lng' => '', 'Redirect' => '', 'CSS' => '', 'JS' => '',
        'Active' => '1', 'ActiveFrom' => '', 'ActiveUntil' => '', 'Sale' => '',
        'Announcement' => '', 'AnnouncementImage' => '', 'AnnouncementIcon' => '', 'CustomFields' => ''];
}

function site_valid_metadata(array $fields): bool
{
    foreach (array_diff_key($fields, ['CSS' => true, 'JS' => true, 'Sale' => true, 'Announcement' => true, 'CustomFields' => true]) as $value) {
        if (strlen($value) > 1500 || preg_match('/[\x00-\x1F\x7F]/', $value)) {
            return false;
        }
    }

    foreach (['CSS', 'JS', 'Announcement'] as $name) {
        if (site_asset_decode($fields[$name] ?? '') === null) {
            return false;
        }
    }
    return site_sale_decode($fields['Sale'] ?? '') !== null && site_decode_page_custom_values($fields['CustomFields'] ?? '') !== null
        && $fields['Title'] !== '' && preg_match('/\A[0-9]{1,5}\z/', $fields['Order']) === 1
        && in_array($fields['MenuVisible'] ?? '0', ['0', '1'], true)
        && in_array($fields['MenuExpand'] ?? '0', ['0', '1'], true)
        && in_array($fields['MobileMenu'] ?? '', ['', '0', '1'], true)
        && in_array($fields['Sitemap'] ?? '1', ['0', '1'], true)
        && in_array($fields['Active'] ?? '1', ['0', '1'], true)
        && in_array($fields['Hidden'] ?? '0', ['0', '1'], true);
}

/** @return array|null */
function site_read_page(string $path, ?string $root = null)
{
    if (!site_valid_path($path)) {
        return null;
    }

    $file = ($root ?? site_content_root()) . '/' . $path;
    $resolved = realpath($file);
    $expected = str_replace('/', DIRECTORY_SEPARATOR, $file);
    if ($resolved !== $expected || !is_file($file)) {
        return null;
    }

    $limit = SITE_PAGE_RECORD_LIMIT;
    $bytes = @file_get_contents($file, false, null, 0, $limit + 1);
    if ($bytes === false || strlen($bytes) > $limit) {
        return null;
    }

    $revision = hash('sha256', $bytes);
    static $memo = [];
    if (isset($memo[$path]) && $memo[$path]['revision'] === $revision) {
        return $memo[$path];
    }
    $cache = site_performance_settings();
    if ($cache['cache']) {
        $cacheKey = 'file-site:' . hash('sha256', $file . ':' . $revision . ':' . $cache['generation'] . ':' . (require __DIR__ . '/version.php'));
        $inMemory = function_exists('apcu_enabled') && apcu_enabled();
        if ($inMemory) {
            $stored = apcu_fetch($cacheKey);
        } else {
            try {
                $record = site_read_record(site_page_cache_file($path), 2 * $limit + 65536);
                $stored = ($record['key'] ?? '') === $cacheKey ? ($record['page'] ?? null) : null;
            } catch (RuntimeException $exception) {
                $stored = null;
            }
        }
        if (is_array($stored) && $stored['path'] === $path && $stored['revision'] === $revision) {
            return $memo[$path] = $stored;
        }
    }
    if (preg_match('//u', $bytes) !== 1) {
        return null;
    }

    $header = site_page_header($path);
    $pattern = str_replace("\n", '\r?\n', preg_quote($header, '~'));
    if (!preg_match('~\A' . $pattern . '\r?\n~', $bytes, $match)) {
        return null;
    }

    $sections = preg_split('/\r?\n\r?\n/', substr($bytes, strlen($match[0])), 2);
    if (count($sections) !== 2) {
        return null;
    }

    $fields = site_page_metadata_defaults();
    $seen = [];
    foreach (preg_split('/\r?\n/', $sections[0]) as $line) {
        $pair = explode(':', $line, 2);
        if (count($pair) !== 2 || !array_key_exists($pair[0], $fields) || isset($seen[$pair[0]])) {
            return null;
        }

        $seen[$pair[0]] = true;
        $fields[$pair[0]] = trim($pair[1], ' ');
    }

    if (!isset($seen['MenuVisible'])) {
        $fields['MenuVisible'] = $fields['Menu'] === '' ? '0' : '1';
    }
    if (!site_valid_metadata($fields) || !site_schedule_date_valid($fields['ActiveFrom']) || !site_schedule_date_valid($fields['ActiveUntil'])
        || ($fields['ActiveFrom'] !== '' && $fields['ActiveUntil'] !== '' && $fields['ActiveFrom'] >= $fields['ActiveUntil'])) {
        return null;
    }

    $parsed = [
        'path' => $path,
        'fields' => $fields,
        'html' => $sections[1],
        'revision' => $revision,
        'menu_explicit' => isset($seen['MenuVisible']),
    ];
    if ($cache['cache']) {
        if ($inMemory) {
            apcu_store($cacheKey, $parsed, 3600);
        } else {
            site_page_cache_write(site_page_cache_file($path), ['key' => $cacheKey, 'page' => $parsed]);
        }
    }
    return $memo[$path] = $parsed;
}

function site_pages(bool $includeTranslations = false, ?string $root = null): array
{
    $root = $root ?? site_content_root();
    $directory = new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS);
    $filter = new RecursiveCallbackFilterIterator($directory, static function (SplFileInfo $item) use ($root): bool {
        if ($item->isLink()) {
            return false;
        }

        $path = str_replace(DIRECTORY_SEPARATOR, '/', substr($item->getPathname(), strlen($root) + 1));
        if ($item->isDir()) {
            return site_valid_path($path . '/index.php')
                && $item->getRealPath() === str_replace('/', DIRECTORY_SEPARATOR, $item->getPathname());
        }

        return site_valid_path($path);
    });

    $pages = [];
    $files = new RecursiveIteratorIterator($filter, RecursiveIteratorIterator::LEAVES_ONLY, RecursiveIteratorIterator::CATCH_GET_CHILD);
    foreach ($files as $file) {
        $path = str_replace(DIRECTORY_SEPARATOR, '/', substr($file->getPathname(), strlen($root) + 1));
        if (!$includeTranslations && site_page_translation($path)[1] !== '') {
            continue;
        }
        $page = site_read_page($path, $root);
        if ($page !== null) {
            $pages[] = ['path' => $path, 'fields' => array_diff_key($page['fields'], ['CSS' => true, 'JS' => true]), 'revision' => $page['revision']];
        }
    }

    usort($pages, static function (array $left, array $right): int {
        $order = (int) $left['fields']['Order'] <=> (int) $right['fields']['Order'];
        return $order !== 0 ? $order : strcmp($left['path'], $right['path']);
    });

    return $pages;
}

function site_asset_decode(string $encoded)
{
    if (strlen($encoded) > (int) ceil(SITE_ASSET_LIMIT / 3) * 4) {
        return null;
    }
    $code = base64_decode($encoded, true);
    return $code !== false && strlen($code) <= SITE_ASSET_LIMIT && preg_match('//u', $code) === 1
        && strpos($code, "\0") === false && base64_encode($code) === $encoded ? $code : null;
}

function site_global_assets(): array
{
    $record = site_read_record(site_content_record(SITE_ASSETS_FILE), SITE_ASSET_RECORD_LIMIT);
    $assets = ['CSS' => '', 'JS' => ''];
    foreach ($assets as $kind => $empty) {
        $encoded = $record[$kind] ?? '';
        if (!is_string($encoded) || site_asset_decode($encoded) === null) {
            throw new RuntimeException('Неверный формат файла со стилями и скриптами.');
        }
        $assets[$kind] = $encoded;
    }
    return $assets;
}

function site_asset_urls(string $base, array $page): array
{
    try {
        $global = site_global_assets();
    } catch (RuntimeException $exception) {
        $global = [];
    }
    $urls = ['CSS' => [], 'JS' => []];
    foreach (['' => $global, 'page' => $page['fields']] as $scope => $assets) {
        foreach ($urls as $kind => $items) {
            if (!empty($assets[$kind])) {
                $params = ['type' => strtolower($kind)];
                if ($scope === 'page') {
                    $params['page'] = $page['path'];
                }
                $urls[$kind][] = site_url($base, 'templates/custom-assets.php') . '?' . http_build_query($params);
            }
        }
    }
    return $urls;
}

function site_page_menu_visible(array $fields, bool $mobile): bool
{
    $mobileChoice = $fields['MobileMenu'] ?? '';
    return ($mobile && $mobileChoice !== '' ? $mobileChoice : $fields['MenuVisible']) === '1';
}

function site_navigation_pages(bool $mobile = false): array
{
    $pages = site_pages();
    $excluded = [];
    foreach ($pages as $page) {
        if ((!site_page_is_public($page['fields']) || !site_page_menu_visible($page['fields'], $mobile)) && basename($page['path']) === 'index.php') {
            $excluded[dirname($page['path'])] = true;
        }
    }

    $visible = array_filter($pages, static function (array $page) use ($excluded, $mobile): bool {
        if (!site_page_is_public($page['fields']) || !site_page_menu_visible($page['fields'], $mobile)) {
            return false;
        }
        $folder = dirname($page['path']);
        while ($folder !== '.') {
            if (isset($excluded[$folder])) {
                return false;
            }
            $folder = dirname($folder);
        }
        return !isset($excluded['.']);
    });
    $byPath = array_column($visible, null, 'path');
    return site_navigation_branch(site_page_tree($pages), $byPath);
}

function site_section_list(array $context, bool $recursive = false, ?string $directory = null): string
{
    $path = $context['originalPath'] ?? '';
    if ($directory === null && ($path === '' || (!$recursive && basename($path) !== 'index.php'))) {
        return '';
    }
    static $trees = [];
    $root = site_content_root();
    $tree = $trees[$root] ?? ($trees[$root] = site_page_tree(site_pages()));
    $folder = $directory === null ? dirname($path) : ($directory === '/' ? '.' : trim($directory, '/'));
    $links = [];
    $visit = static function (string $folder) use (&$visit, $tree, $path, $context, $recursive, &$links) {
        foreach ($tree[$folder] ?? [] as $node) {
            $entry = $node['page'];
            if ($entry !== null && $entry['path'] !== $path && $entry['path'] !== 'index.php'
                && strpos($entry['path'], 'error/') !== 0 && site_page_is_public($entry['fields'])) {
                $localized = site_localized_page($entry['path'], $context['locale']) ?? $entry;
                $label = site_expand_text($localized['fields']['Menu'] ?: $localized['fields']['Title'], $context['snippets']);
                $announcement = site_page_announcement($localized, $context['snippets']);
                $image = site_page_media_url($localized['fields']['AnnouncementImage'] ?? '', $context['base']);
                $icon = $image === '' ? site_page_icon($localized, $context['base']) : '';
                $links[] = '<li>' . ($image === '' ? '' : '<img class="section-announcement-image" src="' . site_escape($image) . '" alt="" loading="lazy">')
                    . '<a href="' . site_escape(site_page_link($context['base'], $localized)) . '">' . $icon . site_escape($label) . '</a>'
                    . ($announcement === '' ? '' : '<div class="section-announcement">' . $announcement . '</div>') . '</li>';
            }
            if ($recursive && $node['folder'] !== '') {
                $visit($node['folder']);
            }
        }
    };
    $visit($folder);
    return $links ? '<ul class="section-links">' . implode('', $links) . '</ul>' : '';
}

function site_page_announcement(array $page, array $snippets): string
{
    $html = site_asset_decode($page['fields']['Announcement'] ?? '') ?? '';
    if (stripos($html, '[u-') !== false) {
        foreach (site_page_custom_fields($page) as $id => $field) {
            $snippets['u-' . strtolower((string) $id)] = site_escape($field['value']);
        }
    }
    $html = preg_replace('~<!--.*?-->|<(script|style|template|iframe|object|svg)\b[^>]*>.*?(?:</\1\s*>|$)~is', '', $html);
    $html = site_remove_search_shortcodes($html);
    $allowed = '<p><br><b><strong><i><em><u><s><strike><sup><sub><ul><ol><li><blockquote><h1><h2><h3><h4><h5><h6><span>';
    $html = strip_tags($html, $allowed);
    $markup = '';
    $open = [];
    foreach (preg_split('~(</?[a-z][a-z0-9]*\b[^>]*>)~i', $html, -1, PREG_SPLIT_DELIM_CAPTURE) as $part) {
        if (!preg_match('~\A<(/?)([a-z][a-z0-9]*)\b([^>]*)>\z~i', $part, $match)) {
            $markup .= site_escape(site_expand_text(html_entity_decode($part, ENT_QUOTES | ENT_HTML5, 'UTF-8'), $snippets));
            continue;
        }
        $tag = strtolower($match[2]);
        if ($match[1] === '/') {
            if (end($open) === $tag) {
                array_pop($open);
                $markup .= '</' . $tag . '>';
            }
        } elseif ($tag !== 'li' || in_array(end($open), ['ul', 'ol'], true)) {
            $markup .= '<' . $tag . ($tag === 'span' ? site_announcement_text_style($match[3]) : '') . '>';
            if ($tag !== 'br') $open[] = $tag;
        }
    }
    foreach (array_reverse($open) as $tag) $markup .= '</' . $tag . '>';
    return site_search_text($markup) === '' ? '' : $markup;
}

function site_page_media_url(string $value, string $base): string
{
    $url = site_map_url($value);
    return $url !== '' && $url[0] === '/' ? site_url($base, substr($url, 1)) : $url;
}

function site_page_system_icons(): array
{
    return ['site.png', 'contact/phone.png', 'contact/tg.png', 'contact/vk.png', 'contact/whatsapp.png',
        'contact/youtube.png', 'contact/instagram.png', 'contact/max.png'];
}

function site_font_awesome_icons(): array
{
    static $icons;
    if ($icons === null) $icons = require __DIR__ . '/editor-icons.php';
    return $icons;
}

function site_parse_page_icon(string $code): ?array
{
    if (preg_match('~\A\[site-icon=([a-z/.]+)\]\z~', $code, $match) && in_array($match[1], site_page_system_icons(), true)) {
        return ['image', $match[1]];
    }
    if (preg_match('~\A\[fa-icon=(?:(solid|regular|brands):)?([a-z0-9][a-z0-9-]{0,63})\]\z~', $code, $match)) {
        $style = $match[1] ?: 'solid';
        if (in_array($match[2], site_font_awesome_icons()[$style] ?? [], true)) {
            return [$style, $match[2]];
        }
    }
    return null;
}

function site_page_icon(array $page, string $base): string
{
    $icon = site_parse_page_icon($page['fields']['AnnouncementIcon'] ?? '');
    if ($icon === null) {
        return '';
    }
    if ($icon[0] === 'image') {
        return '<img class="page-label-icon" src="' . site_escape(site_url($base, 'templates/icons/' . $icon[1])) . '" width="16" height="16" alt="">';
    }
    static $fontawesome;
    if ($fontawesome === null) {
        try { $fontawesome = site_library_settings(site_read_record(SITE_LIBRARIES_FILE))['enabled']['fontawesome']; }
        catch (RuntimeException $exception) { $fontawesome = false; }
    }
    return $fontawesome ? '<i class="page-label-icon fa-' . $icon[0] . ' fa-' . $icon[1] . '" aria-hidden="true"></i>' : '';
}

function site_page_menu_label(string $label, ?array $page, string $base): string
{
    $text = site_escape($label);
    $icon = $page === null ? '' : site_page_icon($page, $base);
    return $icon === '' ? $text : '<span class="page-menu-label">' . $icon . '<span>' . $text . '</span></span>';
}

function site_page_redirect_url(string $base, string $path, string $value): string
{
    $url = site_map_url($value);
    if ($url === '') {
        return '';
    }
    if ($url[0] === '/') {
        $target = parse_url($url, PHP_URL_PATH);
        $original = site_page_translation($path)[0];
        if (in_array($target, [site_page_url('', $original), '/' . $original, '/' . $path], true)) {
            return '';
        }
        return site_url($base, substr($url, 1));
    }
    $target = parse_url($url);
    if (strtolower($target['host'] ?? '') === site_host()
        && in_array($target['path'] ?? '/', [site_page_url($base, site_page_translation($path)[0]), site_url($base, $path)], true)) {
        return '';
    }
    return !isset($target['user']) && !isset($target['pass']) ? $url : '';
}

function site_page_link(string $base, array $page): string
{
    $path = $page['path'];
    return site_page_redirect_url($base, $path, $page['fields']['Redirect'] ?? '')
        ?: site_page_url($base, site_page_translation($path)[0]);
}

function site_page_tree(array $pages): array
{
    $groups = ['.' => []];
    foreach ($pages as $page) {
        $path = $page['path'];
        $folder = dirname($path);
        $order = (int) $page['fields']['Order'];
        for ($current = $folder; $current !== '.'; $current = dirname($current)) {
            $groups[$current] = $groups[$current] ?? [];
            $parent = dirname($current);
            if (!isset($groups[$parent][$current])) {
                $groups[$parent][$current] = ['folder' => $current, 'page' => null, 'order' => $order];
            }
        }
        if ($folder !== '.' && basename($path) === 'index.php') {
            $groups[dirname($folder)][$folder]['page'] = $page;
            $groups[dirname($folder)][$folder]['order'] = $order;
        } else {
            $groups[$folder][$path] = ['folder' => '', 'page' => $page, 'order' => $path === 'index.php' ? -1 : $order];
        }
    }
    foreach ($groups as &$group) {
        uasort($group, static function (array $left, array $right): int {
            $order = $left['order'] <=> $right['order'];
            $leftPath = $left['folder'] ?: $left['page']['path'];
            $rightPath = $right['folder'] ?: $right['page']['path'];
            return $order ?: strcmp($leftPath, $rightPath);
        });
    }
    return $groups;
}

function site_navigation_branch(array $tree, array $byPath, string $folder = '.'): array
{
    $links = [];
    foreach ($tree[$folder] ?? [] as $node) {
        $path = $node['page']['path'] ?? '';
        if (isset($byPath[$path])) {
            $links[] = $byPath[$path];
        }
        if ($node['folder'] !== '') {
            foreach (site_navigation_branch($tree, $byPath, $node['folder']) as $child) {
                $links[] = $child;
            }
        }
    }
    return $links;
}

function site_navigation_menu_tree(array $tree, string $folder = '.'): array
{
    $items = [];
    foreach ($tree[$folder] ?? [] as $node) {
        $page = $node['page'];
        $subfolder = $node['folder'];
        if ($page === null) {
            foreach (site_navigation_menu_tree($tree, $subfolder) as $item) $items[] = $item;
            continue;
        }
        $children = $subfolder !== '' && ($page['fields']['MenuExpand'] ?? '0') === '1'
            ? site_navigation_menu_tree($tree, $subfolder) : [];
        $items[] = ['page' => $page, 'children' => $children];
    }
    return $items;
}

function site_render(string $file, ?string $routeBase = null, array $virtual = [])
{
    ew_functions();
    $path = str_replace(DIRECTORY_SEPARATOR, '/', substr($file, strlen(SITE_ROOT) + 1));
    $base = $routeBase ?? site_base($path);
    if ($path === 'error/404/index.php' && ($_SERVER['REDIRECT_STATUS'] ?? '') === '404' && site_content_root() !== SITE_ROOT) {
        $requested = site_theme_request_path($base, explode('?', $_SERVER['REQUEST_URI'] ?? '', 2)[0]);
        if ($requested !== $path && site_valid_path($requested) && site_read_page(site_page_translation($requested)[0]) !== null) {
            $path = $requested;
            http_response_code(200);
        }
    }
    if ($path === 'privacy.php') {
        http_response_code(404);
        return;
    }
    if (!$virtual && $path === 'index.php' && ($_GET['account'] ?? '') === '1') {
        header('Location: ' . site_url($base, 'app/users-route.php'), true, 302);
        return;
    }
    list($originalPath, $explicit) = site_page_translation($path);
    $enabled = site_region_settings()['languages'];
    $locale = site_public_language($base, $explicit);
    $settings = site_settings();
    $searchMode = !$virtual && $originalPath === 'index.php' && empty($_SERVER['PATH_INFO']) && array_key_exists('search', $_GET)
        && ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET';
    if ($searchMode && $settings['searchMode'] === 'off') {
        http_response_code(404);
        return;
    }
    $searchQuery = $searchMode ? site_search_query($_GET['search']) : '';
    $searchIndex = $searchMode ? site_search_index() : [];
    if ($searchMode && ($_GET['suggest'] ?? '') === '1' && !isset($_GET['ajax'])) {
        header('Content-Type: application/json; charset=UTF-8');
        header('X-Content-Type-Options: nosniff');
        header('Cache-Control: no-store');
        $results = site_search_find($searchIndex, $searchQuery, $locale, 6, 0, $base);
        foreach ($results as &$result) {
            $result = ['title' => $result['title'], 'url' => $result['url']];
        }
        unset($result);
        echo json_encode($results, JSON_UNESCAPED_UNICODE);
        return;
    }
    $brand = site_brand_settings();
    $variables = array_filter(array_diff_key($settings['variables'], site_layout_blocks()), 'is_string');
    $cityRequest = !$virtual && $originalPath === 'index.php' && empty($_SERVER['PATH_INFO']) && isset($_GET['city_select']);
    $contentHost = site_content_host($base, !$cityRequest && !$searchMode && ($_GET['ajax'] ?? '') !== '1');
    $stockLayouts = [];
    $phpLayouts = [];
    $layoutVisibility = [];
    $snippets = ['author' => site_author_credit($locale)] + site_date_snippets()
        + site_snippet_values($contentHost, $stockLayouts, $phpLayouts, $layoutVisibility) + array_map('site_escape', $variables);
    try {
        $cities = site_cities();
    } catch (RuntimeException $exception) {
        $cities = [];
    }
    $cityId = site_selected_city($cities, $contentHost);
    if ($cityRequest) {
        $cityId = $_GET['city_select'];
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'GET' || !site_public_context($base, true)) {
            http_response_code(403);
            return;
        }
        if (!is_string($cityId) || !isset($cities[$cityId])) {
            http_response_code(422);
            return;
        }
    }
    $cityPhones = [];
    $city = [];
    if ($cityId !== '') {
        $city = $cities[$cityId];
        $phoneSnippets = $snippets;
        if ($city['domain'] !== '' && $city['domain'] !== $contentHost) {
            $phoneSnippets = site_snippet_values($city['domain']) + $snippets;
        }
        $cityPhones = site_city_phone_rows($city, ['city' => site_escape($city['name'])] + $phoneSnippets);
        $snippets = site_city_snippets($city, $cityPhones)
            + array_intersect_key($phoneSnippets, array_flip(site_city_live_tokens())) + $snippets;
    }
    $headerContacts = site_header_contact_values($city, $snippets);
    $snippets = array_map(static function (array $field): string { return site_escape($field['value']); }, $headerContacts) + $snippets;
    $contacts = site_city_contacts($city, $cityPhones);
    if ($cityRequest) {
        site_city_response($cityId, $cityPhones, $contacts, $snippets, $base, $headerContacts);
        return;
    }
    $settings['name'] = site_expand_text($settings['name'], $snippets);
    $settings['subtitle'] = site_expand_text($settings['subtitle'], $snippets);
    $sitePublicUrl = $settings['publicUrl'] ?: site_canonical_url($base, 'index.php');
    $snippets = ['site-name' => site_escape($settings['name']), 'site-url' => site_escape($sitePublicUrl)] + $snippets;
    $page = empty($_SERVER['PATH_INFO']) && ($explicit === '' || in_array($explicit, $enabled, true))
        ? site_localized_page($originalPath, $locale) : null;
    if ($virtual) {
        $page = ['path'=>$page['path'] ?? 'index.php', 'language'=>$locale,
            'fields'=>['Title'=>$virtual['title'],'Heading'=>$virtual['title'],'Description'=>$virtual['description'] ?? ''] + site_page_metadata_defaults(),
            'html'=>$virtual['html']];
    }
    if ($originalPath === 'error/404/index.php') {
        http_response_code(404);
    }
    if ($page === null) {
        http_response_code(404);
        $page = site_localized_page('error/404/index.php', $locale) ?? [
            'path' => '',
            'language' => $locale,
            'fields' => ['Title' => site_translation('Страница не найдена', $locale), 'Heading' => '', 'Description' => ''],
            'html' => '<p>' . site_escape(site_translation('Проверьте адрес страницы или выберите раздел в меню.', $locale)) . '</p>',
        ];
        $page['path'] = '';
    }

    header('Content-Type: text/html; charset=UTF-8');
    header('X-Content-Type-Options: nosniff');
    $fragment = ($_GET['ajax'] ?? '') === '1';
    if (!$virtual && !$fragment && !$searchMode && $page['path'] !== '' && $originalPath !== 'error/404/index.php'
        && in_array($_SERVER['REQUEST_METHOD'] ?? 'GET', ['GET', 'HEAD'], true)) {
        $redirect = site_page_redirect_url($base, $page['path'], $page['fields']['Redirect']);
        if ($redirect !== '') {
            header('Location: ' . $redirect, true, 302);
            return;
        }
    }
    $editor = $fragment ? [] : site_editor_session($base);
    $public = site_public_context($base, $fragment);
    if ($fragment && !$public) {
        http_response_code(403);
        echo site_translation('Доступ к фрагменту разрешён только из сеанса на этом сайте.', $locale);
        return;
    }
    $components = [
        'base' => $base, 'page' => $page['path'], 'active' => [$originalPath => true],
        'forms' => null, 'formSession' => $public,
        'contactTokens' => site_contact_tokens(), 'contacts' => $contacts,
        'cities' => $cities, 'cityId' => $cityId, 'cityPhones' => $cityPhones, 'headerContacts' => $headerContacts,
        'extensions' => site_extensions(),
        'pageData' => $page, 'settings' => $settings, 'snippets' => $snippets, 'brand' => $brand,
        'navigation' => null, 'activeBlocks' => [], 'fragment' => $fragment, 'stockLayouts' => $stockLayouts, 'phpLayouts' => $phpLayouts,
        'layoutVisibility' => $layoutVisibility,
        'locale' => $locale, 'editorLocale' => $editor['language'] ?? $locale, 'originalPath' => $originalPath, 'mapPoints' => [],
    ];
    $components += $virtual['context'] ?? [];
    $inline = !$virtual && !$searchMode && $page['path'] !== '' && ($editor['edit'] ?? false);
    $components['bannerEdit'] = $inline;
    $components['layoutInventory'] = !$fragment && (bool) $editor;
    $components['layoutEdit'] = !$fragment && ($editor['edit'] ?? false);
    if ($components['layoutEdit']) $components['snippetHost'] = $contentHost;
    if ($components['layoutInventory'] && $contentHost !== '') {
        try {
            $ownLayouts = site_read_layout_snippets($contentHost);
            $visibilityOverrides = $ownLayouts['_layoutVisibility'] ?? [];
            $components['layoutDomainOverrides'] = $ownLayouts + (is_array($visibilityOverrides) ? $visibilityOverrides : []);
            $components['layoutDomain'] = $contentHost === site_host() ? 'current' : $contentHost;
        } catch (RuntimeException $exception) {
            $components['layoutDomainOverrides'] = [];
        }
    }
    $content = $searchMode && !$fragment ? '' : site_finish_map(site_expand_content($page['html'], $snippets, $components, $inline), $components);
    if (!$searchMode && $page['path'] !== '' && preg_match('~\A(?:\s|&nbsp;|</?(?:p|div)>|<br\s*/?>)*\z~i', $page['html'])) {
        $content = site_section_list($components);
    }
    unset($components['mapPoints']);
    if ($inline && !$fragment) {
        $content = site_mark_editable_page_content($content, $base, $page['path']);
    }
    if (!$virtual && !$searchMode && $page['path'] !== '' && $originalPath === 'index.php') {
        $components['pageContent'] = $content;
        $components['snippets']['page-content'] = $content;
        $content = site_layout_block('home', $components);
        unset($components['pageContent'], $components['snippets']['page-content']);
    }
    $fields = $page['fields'];
    $title = site_expand_text($fields['Title'], $snippets);
    $heading = $fields['Heading'] !== '' ? site_expand_text($fields['Heading'], $snippets) : $title;
    if ($searchMode && !$fragment) {
        $title = $heading = site_translation('Поиск по сайту', $locale);
        $fields['Description'] = '';
        $fields['Keywords'] = '';
        $requestedPage = $_GET['search_page'] ?? '';
        $searchPage = is_string($requestedPage) && preg_match('/\A[1-9][0-9]{0,3}\z/', $requestedPage)
            ? (int) $requestedPage : 1;
        $searchOffset = ($searchPage - 1) * 20;
        $searchResults = site_search_find($searchIndex, $searchQuery, $locale, 21, $searchOffset, $base);
        $searchHasNext = count($searchResults) > 20;
        if ($searchHasNext) {
            array_pop($searchResults);
        }
        ob_start();
        require site_template_file('search-results.php');
        $content = ob_get_clean();
    }
    if ($fragment) {
        $content = site_content_html_urls($content, $base);
        require site_template_file('content.php');
        return;
    }
    $components['title'] = $title;
    $components['searchMode'] = $searchMode;
    $components['searchQuery'] = $searchQuery;
    ob_start();
    require site_template_file('layout.php');
    echo site_content_html_urls(site_place_layout_blocks((string) ob_get_clean(), $components), $base);
}

if (site_region_settings()['timezone'] !== 'auto') {
    date_default_timezone_set(site_region_settings()['timezone']);
}
