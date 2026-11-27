<?php
if (!defined('SITE_ROOT')) { http_response_code(404); exit; }
require_once SITE_ROOT . '/app/shop-package.php';
$installedShop = site_shop_version();
$activeShop = site_shop_enabled();
$shopRelease = $_SESSION['shopRelease'] ?? [];
$releaseVersion = $shopRelease['version'] ?? '';
$updateAvailable = $releaseVersion !== '' && ($installedShop === '' || version_compare($releaseVersion, $installedShop, '>'));
$showRelease = $releaseVersion !== '' || in_array($_POST['action'] ?? '', ['shop-check', 'shop-package'], true);
$releaseLabels = admin_update_progress_labels($adminLanguage);
?>
<h1><?= admin_l('Маркетплейс') ?></h1>
<?php if ($activeShop):
    require_once site_shop_directory() . '/module.php';
    $moduleAdminUrl = $shopUrl;
    $moduleToken = $token;
    $moduleOwner = true;
    $roles = [];
    require site_shop_directory() . '/admin-view.php';
endif; ?>
<section class="panel form-stack shop-module-panel">
    <form method="post" action="<?= site_escape($shopUrl) ?>" class="form-stack">
        <input type="hidden" name="csrf" value="<?= site_escape($token) ?>">
        <input type="hidden" name="action" value="shop-toggle">
        <label class="choice"><input type="checkbox" name="enabled" value="1"<?= $activeShop ? ' checked' : '' ?><?= $installedShop === '' ? ' disabled' : '' ?>>
            <?= admin_l('Включить маркетплейс') ?></label>
        <div><button class="button-primary"<?= $installedShop === '' ? ' disabled' : '' ?>><?= admin_l('Сохранить') ?></button></div>
    </form>
    <p><strong><?= admin_l($activeShop ? 'Модуль активен' : 'Модуль отключён') ?></strong>
        <?php if ($installedShop !== ''): ?> · <code><?= site_escape($installedShop) ?></code><?php endif; ?></p>
    <?php if ($activeShop): ?>
        <div class="header-actions">
            <a href="<?= site_escape(site_url($base,'shop/')) ?>"><?= admin_l('Маркетплейс') ?> /shop/</a>
            <a href="<?= site_escape($designUrl . '&store=1#theme-store') ?>"><?= site_escape(admin_theme_store_labels($adminLanguage)[10]) ?> · GitHub</a>
        </div>
    <?php endif; ?>
</section>
<details class="panel shop-module-panel" id="shop-github"<?= $showRelease ? ' open' : '' ?>>
    <summary><?= admin_l('Установить или обновить модуль') ?></summary>
    <div class="form-stack shop-github-controls">
    <p><a href="https://github.com/<?= SITE_SHOP_REPOSITORY ?>/releases"><?= SITE_SHOP_REPOSITORY ?> · GitHub Releases</a></p>
    <form method="post" class="header-actions" action="<?= site_escape($shopUrl . '#shop-github') ?>" data-shop-release-check>
        <input type="hidden" name="csrf" value="<?= site_escape($token) ?>">
        <input type="hidden" name="action" value="shop-check">
        <button type="submit"><?= admin_l('Проверить обновления') ?></button>
        <progress hidden aria-label="<?= site_escape($releaseLabels['source']) ?>"></progress>
    </form>
    <?php if ($releaseVersion !== ''): ?>
        <p><strong><?= admin_l('Версия на GitHub') ?></strong> <code><?= site_escape($releaseVersion) ?></code>
            · <a href="https://github.com/<?= SITE_SHOP_REPOSITORY ?>/releases/tag/<?= rawurlencode($shopRelease['tag']) ?>"><?= site_escape($shopRelease['tag']) ?></a></p>
        <details>
            <summary><?= site_escape($releaseLabels['release-notes']) ?></summary>
            <p class="shop-release-notes"><?= site_escape($shopRelease['notes'] ?: $releaseLabels['release-notes-empty']) ?></p>
        </details>
        <?php if ($updateAvailable): ?>
            <form method="post" class="form-stack" action="<?= site_escape($shopUrl . '#shop-github') ?>" data-update-stream>
                <input type="hidden" name="csrf" value="<?= site_escape($token) ?>">
                <input type="hidden" name="action" value="shop-package">
                <div><button type="submit" class="button-primary"><?= admin_l('Установить или обновить модуль') ?></button></div>
                <?php require __DIR__ . '/update-progress-view.php'; ?>
            </form>
        <?php else: ?><p role="status"><?= admin_l('Установлена актуальная версия.') ?></p><?php endif; ?>
    <?php endif; ?>
    </div>
</details>
<?php if (!$activeShop && $installedShop !== ''): ?>
    <form method="post" class="panel form-stack" action="<?= site_escape($shopUrl) ?>">
        <input type="hidden" name="csrf" value="<?= site_escape($token) ?>">
        <input type="hidden" name="action" value="shop-remove">
        <label class="choice"><input type="checkbox" name="confirm" value="1" required> <?= admin_l('Удалить файлы модуля. Данные и изображения сохранятся.') ?></label>
        <div><button class="button-danger"><?= admin_l('Удалить') ?></button></div>
    </form>
<?php endif; ?>
