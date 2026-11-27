<?php
if (!defined('SITE_ROOT')) { http_response_code(404); exit; }
$shopView = shop_input($_GET, 'tab');
require_once __DIR__ . '/admin-records.php';
$shopState = shop_settings();
$shopStandaloneEditor = !empty($focusedShop);
if ($shopStandaloneEditor && $shopEditor === 'fragment') {
    $key = shop_input($_GET, 'fragment');
    $defaults = shop_template_fragments();
    if (!isset($defaults[$key])) {
        echo '<p class="notice error" role="alert">' . site_escape(shop_label('not_found')) . '</p>';
        return;
    }
    $current = shop_fragment_source($key, $shopState, true);
    $draft = $error === '' ? $current : shop_input($_POST, 'markup');
    preg_match_all('/\[shop-[a-z-]+\]/', $defaults[$key], $shortcodes);
    $shortcodeLabels = ['url'=>'template_token_url','visual'=>'template_token_visual','name'=>'product_name','children'=>'template_token_children',
        'photo'=>'product_image','summary'=>'summary','variants'=>'variants','label'=>'template_token_label','count'=>'template_token_count',
        'body'=>'template_token_content','title'=>'product_name','home'=>'home','basket'=>'basket','account'=>'account',
        'notice'=>'template_token_notice','content'=>'template_token_content','links'=>'template_token_links',
        'gallery'=>'images','model'=>'door_model','sku'=>'product_sku','size'=>'door_size','opening'=>'door_opening',
        'price'=>'price','old-price'=>'old_price','discount'=>'door_discount','buy'=>'checkout','video'=>'door_video',
        'details'=>'door_details','characteristics'=>'record_properties','stock'=>'product_stock'];
    if (in_array($key, ['item','item-list'], true)) $shortcodes[0] = array_map('shop_template_shortcode',
        ['url','name','summary','photo','gallery','model','sku','size','opening','price','old-price','discount','buy','video','details','attributes','stock','offers']);
    ?>
    <form class="form-stack" method="post" action="<?= site_escape($moduleAdminUrl . '&tab=templates&fragment=' . rawurlencode($key)) ?>" data-shop-fragment-form>
        <?php if ($error !== ''): ?><p class="notice error" role="alert"><?= site_escape($error) ?></p><?php endif; ?>
        <input type="hidden" name="action" value="shop-fragment-save">
        <input type="hidden" name="csrf" value="<?= site_escape($moduleToken) ?>">
        <input type="hidden" name="fragment" value="<?= site_escape($key) ?>">
        <input type="hidden" name="revision" value="<?= site_escape($error === '' ? hash('sha256', $current) : shop_input($_POST, 'revision')) ?>">
        <label class="field">HTML
            <textarea name="markup" data-code-mode="html" rows="14" maxlength="16000" spellcheck="false"><?= "\n" . site_escape($draft) ?></textarea>
        </label>
        <div class="shop-template-shortcodes">
            <?php foreach (array_unique($shortcodes[0]) as $shortcode): ?>
                <?php $tokenName = substr($shortcode, 6, -1); $hintId = 'shop-token-' . $tokenName; ?>
                <span class="shop-template-token">
                    <button type="button" data-shop-template-insert="<?= site_escape($shortcode) ?>" aria-describedby="<?= $hintId ?>"><code><?= site_escape($shortcode) ?></code></button>
                    <span id="<?= $hintId ?>" role="tooltip"><?= site_escape(shop_label($shortcodeLabels[$tokenName] ?? $tokenName)) ?></span>
                </span>
            <?php endforeach; ?>
        </div>
        <div><button class="button-primary" type="submit"><?= site_escape(shop_label('save')) ?></button></div>
    </form>
    <?php
    return;
}
if ($shopStandaloneEditor && in_array($shopEditor, ['product','category'], true)) {
    $catalogRevision = shop_generation();
    $tables = shop_tables($catalogRevision);
    $tabUrl = $moduleAdminUrl . '&tab=catalog';
    $rows = $tables['products'];
    require __DIR__ . ($shopEditor === 'product' ? '/admin-products-view.php' : '/admin-categories-view.php');
    return;
}
if ($shopStandaloneEditor) $shopView = 'brands';
$mayCatalog = $moduleOwner || in_array('catalog', $roles, true);
$mayOrders = $moduleOwner || in_array('orders', $roles, true);
$tabs = ($mayCatalog ? ['catalog'=>'catalog','import'=>'import','images'=>'media_library'] : [])
    + ($mayOrders ? ['orders'=>'orders'] : []) + ($moduleOwner ? ['checkout'=>'checkout_settings','export'=>'export','sales'=>'sales_report','brands'=>'brands','filters'=>'filters','integrations'=>'integrations','templates'=>'templates','settings'=>'settings'] : []);
