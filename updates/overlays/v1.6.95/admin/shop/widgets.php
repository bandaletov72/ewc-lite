<?php
if (!defined('SITE_ROOT')) { http_response_code(404); exit; }
require_once __DIR__ . '/module.php';

function shop_widget_options(string $category): array
{
    $index = shop_index();
    if ($category !== '' && !isset($index['categories'][$category])) return ['sorts'=>[], 'filters'=>[]];
    $list = shop_list_options($index, $category, []);
    $choices = shop_filter_options($index, shop_scoped_offers($index, $category, ''), $list['fields'],
        ['price'=>false, 'fields'=>true], shop_settings())['fields'];
    $filters = [];
    foreach ($choices as $id => $values) {
        $field = $list['fields'][$id];
        $type = in_array($field['type'], ['number','price'], true) ? 'number' : 'choice';
        $options = [];
        if ($type === 'choice') {
            foreach (array_keys($values) as $value) {
                $options[] = [(string) $value, shop_field_type($field['type']) === 'bool'
                    ? shop_label((string) $value === '1' ? 'yes' : 'no') : (string) $value];
            }
        }
        $filters[] = ['id'=>(string) $id,'name'=>shop_field_label($field),'type'=>$type,'options'=>$options];
    }
    return ['sorts'=>$list['sorts'],'filters'=>$filters];
}

function shop_widget_params(string $args): array
{
    preg_match_all('/([a-z][a-z0-9_-]*)=(?:"([^"]*)"|([a-z0-9_.%-]+))/i', $args, $matches, PREG_SET_ORDER);
    $params = [];
    $query = [];
    foreach ($matches as $match) {
        $name = strtolower($match[1]);
        $value = rawurldecode($match[3] ?? $match[2]);
        if ($value === '' || strlen($value) > 500 || preg_match('/[\p{C}]/u', $value) !== 0) continue;
        if (preg_match('/\A(f|min|max)-([a-z0-9][a-z0-9_-]{0,63})\z/', $name, $key)) {
            if ($key[1] === 'f') $query['f'][$key[2]][] = $value;
            elseif (is_numeric($value)) $query[$key[1]][$key[2]] = $value;
        } elseif (in_array($name, ['q','sort','category','layout','limit','mobile','desktop'], true)) {
            $params[$name] = $value;
        }
    }
    return [$params, $query];
}

function shop_widget_render(string $tag, string $args, array &$context): string
{
    [$params, $query] = shop_widget_params($args);
    $base = $context['base'];
    $index = shop_index();
    $settings = shop_settings();
    $page = $context['page'] ?? 'index.php';
    $sequence = ($context['shopWidgetCount'] ?? 0) + 1;
    $context['shopWidgetCount'] = $sequence;
    $block = 'shop-widget-' . $sequence;
    $parameter = 'shop_page_' . $sequence;
    $category = $params['category'] ?? '';
    if ($category !== '' && !isset($index['categories'][$category])) {
        return '<p>' . site_escape(shop_label('no_products')) . '</p>';
    }
    if ($tag === 'categories') return shop_widget_categories($base, $index, $category, ($params['layout'] ?? '') === 'list');
    $size = max(1, min(96, (int) ($params['limit'] ?? $settings['pageSize'])));
    $query += ['q'=>$params['q'] ?? '', 'sort'=>$params['sort'] ?? '', 'p'=>shop_input($_GET, $parameter)];
    $list = shop_listing($index, $category, '', $query, $size, $settings);
    if (!$list['products']) return '<p>' . site_escape(shop_label('no_products')) . '</p>';
    $target = $context['shopUrl'] ?? site_page_url($base, $page !== '' ? $page : 'index.php');
    $pageQuery = array_filter($_GET, static function ($value, $name): bool {
        return is_string($name) && is_string($value)
            && ($name === 'lang' || preg_match('/\Ashop_page_[1-9][0-9]{0,5}\z/', $name));
    }, ARRAY_FILTER_USE_BOTH) + array_intersect_key($_GET, array_flip(array_merge(SHOP_CATALOG_QUERY, ['p'])));
    $html = $tag === 'carousel' ? shop_widget_carousel($base, $index, $settings, $list['products'], $params, $block)
        : shop_grid_html($base, $index, $settings, shop_session($base), $list['products'], ($params['layout'] ?? '') === 'list');
    return '<div class="shop-catalog shop-widget" id="' . $block . '">' . $html
        . shop_pagination($target, $list, $pageQuery, $parameter, '#' . $block, $settings) . '</div>';
}

