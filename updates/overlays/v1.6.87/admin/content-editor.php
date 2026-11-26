<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}
$layouts = array_diff(array_keys(site_layout_blocks()), ['conf-policy']);
$layouts = array_merge($layouts, ['page-content', 'privacy-content']);
$tokens = array_values(array_diff(array_unique(array_merge(['payment', 'item-price', 'item-buy-button', 'sections', 'list-pages', 'author', 'site-name', 'site-url'], array_keys(site_date_snippets()), ['dmY'],
    array_keys(site_snippet_values($currentHost)), array_keys($siteSettings['variables']), array_keys(site_contact_tokens()), ['cities'], site_city_live_tokens())), $layouts, ['conf-policy']));
$iconTokens = array_keys(site_contact_tokens());
try { $editorForms = site_forms(); } catch (RuntimeException $exception) { $editorForms = site_default_forms(); }
$editorForms = array_filter($editorForms, static function ($form, $id): bool {
    return is_array($form) && is_string($id) && site_valid_form_id($id);
}, ARRAY_FILTER_USE_BOTH);
uksort($editorForms, 'strnatcmp');
try { $editorLibraries = site_library_settings(site_read_record(SITE_LIBRARIES_FILE)); } catch (RuntimeException $exception) { $editorLibraries = site_library_settings(); }
$canInsertFA = $editorLibraries['enabled']['fontawesome'];
$chartText = site_iblock_chart_text($adminLanguage);
$editorLabels = admin_editor_labels($adminLanguage);
$typographyLabels = require SITE_ROOT . '/app/editor-typography-labels.php';
$typography = $typographyLabels[$adminLanguage] ?? $typographyLabels['ru'];
$enabledFonts = array_intersect_key(site_font_options(), array_flip(site_font_settings()['enabled']));
$iconFilterLabels = require SITE_ROOT . '/app/help-icon-filter.php';
$iconFilterLabels = $iconFilterLabels[$adminLanguage] ?? $iconFilterLabels['ru'];
$fieldPickerLabels = admin_page_custom_labels();
$fieldPickerDefinitions = site_page_custom_definitions();
$maplistLabels = require SITE_ROOT . '/app/help-maplist.php';
$maplistLabels = $maplistLabels[$adminLanguage] ?? $maplistLabels['ru'];
$routeLabels = require SITE_ROOT . '/app/help-route.php';
$routeLabels = $routeLabels[$adminLanguage] ?? $routeLabels['ru'];
$workflow = site_localized_help(require SITE_ROOT . '/app/help-workflow.php', $adminLanguage);
$workflow = $workflow[$adminLanguage] ?? $workflow['ru'];
$toolsLabel = $workflow['tools'];
$mediaTheme = site_design_active();
$mediaBase = $mediaTheme === '' ? '' : '/templates/themes/' . $mediaTheme;
list($bannerName, $bannerSetLabel, $bannerSetHint) = site_banner_editor_labels($adminLanguage);
$pickerHeading = static function (string $title): void {
    ?>
    <div class="editor-picker-heading">
        <h3><?= site_escape($title) ?></h3>
        <button type="button" data-editor-close-picker title="<?= admin_l('Закрыть') ?>" aria-label="<?= admin_l('Закрыть') ?>"><span aria-hidden="true">×</span></button>
    </div>
    <?php
};
?>
<div class="field field-wide" data-content-editor data-editor-admin-url="<?= site_escape(site_url($base, 'admin/')) ?>"
    data-editor-csrf="<?= site_escape($token) ?>" data-editor-base="<?= site_escape($base) ?>" data-editor-media-base="<?= site_escape($mediaBase) ?>"
    <?php if (isset($editorHistory)): ?>
        data-editor-history-key="<?= bin2hex(random_bytes(16)) ?>"
        <?= empty($creating) && empty($page['unsavedDraft']) ? '' : 'data-editor-history-draft' ?>
    <?php endif; ?>>
    <?php if (isset($editorAnnouncement)): ?>
        <div class="fields">
            <label class="field"><?= site_escape($announcementLabels[6]) ?>
                <input name="AnnouncementImage" id="announcement-image" value="<?= site_escape($editorAnnouncement['AnnouncementImage'] ?? '') ?>">
            </label>
            <label class="field"><?= site_escape($announcementLabels[7]) ?>
                <input name="AnnouncementIcon" id="announcement-icon" value="<?= site_escape($editorAnnouncement['AnnouncementIcon'] ?? '') ?>">
            </label>
        </div>
        <div class="header-actions announcement-picker-actions">
            <button type="button" data-editor-open="image" data-editor-image-target="announcement-image" aria-controls="announcement-picker-image" aria-expanded="false"><?= site_escape($announcementLabels[8]) ?></button>
            <button type="button" data-editor-open="icon" data-editor-icon-target="announcement-icon" aria-controls="announcement-picker-icon" aria-expanded="false"><?= site_escape($editorLabels['icon']) ?></button>
        </div>
    <?php endif; ?>
    <label for="<?= site_escape($editorId) ?>"><?= site_escape($editorTitle) ?></label>
    <div class="header-actions editor-mode-tabs" data-editor-toolbar hidden>
        <button type="button" data-editor-mode="html" aria-pressed="true">HTML</button>
        <button type="button" data-editor-mode="visual" aria-pressed="false"><?= admin_l('Визуальный редактор') ?></button>
    </div>
    <div class="header-actions" data-editor-format hidden>
        <?php if (isset($editorHistory)): ?>
            <span class="editor-tool-group editor-history-tools">
            <button class="editor-tool" type="submit" data-editor-quick-save title="<?= admin_l('Сохранить') ?>" aria-label="<?= admin_l('Сохранить') ?>" disabled><i class="fa-solid fa-floppy-disk" aria-hidden="true"></i></button>
            <?php foreach (['undo' => ['rotate-left', admin_t('Отменить последнее изменение')],
                'redo' => ['rotate-right', $editorHistory[1]], 'restore' => ['clock-rotate-left', $editorHistory[2]]] as $action => list($icon, $label)): ?>
                <?php if ($action === 'restore' && !empty($creating)) continue; ?>
                <button class="editor-tool" type="button" data-editor-history-action="<?= $action ?>" title="<?= site_escape($label) ?>" aria-label="<?= site_escape($label) ?>" disabled><i class="fa-solid fa-<?= $icon ?>" aria-hidden="true"></i></button>
            <?php endforeach; ?>
            </span>
        <?php endif; ?>
        <?php foreach (['bold' => ['bold', admin_t('Жирный')], 'italic' => ['italic', admin_t('Курсив')],
            'underline' => ['underline', $editorLabels['underline']], 'strikeThrough' => ['strikethrough', $editorLabels['strike']],
            'superscript' => ['superscript', 'Superscript · SUP'], 'subscript' => ['subscript', 'Subscript · SUB'],
            'insertUnorderedList' => ['list-ul', admin_t('Маркированный список')], 'insertOrderedList' => ['list-ol', $editorLabels['numbered']]] as $command => list($icon, $label)): ?>
            <button class="editor-tool" type="button" data-editor-command="<?= $command ?>" title="<?= site_escape($label) ?>" aria-label="<?= site_escape($label) ?>"><i class="fa-solid fa-<?= $icon ?>" aria-hidden="true"></i></button>
        <?php endforeach; ?>
        <button class="editor-tool" type="button" data-editor-open="typography" title="<?= site_escape($typography[0]) ?>" aria-label="<?= site_escape($typography[0]) ?>" aria-controls="<?= site_escape($editorId) ?>-picker-typography" aria-expanded="false"><i class="fa-solid fa-font" aria-hidden="true"></i></button>
        <span class="editor-tool-group" data-editor-visual-controls hidden>
            <?php
            foreach (['Left' => 'left', 'Center' => 'center', 'Right' => 'right'] as $command => $icon):
                $alignLabel = $workflow['align'][$icon];
            ?>
                <button class="editor-tool" type="button" data-editor-command="justify<?= $command ?>" title="<?= site_escape($alignLabel) ?>" aria-label="<?= site_escape($alignLabel) ?>"><i class="fa-solid fa-align-<?= $icon ?>" aria-hidden="true"></i></button>
            <?php endforeach; ?>
            <button class="editor-tool" type="button" data-editor-command="removeFormat" title="<?= site_escape($workflow['align']['clear']) ?>" aria-label="<?= site_escape($workflow['align']['clear']) ?>"><i class="fa-solid fa-eraser" aria-hidden="true"></i></button>
        </span>
        <button class="editor-tool" type="button" data-editor-command="formatBlock" data-editor-value="p" title="<?= admin_l('Параграф') ?>" aria-label="<?= admin_l('Параграф') ?>"><i class="fa-solid fa-paragraph" aria-hidden="true"></i></button>
        <?php for ($level = 1; $level <= 5; $level++): ?>
            <?php $headingLabel = $editorLabels['heading'] . ' H' . $level; ?>
            <button class="editor-tool" type="button" data-editor-command="formatBlock" data-editor-value="h<?= $level ?>" title="<?= site_escape($headingLabel) ?>" aria-label="<?= site_escape($headingLabel) ?>">H<?= $level ?></button>
        <?php endfor; ?>
        <?php foreach (['link' => 'link', 'button' => 'square-plus'] as $key => $icon): ?>
            <button class="editor-tool" type="button" data-editor-dialog-trigger="<?= $key ?>" aria-haspopup="dialog" title="<?= site_escape($toolsLabel[$key]) ?>" aria-label="<?= site_escape($toolsLabel[$key]) ?>"><i class="fa-solid fa-<?= $icon ?>" aria-hidden="true"></i></button>
        <?php endforeach; ?>
        <?php foreach (['template' => ['layer-group', $editorLabels['template']], 'image' => ['image', $editorLabels['image']],
            'component' => ['puzzle-piece', $editorLabels['component']], 'icon' => ['icons', $editorLabels['icon']]] 
            + ['custom-fields' => ['sliders', $fieldPickerLabels[0]]]
            + (isset($tree, $announcementLabels) && $editorId === 'html' ? ['directory' => ['folder-tree', $announcementLabels[2]]] : []) as $picker => list($icon, $label)): ?>
            <?php if ($picker === 'component' && !$editorWithComponents) continue; ?>
            <button class="editor-tool" type="button" data-editor-open="<?= $picker ?>" title="<?= site_escape($label) ?>" aria-label="<?= site_escape($label) ?>" aria-controls="<?= site_escape($editorId) ?>-picker-<?= $picker ?>" aria-expanded="false"><i class="fa-solid fa-<?= $icon ?>" aria-hidden="true"></i></button>
        <?php endforeach; ?>
        <?php if (!empty($editorImageTarget)): ?>
            <button type="button" data-editor-open="image" data-editor-image-target="<?= site_escape($editorImageTarget) ?>" aria-controls="<?= site_escape($editorId) ?>-picker-image" aria-expanded="false"><?= site_escape(admin_localized_label('Выбрать подложку', 'Choose background')) ?></button>
        <?php endif; ?>
    </div>
    <div class="panel editor-picker" id="<?= site_escape($editorId) ?>-picker-typography" data-editor-picker="typography" hidden>
        <?php $pickerHeading($typography[0]); ?>
        <div class="editor-picker-body form-stack">
            <p class="hint" data-editor-typography-hint><?= site_escape($typography[5]) ?></p>
            <div class="fields editor-typography-fields">
                <label class="field"><?= site_escape($typography[1]) ?>
                    <select data-editor-text-style="font-size">
                        <option value="">—</option>
                        <?php foreach ([10, 12, 14, 16, 18, 20, 22, 24, 28, 32, 36, 40, 48, 56, 64, 72, 96] as $size): ?>
                            <option value="<?= $size ?>px"><?= $size ?> px</option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <?php foreach (['color' => [2, '#000000'], 'background-color' => [3, '#ffffff']] as $property => list($labelIndex, $defaultColor)): ?>
                    <div class="field">
                        <label class="choice">
                            <input type="checkbox" data-editor-text-color-enabled="<?= $property ?>">
                            <?= site_escape($typography[$labelIndex]) ?>
                        </label>
                        <input type="color" data-editor-text-style="<?= $property ?>" value="<?= $defaultColor ?>" aria-label="<?= site_escape($typography[$labelIndex]) ?>">
                    </div>
                <?php endforeach; ?>
                <label class="field"><?= site_escape($typography[4]) ?>
                    <select data-editor-text-style="font-family">
                        <option value="">—</option>
                        <option value="var(--site-font, inherit)"><?= admin_l('Шрифт сайта') ?></option>
                        <?php foreach ($enabledFonts as $font): ?>
                            <option value="<?= site_escape(site_font_family($font)) ?>"><?= site_escape($font[0]) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
            </div>
            <div><button type="button" data-editor-apply-typography disabled><?= admin_l('Применить') ?></button></div>
        </div>
    </div>
    <div class="panel editor-picker" id="<?= site_escape($editorId) ?>-picker-custom-fields" data-editor-picker="custom-fields" hidden>
        <?php $pickerHeading($fieldPickerLabels[0]); ?>
        <div class="editor-picker-body form-stack">
        <?php if ($fieldPickerDefinitions): ?>
            <label class="field"><?= site_escape($fieldPickerLabels[0]) ?>
                <select data-editor-custom-field>
                    <?php foreach ($fieldPickerDefinitions as $fieldDefinition): ?>
                        <?php $fieldCode = '[u-' . $fieldDefinition['id'] . ']'; ?>
                        <option value="<?= site_escape($fieldCode) ?>"><?= site_escape($fieldCode . ($fieldDefinition['description'] === '' ? '' : ' · ' . $fieldDefinition['description'])) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <div><button type="button" data-editor-add-custom-field><?= admin_l('Вставить в текст') ?></button></div>
        <?php else: ?>
            <?php $settingsInNewTab = !isset($editorHistory) || !empty($creating); ?>
            <p><a href="<?= site_escape(admin_page_custom_add_url($base, $settingsInNewTab)) ?>"
                data-editor-custom-settings<?= $settingsInNewTab ? ' target="_blank" rel="noopener"' : '' ?>><?= site_escape($fieldPickerLabels[10]) ?></a></p>
        <?php endif; ?>
        </div>
    </div>
    <?php if (isset($tree, $announcementLabels) && $editorId === 'html'): ?>
        <div class="panel editor-picker" id="html-picker-directory" data-editor-picker="directory" hidden>
            <?php $pickerHeading($announcementLabels[2]); ?>
            <div class="editor-picker-body form-stack">
            <div><button type="button" data-editor-directory="[list-pages]"><?= site_escape($announcementLabels[3]) ?></button></div>
            <h4><?= site_escape($announcementLabels[4]) ?></h4>
            <?php
            $insertSection = static function (string $path) use ($announcementLabels): void {
                $url = $path === '.' ? '/' : '/' . $path . '/';
                ?>
                <div class="editor-directory-row">
                    <code><?= site_escape($url) ?></code>
                    <button type="button" data-editor-directory="<?= site_escape('[dir]' . $url . '[/dir]') ?>"><?= site_escape($announcementLabels[11]) ?></button>
                </div>
                <?php
            };
            $renderSections = static function (string $parent) use (&$renderSections, $tree, $insertSection): void {
                foreach ($tree[$parent] ?? [] as $node) {
                    $folder = $node['folder'];
                    if ($folder === '' || $folder === 'error') continue;
                    echo '<li>';
                    $insertSection($folder);
                    if (!empty($tree[$folder])) {
                        echo '<ul>';
                        $renderSections($folder);
                        echo '</ul>';
                    }
                    echo '</li>';
                }
            };
            $insertSection('.');
            ?>
            <ul class="editor-directory-tree"><?php $renderSections('.'); ?></ul>
            </div>
        </div>
    <?php endif; ?>
    <div class="panel editor-picker" id="<?= site_escape($editorId) ?>-picker-template" data-editor-picker="template" hidden>
        <?php $pickerHeading($editorLabels['template']); ?>
        <div class="editor-picker-body form-stack">
        <label class="field"><?= admin_l('Шаблоны') ?>
            <select data-editor-template>
                <?php foreach ([admin_t('Шаблоны') => $layouts, admin_t('Подшаблоны') => $tokens] as $label => $options): ?>
                    <optgroup label="<?= site_escape($label) ?>">
                        <?php foreach ($options as $key): ?><option value="<?= site_escape($key) ?>">[<?= site_escape($key) ?>]</option><?php endforeach; ?>
                    </optgroup>
                <?php endforeach; ?>
            </select>
        </label>
        <div><button type="button" data-editor-add-template><?= admin_l('Вставить в текст') ?></button></div>
        </div>
    </div>
    <?php if ($editorWithComponents): ?>
    <div class="panel editor-picker" id="<?= site_escape($editorId) ?>-picker-component" data-editor-picker="component" hidden>
        <?php $pickerHeading($editorLabels['component']); ?>
        <div class="editor-picker-body form-stack">
        <div class="fields">
            <label class="field"><?= admin_l('Компонент') ?>
                <select data-editor-component>
                    <?php $componentCodes = ['gal' => '[gal]', 'fotos' => '[fotos]', 'files' => '[files]', 'include' => '[include]',
                        'form' => '', 'reviews' => '[reviews]', 'callback' => '[callback]', 'iblock' => '[iblock=…]', 'table' => '[iblock=table]', 'charts' => '[iblock=charts]', 'map' => '[map]',
                        'maps' => '[maps]', 'maplist' => '[maplist]', 'points' => '[points]', 'route' => '[route]', 'php' => '[php]', 'banners' => '[banners]', 'list-pages' => '[list-pages]']; ?>
                    <?php foreach (['gal' => admin_t('Карусель'), 'fotos' => admin_t('Фотогалерея'), 'files' => admin_t('Список файлов'),
                        'include' => admin_t('Другая страница'), 'form' => admin_t('Формы'), 'reviews' => site_review_labels($GLOBALS['adminLanguage'] ?? 'ru')[0], 'callback' => admin_t('Обратный звонок'),
                        'table' => $chartText['table'], 'charts' => $chartText['chart'],
                        'iblock' => admin_t('Инфоблок'), 'map' => admin_t('Точка на карте'), 'maps' => $discoveryLabels['all-maps'], 'maplist' => $maplistLabels[0], 'points' => $maplistLabels[3], 'route' => $routeLabels[0], 'php' => admin_t('PHP-модули'), 'banners' => $bannerName,
                        'list-pages' => admin_localized_label('Список страниц раздела', 'Section page list')] as $key => $label): ?>
                        <option value="<?= $key ?>"><?= site_escape(trim($label . ' ' . $componentCodes[$key])) ?></option>
                    <?php endforeach; ?>
                    <?php if (site_shop_enabled()): foreach (site_shop_tags() as $shopTag): ?>
                        <option value="extension:shop-<?= $shopTag ?>" data-kind="single">[shop-<?= $shopTag ?>]</option>
                    <?php endforeach; endif; ?>
                    <option value="extension:register" data-kind="single">[register]</option>
                    <option value="extension:account" data-kind="single">[account]</option>
                    <?php foreach (site_extensions() as $tag => $definition): ?>
                        <?php if (isset(site_php_module_settings()[$tag]) || site_php_module_source($tag)[1] !== '') continue; ?>
                        <option value="extension:<?= site_escape($tag) ?>" data-kind="<?= site_escape($definition['kind']) ?>"><?= site_escape($definition['label'] ?? $tag) ?> [<?= site_escape($tag) ?>]</option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label class="field" data-editor-show="maplist points"><?= site_escape($maplistLabels[4]) ?>
                <select data-editor-param="map-section">
                    <option value=""><?= site_escape($maplistLabels[5]) ?></option>
                    <?php $mapFolders = array_keys($tree ?? site_page_tree(site_pages())); sort($mapFolders, SORT_NATURAL | SORT_FLAG_CASE); ?>
                    <?php foreach ($mapFolders as $mapFolder): ?>
                        <?php if ($mapFolder === 'error' || strpos($mapFolder, 'error/') === 0) continue;
                        $mapFolderPath = $mapFolder === '.' ? '/' : '/' . $mapFolder . '/'; ?>
                        <option value="<?= site_escape($mapFolderPath) ?>"><?= site_escape($mapFolderPath) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label class="field" data-editor-show="extension"><?= admin_l('Значение') ?><input data-editor-param="extension-value"></label>
            <label class="field" data-editor-show="php"><?= site_escape(admin_l('PHP-модули')) ?>
                <select data-editor-param="module">
                    <?php if (site_shop_enabled()): foreach (site_shop_tags() as $shopTag): ?>
                        <option value="extension:shop-<?= $shopTag ?>" data-kind="single">[shop-<?= $shopTag ?>]</option>
                    <?php endforeach; endif; ?>
                    <option value="extension:register" data-kind="single">[register]</option>
                    <option value="extension:account" data-kind="single">[account]</option>
                    <?php foreach (site_extensions() as $name => $definition): ?>
                        <?php if (!site_php_module_enabled($name)) continue; ?>
                        <option value="<?= site_escape($name) ?>"><?= site_escape(site_php_module_settings()[$name]['label'] ?? admin_t($definition['label'] ?? $name)) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label class="field" data-editor-show="form"><?= admin_l('Форма') ?>
                <select data-editor-param="form">
                    <?php foreach ($editorForms as $id => $definition): ?>
                        <option value="<?= site_escape($id) ?>"><?= site_escape(admin_form_name($id, $definition, $adminLanguage)) ?> [<?= site_escape($id) ?>]</option>
                    <?php endforeach; ?>
                </select>
            </label>
            <?php
            $filePickerOptions = [];
            foreach (site_iblock_names() as $id) {
                try { $filePickerOptions[$id] = site_read_iblock($id)['columns'] ?? []; }
                catch (RuntimeException $exception) { $filePickerOptions[$id] = []; }
            }
            $filePickerSelected = '';
            $filePickerMode = 'insert';
            $filePickerLanguage = $adminLanguage;
            $filePickerCsrf = $token;
            $filePickerTitle = $chartText['block'];
            require SITE_ROOT . '/templates/iblock-file-picker.php';
            ?>
            <label class="field" data-editor-show="charts"><?= site_escape($chartText['type']) ?>
                <select data-editor-param="chart-type">
                    <?php foreach (site_iblock_chart_types() as $index => $type): ?>
                        <option value="<?= site_escape($type) ?>"><?= site_escape($chartText['types'][$index]) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label class="field" data-editor-show="charts"><?= site_escape($chartText['labels']) ?><select data-editor-param="chart-labels"></select></label>
            <label class="field" data-editor-show="charts"><?= site_escape($chartText['values']) ?><select data-editor-param="chart-values" multiple size="4"></select></label>
            <label class="field" data-editor-show="charts"><?= site_escape($chartText['title']) ?><input data-editor-param="chart-title" maxlength="120"></label>
            <?php if (!$editorLibraries['enabled']['chartjs']): ?>
                <p class="hint" data-editor-show="charts"><a href="<?= site_escape(site_url($base, 'admin/?libraries=1')) ?>"><?= site_escape($chartText['enable']) ?></a></p>
            <?php endif; ?>
            <?php foreach ([
                'path' => [admin_t('Путь'), 'gal fotos files include', '/images/myfotos/'],
                'banner-set' => [$bannerSetLabel, 'banners', 'main'],
                'banner-folder' => [admin_t('Папка компонента'), 'banners', $mediaBase . '/images/myfotos/'],
                'title' => [admin_t('Название точки или заголовок формы'), 'map callback', admin_t('Офис')],
                'lat' => [admin_t('Широта'), 'map', '55.7558'],
                'lng' => [admin_t('Долгота'), 'map', '37.6173'],
                'zoom' => [admin_t('Масштаб'), 'map maps maplist', '13'],
                'center' => [$discoveryLabels['map-center'], 'map maps maplist', '55.7558,37.6173'],
                'source' => [admin_t('Инфоблок'), 'maps', 'kontur-map'],
                'button' => [admin_t('Текст кнопки'), 'callback route', admin_t('Подробнее')],
                'submit' => [admin_t('Текст кнопки отправки'), 'callback', admin_t('Отправить номер')],
                'placeholder' => [admin_t('Подсказка в поле телефона'), 'callback', '+7(900)123-45-67'],
            ] as $key => $field): ?>
                <label class="field" data-editor-show="<?= $field[1] ?>"><?= site_escape($field[0]) ?>
                    <input data-editor-param="<?= $key ?>" placeholder="<?= site_escape($field[2]) ?>"
                        <?= $key === 'button' ? 'data-editor-route-placeholder="' . site_escape(site_route_label($adminLanguage)) . '" data-editor-default-placeholder="' . site_escape($field[2]) . '"' : '' ?>>
                    <?php if ($key === 'banner-set'): ?><small class="hint">A–Z, a–z, -, _. <?= site_escape($bannerSetHint) ?></small><?php endif; ?>
                    <?php if (in_array($key, ['path', 'banner-folder'], true)): ?>
                        <small class="hint" data-editor-show="<?= $key === 'path' ? 'gal fotos files' : 'banners' ?>"><?= site_escape($adminLanguage === 'ru'
                            ? 'Имена папок: A–Z, a–z, 0–9, дефис и подчёркивание. Слэш разделяет уровни.'
                            : 'Folder names allow A–Z, a–z, 0–9, hyphens and underscores. Use slashes between folders.') ?></small>
                    <?php endif; ?>
                </label>
            <?php endforeach; ?>
            <?php foreach (['layout' => [admin_t('Вид инфоблока'), 'iblock', array_diff_key(site_iblock_views(), ['table' => true, 'charts' => true])],
                'provider' => [admin_t('Карты'), 'map maps maplist route', ['' => admin_t('По умолчанию'), 'yandex' => admin_t('Яндекс Карты'), 'google' => 'Google Maps']],
                'mode' => [admin_t('Режим'), 'map maps maplist', ['' => admin_t('По умолчанию')] + site_map_modes()]] as $key => $field): ?>
                <label class="field" data-editor-show="<?= $field[1] ?>"><?= site_escape($field[0]) ?>
                    <select data-editor-param="<?= $key ?>">
                        <?php foreach ($field[2] as $value => $label): ?><option value="<?= $value ?>"><?= admin_l($label) ?></option><?php endforeach; ?>
                    </select>
                </label>
            <?php endforeach; ?>
            <?php $sizeFields = site_component_size_fields(); ?>
            <?php foreach ($sizeFields as $name => $field): ?>
                <label class="field" data-editor-dimension><?= site_escape(site_component_size_text($field['label'], $adminLanguage)) ?>
                    <select data-editor-size="<?= site_escape($name) ?>">
                        <option value=""><?= admin_l('По умолчанию') ?></option>
                        <?php foreach ($field['options'] as $option => $label): ?><option value="<?= site_escape($option) ?>"><?= site_escape(site_component_size_text($label, $adminLanguage)) ?></option><?php endforeach; ?>
                    </select>
                </label>
            <?php endforeach; ?>
            <?php foreach (site_component_schema() as $kind => $definition): ?>
                <?php foreach (array_diff_key($definition['settings'], $sizeFields) as $name => $field): ?>
                    <label class="field" data-editor-show="<?= site_escape($kind) ?>"><?= admin_l($field['label']) ?>
                        <select data-editor-option="<?= site_escape($name) ?>">
                            <option value=""><?= admin_l('По умолчанию') ?></option>
                            <?php foreach ($field['options'] as $option => $label): ?><option value="<?= site_escape($option) ?>"><?= admin_l($label) ?></option><?php endforeach; ?>
                        </select>
                    </label>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
        <div class="header-actions">
            <button type="button" data-editor-insert-button><?= admin_l('Вставить в текст') ?></button>
            <small class="notice error" data-editor-component-error role="alert" hidden
                data-chart-error="<?= site_escape($chartText['required']) ?>"
                data-banner-error="<?= site_escape($adminLanguage === 'ru' ? 'ID набора допускает A–Z, a–z, дефис и подчёркивание. Для папки укажите путь вроде /images/myfotos/.' : 'The set ID accepts A–Z, a–z, hyphen, and underscore. Use a folder path such as /images/myfotos/.') ?>"
                data-files-error="<?= site_escape($adminLanguage === 'ru' ? 'Укажите путь вроде /files/myfiles/. В именах папок допустимы A–Z, a–z, 0–9, дефис и подчёркивание.' : 'Use a folder path such as /files/myfiles/. Names allow A–Z, a–z, 0–9, hyphens and underscores.') ?>"
                data-images-error="<?= site_escape($adminLanguage === 'ru' ? 'Укажите путь вроде /images/myfotos/. В именах папок допустимы A–Z, a–z, 0–9, дефис и подчёркивание.' : 'Use a folder path such as /images/myfotos/. Names allow A–Z, a–z, 0–9, hyphens and underscores.') ?>"></small>
        </div>
        </div>
    </div>
    <?php endif; ?>
    <div class="panel editor-picker" id="<?= site_escape($editorId) ?>-picker-image" data-editor-picker="image" hidden>
        <?php $pickerHeading($editorLabels['image']); ?>
        <div class="editor-picker-body form-stack">
        <?php $imageLabel = static function (string $ru, string $en): string { return site_escape(admin_localized_label($ru, $en)); }; ?>
        <div class="editor-image-library form-stack" data-image-library
            data-folder-confirm="<?= site_escape(site_media_folder_labels($adminLanguage)[1]) ?>"
            data-delete-title="<?= $imageLabel('Удалить выбранный файл или папку вместе с вложенными изображениями?', 'Delete this file or folder and its images?') ?>"
            data-error-text="<?= $imageLabel('Не удалось выполнить действие с изображениями.', 'The image operation failed.') ?>">
            <div class="header-actions">
                <select data-image-root aria-label="<?= $imageLabel('Каталог изображений', 'Image directory') ?>">
                    <?php foreach (admin_image_library_roots() as $root): ?><option value="<?= site_escape($root) ?>"><?= site_escape($root) ?></option><?php endforeach; ?>
                </select>
                <button type="button" data-image-parent hidden title="<?= $imageLabel('На уровень выше', 'Parent folder') ?>" aria-label="<?= $imageLabel('На уровень выше', 'Parent folder') ?>">↑</button>
                <code data-image-path aria-live="polite"></code>
            </div>
            <div class="fields">
                <div class="field">
                    <label class="field"><?= $imageLabel('Загрузить фото', 'Upload photos') ?><input type="file" data-image-upload accept="image/jpeg,image/png,image/webp,image/gif,image/avif" multiple></label>
                    <div><button type="button" data-image-upload-button><?= admin_l('Загрузить файлы') ?></button></div>
                </div>
                <div class="field">
                    <label class="field"><?= $imageLabel('Новая папка', 'New folder') ?><input data-image-folder-name maxlength="64"></label>
                    <div><button type="button" data-image-mkdir><?= $imageLabel('Создать папку', 'Create folder') ?></button></div>
                </div>
            </div>
            <div class="fields">
                <ul class="editor-image-list" data-image-list></ul>
                <div class="editor-image-preview form-stack" data-image-preview hidden>
                    <img data-image-thumbnail alt="">
                    <p class="hint" data-image-size></p>
                    <div class="fields">
                        <label class="field"><?= $imageLabel('Ширина', 'Width') ?><input type="number" min="1" max="6000" data-image-width></label>
                        <label class="field"><?= $imageLabel('Высота', 'Height') ?><input type="number" min="1" max="6000" data-image-height></label>
                    </div>
                    <label class="field"><?= $imageLabel('Поворот по часовой стрелке', 'Clockwise rotation') ?>
                        <select data-image-angle><option value="0">0°</option><option value="90">90°</option><option value="180">180°</option><option value="270">270°</option></select>
                    </label>
                    <div><button type="button" data-image-transform><?= $imageLabel('Сохранить копию', 'Save a copy') ?></button></div>
                </div>
            </div>
            <p class="hint" data-image-library-status role="status" hidden></p>
        </div>
        <label class="field">URL <input data-editor-image-url placeholder="/images/photo.jpg"></label>
        <label class="field"><?= site_escape($editorLabels['alt']) ?><input data-editor-image-alt></label>
        <div class="header-actions">
            <button type="button" data-editor-add-image data-insert-label="<?= admin_l('Вставить в текст') ?>" data-use-label="<?= $imageLabel('Использовать изображение', 'Use image') ?>"><?= admin_l('Вставить в текст') ?></button>
            <small class="notice error" data-editor-image-error role="alert" hidden
                data-required="<?= $imageLabel('Выберите изображение и нажмите «Вставить в текст».', 'Choose an image and click Insert into text.') ?>"
                data-invalid="<?= $imageLabel('Укажите полный URL изображения или путь от корня сайта.', 'Enter a full image URL or a path from the site root.') ?>"></small>
        </div>
        </div>
    </div>
    <div class="panel editor-picker" id="<?= site_escape($editorId) ?>-picker-icon" data-editor-picker="icon" hidden>
        <?php $pickerHeading($editorLabels['icon']); ?>
        <div class="editor-picker-body form-stack">
            <?php require __DIR__ . '/icon-picker-results.php'; ?>
        </div>
    </div>
    <textarea id="<?= site_escape($editorId) ?>" name="<?= site_escape($editorName) ?>" data-editor-source data-code-mode="html" spellcheck="false" rows="<?= $editorWithComponents ? 12 : 6 ?>"><?= "\n" . site_escape($editorValue) ?></textarea>
    <div class="editor-visual-surface" data-editor-surface hidden>
        <iframe data-editor-frame hidden sandbox="allow-same-origin"
            data-src="<?= site_escape(site_url($base, 'templates/editor-frame.php') . '?v=' . site_update_version()) ?>" title="<?= admin_l('Визуальный редактор страницы') ?>"></iframe>
        <div class="editor-image-selection" data-editor-image-selection hidden>
            <span data-editor-image-resize title="<?= admin_l('Изменить размер') ?>" aria-hidden="true"></span>
        </div>
    </div>
    <?php require __DIR__ . '/content-editor-tools.php'; ?>
    <p class="hint" data-editor-feedback role="status" hidden></p>
    <?php if ($editorWithMedia) require __DIR__ . '/component-media-view.php'; ?>
</div>
