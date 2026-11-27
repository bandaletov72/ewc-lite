<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}

const SITE_DATA_DIR = SITE_ROOT . '/data';
const SITE_IBLOCK_LIMIT = 524288;

function site_iblock_types(bool $includeTableTypes = true): array
{
    return ['text' => 'Текст', 'textarea' => 'Многострочный текст', 'number' => 'Число', 'price' => 'Цена',
        'date' => 'Дата', 'tel' => 'Телефон', 'email' => 'Email', 'url' => 'Ссылка',
        'video' => site_video_labels($GLOBALS['adminLanguage'] ?? 'ru')[0], 'bool' => 'Да/нет']
        + ($includeTableTypes ? ['hidden' => 'Скрытый тег', 'form' => 'Кнопка вызова формы'] : []);
}

function site_valid_iblock_name(string $id): bool
{
    return site_valid_part($id);
}

function site_iblock_file(string $id): string
{
    return site_content_data_directory() . '/' . $id . '.txt.php';
}

function site_iblock_names(): array
{
    $names = [];
    foreach (glob(site_content_data_directory() . '/*.txt.php') ?: [] as $file) {
        $name = basename($file, '.txt.php');
        if (site_valid_iblock_name($name) && is_file($file) && !is_link($file)) $names[] = $name;
    }
    sort($names, SORT_NATURAL | SORT_FLAG_CASE);
    return $names;
}

function site_read_iblock(string $id): array
{
    if (!site_valid_iblock_name($id)) {
        return [];
    }
    return site_read_iblock_file(site_iblock_file($id));
}

function site_read_iblock_file(string $file): array
{
    $payload = site_read_payload($file, SITE_IBLOCK_LIMIT);
    if ($payload === null) {
        return [];
    }
    if (substr(ltrim($payload), 0, 1) === '{') {
        $legacy = json_decode($payload, true);
        if (!is_array($legacy) || !isset($legacy['columns'], $legacy['rows'])) {
            throw new RuntimeException('Не удалось прочитать старый формат инфоблока.');
        }
        return $legacy;
    }
    return site_iblock_from_csv($payload);
}

function site_iblock_csv(array $block): string
{
    $rows = [];
    foreach (['key', 'label', 'type'] as $field) {
        $rows[] = array_column($block['columns'], $field);
    }
    $encoded = [];
    foreach (array_merge($rows, $block['rows']) as $row) {
        $cells = array_map(static function (string $value): string {
            return $value === '' || strpbrk($value, "|\"\r\n") !== false
                ? '"' . str_replace('"', '""', $value) . '"' : $value;
        }, $row);
        $encoded[] = implode('|', $cells);
    }
    return implode("\n", $encoded) . "\n";
}

function site_iblock_from_csv(string $text): array
{
    if (strlen($text) > SITE_IBLOCK_LIMIT || preg_match('//u', $text) !== 1 || strpos($text, "\0") !== false) {
        throw new RuntimeException('Используйте CSV в UTF-8 размером до 512 КиБ.');
    }
    if (substr($text, 0, 3) === "\xEF\xBB\xBF") {
        $text = substr($text, 3);
    }

    $stream = fopen('php://temp', 'w+b');
    try {
        fwrite($stream, $text);
        rewind($stream);
        $headers = [];
        foreach (['key', 'label', 'type'] as $field) {
            $row = fgetcsv($stream, 0, '|', '"', "\0");
            if ($row === false || $row === [null]) {
                throw new RuntimeException('В первых трёх строках CSV укажите ключи, названия и типы столбцов.');
            }
            $headers[$field] = $row;
        }
        $width = count($headers['key']);
        if (count($headers['label']) !== $width || count($headers['type']) !== $width) {
            throw new RuntimeException('Ключи, названия и типы должны содержать одинаковое число столбцов.');
        }
        $block = ['columns' => [], 'rows' => []];
        foreach ($headers['key'] as $index => $key) {
            $block['columns'][] = ['key' => $key, 'label' => trim($headers['label'][$index]),
                'type' => trim($headers['type'][$index])];
        }
        $rowNumber = 3;
        while (($row = fgetcsv($stream, 0, '|', '"', "\0")) !== false) {
            $rowNumber++;
            if ($row === [null] && $width > 1) {
                continue;
            }
            if (count($row) !== $width) {
                throw new RuntimeException('Запись ' . $rowNumber . ' содержит неверное число полей. Используйте разделитель |.');
            }
            $block['rows'][] = array_map(static function ($value): string {
                return (string) $value;
            }, $row);
        }
        return $block;
    } finally {
        fclose($stream);
    }
}

