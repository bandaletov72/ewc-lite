<?php
if (!defined('SITE_ROOT')) { http_response_code(404); exit; }
header('Cache-Control: private, no-store');
$head = $order['head'];
$currency = ['currency'=>$head[11]];
$label = static function (string $key): string { return site_escape(shop_label($key)); };
$title = $label('packing_slip') . ' #' . site_escape(substr($order['id'], 0, 12));
$items = array_filter($order['lines'], static function (array $line): bool { return $line[0] === 'item'; });
$money = static function (int $value) use ($currency): string { return site_escape(shop_money($value, $currency)); };
?>
<!doctype html>
<html lang="<?= site_escape(shop_locale()) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title ?></title>
    <link rel="stylesheet" href="<?= site_escape(site_url($base, 'templates/css/site.css') . '?v=' . filemtime(SITE_ROOT . '/templates/css/site.css')) ?>">
    <script src="<?= site_escape(site_url($base, 'templates/js/admin.js') . '?v=' . filemtime(SITE_ROOT . '/templates/js/admin.js')) ?>" defer></script>
</head>
<body class="shop-order-print">
<main>
    <header class="shop-slip-header">
        <div><p><?= site_escape(site_settings()['name'] ?? '') ?></p><h1><?= $title ?></h1>
            <p><time datetime="<?= site_escape($head[1]) ?>"><?= site_escape(shop_order_date($head[1])) ?></time>
                · <?= site_escape(shop_order_status_label($head[2], shop_order_states())) ?></p></div>
        <button type="button" data-shop-print><?= $label('print_pdf') ?></button>
    </header>
    <dl class="shop-order-contact">
        <div><dt><?= $label('recipient') ?></dt><dd><?= site_escape($head[4]) ?></dd></div>
        <div><dt><?= $label('phone') ?></dt><dd><?= site_escape($head[6]) ?></dd></div>
        <div><dt><?= $label('delivery') ?></dt><dd><?= $label($head[8]) ?></dd></div>
        <?php if ($head[8] === 'shipping'): ?>
            <div class="shop-slip-address"><dt><?= $label('address') ?></dt><dd><?= site_escape($head[7]) ?></dd></div>
        <?php endif; ?>
    </dl>
    <table>
        <thead><tr><th scope="col"><?= $label('products') ?></th><th scope="col"><?= $label('sku') ?></th>
            <th scope="col"><?= $label('quantity') ?></th><th scope="col"><?= $label('price') ?></th><th scope="col"><?= $label('total') ?></th></tr></thead>
        <tbody><?php foreach ($items as $line): ?>
            <tr><td><strong><?= site_escape($line[3]) ?></strong><?php if ($line[4] !== ''): ?><br><?= site_escape($line[4]) ?><?php endif; ?></td>
                <td><?= site_escape($line[2]) ?></td><td><?= (int) $line[6] ?></td>
                <td><?= $money((int) $line[5]) ?></td><td><?= $money((int) $line[5] * (int) $line[6]) ?></td></tr>
        <?php endforeach; ?></tbody>
    </table>
    <dl class="shop-slip-totals">
        <div><dt><?= $label('checkout_subtotal') ?></dt><dd><?= $money((int) $head[10] - (int) $head[9]) ?></dd></div>
        <div><dt><?= $label('shipping') ?></dt><dd><?= $money((int) $head[9]) ?></dd></div>
        <div><dt><?= $label('total') ?></dt><dd><?= $money((int) $head[10]) ?></dd></div>
    </dl>
</main>
</body>
</html>
