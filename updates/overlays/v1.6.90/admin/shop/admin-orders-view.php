<?php
if (!defined('SITE_ROOT')) { http_response_code(404); exit; }
$orders = shop_orders();
$states = shop_order_states();
$pageCount = max(1, (int) ceil(count($orders) / 20));
$pageNumber = min($pageCount, max(1, (int) shop_input($_GET, 'p')));
$ordersUrl = $tabUrl . '&p=' . $pageNumber;
$label = static function (string $key): string { return site_escape(shop_label($key)); };
?>
<div class="shop-orders">
    <div class="admin-heading">
        <h2><?= $label('orders') ?> <span class="count-badge"><?= count($orders) ?></span></h2>
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
    <?php if (!$orders): ?><p><?= $label('empty_orders') ?></p><?php endif; ?>
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
                    <div><dt><?= $label('recipient') ?></dt><dd><?= site_escape($head[4]) ?></dd></div>
                    <div><dt><?= $label('phone') ?></dt><dd><?= site_escape($head[6]) ?></dd></div>
                    <div><dt><?= $label('email') ?></dt><dd><?= site_escape($head[5]) ?></dd></div>
                    <div><dt><?= $label('delivery') ?></dt><dd><?= $label($head[8]) ?></dd></div>
                    <?php if ($head[8] === 'shipping'): ?><div><dt><?= $label('address') ?></dt><dd><?= site_escape($head[7]) ?></dd></div><?php endif; ?>
                </dl>
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
            <?php if ($pageNumber > 1): ?><a class="button" href="<?= site_escape($tabUrl . '&p=' . ($pageNumber - 1)) ?>"><?= $label('gallery_prev') ?></a><?php endif; ?>
            <span aria-current="page"><?= $pageNumber ?> / <?= $pageCount ?></span>
            <?php if ($pageNumber < $pageCount): ?><a class="button" href="<?= site_escape($tabUrl . '&p=' . ($pageNumber + 1)) ?>"><?= $label('gallery_next') ?></a><?php endif; ?>
        </nav>
    <?php endif; ?>
</div>
