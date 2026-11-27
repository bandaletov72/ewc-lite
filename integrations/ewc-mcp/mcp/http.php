<?php
declare(strict_types=1);

// Intentionally a localhost-only prototype, not a deployable public endpoint.
require __DIR__ . '/src/http-bootstrap.php';

if (PHP_SAPI !== 'cli-server' ||
    !in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true)) {
    stopHttp(404);
}

if (parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) !== '/mcp') {
    stopHttp(404);
}

$host = $_SERVER['HTTP_HOST'] ?? '';
if (!preg_match('/\A(?:localhost|127\.0\.0\.1|\[::1\])(?::[0-9]{1,5})?\z/i', $host)) {
    stopHttp(403, 'Invalid Host');
}
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin !== '' && $origin !== 'http://' . $host) {
    stopHttp(403, 'Invalid Origin');
}

$secret = getenv('EWC_MCP_HTTP_TOKEN');
if (!is_string($secret) || !preg_match('/\A[A-Za-z0-9_-]{32,256}\z/', $secret)) {
    stopHttp(503, 'Test endpoint is not configured');
}
if (!hash_equals('Bearer ' . $secret, $_SERVER['HTTP_AUTHORIZATION'] ?? '')) {
    header('WWW-Authenticate: Bearer');
    stopHttp(401, 'Authentication required');
}

$ewcRoot = (string) getenv('EWC_MCP_SITE_ROOT');
$ewcUrl = (string) getenv('EWC_MCP_SITE_URL');
$ewcWrites = getenv('EWC_MCP_TEST_WRITES') === '1';
require __DIR__ . '/src/http-dispatch.php';
