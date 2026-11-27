<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}
$locales = require SITE_ROOT . '/app/help-media-library.php';
$mediaLibraryLabels = $locales[$adminLanguage] ?? $locales['en'];
$standaloneLibrary = true;
$shopLibraryRoot = site_shop_enabled();
if ($shopLibraryRoot) require_once site_shop_directory() . '/module.php';
$imageLabel = static function (string $ru, string $en): string { return site_escape(admin_localized_label($ru, $en)); };
$fileLocales = require SITE_ROOT . '/app/help-media-file-library.php';
list($sortLabel, $sortName, $sortDate, $sortSize, $orderLabel, $ascending, $descending, $progressLabel, $formatsLabel) =
    $fileLocales[$adminLanguage] ?? $fileLocales['en'];
$initialPath = '';
if (isset($_GET['path'])) {
    $requested = admin_input($_GET, 'path');
    $theme = site_design_active();
    if ($theme !== '' && preg_match('~\A/(images|files)(?:/|\z)~', $requested)) {
        $requested = '/templates/themes/' . $theme . $requested;
    }
    try { $initialPath = admin_image_library_path($requested, true); } catch (RuntimeException $exception) {}
}
?>
<h1><?= site_escape($mediaLibraryLabels[0]) ?></h1>
<section class="panel form-stack" data-media-library data-admin-url="<?= site_escape($mediaUrl) ?>" data-csrf="<?= site_escape($token) ?>"
    data-initial-path="<?= site_escape($initialPath) ?>"
    data-overwrite-confirm="<?= site_escape($mediaLibraryLabels[6]) ?>" data-delete-confirm="<?= site_escape($mediaLibraryLabels[7]) ?>">
    <div class="header-actions media-library-toolbar">
        <div class="header-actions">
            <button type="button" data-media-display="grid" aria-pressed="true" title="<?= site_escape($mediaLibraryLabels[1]) ?>"><i class="fa-solid fa-table-cells-large" aria-hidden="true"></i> <?= site_escape($mediaLibraryLabels[1]) ?></button>
            <button type="button" data-media-display="list" aria-pressed="false" title="<?= site_escape($mediaLibraryLabels[2]) ?>"><i class="fa-solid fa-list" aria-hidden="true"></i> <?= site_escape($mediaLibraryLabels[2]) ?></button>
        </div>
        <label class="choice"><input type="checkbox" data-media-select-all> <?= site_escape($mediaLibraryLabels[3]) ?></label>
        <output data-media-count aria-live="polite"></output>
        <button type="button" data-media-delete class="button-danger" disabled><i class="fa-solid fa-trash" aria-hidden="true"></i> <?= site_escape($mediaLibraryLabels[4]) ?></button>
        <div class="header-actions media-library-sort">
            <label><?= site_escape($sortLabel) ?><select data-media-sort>
                <option value="name"><?= site_escape($sortName) ?></option>
                <option value="modified"><?= site_escape($sortDate) ?></option>
                <option value="size"><?= site_escape($sortSize) ?></option>
            </select></label>
            <label><?= site_escape($orderLabel) ?><select data-media-direction>
                <option value="asc"><?= site_escape($ascending) ?></option>
                <option value="desc"><?= site_escape($descending) ?></option>
            </select></label>
        </div>
    </div>
    <div class="media-upload-progress" data-media-upload-progress hidden>
        <progress value="0" max="100" aria-label="<?= site_escape($progressLabel) ?>"></progress>
        <output aria-live="polite"></output>
    </div>
    <?php require __DIR__ . '/image-library-view.php'; ?>
</section>
