<?php
if (!defined('SITE_ROOT')) { http_response_code(404); exit; }

function shop_route_url(string $base, string $path = '', array $query = []): string
{
    return site_url($base, 'shop/' . ($path === '' ? '' : trim($path, '/') . '/'))
        . ($query ? '?' . http_build_query($query, '', '&', PHP_QUERY_RFC3986) : '');
}

function shop_category_url(string $base, array $index, string $id): string
{
    $slugs = [];
    foreach ($index['tree'][$id] ?? [] as $category) $slugs[] = $index['categories'][$category]['slug'];
    return shop_route_url($base, implode('/', $slugs));
}

function shop_current_route(array $index, string $path): ?array
{
    if ($path === '') return ['category'=>'', 'brand'=>'', 'product'=>''];
    $slugs = explode('/', $path);
    if ($slugs[0] === 'product') {
        $id = count($slugs) === 2 ? ($index['slugs'][$slugs[1]] ?? '') : '';
        return $id !== '' && $index['products'][$id]['active'] === '1'
            ? ['category'=>$index['products'][$id]['category_id'], 'brand'=>'', 'product'=>$id] : null;
    }
    $category = '';
    foreach ($slugs as $position => $slug) {
        $next = '';
        foreach ($index['children'][$category] ?? [] as $id) {
            if ($index['categories'][$id]['slug'] === $slug) { $next = (string) $id; break; }
        }
        if ($next === '') {
            return $category !== '' && $position === count($slugs) - 1 && isset($index['brands'][$slug])
                ? ['category'=>$category, 'brand'=>$slug, 'product'=>''] : null;
        }
        $category = $next;
    }
    return ['category'=>$category, 'brand'=>'', 'product'=>''];
}

function shop_token(): string
{
    if (session_status() !== PHP_SESSION_ACTIVE) return '';
    if (!isset($_SESSION['shopCsrf'])) $_SESSION['shopCsrf'] = bin2hex(random_bytes(32));
    return $_SESSION['shopCsrf'];
}

function shop_session(string $base): array
{
    static $snapshot;
    if ($snapshot !== null) return $snapshot;
    $active = session_status() === PHP_SESSION_ACTIVE;
    if (!$active && !site_public_session($base)) return [];
    $snapshot = ['token'=>shop_token(), 'cart'=>$_SESSION['shopCart'] ?? [],
        'user'=>shop_user(), 'order'=>$_SESSION['shopCheckout'] ?? ($_SESSION['shopCheckout'] = bin2hex(random_bytes(12))),
        'last'=>$_SESSION['shopLastOrder'] ?? ''];
    if (!$active) session_write_close();
    return $snapshot;
}

function shop_defaults(): array
{
    return [
        'category' => '<li><a href="{{url}}">{{name}}</a>{{children}}</li>',
        'item' => '<article class="shop-item">{{image}}<h3><a href="{{url}}">{{name}}</a></h3><p>{{summary}}</p>{{offers}}</article>',
        'product' => '<article class="shop-product"><h2>{{name}}</h2><p>{{summary}}</p>{{images}}<div>{{description}}</div>{{attributes}}{{offers}}</article>',
        'basket' => '<a class="button shop-basket" href="{{url}}">{{label}} <strong>{{count}}</strong></a>',
        'checkout' => '<section class="shop-checkout"><h2>{{title}}</h2>{{body}}</section>',
        'account' => '<section class="shop-account"><h2>{{title}}</h2>{{body}}</section>',
        'page' => '<main class="shop-container"><nav>{{home}} {{basket}} {{account}}</nav><h1>{{title}}</h1>{{notice}}{{content}}</main>',
    ];
}

function shop_template(string $kind, array $values, array $settings): string
{
    $template = $settings['templates'][$kind] ?? shop_defaults()[$kind];
    $tokens = [];
    foreach ($values as $key => $value) $tokens['{{' . $key . '}}'] = $value;
    return strtr($template, $tokens);
}

