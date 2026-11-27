<?php
if (!defined('SITE_ROOT')) { http_response_code(404); exit; }

$scheduleLabels = admin_schedule_labels($adminLanguage);
$schedule = admin_jobs_settings()['updateSchedule'];
$releaseLabels = admin_update_progress_labels($adminLanguage);
$spamLabels = site_spam_admin_labels($adminLanguage);
$updateLabels = require SITE_ROOT . '/app/help-theme-update.php';
$updateLabels = $updateLabels[$adminLanguage] ?? $updateLabels['ru'];
$repository = site_update_github_repo();
$starterThemes = admin_design_starter_names();
$active = site_design_active();
$selected = admin_update_theme_selection(admin_input($_GET, 'theme'), $active);
$themeUpdateUrl = $updatesUrl . '&theme=' . rawurlencode($selected === '' ? 'system' : $selected);
$namedTheme = isset($starterThemes[$selected]);
$designLabels = site_design_labels($adminLanguage);
$themeName = $selected === '' ? $designLabels[1] : ($selected === 'demo' ? $designLabels[2] : ($starterThemes[$selected] ?? site_design_name($selected)));
$versions = admin_design_versions($selected);
$versionLabels = [
    'engine' => [admin_t('Обновления движка'), site_update_version()],
    'theme' => [$scheduleLabels[12] . ' · ' . $themeName, $versions['installed']],
    'shop' => [admin_t('Маркетплейс'), site_shop_version()],
    'spam' => [$scheduleLabels[11], site_spam_version()],
];
?>
<h1><?= admin_l('Обновления') ?></h1>
<?php if ($repository !== ''): ?>
    <p><?= admin_l('Источник обновлений') ?> <a href="<?= site_escape('https://github.com/' . $repository) ?>"><?= site_escape($repository) ?></a>.</p>
<?php else: ?>
    <p><?= admin_l('Задайте репозиторий GitHub в') ?> <code>SITE_GITHUB_REPO</code> <?= admin_l('или') ?> <code>maintenance/github.php</code>.</p>
<?php endif; ?>
<p><button type="button" data-update-check><?= site_escape($scheduleLabels[10]) ?></button> <a href="#update-schedule"><?= site_escape($scheduleLabels[18]) ?></a></p>
<?php foreach ($versionLabels as $item => list($title, $localVersion)): ?>
    <section class="panel form-stack" id="update-<?= $item ?>" data-update-item="<?= $item ?>"
        data-newer="<?= site_escape($scheduleLabels[16]) ?>" data-current="<?= site_escape($scheduleLabels[15]) ?>"
        data-unavailable="<?= site_escape($spamLabels[13]) ?>">
        <h2><?= site_escape($title) ?></h2>
        <p role="status">
            <?= admin_l('Установлена версия') ?> <code data-update-installed><?= site_escape($localVersion ?: $updateLabels[10]) ?></code> ·
            <span data-update-remote-label data-live="<?= admin_l('Последняя версия на GitHub') ?>"
                data-cached="<?= admin_l('Последняя проверенная версия на GitHub') ?>"><?= admin_l('Последняя версия на GitHub') ?></span>
            <code data-update-version><?= admin_l('проверяется…') ?></code>
            <strong data-update-status></strong>
        </p>
        <div data-update-notes hidden>
            <h3><?= site_escape($releaseLabels['release-notes']) ?></h3>
            <?php if ($item === 'shop'): ?>
                <ul class="update-release-notes" data-update-note-list data-empty="<?= site_escape($releaseLabels['release-notes-empty']) ?>"></ul>
            <?php else: ?>
                <p class="update-release-notes" data-update-note-text data-empty="<?= site_escape($releaseLabels['release-notes-empty']) ?>"></p>
            <?php endif; ?>
        </div>
        <?php if ($item === 'engine'): ?>
            <form class="form-stack" method="post" action="<?= site_escape($updatesUrl) ?>" data-update-stream>
                <input type="hidden" name="action" value="update-engine">
                <input type="hidden" name="csrf" value="<?= site_escape($token) ?>">
                <div><button class="button-primary" type="submit"<?= $repository === '' ? ' disabled' : '' ?>><?= admin_l('Обновить движок') ?></button></div>
                <?php require __DIR__ . '/update-progress-view.php'; ?>
            </form>
        <?php elseif ($item === 'theme'): ?>
            <?php if ($namedTheme): ?>
                <p><?= site_escape($updateLabels[9]) ?> <code><?= site_escape($versions['available'] ?: $updateLabels[10]) ?></code></p>
                <?php if ($selected === 'dezinfektori' && admin_update_item($versions['pages'], $versions['availablePages'], '')['outdated']): ?>
                    <p class="hint"><?= site_escape($updateLabels[14]) ?></p>
                <?php endif; ?>
                <p class="hint" data-theme-awaits-engine hidden><?= site_escape($scheduleLabels[13]) ?></p>
                <?php require __DIR__ . '/theme-update-form.php'; ?>
            <?php elseif ($selected === '' || $selected === 'demo'): ?>
                <p><?= site_escape($updateLabels[9]) ?> <code><?= site_escape($versions['available']) ?></code></p>
                <p class="hint" data-theme-awaits-engine hidden><?= site_escape($scheduleLabels[13]) ?></p>
            <?php elseif ($versions['installed'] === ''): ?>
                <p class="hint"><?= site_escape($scheduleLabels[14]) ?></p>
            <?php endif; ?>
        <?php elseif ($item === 'shop'): ?>
            <form class="form-stack" method="post" action="<?= site_escape($updatesUrl . '#update-shop') ?>" data-update-stream>
                <input type="hidden" name="action" value="shop-package">
                <input type="hidden" name="csrf" value="<?= site_escape($token) ?>">
                <div><button class="button-primary" type="submit" data-update-when-newer disabled><?= admin_l(site_shop_version() === '' ? 'Установить модуль' : 'Обновить модуль') ?></button></div>
                <?php require __DIR__ . '/update-progress-view.php'; ?>
            </form>
        <?php else: ?>
            <form class="form-stack" method="post" action="<?= site_escape($updatesUrl) ?>" data-update-stream>
                <input type="hidden" name="action" value="refresh-spam">
                <input type="hidden" name="csrf" value="<?= site_escape($token) ?>">
                <div><button class="button-primary" type="submit"<?= $repository === '' ? ' disabled' : '' ?>><?= site_escape($spamLabels[0]) ?></button></div>
                <?php require __DIR__ . '/update-progress-view.php'; ?>
            </form>
        <?php endif; ?>
        <?php if ($item !== 'shop'): ?><label class="choice">
            <input type="checkbox" form="update-schedule" name="<?= $item ?>" value="1"<?= $schedule[$item] ? ' checked' : '' ?>>
            <?= site_escape($scheduleLabels[8]) ?>
        </label><?php endif; ?>
    </section>
<?php endforeach; ?>
<form class="panel form-stack" method="post" id="update-schedule" action="<?= site_escape($updatesUrl) ?>">
    <h2><?= site_escape($scheduleLabels[18]) ?></h2>
    <input type="hidden" name="csrf" value="<?= site_escape($token) ?>">
    <input type="hidden" name="action" value="save-update-jobs">
    <?php require __DIR__ . '/job-schedule-view.php'; ?>
</form>
