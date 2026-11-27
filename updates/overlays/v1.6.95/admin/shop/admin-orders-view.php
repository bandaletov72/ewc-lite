<?php
if (!defined('SITE_ROOT')) { http_response_code(404); exit; }
$orders = shop_orders();
$states = shop_order_states();
$orderTotal = count($orders);
$orderQuery = ltrim(site_search_query($_GET['q'] ?? ''), '#');
$orderStatus = shop_input($_GET, 'order_status');
if (!isset($states[$orderStatus])) $orderStatus = '';
if ($orderQuery !== '' || $orderStatus !== '') {
    $orders = array_values(array_filter($orders, static function (array $order) use ($orderQuery, $orderStatus): bool {
        return ($orderStatus === '' || $order['head'][2] === $orderStatus)
            && ($orderQuery === '' || stripos($order['id'], $orderQuery) !== false);
    }));
}
$filterUrl = $tabUrl . ($orderStatus === '' ? '' : '&order_status=' . rawurlencode($orderStatus))
    . ($orderQuery === '' ? '' : '&q=' . rawurlencode($orderQuery));
$pageCount = max(1, (int) ceil(count($orders) / 20));
$pageNumber = min($pageCount, max(1, (int) shop_input($_GET, 'p')));
$ordersUrl = $filterUrl . '&p=' . $pageNumber;
$label = static function (string $key): string { return site_escape(shop_label($key)); };
?>
<div class="shop-orders">
    <div class="admin-heading">
        <h2><?= $label('orders') ?> <span class="count-badge"><?= count($orders) ?><?= count($orders) === $orderTotal ? '' : ' / ' . $orderTotal ?></span></h2>
    </div>
    <?php if ($moduleOwner): ?>
        <details class="panel shop-status-settings"<?= shop_input($_POST, 'action') === 'shop-status-add' ? ' open' : '' ?>>
            <summary><?= $label('add_order_status') ?></summary>
            <form method="post" action="<?= site_escape($ordersUrl) ?>" class="shop-order-controls">
                <input type="hidden" name="csrf" value="<?= site_escape($moduleToken) ?>">
                <input type="hidden" name="action" value="shop-status-add">
                <label><?= $label('add_order_status') ?><input name="name" maxlength="80" required></label>
                <label><?= $label('status_phase') ?><select name="phase">
                    <?php foreach (['pending','reserved','complete','cancelled'] as $phase): ?>
                        <option value="<?= $phase ?>"><?= $label('phase_' . $phase) ?></option>
                    <?php endforeach; ?>
                </select></label>
                <button type="submit" class="button-primary"><?= $label('save') ?></button>
            </form>
        </details>
    <?php endif; ?>
    <form method="get" action="<?= site_escape(strtok($moduleAdminUrl, '?')) ?>" class="shop-compact-search panel">
        <input type="hidden" name="<?= $moduleOwner ? 'shop' : 'staff' ?>" value="1"><input type="hidden" name="tab" value="orders">
        <label class="field"><?= $label('record_order_id') ?><input type="search" name="q" value="<?= site_escape($orderQuery) ?>"></label>
        <label class="field"><?= $label('status') ?><select name="order_status">
            <option value=""><?= $label('record_all_statuses') ?></option>
            <?php foreach ($states as $id => $state): ?><option value="<?= site_escape($id) ?>"<?= $id === $orderStatus ? ' selected' : '' ?>><?= site_escape(shop_order_status_label($id, $states)) ?></option><?php endforeach; ?>
        </select></label>
        <button type="submit"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i> <?= $label('record_find') ?></button>
        <?php if ($orderQuery !== '' || $orderStatus !== ''): ?><a href="<?= site_escape($tabUrl) ?>"><?= $label('filter_reset') ?></a><?php endif; ?>
    </form>
    <?php if (!$orders): ?><p><?= $label($orderTotal ? 'record_empty' : 'empty_orders') ?></p><?php endif; ?>
    <?php foreach (array_slice($orders, ($pageNumber - 1) * 20, 20) as $order):
        $head = $order['head'];
        $currency = ['currency'=>$head[11]];
        $choices = shop_order_choices($head[2], $states);
        $attempt = site_read_record(SHOP_DIR . '/payment-' . $order['id'] . '.php', 16384);
        ?>
        <details class="panel shop-order-card" id="order-<?= site_escape($order['id']) ?>"
            <?= shop_input($_GET, 'open_order') === $order['id'] || shop_input($_POST, 'order') === $order['id'] ? ' open' : '' ?>>
            <summary><strong>#<?= site_escape(substr($order['id'], 0, 12)) ?></strong>
                <time datetime="<?= site_escape($head[1]) ?>"><?= site_escape(shop_order_date($head[1])) ?></time>
                <span class="shop-order-status"><?= site_escape(shop_order_status_label($head[2], $states)) ?></span>
                <strong><?= site_escape(shop_money((int) $head[10], $currency)) ?></strong>
            </summary>
            <div class="shop-order-detail">
                <dl class="shop-order-contact">
                    <?php if ($head[4] !== ''): ?><div><dt><?= $label('recipient') ?></dt><dd><?= site_escape($head[4]) ?></dd></div><?php endif; ?>
                    <div><dt><?= $label('phone') ?></dt><dd><?= site_escape($head[6]) ?></dd></div>
                    <?php if ($head[5] !== ''): ?><div><dt><?= $label('email') ?></dt><dd><?= site_escape($head[5]) ?></dd></div><?php endif; ?>
                    <?php foreach (shop_order_contact_details($order) as [$field, $value]): ?>
                        <div><dt><?= site_escape($field) ?></dt><dd><?= nl2br(site_escape($value), false) ?></dd></div>
                    <?php endforeach; ?>
                </dl>
                <div class="shop-fulfillment">
                    <i class="fa-solid fa-<?= $head[8] === 'shipping' ? 'truck' : 'store' ?>" aria-hidden="true"></i>
                    <div><strong><?= $label($head[8] === 'shipping' ? 'record_delivery_address' : 'pickup') ?></strong>
                        <div><?= $head[8] === 'shipping' ? ($head[7] === '' ? $label('record_address_missing') : site_escape($head[7])) : $label('record_pickup_info') ?></div>
                        <?php if ($head[8] !== 'shipping' && $head[7] !== ''): ?><small><?= $label('record_customer_address') ?> · <?= site_escape($head[7]) ?></small><?php endif; ?>
                        <?php if ($head[8] === 'pickup' && ($pickup = shop_order_pickup_address($order)) !== ''): ?>
                            <div><?= $label('delivery_pickup_address') ?> · <?= site_escape($pickup) ?></div>
                        <?php endif; ?>
                        <?php if (($states[$head[2]]['phase'] ?? '') === 'reserved' && ($expiry = shop_order_hold_expires($order)) > 0): ?>
                            <div><?= $label('reserve_until') ?> <time datetime="<?= gmdate('c', $expiry) ?>"><?= site_escape(shop_order_date(gmdate('c', $expiry))) ?></time></div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if ($attempt && function_exists('shop_integration_catalog')):
                    $methodName = shop_integration_catalog()[$attempt['integration']]['name'] ?? $attempt['integration']; ?>
                    <p><?= site_escape($methodName . ' · ' . shop_label('payment_check_dashboard')) ?></p>
                <?php endif; ?>
                <ul><?php foreach ($order['lines'] as $line): if ($line[0] === 'item'): ?>
                    <li><?= site_escape(trim($line[3] . ' ' . $line[4]) . ' · ' . $line[6] . ' × ' . shop_money((int) $line[5], $currency)) ?></li>
                <?php endif; endforeach; ?></ul>
                <div class="shop-order-controls">
                    <?php if ($choices): ?>
                        <form method="post" action="<?= site_escape($ordersUrl) ?>" class="shop-order-controls">
                            <input type="hidden" name="csrf" value="<?= site_escape($moduleToken) ?>">
                            <input type="hidden" name="action" value="shop-status">
                            <input type="hidden" name="order" value="<?= site_escape($order['id']) ?>">
                            <input type="hidden" name="current_status" value="<?= site_escape($head[2]) ?>">
                            <label><?= $label('status') ?><select name="status" required>
                                <option value=""><?= site_escape(shop_order_status_label($head[2], $states)) ?></option>
                                <?php foreach ($choices as $next => $choice): ?>
                                    <option value="<?= site_escape($next) ?>"><?= site_escape(shop_order_status_label($next, $states)) ?></option>
                                <?php endforeach; ?>
                            </select></label>
                            <button type="submit" class="button-primary"><?= $label('save') ?></button>
                        </form>
                    <?php endif; ?>
                    <a class="button" target="_blank" rel="noopener" href="<?= site_escape($ordersUrl . '&print_order=' . $order['id']) ?>">
                        <i class="fa-solid fa-print" aria-hidden="true"></i> <?= $label('print_pdf') ?>
                    </a>
                </div>
                <?php $events = array_filter($order['lines'], static function (array $line): bool { return $line[0] === 'event'; }); ?>
                <?php if ($events): ?><details><summary><?= $label('order_history') ?></summary><ol>
                    <?php foreach ($events as $line): ?><li><?= site_escape(shop_order_date($line[1]) . ' · ' . shop_order_status_label($line[2], $states)) ?></li><?php endforeach; ?>
                </ol></details><?php endif; ?>
            </div>
        </details>
    <?php endforeach; ?>
    <?php if ($pageCount > 1): ?>
        <nav class="shop-order-pages" aria-label="<?= $label('orders') ?>">
            <?php if ($pageNumber > 1): ?><a class="button" href="<?= site_escape($filterUrl . '&p=' . ($pageNumber - 1)) ?>"><?= $label('gallery_prev') ?></a><?php endif; ?>
            <span aria-current="page"><?= $pageNumber ?> / <?= $pageCount ?></span>
            <?php if ($pageNumber < $pageCount): ?><a class="button" href="<?= site_escape($filterUrl . '&p=' . ($pageNumber + 1)) ?>"><?= $label('gallery_next') ?></a><?php endif; ?>
        </nav>
    <?php endif; ?>
</div>
