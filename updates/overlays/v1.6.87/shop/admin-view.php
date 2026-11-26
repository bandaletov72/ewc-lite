<?php
if (!defined('SITE_ROOT')) { http_response_code(404); exit; }
$shopView = shop_input($_GET, 'tab');
$shopState = shop_settings();
$mayCatalog = $moduleOwner || in_array('catalog', $roles, true);
$mayOrders = $moduleOwner || in_array('orders', $roles, true);
$tabs = ($mayCatalog ? ['catalog'=>'catalog','import'=>'import','images'=>'images'] : [])
    + ($mayOrders ? ['orders'=>'orders'] : []) + ($moduleOwner ? ['settings'=>'settings'] : []);
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
<?php if ($shopView === 'settings'): ?>
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
    <?php foreach (shop_mail_defaults() as $key => $default): ?>
        <?php $message = $shopState['mail'][$key] ?? $default; ?>
        <details><summary><?= site_escape(shop_label('mail_' . $key)) ?></summary>
            <label class="field"><?= site_escape(shop_label('mail_subject')) ?><input name="mail[<?= $key ?>][0]" value="<?= site_escape($message[0]) ?>"></label>
            <label class="field"><?= site_escape(shop_label('mail_body')) ?><textarea name="mail[<?= $key ?>][1]"><?= site_escape($message[1]) ?></textarea></label>
            <p><code>{{site}} {{name}} {{order}} {{status}} {{total}}</code></p>
        </details>
    <?php endforeach; ?>
    <?= $shopButton('save') ?>
<?php elseif ($shopView === 'catalog'): ?>
    <?php
    $table = shop_input($_GET, 'sheet');
    $sheets = array_diff_key(SHOP_COLUMNS, array_flip($moduleOwner ? ['inventory','barcodes'] : ['warehouses','inventory','barcodes']));
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
    <?= $shopForm('shop-reindex') . $shopButton('reindex') ?>
<?php elseif ($shopView === 'import'): ?>
    <?= $shopForm('shop-import') ?>
    <h2><?= site_escape(shop_label('import')) ?></h2>
    <p><?= site_escape(shop_label('import_help')) ?></p>
    <label class="field"><?= site_escape(shop_label('source')) ?><input name="supplier" required maxlength="120"></label>
    <label class="field"><?= site_escape(shop_label('format')) ?><select name="format"><option value="csv">CSV</option><option value="yml">YML</option></select></label>
    <label class="field"><?= site_escape(shop_label('separator')) ?><select name="separator"><option value="pipe">|</option><option value="semicolon">;</option><option value="comma">,</option><option value="tab">TAB</option></select></label>
    <label class="field">CSV / YML<input type="file" name="catalog_file" accept=".csv,.yml,.xml,text/csv,text/xml" required></label>
    <label class="field"><?= site_escape(shop_label('mapping')) ?><textarea name="mapping" rows="5" placeholder="sku=Артикул&#10;name=Название&#10;price=Цена&#10;field:power=Мощность"></textarea></label>
    <label class="choice"><input type="checkbox" name="descriptions" value="1"> <?= site_escape(shop_label('replace_descriptions')) ?></label>
    <?= $shopButton('preview') ?>
    <?php if (isset($_SESSION['shopImportPreview']['report'])): ?>
        <?= $shopForm('shop-apply') ?>
        <p><?= site_escape(shop_label('new') . ' · ' . $_SESSION['shopImportPreview']['report']['new']
            . ' / ' . shop_label('updated') . ' · ' . $_SESSION['shopImportPreview']['report']['updated']) ?></p>
        <?= $shopButton('apply') ?>
    <?php endif; ?>
<?php elseif ($shopView === 'images'): ?>
    <?= $shopForm('shop-photo') ?>
    <label class="field"><?= site_escape(shop_label('images')) ?><input type="file" name="photo" accept="image/jpeg,image/png,image/webp,image/gif,image/avif" required></label>
    <?= $shopButton('upload') ?>
    <?php if (isset($_SESSION['shopUploaded'])): ?><p><code><?= site_escape($_SESSION['shopUploaded']) ?></code></p><?php endif; ?>
    <div class="shop-gallery">
        <?php foreach (array_slice(glob(__DIR__ . '/images/*') ?: [], 0, 100) as $file): ?>
            <?php if (shop_image_name(basename($file))): ?><figure><?= shop_image($base, basename($file), basename($file)) ?><figcaption><?= site_escape(basename($file)) ?></figcaption></figure><?php endif; ?>
        <?php endforeach; ?>
    </div>
<?php elseif ($shopView === 'orders'): ?>
    <?php foreach (array_slice(shop_orders(), 0, 100) as $order): ?>
        <?php $head = $order['head']; ?>
        <details class="panel">
            <summary><?= site_escape(substr($order['id'], 0, 12) . ' · ' . $head[1] . ' · ' . shop_label($head[2]) . ' · '
                    . shop_money((int) $head[10], ['currency'=>$head[11]])) ?></summary>
            <p><?= site_escape($head[4] . ' · ' . $head[5] . ' · ' . $head[6] . ' · ' . shop_label($head[8]) . ' · ' . $head[7]) ?></p>
            <ul><?php foreach ($order['lines'] as $line): if ($line[0] === 'item'): ?>
                <li><?= site_escape($line[3] . ' ' . $line[4] . ' · ' . $line[6] . ' × ' . shop_money((int) $line[5], ['currency'=>$head[11]])) ?></li>
            <?php elseif ($line[0] === 'event'): ?><li><?= site_escape($line[1] . ' · ' . shop_label($line[2])) ?></li><?php endif; endforeach; ?></ul>
            <?php $choices = ['new'=>['confirmed','cancelled'], 'confirmed'=>['packed','cancelled'], 'packed'=>['issued','cancelled']][$head[2]] ?? []; ?>
            <?php if ($choices): ?>
                <?= $shopForm('shop-status') ?>
                <input type="hidden" name="order" value="<?= site_escape($order['id']) ?>">
                <select name="status" aria-label="<?= site_escape(shop_label('status')) ?>"><?php foreach ($choices as $next): ?>
                    <option value="<?= $next ?>"><?= site_escape(shop_label($next)) ?></option>
                <?php endforeach; ?></select>
                <?= $shopButton('save') ?>
            <?php endif; ?>
        </details>
    <?php endforeach; ?>
<?php endif; ?>
