<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}

function site_content_api_components(string $html): array
{
    $components = [];
    preg_match_all(site_content_pattern([]), $html, $matches, PREG_SET_ORDER);
    foreach ($matches as $match) {
        if (!empty($match['iblock'])) {
            $id = site_iblock_id_from_argument($match['iblock']);
            if ($id !== '') $components['iblock:' . $id] = ['type' => 'iblock', 'id' => $id, 'view' => $match['iblockTemplate']];
            continue;
        }
        if (preg_match('~\A<img\b~i', $match[0])) {
            preg_match('~\s+src\s*=\s*(?:"([^"]*)"|\x27([^\x27]*)\x27|([^\s>]+))~i', $match[0], $src);
            preg_match('~\s+alt\s*=\s*(?:"([^"]*)"|\x27([^\x27]*)\x27|([^\s>]+))~i', $match[0], $alt);
            if ($src) {
                $url = html_entity_decode($src[1] ?: ($src[2] ?? '') ?: ($src[3] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $label = html_entity_decode(($alt[1] ?? '') ?: ($alt[2] ?? '') ?: ($alt[3] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                if ($url !== '') $components['image:' . $url] = ['type' => 'image', 'src' => $url, 'alt' => $label];
            }
            continue;
        }
        foreach (['gallery' => 'gallery', 'include' => 'include', 'form' => 'form', 'banners' => 'banners', 'shopWidget' => 'shop',
            'map' => 'map', 'snippet' => 'snippet'] as $capture => $type) {
            if (empty($match[$capture])) continue;
            $value = trim($capture === 'banners' && !empty($match['bannerFolder']) ? $match['bannerFolder'] : $match[$capture]);
            $components[$type . ':' . $value] = ['type' => $type, 'id' => $value];
        }
    }
    return array_values($components);
}

function site_content_iblock_context(string $path, string $id): ?array
{
    if (!site_content_api_page_path($path) || !site_valid_iblock_name($id)) return null;
    $page = site_read_page($path, site_content_root());
    if ($page === null) return null;
    foreach (site_content_api_components($page['html']) as $component) {
        if ($component['type'] !== 'iblock' || $component['id'] !== $id) continue;
        $block = site_read_iblock($id);
        return $block ? [$page, $block] : null;
    }
    return null;
}

function site_content_iblock_rows(array $rows, array $columns): array
{
    if (!$rows || count($rows) > 50) throw new InvalidArgumentException('Invalid row count.');
    $keys = array_column($columns, 'key');
    $output = [];
    foreach ($rows as $row) {
        if (!is_object($row) || array_diff(array_keys(get_object_vars($row)), $keys)) {
            throw new InvalidArgumentException('Unknown column.');
        }
        $values = [];
        foreach ($keys as $key) {
            $value = $row->$key ?? '';
            if (!is_string($value)) throw new InvalidArgumentException('Cell values must be strings.');
            $values[] = $value;
        }
        $output[] = $values;
    }
    return $output;
}

function site_content_iblock_dispatch(string $operation, string $tokenHash, string $access)
{
    $read = $operation === 'iblock';
    $apply = $operation === 'iblock-apply';
    $method = $read ? 'GET' : 'POST';
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== $method) {
        header('Allow: ' . $method);
        site_content_api_error(405, 'method_not_allowed');
        return;
    }
    if (!$read && $access !== 'write') {
        site_content_api_error(403, 'write_access_required');
        return;
    }
    require_once SITE_ROOT . '/admin/files.php';
    require_once SITE_ROOT . '/admin/config.php';
    require_once SITE_ROOT . '/admin/form-config.php';
    require_once SITE_ROOT . '/admin/iblock-config.php';
    $input = $read ? (object) $_GET : site_content_api_json(SITE_CONTENT_GPT_LIMIT);
    if ($input === null) return;
    $path = $input->path ?? null;
    $id = $input->id ?? null;
    $context = is_string($path) && is_string($id) ? site_content_iblock_context($path, $id) : null;
    if ($context === null) {
        site_content_api_error(404, 'linked_iblock_not_found');
        return;
    }
    list($page, $block) = $context;
    $revision = admin_settings_revision($block);
    $metadata = ['path' => $path, 'id' => $id, 'revision' => $revision, 'columns' => $block['columns'],
        'shared' => true, 'total' => count($block['rows'])];
    if ($read) {
        $part = $input->page ?? '1';
        if (!is_string($part) || !preg_match('/\A[1-9][0-9]{0,4}\z/', $part)) {
            site_content_api_error(400, 'invalid_pagination');
            return;
        }
        site_content_gpt_response($metadata + ['page' => (int) $part, 'per_page' => 20,
            'rows' => array_slice($block['rows'], ((int) $part - 1) * 20, 20)]);
        return;
    }
    $mode = $input->mode ?? null;
    if (!in_array($mode, ['append', 'update', 'replace'], true) || !is_array($input->rows ?? null)
        || !is_string($input->revision ?? null)) {
        site_content_api_error(422, 'invalid_iblock_edit');
        return;
    }
    if (!hash_equals($revision, $input->revision)) {
        site_content_api_error(412, 'iblock_has_changed');
        return;
    }
    $start = $mode === 'update' ? ($input->start_row ?? null) : 1;
    if (!is_int($start) || $start < 1 || ($mode === 'update' && $start + count($input->rows) - 1 > count($block['rows']))) {
        site_content_api_error(422, 'invalid_start_row');
        return;
    }
    if ($apply && (!is_string($input->preview_token ?? null)
        || !preg_match('/\A([0-9]{1,11})\.[a-f0-9]{64}\z/', $input->preview_token, $expiry))) {
        site_content_api_error(428, 'preview_required');
        return;
    }
    try {
        $rows = site_content_iblock_rows($input->rows, $block['columns']);
        $before = $mode === 'replace' ? $block['rows'] : ($mode === 'update' ? array_slice($block['rows'], $start - 1, count($rows)) : []);
        $updated = $block;
        if ($mode === 'append') $start = count($updated['rows']) + 1;
        if ($mode === 'replace') $updated['rows'] = $rows;
        else array_splice($updated['rows'], $start - 1, $mode === 'update' ? count($rows) : 0, $rows);
        $normalized = admin_normalize_iblock($updated);
        if ($mode !== 'replace' && (array_slice($updated['rows'], 0, $start - 1) !== array_slice($normalized['rows'], 0, $start - 1)
            || array_slice($updated['rows'], $start - 1 + count($rows)) !== array_slice($normalized['rows'], $start - 1 + count($rows)))) {
            throw new InvalidArgumentException('Existing untouched rows require normalization in the admin editor.');
        }
        $updated = $normalized;
        if (strlen(SITE_RECORD_HEADER . site_iblock_csv($updated)) > SITE_IBLOCK_LIMIT) {
            throw new InvalidArgumentException('The table is too large.');
        }
    } catch (RuntimeException | InvalidArgumentException $exception) {
        site_content_api_error(422, 'invalid_iblock_rows', ['detail' => $exception->getMessage()]);
        return;
    }
    if ($updated === $block) {
        site_content_api_error(422, 'no_changes');
        return;
    }
    $after = array_slice($updated['rows'], $start - 1, count($rows));
    $until = $apply ? (int) $expiry[1] : time() + SITE_CONTENT_GPT_PREVIEW_LIFETIME;
    $receiptData = [$id, $page['revision'], $mode, $start, $after];
    $receipt = site_content_gpt_preview_token($until, $tokenHash, site_content_data_directory(), $path, $revision, $receiptData);
    if (!$apply) {
        site_content_gpt_response($metadata + ['mode' => $mode, 'start_row' => $start, 'before' => $before, 'after' => $after,
            'preview_token' => $receipt, 'expires_at' => gmdate('c', $until)]);
        return;
    }
    if ($until < time() || !hash_equals($receipt, $input->preview_token)) {
        site_content_api_error(412, 'preview_expired_or_changed');
        return;
    }
    try {
        $newRevision = admin_save_iblock($id, $updated, $revision, static function () use ($path, $id, $page, $tokenHash, $receiptData, $until, $revision, $receipt) {
            $settings = site_content_api_settings();
            if (!$settings['gpt'] || $settings['access'] !== 'write' || !hash_equals($settings['hash'], $tokenHash)) {
                throw new RuntimeException('Access changed.', 403);
            }
            $current = site_content_iblock_context($path, $id);
            if ($until < time() || $current === null || !hash_equals($current[0]['revision'], $page['revision'])
                || !hash_equals($receipt, site_content_gpt_preview_token($until, $tokenHash, site_content_data_directory(), $path, $revision, $receiptData))) {
                throw new RuntimeException('Preview changed.', 412);
            }
        });
    } catch (RuntimeException $exception) {
        if ($exception->getCode() === 403) site_content_api_error(403, 'write_access_required');
        elseif ($exception->getCode() === 412 || admin_settings_revision(site_read_iblock($id)) !== $revision) site_content_api_error(412, 'iblock_has_changed');
        else throw $exception;
        return;
    }
    site_content_api_response(200, ['saved' => true, 'id' => $id, 'path' => $path, 'revision' => $newRevision]);
}