function site_iblock_id_from_argument(string $argument): string
{
    return preg_match('~\A/data/(' . SITE_IDENTIFIER_PATTERN . ')\.txt\z~', trim($argument), $match)
        && site_valid_iblock_name($match[1]) ? $match[1] : '';
}

function site_iblock_views(): array
{
    static $views;
    if ($views === null) {
        $views = [];
        $labels = ['table' => 'Таблица', 'charts' => 'График', 'phones' => 'Телефоны', 'portfolio' => 'Портфолио', 'reviews' => 'Отзывы'];
        foreach (['/templates/iblock/', '/templates/local/iblock/'] as $directory) {
            foreach (glob(SITE_ROOT . $directory . '*.php') ?: [] as $file) {
                $key = basename($file, '.php');
                if (preg_match('/\A[a-z][a-z0-9_-]{0,63}\z/', $key) && realpath($file) === str_replace('/', DIRECTORY_SEPARATOR, $file)) {
                    $views[$key] = $labels[$key] ?? $key;
                }
            }
        }
        $views = array_intersect_key($labels, $views) + $views;
    }
    return $views;
}

function site_iblock_public(array $block, bool $showForms = false): array
{
    if (!$block) return [];
    $columns = array_filter($block['columns'], static function (array $column) use ($showForms): bool {
        return $column['type'] !== 'hidden' && ($showForms || $column['type'] !== 'form');
    });
    if (!$columns) return [];
    if (count($columns) === count($block['columns'])) return $block;

    $block['columns'] = array_values($columns);
    foreach ($block['rows'] as &$row) {
        $values = [];
        foreach ($columns as $position => $column) {
            $values[] = $row[$position] ?? '';
        }
        $row = $values;
    }
    unset($row);
    return $block;
}

function site_iblock_tags(array $block): array
{
    $positions = array_keys(array_filter($block['columns'], static function (array $column): bool {
        return $column['type'] === 'hidden';
    }));
    if (!$positions) return [[], []];

    $labels = [];
    $tagIds = [];
    $rowTags = [];
    $unicodeLower = function_exists('mb_strtolower');
    foreach ($block['rows'] as $rowIndex => $row) {
        foreach ($positions as $position) {
            foreach (explode(',', $row[$position] ?? '') as $value) {
                $label = trim(ltrim(trim($value), '#'));
                if ($label === '') continue;
                $key = $unicodeLower ? mb_strtolower($label, 'UTF-8') : strtolower($label);
                if (!isset($tagIds[$key])) {
                    $tagIds[$key] = count($labels) + 1;
                    $labels[$tagIds[$key]] = $label;
                }
                $rowTags[$rowIndex][$tagIds[$key]] = $tagIds[$key];
            }
        }
    }
    return [$labels, $rowTags];
}

function site_component_iblock(string $template, string $argument, array $options = [], string $locale = 'ru', string $base = '', ?array &$iblockContext = null): string
{
    $id = site_iblock_id_from_argument($argument);
    if ($id === '') {
        return '';
    }

    $view = site_template_file('iblock/' . $template . '.php');
    if (realpath($view) !== str_replace('/', DIRECTORY_SEPARATOR, $view) || !is_file($view)) {
        return '';
    }
    try {
        $block = site_read_iblock($id);
    } catch (RuntimeException $exception) {
        return '';
    }
    if (!isset($block['columns'], $block['rows']) || !$block['rows']) {
        return '';
    }

    $hasTable = in_array($template, ['table', 'charts'], true);
    list($tagLabels, $rowTags) = $hasTable ? site_iblock_tags($block) : [[], []];
    $block = site_iblock_public($block, $hasTable && isset($iblockContext['formSession']['token']));
    if (!$block) return '';

    ob_start();
    require $view;
    return (string) ob_get_clean();
}

function site_iblock_chart_types(): array
{
    return ['bar', 'horizontal', 'line', 'pie', 'doughnut', 'radar', 'polarArea'];
}

