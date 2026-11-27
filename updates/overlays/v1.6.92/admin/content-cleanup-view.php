<?php if (!defined('SITE_ROOT')) exit;
$cleanupLabels = site_localized_help(require SITE_ROOT . '/app/help-media-cleanup.php', $adminLanguage);
$cleanupLabels = $cleanupLabels[$adminLanguage] ?? $cleanupLabels['ru'];
?>
<section class="panel form-stack" id="unused-media">
    <h2><?= site_escape($cleanupLabels[0]) ?></h2>
    <p class="hint"><?= site_escape($cleanupLabels[4]) ?></p>
    <p class="hint"><?= site_escape($cleanupLabels[8]) ?></p>
    <div><a class="button" href="<?= site_escape($maintenanceUrl . '&scan_media=1#unused-media') ?>"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i> <?= site_escape($cleanupLabels[1]) ?></a></div>
    <?php if (admin_input($_GET, 'scan_media') === '1'):
        try { $unusedMedia = admin_unused_media(admin_backup_files()); } catch (RuntimeException $exception) {
            $unusedMedia = null;
            echo '<p class="notice error">' . admin_l($exception->getMessage()) . '</p>';
        }
        if ($unusedMedia === []): ?>
            <p class="hint"><?= site_escape($cleanupLabels[5]) ?></p>
        <?php elseif ($unusedMedia): ?>
            <?php
            $pageSize = 100;
            $pages = (int) ceil(count($unusedMedia) / $pageSize);
            $page = max(1, min($pages, (int) admin_input($_GET, 'media_page')));
            $pageUrl = static function (int $number) use ($maintenanceUrl): string {
                return $maintenanceUrl . '&scan_media=1&media_page=' . $number . '#unused-media';
            };
            ?>
            <p><?= site_escape(sprintf($cleanupLabels[7], count($unusedMedia))) ?></p>
            <form class="form-stack" method="post" action="<?= site_escape($pageUrl($page)) ?>" data-media-cleanup>
                <input type="hidden" name="csrf" value="<?= site_escape($token) ?>">
                <label class="choice" hidden><input type="checkbox" data-media-cleanup-all> <?= site_escape($cleanupLabels[6]) ?></label>
                <?php foreach (array_slice(array_keys($unusedMedia), ($page - 1) * $pageSize, $pageSize) as $path): ?>
                    <label class="choice"><input type="checkbox" name="media[]" value="<?= site_escape($path) ?>"><code><?= site_escape('/' . $path) ?></code></label>
                <?php endforeach; ?>
                <label class="choice"><input type="checkbox" name="confirm" value="1" required> <?= site_escape($cleanupLabels[3]) ?></label>
                <div><button class="button-danger" name="action" value="remove-unused-media"><?= site_escape($cleanupLabels[2]) ?></button></div>
            </form>
            <?php if ($pages > 1): ?>
                <nav class="header-actions" aria-label="<?= site_escape($cleanupLabels[0]) ?>">
                    <?php if ($page > 1): ?><a class="button" href="<?= site_escape($pageUrl($page - 1)) ?>"><?= admin_l('Назад') ?></a><?php endif; ?>
                    <span aria-current="page"><?= $page ?> / <?= $pages ?></span>
                    <?php if ($page < $pages): ?><a class="button" href="<?= site_escape($pageUrl($page + 1)) ?>"><?= admin_l('Далее') ?></a><?php endif; ?>
                </nav>
            <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>
</section>