function shop_image(string $base, string $path, string $title): string
{
    if ($path === '') return '';
    $url = shop_image_name($path) ? shop_endpoint($base, ['image'=>$path]) : $path;
    return '<img src="' . site_escape($url) . '" alt="' . site_escape($title) . '" loading="lazy" decoding="async">';
}

function shop_tree_html(string $base, array $index, array $settings, string $parent): string
{
    if (empty($index['children'][$parent])) return '';
    $html = '<ul class="shop-categories">';
    foreach ($index['children'][$parent] as $id) {
        $category = $index['categories'][$id];
        $html .= shop_template('category', [
            'url'=>site_escape(shop_category_url($base, $index, (string) $id)),
            'name'=>site_escape($category['name']),
            'description'=>site_escape($category['description']),
            'children'=>shop_tree_html($base, $index, $settings, (string) $id),
        ], $settings);
    }
    return $html . '</ul>';
}

function shop_list_options(array $index, string $category, array $query): array
{
    $fields = $category !== '' ? ($index['categoriesFields'][$category] ?? []) : array_keys(array_filter($index['fields'],
        static function (array $field): bool { return $field['category_id'] === ''; }));
    $fields = array_intersect_key($index['fields'], array_fill_keys($fields, true));
    $sorts = ['new-desc'=>shop_label('new_desc'), 'new-asc'=>shop_label('new_asc'),
        'price-asc'=>shop_label('price_asc'), 'price-desc'=>shop_label('price_desc')];
    foreach ($fields as $id => $field) {
        if ($field['sort'] !== '1') continue;
        $sorts[$id . '-desc'] = $field['name'] . ' ↓';
        $sorts[$id . '-asc'] = $field['name'] . ' ↑';
    }
    $sort = shop_input($query, 'sort');
    $search = substr(shop_input($query, 'q'), 0, 200);
    return ['fields'=>$fields, 'sorts'=>$sorts, 'sort'=>isset($sorts[$sort]) ? $sort : 'new-desc', 'q'=>$search,
        'p'=>min(100000, max(1, (int) shop_input($query, 'p')))];
}

