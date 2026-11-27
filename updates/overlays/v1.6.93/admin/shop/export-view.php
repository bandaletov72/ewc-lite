<?php
if (!defined('SITE_ROOT')) { http_response_code(404); exit; }
require_once __DIR__ . '/export.php';
$label = static function (string $key): string { return site_escape(shop_label($key)); };
$catalog = shop_index();
$access = shop_export_key($shopState, 'yml') !== '';
$root = shop_export_root($base);
?>
<section class="panel form-stack">
    <h2><i class="fa-solid fa-file-export" aria-hidden="true"></i> <?= $label('export_download') ?></h2>
    <form method="get" class="form-stack" action="<?= site_escape(strtok($moduleAdminUrl, '?')) ?>">
        <input type="hidden" name="shop" value="1"><input type="hidden" name="tab" value="export">
        <label class="field"><?= $label('categories') ?><select name="category">
            <option value=""><?= $label('record_all_categories') ?></option>
            <?php foreach ($catalog['tree'] as $id => $trail): ?>
                <option value="<?= site_escape((string) $id) ?>"><?= site_escape(implode(' / ', array_map(static function ($item) use ($catalog): string {
                    return $catalog['categories'][$item]['name'];
                }, $trail))) ?></option>
            <?php endforeach; ?>
        </select></label>
        <div class="header-actions">
            <?php foreach (SHOP_EXPORT_FORMATS as $format => [,,$icon]): ?>
                <button type="submit" name="download" value="<?= $format ?>"<?= $format === 'xlsx' && !class_exists('ZipArchive') ? ' disabled title="' . $label('export_zip') . '"' : '' ?>>
                    <i class="fa-solid fa-<?= $icon ?>" aria-hidden="true"></i> <?= strtoupper($format) ?>
                </button>
            <?php endforeach; ?>
        </div>
    </form>
</section>
<section class="panel form-stack" data-shop-feed-report data-error="<?= $label('try_again') ?>" data-notice="<?= site_escape($notice ?? '') ?>">
    <div class="admin-heading">
        <h2><i class="fa-solid fa-circle-check" aria-hidden="true"></i> <?= $label('feed_check') ?></h2>
        <a class="button" href="<?= site_escape($tabUrl . '&validate=yml#shop-feed-check') ?>"><?= $label('feed_check_action') ?></a>
    </div>
    <p class="notice shop-list-message" data-shop-list-message role="status" hidden></p>
    <?php if (shop_input($_GET, 'validate') === 'yml'):
        $diagnostics = shop_export_diagnostics($base);
        ?>
        <dl class="shop-report-totals" id="shop-feed-check">
            <div><dt><?= $label('variants') ?></dt><dd><?= $diagnostics['total'] ?></dd></div>
            <?php foreach ($diagnostics['counts'] as $issue => $count): ?><div><dt><?= $label($issue) ?></dt><dd><?= $count ?></dd></div><?php endforeach; ?>
        </dl>
        <?php if ($diagnostics['sample']): ?>
            <div class="shop-report-table"><table><thead><tr><th scope="col"><?= $label('product_sku') ?></th>
                <th scope="col"><?= $label('product_name') ?></th><th scope="col"><?= $label('feed_issues') ?></th></tr></thead><tbody>
                <?php foreach ($diagnostics['sample'] as [$sku, $name, $issues, $product, $offer]): ?><tr>
                    <td><?= site_escape($sku) ?></td>
                    <td><a href="<?= site_escape($moduleAdminUrl . '&tab=catalog&sheet=products&product_action=edit&' . http_build_query([
                        'product'=>$product, 'feed_offer'=>$offer], '', '&', PHP_QUERY_RFC3986)) ?>" data-shop-feed-edit
                        title="<?= $label('product_edit') ?>"><?= site_escape($name) ?></a></td>
                    <td><?= site_escape(implode(', ', array_map('shop_label', $issues))) ?></td>
                </tr><?php endforeach; ?>
            </tbody></table></div>
        <?php endif; ?>
    <?php endif; ?>
</section>
<section class="panel form-stack">
    <div class="admin-heading"><h2><i class="fa-solid fa-link" aria-hidden="true"></i> <?= $label('export_links') ?></h2>
        <span class="shop-order-status"><?= $label($access ? 'integration_enabled' : 'integration_disabled') ?></span>
    </div>
    <p class="hint"><?= $label('export_link_help') ?></p>
    <?php if ($access): ?>
        <div class="shop-export-links">
            <?php foreach (SHOP_EXPORT_FORMATS as $format => [,,$icon]):
                if ($format === 'xlsx' && !class_exists('ZipArchive')) continue;
                $url = shop_export_url($root, $base, shop_endpoint($base, ['feed'=>$format,'key'=>shop_export_key($shopState, $format)]));
                ?>
                <div class="shop-export-link" data-copy-code>
                    <strong><i class="fa-solid fa-<?= $icon ?>" aria-hidden="true"></i> <?= strtoupper($format) ?></strong>
                    <code data-copy-code-text><?= site_escape($url) ?></code>
                    <button type="button" data-copy-code-button data-copy-confirmation="<?= $label('export_copied') ?>" data-copy-fallback="<?= $label('export_copy') ?>">
                        <i class="fa-regular fa-copy" aria-hidden="true"></i> <?= $label('export_copy') ?>
                    </button>
                    <small role="status" data-copy-code-status></small>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <form method="post" class="header-actions" action="<?= site_escape($tabUrl) ?>">
        <input type="hidden" name="action" value="shop-export-access">
        <input type="hidden" name="csrf" value="<?= site_escape($moduleToken) ?>">
        <button type="submit" name="mode" value="rotate" class="button-primary">
            <i class="fa-solid fa-<?= $access ? 'rotate' : 'key' ?>" aria-hidden="true"></i> <?= $label($access ? 'export_rotate' : 'export_enable') ?>
        </button>
        <?php if ($access): ?><button type="submit" name="mode" value="disable"><?= $label('export_disable') ?></button><?php endif; ?>
    </form>
</section>
