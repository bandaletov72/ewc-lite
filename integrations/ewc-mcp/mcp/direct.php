<?php
declare(strict_types=1);

// Install outside the document root. Map only the exact HTTPS /mcp path to this file.
require __DIR__ . '/src/http-bootstrap.php';

if (PHP_SAPI === 'cli' || ($_SERVER['HTTPS'] ?? '') !== 'on'
    || ($_SERVER['REQUEST_URI'] ?? '') !== '/mcp') {
    stopHttp(404);
}

$host = (string) getenv('EWC_MCP_DIRECT_HOST');
if ($host === '' || preg_match('/\A[a-z0-9.-]+(?::[0-9]{1,5})?\z/', $host) !== 1
    || strtolower($_SERVER['HTTP_HOST'] ?? '') !== $host) {
    stopHttp(403, 'Invalid Host');
}

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if ($origin !== '' && $origin !== 'https://' . $host) {
    stopHttp(403, 'Invalid Origin');
}

$secret = getenv('EWC_MCP_DIRECT_TOKEN');
if (!is_string($secret) || !preg_match('/\A[A-Za-z0-9_-]{32,256}\z/', $secret)) {
    stopHttp(503, 'Direct endpoint is not configured');
}
if (!hash_equals('Bearer ' . $secret, $_SERVER['HTTP_AUTHORIZATION'] ?? '')) {
    header('WWW-Authenticate: Bearer');
    stopHttp(401, 'Authentication required');
}

require __DIR__ . '/src/installation.php';
$ewcRoot = (string) getenv('EWC_MCP_SITE_ROOT');
try {
    $ewcUrl = ewcInstallationConfig($ewcRoot, 'direct')['url'];
} catch (RuntimeException $error) {
    stopHttp(503, 'Direct connection is not configured');
}
$ewcWrites = getenv('EWC_MCP_DIRECT_WRITES') === '1';
$ewcPublish = $ewcWrites && getenv('EWC_MCP_DIRECT_PUBLISH') === '1';
require __DIR__ . '/src/http-dispatch.php';
