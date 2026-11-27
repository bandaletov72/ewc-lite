<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}
?>
<main class="<?= empty($embeddedContent) ? 'container ' : '' ?>site-main" id="content" lang="<?= site_escape($page['language'] ?? 'ru') ?>">
    <?php if (!empty($components['shopEdit']) && !empty($components['shopPageTemplate']) && function_exists('shop_live_editor_link')): ?>
        <div class="layout-edit-outline shop-edit-region" data-shop-edit-region>
            <h1><?= site_escape($heading) ?></h1>
            <div class="layout-edit-actions"><?= shop_live_editor_link($base, 'template', $components['shopPageTemplate']) ?></div>
        </div>
    <?php else: ?>
        <h1><?= site_escape($heading) ?></h1>
    <?php endif; ?>
    <div class="page-content">
        <?= $content ?>
    </div>
</main>
