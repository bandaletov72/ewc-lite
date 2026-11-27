<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}

const SITE_CONTENT_API_CONFIG = SITE_ROOT . '/data/content-api.php';
const SITE_CONTENT_API_CONFIG_LIMIT = 2048;
const SITE_CONTENT_API_FIELDS = [
    'title' => 'Title',
    'heading' => 'Heading',
    'description' => 'Description',
    'keywords' => 'Keywords',
];

function site_content_api_secure(): bool
{
    return in_array(strtolower($_SERVER['HTTPS'] ?? ''), ['on', '1'], true)
        || (PHP_SAPI === 'cli-server'
            && in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true)
            && preg_match('~\A(?:localhost|127\.0\.0\.1|\[::1\])(?::[0-9]+)?\z~i', $_SERVER['HTTP_HOST'] ?? '') === 1
            && !array_intersect_key($_SERVER, array_flip(['HTTP_FORWARDED', 'HTTP_X_FORWARDED_FOR',
                'HTTP_X_FORWARDED_HOST', 'HTTP_X_FORWARDED_PROTO', 'HTTP_X_REAL_IP'])));
}

function site_content_api_settings(): array
{
    $settings = site_read_record(SITE_CONTENT_API_CONFIG, SITE_CONTENT_API_CONFIG_LIMIT);
    if (!$settings) {
        return ['hash' => '', 'access' => 'read', 'gpt' => false, 'gpt_shop' => false, 'preview_key' => ''];
    }
    if (!is_string($settings['hash'] ?? null) || !preg_match('/\A[a-f0-9]{64}\z/', $settings['hash'])
        || !in_array($settings['access'] ?? null, ['read', 'write'], true)) {
        throw new RuntimeException('Invalid content API configuration.');
    }
    $previewKey = $settings['preview_key'] ?? '';
    if (!is_string($previewKey) || ($previewKey !== '' && !preg_match('/\A[a-f0-9]{64}\z/', $previewKey))) {
        throw new RuntimeException('Invalid preview key.');
    }
    return ['hash' => $settings['hash'], 'access' => $settings['access'], 'gpt' => ($settings['gpt'] ?? false) === true,
        'gpt_shop' => ($settings['gpt_shop'] ?? false) === true, 'preview_key' => $previewKey];
}

function site_content_api_response(int $status, array $body)
{
    http_response_code($status);
    header('Content-Type: application/json; charset=UTF-8');
    header('Cache-Control: private, no-store');
    header('X-Content-Type-Options: nosniff');
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'HEAD') {
        echo json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}

function site_content_api_error(int $status, string $code, array $details = [])
{
    site_content_api_response($status, ['error' => ['code' => $code] + $details]);
}

function site_content_api_page_path(string $path): bool
{
    return site_valid_path($path)
        && preg_match('~\A(?:api/v1|error|privacy)(?:/|\z)~i', $path) !== 1;
}

function site_content_api_page(array $page, string $base): array
{
    $path = $page['path'];
    $result = [
        'id' => $path,
        'url' => site_page_url($base, $path),
        'resource' => site_url($base, 'api/v1/pages/' . $path),
    ];
    foreach (SITE_CONTENT_API_FIELDS as $key => $field) {
        $result[$key] = $page['fields'][$field];
    }
    if (isset($page['html'])) {
        $result['html'] = $page['html'];
    }
    return $result;
}

function site_content_api_list(string $base, bool $gpt = false)
{
    $pagination = [];
    foreach (['page' => 1, 'per_page' => $gpt ? 10 : 25] as $name => $default) {
        $value = $_GET[$name] ?? (string) $default;
        if (!is_string($value) || !preg_match('/\A[1-9][0-9]{0,6}\z/', $value)
            || ($name === 'per_page' && (int) $value > ($gpt ? 10 : 100))) {
            site_content_api_error(400, 'invalid_pagination', ['field' => $name]);
            return;
        }
        $pagination[$name] = (int) $value;
    }
    $pages = array_values(array_filter(site_pages(true, site_content_root()), static function (array $page): bool {
        return site_content_api_page_path($page['path']);
    }));
    if ($gpt && isset($_GET['q'])) {
        $query = $_GET['q'];
        if (!is_string($query) || strlen($query) > 240 || preg_match('//u', $query) !== 1) {
            site_content_api_error(400, 'invalid_search');
            return;
        }
        $pattern = '~' . preg_quote(trim($query), '~') . '~iu';
        $pages = array_values(array_filter($pages, static function (array $page) use ($pattern): bool {
            return preg_match($pattern, $page['path'] . ' ' . $page['fields']['Title'] . ' ' . $page['fields']['Heading']) === 1;
        }));
    }
    $pagination['total'] = count($pages);
    $offset = ($pagination['page'] - 1) * $pagination['per_page'];
    $items = array_map(static function (array $page) use ($base): array {
        return site_content_api_page($page, $base);
    }, array_slice($pages, $offset, $pagination['per_page']));
    site_content_api_response(200, ['items' => $items, 'pagination' => $pagination]);
}

