<?php
if (!defined('SITE_ROOT')) { http_response_code(404); exit; }

function shop_admin_record_titles(): array
{
    return [
        'offers'=>['product_id'=>'products','variant'=>'product_variant','sku'=>'product_sku','price_minor'=>'price','stock'=>'product_stock','active'=>'status'],
        'fields'=>['name'=>'product_name','category_id'=>'categories','type'=>'product_type','target'=>'record_target','unit'=>'characteristic_unit','icon'=>'field_icon','filter'=>'filters','sort'=>'sort'],
        'placements'=>['product_id'=>'products','category_id'=>'categories'],
        'images'=>['product_id'=>'products','image'=>'product_image','alt'=>'description'],
        'prices'=>['offer_id'=>'product_variant','type'=>'prices','price_minor'=>'price'],
        'kits'=>['offer_id'=>'kits','component_id'=>'record_component','quantity'=>'quantity'],
        'aliases'=>['slug'=>'aliases','product_id'=>'products'],
        'values'=>['field_id'=>'fields','target_id'=>'record_target','value'=>'characteristic_value'],
    ];
}

function shop_admin_record_change(array &$tables, array $input): void
{
    $sheet = shop_input($input, 'sheet');
    if ($sheet === 'offers' || !isset(shop_admin_record_titles()[$sheet])) throw shop_error('row');
    $position = shop_input($input, 'record');
    $rows = &$tables[$sheet];
    if ($position !== '' && (!ctype_digit($position) || !isset($rows[(int) $position]))) throw shop_error('not_found');
    if (shop_input($input, 'action') === 'shop-record-delete') {
        if ($position === '' || shop_input($input, 'confirm') !== '1') throw shop_error('row');
        array_splice($rows, (int) $position, 1);
        return;
    }
    $row = [];
    foreach (SHOP_COLUMNS[$sheet] as $column) {
        $row[$column] = shop_input($input, $column);
        if (strlen($row[$column]) > 1500 || preg_match('/[\x00-\x1f\x7f]/u', $row[$column]) !== 0) throw shop_error('row', $column);
    }
    if ($sheet === 'fields') {
        $row['id'] = $position === '' ? 'field-' . bin2hex(random_bytes(8)) : $rows[(int) $position]['id'];
        foreach (['filter','sort'] as $flag) $row[$flag] = $row[$flag] === '1' ? '1' : '0';
    } elseif ($sheet === 'prices') {
        $row['price_minor'] = shop_positive_price(shop_import_amount(str_replace(',', '.', $row['price_minor'])));
    } elseif ($sheet === 'values') {
        [$target, $id] = array_pad(explode(':', $row['target_id'], 2), 2, '');
        $fields = array_column($tables['fields'], 'target', 'id');
        if ($target !== ($fields[$row['field_id']] ?? null) || !shop_id($id)) throw shop_error('row', shop_label('record_target'));
        $row['target_id'] = $id;
    }
    $keys = [
        'placements'=>['product_id','category_id'], 'images'=>['product_id','image'], 'prices'=>['offer_id','type'],
        'kits'=>['offer_id','component_id'], 'aliases'=>['slug'], 'values'=>['field_id','target_id','value'],
    ][$sheet] ?? [];
    foreach ($rows as $index => $existing) {
        if ($position !== '' && $index === (int) $position) continue;
        if ($keys && !array_diff_assoc(array_intersect_key($row, array_flip($keys)), $existing)) {
            throw shop_error('record_duplicate');
        }
    }
    if ($position === '') $rows[] = $row;
    else $rows[(int) $position] = $row;
}

function shop_admin_photo_field(string $base, string $name, string $value, string $label, bool $required = false): void
{
    $title = site_escape(shop_label('media_pick'));
    ?>
    <div class="shop-photo-field" data-shop-photo-field>
        <div class="shop-photo-field-row">
            <img alt="" data-shop-field-preview<?= $value !== '' && shop_image_valid($value) ? ' src="' . site_escape(shop_image_url($base, $value)) . '"' : ' hidden' ?>>
            <div><strong><?= site_escape($label) ?></strong><small data-shop-photo-name><?= site_escape($value === '' ? '' : basename($value)) ?></small></div>
            <button type="button" data-shop-choose-image><i class="fa-regular fa-folder-open" aria-hidden="true"></i> <?= $title ?></button>
            <?php if (!$required): ?>
                <button type="button" data-shop-photo-clear title="<?= site_escape(shop_label('remove')) ?>" aria-label="<?= site_escape(shop_label('remove') . ' · ' . $label) ?>">
                    <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                </button>
            <?php endif; ?>
        </div>
        <details><summary>URL</summary>
            <label class="field"><?= site_escape($label) ?>
                <input name="<?= site_escape($name) ?>" value="<?= site_escape($value) ?>" data-shop-field-value<?= $required ? ' required' : '' ?>>
            </label>
        </details>
    </div>
    <?php
}
