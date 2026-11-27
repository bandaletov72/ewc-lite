<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}
?>
<details class="cookie-preferences" data-cookie-preferences data-cookie-path="<?= site_escape(site_url($base)) ?>">
    <summary><?= site_escape(site_translation('Настройки cookie', $locale)) ?></summary>
    <div class="panel form-stack">
        <p><?= site_escape(site_translation('Выберите, какие необязательные сервисы разрешить.', $locale)) ?></p>
        <label class="choice"><input type="checkbox" checked disabled> <?= site_escape(site_translation('Необходимые', $locale)) ?></label>
        <label class="choice"><input type="checkbox" data-cookie-option="analytics"<?= site_cookie_allows('analytics') ? ' checked' : '' ?>> <?= site_escape(site_translation('Аналитика', $locale)) ?></label>
        <label class="choice"><input type="checkbox" data-cookie-option="maps"<?= site_cookie_allows('maps') ? ' checked' : '' ?>> <?= site_escape(site_translation('Встроенные карты', $locale)) ?></label>
        <?php if (site_jivo_widget() !== ''): ?>
            <label class="choice"><input type="checkbox" data-cookie-option="chat"<?= site_cookie_allows('chat') ? ' checked' : '' ?>> JivoSite</label>
        <?php endif; ?>
        <div class="header-actions">
            <button class="button-primary" type="button" data-cookie-choice="all"><?= site_escape(site_translation('Принять все', $locale)) ?></button>
            <button type="button" data-cookie-choice="essential"><?= site_escape(site_translation('Только необходимые', $locale)) ?></button>
            <button type="button" data-cookie-choice="selection"><?= site_escape(site_translation('Сохранить', $locale)) ?></button>
        </div>
    </div>
</details>