function site_iblock_chart_text(string $lang): array
{
    static $labels;
    if ($labels === null) {
        $labels = require __DIR__ . '/help-iblock-charts.php';
    }
    return array_combine(['table', 'chart', 'block', 'type', 'labels', 'values', 'title', 'required', 'enable', 'types', 'help'],
        $labels[$lang] ?? $labels['ru']);
}

function site_iblock_chart_config(array $block, array $options): array
{
    $block = site_iblock_public($block);
    if (!$block) return [];
    $type = $options['type'] ?? 'bar';
    if (!in_array($type, site_iblock_chart_types(), true)) {
        $type = 'bar';
    }
    $columns = array_column($block['columns'], null, 'key');
    $positions = array_flip(array_keys($columns));
    $numeric = array_filter($columns, static function (array $column): bool {
        return in_array($column['type'], ['number', 'price'], true);
    });
    $categories = array_diff_key($columns, $numeric);
    $category = $options['labels'] ?? key($categories) ?? key($columns);
    $categoryPosition = $positions[$category] ?? reset($positions);
    $series = isset($options['values']) ? array_intersect(explode(',', $options['values']), array_keys($numeric)) : array_keys($numeric);
    $round = in_array($type, ['pie', 'doughnut', 'polarArea'], true);
    $series = array_slice(array_unique($series), 0, $round ? 1 : 8);
    $labels = [];
    foreach ($block['rows'] as $index => $row) {
        $labels[] = (string) ($row[$categoryPosition] ?? ($index + 1));
    }
    $datasets = [];
    foreach ($series as $key) {
        $position = $positions[$key];
        $values = [];
        foreach ($block['rows'] as $row) {
            $raw = str_replace(',', '.', trim((string) ($row[$position] ?? '')));
            $number = is_numeric($raw) ? (float) $raw : null;
            $values[] = $number !== null && is_finite($number) && (!$round || $number >= 0) ? $number : null;
        }
        if (count(array_filter($values, static function ($value): bool { return $value !== null; })) > 0) {
            $datasets[] = ['label' => $columns[$key]['label'], 'data' => $values];
        }
    }
    return $datasets ? ['type' => $type, 'labels' => $labels, 'datasets' => $datasets] : [];
}

function site_iblock_validate_cell(string $value, string $type): bool
{
    if ($value === '') {
        return true;
    }
    if (strlen($value) > 12000 || preg_match('//u', $value) !== 1
        || preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', $value)
        || ($type !== 'textarea' && preg_match('/[\r\n]/', $value))) {
        return false;
    }

    switch ($type) {
        case 'number':
            return preg_match('/\A[+-]?(?:[0-9]+(?:[.,][0-9]+)?|[.,][0-9]+)\z/', $value) === 1;
        case 'price':
            return preg_match('/\A[0-9]{1,15}(?:[.,][0-9]{1,2})?\z/', $value) === 1;
        case 'date':
            return preg_match('/\A([0-9]{4})-([0-9]{2})-([0-9]{2})\z/', $value, $date) === 1
                && checkdate((int) $date[2], (int) $date[3], (int) $date[1]);
        case 'tel':
            return site_valid_phone($value);
        case 'email':
            return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
        case 'url':
            return preg_match('~\Ahttps?://~i', $value) === 1 && filter_var($value, FILTER_VALIDATE_URL) !== false;
        case 'video':
            return site_video_source($value) !== null;
        case 'form':
            return site_valid_form_id($value);
        case 'bool':
            return $value === '0' || $value === '1';
    }
    return true;
}