if (!isset($tabs[$shopView])) $shopView = (string) key($tabs);
$tabUrl = $moduleAdminUrl . '&tab=' . $shopView;
$tabIcons = ['catalog'=>'boxes-stacked','import'=>'file-import','checkout'=>'truck','export'=>'file-export','images'=>'images','orders'=>'receipt','sales'=>'chart-column',
    'brands'=>'tags','filters'=>'sliders','integrations'=>'plug','templates'=>'file-code','settings'=>'gear'];
$shopForm = static function (string $action) use ($tabUrl, $moduleToken): string {
    return '<form class="panel form-stack" method="post" enctype="multipart/form-data" action="' . site_escape($tabUrl)
        . '"><input type="hidden" name="csrf" value="' . site_escape($moduleToken) . '"><input type="hidden" name="action" value="' . $action . '">';
};
$shopButton = static function (string $text): string {
    return '<div><button type="submit" class="button-primary">' . site_escape(shop_label($text)) . '</button></div></form>';
};
?>
<?php if (!$shopStandaloneEditor): ?>
<nav class="site-menu scope-menu shop-admin-nav" aria-label="<?= site_escape(shop_label('marketplace')) ?>">
    <?php foreach ($tabs as $id => $key): ?>
        <a href="<?= site_escape($moduleAdminUrl . '&tab=' . $id) ?>"<?= $shopView === $id ? ' aria-current="page"' : '' ?>><i class="fa-solid fa-<?= $tabIcons[$id] ?>" aria-hidden="true"></i> <?= site_escape(shop_label($key)) ?></a>
    <?php endforeach; ?>
</nav>
<?php endif; ?>
<div class="shop-admin-content">
<?php if (!$shopStandaloneEditor && $shopView !== 'catalog'): ?>
    <details class="shop-inline-help"><summary><i class="fa-regular fa-circle-question" aria-hidden="true"></i> <?= site_escape(shop_label('shop_help')) ?></summary>
        <p><?= site_escape(shop_label('help_tab_' . $shopView)) ?></p>
    </details>
<?php endif; ?>
<?php if ($shopView === 'templates'): ?>
    <?php require_once SITE_ROOT . '/admin/page-templates.php'; ?>
    <section class="shop-template-groups" aria-label="<?= site_escape(shop_label('templates')) ?>">
        <section class="panel shop-template-group">
            <h2><i class="fa-regular fa-file-code" aria-hidden="true"></i> <?= site_escape(shop_label('template_pages')) ?></h2>
            <ul class="page-template-list">
                <?php foreach (admin_page_templates(shop_locale(), true) as $id => $item): ?>
                    <li class="page-template-row">
                        <span class="page-template-name"><?= admin_page_template_icon($item, $base) ?> <span><?= site_escape($item['name']) ?></span></span>
                        <?= shop_live_editor_link($base, 'template', $id) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
        <section class="panel shop-template-group">
            <h2><i class="fa-solid fa-cubes" aria-hidden="true"></i> <?= site_escape(shop_label('template_parts')) ?></h2>
            <?php $fragmentIcons = ['category'=>'folder-tree','item'=>'table-cells-large','item-list'=>'list','pagination'=>'list-ol','basket'=>'cart-shopping','checkout'=>'receipt','account'=>'user','page'=>'window-maximize']; ?>
            <ul class="page-template-list">
                <?php foreach ($fragmentIcons as $id => $icon): ?>
                    <li class="page-template-row">
                        <span class="page-template-name"><i class="fa-solid fa-<?= $icon ?>" aria-hidden="true"></i> <span><?= site_escape(shop_label('template_' . $id)) ?></span></span>
                        <?= shop_live_editor_link($base, 'fragment', $id) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
    </section>
    <?php
    $componentDataId = 'page-shop-editor';
    $editor = ['language'=>shop_locale()];
    $editSettingsText = shop_label('templates');
    $editText = static function (string $text) use ($editor): string { return site_escape(site_translation($text, $editor['language'])); };
    require SITE_ROOT . '/templates/page-component-data.php';
    unset($componentDataId);
    ?>
<?php elseif ($shopView === 'export'): ?>
    <?php require __DIR__ . '/export-view.php'; ?>
<?php elseif ($shopView === 'checkout'): ?>
    <?php require __DIR__ . '/checkout-view.php'; ?>