function shop_listing(array $index, string $category, string $brand, array $query, int $size): array
{
    $options = shop_list_options($index, $category, $query);
    $includeChildren = $category === '' || ($index['categories'][$category]['descendants'] ?? '') === '1';
    $ids = array_fill_keys(($includeChildren ? $index['lists'] : $index['local'])[$category] ?? [], true);
    if ($brand !== '') {
        $brands = [];
        foreach (array_keys($index['brands'][$brand] ?? []) as $value) {
            foreach ($index['lookup']['brand'][$value] ?? [] as $id) $brands[$id] = true;
        }
        $ids = array_intersect_key($ids, $brands);
    }
    $filters = is_array($query['f'] ?? null) ? $query['f'] : [];
    $minimum = is_array($query['min'] ?? null) ? $query['min'] : [];
    $maximum = is_array($query['max'] ?? null) ? $query['max'] : [];
    foreach ($options['fields'] as $id => $field) {
        if ($field['filter'] !== '1') continue;
        if (isset($index['lookup'][$id])) {
            $selected = array_slice((array) ($filters[$id] ?? []), 0, 40);
            if (!$selected) continue;
            $matches = [];
            foreach ($selected as $value) {
                if (!is_string($value)) continue;
                foreach ($index['lookup'][$id][$value] ?? [] as $offerId) $matches[$offerId] = true;
            }
            $ids = array_intersect_key($ids, $matches);
        } elseif ($field['type'] === 'number') {
            $min = shop_input($minimum, (string) $id);
            $max = shop_input($maximum, (string) $id);
            if (!is_numeric($min) && !is_numeric($max)) continue;
            foreach (array_keys($ids) as $offerId) {
                $target = $field['target'] === 'offer' ? $offerId : $index['offers'][$offerId]['product_id'];
                $value = $index['values'][$id][$target][0] ?? null;
                if ($value === null || (is_numeric($min) && $value < $min) || (is_numeric($max) && $value > $max)) unset($ids[$offerId]);
            }
        }
    }
    $needle = shop_lower($options['q']);
    $words = array_filter(explode(' ', $needle), 'strlen');
    $prices = [shop_input($query,'price_min'), shop_input($query,'price_max')];
    $rank = [];
    $fieldId = substr($options['sort'], 0, strrpos($options['sort'], '-'));
    $productGroups = [];
    foreach (array_keys($ids) as $id) {
        $offer = $index['offers'][$id];
        $productId = $offer['product_id'];
        $price = (int) $offer['price_minor'];
        if ((is_numeric($prices[0]) && $price < 100 * (float) $prices[0])
            || (is_numeric($prices[1]) && $price > 100 * (float) $prices[1])) continue;
        foreach ($words as $word) if (strpos($index['search'][$id], $word) === false) continue 2;
        $field = $index['fields'][$fieldId] ?? [];
        if ($fieldId === 'price') $value = $price;
        elseif ($fieldId === 'new') $value = $index['products'][$productId]['created'];
        else $value = $index['values'][$fieldId][($field['target'] ?? '') === 'offer' ? $id : $productId][0] ?? null;
        $rank[$id] = [$needle !== '' && shop_lower($offer['sku']) === $needle ? 1 : 0, $value];
        $productGroups[$productId][] = $id;
    }
    $desc = substr($options['sort'], -5) === '-desc';
    $compare = static function ($a, $b) use ($rank, $desc): int {
        [$exactA, $valueA] = $rank[$a];
        [$exactB, $valueB] = $rank[$b];
        $missing = ($valueA === null ? 1 : 0) <=> ($valueB === null ? 1 : 0);
        return ($exactB <=> $exactA) ?: $missing ?: (($valueA <=> $valueB) * ($desc ? -1 : 1)) ?: strcmp((string) $a, (string) $b);
    };
    foreach ($productGroups as &$group) usort($group, $compare);
    unset($group);
    uasort($productGroups, static function (array $a, array $b) use ($compare): int { return $compare($a[0], $b[0]); });
    $pages = max(1, (int) ceil(count($productGroups) / $size));
    $page = min($options['p'], $pages);
    return $options + ['products'=>array_slice($productGroups, ($page - 1) * $size, $size, true),
        'pages'=>$pages, 'page'=>$page, 'count'=>count($productGroups)];
}

function shop_form_start(string $base, string $action, array $state, string $return): string
{
    return '<form method="post" class="shop-form" action="' . site_escape(shop_endpoint($base)) . '">'
        . '<input type="hidden" name="action" value="' . site_escape($action) . '"><input type="hidden" name="token" value="'
        . site_escape($state['token'] ?? '') . '"><input type="hidden" name="return" value="' . site_escape($return) . '">';
}

function shop_offer_html(string $base, array $index, array $settings, array $state, array $offer, string $return): string
{
    static $committed;
    $committed = $committed ?? site_read_record(SHOP_DIR . '/stock.php', SHOP_LIMIT);
    $id = $offer['id'];
    $html = '<div class="shop-offer"><strong>' . site_escape(shop_money((int) $offer['price_minor'], $settings)) . '</strong> '
        . site_escape($offer['variant']) . ' <small>' . site_escape($offer['sku']) . '</small>';
    foreach ($index['prices'][$id] ?? [] as $type => $price) {
        $html .= '<small class="shop-alt-price">' . site_escape($settings['priceLabels'][$type] ?? (string) $type)
            . ' · ' . site_escape(shop_money($price, $settings)) . '</small>';
    }
    if (shop_stock($index, $offer, $committed) > 0) {
        $html .= shop_form_start($base, 'add', $state, $return)
            . '<input type="hidden" name="offer" value="' . site_escape($id) . '"><button type="submit">' . site_escape(shop_label('add')) . '</button></form>';
    } else {
        $html .= '<small>' . site_escape(shop_label('unavailable')) . '</small>';
    }
    return $html . '</div>';
}

