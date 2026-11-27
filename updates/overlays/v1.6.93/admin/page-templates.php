<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}

const SITE_PAGE_TEMPLATES_FILE = SITE_ROOT . '/admin/.page-templates.php';
const SITE_PAGE_TEMPLATES_LIMIT = 2097152;

function admin_page_template_labels(string $language): array
{
    static $labels;
    if ($labels === null) $labels = require SITE_ROOT . '/app/help-page-templates.php';
    return $labels[$language] ?? $labels['ru'];
}

function admin_page_template_catalog_labels(string $language): array
{
    static $labels;
    if ($labels === null) $labels = require SITE_ROOT . '/app/page-template-catalog-labels.php';
    return array_combine(
        ['search', 'all', 'stock', 'custom', 'duplicate', 'from_page', 'open_selected', 'help', 'copy_suffix', 'save', 'icon'],
        $labels[$language] ?? $labels['ru']
    );
}

function admin_stock_page_templates(): array
{
    static $templates;
    if ($templates === null) {
        $templates = array_column(require __DIR__ . '/page-template-stock.php', null, 0);
    }
    return $templates;
}

function admin_marketplace_page_templates(): array
{
    return [
        'shop-category'=>['shop_category_template','boxes-stacked','[shop]'],
        'shop-product'=>['shop_product_template','tag', <<<'HTML'
<div class="shop-product-page">
    [shop-breadcrumbs]
    <article class="shop-product">
        <p>[shop-summary]</p>
        [shop-gallery]
        <div>[shop-description]</div>
        [shop-characteristics]
        [shop-variants]
    </article>
</div>
HTML
        ],
        'shop-cart'=>['basket','cart-shopping','[shop-cart]'],
        'shop-checkout'=>['checkout','receipt','[shop-checkout]'],
    ];
}

function admin_marketplace_page_template(string $id): array
{
    if (!isset(admin_marketplace_page_templates()[$id])) return [];
    $template = site_read_record(SITE_PAGE_TEMPLATES_FILE, SITE_PAGE_TEMPLATES_LIMIT)[$id] ?? [];
    return is_array($template) ? $template : [];
}

function admin_marketplace_product_markup(string $html): string
{
    $html = shop_migrate_template($html);
    $stock = admin_marketplace_page_templates()['shop-product'][2];
    $compact = static function (string $markup): string {
        return preg_replace('/\s+(?=<|\[shop-)/', '', trim($markup));
    };
    return $compact($html) === $compact($stock) ? $stock : $html;
}

function admin_marketplace_product_default(array $settings): string
{
    return isset($settings['templates']['product'])
        ? "<div class=\"shop-product-page\">\n    [shop-breadcrumbs]\n" . $settings['templates']['product'] . "\n</div>"
        : admin_marketplace_page_templates()['shop-product'][2];
}

function admin_page_templates(string $language, bool $marketplace = false): array
{
    $labels = admin_page_template_labels($language);
    $templates = [];
    foreach ($marketplace ? [] : admin_stock_page_templates() as list($id, $index, $icon, $html)) {
        $templates[$id] = [
            'name' => $labels[$index], 'icon' => $icon, 'html' => $html,
            'icon_code' => '[fa-icon=' . $icon . ']',
            'Title' => $labels[$index] . ' | [site-name]', 'Heading' => $labels[$index], 'Description' => '',
            'assets' => ['CSS' => '', 'JS' => ''],
        ];
    }
    if ($marketplace && site_shop_widgets_enabled()) {
        require_once site_shop_directory() . '/module.php';
        foreach (admin_marketplace_page_templates() as $id => [$key, $icon, $html]) {
            if ($id === 'shop-product') $html = admin_marketplace_product_default(shop_settings());
            $name = shop_label($key, $language);
            $templates[$id] = ['name'=>$name,'icon'=>$icon,'html'=>$html,'icon_code'=>'[fa-icon=' . $icon . ']',
                'Title'=>$name . ' | [site-name]','Heading'=>$name,'Description'=>'','assets'=>['CSS'=>'','JS'=>'']];
        }
    }
    $saved = site_read_record(SITE_PAGE_TEMPLATES_FILE, SITE_PAGE_TEMPLATES_LIMIT);
    foreach ($saved as $id => $record) {
        if (!is_string($id) || !is_array($record)
            || (!isset($templates[$id]) && ($marketplace || preg_match('/\Acustom-[a-f0-9]{16}\z/', $id) !== 1))) continue;
        foreach (['name', 'html', 'Title', 'Heading', 'Description'] as $field) {
            if (!is_string($record[$field] ?? null)) continue 2;
        }
        $assets = [];
        foreach (['CSS', 'JS'] as $kind) {
            $value = $record['assets'][$kind] ?? '';
            $assets[$kind] = is_string($value) ? $value : '';
        }
        $code = $record['icon_code'] ?? ($templates[$id]['icon_code'] ?? '');
        $templates[$id] = ['icon' => $templates[$id]['icon'] ?? 'file-code', 'assets' => $assets,
            'icon_code' => is_string($code) ? $code : ''] + $record;
        if ($id === 'shop-product') $templates[$id]['html'] = admin_marketplace_product_markup($record['html']);
    }
    return $templates;
}

