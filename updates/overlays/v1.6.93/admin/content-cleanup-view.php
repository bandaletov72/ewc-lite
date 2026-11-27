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
        $archiveOnly = [];
        try { $unusedMedia = admin_unused_media(admin_backup_files(), $archiveOnly); } catch (RuntimeException $exception) {
            $unusedMedia = null;
            echo '<p class="notice error">' . admin_l($exception->getMessage()) . '</p>';
        }
        if ($unusedMedia === [] && !$archiveOnly): ?>
            <p class="hint"><?= site_escape($cleanupLabels[5]) ?></p>
        <?php elseif (is_array($unusedMedia)): ?>
            <?php
            $pageSize = 100;
            $mediaResults = $unusedMedia + $archiveOnly;
            $pages = (int) ceil(count($mediaResults) / $pageSize);
            $page = max(1, min($pages, (int) admin_input($_GET, 'media_page')));
            $pageItems = array_slice($mediaResults, ($page - 1) * $pageSize, $pageSize, true);
            $hasSelectable = (bool) array_intersect_key($pageItems, $unusedMedia);
            $pageUrl = static function (int $number) use ($maintenanceUrl): string {
                return $maintenanceUrl . '&scan_media=1&media_page=' . $number . '#unused-media';
            };
            ?>
            <p><?= site_escape(sprintf($cleanupLabels[7], count($unusedMedia))) ?></p>
            <?php if ($archiveOnly): ?><p class="hint"><i class="fa-solid fa-box-archive" aria-hidden="true"></i> <?= site_escape(sprintf($cleanupLabels[9], count($archiveOnly))) ?></p><?php endif; ?>
            <form class="form-stack" method="post" action="<?= site_escape($pageUrl($page)) ?>" data-media-cleanup data-shop-lightbox-group>
                <input type="hidden" name="csrf" value="<?= site_escape($token) ?>">
                <?php if ($hasSelectable): ?><label class="choice" hidden><input type="checkbox" data-media-cleanup-all> <?= site_escape($cleanupLabels[6]) ?></label><?php endif; ?>
                <?php $hasPreviews = false; $row = 0; ?>
                <ul class="cleanup-media-list">
                    <?php foreach ($pageItems as $path => $file):
                        $row++;
                        $size = @filesize($file);
                        $isImage = isset(ADMIN_CLEANUP_IMAGE_TYPES[strtolower(pathinfo($path, PATHINFO_EXTENSION))]);
                        $hasPreviews = $hasPreviews || $isImage;
                        ?>
                        <li class="cleanup-media-row">
                            <?php if (isset($archiveOnly[$path])): ?>
                                <i class="fa-solid fa-lock" title="<?= site_escape($cleanupLabels[10]) ?>" aria-label="<?= site_escape($cleanupLabels[10]) ?>"></i>
                            <?php else: ?>
                                <input type="checkbox" id="cleanup-media-<?= $row ?>" name="media[]" value="<?= site_escape($path) ?>">
                            <?php endif; ?>
                            <?php if ($isImage): ?>
                                <?php $preview = $maintenanceUrl . '&media_preview=' . rawurlencode($path); ?>
                                <a class="cleanup-media-thumb" href="<?= site_escape($preview) ?>" data-shop-media-item>
                                    <img src="<?= site_escape($preview) ?>" alt="<?= site_escape(basename($path)) ?>" width="64" height="48" loading="lazy" decoding="async">
                                </a>
                            <?php else: ?>
                                <span class="cleanup-media-thumb" aria-hidden="true"><i class="fa-regular fa-file"></i></span>
                            <?php endif; ?>
                            <?php if (isset($archiveOnly[$path])): ?><span><code><?= site_escape('/' . $path) ?></code> <small><?= site_escape($cleanupLabels[10]) ?></small></span>
                            <?php else: ?><label for="cleanup-media-<?= $row ?>"><code><?= site_escape('/' . $path) ?></code></label><?php endif; ?>
                            <span class="cleanup-media-size"><?= $size === false ? '—' : site_escape(admin_media_cleanup_size($size, $adminLanguage)) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <?php if ($hasSelectable): ?>
                    <label class="choice"><input type="checkbox" name="confirm" value="1" required> <?= site_escape($cleanupLabels[3]) ?></label>
                    <div><button class="button-danger" name="action" value="remove-unused-media"><?= site_escape($cleanupLabels[2]) ?></button></div>
                <?php endif; ?>
            </form>
            <?php if ($hasPreviews): ?>
                <dialog class="editor-dialog shop-media-lightbox" data-shop-media-dialog aria-labelledby="cleanup-media-caption">
                    <div class="header-actions editor-dialog-heading">
                        <strong id="cleanup-media-caption" data-shop-media-caption></strong>
                        <button type="button" data-shop-media-close aria-label="<?= admin_l('Закрыть') ?>" autofocus>
                            <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                        </button>
                    </div>
                    <img alt="">
                    <div class="shop-media-lightbox-nav">
                        <button type="button" data-shop-media-step="-1" aria-label="<?= admin_l('Назад') ?>"><i class="fa-solid fa-chevron-left" aria-hidden="true"></i></button>
                        <span data-shop-media-count aria-live="polite"></span>
                        <button type="button" data-shop-media-step="1" aria-label="<?= admin_l('Далее') ?>"><i class="fa-solid fa-chevron-right" aria-hidden="true"></i></button>
                    </div>
                </dialog>
            <?php endif; ?>
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
