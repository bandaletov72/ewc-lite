<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}

const SITE_CONTENT_GPT_LIMIT = 90000;
const SITE_CONTENT_GPT_PREVIEW_LIFETIME = 600;

function site_content_gpt_response(array $body)
{
    $json = json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($json === false || strlen($json) > SITE_CONTENT_GPT_LIMIT) {
        site_content_api_error(422, 'content_too_large_for_gpt');
        return;
    }
    site_content_api_response(200, $body);
}

function site_content_gpt_preview_token(int $until, string $tokenHash, string $root, string $path, string $revision, array $changes): string
{
    $key = site_content_api_settings()['preview_key'];
    if ($key === '') throw new RuntimeException('Preview signing is unavailable.');
    $message = serialize([$until, $tokenHash, $root, $path, $revision, $changes]);
    return $until . '.' . hash_hmac('sha256', $message, $key);
}

function site_content_gpt_diff(array $page, array $changes): array
{
    $diff = [];
    foreach ($changes as $field => $after) {
        $key = SITE_CONTENT_API_FIELDS[$field] ?? '';
        $before = $key === '' ? $page['html'] : $page['fields'][$key];
        if ($before !== $after) {
            $diff[$field] = ['before' => $before, 'after' => $after];
        }
    }
    return $diff;
}

function site_content_gpt_dispatch(string $operation, string $base, string $tokenHash, string $access)
{
    $reading = $operation === 'page';
    $applying = $operation === 'apply';
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    if ($reading ? !in_array($method, ['GET', 'HEAD'], true) : $method !== 'POST') {
        header('Allow: ' . ($reading ? 'GET, HEAD' : 'POST'));
        site_content_api_error(405, 'method_not_allowed');
        return;
    }
    if ($reading) {
        $path = $_GET['path'] ?? null;
        if (!is_string($path) || !site_content_api_page_path($path)) {
            site_content_api_error(404, 'page_not_found');
            return;
        }
        $page = site_read_page($path, site_content_root());
        if ($page === null) {
            site_content_api_error(404, 'page_not_found');
            return;
        }
        $body = site_content_api_page($page, $base) + ['revision' => $page['revision'], 'components' => site_content_api_components($page['html'])];
        site_content_gpt_response($body);
        return;
    }
    if ($access !== 'write') {
        site_content_api_error(403, 'write_access_required');
        return;
    }
    $body = site_content_api_json(SITE_CONTENT_GPT_LIMIT);
    if ($body === null) return;
    $expected = ['path', 'revision', 'changes'];
    if ($applying) $expected[] = 'preview_token';
    if (!is_string($body->path ?? null) || !site_content_api_page_path($body->path)
        || !is_string($body->revision ?? null) || !preg_match('/\A[a-f0-9]{64}\z/', $body->revision)
        || !is_object($body->changes ?? null) || !get_object_vars($body->changes)
        || array_diff(array_keys(get_object_vars($body)), $expected)) {
        site_content_api_error(422, 'invalid_edit_request');
        return;
    }
    $changes = site_content_api_changes($body->changes);
    if ($changes === null) return;
    if ($applying && (!is_string($body->preview_token ?? null)
        || !preg_match('/\A([0-9]{1,11})\.[a-f0-9]{64}\z/', $body->preview_token, $expiry))) {
        site_content_api_error(428, 'preview_required');
        return;
    }

    require_once SITE_ROOT . '/admin/files.php';
    require_once SITE_ROOT . '/admin/save.php';
    $lock = $applying ? admin_lock() : null;
    try {
        if ($applying && !site_content_api_write_access($tokenHash, 'gpt')) return;
        $root = site_content_root();
        $page = site_read_page($body->path, $root);
        if ($page === null) {
            site_content_api_error(404, 'page_not_found');
            return;
        }
        if (!hash_equals($page['revision'], $body->revision)) {
            site_content_api_error(412, 'page_has_changed');
            return;
        }
        $candidate = site_content_api_candidate($page, $changes);
        if ($candidate === $page) {
            site_content_api_error(422, 'no_changes');
            return;
        }
        $bytes = site_content_api_page_bytes($candidate);
        if ($bytes === null) return;
        if (!$applying) {
            $until = time() + SITE_CONTENT_GPT_PREVIEW_LIFETIME;
            site_content_gpt_response([
                'path' => $body->path,
                'url' => site_page_url($base, $body->path),
                'revision' => $body->revision,
                'diff' => site_content_gpt_diff($page, $changes),
                'preview_token' => site_content_gpt_preview_token($until, $tokenHash, $root, $body->path, $body->revision, $changes),
                'expires_at' => gmdate('c', $until),
            ]);
            return;
        }
        $until = (int) $expiry[1];
        if ($until < time() || !hash_equals(
            site_content_gpt_preview_token($until, $tokenHash, $root, $body->path, $body->revision, $changes),
            $body->preview_token
        )) {
            site_content_api_error(412, 'preview_expired_or_changed');
            return;
        }
        $saved = site_content_api_write_page($root, $body->path, $bytes);
        site_content_api_response(200, [
            'path' => $saved['path'],
            'url' => site_page_url($base, $saved['path']),
            'revision' => $saved['revision'],
            'saved' => true,
        ]);
    } finally {
        if ($lock !== null) fclose($lock);
    }
}
