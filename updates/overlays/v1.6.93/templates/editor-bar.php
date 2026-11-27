<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}
$editText = static function (string $text) use ($editor): string {
    return site_escape(site_translation($text, $editor['language']));
};
$editAdmin = site_url($base, 'admin/');
$shopEditRoute = $components['shopRoute'] ?? null;
$editPath = $searchMode || $shopEditRoute !== null ? '' : $page['path'];
$editGroups = $editPath === '' ? [] : site_page_edit_groups($page, $components['extensions'], $snippets);
$editUrl = site_public_page_edit_url($base, $editPath);
$editParent = $editPath !== '' && dirname($originalPath) !== '.' ? dirname($originalPath) : '';
$editHidden = '<input type="hidden" name="csrf" value="' . site_escape($editor['csrf']) . '">'
    . '<input type="hidden" name="path" value="' . site_escape($editPath) . '">'
    . '<input type="hidden" name="revision" value="' . site_escape($page['revision'] ?? '') . '">';
$editIcon = static function (string $icon, string $label): string {
    return '<i class="fa-solid fa-' . $icon . '" aria-hidden="true"></i><span class="toolbar-label">' . $label . '</span>';
};
$editLink = static function (string $url, string $icon, string $name, bool $opensEditor = false) use ($editIcon, $editText): string {
    $label = $editText($name);
    return '<a class="toolbar-item" href="' . site_escape($url) . '" data-nav-tooltip="' . $label . '"'
        . ($opensEditor ? ' data-page-editor-link aria-haspopup="dialog"' : '') . '>' . $editIcon($icon, $label) . '</a>';
};
$shopEdit = [];
if (is_array($shopEditRoute) && site_shop_enabled() && function_exists('shop_live_editor_link')) {
    $shopIndex = shop_index();
    $product = $shopEditRoute['product'] ?? '';
    $brand = ($shopEditRoute['brand'] ?? '') ?: shop_input($_GET, 'b');
    $category = ($shopEditRoute['category'] ?? '') ?: shop_input($_GET, 'c');
    $editCategory = $category !== '' && isset($shopIndex['categories'][$category]);
    if ($product !== '' && isset($shopIndex['products'][$product])) {
        $shopEdit[] = ['product', $product, true];
        $shopEdit[] = ['seo', $product, true];
        if ($editCategory) $shopEdit[] = ['category', $category, false];
    } elseif ($brand !== '' && isset($shopIndex['brands'][$brand])) {
        $shopEdit[] = ['brand', $brand, false];
    } elseif ($editCategory) {
        $shopEdit[] = ['category', $category, ($shopEditRoute['category'] ?? '') !== ''];
    }
    if (!empty($components['shopPageTemplate'])) $shopEdit[] = ['template', $components['shopPageTemplate'], false];
}
?>
<nav class="editor-bar<?= $shopEditRoute === null ? '' : ' shop-editor-bar' ?>" aria-label="<?= $editText('Панель администратора') ?>">
    <div class="editor-bar-actions">
    <?= $editLink($editAdmin, 'gear', 'Админка') ?>
    <?php foreach ($shopEdit as list($kind, $id, $current)): ?>
        <?= shop_live_editor_link($base, $kind, $id, $current, true) ?>
    <?php endforeach; ?>
    <?php if ($editPath !== ''): ?>
        <?= $editLink($editUrl, 'pen-to-square', 'Править страницу', true) ?>
        <?= $editLink($editAdmin . '?new=1&parent=' . rawurlencode($editParent), 'folder-plus', 'Новый раздел') ?>
        <?php foreach (['page-inline-editor' => ['puzzle-piece', 'Компоненты страницы'],
            'page-layout-list' => ['palette', 'Шаблоны оформления'], 'page-inline-snippets' => ['tags', 'Подшаблоны']] as $dialog => list($icon, $name)): ?>
            <button class="toolbar-item" type="button" aria-haspopup="dialog" data-nav-tooltip="<?= $editText($name) ?>" data-show-page-dialog="<?= $dialog ?>"><?= $editIcon($icon, $editText($name)) ?></button>
        <?php endforeach; ?>
        <?php foreach (['CSS' => 'paintbrush', 'JS' => 'code'] as $asset => $icon): ?>
            <button class="toolbar-item" type="button" data-nav-tooltip="<?= $asset ?>" data-show-page-dialog="page-asset-<?= $asset ?>"><?= $editIcon($icon, $asset) ?></button>
        <?php endforeach; ?>
        <?php $inMenu = $page['fields']['MenuVisible'] === '1'; ?>
        <?php $menuLabel = $editText($inMenu ? 'Убрать из меню' : 'Показать в меню'); ?>
        <form method="post" action="<?= site_escape($editAdmin) ?>">
            <?= $editHidden ?>
            <input type="hidden" name="visible" value="<?= $inMenu ? '0' : '1' ?>">
            <button class="toolbar-item" name="action" value="live-page-menu" data-nav-tooltip="<?= $menuLabel ?>"><?= $editIcon('list', $menuLabel) ?></button>
        </form>
        <?= $editLink($editUrl . '&confirm=hide', 'eye-slash', 'Скрыть') ?>
        <?php if ($editPath !== 'index.php'): ?>
            <?= $editLink($editUrl . '&confirm=delete', 'trash', 'Удалить') ?>
        <?php endif; ?>
    <?php endif; ?>
    </div>
    <form class="editor-bar-toggle" method="post" action="<?= site_escape($editAdmin) ?>">
        <?= $editHidden ?>
        <?php if ($shopEditRoute !== null):
            $backQuery = http_build_query(array_intersect_key($_GET, array_flip(['c','b','q','sort','layout','price_min','price_max','f','min','max','p','view'])), '', '&', PHP_QUERY_RFC3986);
            ?>
            <input type="hidden" name="shop_back" value="<?= site_escape(($components['shopUrl'] ?? site_url($base, site_shop_public_folder() . '/')) . ($backQuery === '' ? '' : '?' . $backQuery)) ?>">
        <?php endif; ?>
        <input type="hidden" name="enabled" value="<?= $editor['edit'] ? '0' : '1' ?>">
        <button class="editor-switch" name="action" value="public-edit-mode" role="switch" aria-checked="<?= $editor['edit'] ? 'true' : 'false' ?>">
            <span><?= $editText('Режим правки') ?></span><span class="editor-switch-track" aria-hidden="true"></span>
        </button>
    </form>