function site_iblock_cell(string $value, string $type): string
{
    if ($value === '' || in_array($type, ['hidden', 'form'], true)) {
        return '';
    }
    if ($type === 'video') return site_video_button($value);
    if ($type === 'price' && preg_match('/\A[0-9]{1,15}(?:[.,][0-9]{1,2})?\z/', $value)) {
        return site_escape(site_format_price($value));
    }
    if ($type === 'number' && preg_match('/\A[+-]?(?:[0-9]+(?:[.,][0-9]+)?|[.,][0-9]+)\z/', $value)) {
        return site_escape(site_format_number($value));
    }
    if ($type === 'date' && preg_match('/\A[0-9]{4}-[0-9]{2}-[0-9]{2}\z/', $value)) {
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
        if ($date !== false && $date->format('Y-m-d') === $value) {
            return site_escape($date->format(site_region_settings()['date']));
        }
    }

    $href = '';
    if ($type === 'tel' && site_valid_phone($value)) {
        $href = 'tel:' . preg_replace('/[^0-9+]/', '', $value);
    } elseif ($type === 'email' && filter_var($value, FILTER_VALIDATE_EMAIL)) {
        $href = 'mailto:' . str_replace('%40', '@', rawurlencode($value));
    } elseif ($type === 'url' && preg_match('~\Ahttps?://~i', $value) && filter_var($value, FILTER_VALIDATE_URL)) {
        $href = $value;
    }
    if ($href !== '') {
        return '<a href="' . site_escape($href) . '">' . site_escape($value) . '</a>';
    }
    if ($type === 'bool') {
        return $value === '1' ? 'Да' : 'Нет';
    }
    return site_escape($value);
}

function site_video_labels(string $locale): array
{
    $labels = [
        'ru'=>['Видео','Смотреть видео','Открыть видео по ссылке'],
        'en'=>['Video','Watch video','Open video link'],
        'uk'=>['Відео','Дивитися відео','Відкрити посилання на відео'],
        'de'=>['Video','Video ansehen','Videolink öffnen'],
        'fr'=>['Vidéo','Voir la vidéo','Ouvrir le lien vidéo'],
        'es'=>['Vídeo','Ver vídeo','Abrir enlace del vídeo'],
        'pt'=>['Vídeo','Ver vídeo','Abrir o link do vídeo'],
        'ja'=>['動画','動画を見る','動画リンクを開く'],
        'ko'=>['동영상','동영상 보기','동영상 링크 열기'],
        'vi'=>['Video','Xem video','Mở liên kết video'],
        'zh'=>['视频','观看视频','打开视频链接'],
    ];
    return $labels[$locale] ?? $labels['en'];
}

function site_video_source(string $url): ?array
{
    if (strlen($url) > 2048 || !filter_var($url, FILTER_VALIDATE_URL)) return null;
    $parts = parse_url($url);
    if (strtolower($parts['scheme'] ?? '') !== 'https' || isset($parts['user']) || isset($parts['pass'])) return null;
    $host = strtolower($parts['host'] ?? '');
    $path = $parts['path'] ?? '';
    if (in_array($host, ['youtube.com','www.youtube.com','www.youtube-nocookie.com','youtu.be'], true)) {
        parse_str($parts['query'] ?? '', $query);
        $id = $host === 'youtu.be' ? trim($path, '/') : ($query['v'] ?? basename($path));
        if (is_string($id) && preg_match('/\A[A-Za-z0-9_-]{11}\z/', $id)) {
            return ['embed', 'https://www.youtube-nocookie.com/embed/' . $id];
        }
        return null;
    }
    if (in_array($host, ['rutube.ru','www.rutube.ru'], true)) {
        return preg_match('~\A/(?:video|play/embed)/([0-9a-f]{32})/?\z~i', $path, $match)
            ? ['embed', 'https://rutube.ru/play/embed/' . $match[1]] : null;
    }
    if (in_array($host, ['vk.com','vkvideo.ru','www.vkvideo.ru'], true) && preg_match('~\A/video(-?\d+)_(\d+)/?\z~', $path, $match)) {
        return ['embed', 'https://vk.com/video_ext.php?oid=' . $match[1] . '&id=' . $match[2]];
    }
    return preg_match('/\.(?:mp4|webm|ogv|mov|m4v)\z/i', $path) ? ['file', $url] : null;
}

function site_video_button(string $url): string
{
    $source = site_video_source($url);
    if ($source === null) return '';
    $locale = site_region_settings()['public'];
    [, $watch, $open] = site_video_labels($locale);
    return '<a class="button site-video-action" href="' . site_escape($url) . '" data-video-source="' . site_escape($source[1])
        . '" data-video-kind="' . $source[0] . '" data-video-close="' . site_escape(site_translation('Закрыть', $locale))
        . '" data-video-link="' . site_escape($open) . '" aria-haspopup="dialog">▶ <span>' . site_escape($watch) . '</span></a>';
}
