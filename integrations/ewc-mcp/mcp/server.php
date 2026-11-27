<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
ini_set('display_errors', 'stderr');
require __DIR__ . '/src/rpc.php';

try {
    if ($argc !== 4) {
        throw new EwcInput('Usage: php server.php SITE_ROOT PRIVATE_STATE_DIRECTORY SITE_URL');
    }
    $site = new EwcSite($argv[1], $argv[2], $argv[3]);
} catch (Throwable $error) {
    fwrite(STDERR, 'EWC MCP: ' . $error->getMessage() . PHP_EOL);
    exit(1);
}

const EWC_MCP_MAX_LINE = 1048576;
while (($line = fgets(STDIN, EWC_MCP_MAX_LINE + 2)) !== false) {
    if (strlen($line) > EWC_MCP_MAX_LINE) {
        fwrite(STDERR, "EWC MCP: Maximum message size exceeded.\n");
        exit(1);
    }
    $message = json_decode($line);
    $response = json_last_error() === JSON_ERROR_NONE
        ? handleMessage($message, $site)
        : rpcError(null, -32700, 'Parse error');
    if ($response !== null) {
        fwrite(STDOUT, ewcJson($response) . "\n");
        fflush(STDOUT);
    }
}
