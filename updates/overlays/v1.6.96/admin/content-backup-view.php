<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}
$backupLocale = site_localized_help(require SITE_ROOT . '/app/help-workflow.php', $adminLanguage);
$backupLocale = ($backupLocale[$adminLanguage] ?? $backupLocale['ru'])['backup'];
$archives = admin_backup_list();
$backupDeleteLabels = admin_backup_delete_labels($adminLanguage);
?>
<?php
$scheduleLabels = admin_schedule_labels($adminLanguage);
$schedule = admin_jobs_settings()['backupSchedule'];
?>
<h1><?= site_escape($scheduleLabels[0]) ?></h1>
<?php if (admin_backup_needs_attention()): ?>
    <p class="notice" role="status"><?= site_escape($scheduleLabels[1]) ?></p>
<?php endif; ?>
<section class="panel form-stack" id="content-backups">
    <h2><?= site_escape($backupLocale[0]) ?></h2>
    <p class="hint"><?= site_escape($backupLocale[5]) ?></p>
    <form method="post" action="<?= site_escape($backupsUrl) ?>#content-backups">
        <input type="hidden" name="csrf" value="<?= site_escape($token) ?>">
        <button class="button-primary" name="action" value="create-content-backup"><i class="fa-solid fa-box-archive" aria-hidden="true"></i> <?= site_escape($backupLocale[1]) ?></button>
    </form>
    <form class="header-actions" method="post" action="<?= site_escape($backupsUrl) ?>#content-backups" enctype="multipart/form-data">
        <input type="hidden" name="csrf" value="<?= site_escape($token) ?>">
        <input type="file" name="archive" accept=".zip,application/zip" aria-label="<?= site_escape($backupLocale[2]) ?>" required>
        <button name="action" value="import-content-backup"><?= site_escape($backupLocale[2]) ?></button>
    </form>
    <?php foreach ($archives as $name => $size): ?>
        <details data-content-backup>
            <summary><code><?= site_escape($name) ?></code> <small class="hint"><?= site_escape(number_format($size / 1048576, 2)) ?> MiB</small></summary>
            <form class="form-stack" method="post" action="<?= site_escape($backupsUrl) ?>#content-backups">
                <input type="hidden" name="csrf" value="<?= site_escape($token) ?>">
                <input type="hidden" name="backup" value="<?= site_escape($name) ?>">
                <label class="choice"><input type="checkbox" name="confirm" value="1" required> <?= site_escape($backupLocale[6]) ?></label>
                <div class="header-actions">
                    <a class="button" href="<?= site_escape($backupsUrl . '&download_backup=' . rawurlencode($name)) ?>"><?= site_escape($backupLocale[3]) ?></a>
                    <button class="button-danger" name="action" value="restore-content-backup"><?= site_escape($backupLocale[4]) ?></button>
                </div>
            </form>
            <details class="backup-delete">
                <summary><?= site_escape($backupDeleteLabels[0]) ?></summary>
                <form class="form-stack" method="post" action="<?= site_escape($backupsUrl) ?>#content-backups">
                    <input type="hidden" name="csrf" value="<?= site_escape($token) ?>">
                    <input type="hidden" name="backup" value="<?= site_escape($name) ?>">
                    <label class="choice"><input type="checkbox" name="confirm_delete" value="1" required> <?= site_escape($backupDeleteLabels[1]) ?></label>
                    <div><button type="submit" class="button-danger" name="action" value="delete-content-backup"><i class="fa-solid fa-trash" aria-hidden="true"></i> <?= site_escape($backupDeleteLabels[0]) ?></button></div>
                </form>
            </details>
        </details>
    <?php endforeach; ?>
</section>
<form class="panel form-stack" method="post" id="backup-schedule" action="<?= site_escape($backupsUrl) ?>">
    <h2><?= site_escape($scheduleLabels[2]) ?></h2>
    <input type="hidden" name="csrf" value="<?= site_escape($token) ?>">
    <input type="hidden" name="action" value="save-backup-jobs">
    <label class="choice"><input type="checkbox" name="backup" value="1"<?= $schedule['backup'] ? ' checked' : '' ?>> <?= site_escape($scheduleLabels[9]) ?></label>
    <?php require __DIR__ . '/job-schedule-view.php'; ?>
</form>