</nav>
<?php if ($shopEditRoute !== null && function_exists('shop_live_editor_link')): ?>
    <dialog class="editor-dialog component-data-dialog shop-live-dialog" id="page-shop-editor" aria-labelledby="page-shop-editor-title">
        <div class="header-actions editor-dialog-heading">
            <h2 id="page-shop-editor-title"><?= site_escape(shop_label('marketplace')) ?></h2>
            <button class="dialog-close" type="button" data-close-page-dialog aria-label="<?= $editText('Закрыть') ?>"></button>
        </div>
        <?php $frameLabels = site_component_frame_labels($editor['language']); ?>
        <div class="form-stack" data-component-frame-status hidden>
            <p class="notice" role="status" data-component-frame-message
                data-loading="<?= site_escape($frameLabels[0]) ?>" data-error="<?= site_escape($frameLabels[1]) ?>"></p>
            <div><button type="button" data-component-frame-retry hidden><?= site_escape($frameLabels[2]) ?></button></div>
        </div>
        <iframe data-component-data-iframe title="<?= site_escape(shop_label('marketplace')) ?>"></iframe>
    </dialog>
<?php endif; ?>
<?php if ($editPath !== ''): ?>
    <?php foreach (['CSS' => 'css', 'JS' => 'javascript'] as $asset => $syntax): ?>
        <dialog class="editor-dialog" id="page-asset-<?= $asset ?>" aria-labelledby="page-asset-title-<?= $asset ?>">
            <form class="form-stack" method="post" action="<?= site_escape($editAdmin) ?>">
                <div class="header-actions editor-dialog-heading">
                    <h2 id="page-asset-title-<?= $asset ?>"><?= $asset ?> · <?= site_escape($heading) ?></h2>
                    <button class="dialog-close" type="button" data-close-page-dialog aria-label="<?= $editText('Закрыть') ?>"></button>
                </div>
                <?= $editHidden ?>
                <input type="hidden" name="asset" value="<?= $asset ?>">
                <textarea name="code" aria-label="<?= $asset ?>" class="short-editor" data-code-mode="<?= $syntax ?>" spellcheck="false" rows="16"><?= "\n" . site_escape(site_asset_decode($page['fields'][$asset] ?? '') ?? '') ?></textarea>
                <div><button class="button-primary" name="action" value="live-page-asset"><?= $editText('Сохранить') ?></button></div>
            </form>
        </dialog>
    <?php endforeach; ?>
<?php endif; ?>
<?php if ($inline): ?>
    <dialog class="editor-dialog component-data-dialog" id="page-banner-editor" aria-labelledby="page-banner-editor-label">
        <div class="header-actions editor-dialog-heading">
            <h2 id="page-banner-editor-label"><?= site_escape(site_banner_edit_slide_label($editor['language'])) ?></h2>
            <a class="button" href="<?= site_escape($editAdmin . '?components=1#banners') ?>" target="_blank" rel="noopener"><?= site_escape(site_banner_defaults_label($editor['language'])) ?></a>
            <button class="dialog-close" type="button" data-close-page-dialog aria-label="<?= $editText('Закрыть') ?>"></button>
        </div>
        <iframe data-component-data-iframe title="<?= site_escape(site_banner_edit_slide_label($editor['language'])) ?>"></iframe>
    </dialog>
<?php endif; ?>
<?php if ($editPath !== ''): ?>
    <?php
    list($editDataText, $editWideText) = site_data_editor_labels($editor['language']);
    $editSettingsText = site_translation('Настройки', $editor['language']);
    $hasDataTarget = false;
    ?>
    <?php foreach (['components' => $editText('Компоненты страницы'), 'snippets' => $editText('Подшаблоны')] as $group => $label): ?>
        <?php $dialogId = $group === 'components' ? 'page-inline-editor' : 'page-inline-snippets'; ?>
        <dialog class="editor-dialog" id="<?= $dialogId ?>" aria-labelledby="<?= $dialogId ?>-title">
            <div class="header-actions editor-dialog-heading">
                <h2 id="<?= $dialogId ?>-title"><?= $label ?></h2>
                <button class="dialog-close" type="button" data-close-page-dialog aria-label="<?= $editText('Закрыть') ?>"></button>
            </div>
            <?php $componentItems = $editGroups[$group]; $editInAdmin = false; require SITE_ROOT . '/templates/page-component-list.php'; ?>
        </dialog>
    <?php endforeach; ?>
    <dialog class="editor-dialog" id="page-layout-list" aria-labelledby="page-layout-list-title">
        <div class="header-actions editor-dialog-heading">
            <h2 id="page-layout-list-title"><?= $editText('Шаблоны оформления') ?></h2>
            <button class="dialog-close" type="button" data-close-page-dialog aria-label="<?= $editText('Закрыть') ?>"></button>
        </div>
        <p class="hint"><?= $editText('Ничего не найдено.') ?></p>
        <ul class="page-list" data-page-layout-list></ul>
    </dialog>
    <?php if ($hasDataTarget): require SITE_ROOT . '/templates/page-component-data.php'; endif; ?>
<?php endif; ?>
