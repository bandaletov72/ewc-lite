<?php
if (!defined('SITE_ROOT')) { http_response_code(404); exit; }

function shop_checkout_fields(array $settings, ?array $forms = null): array
{
    $core = [
        'name'=>['type'=>'text','limit'=>200],
        'email'=>['type'=>'email','limit'=>254],
        'phone'=>['type'=>'tel','limit'=>33],
    ];
    $id = $settings['checkoutForm'] ?? '';
    $fields = $id === '' ? [] : array_column(($forms ?? site_forms())[$id]['fields'] ?? [], null, 'name');
    foreach ($core as $name => $spec) {
        if (($fields[$name]['type'] ?? '') !== $spec['type']) {
            $fields[$name] = ['name'=>$name,'type'=>$spec['type'],'label'=>shop_label($name),'options'=>[],'maxLength'=>$spec['limit']];
        }
        $fields[$name]['required'] = true;
    }
    return array_values($fields);
}

function shop_delivery_rules(array $settings): array
{
    return ($settings['deliveryRules'] ?? []) + [
        'pickup'=>true, 'shipping'=>true, 'minimum'=>0, 'freeFrom'=>0, 'pickupAddress'=>'', 'notes'=>'',
    ];
}

function shop_delivery_quote(array $settings, int $subtotal): array
{
    $rules = shop_delivery_rules($settings);
    $methods = [];
    if ($rules['pickup']) $methods['pickup'] = 0;
    if ($rules['shipping'] && $subtotal >= $rules['minimum']) {
        $methods['shipping'] = $rules['freeFrom'] > 0 && $subtotal >= $rules['freeFrom'] ? 0 : (int) $settings['shipping'];
    }
    return $methods;
}

function shop_checkout_contact(array $settings, array $input, bool $delivery, array $spamRules): array
{
    $fields = shop_checkout_fields($settings);
    $submitted = $input['fields'] ?? $input;
    if (!is_array($submitted)) throw shop_error('contact_invalid');
    $fields[] = ['name'=>'__address','type'=>'text','label'=>shop_label('address'),'required'=>$delivery,'maxLength'=>200];
    $submitted['__address'] = $delivery ? ($input['address'] ?? '') : '';
    $result = site_validate_message(['fields'=>$fields], $submitted);
    $errors = $result['errors'] + site_spam_errors($result['fields'], $spamRules['words']);
    foreach ($result['fields'] as $name => $field) {
        if ($field['type'] === 'tel' && strpos($field['value'], '+89') === 0) {
            $errors[$name] = 'Укажите полный номер телефона, включая код страны.';
        }
    }
    if ($errors) {
        $values = [];
        foreach ($fields as $field) {
            $name = $field['name'];
            $value = $submitted[$name] ?? '';
            if (is_string($value) && strlen($value) <= site_form_field_length($field) * 3 && preg_match('//u', $value) === 1) {
                $values[$name] = $value;
            }
        }
        $_SESSION['shopCheckoutDraft'] = ['values'=>$values,'errors'=>$errors,'method'=>$delivery ? 'shipping' : 'pickup'];
        throw shop_error('contact_invalid');
    }
    $encoded = json_encode($result['fields'], JSON_UNESCAPED_UNICODE);
    if ($encoded === false || strlen($encoded) > 24000) throw shop_error('contact_invalid');
    return $result['fields'];
}

