<?php
if (!defined('SITE_ROOT')) { http_response_code(404); exit; }

function shop_route_url(string $base, string $path = '', array $query = []): string
{
    return site_url($base, site_shop_public_folder() . '/' . ($path === '' ? '' : trim($path, '/') . '/'))
        . ($query ? '?' . http_build_query($query, '', '&', PHP_QUERY_RFC3986) : '');
}

function shop_category_path(array $index, string $id): string
{
    $slugs = [];
    foreach ($index['tree'][$id] ?? [] as $category) $slugs[] = $index['categories'][$category]['slug'];
    return implode('/', $slugs);
}

function shop_category_url(string $base, array $index, string $id): string
{
    return shop_route_url($base, shop_category_path($index, $id));
}

function shop_live_editor_link(string $base, string $kind, string $id = '', bool $current = false, bool $toolbar = false): string
{
    $actions = [
        'product'=>['product_edit_context','pen-to-square',['tab'=>'catalog','sheet'=>'products','product_action'=>'edit','product'=>$id]],
        'seo'=>['seo_edit_context','magnifying-glass',['tab'=>'catalog','sheet'=>'products','product_action'=>'edit','product'=>$id,'product_tab'=>'seo']],
        'category'=>['category_edit','folder-open',['tab'=>'catalog','sheet'=>'categories','category_action'=>'edit','category_id'=>$id]],
        'brand'=>['brand_edit_context','tag',['tab'=>'brands','brand'=>$id]],
        'template'=>['shop_edit_template','code',['tab'=>'templates','template'=>$id,'edit'=>'1']],
        'fragment'=>['shop_edit_template','code',['tab'=>'templates','fragment'=>$id]],
    ];
    [$label, $icon, $params] = $actions[$kind];
    $params += ['shop'=>'1','shop_edit'=>$kind === 'seo' ? 'product' : $kind];
    $href = site_url($base, 'admin/') . '?' . http_build_query($params + ['embed'=>'1'], '', '&', PHP_QUERY_RFC3986);
    $label = site_escape(shop_label($label));
    return '<a class="' . ($toolbar ? 'toolbar-item' : 'button shop-live-edit') . '" href="' . site_escape($href)
        . '" aria-haspopup="dialog" data-shop-live-editor data-editor-modal-label="' . $label . '"'
        . ($current ? ' data-shop-edit-current' : '') . ($toolbar ? ' data-nav-tooltip="' . $label . '"' : '')
        . '><i class="fa-solid fa-' . $icon . '" aria-hidden="true"></i> <span' . ($toolbar ? ' class="toolbar-label"' : '') . '>' . $label . '</span></a>';
}

function shop_product_path(array $index, array $product): string
{
    $category = $product['category_id'];
    $slug = $product['slug'];
    $children = [];
    foreach ($index['children'][$category] ?? [] as $id) $children[$index['categories'][$id]['slug']] = true;
    if (isset($children[$slug])) {
        $stem = $slug . '--p-' . substr(hash('sha256', $product['id']), 0, 12);
        $slug = $stem;
        for ($number = 2; isset($children[$slug]) || isset($index['slugs'][$slug]); $number++) $slug = $stem . '-' . $number;
    }
    return shop_category_path($index, $category) . '/' . $slug;
}

function shop_product_url(string $base, array $index, array $product): string
{
    return site_page_url($base, shop_product_page_path($index, $product));
}

function shop_product_page_path(array $index, array $product): string
{
    $route = site_shop_short_product_urls() ? 'product/' . $product['slug']
        : site_shop_public_folder() . '/' . shop_product_path($index, $product);
    return $route . '/index.php';
}

