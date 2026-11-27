<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}
?>
<div class="admin-heading">
    <h1><?= admin_l('Формы') ?></h1>
    <a class="button button-primary" href="<?= site_escape($formsUrl . '&id=new') ?>"><?= admin_l('Новая форма') ?></a>
</div>
<?php if ($formEditorError !== ''): ?>
    <p class="notice error" role="alert"><?= admin_l($formEditorError) ?></p>
<?php endif; ?>
<div class="editor-grid">
    <nav class="panel" aria-label="<?= admin_l('Список форм') ?>">
        <ul class="page-list">
            <?php foreach ($forms as $id => $item): ?>
                <li><a href="<?= site_escape($formsUrl . '&id=' . rawurlencode($id)) ?>"<?= isset($formId) && $formId === $id && !$formIsNew ? ' aria-current="page"' : '' ?>>
                    <code>[<?= site_escape($id) ?>]</code><small><?= site_escape(admin_form_name($id, $item, $adminLanguage)) ?></small>
                </a></li>
            <?php endforeach; ?>
        </ul>
    </nav>
    <?php if ($formDraft !== null): ?>
        <?php
        $captcha = site_captcha_settings($formDraft, false);
        ?>
        <form class="panel form-stack" method="post" data-form-config action="<?= site_escape($formsUrl . '&id=' . rawurlencode($formIsNew ? 'new' : $formId)) ?>">
            <h2><?= $formIsNew ? admin_t('Новая форма') : admin_t('Редактировать форму') ?></h2>
            <input type="hidden" name="action" value="save-form">
            <input type="hidden" name="csrf" value="<?= site_escape($token) ?>">
            <input type="hidden" name="revision" value="<?= site_escape($formRevision) ?>">
            <div class="fields">
                <label class="field field-wide"><?= site_escape(site_edit_layout_labels($adminLanguage)[1]) ?>
                    <input name="title" maxlength="120" value="<?= site_escape($formDraft['title'] ?? '') ?>">
                </label>
                <div class="field">
                    <?php if ($formIsNew): ?>
                        <label for="form-code"><?= admin_l('Код формы') ?></label>
                        <input id="form-code" name="id" value="<?= site_escape($formId) ?>" maxlength="13" required>
                    <?php else: ?>
                        <input type="hidden" name="id" value="<?= site_escape($formId) ?>">
                        <?= site_copy_code('[' . $formId . ']', $adminLanguage, admin_t('Код формы')) ?>
                    <?php endif; ?>
                </div>
                <div class="field">
                    <label for="form-button"><?= admin_l('Текст кнопки') ?></label>
                    <input id="form-button" name="button" value="<?= site_escape($formDraft['button']) ?>" maxlength="375" required>
                </div>
            </div>
            <fieldset class="field-config form-stack">
                <legend>CAPTCHA</legend>
                <label class="choice">
                    <input type="checkbox" name="captcha_enabled" value="1"<?= $captcha['enabled'] ? ' checked' : '' ?>>
                    <?= admin_l('Использовать CAPTCHA') ?>
                </label>
                <div class="fields">
                    <label class="field"><?= admin_l('Ключ сайта') ?>
                        <input name="captcha_sitekey" value="<?= site_escape($captcha['sitekey']) ?>" maxlength="256" autocomplete="off">
                    </label>
                    <label class="field"><?= admin_l('Секретный ключ') ?>
                        <input type="password" name="captcha_secret" maxlength="256" autocomplete="new-password">
                        <small class="hint"><?= admin_l('Оставьте пустым, чтобы сохранить ранее введённый секретный ключ.') ?></small>
                    </label>
                </div>
                <p class="hint"><?= admin_l('Для галочки «Я не робот» нужны ключи Google reCAPTCHA v2, зарегистрированные для домена сайта.') ?></p>
                <p class="hint"><a href="<?= site_escape($captchaUrl) ?>"><?= admin_l('Общие настройки CAPTCHA') ?></a></p>
                <?php require __DIR__ . '/captcha-links.php'; ?>
            </fieldset>
            <div class="form-stack" data-form-rows>
                <?php foreach ($formDraft['fields'] as $index => $field): ?>
                    <?php require __DIR__ . '/form-field.php'; ?>
                <?php endforeach; ?>
            </div>
            <template data-empty-field>
                <?php
                $index = '__INDEX__';
                $field = ['name' => '', 'label' => '', 'type' => 'text', 'required' => false, 'options' => []];
                require __DIR__ . '/form-field.php';
                ?>
            </template>
            <div class="header-actions">
                <button type="button" data-add-field><?= admin_l('Добавить поле') ?></button>
            </div>
            <p><a href="<?= site_escape($mailTemplatesUrl . '&template=' . rawurlencode(site_form_mail_event($formId))) ?>"><?= site_escape(admin_mail_labels($adminLanguage)['title']) ?></a></p>
            <div>
                <button class="button-primary" type="submit"><?= admin_l('Сохранить форму') ?></button>
            </div>
        </form>
    <?php endif; ?>
</div>
