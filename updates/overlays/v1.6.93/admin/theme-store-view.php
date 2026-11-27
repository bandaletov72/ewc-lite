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
$remoteItems = $storeItems;
foreach ($themes as $id) {
    if (isset($storeItems[$id])) continue;
    $info = admin_design_offer_info($id);
    $accent = $info['storeAccent'] ?? '';
    if (($info['storeListing'] ?? false) !== true || !is_string($accent) || !site_theme_color($accent)) continue;
    $description = $info['storeDescription'] ?? [];
    $installed = site_design_directory($id) !== '';
    $versions = admin_design_versions($id);
    $hasPages = $installed ? site_design_content($id) !== '' : !empty($info['site']);
    $storeItems[$id] = [
        'name'=>(string) ($info['name'] ?? $id), 'version'=>$installed ? $versions['installed'] : $versions['available'],
        'accent'=>$accent, 'description'=>(string) ($description[$adminLanguage] ?? $description['en'] ?? ''),
        'storefront'=>!empty($info['storefront']), 'site'=>$hasPages,
        'siteShop'=>!empty($info['siteShop']) && $hasPages,
    ];
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
                <?php if ($item['description'] !== ''): ?><p><?= site_escape($item['description']) ?></p><?php endif; ?>
                <?= admin_theme_store_badges($item, $storeLabels) ?>
                <?php if (site_design_directory($id) !== ''): ?>
                    <div><a class="button" href="<?= site_escape($designUrl . '&theme=' . rawurlencode($id)) ?>"><?= site_escape($storeLabels[9]) ?></a></div>
                <?php elseif (!isset($remoteItems[$id])): ?>
                    <form method="post" action="<?= site_escape($designUrl) ?>">
                        <input type="hidden" name="action" value="design-apply">
                        <input type="hidden" name="csrf" value="<?= site_escape($token) ?>">
                        <input type="hidden" name="theme" value="<?= site_escape($id) ?>">
                        <button class="button-primary" type="submit"><?= site_escape($storeLabels[8]) ?></button>
                    </form>
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
                <?php if (isset($remoteItems[$id])): ?><div><a href="<?= site_escape($designUrl . '&store=1&demo=' . rawurlencode($id) . '#theme-demo') ?>"><?= site_escape($storeLabels[10]) ?></a></div><?php endif; ?>
            </article>
        <?php endforeach; ?>
    </div>
</section>
<?php
if (site_shop_enabled()) {
    require_once site_shop_directory() . '/module.php';
    if (function_exists('shop_decode_tables')) $demoBackups = admin_theme_store_catalog_backups();
}
$demoId = admin_input($_GET, 'demo');
if ($demoId !== '' && isset($remoteItems[$demoId])) {
    $demoItem = $remoteItems[$demoId];
    $demoSections = [];
    try {
        $demo = admin_theme_store_package($demoId, $demoItem);
        $demoSections = admin_theme_store_demo_sections($demo['products'], $demo['catalogTitle']);
    } catch (RuntimeException $exception) {
        $storeUnavailable = true;
    }
    require __DIR__ . '/theme-store-demo-view.php';
}
?>
<?php if (!empty($demoBackups)): ?>
    <section class="panel form-stack" id="theme-demo-backups">
        <h2><?= site_escape($storeLabels[16]) ?></h2>
        <form class="form-stack" method="post" action="<?= site_escape($designUrl . '&store=1#theme-demo-backups') ?>">
            <input type="hidden" name="action" value="theme-store-restore">
            <input type="hidden" name="csrf" value="<?= site_escape($token) ?>">
            <input type="hidden" name="catalog_revision" value="<?= site_escape(shop_generation()) ?>">
            <label class="field"><?= site_escape($storeLabels[17]) ?>
                <select name="backup" required>
                    <?php foreach ($demoBackups as $id => $backup): ?>
                        <option value="<?= site_escape($id) ?>"><?= site_escape(implode(' · ', array_filter([
                            $backup['name'], $backup['count'] === null ? '' : shop_label('products') . ' ' . site_format_number((string) $backup['count']),
                            date('Y-m-d H:i:s T', $backup['time'])], 'strlen'))) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label class="choice"><input type="checkbox" name="confirm_restore" value="1" required> <?= site_escape($storeLabels[18]) ?></label>
            <div><button type="submit"><?= site_escape($storeLabels[19]) ?></button></div>
        </form>
    </section>
<?php endif; ?>
