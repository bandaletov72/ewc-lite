<?php
if (!defined('SITE_ROOT')) { http_response_code(404); exit; }
$label = static function (string $key): string { return site_escape(shop_label($key)); };
$days = (int) shop_input($_GET, 'days');
if (!in_array($days, [7, 30, 90], true)) $days = 30;
$today = new DateTimeImmutable('today');
$dates = [];
for ($day = $days - 1; $day >= 0; $day--) $dates[$today->modify('-' . $day . ' days')->format('Y-m-d')] = ['orders'=>0, 'sales'=>0, 'amount'=>0];
$reports = [$shopState['currency']=>$dates];
$states = shop_order_states();
foreach (shop_orders() as $order) {
    $head = $order['head'];
    $created = date('Y-m-d', strtotime($head[1]));
    $completed = '';
    if (($states[$head[2]]['phase'] ?? '') === 'complete') {
        foreach ($order['lines'] as $line) {
            if ($line[0] === 'event' && ($states[$line[2]]['phase'] ?? '') === 'complete') $completed = date('Y-m-d', strtotime($line[1]));
        }
        if ($completed === '') $completed = $created;
    }
    if (!isset($dates[$created]) && !isset($dates[$completed])) continue;
    $currency = $head[11];
    if (!isset($reports[$currency])) $reports[$currency] = $dates;
    if (isset($dates[$created])) $reports[$currency][$created]['orders']++;
    if (isset($dates[$completed])) {
        $reports[$currency][$completed]['sales']++;
        $reports[$currency][$completed]['amount'] += (int) $head[10];
    }
}
?>
<div class="shop-orders">
    <div class="admin-heading"><h2><?= $label('sales_report') ?></h2>
        <nav class="site-menu" aria-label="<?= $label('report_period') ?>">
            <?php foreach ([7,30,90] as $option): ?><a href="<?= site_escape($tabUrl . '&days=' . $option) ?>"
                <?= $days === $option ? ' aria-current="page"' : '' ?>><?= $option ?> <?= $label('report_days') ?></a><?php endforeach; ?>
        </nav>
    </div>
    <?php foreach ($reports as $currency => $daily):
        $orders = array_column($daily, 'orders');
        $sales = array_column($daily, 'sales');
        $amounts = array_column($daily, 'amount');
        $number = static function (int $value) use ($currency): string { return site_escape(shop_money($value, ['currency'=>$currency])); };
        $chart = ['dates'=>array_keys($daily), 'orders'=>$orders, 'sales'=>$sales, 'amounts'=>array_map(static function (int $value): float {
            return $value / 100;
        }, $amounts), 'currency'=>$currency, 'labels'=>[shop_label('report_orders'),shop_label('report_sales'),shop_label('report_amount')]];
        ?>
        <section class="panel shop-sales-report">
            <h3><?= site_escape($currency) ?></h3>
            <dl class="shop-report-totals">
                <div><dt><?= $label('report_orders') ?></dt><dd><?= array_sum($orders) ?></dd></div>
                <div><dt><?= $label('report_sales') ?></dt><dd><?= array_sum($sales) ?></dd></div>
                <div><dt><?= $label('report_amount') ?></dt><dd><?= $number(array_sum($amounts)) ?></dd></div>
            </dl>
            <div class="shop-report-chart" hidden><canvas role="img" aria-label="<?= $label('sales_report') ?>"
                data-shop-sales-chart="<?= site_escape(json_encode($chart, JSON_UNESCAPED_UNICODE)) ?>"></canvas></div>
            <details<?= !array_sum($orders) && !array_sum($sales) ? '' : ' open' ?>>
                <summary><?= $label('report_by_day') ?></summary>
                <div class="shop-report-table"><table><thead><tr><th scope="col"><?= $label('report_date') ?></th><th scope="col"><?= $label('report_orders') ?></th>
                    <th scope="col"><?= $label('report_sales') ?></th><th scope="col"><?= $label('report_amount') ?></th></tr></thead><tbody>
                    <?php foreach (array_reverse($daily, true) as $date => $row): if (!$row['orders'] && !$row['sales']) continue; ?>
                        <tr><th scope="row"><?= site_escape($date) ?></th><td><?= $row['orders'] ?></td><td><?= $row['sales'] ?></td><td><?= $number($row['amount']) ?></td></tr>
                    <?php endforeach; ?>
                    <?php if (!array_sum($orders) && !array_sum($sales)): ?><tr><td colspan="4"><?= $label('empty_orders') ?></td></tr><?php endif; ?>
                </tbody></table></div>
            </details>
        </section>
    <?php endforeach; ?>
</div>
