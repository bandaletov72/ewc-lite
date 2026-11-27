<?php
if (!defined('SITE_ROOT')) { http_response_code(404); exit; }

$productLabel = static function (string $key): string { return site_escape(shop_label($key)); };
$products = array_column($rows, null, 'id');
$categories = array_column($tables['categories'], 'name', 'id');
$categoryParents = array_column($tables['categories'], 'parent_id', 'id');
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
$optional = ['barcode'=>'product_barcode','image'=>'product_image','category'=>'product_category','stock'=>'product_stock','status'=>'status','type'=>'product_type','created'=>'product_created'];
$productIndex = shop_index();
$numericColumns = [];
foreach ($productIndex['fields'] as $fieldId => $meta) {
    if ($meta['target'] === 'product' && in_array($meta['type'], ['number','price'], true)) {
        $numericColumns['attribute-' . $fieldId] = $meta;
        $optional['attribute-' . $fieldId] = shop_field_label($meta);
    }
}
$columnLabel = static function (string $key, string $name) use ($numericColumns, $productLabel): string {
    return isset($numericColumns[$key]) ? site_escape($name) : $productLabel($name);
};
$checked = array_fill_keys($_SESSION['shopProductColumns'] ?? [], true);
$showSku = $_SESSION['shopProductShowSku'] ?? true;
$columns = ['name'=>'product_name'] + ($showSku ? ['sku'=>'product_sku'] : []) + ['price'=>'price'] + array_intersect_key($optional, $checked);
$types = ['physical'=>'product_physical','digital'=>'product_digital','service'=>'product_service','kit'=>'kits'];
$sort = shop_input($_GET, 'sort');
$direction = shop_input($_GET, 'dir') === 'desc' ? 'desc' : 'asc';
$sorting = isset($columns[$sort]) ? ['sort'=>$sort,'dir'=>$direction] : [];
$productStock = static function (array $offers) use ($inventory): int {
    $sum = array_sum(array_column($offers, 'stock'));
    foreach (array_keys($offers) as $id) $sum += array_sum($inventory[$id] ?? []);
    return $sum;
};
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
if ($sorting) {
    $sortValues = [];
    foreach ($matching as $product) {
        $id = $product['id'];
        $offers = $offersByProduct[$id] ?? [];
        switch ($sort) {
            case 'price': $value = $offers ? (int) min(array_column($offers, 'price_minor')) : null; break;
            case 'sku': $value = implode(', ', array_column($offers, 'sku')); break;
            case 'barcode': $value = implode(', ', array_intersect_key($barcodes, $offers)); break;
            case 'category': $value = $categories[$product['category_id']] ?? ''; break;
            case 'type': $value = shop_label($types[$product['type']]); break;
            case 'stock': $value = $offers ? $productStock($offers) : null; break;
            case 'status': $value = (int) $product['active']; break;
            default:
                if (isset($numericColumns[$sort])) {
                    $field = $numericColumns[$sort]['id'];
                    $entry = $productIndex['values'][$field][$id][0] ?? null;
                    $value = is_numeric($entry) ? (float) $entry : null;
                } else $value = $product[$sort];
        }
        $sortValues[$id] = is_string($value) ? ($value === '' ? null : shop_lower($value)) : $value;
    }
    usort($matching, static function (array $left, array $right) use ($sortValues, $direction): int {
        $a = $sortValues[$left['id']];
        $b = $sortValues[$right['id']];
        if (($a === null) !== ($b === null)) return $a === null ? 1 : -1;
        $order = is_string($a) ? strnatcmp($a, $b) : ($a <=> $b);
        return $order === 0 ? strcmp($left['id'], $right['id']) : ($direction === 'desc' ? -$order : $order);
    });
}
$pageSize = 20;
$pages = max(1, (int) ceil(count($matching) / $pageSize));
$pageNumber = max(1, min($pages, (int) shop_input($_GET, 'p')));
$listUrl = static function (array $params = []) use ($tabUrl, $filters, $sorting, $pageNumber): string {
    return $tabUrl . '&' . http_build_query($params + ['sheet'=>'products','p'=>$pageNumber] + $filters + $sorting, '', '&', PHP_QUERY_RFC3986);
};
$actionLabels = ['edit'=>['pen','product_edit'],'copy'=>['copy','product_copy'],'delete'=>['trash','remove']];
$renderInline = static function (string $kind, string $target, string $value, string $label, string $detail = '', string $type = '')
    use ($moduleAdminUrl, $moduleToken, $productLabel): void {
    ?>
    <form class="shop-inline-form" method="post" action="<?= site_escape($moduleAdminUrl) ?>" data-shop-price-form
        data-error="<?= $productLabel('try_again') ?>" hidden>
        <input type="hidden" name="action" value="shop-inline-value">
        <input type="hidden" name="csrf" value="<?= site_escape($moduleToken) ?>">
        <input type="hidden" name="kind" value="<?= $kind ?>">
        <input type="hidden" name="target" value="<?= site_escape($target) ?>">
        <input type="hidden" name="detail" value="<?= site_escape($detail) ?>">
        <input type="hidden" name="previous" value="<?= site_escape($value) ?>">
        <input name="value" value="<?= site_escape($value) ?>" aria-label="<?= site_escape($label) ?>"
            <?php if ($kind !== 'attribute'): ?>inputmode="numeric" maxlength="<?= $kind === 'stock' ? 8 : 128 ?>" data-shop-digits
                data-format="<?= $productLabel('digits_format') ?>"<?= $kind === 'stock' ? ' required' : '' ?>
            <?php elseif ($type === 'price'): ?>inputmode="decimal" maxlength="32" data-shop-money
                data-format="<?= $productLabel('price_format') ?>" data-positive="<?= $productLabel('positive_price') ?>"
            <?php else: ?>inputmode="decimal" maxlength="500" data-shop-number data-format="<?= $productLabel('number_format') ?>"<?php endif; ?>>
        <small class="shop-price-status" role="status"></small>
    </form>
    <?php
};
?>
<section class="panel form-stack shop-product-list" aria-labelledby="shop-products-title" data-shop-product-list
    data-error="<?= $productLabel('try_again') ?>" data-notice="<?= site_escape($notice ?? '') ?>" data-server-error="<?= site_escape($error ?? '') ?>" data-page="<?= $pageNumber ?>">
    <div class="header-actions shop-product-heading">
        <h2 id="shop-products-title"><?= $productLabel('products') ?>
            <span class="shop-product-count"><?= site_escape(($filters ? site_format_number((string) count($matching)) . ' / ' : '') . site_format_number((string) count($products))) ?></span>
        </h2>
        <a class="button button-primary" href="<?= site_escape($listUrl(['product_action'=>'new'])) ?>"><?= $productLabel('product_add') ?></a>
    </div>
    <p class="notice shop-list-message" data-shop-list-message role="status" hidden></p>
    <form method="get" class="form-stack">
        <input type="hidden" name="shop" value="1"><input type="hidden" name="tab" value="catalog"><input type="hidden" name="sheet" value="products">
        <?php foreach ($sorting as $key => $value): ?><input type="hidden" name="<?= $key ?>" value="<?= site_escape($value) ?>"><?php endforeach; ?>
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
            <?php if ($filters): ?><a href="<?= site_escape($tabUrl . '&sheet=products' . ($sorting ? '&' . http_build_query($sorting) : '')) ?>"><?= $productLabel('product_reset') ?></a><?php endif; ?>
        </div>
        <details class="shop-product-columns">
            <summary><?= $productLabel('product_columns') ?></summary>
            <div class="header-actions">
                <input type="hidden" name="columns_set" value="1">
                <input type="hidden" name="sku_control" value="1">
                <label class="choice"><input type="checkbox" name="columns[]" value="sku"<?= $showSku ? ' checked' : '' ?>><?= $productLabel('product_sku') ?></label>
                <?php foreach ($optional as $key => $name): ?>
                    <label class="choice"><input type="checkbox" name="columns[]" value="<?= $key ?>"<?= isset($checked[$key]) ? ' checked' : '' ?>><?= $columnLabel($key, $name) ?></label>
                <?php endforeach; ?>
                <button type="submit"><?= $productLabel('apply') ?></button>
            </div>
        </details>
    </form>
    <div class="shop-product-table-wrap" tabindex="0" role="region" aria-labelledby="shop-products-title">
        <table class="shop-product-table" data-shop-column-table="<?= site_escape($moduleAdminUrl) ?>">
            <colgroup><col class="shop-select-column"><?php foreach (array_keys($columns) as $key): ?><col data-column="<?= $key ?>"><?php endforeach; ?><col class="shop-actions-column"></colgroup>
            <thead><tr>
                <th scope="col" class="shop-product-select"><input type="checkbox" data-shop-select-all<?= $matching ? '' : ' disabled' ?> aria-label="<?= $productLabel('bulk_select_page') ?>" title="<?= $productLabel('bulk_select_page') ?>"></th>
                <?php foreach ($columns as $key => $name): ?>
                    <th scope="col"<?= $sort === $key ? ' aria-sort="' . ($direction === 'asc' ? 'ascending' : 'descending') . '"' : '' ?>>
                        <span class="shop-product-sort">
                            <button type="button" class="shop-column-move" hidden data-shop-move="<?= $key ?>"
                                title="<?= $productLabel('move_column') ?> · <?= $columnLabel($key, $name) ?>"
                                aria-label="<?= $productLabel('move_column') ?> · <?= $columnLabel($key, $name) ?>"><i class="fa-solid fa-grip-vertical" aria-hidden="true"></i></button>
                            <span class="shop-column-title" title="<?= $columnLabel($key, $name) ?>"><?= $columnLabel($key, $name) ?></span>
                            <span class="shop-product-sort-links">
                                <?php foreach (['asc'=>'▴','desc'=>'▾'] as $order => $arrow):
                                    $label = $columnLabel($key, $name) . ' · ' . $productLabel('product_sort_' . $order); ?>
                                    <a href="<?= site_escape($listUrl(['sort'=>$key,'dir'=>$order,'p'=>1])) ?>"
                                        title="<?= $label ?>" aria-label="<?= $label ?>"<?= $sort === $key && $direction === $order ? ' aria-current="true"' : '' ?>><?= $arrow ?></a>
                                <?php endforeach; ?>
                            </span>
                        </span>
                        <button type="button" class="iblock-column-resize" hidden data-shop-resize="<?= $key ?>"
                            title="<?= $productLabel('resize_column') ?> · <?= $columnLabel($key, $name) ?>"
                            aria-label="<?= $productLabel('resize_column') ?> · <?= $columnLabel($key, $name) ?>"></button>
                    </th>
                <?php endforeach; ?>
                <th scope="col" class="shop-actions-cell"><?= $productLabel('product_actions') ?></th>
            </tr></thead>
            <tbody>
            <?php foreach (array_slice($matching, ($pageNumber - 1) * $pageSize, $pageSize) as $product):
                $productOffers = $offersByProduct[$product['id']] ?? [];
                $price = shop_price_range(array_map('intval', array_column($productOffers, 'price_minor')), $shopState);
                $multiple = count($productOffers) > 1;
                $cells = ['barcode'=>implode(', ', array_intersect_key($barcodes, $productOffers)) ?: '—',
                    'category'=>$categories[$product['category_id']] ?? '', 'stock'=>$productOffers ? (string) $productStock($productOffers) : '—',
                    'status'=>shop_label($product['active'] === '1' ? 'product_active' : 'product_inactive'), 'type'=>shop_label($types[$product['type']]), 'created'=>$product['created']];
                ?>
                <tr<?= $product['active'] === '1' ? '' : ' class="shop-product-inactive"' ?>>
                    <td class="shop-product-select"><input type="checkbox" name="products[]" value="<?= site_escape($product['id']) ?>" form="shop-bulk-products"
                        data-shop-select aria-label="<?= site_escape(shop_label('bulk_select_product') . ' · ' . $product['name']) ?>"></td>
                    <th scope="row">
                        <?php if ($product['active'] === '1'): ?>
                            <a href="<?= site_escape(shop_route_url($base, 'product/' . $product['slug'])) ?>" target="_blank" rel="noopener"
                                title="<?= $productLabel('product_open_store') ?>"><?= site_escape($product['name']) ?></a>
                        <?php else: ?>
                            <?= site_escape($product['name']) ?>
                        <?php endif; ?>
                    </th>
                    <?php if ($showSku): ?><td class="shop-product-skus"><?= site_escape(implode(', ', array_column($productOffers, 'sku')) ?: '—') ?></td><?php endif; ?>
                    <td class="shop-product-price">
                        <?php if ($multiple): ?><details><summary data-shop-price-range title="<?= site_escape($price) ?>"><?= site_escape($price) ?></summary><?php endif; ?>
                        <?php if (!$multiple): ?><span data-shop-price-fallback><?= site_escape($price) ?></span><?php endif; ?>
                        <?php foreach ($productOffers as $offer): ?>
                            <?php $invalidPrice = (int) $offer['price_minor'] < 1; ?>
                            <form class="shop-price-form" action="<?= site_escape($moduleAdminUrl) ?>" method="post"
                                data-shop-price-form data-error="<?= $productLabel('try_again') ?>" hidden>
                                <input type="hidden" name="action" value="shop-price">
                                <input type="hidden" name="csrf" value="<?= site_escape($moduleToken) ?>">
                                <input type="hidden" name="offer" value="<?= site_escape($offer['id']) ?>">
                                <input type="hidden" name="previous" value="<?= site_escape($offer['price_minor']) ?>">
                                <?php if ($multiple): ?><span><?= site_escape($offer['sku'] . ($offer['variant'] === '' ? '' : ' · ' . $offer['variant'])) ?></span><?php endif; ?>
                                <div class="shop-price-input">
                                    <input name="price" inputmode="decimal" required maxlength="32" value="<?= shop_price_input((int) $offer['price_minor']) ?>"
                                        data-shop-money data-format="<?= $productLabel('price_format') ?>" data-positive="<?= $productLabel('positive_price') ?>"
                                        aria-label="<?= site_escape(shop_label('price') . ' · ' . $product['name'] . ' · ' . $offer['sku']) ?>"<?= $invalidPrice ? ' aria-invalid="true"' : '' ?>>
                                    <span><?= site_escape($shopState['currency'] === 'RUB' ? '₽' : $shopState['currency']) ?></span>
                                </div>
                                <span class="shop-price-status" role="status"<?= $invalidPrice ? ' data-error="true"' : '' ?>><?= $invalidPrice ? $productLabel('positive_price') : '' ?></span>
                            </form>
                        <?php endforeach; ?>
                        <?php if ($multiple): ?></details><?php endif; ?>
                    </td>
                    <?php foreach ($optional as $key => $name): if (isset($checked[$key])): ?>
                        <td>
                            <?php if ($key === 'image' && $product['image'] !== ''): ?>
                                <a class="shop-photo-link" href="<?= site_escape(shop_image_url($base, $product['image'])) ?>" data-shop-photo>
                                    <?= shop_image($base, $product['image'], $product['name']) ?>
                                </a>
                            <?php elseif (isset($numericColumns[$key])):
                                $meta = $numericColumns[$key];
                                $fieldId = $meta['id'];
                                $value = $productIndex['values'][$fieldId][$product['id']][0] ?? '';
                                if (shop_product_has_field($productIndex, $product['id'], $fieldId)): ?>
                                    <span data-shop-price-fallback><?= site_escape($value === '' ? '—' : $value) ?></span>
                                    <?php $renderInline('attribute', $product['id'], $value, shop_field_label($meta) . ' · ' . $product['name'], $fieldId, $meta['type']); ?>
                                <?php else: ?>—<?php endif; ?>
                            <?php elseif (($key === 'barcode' || $key === 'stock') && $productOffers):
                                $grouped = $multiple || ($key === 'stock' && $tables['warehouses']);
                                if ($grouped): ?><details class="shop-inline-group"><summary data-shop-inline-summary><?= site_escape($cells[$key]) ?></summary><?php
                                else: ?><span data-shop-price-fallback><?= site_escape($cells[$key]) ?></span><?php endif;
                                foreach ($productOffers as $offer):
                                    if ($grouped && $multiple): ?><small><?= site_escape($offer['sku']) ?></small><?php endif;
                                    $label = shop_label($name) . ' · ' . $offer['sku'];
                                    if ($key === 'stock') {
                                        $renderInline('stock', $offer['id'], $offer['stock'],
                                            $tables['warehouses'] ? shop_label('product_unassigned') . ' · ' . $offer['sku'] : $label);
                                        foreach ($tables['warehouses'] as $warehouse) {
                                            echo '<small>' . site_escape($warehouse['name']) . '</small>';
                                            $renderInline('stock', $offer['id'], $inventory[$offer['id']][$warehouse['id']] ?? '0',
                                                $label . ' · ' . $warehouse['name'], $warehouse['id']);
                                        }
                                    } else {
                                        $renderInline('barcode', $offer['id'], $barcodes[$offer['id']] ?? '', $label);
                                    }
                                endforeach;
                                if ($grouped): ?></details><?php endif; ?>
                            <?php else: ?><?= $key === 'image' ? '—' : site_escape($cells[$key]) ?><?php endif; ?>
                        </td>
                    <?php endif; endforeach; ?>
                    <td class="shop-actions-cell"><div class="shop-product-actions">
                        <?php foreach ($actionLabels as $action => list($icon, $text)): ?>
                            <a class="button" href="<?= site_escape($listUrl(['product_action'=>$action,'product'=>$product['id']])) ?>" title="<?= $productLabel($text) ?>"
                                aria-label="<?= site_escape(shop_label($text) . ' · ' . $product['name']) ?>"><i class="fa-solid fa-<?= $icon ?>" aria-hidden="true"></i></a>
                        <?php endforeach; ?>
                    </div></td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$matching): ?><tr><td colspan="<?= count($columns) + 2 ?>"><?= $productLabel('no_products') ?></td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if ($matching): ?>
        <form id="shop-bulk-products" class="header-actions shop-bulk-actions" method="post" action="<?= site_escape($listUrl()) ?>"
            data-shop-bulk data-confirm="<?= $productLabel('bulk_confirm') ?>">
            <input type="hidden" name="csrf" value="<?= site_escape($moduleToken) ?>">
            <input type="hidden" name="action" value="shop-product-bulk">
            <input type="hidden" name="revision" value="<?= site_escape($catalogRevision) ?>">
            <button type="submit" name="operation" value="activate"><?= $productLabel('bulk_activate') ?></button>
            <button type="submit" name="operation" value="deactivate"><?= $productLabel('bulk_deactivate') ?></button>
            <button class="button-danger" type="submit" name="operation" value="delete"><?= $productLabel('bulk_delete') ?></button>
            <noscript><label class="choice"><input type="checkbox" name="confirm" value="1"><?= $productLabel('bulk_confirm') ?></label></noscript>
        </form>
    <?php endif; ?>
    <?php if ($pages > 1): ?>
        <nav class="header-actions" aria-label="<?= $productLabel('pages') ?>">
            <?php foreach (array_unique(array_merge([1], range(max(1, $pageNumber - 2), min($pages, $pageNumber + 2)), [$pages])) as $number): ?>
                <a class="button<?= $number === $pageNumber ? ' button-primary' : '' ?>" href="<?= site_escape($listUrl(['p'=>$number])) ?>"<?= $number === $pageNumber ? ' aria-current="page"' : '' ?>><?= $number ?></a>
            <?php endforeach; ?>
        </nav>
    <?php endif; ?>
