<?php if (!defined('SITE_ROOT')) { http_response_code(404); exit; } ?>
<section class="panel form-stack" id="theme-demo">
    <h2><?= site_escape($storeLabels[10] . ' · ' . $demoItem['name']) ?></h2>
    <?php if ($storeUnavailable): ?><p role="status"><?= site_escape($storeLabels[1]) ?></p><?php endif; ?>
    <?php if ($demoSections): ?>
        <?php $demoReady = site_shop_enabled() && function_exists('shop_decode_tables'); ?>
        <form class="form-stack" method="post" data-theme-demo data-update-stream
            action="<?= site_escape($designUrl . '&store=1&demo=' . rawurlencode($demoId) . '#theme-demo') ?>">
            <input type="hidden" name="action" value="theme-store-demo">
            <input type="hidden" name="csrf" value="<?= site_escape($token) ?>">
            <input type="hidden" name="theme" value="<?= site_escape($demoId) ?>">
            <input type="hidden" name="revision" value="<?= site_escape($demoItem['sha256']) ?>">
            <input type="hidden" name="catalog_revision" value="<?= site_escape(site_shop_enabled() ? shop_generation() : '') ?>">
            <fieldset class="form-stack theme-demo-selection">
            <legend><?= site_escape($storeLabels[28]) ?></legend>
            <label hidden class="choice"><input type="checkbox" data-theme-demo-all checked> <?= site_escape($storeLabels[35]) ?></label>
            <div class="theme-demo-options">
                <?php foreach ($demoSections as $key => $section): ?>
                    <label class="choice">
                        <input type="checkbox" name="sections[]" value="<?= site_escape($key) ?>" checked>
                        <i class="fa-regular fa-folder" aria-hidden="true"></i>
                        <span><?= site_escape($section['name']) ?></span>
                        <small aria-label="<?= site_escape($storeLabels[36]) ?>"><?= site_format_number((string) count($section['products'])) ?></small>
                    </label>
                <?php endforeach; ?>
            </div>
            </fieldset>
            <fieldset class="form-stack theme-demo-selection">
                <legend><?= site_escape($storeLabels[30]) ?></legend>
                <div class="theme-demo-options theme-demo-modes">
                    <?php foreach (['merge'=>[31,32],'replace'=>[33,34]] as $mode => [$title,$description]): ?>
                        <label class="choice"><input type="radio" name="catalog_mode" value="<?= $mode ?>"<?= $mode === 'merge' ? ' checked' : '' ?>>
                            <span><strong><?= site_escape($storeLabels[$title]) ?></strong><span class="hint"><?= site_escape($storeLabels[$description]) ?></span></span>
                        </label>
                    <?php endforeach; ?>
                </div>
                <label class="choice" data-theme-demo-confirm><input type="checkbox" name="confirm_replace" value="1"> <?= site_escape($storeLabels[14]) ?></label>
            </fieldset>
            <?php if (!$demoReady): ?><p class="hint"><?= site_escape($storeLabels[site_shop_enabled() ? 27 : 11]) ?></p><?php endif; ?>
            <div><button type="submit" class="button-primary"<?= $demoReady ? '' : ' disabled data-module-disabled="1"' ?>><?= site_escape($storeLabels[10]) ?></button></div>
            <?php $demoProgressLabel = $storeLabels[21]; require __DIR__ . '/update-progress-view.php'; unset($demoProgressLabel); ?>
        </form>
    <?php endif; ?>
</section>
