<?php
if (!defined('SITE_ROOT')) { http_response_code(404); exit; }

$productLabel = static function (string $key): string { return site_escape(shop_label($key)); };
$products = array_column($rows, null, 'id');
$categories = array_column($tables['categories'], 'name', 'id');
asort($categories, SORT_NATURAL | SORT_FLAG_CASE);
$offersByProduct = [];
foreach ($tables['offers'] as $offer) $offersByProduct[$offer['product_id']][$offer['id']] = $offer;
$barcodes = array_column($tables['barcodes'], 'barcode', 'offer_id');
$inventory = [];
foreach ($tables['inventory'] as $row) $inventory[$row['offer_id']][$row['warehouse_id']] = $row['quantity'];
$query = shop_input($_GET, 'q');
$category = shop_input($_GET, 'category');
$active = shop_input($_GET, 'active');
$filters = array_filter(['q'=>$query,'category'=>$category,'active'=>$active], static function (string $value): bool { return $value !== ''; });
$matching = [];
$needle = shop_lower($query);
foreach ($products as $product) {
    if (($category !== '' && $product['category_id'] !== $category) || ($active !== '' && $product['active'] !== $active)) continue;
    if ($needle !== '') {
        $productOffers = $offersByProduct[$product['id']] ?? [];
        $searchable = implode("\n", array_merge([$product['name']], array_column($productOffers, 'sku'), array_intersect_key($barcodes, $productOffers)));
        if (strpos(shop_lower($searchable), $needle) === false) continue;
    }
    $matching[] = $product;
}
$pageSize = 20;
$pages = max(1, (int) ceil(count($matching) / $pageSize));
$pageNumber = max(1, min($pages, (int) shop_input($_GET, 'p')));
$listUrl = static function (array $params = []) use ($tabUrl, $filters, $pageNumber): string {
    return $tabUrl . '&' . http_build_query($params + ['sheet'=>'products','p'=>$pageNumber] + $filters, '', '&', PHP_QUERY_RFC3986);
};
$optional = ['barcode'=>'product_barcode','image'=>'product_image','category'=>'product_category','stock'=>'product_stock','status'=>'status','type'=>'product_type','created'=>'product_created'];
$checked = array_fill_keys($_SESSION['shopProductColumns'] ?? [], true);
$types = ['physical'=>'product_physical','digital'=>'product_digital','service'=>'product_service','kit'=>'kits'];
$actionLabels = ['edit'=>['pen','product_edit'],'copy'=>['copy','product_copy'],'delete'=>['trash','remove']];
?>
<section class="panel form-stack shop-product-list" aria-labelledby="shop-products-title">
    <div class="header-actions shop-product-heading">
        <h2 id="shop-products-title"><?= $productLabel('products') ?></h2>
        <a class="button button-primary" href="<?= site_escape($listUrl(['product_action'=>'new'])) ?>"><?= $productLabel('product_add') ?></a>
    </div>
    <form method="get" class="form-stack">
        <input type="hidden" name="shop" value="1"><input type="hidden" name="tab" value="catalog"><input type="hidden" name="sheet" value="products">
        <div class="shop-product-filters">
            <label class="field"><?= $productLabel('product_search') ?><input type="search" name="q" value="<?= site_escape($query) ?>"></label>
            <label class="field"><?= $productLabel('product_category') ?><select name="category">
                <option value=""><?= $productLabel('product_all_categories') ?></option>
                <?php foreach ($categories as $id => $name): ?><option value="<?= site_escape((string) $id) ?>"<?= (string) $id === $category ? ' selected' : '' ?>><?= site_escape($name) ?></option><?php endforeach; ?>
            </select></label>
            <label class="field"><?= $productLabel('status') ?><select name="active">
                <option value=""><?= $productLabel('product_all_statuses') ?></option>
                <?php foreach (['1'=>'product_active','0'=>'product_inactive'] as $value => $key): ?><option value="<?= $value ?>"<?= (string) $value === $active ? ' selected' : '' ?>><?= $productLabel($key) ?></option><?php endforeach; ?>
            </select></label>
            <button type="submit"><?= $productLabel('filters') ?></button>
            <?php if ($filters): ?><a href="<?= site_escape($tabUrl . '&sheet=products') ?>"><?= $productLabel('product_reset') ?></a><?php endif; ?>
        </div>
        <details class="shop-product-columns">
            <summary><?= $productLabel('product_columns') ?></summary>
            <div class="header-actions">
                <input type="hidden" name="columns_set" value="1">
                <?php foreach ($optional as $key => $name): ?>
                    <label class="choice"><input type="checkbox" name="columns[]" value="<?= $key ?>"<?= isset($checked[$key]) ? ' checked' : '' ?>><?= $productLabel($name) ?></label>
                <?php endforeach; ?>
                <button type="submit"><?= $productLabel('apply') ?></button>
            </div>
        </details>
    </form>
    <div class="shop-product-table-wrap" tabindex="0" role="region" aria-labelledby="shop-products-title">
        <table class="shop-product-table">
            <thead><tr>
                <th scope="col"><?= $productLabel('product_name') ?></th><th scope="col"><?= $productLabel('product_sku') ?></th><th scope="col"><?= $productLabel('price') ?></th>
                <?php foreach ($optional as $key => $name): if (isset($checked[$key])): ?><th scope="col"><?= $productLabel($name) ?></th><?php endif; endforeach; ?>
                <th scope="col" class="shop-product-actions"><?= $productLabel('product_actions') ?></th>
            </tr></thead>
            <tbody>
            <?php foreach (array_slice($matching, ($pageNumber - 1) * $pageSize, $pageSize) as $product):
                $productOffers = $offersByProduct[$product['id']] ?? [];
                $prices = array_map('intval', array_column($productOffers, 'price_minor'));
                $price = $prices ? shop_money(min($prices), $shopState) : '—';
                if ($prices && min($prices) !== max($prices)) $price .= ' – ' . shop_money(max($prices), $shopState);
                $sum = array_sum(array_column($productOffers, 'stock'));
                foreach ($productOffers as $offerId => $offer) $sum += array_sum($inventory[$offerId] ?? []);
                $cells = ['barcode'=>implode(', ', array_intersect_key($barcodes, $productOffers)) ?: '—',
                    'category'=>$categories[$product['category_id']] ?? '', 'stock'=>$productOffers ? (string) $sum : '—',
                    'status'=>shop_label($product['active'] === '1' ? 'product_active' : 'product_inactive'), 'type'=>shop_label($types[$product['type']]), 'created'=>$product['created']];
                ?>
                <tr>
                    <th scope="row"><a href="<?= site_escape($listUrl(['product_action'=>'edit','product'=>$product['id']])) ?>"><?= site_escape($product['name']) ?></a></th>
                    <td class="shop-product-skus"><?= site_escape(implode(', ', array_column($productOffers, 'sku')) ?: '—') ?></td>
                    <td class="shop-product-price"><?= site_escape($price) ?></td>
                    <?php foreach ($optional as $key => $name): if (isset($checked[$key])): ?>
                        <td><?= $key === 'image' ? (shop_image($base, $product['image'], $product['name']) ?: '—') : site_escape($cells[$key]) ?></td>
                    <?php endif; endforeach; ?>
                    <td><div class="shop-product-actions">
                        <?php foreach ($actionLabels as $action => list($icon, $text)): ?>
                            <a class="button" href="<?= site_escape($listUrl(['product_action'=>$action,'product'=>$product['id']])) ?>" title="<?= $productLabel($text) ?>"
                                aria-label="<?= site_escape(shop_label($text) . ' · ' . $product['name']) ?>"><i class="fa-solid fa-<?= $icon ?>" aria-hidden="true"></i></a>
                        <?php endforeach; ?>
                    </div></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$matching): ?><tr><td colspan="<?= 4 + count(array_intersect_key($optional, $checked)) ?>"><?= $productLabel('no_products') ?></td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if ($pages > 1): ?>
        <nav class="header-actions" aria-label="<?= $productLabel('pages') ?>">
            <?php foreach (array_unique(array_merge([1], range(max(1, $pageNumber - 2), min($pages, $pageNumber + 2)), [$pages])) as $number): ?>
                <a class="button<?= $number === $pageNumber ? ' button-primary' : '' ?>" href="<?= site_escape($listUrl(['p'=>$number])) ?>"<?= $number === $pageNumber ? ' aria-current="page"' : '' ?>><?= $number ?></a>
            <?php endforeach; ?>
        </nav>
    <?php endif; ?>
