<?php
if (!defined('SITE_ROOT')) { http_response_code(404); exit; }
$shopView = shop_input($_GET, 'tab');
$shopState = shop_settings();
$mayCatalog = $moduleOwner || in_array('catalog', $roles, true);
$mayOrders = $moduleOwner || in_array('orders', $roles, true);
$tabs = ($mayCatalog ? ['catalog'=>'catalog','import'=>'import','images'=>'images'] : [])
    + ($mayOrders ? ['orders'=>'orders'] : []) + ($moduleOwner ? ['sales'=>'sales_report','brands'=>'brands','filters'=>'filters','integrations'=>'integrations','settings'=>'settings'] : []);
if (!isset($tabs[$shopView])) $shopView = (string) key($tabs);
$tabUrl = $moduleAdminUrl . '&tab=' . $shopView;
$shopForm = static function (string $action) use ($tabUrl, $moduleToken): string {
    return '<form class="panel form-stack" method="post" enctype="multipart/form-data" action="' . site_escape($tabUrl)
        . '"><input type="hidden" name="csrf" value="' . site_escape($moduleToken) . '"><input type="hidden" name="action" value="' . $action . '">';
};
$shopButton = static function (string $text): string {
    return '<div><button type="submit" class="button-primary">' . site_escape(shop_label($text)) . '</button></div></form>';
};
?>
<nav class="site-menu scope-menu" aria-label="<?= site_escape(shop_label('marketplace')) ?>">
    <?php foreach ($tabs as $id => $key): ?>
        <a href="<?= site_escape($moduleAdminUrl . '&tab=' . $id) ?>"<?= $shopView === $id ? ' aria-current="page"' : '' ?>><?= site_escape(shop_label($key)) ?></a>
    <?php endforeach; ?>
</nav>
<div class="shop-admin-content">
<?php if ($shopView === 'brands'): ?>
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
    <div class="admin-heading">
        <h2><?= site_escape(shop_label('brands')) ?></h2>
        <a class="button button-primary" href="<?= site_escape($tabUrl . '&brand=_new') ?>"><i class="fa-solid fa-plus" aria-hidden="true"></i> <?= site_escape(shop_label('brand_add')) ?></a>
    </div>
    <?php if ($brandNames): ?>
        <nav class="site-menu scope-menu" aria-label="<?= site_escape(shop_label('brands')) ?>">
            <?php foreach ($brandNames as $id => $name): ?><a href="<?= site_escape($tabUrl . '&brand=' . rawurlencode((string) $id)) ?>"
                <?= (string) $id === $brandId ? ' aria-current="page"' : '' ?>><?= site_escape($name) ?></a><?php endforeach; ?>
        </nav>
    <?php endif; ?>
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
    <?php foreach (['price','fields','search','sort','live'] as $key): ?>
        <label class="choice"><input type="checkbox" name="<?= $key ?>" value="1"<?= $filterSettings[$key] ? ' checked' : '' ?>>
            <?= site_escape(shop_label('filter_' . $key)) ?></label>
    <?php endforeach; ?>
    <?= $shopButton('save') ?>