<?php elseif ($shopView === 'brands'): ?>
    <?php
    $profiles = shop_brand_profiles();
    $brandNames = [];
    foreach (shop_index()['brands'] as $id => $names) $brandNames[$id] = (string) key($names);
    foreach ($profiles as $id => $profile) $brandNames[$id] = $profile['name'];
    asort($brandNames, SORT_NATURAL | SORT_FLAG_CASE);
    $brandId = shop_input($_GET, 'brand');
    if ($brandId !== '_new' && !isset($brandNames[$brandId])) $brandId = (string) key($brandNames);
    $profile = $profiles[$brandId] ?? [];
    $brandDraft = $profile + ['name'=>$brandNames[$brandId] ?? '','description'=>'','website'=>'','logo'=>''];
    if (($error ?? '') !== '' && shop_input($_POST, 'action') === 'shop-brand-save') {
        foreach (array_keys($brandDraft) as $field) $brandDraft[$field] = shop_input($_POST, $field);
    }
    ?>
    <?php if (!$shopStandaloneEditor): ?>
    <div class="admin-heading">
        <h2><?= site_escape(shop_label('brands')) ?></h2>
        <a class="button button-primary" href="<?= site_escape($tabUrl . '&brand=_new') ?>"><i class="fa-solid fa-plus" aria-hidden="true"></i> <?= site_escape(shop_label('brand_add')) ?></a>
    </div>
    <?php if (count($brandNames) > 8): ?>
        <form method="get" action="<?= site_escape(strtok($moduleAdminUrl, '?')) ?>" class="shop-compact-search">
            <input type="hidden" name="shop" value="1"><input type="hidden" name="tab" value="brands">
            <label class="field"><?= site_escape(shop_label('brand')) ?><select name="brand">
                <?php foreach ($brandNames as $id => $name): ?><option value="<?= site_escape((string) $id) ?>"<?= (string) $id === $brandId ? ' selected' : '' ?>><?= site_escape($name) ?></option><?php endforeach; ?>
            </select></label>
            <button type="submit"><?= site_escape(shop_label('record_edit')) ?></button>
        </form>
    <?php elseif ($brandNames): ?>
        <nav class="site-menu scope-menu" aria-label="<?= site_escape(shop_label('brands')) ?>">
            <?php foreach ($brandNames as $id => $name): ?><a href="<?= site_escape($tabUrl . '&brand=' . rawurlencode((string) $id)) ?>"
                <?= (string) $id === $brandId ? ' aria-current="page"' : '' ?>><?= site_escape($name) ?></a><?php endforeach; ?>
        </nav>
    <?php endif; ?>
    <?php endif; ?>
    <?php if ($shopStandaloneEditor && $error !== ''): ?><p class="notice error" role="alert"><?= site_escape($error) ?></p><?php endif; ?>
    <form class="panel form-stack" method="post" enctype="multipart/form-data" data-shop-brand-form data-server-error="<?= site_escape($error ?? '') ?>"
        action="<?= site_escape($tabUrl . '&brand=' . rawurlencode($brandId ?: '_new')) ?>">
        <input type="hidden" name="csrf" value="<?= site_escape($moduleToken) ?>">
        <input type="hidden" name="action" value="shop-brand-save">
        <input type="hidden" name="brand_id" value="<?= site_escape($brandId === '_new' ? '' : $brandId) ?>">
        <input type="hidden" name="revision" value="<?= hash('sha256', serialize($profile)) ?>">
        <div class="fields">
            <label class="field"><?= site_escape(shop_label('product_name')) ?><input name="name" required maxlength="180" value="<?= site_escape($brandDraft['name']) ?>"></label>
            <label class="field"><?= site_escape(shop_label('brand_website')) ?><input name="website" type="url" maxlength="1000" placeholder="https://" value="<?= site_escape($brandDraft['website']) ?>"></label>
            <label class="field field-wide"><?= site_escape(shop_label('description')) ?><textarea name="description" rows="5" maxlength="5000"><?= "\n" . site_escape($brandDraft['description']) ?></textarea></label>
            <label class="field"><?= site_escape(shop_label('brand_logo')) ?><input name="logo" list="shop-brand-images" value="<?= site_escape($brandDraft['logo']) ?>"></label>
            <datalist id="shop-brand-images"><?php foreach (glob(SHOP_IMAGES_DIR . '/*') ?: [] as $file): if (shop_image_name(basename($file))): ?><option value="<?= site_escape(basename($file)) ?>"></option><?php endif; endforeach; ?></datalist>
            <label class="field"><?= site_escape(shop_label('brand_upload')) ?><input name="photo" type="file" accept="image/jpeg,image/png,image/webp,image/gif,image/avif"></label>
        </div>
        <?php if ($brandDraft['logo'] !== '' && shop_image_valid($brandDraft['logo'])): ?><div class="shop-brand-preview"><?= shop_image($base, $brandDraft['logo'], $brandDraft['name']) ?></div><?php endif; ?>
        <div><button class="button-primary" type="submit"><?= site_escape(shop_label('save')) ?></button></div>
    </form>
