<?php
if (!defined('SITE_ROOT')) { http_response_code(404); exit; }

function shop_admin_category_change(array &$tables, array $input, bool $delete): void
{
    $id = shop_input($input, 'id');
    $categories = array_column($tables['categories'], null, 'id');
    if ($id !== '' && !isset($categories[$id])) throw shop_error('not_found');
    if ($delete) {
        if ($id === '' || shop_input($input, 'confirm') !== '1') throw shop_error('not_found');
        foreach (['categories'=>'parent_id','products'=>'category_id','placements'=>'category_id','fields'=>'category_id'] as $table => $key) {
            if (in_array($id, array_column($tables[$table], $key), true)) throw shop_error('category_not_empty');
        }
        unset($categories[$id]);
        $tables['categories'] = array_values($categories);
        $media = array_column($tables['category_media'], null, 'category_id');
        unset($media[$id]);
        $tables['category_media'] = array_values($media);
        return;
    }
    $parent = shop_input($input, 'parent_id');
    $name = shop_input($input, 'name');
    $slug = shop_input($input, 'slug');
    $sort = shop_input($input, 'sort');
    $description = shop_input($input, 'description');
    if (($parent !== '' && !isset($categories[$parent])) || $name === '' || strlen($name) > 240
        || preg_match('/[\x00-\x1f\x7f]/u', $name) !== 0 || strlen($description) > 5000
        || preg_match('//u', $description) !== 1 || strpos($description, '<?') !== false
        || !preg_match('/\A-?[0-9]{1,8}\z/', $sort)) {
        throw shop_error('row', shop_label('categories'));
    }
    $siblings = [];
    foreach ($categories as $category) {
        if ($category['parent_id'] === $parent && $category['id'] !== $id) $siblings[$category['slug']] = true;
    }
    if ($slug === '') {
        $base = shop_slug($name) ?: 'section';
        $slug = $base;
        for ($number = 2; isset($siblings[$slug]); ++$number) {
            $suffix = '-' . $number;
            $slug = substr($base, 0, 64 - strlen($suffix)) . $suffix;
        }
    }
    if (!shop_id($slug) || isset($siblings[$slug])) throw shop_error('category_slug_used');
    if ($id === '') $id = 'category-' . bin2hex(random_bytes(12));
    $categories[$id] = ['id'=>$id,'parent_id'=>$parent,'slug'=>$slug,'name'=>$name,'sort'=>$sort,
        'descendants'=>shop_input($input, 'descendants') === '1' ? '1' : '0','description'=>$description];
    $tables['categories'] = array_values($categories);
    if (isset($input['visual_type'])) {
        $type = shop_input($input, 'visual_type');
        $media = array_column($tables['category_media'], null, 'category_id');
        if ($type === '' || $type === 'none') {
            unset($media[$id]);
        } else {
            $value = $type === 'icon' ? shop_input($input, 'icon') : shop_input($input, 'image');
            if ($type === 'image' && (!shop_image_name($value) || !is_file(SHOP_IMAGES_DIR . '/' . $value) || is_link(SHOP_IMAGES_DIR . '/' . $value))) {
                throw shop_error('photo_invalid');
            }
            $media[$id] = ['category_id'=>$id,'type'=>$type,'value'=>$value];
        }
        $tables['category_media'] = array_values($media);
    }
}

function shop_admin_category_move(array &$tables, array $input): void
{
    $id = shop_input($input, 'id');
    $anchor = shop_input($input, 'target');
    $position = shop_input($input, 'position');
    $categories = array_column($tables['categories'], null, 'id');
    if ($id === $anchor || !isset($categories[$id], $categories[$anchor])
        || !in_array($position, ['before','after','inside'], true)) throw shop_error('row', shop_label('categories'));
    $parent = $position === 'inside' ? $anchor : $categories[$anchor]['parent_id'];
    for ($path = $parent; $path !== ''; $path = $categories[$path]['parent_id']) {
        if ($path === $id) throw shop_error('tree', shop_label('categories'));
    }
    $categories[$id]['parent_id'] = $parent;
    $siblings = array_keys(array_filter($categories, static function (array $row) use ($parent, $id): bool {
        return $row['parent_id'] === $parent && $row['id'] !== $id;
    }));
    usort($siblings, static function ($a, $b) use ($categories): int {
        return ((int) $categories[$a]['sort'] <=> (int) $categories[$b]['sort']) ?: strcmp((string) $a, (string) $b);
    });
    $offset = $position === 'inside' ? count($siblings) : array_search($anchor, array_map('strval', $siblings), true);
    array_splice($siblings, $offset + ($position === 'after' ? 1 : 0), 0, [$id]);
    foreach ($siblings as $index => $key) $categories[$key]['sort'] = (string) ($index * 10);
    $tables['categories'] = array_values($categories);
}