function shop_product_html(string $base, array $index, array $settings, array $state, string $id, ?array $listingOffers = null, ?array &$context = null): string
{
    $product = $index['products'][$id] ?? null;
    if (!$product || $product['active'] !== '1') return '';
    $url = shop_route_url($base, 'product/' . $product['slug']);
    $html = $attributes = $gallery = '';
    foreach ($listingOffers ?? ($index['productOffers'][$id] ?? []) as $offerId) {
        $html .= shop_offer_html($base, $index, $settings, $state, $index['offers'][$offerId], $url);
        if ($listingOffers !== null) continue;
        foreach ($index['kits'][$offerId] ?? [] as $component => $quantity) {
            $part = $index['offers'][$component];
            $html .= '<p>' . $quantity . ' × ' . site_escape($index['products'][$part['product_id']]['name'] . ' ' . $part['variant']) . '</p>';
        }
        foreach ($index['fields'] as $fieldId => $field) {
            if ($field['target'] !== 'offer' || empty($index['values'][$fieldId][$offerId])) continue;
            $html .= '<small class="shop-alt-price">' . site_escape($field['name'] . ' · ' . implode(', ', $index['values'][$fieldId][$offerId])) . '</small>';
        }
    }
    if ($listingOffers === null) {
        foreach ($index['fields'] as $fieldId => $field) {
            if ($field['target'] !== 'product' || empty($index['values'][$fieldId][$id])) continue;
            $attributes .= '<dt>' . site_escape($field['name']) . '</dt><dd>' . site_escape(implode(', ', $index['values'][$fieldId][$id])) . '</dd>';
        }
        foreach ($index['images'][$id] ?? [] as $pic) $gallery .= shop_image($base, $pic['image'], $pic['alt'] ?: $product['name']);
    }
    $image = shop_image($base, $product['image'] ?: ($index['images'][$id][0]['image'] ?? ''), $product['name']);
    $description = nl2br(site_escape($product['description']));
    if ($listingOffers === null && $context !== null && isset($context['extensions'], $context['snippets']) && empty($context['shopDescription'])) {
        $context['shopDescription'] = true;
        try { $description = site_expand_content($product['description'], $context['snippets'], $context); }
        finally { unset($context['shopDescription']); }
    }
    return shop_template($listingOffers === null ? 'product' : 'item', [
        'name'=>site_escape($product['name']), 'url'=>site_escape($url), 'image'=>$image,
        'images'=>'<div class="shop-gallery">' . ($product['image'] !== '' ? $image : '') . $gallery . '</div>',
        'summary'=>site_escape($product['summary']), 'description'=>$description,
        'attributes'=>$attributes === '' ? '' : '<dl>' . $attributes . '</dl>', 'offers'=>$html, 'type'=>site_escape($product['type']),
    ], $settings);
}

function shop_hidden_query(array $query, array $exclude = []): string
{
    $html = '';
    foreach (['q','c','sort','price_min','price_max','f','min','max'] as $key) {
        if (in_array($key, $exclude, true) || !isset($query[$key])) continue;
        $values = $query[$key];
        if (is_string($values)) {
            $html .= '<input type="hidden" name="' . $key . '" value="' . site_escape($values) . '">';
            continue;
        }
        if (!is_array($values)) continue;
        foreach (array_slice($values, 0, 100, true) as $id => $value) {
            if (!shop_id((string) $id)) continue;
            foreach (array_slice((array) $value, 0, 40) as $v) {
                if (!is_string($v)) continue;
                $html .= '<input type="hidden" name="' . $key . '[' . $id . ']' . (is_array($value) ? '[]' : '')
                    . '" value="' . site_escape($v) . '">';
            }
        }
    }
    return $html;
}