function site_content_api_json(int $limit): ?object
{
    $type = strtolower(trim(explode(';', $_SERVER['CONTENT_TYPE'] ?? '', 2)[0]));
    if ($type !== 'application/json') {
        site_content_api_error(415, 'json_required');
        return null;
    }
    $body = file_get_contents('php://input', false, null, 0, $limit + 1);
    if ($body === false || strlen($body) > $limit) {
        site_content_api_error(413, 'body_too_large');
        return null;
    }
    $parsed = json_decode($body, false, 8);
    if (!is_object($parsed) || !get_object_vars($parsed)) {
        site_content_api_error(400, 'json_object_required');
        return null;
    }
    return $parsed;
}

function site_content_api_changes(object $changes): ?array
{
    $normalized = [];
    foreach ($changes as $key => $value) {
        if ($key !== 'html' && !isset(SITE_CONTENT_API_FIELDS[$key])) {
            site_content_api_error(422, 'unknown_field');
            return null;
        }
        if (!is_string($value) || ($key === 'html'
                ? strlen($value) > SITE_PAGE_LIMIT || strpos($value, "\0") !== false
                : strlen($value) > 1500 || preg_match('/[\x00-\x1f\x7f]/', $value)
                    || ($key === 'title' && trim($value, ' ') === ''))) {
            site_content_api_error(422, 'invalid_field', ['field' => $key]);
            return null;
        }
        $normalized[$key] = $key === 'html' ? $value : trim($value, ' ');
    }
    ksort($normalized);
    return $normalized;
}

function site_content_api_candidate(array $page, array $changes): array
{
    foreach (SITE_CONTENT_API_FIELDS as $key => $field) {
        $page['fields'][$field] = $changes[$key] ?? $page['fields'][$field];
    }
    $page['html'] = $changes['html'] ?? $page['html'];
    return $page;
}

function site_content_api_write_access(string $tokenHash, string $feature = ''): bool
{
    $settings = site_content_api_settings();
    if (hash_equals($settings['hash'], $tokenHash) && $settings['access'] === 'write'
        && ($feature === '' || ($settings['gpt'] && $settings[$feature] && $settings['preview_key'] !== ''))) {
        return true;
    }
    site_content_api_error(403, 'write_access_required');
    return false;
}

function site_content_api_page_bytes(array $page): ?string
{
    try {
        return admin_page_bytes($page['path'], $page['fields'], $page['html']);
    } catch (RuntimeException $exception) {
        site_content_api_error(422, 'invalid_page_content');
        return null;
    }
}

function site_content_api_write_page(string $root, string $path, string $bytes): array
{
    if (!admin_replace_file($root . '/' . $path, $bytes)) {
        throw new RuntimeException('Could not write page.');
    }
    $page = site_read_page($path, $root);
    if ($page === null) {
        throw new RuntimeException('Could not read saved page.');
    }
    return $page;
}

function site_content_api_update(string $path, string $base, string $tokenHash)
{
    $revision = trim($_SERVER['HTTP_IF_MATCH'] ?? '');
    if ($revision === '') {
        site_content_api_error(428, 'if_match_required');
        return;
    }
    if (!preg_match('/\A"([a-f0-9]{64})"\z/', $revision, $match)) {
        site_content_api_error(400, 'invalid_if_match');
        return;
    }
    // A JSON string can encode each byte as six characters.
    $body = site_content_api_json(6 * SITE_PAGE_LIMIT + 4096);
    $changes = $body === null ? null : site_content_api_changes($body);
    if ($changes === null) return;
    require_once SITE_ROOT . '/admin/files.php';
    require_once SITE_ROOT . '/admin/save.php';
    $lock = admin_lock();
    try {
        if (!site_content_api_write_access($tokenHash)) return;
        $root = site_content_root();
        $page = site_read_page($path, $root);
        if ($page === null) {
            site_content_api_error(404, 'page_not_found');
            return;
        }
        if (!hash_equals($page['revision'], $match[1])) {
            site_content_api_error(412, 'page_has_changed');
            return;
        }
        $candidate = site_content_api_candidate($page, $changes);
        if ($candidate !== $page) {
            $bytes = site_content_api_page_bytes($candidate);
            if ($bytes === null) return;
            $page = site_content_api_write_page($root, $path, $bytes);
        }
        header('ETag: "' . $page['revision'] . '"');
        site_content_api_response(200, site_content_api_page($page, $base));
    } finally {
        fclose($lock);
    }
}

