<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}
$labels = site_design_labels($adminLanguage);
$storeLabels = admin_theme_store_labels($adminLanguage);
$themes = admin_design_themes();
$active = site_design_active();
$selected = admin_input($_GET, 'theme');
if (!in_array($selected, $themes, true)) $selected = $active !== '' ? $active : 'demo';
$file = admin_input($_GET, 'file');
$inventory = admin_design_inventory();
if (!in_array($file, $inventory, true)) $file = '';
$draftPath = $file === '' ? '' : site_design_file($selected, $file);
$asset = preg_match('/\.(?:css|js)\z/', $file) === 1;
$starterThemes = admin_design_starter_names();
$label = static function (string $id) use ($labels, $starterThemes): string {
    return $id === '' ? $labels[1] : ($id === 'demo' ? $labels[2] : ($starterThemes[$id] ?? site_design_name($id)));
};
$themeContentText = require SITE_ROOT . '/app/help-theme-content.php';
$themeContentText = $themeContentText[$adminLanguage] ?? $themeContentText['ru'];
$updateLabels = require SITE_ROOT . '/app/help-theme-update.php';
$updateLabels = $updateLabels[$adminLanguage] ?? $updateLabels['ru'];
$url = $designUrl . '&theme=' . rawurlencode($selected);
$source = $draftPath === '' ? '' : (string) @file_get_contents($draftPath, false, null, 0, 262145);
?>
<h1><?= site_escape($labels[0]) ?></h1>
<p class="hint"><?= site_escape($themeContentText[0]) ?></p>
<p><a class="button" href="<?= site_escape($designUrl . '&store=1#theme-store') ?>"><i class="fa-solid fa-palette" aria-hidden="true"></i> <?= site_escape($storeLabels[0]) ?></a></p>
<?php if (isset($_GET['store'])) require __DIR__ . '/theme-store-view.php'; ?>
<section class="panel form-stack">
    <form class="header-actions design-theme-actions" method="post" action="<?= site_escape($designUrl) ?>">
        <input type="hidden" name="csrf" value="<?= site_escape($token) ?>">
        <input type="hidden" name="action" value="design-apply">
        <label class="field"><?= site_escape($labels[3]) ?>
            <select name="theme" data-active-theme-select>
                <?php foreach (array_merge([''], $themes) as $id): ?>
                    <?php
                    $complete = ($id !== '' && site_design_content($id) !== '') || (isset($starterThemes[$id]) && site_design_directory($id) === '');
                    $info = admin_design_offer_info($id);
                    $features = ['storefront'=>!empty($info['storefront']),'site'=>$complete,'siteShop'=>$complete && !empty($info['siteShop'])];
                    $caption = $label($id) . ' — ' . $storeLabels[2] . ' · ' . $storeLabels[$features['storefront'] ? 5 : 6];
                    if ($complete) $caption .= ' — ' . $storeLabels[4] . ' · ' . $storeLabels[$features['siteShop'] ? 5 : 6];
                    ?>
                    <option value="<?= site_escape($id) ?>" data-theme-features="<?= site_escape(json_encode($features)) ?>"<?= $id === $active ? ' selected' : '' ?>><?= site_escape($caption) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <button class="button-primary" type="submit"><?= site_escape($labels[4]) ?></button>
    </form>
    <?php
    $selectedInfo = admin_design_offer_info($selected);
    $selectedInfo['site'] = site_design_content($selected) !== '' || (isset($starterThemes[$selected]) && site_design_directory($selected) === '');
    ?>
    <div data-active-theme-badges data-yes="<?= site_escape($storeLabels[5]) ?>" data-no="<?= site_escape($storeLabels[6]) ?>">
        <?= admin_theme_store_badges($selectedInfo, $storeLabels) ?>
    </div>
    <?php if (!empty($selectedInfo['storePackage']) || admin_theme_store_builtin_demo($selected) !== null): ?>
        <div><a class="button" href="<?= site_escape($designUrl . '&store=1&demo=' . rawurlencode($selected) . '#theme-demo') ?>"><?= site_escape($storeLabels[10]) ?></a></div>
    <?php endif; ?>
    <details class="field-config disclosure-panel">
        <summary><span class="disclosure-arrow" aria-hidden="true"></span><?= site_escape($labels[5]) ?></summary>
        <form class="form-stack" method="post" action="<?= site_escape($designUrl) ?>">
            <input type="hidden" name="csrf" value="<?= site_escape($token) ?>">
            <input type="hidden" name="action" value="design-create">
            <label class="field"><?= site_escape($labels[6]) ?>
                <input name="theme" required maxlength="64" pattern="[a-z0-9_\-]{1,64}" placeholder="my-theme">
            </label>
            <div><button class="button-primary" type="submit"><?= site_escape($labels[5]) ?></button></div>
        </form>
    </details>
