<?php
if (!defined('SITE_ROOT')) { http_response_code(404); exit; }
$label = static function (string $key): string { return site_escape(shop_label($key)); };
$modules = shop_integration_catalog();
$drivers = shop_integration_drivers();
$configs = shop_integration_config();
$groupIcons = ['marketplace_api'=>'store', 'delivery_api'=>'truck', 'payment_api'=>'credit-card'];
$selected = shop_input($_GET, 'integration');
$remote = $_SESSION['shopIntegrationCatalog'] ?? null;
$checked = $_SESSION['shopIntegrationChecked'] ?? '';
unset($_SESSION['shopIntegrationChecked']);
?>
<div class="shop-orders">
    <div class="admin-heading"><h2><?= $label('integrations') ?></h2>
        <a href="https://github.com/<?= SITE_SHOP_REPOSITORY ?>/tree/main/marketplace-integrations">GitHub <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i></a>
    </div>
    <nav class="site-menu scope-menu shop-catalog-nav-links" aria-label="<?= $label('integrations') ?>">
        <?php foreach ($groupIcons as $group => $icon): ?><a href="#shop-<?= $group ?>"><i class="fa-solid fa-<?= $icon ?>" aria-hidden="true"></i> <?= $label($group) ?></a><?php endforeach; ?>
    </nav>
    <section class="panel form-stack shop-integration-catalog" id="shop-integration-catalog">
        <div class="shop-integration-toolbar">
            <h3><i class="fa-brands fa-github" aria-hidden="true"></i> <?= $label('integration_catalog') ?></h3>
            <form method="post" action="<?= site_escape($tabUrl . '#shop-integration-catalog') ?>">
                <input type="hidden" name="csrf" value="<?= site_escape($moduleToken) ?>">
                <button name="action" value="shop-integration-refresh" type="submit"><i class="fa-solid fa-rotate" aria-hidden="true"></i> <?= $label('integration_refresh') ?></button>
            </form>
        </div>
        <?php if ($remote !== null):
            $available = array_filter($remote, static function (array $module, string $id) use ($modules): bool {
                return !isset($modules[$id]) || version_compare($module['version'], $modules[$id]['version'], '>');
            }, ARRAY_FILTER_USE_BOTH);
            ?>
            <?php if (!$available): ?><p><?= $label('integration_current') ?></p><?php endif; ?>
            <?php foreach ($available as $id => $module):
                $type = $drivers[$module['driver']][1];
                ?>
                <form method="post" action="<?= site_escape($tabUrl . '#shop-integration-catalog') ?>" class="shop-integration-module">
                    <input type="hidden" name="csrf" value="<?= site_escape($moduleToken) ?>">
                    <input type="hidden" name="integration" value="<?= site_escape($id) ?>">
                    <div><strong><?= site_escape($drivers[$module['driver']][0]) ?></strong> <code><?= site_escape($module['version']) ?></code>
                        <small><i class="fa-solid fa-<?= $groupIcons[$type] ?>" aria-hidden="true"></i> <?= $label($type) ?></small></div>
                    <button name="action" value="shop-integration-install" type="submit"><i class="fa-solid fa-download" aria-hidden="true"></i>
                        <?= $label(isset($modules[$id]) ? 'integration_update' : 'integration_install') ?></button>
                </form>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>
    <?php foreach ($groupIcons as $group => $icon): ?>
        <section class="shop-integration-group" id="shop-<?= $group ?>">
            <h3><i class="fa-solid fa-<?= $icon ?>" aria-hidden="true"></i> <?= $label($group) ?></h3>
            <div class="shop-integration-grid">
                <?php foreach ($modules as $id => $module):
                    [$title, $type, $fields, $secrets, $docs] = $drivers[$module['driver']];
                    if ($type !== $group) continue;
                    $config = $configs[$id] ?? ['fields'=>[], 'secrets'=>[]];
                    $enabled = !empty($config['enabled']);
                    ?>
                    <details class="panel shop-integration-card"<?= $id === $selected ? ' open' : '' ?>>
                        <summary><strong><?= site_escape($module['name']) ?></strong>
                            <small class="<?= $enabled ? 'shop-order-status' : '' ?>"><?= $label($enabled ? 'integration_enabled' : 'integration_disabled') ?></small></summary>
                        <div class="shop-order-detail">
                            <span><code><?= site_escape($module['version']) ?></code> · <a href="<?= site_escape($docs) ?>"><?= $label('integration_docs') ?></a></span>
                            <?php if ($type !== 'payment_api'): ?><p><?= $label('integration_connection_only') ?></p><?php endif; ?>
                            <?php if ($checked === $id): ?><p role="status"><?= $label('integration_connected') ?></p><?php endif; ?>
                            <form class="form-stack" method="post" action="<?= site_escape($tabUrl . '&integration=' . rawurlencode($id)) ?>">
                                <input type="hidden" name="csrf" value="<?= site_escape($moduleToken) ?>">
                                <input type="hidden" name="integration" value="<?= site_escape($id) ?>">
                                <input type="hidden" name="revision" value="<?= hash('sha256', serialize($config)) ?>">
                                <?php foreach ($fields as $field): ?>
                                    <label><?= $label($field) ?><input name="<?= site_escape($field) ?>" value="<?= site_escape($config['fields'][$field] ?? '') ?>" maxlength="4096"></label>
                                <?php endforeach; ?>
                                <?php foreach ($secrets as $field): ?>
                                    <label><?= $label($field) ?><input type="password" name="<?= site_escape($field) ?>" autocomplete="new-password" maxlength="4096"
                                        <?= isset($config['secrets'][$field]) ? 'placeholder="' . $label('integration_secret_saved') . '"' : '' ?>></label>
                                    <?php if (isset($config['secrets'][$field])): ?>
                                        <label class="choice"><input type="checkbox" name="clear[<?= site_escape($field) ?>]" value="1"> <?= $label('remove') ?> · <?= $label($field) ?></label>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                <label class="choice"><input type="checkbox" name="enabled" value="1"<?= $enabled ? ' checked' : '' ?>> <?= $label('integration_on') ?></label>
                                <div class="shop-order-controls">
                                    <button type="submit" class="button-primary" name="action" value="shop-integration-save"><?= $label('save') ?></button>
                                    <?php if (shop_integration_can_check($module['driver']) && count($config['secrets']) === count($secrets)): ?>
                                        <button type="submit" name="action" value="shop-integration-check"><?= $label('integration_check') ?></button>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </details>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endforeach; ?>
</div>