<?php elseif ($shopView === 'settings'): ?>
    <?= $shopForm('shop-settings') ?>
    <h2><?= site_escape(shop_label('settings')) ?></h2>
    <label class="field"><?= site_escape(shop_label('catalog_page')) ?>
        <select name="catalog"><option value="">/shop/</option>
            <?php foreach (site_pages() as $candidate): ?>
                <option value="<?= site_escape($candidate['path']) ?>"<?= $shopState['catalog'] === $candidate['path'] ? ' selected' : '' ?>><?= site_escape($candidate['path']) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
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
    <label class="field"><?= site_escape(shop_label('page_size')) ?><input type="number" name="pageSize" min="1" max="96" value="<?= $shopState['pageSize'] ?>"></label>
    <label class="field"><?= site_escape(shop_label('shipping')) ?><input type="number" step="0.01" min="0" name="shipping" value="<?= number_format($shopState['shipping'] / 100, 2, '.', '') ?>"></label>
    <label class="choice"><input type="checkbox" name="guest" value="1"<?= $shopState['guest'] ? ' checked' : '' ?>> <?= site_escape(shop_label('guest')) ?></label>
    <label class="field"><?= site_escape(shop_label('price_labels')) ?><textarea name="priceLabels" rows="3" placeholder="old=Старая цена"><?php
        foreach ($shopState['priceLabels'] ?? [] as $id => $label) echo site_escape($id . '=' . $label) . "\n";
    ?></textarea></label>
    <h2><?= site_escape(shop_label('templates')) ?></h2>
    <?php foreach (shop_defaults() as $key=>$markup): ?>
        <details><summary><?= site_escape(shop_label('template_' . $key)) ?></summary>
            <textarea aria-label="<?= site_escape(shop_label('template_' . $key)) ?>" name="template[<?= $key ?>]" rows="5" data-code-mode="html"><?= site_escape($shopState['templates'][$key] ?? $markup) ?></textarea>
        </details>
    <?php endforeach; ?>
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
    ?>
    <nav class="site-menu scope-menu" aria-label="<?= site_escape(shop_label('catalog')) ?>">
        <?php foreach ($sheets as $key=>$cols): ?><a href="<?= site_escape($tabUrl . '&sheet=' . $key) ?>"<?= $table === $key ? ' aria-current="page"' : '' ?>><?= site_escape(shop_label($key)) ?></a><?php endforeach; ?>
    </nav>
    <?php if ($moduleOwner && $table === 'products'): ?>
        <?php require __DIR__ . '/admin-products-view.php'; ?>
    <?php elseif ($moduleOwner && $table === 'categories'): ?>
        <?php require __DIR__ . '/admin-categories-view.php'; ?>
    <?php elseif ($table === 'warehouses'): ?>
        <section class="panel form-stack shop-module-panel">
            <h2><?= site_escape(shop_label('warehouses')) ?></h2>
            <?php if ($rows): ?><ul><?php foreach ($rows as $warehouse): ?><li><?= site_escape($warehouse['name']) ?></li><?php endforeach; ?></ul><?php endif; ?>
            <form method="post" class="form-stack" action="<?= site_escape($tabUrl . '&sheet=warehouses') ?>">
                <input type="hidden" name="csrf" value="<?= site_escape($moduleToken) ?>">
                <input type="hidden" name="action" value="shop-warehouse-add">
                <input type="hidden" name="revision" value="<?= site_escape($catalogRevision) ?>">
                <label class="field"><?= site_escape(shop_label('warehouse_name')) ?><input name="warehouse" maxlength="240" required></label>
                <div><button class="button-primary" type="submit"><?= site_escape(shop_label('warehouse_add')) ?></button></div>
            </form>
        </section>
    <?php else: ?>
    <?= $shopForm('shop-sheet') ?>
    <h2><?= site_escape(shop_label($table)) ?></h2>
    <input type="hidden" name="sheet" value="<?= site_escape($table) ?>">
    <input type="hidden" name="revision" value="<?= site_escape($catalogRevision) ?>">
    <label class="field"><?= site_escape(shop_label('sheet_help')) ?>
        <textarea name="csv" rows="16" spellcheck="false" required><?= site_escape(shop_csv(array_merge([SHOP_COLUMNS[$table]], $rows))) ?></textarea>
    </label>
    <?= $shopButton('save') ?>
    <?php endif; ?>
    <details class="panel shop-maintenance"><summary><?= site_escape(shop_label('reindex')) ?></summary>
        <?= $shopForm('shop-reindex') . $shopButton('reindex') ?>
    </details>