</section>
<?php
$productId = shop_input($_GET, 'product');
$productAction = shop_input($_GET, 'product_action') ?: ($productId !== '' ? 'edit' : '');
$source = $products[$productId] ?? null;
if ($productAction === '' || !isset(($actionLabels + ['new'=>true])[$productAction])) return;
if ($productAction !== 'new' && !$source) {
    echo '<p class="notice error" role="alert">' . $productLabel('not_found') . '</p>';
    return;
}
$copying = $productAction === 'copy';
$productCreating = $productAction === 'new';
$deleting = $productAction === 'delete';
$draft = ($source ?? []) + ['name'=>'','category_id'=>$category,'slug'=>'','summary'=>'','description'=>'','image'=>'','type'=>'physical','active'=>'1'];
$draftOffers = $offersByProduct[$productId] ?? [];
foreach ($draftOffers as $offerId => &$offer) {
    $offer['price'] = sprintf('%d.%02d', intdiv((int) $offer['price_minor'], 100), (int) $offer['price_minor'] % 100);
    $offer['barcode'] = $copying ? '' : ($barcodes[$offerId] ?? '');
    $offer['inventory'] = $copying ? [] : ($inventory[$offerId] ?? []);
    if ($copying) {
        $offer['sku'] = (strlen($offer['sku']) <= 126 ? $offer['sku'] . '-' : '') . 'COPY-' . bin2hex(random_bytes(6));
        $offer['stock'] = '0';
    }
}
unset($offer);
if ($copying) {
    $draft['name'] .= ' (' . shop_label('product_copy_noun') . ')';
    $draft['slug'] = '';
    $draft['active'] = '0';
}
$draftOffers['_new'] = ['sku'=>'','barcode'=>'','variant'=>'','price'=>'','stock'=>'0','active'=>'1','inventory'=>[]];
$postAction = $deleting ? 'shop-product-delete' : 'shop-product-save';
$failed = $error !== '' && shop_input($_POST, 'action') === $postAction;
if ($failed && !$deleting) {
    foreach (['name','category_id','slug','summary','description','image','type','active'] as $field) $draft[$field] = shop_input($_POST, $field);
    foreach ($draftOffers as $key => &$offer) {
        $entry = $_POST['offers'][$key] ?? null;
        if (is_array($entry)) {
            foreach (['sku','barcode','variant','price','stock','active'] as $field) $offer[$field] = shop_input($entry, $field);
            $offer['inventory'] = is_array($entry['inventory'] ?? null) ? array_filter($entry['inventory'], 'is_string') : [];
        }
    }
    unset($offer);
}
$title = $productCreating ? 'product_add' : $actionLabels[$productAction][1];
?>
<dialog class="editor-dialog shop-product-dialog" data-shop-product-dialog aria-labelledby="shop-product-dialog-title" open>
    <form class="form-stack" method="post" action="<?= site_escape($listUrl(['product_action'=>$productAction,'product'=>$productId])) ?>">
        <div class="header-actions editor-dialog-heading">
            <h2 id="shop-product-dialog-title"><?= $productLabel($title) ?></h2>
            <a class="button" href="<?= site_escape($listUrl()) ?>" data-shop-product-close aria-label="<?= $productLabel('product_close') ?>">×</a>
        </div>
        <?php if ($failed): ?><p class="notice error" role="alert"><?= site_escape($error) ?></p><?php endif; ?>
        <input type="hidden" name="csrf" value="<?= site_escape($moduleToken) ?>">
        <input type="hidden" name="action" value="<?= $postAction ?>">
        <input type="hidden" name="revision" value="<?= site_escape($failed ? shop_input($_POST, 'revision') : $catalogRevision) ?>">
        <input type="hidden" name="id" value="<?= site_escape($copying || $productCreating ? '' : $productId) ?>">
        <?php if ($deleting): ?>
            <p><?= $productLabel('product_confirm_delete') ?> <strong><?= site_escape($draft['name']) ?></strong></p>
            <input type="hidden" name="confirm" value="1">
        <?php else: ?>
            <input type="hidden" name="copy" value="<?= site_escape($copying ? $productId : '') ?>">
            <div class="fields">
                <label class="field field-wide"><?= $productLabel('product_name') ?><input name="name" required maxlength="1000" value="<?= site_escape($draft['name']) ?>"></label>
                <label class="field"><?= $productLabel('product_category') ?><select name="category_id" required>
                    <option value=""></option>
                    <?php foreach ($categories as $id => $name): ?><option value="<?= site_escape((string) $id) ?>"<?= (string) $id === $draft['category_id'] ? ' selected' : '' ?>><?= site_escape($name) ?></option><?php endforeach; ?>
                </select></label>
                <label class="field"><?= $productLabel('product_slug') ?><input name="slug" maxlength="64" pattern="[a-z0-9][a-z0-9_\-]{0,63}" placeholder="<?= $productLabel('product_slug_auto') ?>" value="<?= site_escape($draft['slug']) ?>"></label>
                <label class="field"><?= $productLabel('product_type') ?><select name="type">
                    <?php foreach ($types as $type => $key): ?><option value="<?= $type ?>"<?= $type === $draft['type'] ? ' selected' : '' ?>><?= $productLabel($key) ?></option><?php endforeach; ?>
                </select></label>
                <label class="field"><?= $productLabel('status') ?><select name="active">
                    <?php foreach (['1'=>'product_active','0'=>'product_inactive'] as $value => $key): ?><option value="<?= $value ?>"<?= (string) $value === $draft['active'] ? ' selected' : '' ?>><?= $productLabel($key) ?></option><?php endforeach; ?>
                </select></label>
                <label class="field field-wide"><?= $productLabel('product_image') ?><input name="image" list="shop-product-images" value="<?= site_escape($draft['image']) ?>"></label>
                <datalist id="shop-product-images"><?php foreach (glob(__DIR__ . '/images/*') ?: [] as $file): if (shop_image_name(basename($file))): ?><option value="<?= site_escape(basename($file)) ?>"></option><?php endif; endforeach; ?></datalist>
                <label class="field field-wide"><?= $productLabel('summary') ?><textarea name="summary" rows="2" maxlength="5000"><?= site_escape($draft['summary']) ?></textarea></label>
            </div>
            <div class="form-stack">
                <h3><?= $productLabel('offers') ?></h3>
                <?php foreach ($draftOffers as $id => $offer): ?>
                    <details class="panel shop-product-offer"<?= $id !== '_new' || count($draftOffers) === 1 || $failed ? ' open' : '' ?>>
                        <summary><?= $id === '_new' ? $productLabel('product_add_offer') : site_escape($offer['sku']) ?></summary>
                        <div class="fields">
                            <?php foreach (['sku'=>'product_sku','barcode'=>'product_barcode','price'=>'price','variant'=>'product_variant','stock'=>$tables['warehouses'] ? 'product_unassigned' : 'product_stock'] as $field => $key): ?>
                                <label class="field"><?= $productLabel($key) ?><input name="offers[<?= site_escape((string) $id) ?>][<?= $field ?>]" value="<?= site_escape($offer[$field]) ?>"
                                    <?= $field === 'price' ? 'inputmode="decimal"' : ($field === 'stock' ? 'inputmode="numeric" pattern="[0-9]{1,8}"' : '') ?>
                                    <?= $id !== '_new' && in_array($field, ['sku','price','stock'], true) ? 'required' : '' ?>></label>
                            <?php endforeach; ?>
                            <label class="field"><?= $productLabel('status') ?><select name="offers[<?= site_escape((string) $id) ?>][active]">
                                <?php foreach (['1'=>'product_active','0'=>'product_inactive'] as $value => $key): ?><option value="<?= $value ?>"<?= (string) $value === $offer['active'] ? ' selected' : '' ?>><?= $productLabel($key) ?></option><?php endforeach; ?>
                            </select></label>
                            <?php foreach ($tables['warehouses'] as $warehouse): ?>
                                <label class="field"><?= site_escape($warehouse['name']) ?>
                                    <input name="offers[<?= site_escape((string) $id) ?>][inventory][<?= site_escape($warehouse['id']) ?>]" inputmode="numeric" pattern="[0-9]{1,8}"
                                        value="<?= site_escape($offer['inventory'][$warehouse['id']] ?? '0') ?>"></label>
                            <?php endforeach; ?>
                        </div>
                    </details>
                <?php endforeach; ?>
            </div>
            <?php
            $editorId = 'shop-product-description';
            $editorName = 'description';
            $editorTitle = shop_label('description');
            $editorValue = $draft['description'];
            $editorWithComponents = true;
            $editorWithMedia = false;
            $discoveryLabels = admin_maintenance_labels($adminLanguage);
            require SITE_ROOT . '/admin/content-editor.php';
            ?>
            <input type="hidden" name="complete" value="1">
        <?php endif; ?>
        <div class="header-actions">
            <button class="<?= $deleting ? 'button-danger' : 'button-primary' ?>" type="submit"><?= $productLabel($deleting ? 'remove' : 'save') ?></button>
            <a class="button" href="<?= site_escape($listUrl()) ?>"><?= $productLabel('product_cancel') ?></a>
        </div>
    </form>
</dialog>