<?php elseif ($shopView === 'filters'): ?>
    <?php $filterSettings = shop_filter_settings($shopState); ?>
    <?= $shopForm('shop-filter-settings') ?>
    <h2><?= site_escape(shop_label('filters')) ?></h2>
    <label class="field"><?= site_escape(shop_label('filter_position')) ?><select name="position">
        <?php foreach (['off','top','left'] as $position): ?>
            <option value="<?= $position ?>"<?= $filterSettings['position'] === $position ? ' selected' : '' ?>><?= site_escape(shop_label('filter_' . $position)) ?></option>
        <?php endforeach; ?>
    </select></label>
    <div class="shop-filter-settings-grid">
        <?php foreach (['price'=>'tag','fields'=>'list-check','search'=>'magnifying-glass','sort'=>'arrow-down-wide-short','live'=>'bolt'] as $key => $icon): ?>
            <label class="choice"><input type="checkbox" name="<?= $key ?>" value="1"<?= $filterSettings[$key] ? ' checked' : '' ?>>
                <i class="fa-solid fa-<?= $icon ?>" aria-hidden="true"></i> <?= site_escape(shop_label('filter_' . $key)) ?></label>
        <?php endforeach; ?>
    </div>
    <?= $shopButton('save') ?>
<?php elseif ($shopView === 'settings'): ?>
    <?= $shopForm('shop-settings') ?>
    <h2><?= site_escape(shop_label('settings')) ?></h2>
    <div class="shop-settings-grid">
    <fieldset class="shop-settings-group form-stack">
    <legend><i class="fa-solid fa-store" aria-hidden="true"></i> <?= site_escape(shop_label('shop_settings_catalog')) ?></legend>
    <div class="shop-settings-fields">
    <label class="field"><?= site_escape(shop_label('catalog_folder')) ?>
        <input name="catalog_folder" value="/<?= site_escape(site_shop_public_folder()) ?>/" maxlength="230" placeholder="/catalog/">
    </label>
    <label class="field"><?= site_escape(shop_label('catalog_page')) ?>
        <select name="catalog"><option value="">/<?= site_escape(site_shop_public_folder()) ?>/</option>
            <?php foreach (site_pages() as $candidate): ?>
                <option value="<?= site_escape($candidate['path']) ?>"<?= $shopState['catalog'] === $candidate['path'] ? ' selected' : '' ?>><?= site_escape($candidate['fields']['Title'] . ' · ' . site_page_url($base, $candidate['path'])) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    </div>
    <label class="field"><?= site_escape(shop_label('page_size')) ?><input type="number" name="pageSize" min="1" max="96" value="<?= $shopState['pageSize'] ?>"></label>
    <label class="choice shop-settings-choice">
        <input type="hidden" name="short_product_urls" value="0">
        <input type="checkbox" name="short_product_urls" value="1"<?= site_shop_short_product_urls() ? ' checked' : '' ?>>
        <span><?= site_escape(shop_label('short_product_urls')) ?><small><code><?= site_escape(site_url($base, 'product/')) ?>…/</code></small></span>
    </label>
    </fieldset>
    <fieldset class="shop-settings-group form-stack">
    <legend><i class="fa-solid fa-tag" aria-hidden="true"></i> <?= site_escape(shop_label('shop_settings_prices')) ?></legend>
    <div class="shop-settings-fields">
    <label class="field"><?= site_escape(shop_label('currency')) ?><select name="currency">
        <?php foreach (['RUB','USD','EUR','GBP','UAH','BYN','PLN','KZT','CNY'] as $currency): ?>
            <option<?= $currency === $shopState['currency'] ? ' selected' : '' ?>><?= $currency ?></option>
        <?php endforeach; ?></select></label>
    <label class="field"><?= site_escape(shop_label('price_rounding')) ?>
        <select name="rounding">
            <?php foreach (['100'=>'hundreds','10'=>'tens','1'=>'units','0.1'=>'tenths','0.01'=>'hundredths'] as $value => $label): ?>
                <option value="<?= $value ?>"<?= (string) $value === $shopState['rounding'] ? ' selected' : '' ?>><?= $value ?> · <?= site_escape(shop_label('round_' . $label)) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    </div>
    <label class="field"><?= site_escape(shop_label('price_labels')) ?><textarea name="priceLabels" rows="2"><?php
        foreach ($shopState['priceLabels'] ?? [] as $id => $label) echo site_escape($id . '=' . $label) . "\n";
    ?></textarea></label>
    </fieldset>
    <fieldset class="shop-settings-group shop-settings-checkout">
        <legend><i class="fa-solid fa-truck" aria-hidden="true"></i> <?= site_escape(shop_label('checkout_settings')) ?></legend>
        <div class="header-actions">
            <label class="choice"><input type="checkbox" name="guest" value="1"<?= $shopState['guest'] ? ' checked' : '' ?>> <?= site_escape(shop_label('guest')) ?></label>
            <a class="button" href="<?= site_escape($moduleAdminUrl . '&tab=checkout') ?>"><i class="fa-solid fa-arrow-right" aria-hidden="true"></i> <?= site_escape(shop_label('checkout_settings')) ?></a>
        </div>
    </fieldset>
    </div>
    <?php if ($moduleOwner): ?><p><a href="<?= site_escape($mailTemplatesUrl) ?>"><?= site_escape(admin_mail_labels($adminLanguage)['title']) ?></a></p><?php endif; ?>
    <?= $shopButton('save') ?>