function shop_controls(string $tag, string $url, array $index, array $selection, array $query): string
{
    $html = '<form class="shop-controls" method="get" action="' . site_escape($url) . '">';
    if ($tag === 'search') {
        $html .= shop_hidden_query($query, ['q']) . '<label>' . site_escape(shop_label('search')) . ' <input type="search" name="q" maxlength="200" value="'
            . site_escape($selection['q']) . '"></label>';
    } elseif ($tag === 'sort') {
        $html .= shop_hidden_query($query, ['sort']) . '<label>' . site_escape(shop_label('sort')) . ' <select name="sort">';
        foreach ($selection['sorts'] as $id => $label) {
            $html .= '<option value="' . site_escape((string) $id) . '"' . ($id === $selection['sort'] ? ' selected' : '')
                . '>' . site_escape($label) . '</option>';
        }
        $html .= '</select></label>';
    } else {
        $html .= shop_hidden_query($query, ['f','min','max','price_min','price_max']);
        $html .= '<fieldset><legend>' . site_escape(shop_label('price')) . '</legend>';
        foreach (['price_min'=>'min','price_max'=>'max'] as $key => $label) {
            $html .= '<label>' . site_escape(shop_label($label)) . ' <input type="number" step="0.01" min="0" name="' . $key . '" value="'
                . site_escape(shop_input($query, $key)) . '"></label> ';
        }
        $html .= '</fieldset>';
        foreach ($selection['fields'] as $id => $field) {
            if ($field['filter'] !== '1') continue;
            $content = '';
            if ($field['type'] === 'number') {
                foreach (['min', 'max'] as $bound) {
                    $values = is_array($query[$bound] ?? null) ? $query[$bound] : [];
                    $content .= '<label>' . site_escape(shop_label($bound)) . ' <input type="number" step="any" name="' . $bound . '[' . $id
                        . ']" value="' . site_escape(shop_input($values, (string) $id)) . '"></label> ';
                }
            } else {
                $values = is_array($query['f'] ?? null) ? $query['f'] : [];
                foreach (array_keys($index['lookup'][$id] ?? []) as $value) {
                    $value = (string) $value;
                    $label = $field['type'] === 'boolean' ? shop_label($value === '1' ? 'yes' : 'no') : $value;
                    $content .= '<label><input type="checkbox" name="f[' . $id . '][]" value="' . site_escape($value) . '"'
                        . (in_array($value, (array) ($values[$id] ?? []), true) ? ' checked' : '') . '> ' . site_escape($label) . '</label> ';
                }
            }
            if ($content !== '') $html .= '<fieldset><legend>' . site_escape($field['name']) . '</legend>' . $content . '</fieldset>';
        }
    }
    return $html . '<button type="submit">' . site_escape(shop_label('apply')) . '</button></form>';
}

function shop_pagination(string $url, array $selection, array $query): string
{
    if ($selection['pages'] <= 1) return '';
    $query = array_intersect_key($query, array_flip(['q','c','sort','price_min','price_max','f','min','max']));
    $page = $selection['page'];
    $pages = $selection['pages'];
    $numbers = array_unique(array_merge([1], range(max(1, $page - 2), min($pages, $page + 2)), [$pages]));
    $html = '<nav class="shop-pagination" aria-label="' . site_escape(shop_label('pages')) . '">';
    foreach ($numbers as $number) {
        $href = $url . '?' . http_build_query($query + ['p' => $number], '', '&', PHP_QUERY_RFC3986);
        $html .= '<a class="button" href="' . site_escape($href) . '"' . ($number === $page ? ' aria-current="page"' : '') . '>' . $number . '</a> ';
    }
    return $html . '</nav>';
}