function site_content_api_dispatch(string $base, string $request)
{
    try {
        $settings = site_content_api_settings();
        if ($settings['hash'] === '') {
            site_content_api_error(404, 'api_disabled');
            return;
        }
        if (!site_content_api_secure()) {
            site_content_api_error(403, 'https_required');
            return;
        }
        $token = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
        if (!preg_match('/\ABearer (ewc_[a-f0-9]{64})\z/i', $token, $match)
            || !hash_equals($settings['hash'], $tokenHash = hash('sha256', $match[1]))) {
            header('WWW-Authenticate: Bearer realm="EWC Content API"');
            site_content_api_error(401, 'invalid_token');
            return;
        }
        if (preg_match('~\A/api/v1/shop(?:/|\z)~', $request)) {
            if (!$settings['gpt'] || !$settings['gpt_shop'] || !site_shop_enabled()) {
                site_content_api_error(404, 'shop_gpt_disabled');
                return;
            }
            require __DIR__ . '/content-api-gpt.php';
            require __DIR__ . '/content-api-shop.php';
            site_content_shop_dispatch(substr($request, strlen('/api/v1/shop/')), $base, $tokenHash, $settings['access']);
            return;
        }
        $path = substr($request, strlen('/api/v1/pages'));
        $collection = $path === '' || $path === '/';
        $path = substr($path, 1);
        $gptPages = ['page', 'preview', 'apply', 'iblock', 'iblock-preview', 'iblock-apply', 'image-import'];
        if (!$settings['gpt'] && ($path === 'search' || in_array($path, $gptPages, true))) {
            site_content_api_error(404, 'gpt_disabled');
            return;
        }
        if ($path === 'search') {
            if (in_array($_SERVER['REQUEST_METHOD'] ?? 'GET', ['GET', 'HEAD'], true)) {
                site_content_api_list($base, true);
            } else {
                header('Allow: GET, HEAD');
                site_content_api_error(405, 'method_not_allowed');
            }
            return;
        }
        if (in_array($path, $gptPages, true)) {
            require __DIR__ . '/content-api-gpt.php';
            require __DIR__ . '/content-api-iblock.php';
            if ($path === 'image-import') {
                require __DIR__ . '/content-api-images.php';
                site_content_image_import($base, $tokenHash, $settings['access']);
            } elseif (strpos($path, 'iblock') === 0) {
                site_content_iblock_dispatch($path, $tokenHash, $settings['access']);
            } else {
                site_content_gpt_dispatch($path, $base, $tokenHash, $settings['access']);
            }
            return;
        }
        if (!$collection && !site_content_api_page_path($path)) {
            site_content_api_error(404, 'page_not_found');
            return;
        }
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if ($method === 'PATCH' && !$collection) {
            if ($settings['access'] === 'write') {
                site_content_api_update($path, $base, $tokenHash);
            } else {
                site_content_api_error(403, 'write_access_required');
            }
        } elseif ($method !== 'GET' && $method !== 'HEAD') {
            header('Allow: ' . ($collection ? 'GET, HEAD' : 'GET, HEAD, PATCH'));
            site_content_api_error(405, 'method_not_allowed');
        } elseif ($collection) {
            site_content_api_list($base);
        } else {
            $page = site_read_page($path, site_content_root());
            if ($page === null) {
                site_content_api_error(404, 'page_not_found');
                return;
            }
            header('ETag: "' . $page['revision'] . '"');
            site_content_api_response(200, site_content_api_page($page, $base));
        }
    } catch (Throwable $exception) {
        error_log('EWC: content API request failed.');
        site_content_api_error(500, 'internal_error');
    }
}