<?php elseif ($shopView === 'catalog'): ?>
    <?php
    $table = shop_input($_GET, 'sheet');
    $sheets = array_diff_key(SHOP_COLUMNS, array_flip($moduleOwner ? ['inventory','barcodes','product_seo','category_media','offer_media'] : ['warehouses','inventory','barcodes','product_seo','category_media','offer_media']));
    if (!isset($sheets[$table])) $table = 'products';
    $catalogRevision = shop_generation();
    $tables = shop_tables($catalogRevision);
    $rows = $tables[$table];
    $sheetNames = array_combine(array_keys($sheets), array_keys($sheets));
    $sheetNames = ['offers'=>'variants','fields'=>'record_properties','images'=>'record_gallery','prices'=>'record_extra_prices',
        'aliases'=>'record_redirects','values'=>'record_property_values'] + $sheetNames;
    $primary = array_flip(['products','categories','offers','fields','warehouses']);
    ?>
    <div class="shop-catalog-nav">
        <nav class="site-menu scope-menu" aria-label="<?= site_escape(shop_label('catalog')) ?>">
            <?php foreach (array_intersect_key($sheets, $primary) as $key=>$cols): ?><a href="<?= site_escape($tabUrl . '&sheet=' . $key) ?>"<?= $table === $key ? ' aria-current="page"' : '' ?>><?= site_escape(shop_label($sheetNames[$key])) ?></a><?php endforeach; ?>
        </nav>
        <details<?= isset($primary[$table]) ? '' : ' open' ?>><summary><?= site_escape(shop_label('record_more')) ?></summary>
            <nav class="site-menu scope-menu" aria-label="<?= site_escape(shop_label('record_more')) ?>">
                <?php foreach (array_diff_key($sheets, $primary) as $key=>$cols): ?><a href="<?= site_escape($tabUrl . '&sheet=' . $key) ?>"<?= $table === $key ? ' aria-current="page"' : '' ?>><?= site_escape(shop_label($sheetNames[$key])) ?></a><?php endforeach; ?>
            </nav>
        </details>
    </div>
    <?php if (isset($primary[$table]) && !in_array($table, ['offers','fields'], true)): ?>
        <details class="shop-inline-help"><summary><i class="fa-regular fa-circle-question" aria-hidden="true"></i> <?= site_escape(shop_label('shop_help')) ?></summary>
            <p><?= site_escape(shop_label('help_' . $table)) ?></p>
        </details>
    <?php endif; ?>
    <?php if ($moduleOwner && $table === 'products'): ?>
        <?php require __DIR__ . '/admin-products-view.php'; ?>
    <?php elseif ($moduleOwner && $table === 'categories'): ?>
        <?php require __DIR__ . '/admin-categories-view.php'; ?>
    <?php elseif ($table === 'warehouses'): ?>
        <section class="panel form-stack shop-module-panel">
            <h2><?= site_escape(shop_label('warehouses')) ?> <span class="count-badge"><?= count($rows) ?></span></h2>
            <?php if ($rows):
                $warehouseTotals = [];
                foreach ($tables['inventory'] as $entry) $warehouseTotals[$entry['warehouse_id']] = ($warehouseTotals[$entry['warehouse_id']] ?? 0) + (int) $entry['quantity'];
                ?>
                <div class="shop-report-table"><table class="shop-record-table"><thead><tr>
                    <th scope="col"><?= site_escape(shop_label('warehouse_name')) ?></th><th scope="col"><?= site_escape(shop_label('product_stock')) ?></th>
                </tr></thead><tbody>
                    <?php foreach ($rows as $warehouse): ?><tr><td><?= site_escape($warehouse['name']) ?></td><td><?= $warehouseTotals[$warehouse['id']] ?? 0 ?></td></tr><?php endforeach; ?>
                </tbody></table></div>
            <?php endif; ?>
            <form method="post" class="form-stack" action="<?= site_escape($tabUrl . '&sheet=warehouses') ?>">
                <input type="hidden" name="csrf" value="<?= site_escape($moduleToken) ?>">
                <input type="hidden" name="action" value="shop-warehouse-add">
                <input type="hidden" name="revision" value="<?= site_escape($catalogRevision) ?>">
                <label class="field"><?= site_escape(shop_label('warehouse_name')) ?><input name="warehouse" maxlength="240" required></label>
                <div><button class="button-primary" type="submit"><?= site_escape(shop_label('warehouse_add')) ?></button></div>
            </form>
        </section>
    <?php else: ?>
    <?php require __DIR__ . '/admin-records-view.php'; ?>
    <details class="panel shop-maintenance"<?= ($error ?? '') !== '' && shop_input($_POST, 'action') === 'shop-sheet' ? ' open' : '' ?>>
    <summary><i class="fa-solid fa-file-csv" aria-hidden="true"></i> <?= site_escape(shop_label('record_csv')) ?></summary>
    <?= $shopForm('shop-sheet') ?>
    <input type="hidden" name="sheet" value="<?= site_escape($table) ?>">
    <input type="hidden" name="revision" value="<?= site_escape($catalogRevision) ?>">
    <label class="field"><?= site_escape(shop_label('sheet_help')) ?>
        <textarea name="csv" rows="12" spellcheck="false" required><?= site_escape(($error ?? '') !== '' && shop_input($_POST, 'action') === 'shop-sheet' ? shop_input($_POST, 'csv') : shop_csv(array_merge([SHOP_COLUMNS[$table]], $rows))) ?></textarea>
    </label>
    <?= $shopButton('save') ?>
    </details>
    <?php endif; ?>
    <details class="panel shop-maintenance"><summary><?= site_escape(shop_label('reindex')) ?></summary>
        <?= $shopForm('shop-reindex') . $shopButton('reindex') ?>
    </details>