<?php elseif ($shopView === 'import'): ?>
    <?= $shopForm('shop-import') ?>
    <h2><?= site_escape(shop_label('import')) ?></h2>
    <p><?= site_escape(shop_label('import_help')) ?></p>
    <div class="shop-import-fields">
    <label class="field"><?= site_escape(shop_label('source')) ?><input name="supplier" required maxlength="120"></label>
    <label class="field"><?= site_escape(shop_label('format')) ?><select name="format"><option value="csv">CSV</option><option value="yml">YML</option></select></label>
    <label class="field"><?= site_escape(shop_label('separator')) ?><select name="separator"><option value="pipe">|</option><option value="semicolon">;</option><option value="comma">,</option><option value="tab">TAB</option></select></label>
    <label class="field">CSV / YML<input type="file" name="catalog_file" accept=".csv,.yml,.xml,text/csv,text/xml" required></label>
    </div>
    <details><summary><?= site_escape(shop_label('mapping')) ?></summary>
        <textarea name="mapping" rows="5" aria-label="<?= site_escape(shop_label('mapping')) ?>" placeholder="sku=Артикул&#10;name=Название&#10;price=Цена&#10;field:power=Мощность"></textarea>
    </details>
    <label class="choice"><input type="checkbox" name="descriptions" value="1"> <?= site_escape(shop_label('replace_descriptions')) ?></label>
    <?= $shopButton('preview') ?>
    <?php if (isset($_SESSION['shopImportPreview']['report'])): ?>
        <?= $shopForm('shop-apply') ?>
        <p><?= site_escape(shop_label('new') . ' · ' . $_SESSION['shopImportPreview']['report']['new']
            . ' / ' . shop_label('updated') . ' · ' . $_SESSION['shopImportPreview']['report']['updated']) ?></p>
        <?= $shopButton('apply') ?>
    <?php endif; ?>
<?php elseif ($shopView === 'images'): ?>
    <?php $photos = array_values(array_filter(glob(SHOP_IMAGES_DIR . '/*') ?: [], static function (string $file): bool {
        return is_file($file) && shop_image_name(basename($file));
    }));
    $photoPages = max(1, (int) ceil(count($photos) / 24));
    $photoPage = min($photoPages, max(1, (int) shop_input($_GET, 'p'))); ?>
    <?= $shopForm('shop-photo') ?>
    <h2><?= site_escape(shop_label('images')) ?> <span class="count-badge"><?= count($photos) ?></span></h2>
    <label class="field"><?= site_escape(shop_label('upload')) ?><input type="file" name="photo" accept="image/jpeg,image/png,image/webp,image/gif,image/avif" required></label>
    <?= $shopButton('upload') ?>
    <?php if (isset($_SESSION['shopUploaded'])): ?><p><code><?= site_escape($_SESSION['shopUploaded']) ?></code></p><?php endif; ?>
    <div class="shop-admin-media">
        <?php foreach (array_slice($photos, ($photoPage - 1) * 24, 24) as $file): ?>
            <figure class="panel"><a href="<?= site_escape(shop_image_url($base, basename($file))) ?>" target="_blank" rel="noopener">
                <?= shop_image($base, basename($file), basename($file)) ?></a><figcaption><code><?= site_escape(basename($file)) ?></code></figcaption></figure>
        <?php endforeach; ?>
    </div>
    <?php if ($photoPages > 1): ?><nav class="shop-order-pages" aria-label="<?= site_escape(shop_label('images')) ?>">
        <?php if ($photoPage > 1): ?><a class="button" href="<?= site_escape($tabUrl . '&p=' . ($photoPage - 1)) ?>"><?= site_escape(shop_label('gallery_prev')) ?></a><?php endif; ?>
        <span aria-current="page"><?= $photoPage ?> / <?= $photoPages ?></span>
        <?php if ($photoPage < $photoPages): ?><a class="button" href="<?= site_escape($tabUrl . '&p=' . ($photoPage + 1)) ?>"><?= site_escape(shop_label('gallery_next')) ?></a><?php endif; ?>
    </nav><?php endif; ?>
<?php elseif ($shopView === 'orders'): ?>
    <?php if (is_file(__DIR__ . '/admin-orders-view.php')) require __DIR__ . '/admin-orders-view.php'; ?>
<?php elseif ($shopView === 'sales'): ?>
    <?php if (is_file(__DIR__ . '/admin-sales-view.php')) require __DIR__ . '/admin-sales-view.php'; ?>
<?php elseif ($shopView === 'integrations'): ?>
    <?php if (is_file(__DIR__ . '/admin-integrations-view.php')) require __DIR__ . '/admin-integrations-view.php'; ?>
<?php endif; ?>
</div>