</section>
<?php if (isset($starterThemes[$selected]) && site_design_directory($selected) !== ''): ?>
    <p><a class="button" href="<?= site_escape($updatesUrl . '&theme=' . rawurlencode($selected) . '#update-theme') ?>"><?= site_escape($updateLabels[0]) ?></a></p>
<?php endif; ?>
<?php if (site_design_directory($selected) !== '' && ($active !== '' || isset($_GET['theme']))): ?>
<details class="panel disclosure-panel design-theme-files"<?= $file !== '' ? ' open' : '' ?>>
    <summary><span class="disclosure-arrow" aria-hidden="true"></span><?= site_escape($themeContentText[2]) ?></summary>
    <p class="hint"><?= site_escape($themeContentText[3]) ?></p>
    <p class="hint"><?= site_escape($labels[9]) ?></p>
    <h2><code>templates/themes/<?= site_escape($selected) ?>/</code></h2>
    <form class="header-actions" method="get" action="<?= site_escape($adminUrl) ?>">
        <input type="hidden" name="design" value="1">
        <input type="hidden" name="theme" value="<?= site_escape($selected) ?>">
        <label class="field"><?= site_escape($labels[7]) ?>
            <select name="file" required>
                <option value="">—</option>
                <?php foreach ($inventory as $path): ?>
                    <option value="<?= site_escape($path) ?>"<?= $path === $file ? ' selected' : '' ?>><?= site_escape($path) ?><?= site_design_file($selected, $path) !== '' ? ' ✓' : '' ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <button type="submit"><?= admin_l('Открыть') ?></button>
    </form>
    <?php if ($file !== ''): ?>
        <form class="form-stack" method="post" action="<?= site_escape($url . '&file=' . rawurlencode($file)) ?>">
            <input type="hidden" name="csrf" value="<?= site_escape($token) ?>">
            <input type="hidden" name="theme" value="<?= site_escape($selected) ?>">
            <input type="hidden" name="file" value="<?= site_escape($file) ?>">
            <?php if ($draftPath === ''): ?>
                <input type="hidden" name="action" value="design-copy">
                <div><button class="button-primary" type="submit"><?= site_escape($asset ? $labels[11] : $labels[8]) ?></button></div>
            <?php else: ?>
                <input type="hidden" name="action" value="design-save">
                <input type="hidden" name="revision" value="<?= site_escape((string) hash_file('sha256', $draftPath)) ?>">
                <label class="field"><code><?= site_escape($file) ?></code>
                    <textarea name="source" rows="16" spellcheck="false" data-code-mode="<?= $asset ? (substr($file, -3) === 'css' ? 'css' : 'javascript') : 'php-mixed' ?>"><?= "\n" . site_escape($error !== '' && isset($_POST['source']) ? admin_input($_POST, 'source') : $source) ?></textarea>
                </label>
                <div><button class="button-primary" type="submit"><?= admin_l('Сохранить') ?></button></div>
            <?php endif; ?>
        </form>
        <?php if ($draftPath !== ''): ?>
            <details class="disclosure-panel design-theme-files">
                <summary><?= site_escape($themeContentText[4]) ?></summary>
                <form class="form-stack" method="post" action="<?= site_escape($url . '&file=' . rawurlencode($file)) ?>">
                    <input type="hidden" name="csrf" value="<?= site_escape($token) ?>">
                    <input type="hidden" name="theme" value="<?= site_escape($selected) ?>">
                    <input type="hidden" name="file" value="<?= site_escape($file) ?>">
                    <input type="hidden" name="revision" value="<?= site_escape((string) hash_file('sha256', $draftPath)) ?>">
                    <label class="choice"><input type="checkbox" name="confirm" value="1" required> <?= site_escape($themeContentText[5]) ?></label>
                    <div><button class="button-danger" name="action" value="design-reset" type="submit"><?= site_escape($themeContentText[4]) ?></button></div>
                </form>
            </details>
        <?php endif; ?>
    <?php endif; ?>
</details>
<?php endif; ?>