<?php elseif ($shopView === 'import'): ?>
    <?php
    require_once __DIR__ . '/import.php';
    $columnTitles = ['sku'=>'product_sku','name'=>'product_name','price'=>'price','price_minor'=>'import_minor','stock'=>'product_stock',
        'barcode'=>'product_barcode','old_price'=>'old_price','brand'=>'brand','variant'=>'product_variant','category'=>'categories',
        'description'=>'description','summary'=>'summary','image'=>'product_image','currency'=>'currency','group'=>'products','active'=>'status','external'=>'source'];
    $mappingOptions = [];
    foreach (shop_import_column_aliases() as $field => $aliases) $mappingOptions[$field] = ['label'=>shop_label($columnTitles[$field]),'aliases'=>$aliases];
    ?>
    <?= $shopForm('shop-import') ?>
    <h2><?= site_escape(shop_label('import')) ?></h2>
    <p class="hint"><?= site_escape(shop_label('import_help')) ?></p>
    <div class="shop-import-fields">
    <label class="field"><?= site_escape(shop_label('source')) ?><input name="supplier" required maxlength="120"></label>
    <label class="field"><?= site_escape(shop_label('format')) ?><select name="format" data-shop-import-format><option value="csv">CSV</option><option value="yml">YML</option></select></label>
    <label class="field" data-shop-csv-separator><?= site_escape(shop_label('separator')) ?><select name="separator"><option value="pipe">|</option><option value="semicolon">;</option><option value="comma">,</option><option value="tab">TAB</option></select></label>
    <label class="field">CSV / YML<input type="file" name="catalog_file" accept=".csv,.yml,.xml,text/csv,text/xml" required></label>
    </div>
    <div data-shop-import-columns class="shop-record-fields" hidden
        data-columns="<?= site_escape(json_encode($mappingOptions, JSON_UNESCAPED_UNICODE)) ?>"
        data-auto="<?= site_escape(shop_label('import_auto')) ?>"></div>
    <details><summary><?= site_escape(shop_label('mapping')) ?></summary>
        <textarea name="mapping" rows="5" aria-label="<?= site_escape(shop_label('mapping')) ?>" placeholder="sku=Артикул&#10;name=Название&#10;price=Цена&#10;field:power=Мощность"></textarea>
    </details>
    <label class="choice"><input type="checkbox" name="descriptions" value="1"> <?= site_escape(shop_label('replace_descriptions')) ?></label>
    <?= $shopButton('preview') ?>
    <?php if (isset($_SESSION['shopImportPreview']['report'])): ?>
        <?php $report = $_SESSION['shopImportPreview']['report']; ?>
        <?= $shopForm('shop-apply') ?>
        <h2><?= site_escape(shop_label('preview')) ?></h2>
        <dl class="shop-report-totals">
            <?php foreach (['new','updated','unchanged'] as $status): ?>
                <div><dt><?= site_escape(shop_label($status === 'unchanged' ? 'import_unchanged' : $status)) ?></dt><dd><?= (int) ($report[$status] ?? 0) ?></dd></div>
            <?php endforeach; ?>
        </dl>
        <?php if (!empty($report['mapping'])): ?><details><summary><?= site_escape(shop_label('import_match')) ?></summary>
            <dl class="shop-order-contact">
                <?php foreach ($report['mapping'] as $field => $source): ?>
                    <div><dt><?= site_escape(shop_label($columnTitles[$field])) ?></dt><dd><?= site_escape($source) ?></dd></div>
                <?php endforeach; ?>
            </dl>
        </details><?php endif; ?>
        <?php if (!empty($report['sample'])): ?>
            <?php if ($report['new'] + $report['updated'] > count($report['sample'])): ?><p class="hint"><?= site_escape(shop_label('import_sample')) ?></p><?php endif; ?>
            <div class="shop-report-table"><table><thead><tr>
                <?php foreach (['product_sku','product_name','status','price','product_stock'] as $key): ?><th scope="col"><?= site_escape(shop_label($key)) ?></th><?php endforeach; ?>
            </tr></thead><tbody>
                <?php foreach ($report['sample'] as $entry): ?><tr>
                    <td><?= site_escape($entry['sku']) ?></td><td><?= site_escape($entry['name']) ?></td><td><?= site_escape(shop_label($entry['status'])) ?></td>
                    <td><?= site_escape(implode(' → ', array_map(static function (?string $value) use ($shopState): string {
                        return $value === null ? '—' : shop_money((int) $value, $shopState);
                    }, $entry['price']))) ?></td>
                    <td><?= site_escape(($entry['stock'][0] ?? '—') . ' → ' . $entry['stock'][1]) ?></td>
                </tr><?php endforeach; ?>
            </tbody></table></div>
        <?php endif; ?>
        <?= $shopButton('apply') ?>
    <?php endif; ?>
