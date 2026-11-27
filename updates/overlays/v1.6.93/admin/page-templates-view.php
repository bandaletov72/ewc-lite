<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}
$labels = admin_page_template_labels($adminLanguage);
$catalog = admin_page_template_catalog_labels($adminLanguage);
$createPage = $labels[13];
$isFocused = !empty($focusedShopTemplate);
try {
    $templates = admin_page_templates($adminLanguage, $isFocused);
    $revision = admin_settings_revision(site_read_record(SITE_PAGE_TEMPLATES_FILE, SITE_PAGE_TEMPLATES_LIMIT));
} catch (RuntimeException $exception) {
    echo '<p class="notice error" role="alert">' . admin_l($exception->getMessage()) . '</p>';
    return;
}
$selected = $pageTemplateDraftId ?? admin_input($_GET, 'template');
$record = $pageTemplateDraft ?? $templates[$selected] ?? ['name' => '', 'Title' => '', 'Heading' => '', 'Description' => '', 'html' => ''];
$editing = $selected === 'new' || isset($pageTemplateDraft) || admin_input($_GET, 'edit') === '1';
$templateContainer = $isFocused ? 'div' : 'dialog';
$templateUrl = static function (array $query) use ($pageTemplatesUrl): string {
    return $pageTemplatesUrl . '&' . http_build_query($query) . '#template-edit';
};
$newPageUrl = static function (string $id) use ($adminUrl): string {
    return $adminUrl . '?' . http_build_query(['new' => '1', 'preset' => $id]);
};
$missingSource = false;
if ($selected === 'new' && !isset($pageTemplateDraft)) {
    $copyFrom = admin_input($_GET, 'copy_from');
    $fromPage = admin_input($_GET, 'from_page');
    if ($copyFrom !== '') {
        $missingSource = !isset($templates[$copyFrom]);
        if (!$missingSource) {
            $record = $templates[$copyFrom];
            preg_match('/\A.{1,105}/us', $record['name'], $name);
            $record['name'] = rtrim($name[0] ?? '') . $catalog['copy_suffix'];
        }
    } elseif ($fromPage !== '') {
        $source = site_read_page(admin_page_path($fromPage));
        $missingSource = $source === null;
        if ($source !== null) {
            $fields = $source['fields'];
            $name = $fields['Menu'] ?: ($fields['Heading'] ?: ($fields['Title'] ?: $source['path']));
            preg_match('/\A.{1,120}/us', trim($name), $shortName);
            $record = [
                'name' => $shortName[0] ?? '',
                'Title' => $fields['Title'] ?: ($shortName[0] ?? ''),
                'Heading' => $fields['Heading'],
                'Description' => $fields['Description'],
                'html' => $source['html'],
                'assets' => [
                    'CSS' => site_asset_decode($fields['CSS'] ?? '') ?? '',
                    'JS' => site_asset_decode($fields['JS'] ?? '') ?? '',
                ],
            ];
        }
    }
}
?>
<?php if (!$isFocused): ?>
<h1><?= site_escape($labels[0]) ?></h1>
<section class="page-template-catalog" data-page-template-catalog>
    <div class="page-template-toolbar">
        <div class="page-template-filters" data-template-filters hidden>
            <input type="search" data-template-search aria-label="<?= site_escape($catalog['search']) ?>" placeholder="<?= site_escape($catalog['search']) ?>">
            <select data-template-kind aria-label="<?= site_escape($catalog['all']) ?>">
                <option value=""><?= site_escape($catalog['all']) ?></option>
                <option value="stock"><?= site_escape($catalog['stock']) ?></option>
                <option value="custom"><?= site_escape($catalog['custom']) ?></option>
            </select>
        </div>
        <a class="button button-primary" href="<?= site_escape($templateUrl(['template' => 'new'])) ?>">+ <?= site_escape($labels[3]) ?></a>
    </div>
    <ul class="page-template-list">
        <?php foreach ($templates as $id => $item): ?>
            <li class="page-template-row" data-template-row="<?= strpos($id, 'custom-') === 0 ? 'custom' : 'stock' ?>">
                <a class="page-template-name" data-template-name href="<?= site_escape($templateUrl(['template' => $id])) ?>"<?= $selected === $id ? ' aria-current="true"' : '' ?>>
                    <?= admin_page_template_icon($item, $base) ?><span><?= site_escape($item['name']) ?></span>
                </a>
                <div class="page-template-actions">
                    <a href="<?= site_escape($templateUrl(['template' => $id, 'edit' => '1'])) ?>"><i class="fa-solid fa-pen" aria-hidden="true"></i><?= admin_l('Редактировать') ?></a>
                    <?php if ($id !== 'shop-product'): ?><a href="<?= site_escape($newPageUrl($id)) ?>"><i class="fa-solid fa-plus" aria-hidden="true"></i><?= site_escape($createPage) ?></a><?php endif; ?>
                    <a href="<?= site_escape($templateUrl(['template' => 'new', 'copy_from' => $id])) ?>"><i class="fa-solid fa-copy" aria-hidden="true"></i><?= site_escape($catalog['duplicate']) ?></a>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
    <p class="hint" data-template-empty role="status" hidden><?= admin_l('Ничего не найдено.') ?></p>