function admin_page_template_icon(array $template, string $base): string
{
    $icon = site_parse_page_icon($template['icon_code'] ?? '');
    if ($icon !== null && $icon[0] === 'image') {
        return '<img src="' . site_escape(site_url($base, 'templates/icons/' . $icon[1])) . '" width="16" height="16" alt="">';
    }
    list($style, $name) = $icon ?? ['solid', $template['icon']];
    return '<i class="fa-' . site_escape($style) . ' fa-' . site_escape($name) . '" aria-hidden="true"></i>';
}

function admin_page_template_draft(array $input): array
{
    $draft = [];
    foreach (['name', 'Title', 'Heading', 'Description'] as $field) {
        $draft[$field] = trim(admin_input($input, $field));
    }
    $draft['html'] = admin_input($input, 'html');
    $draft['icon_code'] = trim(admin_input($input, 'icon_code'));
    foreach (['CSS', 'JS'] as $kind) {
        $draft['assets'][$kind] = is_array($input['assets'] ?? null) ? admin_input($input['assets'], $kind) : '';
    }
    return $draft;
}

function admin_save_page_template(string $id, array $draft, string $revision, bool $marketplace = false)
{
    $metadata = $draft;
    unset($metadata['html'], $metadata['assets']);
    foreach ($metadata as $text) {
        if (strlen($text) > 1500 || preg_match('/\A[^\p{Cc}]*\z/u', $text) !== 1) {
            throw new RuntimeException('Проверьте длину и формат полей настроек.');
        }
    }
    if ($draft['name'] === '' || $draft['Title'] === '' || strlen($draft['name']) > 480
        || (($draft['icon_code'] ?? '') !== '' && site_parse_page_icon($draft['icon_code']) === null)
        || strlen($draft['html']) > 262144 || strpos($draft['html'], "\0") !== false
        || preg_match('//u', $draft['html']) !== 1 || preg_match('/<\?(?:php|=)?/i', $draft['html'])) {
        throw new RuntimeException('Проверьте длину и формат полей настроек.');
    }
    foreach ($draft['assets'] as $code) admin_encode_asset($code);
    $lock = admin_lock();
    try {
        $templates = site_read_record(SITE_PAGE_TEMPLATES_FILE, SITE_PAGE_TEMPLATES_LIMIT);
        if (!hash_equals(admin_settings_revision($templates), $revision)) {
            throw new RuntimeException('Настройки изменились. Скопируйте правки и откройте раздел заново.');
        }
        if ($marketplace && (!site_shop_widgets_enabled() || !isset(admin_marketplace_page_templates()[$id]))) {
            throw new RuntimeException('Страница не найдена.');
        }
        if (!$marketplace && $id !== 'new' && !isset(admin_stock_page_templates()[$id])
            && (preg_match('/\Acustom-[a-f0-9]{16}\z/', $id) !== 1 || !isset($templates[$id]))) {
            throw new RuntimeException('Страница не найдена.');
        }
        $id = $id === 'new' ? 'custom-' . bin2hex(random_bytes(8)) : $id;
        if ($marketplace && $id === 'shop-product') {
            require_once site_shop_directory() . '/module.php';
            $draft['html'] = admin_marketplace_product_markup($draft['html']);
        }
        $templates[$id] = $draft;
        admin_write_record(SITE_PAGE_TEMPLATES_FILE, $templates, SITE_PAGE_TEMPLATES_LIMIT);
        return $id;
    } finally {
        fclose($lock);
    }
}