<?php elseif ($shopView === 'images'): ?>
    <?php $photos = array_values(array_filter(glob(SHOP_IMAGES_DIR . '/*') ?: [], static function (string $file): bool {
        return is_file($file) && shop_image_name(basename($file));
    }));
    $photoCount = count($photos);
    $photoQuery = site_search_query($_GET['q'] ?? '');
    if ($photoQuery !== '') $photos = array_values(array_filter($photos, static function (string $file) use ($photoQuery): bool {
        return stripos(basename($file), $photoQuery) !== false;
    }));
    $photoListUrl = $tabUrl . ($photoQuery === '' ? '' : '&q=' . rawurlencode($photoQuery));
    $photoPages = max(1, (int) ceil(count($photos) / 24));
    $photoPage = min($photoPages, max(1, (int) shop_input($_GET, 'p'))); ?>
    <div class="admin-heading"><h2><?= site_escape(shop_label('media_library')) ?> <span class="count-badge"><?= count($photos) ?><?= count($photos) === $photoCount ? '' : ' / ' . $photoCount ?></span></h2>
        <?php if ($moduleOwner): ?><button type="button" class="button-primary" data-shop-choose-image><i class="fa-regular fa-folder-open" aria-hidden="true"></i> <?= site_escape(shop_label('media_pick')) ?></button><?php endif; ?>
    </div>
    <details class="panel shop-maintenance"><summary><?= site_escape(shop_label('upload')) ?></summary>
    <?= $shopForm('shop-photo') ?>
    <label class="field"><?= site_escape(shop_label('upload')) ?><input type="file" name="photo" accept="image/jpeg,image/png,image/webp,image/gif,image/avif" required></label>
    <?= $shopButton('upload') ?>
    </details>
    <form method="get" action="<?= site_escape(strtok($moduleAdminUrl, '?')) ?>" class="shop-compact-search">
        <input type="hidden" name="<?= $moduleOwner ? 'shop' : 'staff' ?>" value="1"><input type="hidden" name="tab" value="images">
        <label class="field"><?= site_escape(shop_label('media_library')) ?><input type="search" name="q" value="<?= site_escape($photoQuery) ?>"></label>
        <button type="submit"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i> <?= site_escape(shop_label('record_find')) ?></button>
        <?php if ($photoQuery !== ''): ?><a href="<?= site_escape($tabUrl) ?>"><?= site_escape(shop_label('filter_reset')) ?></a><?php endif; ?>
    </form>
    <?php if (!$photos): ?><p class="shop-empty"><?= site_escape(shop_label('record_empty')) ?></p><?php endif; ?>
    <?php if (isset($_SESSION['shopUploaded'])): ?><p><code><?= site_escape($_SESSION['shopUploaded']) ?></code></p><?php endif; ?>
    <div class="shop-admin-media" data-shop-lightbox-group>
        <?php foreach (array_slice($photos, ($photoPage - 1) * 24, 24) as $file): ?>
            <figure class="panel"><a href="<?= site_escape(shop_image_url($base, basename($file))) ?>" data-shop-media-item>
                <?= shop_image($base, basename($file), basename($file)) ?></a><figcaption><code><?= site_escape(basename($file)) ?></code></figcaption></figure>
        <?php endforeach; ?>
    </div>
    <?php if ($moduleOwner && $photos): ?>
        <dialog class="editor-dialog shop-media-lightbox" data-shop-media-dialog aria-label="<?= site_escape(shop_label('media_library')) ?>">
            <div class="header-actions editor-dialog-heading">
                <strong data-shop-media-caption></strong>
                <button type="button" data-shop-media-close aria-label="<?= site_escape(shop_label('product_close')) ?>" title="<?= site_escape(shop_label('product_close')) ?>">
                    <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                </button>
            </div>
            <img alt="">
            <div class="shop-media-lightbox-nav">
                <button type="button" data-shop-media-step="-1" aria-label="<?= site_escape(shop_label('gallery_prev')) ?>"><i class="fa-solid fa-chevron-left" aria-hidden="true"></i></button>
                <span data-shop-media-count aria-live="polite"></span>
                <button type="button" data-shop-media-step="1" aria-label="<?= site_escape(shop_label('gallery_next')) ?>"><i class="fa-solid fa-chevron-right" aria-hidden="true"></i></button>
            </div>
        </dialog>
    <?php endif; ?>
    <?php if ($photoPages > 1): ?><nav class="shop-order-pages" aria-label="<?= site_escape(shop_label('images')) ?>">
        <?php if ($photoPage > 1): ?><a class="button" href="<?= site_escape($photoListUrl . '&p=' . ($photoPage - 1)) ?>"><?= site_escape(shop_label('gallery_prev')) ?></a><?php endif; ?>
        <span aria-current="page"><?= $photoPage ?> / <?= $photoPages ?></span>
        <?php if ($photoPage < $photoPages): ?><a class="button" href="<?= site_escape($photoListUrl . '&p=' . ($photoPage + 1)) ?>"><?= site_escape(shop_label('gallery_next')) ?></a><?php endif; ?>
    </nav><?php endif; ?>
