<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
try {
    $url = $argv[1] ?? '';
    $token = getenv('EWC_MCP_DIRECT_TOKEN');
    if (!is_string($token) || !preg_match('/\A[A-Za-z0-9_-]{32,256}\z/', $token)
        || !preg_match('~\Ahttps://[a-zA-Z0-9.-]+(?::[0-9]{1,5})?/mcp\z~', $url)) {
        throw new RuntimeException('Use an HTTPS /mcp URL and set EWC_MCP_DIRECT_TOKEN securely in the environment.');
    }
    $call = static function (array $packet, string $version = '') use ($url, $token): array {
        $headers = [
            'Authorization: Bearer ' . $token,
            'Content-Type: application/json',
            'Accept: application/json, text/event-stream',
        ];
        if ($version !== '') {
            $headers[] = 'MCP-Protocol-Version: ' . $version;
        }
        $context = stream_context_create([
            'ssl' => ['verify_peer' => true, 'verify_peer_name' => true],
            'http' => [
                'method' => 'POST',
                'header' => $headers,
                'content' => json_encode(['jsonrpc' => '2.0'] + $packet),
                'ignore_errors' => true,
                'follow_location' => 0,
                'timeout' => 15,
            ],
        ]);
        $body = @file_get_contents($url, false, $context, 0, 2097153);
        $responseHeaders = function_exists('http_get_last_response_headers')
            ? http_get_last_response_headers() : ($http_response_header ?? []);
        $status = (int) substr($responseHeaders[0] ?? '', 9, 3);
        if ($status === 202 && !isset($packet['id'])) {
            return [];
        }
        $data = is_string($body) && strlen($body) <= 2097152 ? json_decode($body, true) : null;
        if ($status !== 200 || !is_array($data) || !is_array($data['result'] ?? null) || !empty($data['result']['isError'])) {
            throw new RuntimeException('MCP request failed: ' . $packet['method'] . ' (HTTP ' . $status . ').');
        }
        return $data['result'];
    };
    $initial = $call(['id' => 1, 'method' => 'initialize', 'params' => [
        'protocolVersion' => '2025-11-25', 'capabilities' => (object) [],
        'clientInfo' => ['name' => 'ewc-direct-example', 'version' => '1.0'],
    ]]);
    $version = $initial['protocolVersion'] ?? '';
    if (!in_array($version, ['2025-11-25', '2025-06-18', '2025-03-26'], true)) {
        throw new RuntimeException('Unsupported negotiated MCP version.');
    }
    $call(['method' => 'notifications/initialized'], $version);
    $result = $call(['id' => 2, 'method' => 'tools/call', 'params' => ['name' => 'list_pages']], $version);
    echo json_encode($result['structuredContent'] ?? $result, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT), PHP_EOL;
} catch (RuntimeException $error) {
    fwrite(STDERR, $error->getMessage() . PHP_EOL);
    exit(1);
}
