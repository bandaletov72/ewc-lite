<?php
if (!defined('SITE_ROOT')) { http_response_code(404); exit; }
$storeUrl = $designUrl . '&store=1#theme-store';
$storeItems = [];
$storeUnavailable = false;
try {
    $storeItems = admin_theme_store_catalog();
} catch (RuntimeException $exception) {
    $storeUnavailable = true;
}
?>
<section class="panel form-stack" id="theme-store">
    <div class="header-actions">
        <h2><?= site_escape($storeLabels[0]) ?></h2>
        <a href="<?= site_escape('https://github.com/' . site_update_github_repo() . '/tree/HEAD/theme-store') ?>">GitHub</a>
    </div>
    <?php if ($storeUnavailable): ?><p role="status"><?= site_escape($storeLabels[1]) ?></p><?php endif; ?>
    <div class="theme-store-admin-grid">
        <?php foreach ($storeItems as $id => $item): ?>
            <article class="theme-store-admin-card form-stack">
                <div class="theme-store-swatch" style="--theme-store-swatch:<?= site_escape($item['accent']) ?>" aria-hidden="true"></div>
                <h3><?= site_escape($item['name']) ?> <small><?= site_escape($item['version']) ?></small></h3>
                <p><?= site_escape($item['description']) ?></p>
                <?= admin_theme_store_badges($item, $storeLabels) ?>
                <?php if (in_array($id, $themes, true)): ?>
                    <div><a class="button" href="<?= site_escape($designUrl . '&theme=' . rawurlencode($id)) ?>"><?= site_escape($storeLabels[9]) ?></a></div>
                <?php else: ?>
                    <form class="form-stack" method="post" action="<?= site_escape($storeUrl) ?>" data-update-stream>
                        <input type="hidden" name="action" value="theme-store-install">
                        <input type="hidden" name="csrf" value="<?= site_escape($token) ?>">
                        <input type="hidden" name="theme" value="<?= site_escape($id) ?>">
                        <?php if ($item['site']): ?>
                            <label class="choice"><input type="checkbox" name="with_pages" value="1" checked> <?= site_escape($storeLabels[7]) ?></label>
                        <?php endif; ?>
                        <div><button type="submit" class="button-primary"><?= site_escape($storeLabels[8]) ?></button></div>
                        <?php require __DIR__ . '/update-progress-view.php'; ?>
                    </form>
                <?php endif; ?>
                <div><a href="<?= site_escape($designUrl . '&store=1&demo=' . rawurlencode($id) . '#theme-demo') ?>"><?= site_escape($storeLabels[10]) ?></a></div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
<?php
$demoId = admin_input($_GET, 'demo');
if ($demoId !== '' && isset($storeItems[$demoId])) {
    $demoItem = $storeItems[$demoId];
    $demoProducts = [];
    try {
        $demoProducts = admin_theme_store_package($demoId, $demoItem)['products'];
    } catch (RuntimeException $exception) {
        $storeUnavailable = true;
    }
    require __DIR__ . '/theme-store-demo-view.php';
}
?>