function shop_checkout_field(array $field, string $value, string $error): string
{
    $name = $field['name'];
    $type = $field['type'];
    $id = 'shop-checkout-' . $name;
    $attrs = ' name="fields[' . site_escape($name) . ']" aria-describedby="' . site_escape($id) . '-error"'
        . ($field['required'] ? ' required' : '') . ($error === '' ? '' : ' aria-invalid="true"');
    $label = site_escape($field['label']);
    $html = '<div class="field shop-checkout-field' . ($name === 'name' || in_array($type, ['textarea','radio'], true) ? ' shop-checkout-field-wide' : '')
        . '" data-message-field>';
    if ($type === 'radio') {
        $html .= '<fieldset><legend>' . $label . '</legend>';
        foreach ($field['options'] as $option) {
            $html .= '<label class="choice"><input type="radio"' . $attrs . ' value="' . site_escape($option) . '"'
                . ($value === $option ? ' checked' : '') . '> ' . site_escape($option) . '</label>';
        }
        $html .= '</fieldset>';
    } else {
        $html .= '<label for="' . site_escape($id) . '">' . $label . '</label>';
        $attrs .= ' id="' . site_escape($id) . '"';
        if ($type === 'textarea') {
            $html .= '<textarea' . $attrs . ' rows="3" maxlength="' . site_form_field_length($field) . '">'
                . "\n" . site_escape($value) . '</textarea>';
        } elseif ($type === 'select') {
            $html .= '<select' . $attrs . '><option value=""></option>';
            foreach ($field['options'] as $option) {
                $html .= '<option' . ($value === $option ? ' selected' : '') . '>' . site_escape($option) . '</option>';
            }
            $html .= '</select>';
        } else {
            $autocomplete = ['email'=>'email','tel'=>'tel'][$type] ?? ($name === 'name' ? 'name' : '');
            $html .= '<input' . $attrs . ' type="' . site_escape($type) . '" maxlength="' . site_form_field_length($field) . '"'
                . ($autocomplete === '' ? '' : ' autocomplete="' . $autocomplete . '"')
                . ($type === 'email' ? ' pattern="[^@\s]+@[^@\s]+\.[^@\s]+"' : '') . ' value="' . site_escape($value) . '">';
        }
    }
    return $html . '<small class="field-error" id="' . site_escape($id) . '-error" data-field-error'
        . ($error === '' ? ' hidden' : '') . '>' . site_escape(site_translation($error, shop_locale())) . '</small></div>';
}

function shop_quick_checkout_html(string $base, array $index, array $settings, array $state, array $offer, array $product, string $nonce): string
{
    $stock = shop_cached_stock();
    $quote = shop_cart_quote($index, [$offer['id']=>1], $stock['committed'], $settings, true);
    $url = shop_product_url($base, $index, $product);
    $return = shop_endpoint($base, ['view'=>'quick','offer'=>$offer['id']]);
    $name = $product['name'] . ($offer['variant'] === '' ? '' : ' · ' . $offer['variant']);
    $html = '<section class="shop-checkout-panel shop-quick-checkout"><a class="shop-quick-product" href="'
        . site_escape($url) . '">' . site_escape($name) . '</a>';
    if (!$quote['valid']) return $html . '<p>' . site_escape(shop_label(shop_quick_stock_label($index, $offer, $stock))) . '</p></section>';
    $address = shop_delivery_rules($settings)['pickupAddress'];
    $html .= '<div class="shop-quick-summary"><span><i class="fa-solid fa-location-dot" aria-hidden="true"></i> '
        . site_escape(shop_label('pickup')) . ($address === '' ? '' : ' · ' . site_escape($address))
        . '</span><strong>' . site_escape(shop_money($quote['total'], $settings)) . '</strong></div>';
    if (empty($settings['guest']) && empty($state['user'])) {
        return $html . '<a class="button button-primary" href="' . site_escape(site_url($base) . '?account=1') . '">'
            . site_escape(shop_label('login')) . '</a></section>';
    }
    $phone = ['name'=>'phone','type'=>'tel','label'=>shop_label('phone'),'required'=>true,'maxLength'=>33];
    return $html . shop_form_start($base, 'quick-order', $state, $return)
        . '<input type="hidden" name="offer" value="' . site_escape($offer['id']) . '">'
        . '<input type="hidden" name="nonce" value="' . site_escape($nonce) . '">'
        . '<input type="hidden" name="quoted" value="' . $quote['total'] . '"><input type="hidden" name="quoted_shipping" value="0">'
        . '<div hidden><input type="text" name="website" tabindex="-1" autocomplete="off" aria-label="Website"></div>'
        . shop_checkout_field($phone, $state['user']['phone'] ?? '', '')
        . '<button class="button-primary" type="submit">' . site_escape(shop_label('checkout')) . '</button></form></section>';
}

function shop_order_contact_details(array $order): array
{
    $details = [];
    foreach ($order['lines'] as $line) {
        if ($line[0] === 'contact' && isset($line[1], $line[2])) $details[] = [$line[1], $line[2]];
    }
    return $details;
}

function shop_order_pickup_address(array $order): string
{
    foreach ($order['lines'] as $line) if ($line[0] === 'pickup-address') return $line[1] ?? '';
    return '';
}
