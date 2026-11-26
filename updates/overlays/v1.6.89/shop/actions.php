<?php
if (!defined('SITE_ROOT')) { http_response_code(404); exit; }

function shop_return(string $base, string $target): string
{
    $prefix = site_url($base);
    if (strpos($target, $prefix) === 0 && strpos($target, '//') !== 0
        && preg_match('~\A/[^\\\\\x00-\x20]*\z~', $target)) return $target;
    return shop_route_url($base);
}

function shop_submit(string $base, array $input): string
{
    $token = shop_input($input, 'token');
    if ($token === '' || !hash_equals(shop_token(), $token)) throw shop_error('session');
    $index = shop_index();
    $settings = shop_settings();
    $action = shop_input($input, 'action');
    $cart = $_SESSION['shopCart'] ?? [];
    if (in_array($action, ['add', 'quantity'], true)) {
        $offer = shop_input($input, 'offer');
        if (!shop_id($offer)) throw shop_error('not_found');
        $quantity = $action === 'add' ? ($cart[$offer] ?? 0) + 1
            : (shop_input($input, 'remove') === '1' ? 0 : max(0, (int) shop_input($input, 'qty')));
        if ($quantity) {
            $cart[$offer] = $quantity;
            $quote = shop_cart_quote($index, $cart, site_read_record(SHOP_DIR . '/stock.php', SHOP_LIMIT));
            if (!$quote['valid'] || count($cart) > 80) throw shop_error('stock');
        } else unset($cart[$offer]);
        $_SESSION['shopCart'] = $cart;
        $_SESSION['shopNotice'] = shop_label('cart_updated');
        return shop_return($base, shop_input($input, 'return'));
    }
    if ($action !== 'order') throw shop_error('not_found');
    $nonce = shop_input($input, 'nonce');
    if ($nonce !== '' && hash_equals($_SESSION['shopLastOrder'] ?? '', $nonce)) return shop_endpoint($base, ['view'=>'checkout']);
    if ($nonce === '' || !hash_equals($_SESSION['shopCheckout'] ?? '', $nonce)) throw shop_error('session');
    $created = false;
    $lock = shop_lock();
    try {
        // Use the catalog generation and price in force after acquiring the same lock as imports.
        $index = shop_index(true);
        $settings = shop_settings();
        if (shop_order($nonce) === null) {
            $rules = site_spam_rules();
            $ip = site_normalize_ip($_SERVER['REMOTE_ADDR'] ?? '');
            if (site_spam_request_blocked($ip, $_SERVER['HTTP_USER_AGENT'] ?? '', $rules)) throw shop_error('contact_invalid');
            if (($_SESSION['shopOrderTime'] ?? 0) > time() - 8) throw shop_error('try_again');
            $user = shop_user();
            if (!$settings['guest'] && !$user) throw shop_error('login');
            $quote = shop_cart_quote($index, $cart, shop_committed());
            if (!$quote['valid']) throw shop_error('stock');
            if ((string) $quote['total'] !== shop_input($input, 'quoted')
                || (string) $settings['shipping'] !== shop_input($input, 'quoted_shipping')) throw shop_error('changed_price');
            $contact = [];
            foreach (['name', 'email', 'phone', 'address', 'method'] as $field) $contact[$field] = shop_input($input, $field);
            foreach ($contact as $value) {
                if (strlen($value) > 200 || preg_match('/[\x00-\x1f]/', $value) || preg_match('//u', $value) !== 1) throw shop_error('contact_invalid');
            }
            if ($contact['name'] === '' || !filter_var($contact['email'], FILTER_VALIDATE_EMAIL)
                || strlen(preg_replace('/\D/', '', $contact['phone'])) < 6 || !in_array($contact['method'], ['shipping','pickup'], true)
                || ($contact['method'] === 'shipping' && $contact['address'] === '')
                || site_spam_errors(['name'=>['value'=>$contact['name'],'type'=>'text'],
                    'address'=>['value'=>$contact['address'],'type'=>'text']], $rules['words'])) throw shop_error('contact_invalid');
            $shipping = $contact['method'] === 'shipping' ? $settings['shipping'] : 0;
            $rows = [['order', gmdate('c'), 'new', $user['id'] ?? '', $contact['name'], $contact['email'], $contact['phone'],
                $contact['address'], $contact['method'], $shipping, $quote['total'] + $shipping, $settings['currency']]];
            foreach ($quote['lines'] as [$product, $offer, $qty]) {
                $rows[] = ['item', $offer['id'], $offer['sku'], $product['name'], $offer['variant'], $offer['price_minor'], $qty];
            }
            foreach ($quote['units'] as $id => $qty) $rows[] = ['stock', $id, $qty];
            shop_write('order-' . $nonce, shop_csv($rows), 262144);
            $created = true;
        }
        $_SESSION['shopLastOrder'] = $nonce;
        $_SESSION['shopOrderTime'] = time();
        unset($_SESSION['shopCheckout'], $_SESSION['shopCart']);
    } finally {
        fclose($lock);
    }
    if ($created) shop_order_mail($nonce, 'order');
    return shop_endpoint($base, ['view'=>'checkout']);
}
