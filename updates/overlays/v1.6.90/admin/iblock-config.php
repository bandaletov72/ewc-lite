<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}

function admin_iblock_shortcode(string $name): string
{
    $view = $name === 'chart-demo' ? 'charts' : ($name === 'kontur-works' ? 'portfolio' : 'table');
    return '[iblock=' . $view . ']/data/' . $name . '.txt[iblock]';
}

function admin_create_iblock_file(string $name, string $view): array
{
    $id = preg_replace('/\.txt\z/i', '', trim($name));
    $layouts = [
        'portfolio' => ['title' => ['Название', 'text'], 'category' => ['Раздел', 'text'],
            'description' => ['Описание', 'textarea'], 'images' => ['Фотографии через запятую', 'text'], 'url' => ['Ссылка', 'url']],
        'reviews' => ['author' => ['Имя', 'text'], 'text' => ['Текст', 'textarea'],
            'rating' => ['Оценка', 'number'], 'published' => ['Опубликовано', 'bool']],
        'phones' => ['name' => ['Название', 'text'], 'phone' => ['Телефон', 'tel']],
        'charts' => ['label' => ['Подпись', 'text'], 'value' => ['Значение', 'number']],
    ];
    if (!isset(site_iblock_views()[$view])) {
        throw new RuntimeException('Не удалось выполнить действие.');
    }
    $columns = [];
    foreach ($layouts[$view] ?? ['title' => ['Название', 'text'], 'description' => ['Описание', 'textarea']] as $key => list($label, $type)) {
        $columns[] = ['key' => $key, 'label' => admin_t($label), 'type' => $type];
    }
    admin_save_iblock($id, ['columns' => $columns, 'rows' => []], '');
    return ['id' => $id, 'path' => '/data/' . $id . '.txt', 'columns' => $columns];
}

function admin_iblock_draft(array $posted): array
{
    $columns = [];
    $keys = [];
    foreach (is_array($posted['columns'] ?? null) ? $posted['columns'] : [] as $index => $column) {
        if (!is_array($column)) {
            continue;
        }
        $keys[] = $index;
        $columns[] = [
            'key' => admin_input($column, 'key'),
            'label' => trim(admin_input($column, 'label')),
            'type' => admin_input($column, 'type'),
        ];
    }
    $rows = [];
    foreach (is_array($posted['rows'] ?? null) ? $posted['rows'] : [] as $row) {
        if (!is_array($row)) {
            continue;
        }
        $values = [];
        foreach ($keys as $index) {
            $values[] = admin_input($row, (string) $index);
        }
        $rows[] = $values;
    }
    return ['columns' => $columns, 'rows' => $rows];
}

function admin_validate_cell(string $value, string $type): bool
{
    return site_iblock_validate_cell($value, $type);
}

function admin_save_iblock(string $id, array $block, string $revision): string
{
    if (!site_valid_iblock_name($id)) {
        if (preg_match('/\A' . SITE_IDENTIFIER_PATTERN . '\z/', $id) === 1) {
            throw new RuntimeException('Это имя зарезервировано системой. Укажите другое имя инфоблока.');
        }
        throw new RuntimeException('Имя инфоблока должно содержать от 1 до 64 символов A–Z, a–z, 0–9, дефисов или подчёркиваний. Без пробелов.');
    }
    if (!$block['columns']) {
        throw new RuntimeException('Добавьте хотя бы один столбец.');
    }

    $types = site_iblock_types();
    $used = [];
    foreach ($block['columns'] as $column) {
        if (!preg_match('/\A' . SITE_IDENTIFIER_PATTERN . '\z/', $column['key'])) {
            throw new RuntimeException('Ключ столбца должен содержать от 1 до 64 символов A–Z, a–z, 0–9, дефисов или подчёркиваний. Без пробелов.');
        }
        if (isset($used[$column['key']])) {
            throw new RuntimeException('Ключи столбцов должны быть уникальными.');
        }
        if (!admin_valid_form_label($column['label']) || !isset($types[$column['type']])) {
            throw new RuntimeException('Укажите название и тип каждого столбца.');
        }
        $used[$column['key']] = true;
    }
    foreach ($block['rows'] as $rowIndex => &$row) {
        foreach ($block['columns'] as $index => $column) {
            $value = $column['type'] === 'textarea'
                ? str_replace(["\r\n", "\r"], "\n", $row[$index])
                : trim($row[$index]);
            if (!admin_validate_cell($value, $column['type'])) {
                throw new RuntimeException('Проверьте значение. Строка ' . ($rowIndex + 1) . ', столбец «' . $column['label'] . '».');
            }
            $row[$index] = $column['type'] === 'price' ? str_replace(',', '.', $value) : $value;
        }
    }
    unset($row);

    $lock = admin_lock();
    try {
        $file = site_iblock_file($id);
        $current = site_read_iblock($id);
        $expected = file_exists($file) ? admin_settings_revision($current) : '';
        if (!hash_equals($expected, $revision)) {
            throw new RuntimeException('Инфоблок уже существует или был изменён. Скопируйте правки и откройте его заново.');
        }
        $directory = dirname($file);
        if (!is_dir($directory) && !@mkdir($directory, 0775) && !is_dir($directory)) {
            throw new RuntimeException('Не удалось записать файл. Проверьте права записи в его каталог.');
        }
        admin_write_payload($file, site_iblock_csv($block), SITE_IBLOCK_LIMIT);
        return admin_settings_revision($block);
    } finally {
        fclose($lock);
    }
}
