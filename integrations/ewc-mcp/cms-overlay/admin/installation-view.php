<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}
?>
<section class="panel setup-panel">
    <h1><?= admin_l('Первичная настройка сайта') ?></h1>
    <p><?= admin_l('Укажите название, выберите оформление и при желании загрузите логотип. Эти параметры можно будет изменить в настройках.') ?></p>
    <?php if ($error !== ''): ?>
        <p class="notice error" role="alert"><?= admin_l($error) ?></p>
    <?php endif; ?>
    <?php if ($recoveryNotice !== ''): ?>
        <p class="notice" role="status"><?= site_escape($recoveryLabels[$recoveryNotice]) ?></p>
    <?php endif; ?>
    <form class="form-stack" method="post" enctype="multipart/form-data" action="<?= site_escape($installationUrl) ?>">
        <input type="hidden" name="csrf" value="<?= site_escape($token) ?>">
        <input type="hidden" name="revision" value="<?= site_escape($installationRevision) ?>">
        <div class="field">
            <label for="initial-name"><?= admin_l('Название сайта') ?></label>
            <input id="initial-name" name="name" value="<?= site_escape($installationDraft['name']) ?>" maxlength="375" required>
        </div>
        <div class="field">
            <label for="initial-description"><?= admin_l('Метаописание по умолчанию') ?></label>
            <input id="initial-description" name="description" value="<?= site_escape($installationDraft['description']) ?>" maxlength="375">
        </div>
        <div class="field">
            <label for="initial-footer"><?= admin_l('Текст подвала') ?></label>
            <input id="initial-footer" name="footer" value="<?= site_escape($installationDraft['footer']) ?>" maxlength="375" placeholder="<?= admin_l('© [year] Название сайта') ?>">
            <small class="hint"><?= admin_l('[year] автоматически подставит текущий год.') ?></small>
        </div>
        <div class="fields">
            <div class="field">
                <label for="initial-palette"><?= admin_l('Палитра') ?></label>
                <select id="initial-palette" name="preset">
                    <option value=""><?= admin_l('Текущая палитра') ?></option>
                    <?php foreach (site_theme_state()['presets'] as $id => $preset): ?>
                        <option value="<?= site_escape($id) ?>"<?= $installationDraft['preset'] === $id ? ' selected' : '' ?>><?= site_escape($preset['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="field">
                <label for="initial-mode"><?= admin_l('Режим темы') ?></label>
                <select id="initial-mode" name="mode">
                    <?php foreach (['auto' => admin_t('Автоматически'), 'light' => admin_t('Светлый'), 'dark' => admin_t('Тёмный')] as $mode => $label): ?>
                        <option value="<?= $mode ?>"<?= $installationDraft['mode'] === $mode ? ' selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <?php if (function_exists('imagecreatefromstring') && function_exists('imagepng')): ?>
            <div class="field">
                <label for="initial-logo"><?= admin_l('Логотип') ?></label>
                <input id="initial-logo" name="logo" type="file" accept="image/png,image/jpeg,image/gif,image/webp">
                <small class="hint"><?= admin_l('PNG, JPEG, GIF или WebP. Из логотипа будет создан favicon.') ?></small>
            </div>
        <?php else: ?>
            <p class="hint"><?= admin_l('Логотип можно загрузить в настройках после подключения PHP GD.') ?></p>
        <?php endif; ?>
        <div class="header-actions">
            <button class="button-primary" type="submit" name="action" value="finish-installation"><?= admin_l('Сохранить и открыть админку') ?></button>
            <button type="submit" name="action" value="skip-installation" formnovalidate><?= admin_l('Продолжить с текущими настройками') ?></button>
        </div>
    </form>
</section>
