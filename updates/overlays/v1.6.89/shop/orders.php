<?php
if (!defined('SITE_ROOT')) { http_response_code(404); exit; }

const SHOP_ORDER_STATES = ['new', 'confirmed', 'packed', 'issued', 'cancelled'];

function shop_user(): ?array
{
    return site_account_current();
}

function shop_order(string $id): ?array
{
    if (!preg_match('/\A[a-f0-9]{24}\z/', $id)) return null;
    $content = site_read_payload(SHOP_DIR . '/order-' . $id . '.php', 262144);
    if ($content === null) return null;
    $rows = shop_csv_read($content);
    $head = array_shift($rows);
    if (count($head) !== 12 || $head[0] !== 'order') throw shop_error('storage');
    return ['id'=>$id, 'head'=>$head, 'lines'=>$rows];
}

function shop_orders(): array
{
    $orders = [];
    foreach (glob(SHOP_DIR . '/order-*.php') ?: [] as $file) {
        $order = shop_order(substr(basename($file), 6, -4));
        if ($order) $orders[] = $order;
    }
    usort($orders, static function (array $a, array $b): int { return strcmp($b['head'][1], $a['head'][1]); });
    return $orders;
}

function shop_committed(): array
{
    $units = [];
    foreach (shop_orders() as $order) {
        if (!in_array($order['head'][2], ['confirmed','packed','issued'], true)) continue;
        foreach ($order['lines'] as $line) {
            if ($line[0] === 'stock') $units[$line[1]] = ($units[$line[1]] ?? 0) + (int) $line[2];
        }
    }
    return $units;
}

function shop_stock_units(array $index, string $offer): array
{
    return $index['kits'][$offer] ?? [$offer => 1];
}

function shop_order_status(string $id, string $status): void
{
    $order = shop_order($id);
    $transitions = ['new'=>['confirmed','cancelled'], 'confirmed'=>['packed','cancelled'], 'packed'=>['issued','cancelled']];
    if (!$order || !in_array($status, $transitions[$order['head'][2]] ?? [], true)) throw shop_error('status_invalid');
    if ($status === 'confirmed') {
        $index = shop_index(true);
        $committed = shop_committed();
        foreach ($order['lines'] as $line) {
            if ($line[0] === 'stock' && (int) ($index['offers'][$line[1]]['stock'] ?? 0) - ($committed[$line[1]] ?? 0) < (int) $line[2]) {
                throw shop_error('stock');
            }
        }
    }
    $order['head'][2] = $status;
    $order['lines'][] = ['event', gmdate('c'), $status];
    shop_write('order-' . $id, shop_csv(array_merge([$order['head']], $order['lines'])), 262144);
    shop_refresh_stock_cache();
    shop_order_mail($id, 'status');
}

function shop_refresh_stock_cache(): void
{
    shop_write('stock', json_encode(shop_committed(), JSON_FORCE_OBJECT));
}

function shop_stock(array $index, array $offer, array $committed): int
{
    $counts = [];
    foreach (shop_stock_units($index, $offer['id']) as $id => $units) {
        $counts[] = intdiv(max(0, (int) ($index['offers'][$id]['stock'] ?? 0) - ($committed[$id] ?? 0)), $units);
    }
    return min($counts);
}

function shop_cart_quote(array $index, array $cart, array $committed): array
{
    $total = 0;
    $lines = $units = [];
    foreach ($cart as $offerId => $quantity) {
        $offer = $index['offers'][$offerId] ?? null;
        $product = $index['products'][$offer['product_id'] ?? ''] ?? null;
        if (!$offer || !$product || $offer['active'] !== '1' || $product['active'] !== '1' || $quantity < 1 || $quantity > 999) {
            return ['lines'=>$lines, 'total'=>$total, 'units'=>$units, 'valid'=>false];
        }
        $total += (int) $offer['price_minor'] * $quantity;
        $lines[$offerId] = [$product, $offer, $quantity];
        foreach (shop_stock_units($index, $offer['id']) as $id => $count) $units[$id] = ($units[$id] ?? 0) + $count * $quantity;
    }
    foreach ($units as $id => $count) {
        if ((int) ($index['offers'][$id]['stock'] ?? 0) - ($committed[$id] ?? 0) < $count) return compact('lines','total','units') + ['valid'=>false];
    }
    return compact('lines','total','units') + ['valid'=>(bool) $lines];
}

function shop_mail_defaults(): array
{
    return site_region_settings()['public'] === 'ru' ? [
        'order'=>['Заказ №{{order}} принят', "Здравствуйте, {{name}}!\nЗаказ №{{order}} на сумму {{total}} принят."],
        'status'=>['Статус заказа №{{order}} изменён', "Здравствуйте, {{name}}!\nНовый статус заказа №{{order}}: {{status}}."],
    ] : [
        'order'=>['Order #{{order}} received', "Hi {{name}},\nYour order #{{order}} for {{total}} has been received."],
        'status'=>['Status of order #{{order}} changed', "Hi {{name}},\nThe new status of order #{{order}} is {{status}}."],
    ];
}

function shop_order_mail(string $id, string $event): void
{
    $order = shop_order($id);
    if (!$order) return;
    $head = $order['head'];
    $template = shop_settings()['mail'][$event] ?? shop_mail_defaults()[$event];
    site_send_template($head[5], $template, ['order'=>substr($id,0,12), 'name'=>$head[4], 'status'=>shop_label($head[2]),
        'total'=>shop_money((int) $head[10], ['currency'=>$head[11]])]);
}
