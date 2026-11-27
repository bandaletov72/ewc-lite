<?php
if (!defined('SITE_ROOT')) { http_response_code(404); exit; }
require_once __DIR__ . '/admin-records.php';
$titles = shop_admin_record_titles()[$table] ?? [];
if (!$titles) return;
$label = static function (string $key): string { return site_escape(shop_label($key)); };
$productsById = array_column($tables['products'], null, 'id');
$productsList = array_column($tables['products'], 'name', 'id');
$categoryList = array_column($tables['categories'], 'name', 'id');
$offerList = $kitList = $componentList = [];
foreach ($tables['offers'] as $offer) {
    $product = $productsById[$offer['product_id']];
    $name = implode(' · ', array_filter([$product['name'], $offer['variant'], $offer['sku']], 'strlen'));
    $offerList[$offer['id']] = $name;
    if ($product['type'] === 'kit') $kitList[$offer['id']] = $name;
    else $componentList[$offer['id']] = $name;
}
foreach (['productsList','categoryList','offerList','kitList','componentList'] as $list) asort($$list, SORT_NATURAL | SORT_FLAG_CASE);
$fieldTypes = shop_field_types();
$fieldsById = array_column($tables['fields'], null, 'id');
$fieldList = array_map('shop_field_label', $fieldsById);
$targetList = [];
foreach (['product'=>$productsList,'offer'=>$offerList] as $type => $choices) {
    foreach ($choices as $id => $name) $targetList[$type . ':' . $id] = $name;
}
$optionLists = [
    'product_id'=>$productsList, 'category_id'=>($table === 'fields' ? [''=>shop_label('record_all_categories')] : []) + $categoryList,
    'offer_id'=>$table === 'kits' ? $kitList : $offerList, 'component_id'=>$componentList,
    'field_id'=>$fieldList, 'target_id'=>$targetList, 'target'=>['product'=>shop_label('products'),'offer'=>shop_label('variants')],
];
if ($table === 'fields') $optionLists['type'] = $fieldTypes;
$priceNames = $shopState['priceLabels'] ?? [];
$viewValue = static function (string $column, array $row) use ($optionLists, $fieldsById, $priceNames, $shopState): string {
    $value = $row[$column];
    if ($column === 'price_minor') return shop_money((int) $value, $shopState);
    if ($column === 'active') return shop_label($value === '1' ? 'product_active' : 'product_inactive');
    if (in_array($column, ['filter','sort'], true)) return shop_label($value === '1' ? 'yes' : 'no');
    if ($column === 'type') return $optionLists['type'][shop_field_type($value)] ?? $priceNames[$value] ?? $value;
    if ($column === 'target_id') $value = ($fieldsById[$row['field_id']]['target'] ?? '') . ':' . $value;
    if ($column === 'value' && shop_field_type($fieldsById[$row['field_id']]['type'] ?? '') === 'bool') return shop_label($value === '1' ? 'yes' : 'no');
    return $optionLists[$column][$value] ?? $value;
};
$needle = shop_lower(site_search_query($_GET['q'] ?? ''));
$matches = [];
foreach ($rows as $position => $row) {
    $searchable = [];
    if ($needle !== '') {
        foreach (array_keys($titles) as $column) $searchable[] = $viewValue($column, $row);
    }
    if ($needle === '' || strpos(shop_lower(implode(' ', $searchable)), $needle) !== false) $matches[$position] = $row;
}
$pages = max(1, (int) ceil(count($matches) / 20));
$recordPage = min($pages, max(1, (int) shop_input($_GET, 'p')));
$recordUrl = $tabUrl . '&sheet=' . $table;
$filteredUrl = $recordUrl . ($needle === '' ? '' : '&q=' . rawurlencode(site_search_query($_GET['q'] ?? '')));
$returnUrl = $filteredUrl . '&p=' . $recordPage;
$record = shop_input($_GET, 'record');
$editing = $table !== 'offers' && ($record === '_new' || (ctype_digit($record) && isset($rows[(int) $record])));
$deleting = $editing && $record !== '_new' && shop_input($_GET, 'record_action') === 'delete';
$draft = $editing && $record !== '_new' ? $rows[(int) $record] : [];
if ($table === 'values' && $draft) $draft['target_id'] = ($fieldsById[$draft['field_id']]['target'] ?? '') . ':' . $draft['target_id'];
if ($table === 'prices' && $draft) $draft['price_minor'] = shop_price_input((int) $draft['price_minor']);
if ($table === 'fields' && $draft) $draft['type'] = shop_field_type($draft['type']);
if ($editing && ($error ?? '') !== '' && shop_input($_POST, 'action') === 'shop-record-save') {
    foreach (array_keys($titles) as $column) $draft[$column] = shop_input($_POST, $column);
}
?>
<section class="panel shop-records">
    <div class="admin-heading">
        <h2><?= $label($sheetNames[$table]) ?> <span class="count-badge"><?= count($matches) ?></span></h2>
        <?php if ($table !== 'offers'): ?><a class="button button-primary" href="<?= site_escape($returnUrl . '&record=_new#shop-record-editor') ?>"><i class="fa-solid fa-plus" aria-hidden="true"></i> <?= $label('record_add') ?></a><?php endif; ?>
    </div>
    <p class="hint"><?= $label('help_' . $table) ?></p>
    <?php if ($editing): ?>
        <form id="shop-record-editor" class="shop-record-editor form-stack" method="post" data-shop-record-sheet="<?= $table ?>"
            action="<?= site_escape($returnUrl . '&record=' . rawurlencode($record) . ($deleting ? '&record_action=delete' : '')) ?>">
            <input type="hidden" name="csrf" value="<?= site_escape($moduleToken) ?>">
            <input type="hidden" name="revision" value="<?= site_escape($catalogRevision) ?>">
            <input type="hidden" name="action" value="<?= $deleting ? 'shop-record-delete' : 'shop-record-save' ?>">
            <input type="hidden" name="sheet" value="<?= $table ?>">
            <input type="hidden" name="record" value="<?= $record === '_new' ? '' : site_escape($record) ?>">
            <h3><?= $label($deleting ? 'remove' : ($record === '_new' ? 'record_add' : 'record_edit')) ?></h3>
            <?php if ($deleting): ?>
                <p><?= site_escape(implode(' · ', array_map(static function (string $column) use ($viewValue, $rows, $record): string {
                    return $viewValue($column, $rows[(int) $record]);
                }, array_keys($titles)))) ?></p>
                <input type="hidden" name="confirm" value="1">
            <?php else: ?>
                <div class="shop-record-fields">
                    <?php foreach ($titles as $column => $key):
                        $value = $draft[$column] ?? '';
                        if (in_array($column, ['filter','sort'], true)): ?>
                            <label class="choice"><input type="checkbox" name="<?= $column ?>" value="1"<?= $value === '1' ? ' checked' : '' ?>><?= $label($key) ?></label>
                        <?php elseif (isset($optionLists[$column])): ?>
                            <label class="field"><?= $label($key) ?><select name="<?= $column ?>"<?= $column === 'category_id' && $table === 'fields' ? '' : ' required' ?>>
                                <?php if (!array_key_exists('', $optionLists[$column])): ?><option value=""></option><?php endif; ?>
                                <?php foreach ($optionLists[$column] as $id => $name): ?>
                                    <option value="<?= site_escape((string) $id) ?>"<?= (string) $id === $value ? ' selected' : '' ?>
                                        <?= $column === 'field_id' ? 'data-target="' . site_escape($fieldsById[$id]['target']) . '" data-type="' . site_escape(shop_field_type($fieldsById[$id]['type'])) . '"' : '' ?>><?= site_escape($name) ?></option>
                                <?php endforeach; ?>
                            </select></label>
                        <?php elseif ($column === 'image' && $moduleOwner): ?>
                            <div class="field-wide"><?php shop_admin_photo_field($base, 'image', $value, shop_label($key), true); ?></div>
                        <?php elseif ($column === 'value'): ?>
                            <label class="field"><?= $label($key) ?>
                                <input name="value" data-record-text value="<?= site_escape($value) ?>" maxlength="500" required>
                                <select name="value" data-record-bool hidden disabled required>
                                    <option value=""></option>
                                    <option value="1"<?= $value === '1' ? ' selected' : '' ?>><?= $label('yes') ?></option>
                                    <option value="0"<?= $value === '0' ? ' selected' : '' ?>><?= $label('no') ?></option>
                                </select>
                            </label>
                        <?php else: ?>
                            <label class="field"><?= $label($key) ?><input name="<?= $column ?>" value="<?= site_escape($value) ?>"
                                <?= in_array($column, ['unit','alt','icon'], true) ? '' : 'required' ?>
                                <?= $column === 'icon' ? 'placeholder="[fa-icon=tag]" spellcheck="false"' : '' ?>
                                <?= $column === 'price_minor' ? 'inputmode="decimal"' : '' ?>
                                <?= $column === 'quantity' ? 'type="number" min="1" max="9999"' : '' ?>
                                <?= $column === 'type' && $table === 'prices' ? 'list="shop-price-types"' : '' ?>
                                <?= in_array($column, ['slug','type'], true) ? 'pattern="[a-z0-9][a-z0-9_\-]{0,63}"' : '' ?>
                                maxlength="<?= $column === 'unit' ? '80' : '500' ?>"></label>
                        <?php endif; endforeach; ?>
                </div>
                <?php if ($table === 'prices'): ?><datalist id="shop-price-types">
                    <?php foreach ($priceNames as $id => $name): ?><option value="<?= site_escape((string) $id) ?>"><?= site_escape($name) ?></option><?php endforeach; ?>
                </datalist><?php endif; ?>
            <?php endif; ?>
            <div class="header-actions">
                <button type="submit" class="<?= $deleting ? 'button-danger' : 'button-primary' ?>"><?= $label($deleting ? 'remove' : 'save') ?></button>
                <a class="button" href="<?= site_escape($returnUrl) ?>"><?= $label('product_cancel') ?></a>
            </div>
        </form>
    <?php endif; ?>
    <form method="get" action="<?= site_escape(strtok($moduleAdminUrl, '?')) ?>" class="shop-compact-search">
        <input type="hidden" name="<?= $moduleOwner ? 'shop' : 'staff' ?>" value="1">
        <input type="hidden" name="tab" value="catalog"><input type="hidden" name="sheet" value="<?= $table ?>">
        <label class="field"><?= $label('search') ?><input type="search" name="q" value="<?= site_escape(site_search_query($_GET['q'] ?? '')) ?>"></label>
        <button type="submit"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i> <?= $label('record_find') ?></button>
        <?php if ($needle !== ''): ?><a href="<?= site_escape($recordUrl) ?>"><?= $label('filter_reset') ?></a><?php endif; ?>
    </form>
    <?php if ($matches): ?>
        <div class="shop-report-table"><table class="shop-record-table">
            <thead><tr><?php foreach ($titles as $key): ?><th scope="col"><?= $label($key) ?></th><?php endforeach; ?>
                <?php if ($table !== 'offers' || $moduleOwner): ?><th scope="col"><?= $label('product_actions') ?></th><?php endif; ?></tr></thead>
            <tbody><?php foreach (array_slice($matches, ($recordPage - 1) * 20, 20, true) as $position => $row): ?><tr>
                <?php foreach (array_keys($titles) as $column): $display = $viewValue($column, $row); ?>
                    <td><?php if ($column === 'image'): ?>
                        <a class="shop-record-photo" href="<?= site_escape(shop_image_url($base, $row['image'])) ?>" target="_blank" rel="noopener"><?= shop_image($base, $row['image'], $row['alt']) ?></a>
                    <?php elseif ($column === 'slug'): ?>
                        <code><?= site_escape(strpos($row['slug'], '/') !== false ? site_url($base, $row['slug'] . '/') : $row['slug']) ?></code>
                    <?php elseif ($table === 'aliases' && $column === 'product_id'): ?>
                        <a href="<?= site_escape(shop_product_url($base, shop_index(), $productsById[$row['product_id']])) ?>" target="_blank" rel="noopener"><?= site_escape($display) ?></a>
                    <?php elseif ($column === 'active'): ?><span class="shop-order-status"><?= site_escape($display) ?></span>
                    <?php else: ?><?= site_escape($display === '' ? '—' : $display) ?><?php endif; ?></td>
                <?php endforeach; ?>
                <?php if ($table === 'offers' && $moduleOwner): ?>
                    <td><a class="button" href="<?= site_escape($tabUrl . '&sheet=products&product_action=edit&product=' . rawurlencode($row['product_id']) . '&product_tab=variants&offer=' . rawurlencode($row['id'])) ?>">
                        <i class="fa-solid fa-pen" aria-hidden="true"></i> <?= $label('record_edit') ?></a></td>
                <?php elseif ($table !== 'offers'): ?>
                    <td><div class="shop-record-actions">
                        <a class="button" href="<?= site_escape($returnUrl . '&record=' . $position . '#shop-record-editor') ?>" aria-label="<?= $label('record_edit') ?>" title="<?= $label('record_edit') ?>"><i class="fa-solid fa-pen" aria-hidden="true"></i></a>
                        <a class="button" href="<?= site_escape($returnUrl . '&record=' . $position . '&record_action=delete#shop-record-editor') ?>" aria-label="<?= $label('remove') ?>" title="<?= $label('remove') ?>"><i class="fa-solid fa-trash" aria-hidden="true"></i></a>
                    </div></td>
                <?php endif; ?>
            </tr><?php endforeach; ?></tbody>
        </table></div>
    <?php else: ?><p class="shop-empty"><?= $label('record_empty') ?></p><?php endif; ?>
    <?php if ($pages > 1): ?><nav class="shop-order-pages" aria-label="<?= $label('pages') ?>">
        <?php if ($recordPage > 1): ?><a class="button" href="<?= site_escape($filteredUrl . '&p=' . ($recordPage - 1)) ?>"><?= $label('gallery_prev') ?></a><?php endif; ?>
        <span aria-current="page"><?= $recordPage ?> / <?= $pages ?></span>
        <?php if ($recordPage < $pages): ?><a class="button" href="<?= site_escape($filteredUrl . '&p=' . ($recordPage + 1)) ?>"><?= $label('gallery_next') ?></a><?php endif; ?>
    </nav><?php endif; ?>
</section>