</section>
<dialog class="shop-photo-dialog" data-shop-photo-dialog aria-label="<?= $productLabel('product_image') ?>">
    <button type="button" class="dialog-close" data-shop-photo-close autofocus aria-label="<?= $productLabel('product_close') ?>"></button>
    <img alt="">
</dialog>
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
    $offer['price'] = shop_price_input((int) $offer['price_minor']);
    $offer['barcode'] = $copying ? '' : ($barcodes[$offerId] ?? '');
    $offer['inventory'] = $copying ? [] : ($inventory[$offerId] ?? []);
    $offer['image'] = $productIndex['offerMedia'][$offerId] ?? '';
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
$draftOffers['_new'] = ['sku'=>'','barcode'=>'','variant'=>'','price'=>'','stock'=>'0','active'=>'1','inventory'=>[],'image'=>''];
$postAction = $deleting ? 'shop-product-delete' : 'shop-product-save';
$failed = $error !== '' && shop_input($_POST, 'action') === $postAction;
if ($failed && !$deleting) {
    foreach (['name','category_id','slug','summary','description','image','type','active'] as $field) $draft[$field] = shop_input($_POST, $field);
    foreach ($draftOffers as $key => &$offer) {
        $entry = $_POST['offers'][$key] ?? null;
        if (is_array($entry)) {
            foreach (['sku','barcode','variant','price','stock','active','image'] as $field) $offer[$field] = shop_input($entry, $field);
            $offer['inventory'] = is_array($entry['inventory'] ?? null) ? array_filter($entry['inventory'], 'is_string') : [];
        }
    }
    unset($offer);
}
$title = $productCreating ? 'product_add' : $actionLabels[$productAction][1];
$seoTab = shop_input($_GET, 'product_tab') === 'seo';
$seoFields = ($failed && is_array($_POST['seo'] ?? null) ? $_POST['seo'] : ($productIndex['seo'][$productId] ?? []));
$seoLabels = ['title'=>'Title', 'heading'=>'H1', 'description'=>'Description', 'keywords'=>'Keywords'];
?>
<dialog class="editor-dialog shop-product-dialog" data-shop-product-dialog aria-labelledby="shop-product-dialog-title" open>
    <form class="form-stack" method="post" enctype="multipart/form-data" action="<?= site_escape($listUrl(['product_action'=>$productAction,'product'=>$productId] + ($seoTab ? ['product_tab'=>'seo'] : []))) ?>">
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
            <div class="site-menu shop-card-tabs" role="tablist" data-shop-card-tabs data-selected="<?= $seoTab ? 'seo' : 'main' ?>" hidden>
                <button type="button" role="tab" id="shop-tab-main" data-shop-card-tab="main" aria-controls="shop-card-main"><?= $productLabel('products') ?></button>
                <button type="button" role="tab" id="shop-tab-seo" data-shop-card-tab="seo" aria-controls="shop-card-seo">SEO</button>
            </div>
            <section class="form-stack" id="shop-card-main" data-shop-card-panel="main" aria-labelledby="shop-tab-main">
            <div class="fields">
                <label class="field field-wide"><?= $productLabel('product_name') ?><input name="name" required maxlength="1000" value="<?= site_escape($draft['name']) ?>"></label>
                <label class="field"><?= $productLabel('product_category') ?><select name="category_id" required>
                    <option value=""></option>
                    <?php foreach ($categories as $id => $name): ?><option value="<?= site_escape((string) $id) ?>" data-parent="<?= site_escape($categoryParents[$id]) ?>"<?= (string) $id === $draft['category_id'] ? ' selected' : '' ?>><?= site_escape($name) ?></option><?php endforeach; ?>
                </select></label>
                <label class="field"><?= $productLabel('product_slug') ?><input name="slug" maxlength="64" pattern="[a-z0-9][a-z0-9_\-]{0,63}" placeholder="<?= $productLabel('product_slug_auto') ?>" value="<?= site_escape($draft['slug']) ?>"></label>
                <label class="field"><?= $productLabel('product_type') ?><select name="type">
                    <?php foreach ($types as $type => $key): ?><option value="<?= $type ?>"<?= $type === $draft['type'] ? ' selected' : '' ?>><?= $productLabel($key) ?></option><?php endforeach; ?>
                </select></label>
                <label class="field"><?= $productLabel('status') ?><select name="active">
                    <?php foreach (['1'=>'product_active','0'=>'product_inactive'] as $value => $key): ?><option value="<?= $value ?>"<?= (string) $value === $draft['active'] ? ' selected' : '' ?>><?= $productLabel($key) ?></option><?php endforeach; ?>
                </select></label>
                <label class="field field-wide"><?= $productLabel('product_image') ?><input name="image" list="shop-product-images" value="<?= site_escape($draft['image']) ?>"></label>
                <datalist id="shop-product-images"><?php foreach (glob(SHOP_IMAGES_DIR . '/*') ?: [] as $file): if (shop_image_name(basename($file))): ?><option value="<?= site_escape(basename($file)) ?>"></option><?php endif; endforeach; ?></datalist>
                <label class="field field-wide"><?= $productLabel('summary') ?><textarea name="summary" rows="2" maxlength="5000"><?= site_escape($draft['summary']) ?></textarea></label>
            </div>
            <?php
            $characteristicDraft = $failed && is_array($_POST['characteristics'] ?? null)
                ? array_slice(array_values(array_filter($_POST['characteristics'], 'is_array')), 0, 25) : [];
            $fieldsById = array_column($tables['fields'], null, 'id');
            $existingCharacteristics = [];
            foreach ($tables['values'] as $entry) {
                if ($entry['target_id'] === $productId && ($fieldsById[$entry['field_id']]['target'] ?? '') === 'product') {
                    $existingCharacteristics[$entry['field_id']][] = $entry['value'];
                }
            }
            $renderCharacteristic = static function (string $number, array $entry) use ($productLabel): void {
                $fieldPrefix = 'characteristics[' . $number . ']';
                $selectedType = shop_input($entry, 'type') ?: 'text';
                ?>
                <label class="field"><?= $productLabel('product_name') ?>
                    <input name="<?= $fieldPrefix ?>[name]" data-characteristic-input="name" maxlength="180"
                        value="<?= site_escape(shop_input($entry, 'name')) ?>" list="shop-category-fields">
                </label>
                <label class="field"><?= $productLabel('characteristic_unit') ?>
                    <input name="<?= $fieldPrefix ?>[unit]" data-characteristic-input="unit" maxlength="80"
                        value="<?= site_escape(shop_input($entry, 'unit')) ?>">
                </label>
                <label class="field"><?= admin_l('Тип') ?>
                    <select name="<?= $fieldPrefix ?>[type]" data-characteristic-input="type">
                        <?php foreach (shop_field_types() as $type => $typeName): ?>
                            <option value="<?= site_escape($type) ?>"<?= $type === $selectedType ? ' selected' : '' ?>><?= admin_l($typeName) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label class="field"><?= $productLabel('characteristic_value') ?>
                    <textarea rows="1" name="<?= $fieldPrefix ?>[value]" maxlength="500"
                        data-characteristic-input="value"><?= "\n" . site_escape(shop_input($entry, 'value')) ?></textarea>
                </label>
                <?php
            };
            ?>
            <details class="panel shop-characteristics" data-shop-characteristics data-yes="<?= $productLabel('yes') ?>" data-no="<?= $productLabel('no') ?>"
                data-price-format="<?= $productLabel('price_format') ?>" data-positive="<?= $productLabel('positive_price') ?>"
                data-number-format="<?= $productLabel('number_format') ?>"<?= $characteristicDraft ? ' open' : '' ?>>
                <summary><i class="fa-solid fa-plus" aria-hidden="true"></i> <?= $productLabel('characteristic_add') ?></summary>
                <?php if ($existingCharacteristics): ?>
                    <dl class="shop-characteristic-current">
                        <?php foreach ($existingCharacteristics as $fieldId => $values): ?>
                            <div><dt><?= site_escape(shop_field_label($fieldsById[$fieldId])) ?></dt>
                                <dd><?= site_escape(implode(', ', $values)) ?></dd></div>
                        <?php endforeach; ?>
                    </dl>
                <?php endif; ?>
                <datalist id="shop-category-fields"></datalist>
                <template data-characteristic-options>
                    <?php foreach ($fieldsById as $field): if ($field['target'] === 'product'): ?>
                        <option value="<?= site_escape($field['name']) ?>" data-scope="<?= site_escape($field['category_id']) ?>"
                            data-unit="<?= site_escape($field['unit'] ?? '') ?>" data-type="<?= site_escape(shop_field_type($field['type'])) ?>"></option>
                    <?php endif; endforeach; ?>
                </template>
                <div data-characteristic-rows>
                    <?php foreach ($characteristicDraft ?: [[]] as $number => $entry): ?>
                        <div class="shop-characteristic-fields"><?php $renderCharacteristic((string) $number, $entry); ?></div>
                    <?php endforeach; ?>
                </div>
                <template data-characteristic-row><div class="shop-characteristic-fields"><?php $renderCharacteristic('0', []); ?></div></template>
                <button type="button" data-characteristic-add hidden><i class="fa-solid fa-plus" aria-hidden="true"></i> <?= $productLabel('characteristic_more') ?></button>
            </details>
            <div class="form-stack">
                <h3><?= $productLabel('offers') ?></h3>
                <?php foreach ($draftOffers as $id => $offer): ?>
                    <details class="panel shop-product-offer"<?= $id !== '_new' || count($draftOffers) === 1 || $failed ? ' open' : '' ?>>
                        <summary><?= $id === '_new' ? $productLabel('product_add_offer') : site_escape($offer['sku']) ?></summary>
                        <div class="fields">
                            <?php foreach (['sku'=>'product_sku','barcode'=>'product_barcode','price'=>'price','variant'=>'product_variant','stock'=>$tables['warehouses'] ? 'product_unassigned' : 'product_stock'] as $field => $key): ?>
                                <label class="field"><?= $productLabel($key) ?><input name="offers[<?= site_escape((string) $id) ?>][<?= $field ?>]" value="<?= site_escape($offer[$field]) ?>"
                                    <?php if ($field === 'price'): ?>inputmode="decimal" maxlength="32" data-shop-money data-format="<?= $productLabel('price_format') ?>" data-positive="<?= $productLabel('positive_price') ?>"
                                    <?php elseif (in_array($field, ['stock','barcode'], true)): ?>
                                        inputmode="numeric" data-shop-digits data-format="<?= $productLabel('digits_format') ?>"
                                        maxlength="<?= $field === 'stock' ? 8 : 128 ?>"
                                    <?php endif; ?>
                                    <?= $id !== '_new' && in_array($field, ['sku','price','stock'], true) ? 'required' : '' ?>></label>
                            <?php endforeach; ?>
                            <label class="field"><?= $productLabel('status') ?><select name="offers[<?= site_escape((string) $id) ?>][active]">
                                <?php foreach (['1'=>'product_active','0'=>'product_inactive'] as $value => $key): ?><option value="<?= $value ?>"<?= (string) $value === $offer['active'] ? ' selected' : '' ?>><?= $productLabel($key) ?></option><?php endforeach; ?>
                            </select></label>
                            <label class="field"><?= $productLabel('variant_photo') ?><input name="offers[<?= site_escape((string) $id) ?>][image]"
                                list="shop-product-images" value="<?= site_escape($offer['image']) ?>"></label>
                            <label class="field"><?= $productLabel('upload') ?><input type="file" name="offer_photo[<?= site_escape((string) $id) ?>]"
                                accept="image/jpeg,image/png,image/webp,image/gif,image/avif"></label>
                            <?php foreach ($tables['warehouses'] as $warehouse): ?>
                                <label class="field"><?= site_escape($warehouse['name']) ?>
                                    <input name="offers[<?= site_escape((string) $id) ?>][inventory][<?= site_escape($warehouse['id']) ?>]" inputmode="numeric"
                                        maxlength="8" data-shop-digits data-format="<?= $productLabel('digits_format') ?>"
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
            </section>
            <section class="form-stack" id="shop-card-seo" data-shop-card-panel="seo" aria-labelledby="shop-tab-seo">
                <div class="fields">
                    <?php foreach ($seoLabels as $key => $seoLabel): ?>
                        <label class="field<?= $key === 'description' || $key === 'keywords' ? ' field-wide' : '' ?>"><?= $seoLabel ?>
                            <input name="seo[<?= $key ?>]" maxlength="<?= SHOP_SEO_LIMITS[$key] ?>" value="<?= site_escape(shop_input($seoFields, $key)) ?>">
                        </label>
                    <?php endforeach; ?>
                    <label class="choice"><input type="checkbox" name="seo[noindex]" value="1"<?= shop_input($seoFields, 'noindex') === '1' ? ' checked' : '' ?>><?= $productLabel('seo_noindex') ?></label>
                </div>
            </section>
            <input type="hidden" name="complete" value="1">
        <?php endif; ?>
        <div class="header-actions">
            <button class="<?= $deleting ? 'button-danger' : 'button-primary' ?>" type="submit"><?= $productLabel($deleting ? 'remove' : 'save') ?></button>
            <a class="button" href="<?= site_escape($listUrl()) ?>" data-shop-product-close><?= $productLabel('product_cancel') ?></a>
        </div>
    </form>
</dialog>