function shop_cart_html(string $base, array $settings, array $index, array $state, bool $checkout): string
{
    $cart = $state['cart'] ?? [];
    if (!$cart) {
        $last = $state['last'] ?? '';
        if ($checkout && $last !== '') return '<p role="status">' . site_escape(shop_label('order_received') . ' #' . substr($last, 0, 12)) . '</p>';
        return '<p>' . site_escape(shop_label('empty_cart')) . '</p>';
    }
    $quote = shop_cart_quote($index, $cart, site_read_record(SHOP_DIR . '/stock.php', SHOP_LIMIT));
    $url = shop_endpoint($base, ['view'=>'cart']);
    $html = '<ul class="shop-cart-items">';
    foreach ($cart as $id => $qty) {
        if (isset($quote['lines'][$id])) {
            [$product, $offer] = $quote['lines'][$id];
            $label = $product['name'] . ' ' . $offer['variant'] . ' · ' . shop_money((int) $offer['price_minor'], $settings);
        } else $label = (string) $id . ' · ' . shop_label('unavailable');
        $html .= '<li>' . site_escape($label) . ' ' . shop_form_start($base, 'quantity', $state, $url)
            . '<input type="hidden" name="offer" value="' . site_escape((string) $id) . '"><label>' . site_escape(shop_label('quantity'))
            . ' <input type="number" name="qty" min="0" max="999" value="' . (int) $qty . '"></label> <button>'
            . site_escape(shop_label('apply')) . '</button> <button name="remove" value="1">' . site_escape(shop_label('remove')) . '</button></form></li>';
    }
    $html .= '</ul><p><strong>' . site_escape(shop_label('total') . ' · ' . shop_money($quote['total'], $settings)) . '</strong></p>';
    if (!$quote['valid']) return $html . '<p>' . site_escape(shop_label('stock')) . '</p>';
    if (!$checkout) return $html . '<a class="button" href="' . site_escape(shop_endpoint($base, ['view'=>'checkout'])) . '">' . site_escape(shop_label('checkout')) . '</a>';
    if (!$settings['guest'] && empty($state['user'])) return $html . '<a class="button" href="' . site_escape(site_url($base) . '?account=1') . '">' . site_escape(shop_label('login')) . '</a>';
    $user = $state['user'] ?? [];
    $savedAddresses = preg_split('/\R/u', $user['addresses'] ?? '', -1, PREG_SPLIT_NO_EMPTY);
    if ($savedAddresses) $user['address'] = reset($savedAddresses);
    $html .= shop_form_start($base, 'order', $state, shop_endpoint($base, ['view'=>'checkout']))
        . '<input type="hidden" name="nonce" value="' . site_escape($state['order']) . '"><input type="hidden" name="quoted" value="' . $quote['total'] . '">'
        . '<input type="hidden" name="quoted_shipping" value="' . $settings['shipping'] . '">';
    foreach (['name'=>'text','email'=>'email','phone'=>'tel','address'=>'text'] as $field => $type) {
        $html .= '<label>' . site_escape(shop_label($field)) . ' <input maxlength="200" ' . ($field === 'address' ? '' : 'required ')
            . 'name="' . $field . '" type="' . $type . '"' . ($field === 'address' && $savedAddresses ? ' list="shop-saved-addresses"' : '') . ' value="' . site_escape($user[$field] ?? '') . '"></label> ';
    }
    if ($savedAddresses) {
        $html .= '<datalist id="shop-saved-addresses">';
        foreach ($savedAddresses as $address) $html .= '<option value="' . site_escape($address) . '">';
        $html .= '</datalist>';
    }
    $html .= '<label>' . site_escape(shop_label('delivery')) . ' <select name="method"><option value="pickup">' . site_escape(shop_label('pickup'))
        . '</option><option value="shipping">' . site_escape(shop_label('shipping') . ' · ' . shop_money($settings['shipping'], $settings))
        . '</option></select></label> <button>' . site_escape(shop_label('checkout')) . '</button></form>';
    return shop_template('checkout', ['title'=>site_escape(shop_label('checkout')), 'body'=>$html], $settings);
}

