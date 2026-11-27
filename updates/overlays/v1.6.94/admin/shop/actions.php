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
    if ($action === 'payment' && function_exists('shop_start_payment')) {
        return shop_start_payment($base, shop_input($input, 'integration'), shop_input($input, 'order_id'));
    }
    $cart = $_SESSION['shopCart'] ?? [];
    if (in_array($action, ['add', 'quantity'], true)) {
        $offer = shop_input($input, 'offer');
        if (!shop_id($offer)) throw shop_error('not_found');
        $quantity = $action === 'add' ? ($cart[$offer] ?? 0) + 1
            : (shop_input($input, 'remove') === '1' ? 0 : max(0, (int) shop_input($input, 'qty')));
        if ($quantity) {
            $cart[$offer] = $quantity;
            $quote = shop_cart_quote($index, $cart, shop_cached_stock()['committed'], $settings);
            if (!$quote['valid'] || count($cart) > 80) throw shop_error('stock');
        } else unset($cart[$offer]);
        $_SESSION['shopCart'] = $cart;
        $_SESSION['shopNotice'] = shop_label('cart_updated');
        return shop_return($base, shop_input($input, 'return'));
    }
    $quick = $action === 'quick-order';
    if ($action !== 'order' && !$quick) throw shop_error('not_found');
    if ((int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > SITE_FORM_REQUEST_LIMIT) throw shop_error('contact_invalid');
    $nonce = shop_input($input, 'nonce');
    $offerId = $quick ? shop_input($input, 'offer') : '';
    if ($quick && !shop_id($offerId)) throw shop_error('not_found');
    $redirect = $quick ? shop_endpoint($base, ['view'=>'quick','offer'=>$offerId,'done'=>$nonce])
        : shop_endpoint($base, ['view'=>'checkout']);
    $last = $quick ? ($_SESSION['shopLastQuickOrder'][$offerId] ?? '') : ($_SESSION['shopLastOrder'] ?? '');
    $issued = $quick ? ($_SESSION['shopQuickCheckout'][$offerId] ?? '') : ($_SESSION['shopCheckout'] ?? '');
    if ($nonce !== '' && hash_equals($last, $nonce)) return $redirect;
    if ($nonce === '' || !hash_equals($issued, $nonce)) throw shop_error('session');
    if ($quick) $cart = [$offerId=>1];
    $created = false;
    $lock = shop_lock();
    try {
        // Use the catalog generation and price in force after acquiring the same lock as imports.
        $index = shop_index(true);
        $settings = shop_settings();
        if (shop_order($nonce) === null) {
            $rules = site_spam_rules();
            $ip = site_normalize_ip($_SERVER['REMOTE_ADDR'] ?? '');
            if (site_spam_request_blocked($ip, $_SERVER['HTTP_USER_AGENT'] ?? '', $rules) || ($input['website'] ?? '') !== '') throw shop_error('contact_invalid');
            if (($_SESSION['shopOrderTime'] ?? 0) > time() - 8) throw shop_error('try_again');
            $user = shop_user();
            if (!$settings['guest'] && !$user) throw shop_error('login');
            $quote = shop_cart_quote($index, $cart, shop_committed(), $settings, $quick);
            if (!$quote['valid']) throw shop_error('stock');
            $methods = $quick ? ['pickup'=>0] : shop_delivery_quote($settings, $quote['total']);
            $method = $quick ? 'pickup' : shop_input($input, 'method');
            if (!array_key_exists($method, $methods)) throw shop_error('delivery_unavailable');
            if ((string) $quote['total'] !== shop_input($input, 'quoted')
                || (string) ($methods['shipping'] ?? 0) !== shop_input($input, 'quoted_shipping')) throw shop_error('changed_price');
            if ($quick) {
                $fields = $input['fields'] ?? [];
                $phone = site_normalize_form_phone(is_array($fields) ? shop_input($fields, 'phone') : '', site_region_settings()['country']);
                if ($phone === '' || strpos($phone, '+89') === 0) throw shop_error('quick_phone_error');
                $contact = [
                    'name'=>['value'=>''], 'email'=>['value'=>''], '__address'=>['value'=>''], 'phone'=>['value'=>$phone],
                ];
            } else {
                $contact = shop_checkout_contact($settings, $input, $method === 'shipping', $rules);
            }
            $shipping = $methods[$method];
            $rows = [['order', gmdate('c'), $quick ? 'confirmed' : 'new', $user['id'] ?? '', $contact['name']['value'], $contact['email']['value'],
                $contact['phone']['value'], $contact['__address']['value'], $method, $shipping, $quote['total'] + $shipping, $settings['currency']]];
            foreach (array_diff_key($contact, array_flip(['name','email','phone','__address'])) as $field) {
                if ($field['value'] !== '') $rows[] = ['contact', $field['label'], $field['value']];
            }
            $pickupAddress = shop_delivery_rules($settings)['pickupAddress'];
            if ($method === 'pickup' && $pickupAddress !== '') $rows[] = ['pickup-address', $pickupAddress];
            if ($quick) $rows[] = ['pickup-hold', time() + 86400];
            foreach ($quote['lines'] as [$product, $offer, $qty]) {
                $rows[] = ['item', $offer['id'], $offer['sku'], $product['name'], $offer['variant'], $offer['price_minor'], $qty];
            }
            foreach ($quote['units'] as $id => $qty) $rows[] = ['stock', $id, $qty];
            shop_write('order-' . $nonce, shop_csv($rows), 262144);
            $created = true;
        }
        if ($quick) {
            shop_refresh_stock_cache();
            $_SESSION['shopLastQuickOrder'] = array_slice([$offerId=>$nonce] + ($_SESSION['shopLastQuickOrder'] ?? []), 0, 12, true);
            unset($_SESSION['shopQuickCheckout'][$offerId]);
        } else {
            $_SESSION['shopLastOrder'] = $nonce;
            unset($_SESSION['shopCheckout'], $_SESSION['shopCart'], $_SESSION['shopCheckoutDraft']);
        }
        $_SESSION['shopOrderTime'] = time();
    } finally {
        fclose($lock);
    }
    if ($created) shop_order_mail($nonce, 'order');
    return $redirect;
}