</section>
<?php endif; ?>
<?php if ($selected === 'new' || isset($templates[$selected])): ?>
    <section id="template-edit" class="<?= $isFocused ? 'form-stack' : 'panel form-stack' ?>">
        <?php if (!$isFocused): ?>
        <h2><?= site_escape($selected === 'new' ? $labels[3] : $templates[$selected]['name']) ?></h2>
        <?php if (isset($templates[$selected])): ?>
            <div class="page-template-actions">
                <?php if ($selected !== 'shop-product'): ?><a href="<?= site_escape($newPageUrl($selected)) ?>"><i class="fa-solid fa-plus" aria-hidden="true"></i><?= site_escape($createPage) ?></a><?php endif; ?>
                <a href="<?= site_escape($templateUrl(['template' => 'new', 'copy_from' => $selected])) ?>"><i class="fa-solid fa-copy" aria-hidden="true"></i><?= site_escape($catalog['duplicate']) ?></a>
            </div>
            <?= site_copy_code($templates[$selected]['html'], $adminLanguage, 'HTML', '', [
                'href' => $templateUrl(['template' => $selected, 'edit' => '1']),
                'label' => admin_t('Редактировать'),
            ]) ?>
        <?php endif; ?>
        <?php endif; ?>
        <?php if ($editing): ?>
            <?php $closeUrl = isset($templates[$selected]) ? $templateUrl(['template' => $selected]) : $pageTemplatesUrl; ?>
            <<?= $templateContainer ?> class="<?= $isFocused ? '' : 'editor-dialog page-template-dialog' ?>" data-page-template-dialog aria-labelledby="page-template-dialog-title"<?= $isFocused ? '' : ' open' ?>>
                <form class="form-stack" method="post" action="<?= site_escape($isFocused
                    ? $shopUrl . '&tab=templates&shop_edit=template&template=' . rawurlencode($selected) . '&embed=1&edit=1'
                    : $pageTemplatesUrl . '#template-edit') ?>">
                    <div class="header-actions editor-dialog-heading"<?= $isFocused ? ' hidden' : '' ?>>
                        <h2 id="page-template-dialog-title"><?= site_escape($selected === 'new' ? $labels[3] : ($record['name'] ?: $templates[$selected]['name'])) ?></h2>
                        <a class="dialog-close" data-page-template-close href="<?= site_escape($closeUrl) ?>" aria-label="<?= admin_l('Закрыть') ?>"></a>
                    </div>
                    <?php if ($error !== '' || $missingSource): ?>
                        <p class="notice error" role="alert"><?= admin_l($error !== '' ? $error : 'Страница не найдена.') ?></p>
                    <?php endif; ?>
                    <input type="hidden" name="csrf" value="<?= site_escape($token) ?>">
                    <input type="hidden" name="revision" value="<?= site_escape($revision) ?>">
                    <input type="hidden" name="action" value="save-page-template">
                    <input type="hidden" name="template" value="<?= site_escape($selected) ?>">
                    <label class="field"><?= site_escape($labels[4]) ?>
                        <input name="name" maxlength="120" required value="<?= site_escape($record['name']) ?>">
                    </label>
                    <label class="field"><?= site_escape($catalog['icon']) ?>
                        <input name="icon_code" data-page-template-icon-code maxlength="96" spellcheck="false" value="<?= site_escape($record['icon_code'] ?? '') ?>" placeholder="[fa-icon=house]">
                    </label>
                    <details class="field-config disclosure-panel" data-page-template-icon-picker>
                        <summary><span class="disclosure-arrow" aria-hidden="true"></span><?= site_escape(admin_editor_labels($adminLanguage)['icon']) ?></summary>
                        <div class="form-stack">
                            <?php
                            $iconFilterLabels = require SITE_ROOT . '/app/help-icon-filter.php';
                            $iconFilterLabels = $iconFilterLabels[$adminLanguage] ?? $iconFilterLabels['ru'];
                            $iconTokens = [];
                            $canInsertFA = true;
                            require __DIR__ . '/icon-picker-results.php';
                            ?>
                        </div>
                    </details>
                    <?php foreach (['Title' => 'Заголовок вкладки', 'Heading' => 'Заголовок H1', 'Description' => 'Метаописание'] as $key => $label): ?>
                        <?php if ($isFocused): ?><input type="hidden" name="<?= $key ?>" value="<?= site_escape($record[$key]) ?>"><?php else: ?>
                        <label class="field"><?= admin_l($label) ?>
                            <input name="<?= $key ?>" maxlength="375"<?= $key === 'Title' ? ' required' : '' ?> value="<?= site_escape($record[$key]) ?>">
                        </label>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <label class="field">HTML
                        <textarea name="html" rows="12" spellcheck="false" data-code-mode="html"><?= "\n" . site_escape($record['html']) ?></textarea>
                    </label>
                    <?php if ($selected === 'shop-product'): ?>
                        <div class="shop-template-shortcodes">
                            <?php $codes = ['[shop-name]'=>'product_name','[shop-summary]'=>'summary','[shop-description]'=>'description','[shop-photo]'=>'product_image',
                                '[shop-gallery]'=>'record_gallery','[shop-characteristics]'=>'record_properties','[shop-variants]'=>'variants',
                                '[shop-price]'=>'price','[shop-buy]'=>'add','[shop-url]'=>'product_slug','[shop-breadcrumbs]'=>'categories']; ?>
                            <?php foreach ($codes as $code => $key): ?>
                                <button type="button" data-shop-template-insert="<?= site_escape($code) ?>" title="<?= site_escape(shop_label($key, $adminLanguage)) ?>"><code><?= site_escape($code) ?></code></button>
                            <?php endforeach; ?>
                        </div>
                        <p class="hint"><i class="fa-solid fa-circle-info" aria-hidden="true"></i> <?= site_escape(shop_label('template_schema_help', $adminLanguage)) ?></p>
                    <?php endif; ?>
                    <details class="field-config disclosure-panel"<?= $error !== '' ? ' open' : '' ?>>
                        <summary><span class="disclosure-arrow" aria-hidden="true"></span>CSS · JavaScript</summary>
                        <div class="form-stack"><?php admin_asset_editors($record['assets'] ?? []); ?></div>
                    </details>
                    <div><button type="submit" class="button-primary"><i class="fa-solid fa-floppy-disk" aria-hidden="true"></i> <?= site_escape($catalog['save']) ?></button></div>
                </form>
            </<?= $templateContainer ?>>
        <?php endif; ?>
    </section>
<?php endif; ?>