function shop_widget_categories(string $base, array $index, string $parent, bool $list): string
{
    $children = $index['children'][$parent] ?? [];
    if (!$children) return '';
    $html = '<ul class="shop-widget-categories' . ($list ? ' shop-widget-categories-list' : '') . '">';
    foreach ($children as $id) {
        $category = $index['categories'][$id];
        $media = $index['categoryMedia'][$id] ?? null;
        if ($media && $media['type'] === 'image' && !is_file(SHOP_IMAGES_DIR . '/' . $media['value'])) $media = null;
        if ($media === null && isset($index['categoryFallbacks'][$id])) {
            $media = ['type'=>'image', 'value'=>$index['categoryFallbacks'][$id]];
        }
        $visual = shop_category_visual($base, $media);
        $html .= '<li><a class="shop-category-card" href="' . site_escape(shop_category_url($base, $index, (string) $id)) . '">'
            . '<span class="shop-category-card-visual" aria-hidden="true"' . (($media['type'] ?? '') === 'image' ? ' data-category-photo' : '') . '>'
            . ($visual === '' ? '<i class="fa-regular fa-folder" aria-hidden="true"></i>' : $visual) . '</span>'
            . '<strong>' . site_escape($category['name']) . '</strong></a></li>';
    }
    return $html . '</ul>';
}

function shop_widget_carousel(string $base, array $index, array $settings, array $products, array $params, string $block): string
{
    $desktop = (int) ($params['desktop'] ?? 0);
    $mobile = (int) ($params['mobile'] ?? 0);
    $desktop = $desktop >= 1 && $desktop <= 6 ? (string) $desktop : 'auto';
    $mobile = $mobile >= 1 && $mobile <= 4 ? (string) $mobile : 'auto';
    $label = site_escape(shop_label('widget_carousel'));
    $track = $block . '-track';
    $html = '<section class="shop-widget-carousel" aria-label="' . $label . '" data-shop-carousel data-desktop="' . $desktop
        . '" data-mobile="' . $mobile . '"><div class="shop-carousel-controls" data-shop-carousel-controls hidden>';
    foreach ([-1=>'gallery_prev', 1=>'gallery_next'] as $direction => $key) {
        $html .= '<button type="button" data-shop-carousel-step="' . $direction . '" aria-controls="' . $track . '" aria-label="'
            . site_escape(shop_label($key)) . '"><i class="fa-solid fa-chevron-' . ($direction === -1 ? 'left' : 'right')
            . '" aria-hidden="true"></i></button>';
    }
    $html .= '</div><ul class="shop-carousel-track" id="' . $track . '" tabindex="0">';
    foreach ($products as $id => $offers) {
        $product = $index['products'][$id];
        $name = site_escape($product['name']);
        $url = site_escape(shop_product_url($base, $index, $product));
        $photo = ($index['offerMedia'][$offers[0]] ?? '') ?: ($product['image'] ?: ($index['images'][$id][0]['image'] ?? ''));
        $prices = array_map(static function ($offer) use ($index): int { return (int) $index['offers'][$offer]['price_minor']; }, $offers);
        $html .= '<li class="shop-carousel-card"><a href="' . $url . '">' . shop_image($base, $photo, '')
            . '<span>' . $name . '</span></a><strong>' . site_escape(shop_price_range($prices, $settings)) . '</strong></li>';
    }
    return $html . '</ul></section>';
}