function shop_current_route(array $index, string $path, bool $shortProduct = false): ?array
{
    if ($shortProduct) {
        $id = $index['slugs'][$path] ?? '';
        $product = $index['products'][$id] ?? null;
        return $product && $product['active'] === '1' ? ['category'=>$product['category_id'], 'brand'=>'', 'product'=>(string) $id] : null;
    }
    if ($path === '') return ['category'=>'', 'brand'=>'', 'product'=>''];
    $slugs = explode('/', $path);
    $category = '';
    foreach ($slugs as $position => $slug) {
        $next = '';
        foreach ($index['children'][$category] ?? [] as $id) {
            if ($index['categories'][$id]['slug'] === $slug) { $next = (string) $id; break; }
        }
        if ($next === '') {
            if (site_shop_short_product_urls() || $category === '' || $position !== count($slugs) - 1) return null;
            $original = preg_replace('/--p-[a-f0-9]{12}(?:-[0-9]+)?\z/', '', $slug);
            $id = $index['slugs'][$slug] ?? ($index['slugs'][$original] ?? '');
            $product = $index['products'][$id] ?? null;
            if ($product && $product['active'] === '1' && $product['category_id'] === $category && shop_product_path($index, $product) === $path) {
                return ['category'=>$category, 'brand'=>'', 'product'=>(string) $id];
            }
            return null;
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
        'last'=>$_SESSION['shopLastOrder'] ?? '', 'checkoutDraft'=>$_SESSION['shopCheckoutDraft'] ?? []];
    if (!$active) session_write_close();
    return $snapshot;
}

function shop_defaults(): array
{
    return [
        'category' => "<li>\n    <a href=\"[shop-url]\">[shop-visual][shop-name]</a>\n    [shop-children]\n</li>",
        'item' => "<article class=\"shop-item\">\n    [shop-photo]\n    <h3><a href=\"[shop-url]\">[shop-name]</a></h3>\n    <p>[shop-summary]</p>\n    [shop-variants]\n</article>",
        'item-list' => "<article class=\"shop-item shop-item-row\">\n    [shop-photo]\n    <h3><a href=\"[shop-url]\">[shop-name]</a></h3>\n    [shop-variants]\n</article>",
        'product' => "<article class=\"shop-product\">\n    [shop-heading]\n    <p>[shop-summary]</p>\n    [shop-gallery]\n    <div>[shop-description]</div>\n    [shop-characteristics]\n    [shop-variants]\n</article>",
        'basket' => "<a class=\"button shop-basket\" href=\"[shop-url]\">\n    <i class=\"fa-solid fa-cart-shopping\" aria-hidden=\"true\"></i> [shop-label]\n    <strong>[shop-count]</strong>\n</a>",
        'checkout' => "<section class=\"shop-checkout\">\n    [shop-body]\n</section>",
        'account' => "<section class=\"shop-account\">\n    <h2>[shop-title]</h2>\n    [shop-body]\n</section>",
        'page' => "<main class=\"shop-container\">\n    <nav>[shop-home] [shop-basket] [shop-account]</nav>\n    <h1>[shop-title]</h1>\n    [shop-notice]\n    [shop-content]\n</main>",
        'pagination' => "<nav class=\"shop-pagination\" aria-label=\"[shop-label]\">\n    [shop-links]\n</nav>",
    ];
}

function shop_template_fragments(): array
{
    return array_diff_key(shop_defaults(), ['product'=>true]);
}

function shop_catalog_templates(): array
{
    static $templates;
    if ($templates !== null) return $templates;
    $file = site_design_file(site_design_active(), 'shop-card.php');
    $templates = $file === '' ? [] : require $file;
    return $templates = is_array($templates) ? array_filter($templates, 'is_string') : [];
}

function shop_fragment_source(string $kind, array $settings, bool $theme = false): string
{
    $fallback = $theme ? (shop_catalog_templates()[$kind] ?? shop_defaults()[$kind]) : shop_defaults()[$kind];
    return $settings['templates'][$kind] ?? ($kind === 'item-list' ? ($settings['templates']['item'] ?? $fallback) : $fallback);
}

function shop_template_shortcode(string $name): string
{
    $names = ['image'=>'photo','images'=>'gallery','attributes'=>'characteristics','offers'=>'variants'];
    return '[shop-' . ($names[$name] ?? $name) . ']';
}

function shop_migrate_template(string $html): string
{
    return preg_replace_callback('/\{\{(name|url|visual|children|image|heading|summary|images|description|attributes|offers|price|buy|label|count|body|title|home|basket|account|notice|content|type)\}\}/',
        static function (array $match): string { return shop_template_shortcode($match[1]); }, $html);
}

function shop_template(string $kind, array $values, array $settings, bool $theme = false): string
{
    $template = shop_fragment_source($kind, $settings, $theme);
    $tokens = [];
    foreach ($values as $key => $value) $tokens[shop_template_shortcode($key)] = $value;
    return strtr($template, $tokens);
}

function shop_image_url(string $base, string $path): string
{
    if (shop_image_name($path)) return shop_endpoint($base, ['image'=>$path]);
    return shop_library_image_path($path) ? site_url($base, substr($path, 1)) : $path;
}

function shop_image(string $base, string $path, string $title): string
{
    if ($path === '') return '';
    $url = shop_image_url($base, $path);
    return '<img src="' . site_escape($url) . '" alt="' . site_escape($title) . '" loading="lazy" decoding="async">';
}

function shop_section_link(string $base, array $index, string $id, string $pageUrl = ''): string
{
    return $pageUrl === '' ? shop_category_url($base, $index, $id)
        : $pageUrl . ($id === '' ? '' : '?' . http_build_query(['c'=>$id], '', '&', PHP_QUERY_RFC3986));
}

function shop_category_visual(string $base, ?array $media): string
{
    if ($media === null) return '';
    if ($media['type'] === 'image') return '<span class="shop-category-visual">' . shop_image($base, $media['value'], '') . '</span>';
    $icon = site_parse_page_icon($media['value']);
    if ($icon === null) return '';
    if ($icon[0] === 'image') return site_page_icon(['fields'=>['AnnouncementIcon'=>$media['value']]], $base);
    return '<i class="shop-category-visual fa-' . $icon[0] . ' fa-' . $icon[1] . '" aria-hidden="true"></i>';
}

function shop_tree_html(string $base, array $index, array $settings, string $parent, string $pageUrl = ''): string
{
    if (empty($index['children'][$parent])) return '';
    $html = '<ul class="shop-categories">';
    foreach ($index['children'][$parent] as $id) {
        $category = $index['categories'][$id];
        $html .= shop_template('category', [
            'url'=>site_escape(shop_section_link($base, $index, (string) $id, $pageUrl)),
            'name'=>site_escape($category['name']),
            'visual'=>shop_category_visual($base, $index['categoryMedia'][$id] ?? null),
            'description'=>site_escape($category['description']),
            'children'=>shop_tree_html($base, $index, $settings, (string) $id, $pageUrl),
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

function shop_scoped_offers(array $index, string $category, string $brand): array
{
    $includeChildren = $category === '' || ($index['categories'][$category]['descendants'] ?? '') === '1';
    $ids = array_fill_keys(($includeChildren ? $index['lists'] : $index['local'])[$category] ?? [], true);
    if ($brand !== '') {
        $brands = [];
        foreach (array_keys($index['brands'][$brand] ?? []) as $value) {
            foreach ($index['lookup']['brand'][$value] ?? [] as $id) $brands[$id] = true;
        }
        $ids = array_intersect_key($ids, $brands);
    }
    return $ids;
}

function shop_listing(array $index, string $category, string $brand, array $query, int $size, ?array $settings = null): array
{
    $settings = $settings ?? shop_settings();
    $options = shop_list_options($index, $category, $query);
    $ids = shop_scoped_offers($index, $category, $brand);
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
        } elseif (in_array($field['type'], ['number','price'], true)) {
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
        $price = shop_rounded_price((int) $offer['price_minor'], $settings);
        if ($price < 1 || (is_numeric($prices[0]) && $price < 100 * (float) $prices[0])
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
    $contact = in_array($action, ['order','quick-order'], true) ? ' data-shop-checkout-form data-phone-profile="' . site_escape(json_encode(site_phone_profile())) . '"'
        . ' data-phone-error="' . site_escape(site_translation('Укажите полный номер телефона, включая код страны.', shop_locale())) . '"' : '';
    return '<form method="post" class="shop-form" action="' . site_escape(shop_endpoint($base)) . '"'
        . $contact . (in_array($action, ['add','quantity'], true) ? ' data-shop-cart-form data-error="' . site_escape(shop_label('try_again')) . '"' : '') . '>'
        . '<input type="hidden" name="action" value="' . site_escape($action) . '"><input type="hidden" name="token" value="'
        . site_escape($state['token'] ?? '') . '"><input type="hidden" name="return" value="' . site_escape($return) . '">';
}

function shop_cart_json(string $base, array $settings, array $index, string $message = '', int $status = 200): void
{
    $state = shop_session($base);
    session_write_close();
    http_response_code($status);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode(['title'=>shop_label('basket'),'close'=>shop_label('product_close'),'message'=>$message,
        'count'=>array_sum($state['cart'] ?? []),'html'=>shop_cart_html($base, $settings, $index, $state, false)], JSON_UNESCAPED_UNICODE);
}

function shop_offer_html(string $base, array $index, array $settings, array $state, array $offer, string $return, ?string $features = null, bool $single = false): string
{
    if ((int) $offer['price_minor'] < 1) return '';
    $stock = shop_cached_stock();
    $id = $offer['id'];
    $priceText = shop_offer_price_markup($offer, $settings);
    if ($single) {
        $html = '<div class="shop-offer shop-offer-single"><div class="shop-single-purchase"><div class="shop-single-price">' . $priceText;
        $reference = implode(' · ', array_unique(array_filter([$offer['variant'], $offer['sku']], 'strlen')));
        if ($reference !== '') $html .= '<small class="shop-single-sku">' . site_escape($reference) . '</small>';
    } elseif ($features === null) {
        $html = '<div class="shop-offer">' . $priceText . ' '
            . site_escape($offer['variant']) . ' <small>' . site_escape($offer['sku']) . '</small>';
    } else {
        $name = $offer['variant'] !== '' ? $offer['variant'] : $offer['sku'];
        $image = shop_image($base, $index['offerMedia'][$id] ?? '', $name);
        $html = '<article class="shop-offer shop-variant-card"><div class="shop-variant-header">' . $image
            . '<div><h3>' . site_escape($name) . '</h3>'
            . ($name === $offer['sku'] ? '' : '<small>' . site_escape($offer['sku']) . '</small>')
            . '</div></div>' . $features . '<div class="shop-variant-price">' . $priceText . '</div>';
    }
    foreach ($index['prices'][$id] ?? [] as $type => $price) {
        if ($price < 1) continue;
        $text = site_escape(shop_money(shop_rounded_price($price, $settings), $settings));
        $html .= '<small class="shop-alt-price">' . site_escape($settings['priceLabels'][$type] ?? (string) $type)
            . ' · ' . $text . '</small>';
    }
    $quick = ($index['products'][$offer['product_id']]['quick_pickup'] ?? '') === '1';
    if (shop_stock($index, $offer, $stock['committed']) > 0) {
        $action = shop_buy_form($base, $state, $return, [$offer], $single, $quick);
    } else {
        $status = $quick ? shop_quick_stock_label($index, $offer, $stock) : 'unavailable';
        $action = '<small class="shop-stock-status shop-stock-' . $status . '">' . site_escape(shop_label($status)) . '</small>';
    }
    if ($single) return $html . '</div>' . $action . '</div>' . $features . '</div>';
    $html .= $action;
    return $html . ($features === null ? '</div>' : '</article>');
}

function shop_offer_price_markup(array $offer, array $settings): string
{
    $current = shop_rounded_price((int) $offer['price_minor'], $settings);
    $previous = shop_rounded_price((int) ($offer['old_price_minor'] ?? 0), $settings);
    return '<span class="shop-price-pair"><strong>' . site_escape(shop_money($current, $settings)) . '</strong>'
        . ($previous > 0 ? '<s title="' . site_escape(shop_label('old_price')) . '">' . site_escape(shop_money($previous, $settings)) . '</s>' : '') . '</span>';
}

function shop_buy_form(string $base, array $state, string $return, array $offers, bool $primary = false, bool $quick = false): string
{
    if (!$offers) return '<small>' . site_escape(shop_label('unavailable')) . '</small>';
    $buttonClass = $primary || $quick ? ' button-primary' : '';
    $label = site_escape(shop_label($quick ? 'quick_order' : 'add'));
    $icon = '<i class="fa-solid fa-' . ($quick ? 'phone' : 'cart-plus') . '" aria-hidden="true"></i> ';
    if ($quick && count($offers) === 1) {
        $link = shop_endpoint($base, ['view'=>'quick','offer'=>reset($offers)['id']]);
        return '<a class="button shop-quick-button' . $buttonClass . '" data-shop-quick-order data-close="'
            . site_escape(shop_label('product_close')) . '" data-error="' . site_escape(shop_label('try_again'))
            . '" href="' . site_escape($link) . '">' . $icon . $label . '</a>';
    }
    $html = $quick ? '<form method="get" class="shop-form" action="' . site_escape(shop_endpoint($base)) . '">'
        . '<input type="hidden" name="view" value="quick">' : shop_form_start($base, 'add', $state, $return);
    if (count($offers) === 1) {
        $html .= '<input type="hidden" name="offer" value="' . site_escape(reset($offers)['id']) . '">';
    } else {
        $html .= '<label class="field">' . site_escape(shop_label('product_variant')) . '<select name="offer">';
        foreach ($offers as $offer) {
            $html .= '<option value="' . site_escape($offer['id']) . '">' . site_escape($offer['variant'] ?: $offer['sku']) . '</option>';
        }
        $html .= '</select></label>';
    }
    return $html . '<button type="submit"' . ($buttonClass !== '' ? ' class="button-primary"' : '') . '>' . $icon . $label . '</button></form>';
}

function shop_product_schema(string $base, array $index, array $settings, array $product, array $offers, array $available): string
{
    $canonical = site_canonical_url($base, shop_product_page_path($index, $product));
    if (!preg_match('~\Ahttps?://[^/]+~', $canonical, $origin)) return '';
    $id = $product['id'];
    $schema = ['@context'=>'https://schema.org','@type'=>'Product','name'=>$product['name'],'url'=>$canonical];
    $description = trim(strip_tags($product['summary'] ?: $product['description']));
    if ($description !== '') $schema['description'] = $description;
    $pictures = [$product['image']];
    foreach ($index['images'][$id] ?? [] as $image) $pictures[] = $image['image'];
    foreach ($offers as $offer) $pictures[] = $index['offerMedia'][$offer['id']] ?? '';
    foreach (array_unique(array_filter($pictures, 'strlen')) as $picture) {
        $url = shop_image_url($base, $picture);
        $schema['image'][] = strpos($url, '/') === 0 ? $origin[0] . $url : $url;
    }
    $brand = $index['values']['brand'][$id][0] ?? '';
    if ($brand !== '') $schema['brand'] = ['@type'=>'Brand','name'=>$brand];
    $structuredOffers = [];
    foreach ($offers as $offer) {
        $entry = ['@type'=>'Offer','url'=>$canonical,'priceCurrency'=>$settings['currency'],
            'price'=>number_format(shop_rounded_price((int) $offer['price_minor'], $settings) / 100, 2, '.', ''),
            'availability'=>'https://schema.org/' . (isset($available[$offer['id']]) ? 'InStock' : 'OutOfStock')];
        if ($offer['sku'] !== '') $entry['sku'] = $offer['sku'];
        $structuredOffers[] = $entry;
    }
    if (count($structuredOffers) === 1) $schema['sku'] = $offers[0]['sku'];
    if ($structuredOffers) $schema['offers'] = count($structuredOffers) === 1 ? $structuredOffers[0] : $structuredOffers;
    $json = json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_INVALID_UTF8_SUBSTITUTE);
    return $json === false ? '' : '<script type="application/ld+json">' . $json . '</script>';
}

function shop_characteristic_html(array $values, array $field, ?array &$context): string
{
    $type = shop_field_type($field['type']);
    if ($type === 'hidden') return '';
    if ($type === 'form') {
        $form = reset($values);
        if ($context === null || !is_string($form) || !site_valid_form_id($form)) return '';
        return site_render_action_button(['action'=>'form','form'=>$form,'label'=>$field['name']], $context);
    }
    return implode(', ', array_map(static function (string $value) use ($type): string {
        if ($type === 'bool') return site_escape(shop_label($value === '1' ? 'yes' : 'no'));
        return site_iblock_cell($value, $type);
    }, $values));
}

function shop_product_gallery(string $base, array $product, array $extras): string
{
    $photos = [];
    if ($product['image'] !== '') $photos[$product['image']] = '';
    foreach ($extras as $photo) {
        if ($photo['alt'] !== '' || !isset($photos[$photo['image']])) $photos[$photo['image']] = $photo['alt'];
    }
    if (!$photos) return '';
    $pictures = [];
    foreach ($photos as $path => $caption) {
        $pictures[] = ['url'=>shop_image_url($base, $path), 'alt'=>$caption !== '' ? $caption : $product['name'], 'caption'=>$caption];
    }
    $html = '<div class="shop-gallery' . (count($pictures) > 1 ? ' shop-gallery-multiple' : '') . '" data-shop-gallery'
        . ' data-photo-close="' . site_escape(shop_label('product_close')) . '"'
        . ' data-photo-prev="' . site_escape(shop_label('gallery_prev')) . '" data-photo-next="' . site_escape(shop_label('gallery_next')) . '">';
    if (count($pictures) > 1) {
        $html .= '<div class="shop-gallery-thumbs" aria-label="' . site_escape(shop_label('images')) . '">';
        foreach ($pictures as $position => $picture) {
            $html .= '<a href="' . site_escape($picture['url']) . '" title="' . site_escape($picture['alt']) . '" data-shop-gallery-thumb="' . $position . '" data-photo-caption="' . site_escape($picture['caption']) . '" data-photo-alt="' . site_escape($picture['alt']) . '"'
                . ($position === 0 ? ' aria-current="true"' : '') . ' aria-label="'
                . site_escape($picture['alt'] . ' · ' . ($position + 1)) . '"><img src="' . site_escape($picture['url'])
                . '" alt="" loading="lazy" decoding="async"></a>';
        }
        $html .= '</div>';
    }
    $first = $pictures[0];
    return $html . '<a class="shop-gallery-main" href="' . site_escape($first['url']) . '" title="' . site_escape($first['alt']) . '" data-shop-gallery-open data-photo-index="0"'
        . ' data-photos="' . site_escape((string) json_encode($pictures, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)) . '">'
        . '<img src="' . site_escape($first['url']) . '" alt="' . site_escape($first['alt']) . '" decoding="async">'
        . '<span class="shop-gallery-caption" data-shop-gallery-caption' . ($first['caption'] === '' ? ' hidden' : '') . '>'
        . site_escape($first['caption']) . '</span></a></div>';
}

function shop_listing_shortcodes(string $base, array $index, array $settings, array $state, array $product, array $offers, string $url, bool $door): array
{
    $id = $product['id'];
    $fieldValue = static function (string $name) use ($index, $id): string {
        return $index['values'][$name][$id][0] ?? '';
    };
    $gallery = $index['images'][$id] ?? [];
    $photos = '';
    if ($door) {
        $paths = array_values(array_unique(array_filter(array_merge([$product['image']], array_column($gallery, 'image')), 'strlen')));
        foreach (array_slice($paths, 0, 2) as $position => $path) {
            $side = shop_label($position === 0 ? 'door_exterior' : 'door_interior');
            $photos .= '<a href="' . site_escape($url) . '" class="stal-door-photo">'
                . shop_image($base, $path, $product['name'] . ' · ' . $side) . '<span>' . site_escape($side) . '</span></a>';
        }
    } else {
        $photos = shop_product_gallery($base, $product, $gallery);
    }
    $offer = count($offers) === 1 ? reset($offers) : null;
    $previous = (int) ($offer['old_price_minor'] ?? 0);
    $current = (int) ($offer['price_minor'] ?? 0);
    $discount = $previous > $current ? (int) round(100 * (1 - $current / $previous)) : 0;
    $stock = shop_cached_stock();
    $available = array_filter($offers, static function (array $variant) use ($index, $stock): bool {
        return shop_stock($index, $variant, $stock['committed']) > 0;
    });
    $quick = ($product['quick_pickup'] ?? '') === '1';
    $status = $quick && $offer !== null ? shop_quick_stock_label($index, $offer, $stock) : 'unavailable';
    $statusHtml = $available ? '' : '<small class="shop-stock-status shop-stock-' . $status . '">' . site_escape(shop_label($status)) . '</small>';
    $prices = array_map(static function (array $variant) use ($settings): int {
        return shop_rounded_price((int) $variant['price_minor'], $settings);
    }, $offers);
    $specs = $video = '';
    foreach ($index['fields'] as $fieldId => $field) {
        if ($field['target'] !== 'product' || empty($index['values'][$fieldId][$id])
            || in_array($field['type'], ['hidden','form'], true)) continue;
        if ($field['type'] === 'video') {
            if ($video === '') $video = site_video_button($fieldValue($fieldId));
            continue;
        }
        if ($door && in_array($fieldId, ['doors-model','doors-width','doors-height','doors-opening'], true)) continue;
        $context = null;
        $entry = shop_characteristic_html($index['values'][$fieldId][$id], $field, $context);
        if ($door && $fieldId === 'doors-showroom') {
            $address = $fieldValue($fieldId);
            $entry = '<a href="' . site_escape('https://yandex.ru/maps/?text=' . rawurlencode($address)) . '" rel="noopener" target="_blank">'
                . site_escape($address) . '</a>';
        }
        if ($entry !== '') $specs .= '<div><dt>' . shop_field_icon($field, $base) . site_escape(shop_field_label($field))
            . '</dt><dd>' . $entry . '</dd></div>';
    }
    $attributes = $specs === '' ? '' : '<dl class="shop-specs">' . $specs . '</dl>';
    $width = $fieldValue('doors-width');
    $height = $fieldValue('doors-height');
    $opening = $fieldValue('doors-opening');
    $sku = $offer['sku'] ?? implode(', ', array_column($offers, 'sku'));
    if ($door) $sku = preg_replace('/\AСТАЛ-/u', '', $sku) ?? $sku;
    return [
        'images'=>$photos, 'model'=>site_escape($fieldValue('doors-model') ?: $product['name']), 'sku'=>site_escape($sku),
        'size'=>$width === '' || $height === '' ? '' : '<span><i class="fa-solid fa-ruler" aria-hidden="true"></i> '
            . site_escape($width . ' × ' . $height . ' ' . ($index['fields']['doors-width']['unit'] ?? '')) . '</span>',
        'opening'=>$opening === '' ? '' : '<span>' . shop_field_icon($index['fields']['doors-opening'] ?? [], $base) . site_escape($opening) . '</span>',
        'price'=>site_escape(shop_price_range($prices, $settings)),
        'old-price'=>$previous <= 0 ? '' : '<s title="' . site_escape(shop_label('old_price')) . '">'
            . site_escape(shop_money(shop_rounded_price($previous, $settings), $settings)) . '</s>',
        'discount'=>$discount <= 0 ? '' : '<span class="stal-door-discount">−' . $discount . '%</span>',
        'buy'=>$available ? shop_buy_form($base, $state, $url, $available, true, $quick) : $statusHtml,
        'stock'=>$statusHtml, 'video'=>$video, 'attributes'=>$attributes,
        'details'=>$attributes === '' ? '' : '<details class="stal-door-details"><summary>'
            . site_escape(shop_label($door ? 'door_details' : 'record_properties')) . '</summary>' . $attributes . '</details>',
    ];
}

function shop_product_html(string $base, array $index, array $settings, array $state, string $id, ?array $listingOffers = null, ?array &$context = null, bool $list = false): string
{
    $product = $index['products'][$id] ?? null;
    if (!$product || $product['active'] !== '1') return '';
    $url = shop_product_url($base, $index, $product);
    $html = $attributes = $gallery = '';
    $offerIds = array_filter($listingOffers ?? ($index['productOffers'][$id] ?? []), static function ($offerId) use ($index): bool {
        return (int) $index['offers'][$offerId]['price_minor'] > 0;
    });
    if ($listingOffers === null) {
        $singleOffer = count($offerIds) === 1;
        $variantImages = $index['images'][$id] ?? [];
        $valuesByField = [];
        foreach ($index['fields'] as $fieldId => $field) {
            if ($field['target'] !== 'offer' || shop_field_type($field['type']) === 'hidden') continue;
            $values = [];
            foreach ($offerIds as $offerId) $values[$offerId] = implode("\x1f", $index['values'][$fieldId][$offerId] ?? []);
            if (array_filter($values, 'strlen')) $valuesByField[$fieldId] = count(array_unique($values)) > 1;
        }
        foreach ($offerIds as $offerId) {
            $offer = $index['offers'][$offerId];
            $specs = $kitHtml = '';
            foreach ($valuesByField as $fieldId => $different) {
                $field = $index['fields'][$fieldId];
                $value = shop_characteristic_html($index['values'][$fieldId][$offerId] ?? [], $field, $context);
                if ($value === '' && (!$different || $field['type'] === 'form')) continue;
                $specs .= '<div' . ($different ? ' class="shop-variant-difference"' : '') . '><dt>'
                    . shop_field_icon($field, $base) . site_escape(shop_field_label($field)) . '</dt><dd>' . ($value === '' ? '—' : $value) . '</dd></div>';
            }
            foreach ($index['kits'][$offerId] ?? [] as $component => $quantity) {
                $part = $index['offers'][$component];
                $kitHtml .= '<li>' . $quantity . ' × ' . site_escape($index['products'][$part['product_id']]['name'] . ' ' . $part['variant']) . '</li>';
            }
            $features = ($specs === '' ? '' : '<dl class="shop-variant-specs">' . $specs . '</dl>')
                . ($kitHtml === '' ? '' : '<ul class="shop-variant-kit">' . $kitHtml . '</ul>');
            $html .= shop_offer_html($base, $index, $settings, $state, $offer, $url, $features, $singleOffer);
            if (isset($index['offerMedia'][$offerId])) $variantImages[] = ['image'=>$index['offerMedia'][$offerId],
                'alt'=>$product['name'] . ($offer['variant'] === '' ? '' : ' · ' . $offer['variant'])];
        }
        if ($html === '') {
            $html = '<p>' . site_escape(shop_label('unavailable')) . '</p>';
        } elseif (!$singleOffer) {
            $html = '<section class="shop-variants"><h2>' . site_escape(shop_label('variants')) . '</h2><div class="shop-variant-grid">' . $html . '</div></section>';
        }
        foreach ($index['fields'] as $fieldId => $field) {
            if ($field['target'] !== 'product' || empty($index['values'][$fieldId][$id])) continue;
            $value = shop_characteristic_html($index['values'][$fieldId][$id], $field, $context);
            if ($value !== '') $attributes .= '<div class="shop-spec"><dt>' . shop_field_icon($field, $base) . site_escape(shop_field_label($field)) . '</dt><dd>' . $value . '</dd></div>';
        }
        $gallery = shop_product_gallery($base, $product, $variantImages);
    } else foreach ($offerIds as $offerId) $html .= shop_offer_html($base, $index, $settings, $state, $index['offers'][$offerId], $url);
    $image = shop_image($base, $product['image'] ?: ($index['images'][$id][0]['image'] ?? ''), $product['name']);
    $description = nl2br(site_escape($product['description']));
    if ($listingOffers === null && $context !== null && isset($context['extensions'], $context['snippets']) && empty($context['shopDescription'])) {
        $context['shopDescription'] = true;
        try { $description = site_expand_content($product['description'], $context['snippets'], $context); }
        finally { unset($context['shopDescription']); }
    }
    $values = [
        'name'=>site_escape($product['name']), 'url'=>site_escape($url), 'image'=>$image,
        'heading'=>empty($context['shopRoute']['product']) ? '<h2>' . site_escape($product['name']) . '</h2>' : '',
        'images'=>$gallery,
        'summary'=>site_escape($product['summary']), 'description'=>$description,
        'attributes'=>$attributes === '' ? '' : '<dl class="shop-specs">' . $attributes . '</dl>', 'offers'=>$html, 'type'=>site_escape($product['type']),
    ];
    if ($listingOffers === null && isset($context['shopProductMarkup'])) {
        $offers = array_values(array_intersect_key($index['offers'], array_fill_keys($offerIds, true)));
        $stock = shop_cached_stock();
        $available = [];
        foreach ($offers as $offer) {
            if (shop_stock($index, $offer, $stock['committed']) > 0) $available[$offer['id']] = $offer;
        }
        $prices = array_map(static function (array $offer) use ($settings): int {
            return shop_rounded_price((int) $offer['price_minor'], $settings);
        }, $offers);
        $values['price'] = count($offers) === 1 ? shop_offer_price_markup($offers[0], $settings) : site_escape(shop_price_range($prices, $settings));
        $quick = ($product['quick_pickup'] ?? '') === '1';
        $values['buy'] = $quick && count($offers) === 1 && !$available
            ? '<small class="shop-stock-status">' . site_escape(shop_label(shop_quick_stock_label($index, $offers[0], $stock))) . '</small>'
            : shop_buy_form($base, $state, $url, $available, false, $quick);
        $markup = shop_migrate_template($context['shopProductMarkup']);
        $context['shopDescription'] = true;
        try { $settings['templates']['product'] = site_expand_content($markup, $context['snippets'], $context); }
        finally { unset($context['shopDescription']); }
        $schema = shop_product_schema($base, $index, $settings, $product, $offers, $available);
        return shop_template('product', $values, $settings) . $schema;
    }
    if ($listingOffers === null) return shop_template('product', $values, $settings);
    $kind = $list ? 'item-list' : 'item';
    $category = site_design_info(site_design_active())['shopCategory'] ?? '';
    $themed = is_string($category) && isset($index['placements'][$id][$category], shop_catalog_templates()[$kind]);
    if ($themed || !empty($settings['templates'][$kind]) || ($list && !empty($settings['templates']['item']))) {
        $offers = array_map(static function (string $offerId) use ($index): array { return $index['offers'][$offerId]; }, $offerIds);
        $values = shop_listing_shortcodes($base, $index, $settings, $state, $product, $offers, $url, $themed) + $values;
    }
    return shop_template($kind, $values, $settings, $themed);
}

const SHOP_CATALOG_QUERY = ['q','c','b','sort','price_min','price_max','f','min','max','layout'];

function shop_hidden_query(array $query, array $exclude = []): string
{
    $html = '';
    foreach (SHOP_CATALOG_QUERY as $key) {
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

function shop_filter_options(array $index, array $ids, array $fields, array $filters, array $settings): array
{
    $fields = $filters['fields'] ? array_filter($fields, static function (array $field): bool {
        return $field['filter'] === '1';
    }) : [];
    $options = ['prices'=>[], 'fields'=>[]];
    foreach (array_keys($ids) as $offerId) {
        $offer = $index['offers'][$offerId];
        $price = shop_rounded_price((int) $offer['price_minor'], $settings);
        if ($price < 1) continue;
        if ($filters['price']) $options['prices'][shop_price_input($price)] = true;
        foreach ($fields as $fieldId => $field) {
            $target = $field['target'] === 'offer' ? $offerId : $offer['product_id'];
            $values = $index['values'][$fieldId][$target] ?? [];
            if (in_array($field['type'], ['number','price'], true)) $values = array_filter(array_slice($values, 0, 1), 'is_numeric');
            elseif (!isset($index['lookup'][$fieldId])) continue;
            foreach ($values as $value) $options['fields'][$fieldId][$value] = true;
        }
    }
    return $options;
}

function shop_range_select(string $name, string $label, string $current, array $range): string
{
    if (is_numeric($current)) $range[$current] = true;
    $choices = array_keys($range);
    sort($choices, SORT_NUMERIC);
    if (count($choices) > 80) {
        $last = count($choices) - 1;
        $sample = [];
        for ($i = 0; $i < 80; ++$i) $sample[] = $choices[(int) round($i * $last / 79)];
        if (is_numeric($current) && !in_array($current, array_map('strval', $sample), true)) $sample[] = $current;
        sort($sample, SORT_NUMERIC);
        $choices = $sample;
    }
    $html = '<label class="shop-filter-number"><span>' . site_escape($label) . '</span><select name="' . site_escape($name) . '"><option value="">—</option>';
    foreach ($choices as $value) {
        $value = (string) $value;
        $html .= '<option value="' . site_escape($value) . '"' . ($value === $current ? ' selected' : '') . '>'
            . site_escape(site_format_number($value)) . '</option>';
    }
    return $html . '</select></label>';
}

function shop_controls(string $tag, string $url, array $selection, array $query, array $options = [], bool $live = false): string
{
    $html = '<form class="shop-controls shop-controls-' . $tag . '" method="get" action="' . site_escape($url) . '"'
        . ($live ? ' data-shop-live-filter data-error="' . site_escape(shop_label('try_again')) . '"' : '') . '>';
    if ($tag === 'search') {
        $label = site_escape(shop_label('search'));
        $html .= shop_hidden_query($query, ['q']) . '<label><span class="shop-toolbar-label">' . $label
            . '</span><input type="search" name="q" maxlength="200" placeholder="' . $label . '" value="'
            . site_escape($selection['q']) . '"></label>';
    } elseif ($tag === 'sort') {
        $html .= shop_hidden_query($query, ['sort']) . '<label><span class="shop-toolbar-label">' . site_escape(shop_label('sort'))
            . '</span><i class="fa-solid fa-arrow-down-wide-short" aria-hidden="true"></i> <select name="sort" data-shop-auto-sort>';
        foreach ($selection['sorts'] as $id => $label) {
            $html .= '<option value="' . site_escape((string) $id) . '"' . ($id === $selection['sort'] ? ' selected' : '')
                . '>' . site_escape($label) . '</option>';
        }
        $html .= '</select></label>';
    } else {
        $html .= shop_hidden_query($query, ['f','min','max','price_min','price_max']);
        if ($options['prices']) {
            $html .= '<details class="shop-filter-option" data-shop-popover><summary><i class="fa-solid fa-tag" aria-hidden="true"></i> ' . site_escape(shop_label('price')) . '</summary>'
                . '<fieldset class="shop-filter-popover" aria-label="' . site_escape(shop_label('price')) . '">';
            foreach (['price_min'=>'min','price_max'=>'max'] as $key => $label) {
                $html .= shop_range_select($key, shop_label($label), shop_input($query, $key), $options['prices']);
            }
            $html .= '</fieldset></details>';
        }
        foreach ($selection['fields'] as $id => $field) {
            $choices = $options['fields'][$id] ?? [];
            if (!$choices) continue;
            $content = '';
            $numeric = in_array($field['type'], ['number','price'], true);
            if ($numeric) {
                foreach (['min', 'max'] as $bound) {
                    $values = is_array($query[$bound] ?? null) ? $query[$bound] : [];
                    $content .= shop_range_select($bound . '[' . $id . ']', shop_label($bound), shop_input($values, (string) $id), $choices);
                }
            } else {
                $values = is_array($query['f'] ?? null) ? $query['f'] : [];
                foreach (array_keys($choices) as $value) {
                    $value = (string) $value;
                    $label = shop_field_type($field['type']) === 'bool' ? shop_label($value === '1' ? 'yes' : 'no') : $value;
                    $content .= '<label class="shop-filter-choice"><input type="checkbox" name="f[' . $id . '][]" value="' . site_escape($value) . '"'
                        . (in_array($value, (array) ($values[$id] ?? []), true) ? ' checked' : '') . '><span>' . site_escape($label) . '</span></label> ';
                }
            }
            if ($content !== '') {
                $label = site_escape(shop_field_label($field));
                $icon = $numeric ? 'sliders' : 'list-check';
                $html .= '<details class="shop-filter-option" data-shop-popover><summary><i class="fa-solid fa-' . $icon . '" aria-hidden="true"></i> ' . $label . '</summary><fieldset class="shop-filter-popover" aria-label="'
                    . $label . '">' . $content . '</fieldset></details>';
            }
        }
    }
    $submit = '<button type="submit">' . site_escape(shop_label('apply')) . '</button>';
    if ($tag === 'sort') $submit = '<noscript>' . $submit . '</noscript>';
    if ($tag === 'search') {
        $label = site_escape(shop_label('search'));
        $submit = '<button type="submit" class="shop-search-submit" title="' . $label . '" aria-label="' . $label
            . '"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i></button>';
    }
    if ($tag === 'filters') {
        $keep = array_intersect_key($query, array_flip(['q','c','b','sort','layout']));
        $href = $url . ($keep ? '?' . http_build_query($keep, '', '&', PHP_QUERY_RFC3986) : '');
        $submit .= '<a class="shop-filter-reset" data-shop-filter-reset href="' . site_escape($href)
            . '"><i class="fa-solid fa-rotate-left" aria-hidden="true"></i> ' . site_escape(shop_label('filter_reset')) . '</a>';
    }
    return $html . $submit
        . ($live ? '<p role="status" class="shop-filter-message" data-shop-filter-status hidden></p>' : '') . '</form>';
}

function shop_view_switch(string $url, array $query): string
{
    $layout = $query['layout'] ?? 'grid';
    $query = array_intersect_key($query, array_flip(array_merge(SHOP_CATALOG_QUERY, ['p'])));
    unset($query['layout']);
    $html = '<div class="shop-view-switch">';
    foreach (['grid'=>'table-cells-large', 'list'=>'list'] as $mode => $icon) {
        $params = $mode === 'list' ? $query + ['layout'=>'list'] : $query;
        $href = $url . ($params ? '?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986) : '');
        $label = site_escape(shop_label('view_' . $mode));
        $html .= '<a href="' . site_escape($href) . '" title="' . $label . '" aria-label="' . $label . '"'
            . ($layout === $mode ? ' aria-current="true"' : '') . '><i class="fa-solid fa-' . $icon . '" aria-hidden="true"></i></a>';
    }
    return $html . '</div>';
}

function shop_pagination(string $url, array $selection, array $query, string $parameter = 'p', string $anchor = '', ?array $settings = null): string
{
    if ($selection['pages'] <= 1) return '';
    if ($parameter === 'p') $query = array_intersect_key($query, array_flip(SHOP_CATALOG_QUERY));
    unset($query[$parameter]);
    $page = $selection['page'];
    $pages = $selection['pages'];
    $numbers = array_unique(array_merge([1], range(max(1, $page - 2), min($pages, $page + 2)), [$pages]));
    $html = '';
    foreach ($numbers as $number) {
        $href = $url . '?' . http_build_query($query + [$parameter => $number], '', '&', PHP_QUERY_RFC3986) . $anchor;
        $html .= '<a class="button" href="' . site_escape($href) . '"' . ($number === $page ? ' aria-current="page"' : '') . '>' . $number . '</a> ';
    }
    return shop_template('pagination', ['label'=>site_escape(shop_label('pages')),'links'=>$html], $settings ?? shop_settings());
}

function shop_grid_html(string $base, array $index, array $settings, array $state, array $products, bool $list): string
{
    $html = '<div class="shop-grid' . ($list ? ' shop-grid-list' : '') . '">';
    $context = null;
    foreach ($products as $id => $offers) $html .= shop_product_html($base, $index, $settings, $state, (string) $id, $offers, $context, $list);
    return $html . '</div>';
}

function shop_cart_html(string $base, array $settings, array $index, array $state, bool $checkout): string
{
    $cart = $state['cart'] ?? [];
    if (!$cart) {
        $last = $state['last'] ?? '';
        if ($checkout && $last !== '') {
            $html = '<p role="status">' . site_escape(shop_label('order_received') . ' #' . substr($last, 0, 12)) . '</p>';
            $order = function_exists('shop_payment_options') ? shop_order($last) : null;
            $payments = $order ? shop_payment_options($order) : [];
            if (!$payments) return $html;
            $return = shop_endpoint($base, ['view'=>'checkout']);
            $html .= shop_form_start($base, 'payment', $state, $return)
                . '<input type="hidden" name="order_id" value="' . site_escape($last) . '">'
                . '<section class="shop-checkout-panel"><h2>' . site_escape(shop_label('payment_api')) . '</h2><div class="shop-checkout-payment">';
            foreach ($payments as $id => $module) {
                $html .= '<button type="submit" name="integration" value="' . site_escape($id) . '">' . site_escape($module['name']) . '</button>';
            }
            return $html . '</div></section></form>';
        }
        return '<p>' . site_escape(shop_label('empty_cart')) . '</p>';
    }
    $quote = shop_cart_quote($index, $cart, shop_cached_stock()['committed'], $settings);
    if ($checkout && $quote['valid'] && ($settings['guest'] || !empty($state['user']))) {
        return shop_template('checkout', ['title'=>site_escape(shop_label('checkout')),
            'body'=>shop_checkout_html($base, $settings, $state, $quote)], $settings);
    }
    $url = shop_endpoint($base, ['view'=>'cart']);
    $html = '<ul class="shop-cart-items">';
    foreach ($cart as $id => $qty) {
        if (isset($quote['lines'][$id])) {
            [$product, $offer] = $quote['lines'][$id];
            $label = $product['name'] . ' ' . $offer['variant'] . ' · ' . ((int) $offer['price_minor'] > 0
                ? shop_money((int) $offer['price_minor'], $settings) : shop_label('unavailable'));
        } else $label = (string) $id . ' · ' . shop_label('unavailable');
        $html .= '<li>' . site_escape($label) . ' ' . shop_form_start($base, 'quantity', $state, $url)
            . '<input type="hidden" name="offer" value="' . site_escape((string) $id) . '"><label>' . site_escape(shop_label('quantity'))
            . ' <input type="number" name="qty" min="0" max="999" value="' . (int) $qty . '"></label> <button>'
            . site_escape(shop_label('apply')) . '</button> <button name="remove" value="1">' . site_escape(shop_label('remove')) . '</button></form></li>';
    }
    $html .= '</ul><p><strong>' . site_escape(shop_label('total') . ' · ' . shop_money($quote['total'], $settings)) . '</strong></p>';
    if (!$quote['valid']) return $html . '<p>' . site_escape(shop_label('stock')) . '</p>';
    if (!$checkout) return $html . '<a class="button" href="' . site_escape(shop_endpoint($base, ['view'=>'checkout'])) . '">' . site_escape(shop_label('checkout')) . '</a>';
    return $html . '<a class="button" href="' . site_escape(site_url($base) . '?account=1') . '">' . site_escape(shop_label('login')) . '</a>';
}

function shop_checkout_html(string $base, array $settings, array $state, array $quote): string
{
    $user = $state['user'] ?? [];
    $draft = $state['checkoutDraft'] ?? [];
    $values = ($draft['values'] ?? []) + array_intersect_key($user, array_flip(['name','email','phone']));
    $errors = $draft['errors'] ?? [];
    $savedAddresses = preg_split('/\R/u', $user['addresses'] ?? '', -1, PREG_SPLIT_NO_EMPTY);
    if ($savedAddresses) $user['address'] = reset($savedAddresses);
    $label = static function (string $key): string { return site_escape(shop_label($key)); };
    $methods = shop_delivery_quote($settings, $quote['total']);
    $rules = shop_delivery_rules($settings);
    if (!$methods) {
        return '<p class="notice">' . $label('delivery_unavailable') . ' ' . $label('delivery_minimum') . ' · '
            . site_escape(shop_money($rules['minimum'], $settings)) . '</p><a class="button" href="'
            . site_escape(shop_endpoint($base, ['view'=>'cart'])) . '">' . $label('checkout_edit_cart') . '</a>';
    }
    $method = $draft['method'] ?? '';
    if (!isset($methods[$method])) $method = (string) key($methods);
    $isShipping = $method === 'shipping';
    $subtotal = site_escape(shop_money($quote['total'], $settings));
    $shippingFee = $methods['shipping'] ?? 0;
    $shipping = site_escape(shop_money($shippingFee, $settings));
    $shippingTotal = site_escape(shop_money($quote['total'] + $shippingFee, $settings));
    $html = '<div class="shop-checkout-layout">'
        . shop_form_start($base, 'order', $state, shop_endpoint($base, ['view'=>'checkout']))
        . '<input type="hidden" name="nonce" value="' . site_escape($state['order']) . '"><input type="hidden" name="quoted" value="' . $quote['total'] . '">'
        . '<input type="hidden" name="quoted_shipping" value="' . $shippingFee . '">'
        . '<div hidden><input type="text" name="website" tabindex="-1" autocomplete="off" aria-label="Website"></div>'
        . '<section class="shop-checkout-panel"><h2><i class="fa-regular fa-user" aria-hidden="true"></i> ' . $label('checkout_contact') . '</h2>'
        . '<div class="shop-checkout-contact">';
    foreach (shop_checkout_fields($settings) as $field) {
        $name = $field['name'];
        $html .= shop_checkout_field($field, $values[$name] ?? '', $errors[$name] ?? '');
    }
    $html .= '</div></section><section class="shop-checkout-panel"><h2><i class="fa-solid fa-truck" aria-hidden="true"></i> ' . $label('delivery') . '</h2>'
        . '<label>' . $label('delivery') . '<select name="method" data-shop-checkout-method>';
    foreach ($methods as $choice => $fee) {
        $html .= '<option value="' . $choice . '"' . ($method === $choice ? ' selected' : '') . '>'
            . $label($choice) . ($choice === 'shipping' ? ' · ' . $shipping : '') . '</option>';
    }
    $addressError = $errors['__address'] ?? '';
    $html .= '</select></label><label data-shop-checkout-address data-message-field>' . $label('record_delivery_address')
        . '<input name="address" maxlength="200" autocomplete="street-address" aria-describedby="shop-address-error"'
        . ($isShipping ? ' required' : '') . ($addressError === '' ? '' : ' aria-invalid="true"')
        . ($savedAddresses ? ' list="shop-saved-addresses"' : '') . ' value="' . site_escape($values['__address'] ?? $user['address'] ?? '') . '">'
        . '<small class="field-error" id="shop-address-error" data-field-error' . ($addressError === '' ? ' hidden' : '') . '>'
        . site_escape(site_translation($addressError, shop_locale())) . '</small></label>';
    if ($rules['pickupAddress'] !== '' && isset($methods['pickup'])) {
        $html .= '<p class="hint" data-shop-checkout-pickup>' . $label('delivery_pickup_address') . ' · ' . site_escape($rules['pickupAddress']) . '</p>';
    }
    if ($rules['shipping'] && !isset($methods['shipping'])) {
        $html .= '<p class="hint">' . $label('delivery_minimum') . ' · ' . site_escape(shop_money($rules['minimum'], $settings)) . '</p>';
    }
    if ($rules['notes'] !== '') $html .= '<details><summary>' . $label('delivery_rules') . '</summary><p>' . nl2br(site_escape($rules['notes']), false) . '</p></details>';
    if ($savedAddresses) {
        $html .= '<datalist id="shop-saved-addresses">';
        foreach ($savedAddresses as $address) $html .= '<option value="' . site_escape($address) . '">';
        $html .= '</datalist>';
    }
    $html .= '</section><button type="submit" class="button-primary shop-checkout-submit"><i class="fa-solid fa-check" aria-hidden="true"></i> '
        . $label('checkout') . '</button></form><aside class="shop-checkout-panel shop-checkout-summary">'
        . '<div class="shop-checkout-summary-heading"><h2><i class="fa-solid fa-bag-shopping" aria-hidden="true"></i> ' . $label('checkout_summary') . '</h2>'
        . '<a href="' . site_escape(shop_endpoint($base, ['view'=>'cart'])) . '">' . $label('checkout_edit_cart') . '</a></div>'
        . '<ul class="shop-checkout-items">';
    foreach ($quote['lines'] as [$product, $offer, $quantity]) {
        $image = shop_image($base, $product['image'], '');
        $price = (int) $offer['price_minor'];
        $html .= '<li><div class="shop-checkout-thumb">' . ($image ?: '<i class="fa-solid fa-box" aria-hidden="true"></i>') . '</div>'
            . '<div><strong>' . site_escape($product['name']) . '</strong>'
            . ($offer['variant'] === '' ? '' : '<small>' . site_escape($offer['variant']) . '</small>')
            . '<small>' . site_escape($quantity . ' × ' . shop_money($price, $settings)) . '</small></div>'
            . '<strong class="shop-checkout-line-total">' . site_escape(shop_money($price * $quantity, $settings)) . '</strong></li>';
    }
    return $html . '</ul><dl class="shop-checkout-totals"><div><dt>' . $label('checkout_subtotal') . '</dt><dd>' . $subtotal
        . '</dd></div><div data-shop-checkout-shipping' . ($isShipping ? '' : ' hidden') . '><dt>' . $label('shipping') . '</dt><dd>' . $shipping
        . '</dd></div><div class="shop-checkout-total"><dt>' . $label('total') . '</dt><dd data-shop-checkout-total data-pickup="' . $subtotal
        . '" data-shipping="' . $shippingTotal . '" aria-live="polite">' . ($isShipping ? $shippingTotal : $subtotal) . '</dd></div></dl></aside></div>';
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
    $states = shop_order_states();
    foreach (shop_orders() as $order) {
        if ($order['head'][3] !== $state['user']['id']) continue;
        $body .= '<li>' . site_escape(substr($order['id'], 0, 12) . ' · ' . $order['head'][1] . ' · '
            . shop_order_status_label($order['head'][2], $states) . ' · ' . shop_money((int) $order['head'][10], ['currency'=>$order['head'][11]])) . '</li>';
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
    if (shop_input($query, 'layout') !== 'list') unset($query['layout']);
    $brand = shop_input($query, 'b');
    if ($route['brand'] === '' && isset($index['brands'][$brand])) $route['brand'] = $brand;
    $filters = shop_filter_settings($settings);
    if ($filters['position'] === 'off' || !$filters['price']) unset($query['price_min'], $query['price_max']);
    if ($filters['position'] === 'off' || !$filters['fields']) unset($query['f'], $query['min'], $query['max']);
    if (!$filters['search']) unset($query['q']);
    if (!$filters['sort']) unset($query['sort']);
    $category = $params['category'] ?? ($route['category'] ?: shop_input($query, 'c'));
    if (isset($params['category']) && !isset($index['categories'][$category]) && !empty($context['shopRoute'])
        && $category === (site_design_info(site_design_active())['shopCategory'] ?? '')) {
        return '<p>' . site_escape(shop_label('no_products')) . '</p>';
    }
    $category = isset($index['categories'][$category]) ? (string) $category : '';
    $page = $context['page'] ?? '';
    $url = $context['shopUrl'] ?? ($page !== '' ? site_page_url($base, $page) : shop_catalog_url($base, $settings));
    if (isset($params['category'])) $query['c'] = $category;
    if ($tag === 'catalog') {
        if (!empty($context['shopDescription'])) return '';
        $catalogContext = $context;
        if (!isset($context['shopRoute'])) $catalogContext['shopCategoryPage'] = $url;
        $html = '<div class="shop-catalog" data-shop-catalog>'
            . ($category === '' ? '' : shop_component('breadcrumbs', $params, $catalogContext))
            . '<div class="shop-catalog-actions">';
        $categories = shop_component('categories', $params, $catalogContext);
        if ($categories !== '') {
            $html .= '<details class="shop-catalog-categories" data-shop-popover><summary><i class="fa-solid fa-layer-group" aria-hidden="true"></i> ' . site_escape(shop_label('categories')) . '</summary>'
                . '<div class="shop-category-panel">' . $categories . '</div></details>';
        }
        $controls = shop_component('filters', $params, $catalogContext);
        if ($filters['position'] === 'top') $html .= $controls;
        $html .= shop_component('basket', $params, $catalogContext) . '</div>';
        $html .= shop_brand_links($base, $index, $category, $route['brand'], $url, !isset($context['shopRoute']), !empty($context['shopEdit']));
        $tools = shop_component('search', $params, $catalogContext) . shop_component('sort', $params, $catalogContext)
            . shop_view_switch($url, $query);
        $listing = '<div class="shop-catalog-tools">' . $tools . '</div>'
            . shop_component('items', $params, $catalogContext);
        if ($controls !== '' && $filters['position'] === 'left') {
            return $html . '<div class="shop-catalog-layout"><aside aria-label="' . site_escape(shop_label('filters')) . '">'
                . $controls . '</aside><div data-shop-listing>' . $listing . '</div></div></div>';
        }
        return $html . '<div data-shop-listing>' . $listing . '</div></div>';
    }
    if (($tag === 'search' && !$filters['search']) || ($tag === 'sort' && !$filters['sort'])
        || ($tag === 'filters' && ($filters['position'] === 'off' || (!$filters['price'] && !$filters['fields'])))) return '';
    $categoryPage = $context['shopCategoryPage'] ?? '';
    if ($tag === 'categories') return shop_tree_html($base, $index, $settings, $category, $categoryPage);
    if ($tag === 'basket') return shop_template('basket', ['url'=>site_escape(shop_endpoint($base, ['view'=>'cart'])),
        'label'=>site_escape(shop_label('basket')), 'count'=>(string) array_sum($state['cart'] ?? [])], $settings);
    if ($tag === 'cart' || $tag === 'checkout') return shop_cart_html($base, $settings, $index, $state, $tag === 'checkout');
    if ($tag === 'account' || $tag === 'orders') return shop_account_html($base, $state, $settings, $tag === 'orders');
    if ($tag === 'product') {
        if (!empty($context['shopDescription'])) return '';
        $id = (string) ($params['id'] ?? $route['product']);
        $markup = shop_product_html($base, $index, $settings, $state, $id, null, $context);
        if ($markup === '' || empty($context['shopEdit'])) return $markup;
        return '<div class="layout-edit-outline shop-edit-region" data-shop-edit-region><div class="layout-edit-actions shop-edit-product-actions">'
            . shop_live_editor_link($base, 'product', $id, $id === $route['product'])
            . shop_live_editor_link($base, 'seo', $id, $id === $route['product'])
            . '</div>' . $markup . '</div>';
    }
    if ($tag === 'breadcrumbs') {
        $links = ['<a href="' . site_escape(shop_section_link($base, $index, '', $categoryPage)) . '">' . site_escape(shop_label('catalog')) . '</a>'];
        foreach ($index['tree'][$category] ?? [] as $id) {
            $links[] = '<a href="' . site_escape(shop_section_link($base, $index, (string) $id, $categoryPage)) . '">' . site_escape($index['categories'][$id]['name']) . '</a>';
        }
        $editable = !empty($context['shopEdit']) && $category !== '';
        $edit = $editable ? '<div class="layout-edit-actions">' . shop_live_editor_link($base, 'category', $category, $route['product'] === '') . '</div>' : '';
        return '<nav class="shop-crumbs' . ($editable ? ' layout-edit-outline shop-edit-region" data-shop-edit-region' : '"')
            . '>' . implode(' / ', $links) . $edit . '</nav>';
    }
    $size = max(1, min(96, (int) ($params['limit'] ?? $settings['pageSize'])));
    $key = hash('sha256', serialize([$category, $route['brand'], $query, $size]));
    if (!isset($context['shopLists'][$key])) $context['shopLists'][$key] = shop_listing($index, $category, $route['brand'], $query, $size, $settings);
    $list = $context['shopLists'][$key];
    if ($tag === 'pages') return shop_pagination($url, $list, $query, 'p', '', $settings);
    if ($tag !== 'items') {
        if ($tag !== 'filters') return shop_controls($tag, $url, $list, $query);
        $options = shop_filter_options($index, shop_scoped_offers($index, $category, $route['brand']), $list['fields'], $filters, $settings);
        if (!$options['prices'] && !$options['fields'] && !array_intersect_key($query, array_flip(['f','min','max','price_min','price_max']))) return '';
        return shop_controls($tag, $url, $list, $query, $options, $filters['live']);
    }
    if (!$list['products']) return '<p>' . site_escape(shop_label('no_products')) . '</p>';
    return shop_grid_html($base, $index, $settings, $state, $list['products'], ($query['layout'] ?? '') === 'list') . shop_pagination($url, $list, $query, 'p', '', $settings);
}

function shop_brand_mark(string $base, string $name, string $logo, bool $fallback = true): string
{
    if (shop_image_name($logo) && is_file(SHOP_IMAGES_DIR . '/' . $logo) && !is_link(SHOP_IMAGES_DIR . '/' . $logo)) {
        return '<span class="shop-brand-mark shop-brand-logo">' . shop_image($base, $logo, '') . '</span>';
    }
    if (!$fallback) return '';
    preg_match('/\A\X/u', $name, $initial);
    return '<span class="shop-brand-mark" aria-hidden="true">' . site_escape($initial[0] ?? '') . '</span>';
}

function shop_brand_links(string $base, array $index, string $category, string $selected, string $url, bool $embedded, bool $editable = false): string
{
    $group = $category === '' || $index['categories'][$category]['descendants'] === '1' ? 'lists' : 'local';
    $members = array_fill_keys($index[$group][$category] ?? [], true);
    $brands = [];
    foreach ($index['brands'] as $slug => $values) {
        foreach ($values as $name => $enabled) {
            foreach ($index['lookup']['brand'][$name] ?? [] as $offer) {
                if (!isset($members[$offer])) continue;
                $brands[$slug] = (string) $name;
                break 2;
            }
        }
    }
    if (!$brands) return '';
    $profiles = shop_brand_profiles();
    if ($selected !== '') {
        if (!isset($brands[$selected])) return '';
        $profile = $profiles[$selected] ?? [];
        $name = $profile['name'] ?? $brands[$selected];
        $website = $profile['website'] ?? '';
        $edit = $editable ? '<div class="layout-edit-actions">' . shop_live_editor_link($base, 'brand', $selected) . '</div>' : '';
        $editorAttributes = $editable ? ' layout-edit-outline shop-edit-region" data-shop-edit-region' : '"';
        return '<section class="shop-brand-feature' . $editorAttributes . ' data-shop-brand="' . site_escape($selected) . '">' . shop_brand_mark($base, $name, $profile['logo'] ?? '') . '<div><h2>'
            . site_escape($name) . '</h2>' . (empty($profile['description']) ? '' : '<p>' . nl2br(site_escape($profile['description'])) . '</p>')
            . ($website === '' ? '' : '<a href="' . site_escape($website) . '">' . site_escape(shop_label('brand_website')) . '</a>')
            . '</div>' . $edit . '</section>';
    }
    $html = '<section class="shop-brands"><h2>' . site_escape(shop_label('brands')) . '</h2><div class="shop-brand-grid">';
    foreach ($brands as $slug => $name) {
        $profile = $profiles[$slug] ?? [];
        $name = $profile['name'] ?? $name;
        $href = $url . '?' . http_build_query(array_filter(['c'=>$embedded ? $category : '', 'b'=>$slug]), '', '&', PHP_QUERY_RFC3986);
        $html .= '<a class="shop-brand-card" href="' . site_escape($href) . '">' . shop_brand_mark($base, $name, $profile['logo'] ?? '', false)
            . '<strong>' . site_escape($name) . '</strong></a>';
    }
    return $html . '</div></section>';
}
