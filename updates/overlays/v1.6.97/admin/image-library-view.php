<?php
if (!defined('SITE_ROOT')) { http_response_code(404); exit; }
$imageLibraryLabels = [
    'ru'=>['Поиск по имени файла','Файлы не найдены','Назад','Вперёд'],
    'en'=>['Search by filename','No files found','Previous','Next'],
    'de'=>['Nach Dateiname suchen','Keine Dateien gefunden','Zurück','Weiter'],
    'es'=>['Buscar por nombre de archivo','No hay archivos','Anterior','Siguiente'],
    'fr'=>['Rechercher par nom de fichier','Aucun fichier trouvé','Précédent','Suivant'],
    'ja'=>['ファイル名で検索','ファイルが見つかりません','前へ','次へ'],
    'ko'=>['파일 이름으로 검색','파일이 없습니다','이전','다음'],
    'pt'=>['Pesquisar por nome de ficheiro','Nenhum ficheiro encontrado','Anterior','Seguinte'],
    'uk'=>['Пошук за назвою файлу','Файлів не знайдено','Назад','Далі'],
    'vi'=>['Tìm theo tên tệp','Không tìm thấy tệp','Trước','Tiếp'],
    'zh'=>['按文件名搜索','没有找到文件','上一页','下一页'],
];
[$findImage, $noImages, $previousImages, $nextImages] = $imageLibraryLabels[$adminLanguage] ?? $imageLibraryLabels['en'];
?>
        <div class="editor-image-library form-stack" data-image-library<?= !empty($allowLibraryFiles) ? ' data-library-files' : '' ?>
            data-folder-confirm="<?= site_escape(site_media_folder_labels($adminLanguage)[1]) ?>"
            data-delete-title="<?= $imageLabel('Удалить выбранный файл или папку вместе с вложенными изображениями?', 'Delete this file or folder and its images?') ?>"
            data-error-text="<?= $imageLabel('Не удалось выполнить действие с изображениями.', 'The image operation failed.') ?>">
            <div class="header-actions">
                <select data-image-root aria-label="<?= $imageLabel('Каталог изображений', 'Image directory') ?>">
                    <?php if (!empty($shopLibraryRoot) && empty($standaloneLibrary)): ?><option value=":shop:"><?= site_escape(shop_label('media_library')) ?></option><?php endif; ?>
                    <?php foreach (admin_image_library_roots(!empty($standaloneLibrary) || !empty($allowLibraryFiles)) as $root): ?><option value="<?= site_escape($root) ?>"><?= site_escape($root) ?></option><?php endforeach; ?>
                    <?php if (!empty($shopLibraryRoot) && !empty($standaloneLibrary)): ?><option value=":shop:"><?= site_escape(shop_label('media_library')) ?> · <?= admin_l('Маркетплейс') ?></option><?php endif; ?>
                </select>
                <button type="button" data-image-parent hidden title="<?= $imageLabel('На уровень выше', 'Parent folder') ?>" aria-label="<?= $imageLabel('На уровень выше', 'Parent folder') ?>">↑</button>
                <code data-image-path aria-live="polite"></code>
                <label class="editor-image-search"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                    <input type="search" data-image-search aria-label="<?= site_escape($findImage) ?>" placeholder="<?= site_escape($findImage) ?>"></label>
            </div>
            <details class="editor-image-files"><summary><i class="fa-solid fa-upload" aria-hidden="true"></i> <?= admin_l('Загрузить файлы') ?></summary><div class="fields">
                <div class="field">
                    <label class="field"><?= empty($standaloneLibrary) ? $imageLabel('Загрузить фото', 'Upload photos') : admin_l('Загрузить файлы') ?>
                        <input type="file" data-image-upload accept="<?= empty($standaloneLibrary) && empty($allowLibraryFiles) ? 'image/jpeg,image/png,image/webp,image/gif,image/avif'
                            : site_escape('.' . implode(',.', admin_image_library_extensions(true))) ?>" multiple></label>
                    <?php if (!empty($standaloneLibrary)): ?><p class="hint"><?= site_escape($formatsLabel) ?></p><?php endif; ?>
                    <div><button type="button" data-image-upload-button><?= admin_l('Загрузить файлы') ?></button></div>
                </div>
                <div class="field">
                    <label class="field"><?= $imageLabel('Новая папка', 'New folder') ?><input data-image-folder-name maxlength="64"></label>
                    <div><button type="button" data-image-mkdir><?= $imageLabel('Создать папку', 'Create folder') ?></button></div>
                </div>
            </div></details>
            <div class="editor-image-browser">
                <div class="editor-image-results">
                    <ul class="editor-image-list" data-image-list></ul>
                    <p class="hint" data-image-empty hidden><?= site_escape($noImages) ?></p>
                    <div class="editor-image-pages">
                        <button type="button" data-image-page-step="-1" aria-label="<?= site_escape($previousImages) ?>" title="<?= site_escape($previousImages) ?>" disabled><i class="fa-solid fa-chevron-left" aria-hidden="true"></i></button>
                        <span data-image-page aria-live="polite"></span>
                        <button type="button" data-image-page-step="1" aria-label="<?= site_escape($nextImages) ?>" title="<?= site_escape($nextImages) ?>" disabled><i class="fa-solid fa-chevron-right" aria-hidden="true"></i></button>
                    </div>
                </div>
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
                    <div><button type="button" data-image-transform><?= empty($standaloneLibrary)
                        ? $imageLabel('Сохранить копию', 'Save a copy') : site_escape($mediaLibraryLabels[5]) ?></button></div>
                </div>
            </div>
            <p class="hint" data-image-library-status role="status" hidden></p>
        </div>
