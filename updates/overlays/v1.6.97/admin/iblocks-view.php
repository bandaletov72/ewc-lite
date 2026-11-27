<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}
?>
<?php if ($iblockViewSuffix === ''): ?>
<div class="admin-heading">
    <h1><?= admin_l('Инфоблоки') ?></h1>
    <a class="button button-primary" href="<?= site_escape($iblocksUrl . '&new=1') ?>"><?= admin_l('Новый инфоблок') ?></a>
</div>
<?php endif; ?>
<?php if ($iblockEditorError !== ''): ?>
    <p class="notice error" role="alert"><?= admin_l($iblockEditorError) ?></p>
<?php endif; ?>
<div class="<?= $iblockViewSuffix === '' ? 'editor-grid' : 'iblock-focus-panel' ?>">
    <?php if ($iblockViewSuffix === ''): ?>
    <nav class="panel" aria-label="<?= admin_l('Список инфоблоков') ?>">
        <?php if (!$iblockNames): ?>
            <p data-iblock-empty><?= admin_l('Инфоблоков пока нет.') ?></p>
        <?php endif; ?>
            <ul class="page-list" data-iblock-list>
                <?php foreach ($iblockNames as $name): ?>
                    <li><a href="<?= site_escape($iblocksUrl . '&id=' . rawurlencode($name) . ($iblockTextMode ? '&text=1' : '')) ?>"<?= !$iblockIsNew && $iblockId === $name ? ' aria-current="page"' : '' ?>><?= site_escape($name) ?></a></li>
                <?php endforeach; ?>
            </ul>
    </nav>
    <?php endif; ?>
    <?php if ($iblockDraft !== null): ?>
        <?php $blockUrl = $iblocksUrl . ($iblockIsNew ? '&new=1' : '&id=' . rawurlencode($iblockId)) . $iblockViewSuffix; ?>
        <form class="panel form-stack" method="post" data-iblock-editor data-iblock-new="<?= $iblockIsNew ? '1' : '0' ?>" <?= $iblockTextMode ? '' : 'data-iblock-config' ?> action="<?= site_escape($blockUrl . ($iblockTextMode ? '&text=1' : '')) ?>">
            <div class="header-actions">
                <h2 data-iblock-heading-title><?= $iblockIsNew ? admin_t('Новый инфоблок') : admin_t('Редактирование инфоблока') ?></h2>
                <a class="button" data-iblock-mode-link href="<?= site_escape($blockUrl . ($iblockTextMode ? '' : '&text=1')) ?>">
                    <?php if (!$iblockTextMode): ?><span aria-hidden="true">☰</span><?php endif; ?>
                    <?= $iblockTextMode ? admin_t('Править как таблицу') : admin_t('Править как текст') ?>
                </a>
                <?php if (!$iblockIsNew && site_valid_iblock_name($iblockId)): ?>
                    <a class="button" href="<?= site_escape($mediaUrl . '&path=' . rawurlencode('/files/' . $iblockId)) ?>" target="_blank" rel="noopener noreferrer">
                        <i class="fa-solid fa-folder-open" aria-hidden="true"></i> <?= site_escape(admin_localized_label('Файлы инфоблока', 'Infoblock files')) ?></a>
                    <a class="button" href="<?= site_escape($mediaUrl . '&path=' . rawurlencode('/images/' . $iblockId)) ?>" target="_blank" rel="noopener noreferrer">
                        <i class="fa-solid fa-images" aria-hidden="true"></i> <?= site_escape(admin_localized_label('Фото инфоблока', 'Infoblock photos')) ?></a>
                <?php endif; ?>
            </div>
            <input type="hidden" name="action" value="<?= $iblockTextMode ? 'save-iblock-text' : 'save-iblock' ?>">
            <input type="hidden" name="csrf" value="<?= site_escape($token) ?>">
            <input type="hidden" name="revision" value="<?= site_escape($iblockRevision) ?>">
            <div class="field">
                <label for="iblock-id"><?= admin_l('Имя инфоблока') ?></label>
                <input id="iblock-id" name="id" value="<?= site_escape($iblockId) ?>" placeholder="Contacts-2026_main" maxlength="64"
                    aria-describedby="iblock-identifiers-hint" <?= $iblockIsNew ? 'required data-identifier-input pattern="' . site_escape(SITE_IDENTIFIER_PATTERN) . '"' : 'readonly' ?>>
                <small class="hint" id="iblock-identifiers-hint"><?= admin_l('В имени инфоблока и ключах столбцов разрешены A–Z, a–z, 0–9, дефис (-) и подчёркивание (_). Без пробелов.') ?></small>
            </div>
            <div data-iblock-shortcode<?= $iblockIsNew ? ' hidden' : '' ?>>
                <?= site_copy_code(admin_iblock_shortcode($iblockId), $adminLanguage, admin_t('Код для вставки')) ?>
            </div>
            <p class="notice" data-iblock-status role="status" hidden></p>
            <?php if ($iblockTextMode): ?>
                <div class="field">
                    <label for="iblock-csv"><?= admin_l('Содержимое CSV') ?></label>
                    <small class="hint" id="csv-hint"><?= admin_l('Разделитель |. Первые три строки содержат ключи, названия и типы столбцов. Затем идут данные. Значения с |, кавычками или переносами заключайте в двойные кавычки, а кавычки внутри удваивайте.') ?></small>
                    <textarea id="iblock-csv" name="csv" rows="16" spellcheck="false" aria-describedby="csv-hint"><?= "\n" . site_escape($iblockCsv) ?></textarea>
                </div>
                <div class="header-actions">
                    <button type="button" data-copy-iblock><?= admin_l('Скопировать CSV') ?></button>
                    <button class="button-primary" type="submit"><?= admin_l('Сохранить инфоблок') ?></button>
                    <small class="hint" data-copy-status role="status"></small>
                </div>
            <?php else: ?>
            <?php
            $iblockFormOptions = [];
            try {
                foreach (site_forms() as $formId => $definition) {
                    if (site_valid_form_id($formId)) $iblockFormOptions[$formId] = admin_form_name($formId, $definition, $adminLanguage);
                }
            } catch (RuntimeException $exception) {
                // Keep other columns editable even when saved form definitions are unavailable.
            }
            ?>
            <template data-iblock-form-options>
                <option value="">—</option>
                <?php foreach ($iblockFormOptions as $id => $name): ?>
                    <option value="<?= site_escape($id) ?>"><?= site_escape($name . ' [' . $id . ']') ?></option>
                <?php endforeach; ?>
            </template>
            <h2><?= admin_l('Столбцы') ?></h2>
            <div class="form-stack" data-iblock-columns>
                <?php foreach ($iblockDraft['columns'] as $index => $column): ?>
                    <?php require __DIR__ . '/iblock-column.php'; ?>
                <?php endforeach; ?>
            </div>
            <template data-iblock-column-template>
                <?php
                $index = '__INDEX__';
                $column = ['key' => '', 'label' => '', 'type' => 'text'];
                require __DIR__ . '/iblock-column.php';
                ?>
            </template>
            <div><button type="button" data-add-column><?= admin_l('Добавить столбец') ?></button></div>
            <h2><?= admin_l('Строки') ?></h2>
            <div class="iblock-table-wrap">
                <table class="iblock-table iblock-editor-table">
                    <?php $columnResizeLabels = require SITE_ROOT . '/app/iblock-resize-labels.php'; ?>
                    <thead><tr data-iblock-heading data-resize-label="<?= site_escape($columnResizeLabels[$adminLanguage] ?? $columnResizeLabels['ru']) ?>">
                        <th scope="col"><?= admin_l('Порядок') ?></th>
                        <?php foreach ($iblockDraft['columns'] as $index => $column): ?>
                            <th scope="col" data-cell-column="<?= $index ?>"><?= site_escape($column['label']) ?></th>
                        <?php endforeach; ?>
                        <th scope="col" data-iblock-actions><?= admin_l('Действия') ?></th>
                    </tr></thead>
                    <tbody data-iblock-rows>
                        <?php foreach ($iblockDraft['rows'] as $rowIndex => $row): ?>
                            <tr data-row-index="<?= $rowIndex ?>">
                                <td class="iblock-order"><button class="iblock-drag" type="button" draggable="true" data-drag-row aria-label="<?= admin_l('Переместить строку. Используйте стрелки вверх и вниз') ?>" title="<?= admin_l('Перетащить строку') ?>">⠿</button></td>
                                <?php foreach ($iblockDraft['columns'] as $index => $column): ?>
                                    <?php require __DIR__ . '/iblock-cell.php'; ?>
                                <?php endforeach; ?>
                                <td data-iblock-actions><button class="iblock-remove-row" type="button" data-remove-row title="<?= admin_l('Удалить строку') ?>" aria-label="<?= admin_l('Удалить строку') ?>"><i class="fa-solid fa-trash" aria-hidden="true"></i></button></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <input type="hidden" name="complete" value="1">
            <div class="header-actions">
                <button type="button" data-add-row><?= admin_l('Добавить строку') ?></button>
                <button class="button-primary" type="submit"><?= admin_l('Сохранить инфоблок') ?></button>
            </div>
            <?php endif; ?>
        </form>
    <?php endif; ?>
</div>
