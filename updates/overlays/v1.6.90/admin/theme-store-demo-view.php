<?php if (!defined('SITE_ROOT')) { http_response_code(404); exit; } ?>
<section class="panel form-stack" id="theme-demo">
    <h2><?= site_escape($storeLabels[10] . ' · ' . $demoItem['name']) ?></h2>
    <?php if ($storeUnavailable): ?><p role="status"><?= site_escape($storeLabels[1]) ?></p><?php endif; ?>
    <?php if ($demoProducts): ?>
        <form class="form-stack" method="post" data-theme-demo
            action="<?= site_escape($designUrl . '&store=1&demo=' . rawurlencode($demoId) . '#theme-demo') ?>">
            <input type="hidden" name="action" value="theme-store-demo">
            <input type="hidden" name="csrf" value="<?= site_escape($token) ?>">
            <input type="hidden" name="theme" value="<?= site_escape($demoId) ?>">
            <input type="hidden" name="revision" value="<?= site_escape($demoItem['sha256']) ?>">
            <label hidden class="choice"><input type="checkbox" data-theme-demo-all checked> <?= site_escape($storeLabels[13]) ?></label>
            <div class="theme-demo-options">
                <?php foreach ($demoProducts as $number => $product): ?>
                    <label class="choice">
                        <input type="checkbox" name="products[]" value="<?= $number ?>" checked>
                        <span><?= site_escape($product['name']) ?></span>
                        <small><?= site_escape(site_format_number((string) $product['price'])) ?> ₽</small>
                    </label>
                <?php endforeach; ?>
            </div>
            <?php if (!site_shop_enabled()): ?><p class="hint"><?= site_escape($storeLabels[11]) ?></p><?php endif; ?>
            <div><button type="submit" class="button-primary"<?= site_shop_enabled() ? '' : ' disabled data-module-disabled="1"' ?>><?= site_escape($storeLabels[10]) ?></button></div>
        </form>
    <?php endif; ?>
</section>