function shop_account_html(string $base, array $state, array $settings, bool $ordersOnly): string
{
    if (empty($state['user'])) {
        return '<p><a href="' . site_escape(site_url($base) . '?account=1') . '">' . site_escape(shop_label('login')) . '</a></p>';
    }
    $body = '';
    if (!$ordersOnly) {
        $body = '<p>' . site_escape($state['user']['name']) . '</p><a href="' . site_escape(site_url($base) . '?account=1') . '">'
            . site_escape(shop_label('account')) . '</a>';
        $roles = explode(',', $state['user']['roles']);
        if (array_intersect($roles, ['catalog','orders'])) {
            $body .= ' <a href="' . site_escape(shop_endpoint($base, ['staff'=>1])) . '">' . site_escape(shop_label('staff')) . '</a>';
        }
    }
    $body .= '<h3>' . site_escape(shop_label('orders')) . '</h3><ul>';
    foreach (shop_orders() as $order) {
        if ($order['head'][3] !== $state['user']['id']) continue;
        $body .= '<li>' . site_escape(substr($order['id'], 0, 12) . ' · ' . $order['head'][1] . ' · '
            . shop_label($order['head'][2]) . ' · ' . shop_money((int) $order['head'][10], ['currency'=>$order['head'][11]])) . '</li>';
    }
    return shop_template('account', ['title'=>site_escape(shop_label('account')), 'body'=>$body . '</ul>'], $settings);
}

function shop_component(string $tag, array $params, array &$context): string
{
    $base = $context['base'];
    $settings = shop_settings();
    $index = shop_index();
    $state = shop_session($base);
    $route = $context['shopRoute'] ?? ['category'=>'', 'brand'=>'', 'product'=>''];
    $query = $_GET;
    $category = $params['category'] ?? ($route['category'] ?: shop_input($query, 'c'));
    $category = isset($index['categories'][$category]) ? (string) $category : '';
    $page = $context['page'] ?? '';
    $url = $context['shopUrl'] ?? ($page !== '' ? site_page_url($base, $page) : shop_catalog_url($base, $settings));
    if (isset($params['category'])) $query['c'] = $category;
    if ($tag === 'categories') return shop_tree_html($base, $index, $settings, $category);
    if ($tag === 'basket') return shop_template('basket', ['url'=>site_escape(shop_endpoint($base, ['view'=>'cart'])),
        'label'=>site_escape(shop_label('basket')), 'count'=>(string) array_sum($state['cart'] ?? [])], $settings);
    if ($tag === 'cart' || $tag === 'checkout') return shop_cart_html($base, $settings, $index, $state, $tag === 'checkout');
    if ($tag === 'account' || $tag === 'orders') return shop_account_html($base, $state, $settings, $tag === 'orders');
    if ($tag === 'product') return empty($context['shopDescription']) ? shop_product_html($base, $index, $settings, $state, (string) ($params['id'] ?? $route['product']), null, $context) : '';
    if ($tag === 'breadcrumbs') {
        $links = ['<a href="' . site_escape(shop_route_url($base)) . '">' . site_escape(shop_label('catalog')) . '</a>'];
        foreach ($index['tree'][$category] ?? [] as $id) {
            $links[] = '<a href="' . site_escape(shop_category_url($base, $index, (string) $id)) . '">' . site_escape($index['categories'][$id]['name']) . '</a>';
        }
        return '<nav class="shop-crumbs">' . implode(' / ', $links) . '</nav>';
    }
    $size = max(1, min(96, (int) ($params['limit'] ?? $settings['pageSize'])));
    $key = hash('sha256', serialize([$category, $route['brand'], $query, $size]));
    if (!isset($context['shopLists'][$key])) $context['shopLists'][$key] = shop_listing($index, $category, $route['brand'], $query, $size);
    $list = $context['shopLists'][$key];
    if ($tag === 'pages') return shop_pagination($url, $list, $query);
    if ($tag !== 'items') return shop_controls($tag, $url, $index, $list, $query);
    if (!$list['products']) return '<p>' . site_escape(shop_label('no_products')) . '</p>';
    $html = '<div class="shop-grid">';
    foreach ($list['products'] as $id => $offers) $html .= shop_product_html($base, $index, $settings, $state, (string) $id, $offers);
    return $html . '</div>' . shop_pagination($url, $list, $query);
}