<?php elseif ($shopView === 'orders'): ?>
    <?php if (is_file(__DIR__ . '/admin-orders-view.php')) require __DIR__ . '/admin-orders-view.php'; ?>
<?php elseif ($shopView === 'sales'): ?>
    <?php if (is_file(__DIR__ . '/admin-sales-view.php')) require __DIR__ . '/admin-sales-view.php'; ?>
<?php elseif ($shopView === 'integrations'): ?>
    <?php if (is_file(__DIR__ . '/admin-integrations-view.php')) require __DIR__ . '/admin-integrations-view.php'; ?>
<?php endif; ?>
</div>
<?php if ($moduleOwner): ?>
    <dialog class="editor-dialog shop-library-dialog" data-shop-library
        data-admin-url="<?= site_escape(site_url($base, 'admin/')) ?>" data-base="<?= site_escape($base) ?>" data-csrf="<?= site_escape($moduleToken) ?>"
        aria-labelledby="shop-library-title">
        <div class="editor-dialog-heading header-actions">
            <h2 id="shop-library-title"><?= site_escape(shop_label('media_pick')) ?></h2>
            <button type="button" data-shop-library-close aria-label="<?= site_escape(shop_label('product_close')) ?>">×</button>
        </div>
        <?php
        $shopLibraryRoot = true;
        $imageLabel = static function (string $ru, string $en): string { return site_escape(admin_localized_label($ru, $en)); };
        require SITE_ROOT . '/admin/image-library-view.php';
        ?>
        <button type="button" class="button-primary" data-shop-library-use disabled><?= site_escape(shop_label('media_use')) ?></button>
    </dialog>
<?php endif; ?>
