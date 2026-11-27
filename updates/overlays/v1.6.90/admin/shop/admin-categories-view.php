<?php
if (!defined('SITE_ROOT')) { http_response_code(404); exit; }
$categoryUrl = $tabUrl . '&sheet=categories';
$index = shop_index();
$categories = $index['categories'];
$productCounts = array_count_values(array_column($tables['products'], 'category_id'));
$used = array_fill_keys(array_merge(array_column($tables['placements'], 'category_id'), array_column($tables['fields'], 'category_id')), true);
$labels = static function (string $key): string { return site_escape(shop_label($key)); };
$categoryAction = shop_input($_GET, 'category_action');
$categoryId = shop_input($_GET, 'category_id');
$deleting = $categoryAction === 'delete';
$creating = $categoryAction === 'new';
$current = $categories[$categoryId] ?? null;
$edit = $creating || ($current && in_array($categoryAction, ['edit','delete'], true));
$draft = $creating ? ['name'=>'','slug'=>'','parent_id'=>$categoryId,'sort'=>'0','descendants'=>'1','description'=>''] : $current;
$visual = !$creating ? ($index['categoryMedia'][$categoryId] ?? []) : [];
$visualType = $visual['type'] ?? 'none';
$visualIcon = $visualType === 'icon' ? $visual['value'] : '';
$visualImage = $visualType === 'image' ? $visual['value'] : '';
$postAction = $deleting ? 'shop-category-delete' : 'shop-category-save';
$failed = $edit && $error !== '' && shop_input($_POST, 'action') === $postAction;
if ($failed && !$deleting) foreach (array_keys($draft) as $key) $draft[$key] = shop_input($_POST, $key);
if ($failed && !$deleting) {
    $visualType = shop_input($_POST, 'visual_type') ?: $visualType;
    $visualIcon = shop_input($_POST, 'icon');
    $visualImage = shop_input($_POST, 'image');
}
$flatten = [];
$append = static function (string $parent, int $depth) use (&$append, &$flatten, $index): void {
    foreach ($index['children'][$parent] ?? [] as $id) {
        $flatten[$id] = $depth;
        $append((string) $id, $depth + 1);
    }
};
$append('', 0);
?>
<section class="panel form-stack shop-category-editor" data-shop-category-editor data-url="<?= site_escape($categoryUrl) ?>"
    data-error="<?= $labels('try_again') ?>" data-server-error="<?= site_escape($error ?? '') ?>">
    <div class="header-actions shop-product-heading">
        <h2><?= $labels('categories') ?> <span class="shop-product-count"><?= count($categories) ?></span></h2>
        <a class="button button-primary" href="<?= site_escape($categoryUrl . '&category_action=new') ?>">
            <i class="fa-solid fa-plus" aria-hidden="true"></i> <?= $labels('category_add') ?>
        </a>
    </div>
    <p data-category-message class="notice error" role="status" hidden></p>
    <form hidden data-shop-category-sort-form method="post" action="<?= site_escape($categoryUrl) ?>">
        <input type="hidden" name="action" value="shop-category-move">
        <input type="hidden" name="csrf" value="<?= site_escape($moduleToken) ?>">
        <input type="hidden" name="revision" value="<?= site_escape($catalogRevision) ?>">
        <input type="hidden" name="id"><input type="hidden" name="target"><input type="hidden" name="position">
    </form>
    <?php if ($flatten): ?>
        <ul class="shop-admin-category-list">
            <?php foreach ($flatten as $key => $depth):
                $category = $categories[$key];
                $id = (string) $key;
                $url = $categoryUrl . '&category_id=' . rawurlencode($id) . '&category_action=';
                $path = implode('/', array_map(static function ($part) use ($categories): string { return $categories[$part]['slug']; }, $index['tree'][$id]));
                $locked = isset($used[$id]) || !empty($index['children'][$id]) || !empty($productCounts[$id]);
                ?>
                <li data-shop-category-id="<?= site_escape($id) ?>" data-shop-category-parent="<?= site_escape($category['parent_id']) ?>" style="--section-depth:<?= min(9, $depth) ?>">
                    <div class="shop-admin-category-name">
                        <button type="button" class="shop-column-move" data-shop-category-drag hidden title="<?= $labels('category_move') ?>" aria-label="<?= site_escape(shop_label('category_move') . ' · ' . $category['name']) ?>"><i class="fa-solid fa-grip-vertical" aria-hidden="true"></i></button>
                        <?php if (!empty($index['children'][$id])): ?>
                            <button type="button" class="shop-category-expander" data-shop-category-expand hidden aria-expanded="true"
                                title="<?= $labels('category_nested') ?>" aria-label="<?= site_escape(shop_label('category_nested') . ' · ' . $category['name']) ?>"><i class="fa-solid fa-chevron-down" aria-hidden="true"></i></button>
                        <?php else: ?><span class="shop-category-expander" aria-hidden="true"></span><?php endif; ?>
                        <?= shop_category_visual($base, $index['categoryMedia'][$id] ?? null) ?: '<i class="fa-regular fa-folder" aria-hidden="true"></i>' ?>
                        <div><strong><?= site_escape($category['name']) ?></strong><small><?= site_escape($path) ?></small></div>
                    </div>
                    <a class="shop-admin-category-count" href="<?= site_escape($moduleAdminUrl . '&tab=catalog&sheet=products&category=' . rawurlencode($id)) ?>"
                        title="<?= $labels('products') ?>"><?= site_format_number((string) ($productCounts[$id] ?? 0)) ?></a>
                    <div class="shop-product-actions">
                        <?php foreach (['new'=>['folder-plus','category_child'], 'edit'=>['pen','product_edit']] as $mode => list($icon, $text)): ?>
                            <a class="button" href="<?= site_escape($url . $mode) ?>" title="<?= $labels($text) ?>"
                                aria-label="<?= site_escape(shop_label($text) . ' · ' . $category['name']) ?>"><i class="fa-solid fa-<?= $icon ?>" aria-hidden="true"></i></a>
                        <?php endforeach; ?>
                        <a class="button" href="<?= site_escape(shop_route_url($base, $path)) ?>" target="_blank" rel="noopener" title="<?= $labels('category_open') ?>"
                            aria-label="<?= site_escape(shop_label('category_open') . ' · ' . $category['name']) ?>"><i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></a>
                        <?php if ($locked): ?>
                            <button type="button" disabled title="<?= $labels('category_not_empty') ?>" aria-label="<?= $labels('remove') ?>"><i class="fa-solid fa-trash" aria-hidden="true"></i></button>
                        <?php else: ?>
                            <a class="button" href="<?= site_escape($url . 'delete') ?>" title="<?= $labels('remove') ?>"
                                aria-label="<?= site_escape(shop_label('remove') . ' · ' . $category['name']) ?>"><i class="fa-solid fa-trash" aria-hidden="true"></i></a>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <?php if ($edit): ?>
        <dialog class="editor-dialog shop-product-dialog" data-shop-category-dialog aria-labelledby="shop-category-title" open>
            <form class="form-stack" method="post" enctype="multipart/form-data" action="<?= site_escape($categoryUrl . '&category_action=' . $categoryAction . '&category_id=' . rawurlencode($categoryId)) ?>">
                <div class="editor-dialog-heading header-actions">
                    <h3 id="shop-category-title"><?= $labels($deleting ? 'remove' : ($creating ? 'category_add' : 'category_edit')) ?></h3>
                    <a class="button" data-category-close href="<?= site_escape($categoryUrl) ?>" aria-label="<?= $labels('product_close') ?>">×</a>
                </div>
                <?php if ($failed): ?><p class="notice error" role="alert"><?= site_escape($error) ?></p><?php endif; ?>
                <input type="hidden" name="action" value="<?= $postAction ?>">
                <input type="hidden" name="csrf" value="<?= site_escape($moduleToken) ?>">
                <input type="hidden" name="id" value="<?= $creating ? '' : site_escape($categoryId) ?>">
                <input type="hidden" name="revision" value="<?= site_escape($failed ? shop_input($_POST, 'revision') : $catalogRevision) ?>">
                <?php if ($deleting): ?>
                    <input type="hidden" name="confirm" value="1">
                    <p><?= $labels('category_delete') ?> <strong><?= site_escape($current['name']) ?></strong></p>
                <?php else: ?>
                    <div class="fields">
                        <label class="field field-wide"><?= $labels('product_name') ?><input name="name" required maxlength="240" value="<?= site_escape($draft['name']) ?>" autofocus></label>
                        <label class="field"><?= $labels('category_parent') ?><select name="parent_id">
                            <option value=""><?= $labels('category_root') ?></option>
                            <?php foreach ($flatten as $id => $depth):
                                if (!$creating && in_array($categoryId, array_map('strval', $index['tree'][$id]), true)) continue;
                                ?>
                                <option value="<?= site_escape((string) $id) ?>"<?= (string) $id === $draft['parent_id'] ? ' selected' : '' ?>><?= site_escape(str_repeat('— ', $depth) . $categories[$id]['name']) ?></option>
                            <?php endforeach; ?>
                        </select></label>
                        <label class="field"><?= $labels('category_slug') ?><input name="slug" maxlength="64" pattern="[a-z0-9][a-z0-9_\-]{0,63}"
                            placeholder="<?= $labels('product_slug_auto') ?>" value="<?= site_escape($draft['slug']) ?>"></label>
                        <label class="field"><?= $labels('category_order') ?><input type="number" name="sort" max="99999999" min="-99999999" required value="<?= site_escape($draft['sort']) ?>"></label>
                        <label class="choice"><input type="checkbox" name="descendants" value="1"<?= $draft['descendants'] === '1' ? ' checked' : '' ?>><?= $labels('category_nested') ?></label>
                        <label class="field"><?= $labels('category_visual') ?>
                            <select name="visual_type" data-category-visual-type>
                                <?php foreach (['none'=>'—','icon'=>shop_label('category_icon'),'image'=>shop_label('product_image')] as $type => $visualLabel): ?>
                                    <option value="<?= $type ?>"<?= $type === $visualType ? ' selected' : '' ?>><?= site_escape($visualLabel) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <div class="field" data-category-visual="icon">
                            <label><?= $labels('category_icon') ?> <input name="icon" maxlength="90" value="<?= site_escape($visualIcon) ?>" placeholder="[fa-icon=folder]"></label>
                            <div class="header-actions">
                                <?php foreach (['folder','screwdriver-wrench','hammer','bolt','leaf','seedling','box','tags','gift','shirt','house','cart-shopping'] as $choice): ?>
                                    <button type="button" data-category-icon="[fa-icon=<?= $choice ?>]" title="<?= $choice ?>" aria-label="<?= $choice ?>"><i class="fa-solid fa-<?= $choice ?>" aria-hidden="true"></i></button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <div class="field" data-category-visual="image">
                            <label><?= $labels('product_image') ?> <input name="image" list="shop-category-images" value="<?= site_escape($visualImage) ?>"></label>
                            <datalist id="shop-category-images"><?php foreach (glob(SHOP_IMAGES_DIR . '/*') ?: [] as $file): if (shop_image_name(basename($file))): ?><option value="<?= site_escape(basename($file)) ?>"></option><?php endif; endforeach; ?></datalist>
                            <label><?= $labels('upload') ?> <input type="file" name="category_photo" accept="image/jpeg,image/png,image/webp,image/gif,image/avif"></label>
                        </div>
                        <label class="field field-wide"><?= $labels('description') ?><textarea name="description" rows="3" maxlength="5000"><?= "\n" . site_escape($draft['description']) ?></textarea></label>
                    </div>
                <?php endif; ?>
                <div class="header-actions">
                    <button type="submit" class="<?= $deleting ? 'button-danger' : 'button-primary' ?>"><?= $labels($deleting ? 'remove' : 'save') ?></button>
                    <a class="button" data-category-close href="<?= site_escape($categoryUrl) ?>"><?= $labels('product_cancel') ?></a>
                </div>
            </form>
        </dialog>
    <?php endif; ?>
</section>
