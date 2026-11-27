<?php
declare(strict_types=1);

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    stopHttp(405);
}
$contentType = strtolower(trim(explode(';', $_SERVER['CONTENT_TYPE'] ?? '')[0]));
if ($contentType !== 'application/json') {
    stopHttp(415, 'Expected application/json');
}
$accept = strtolower($_SERVER['HTTP_ACCEPT'] ?? '*/*');
if (!preg_match('/(?:application\/json|application\/\*|\*\/\*)/', $accept)) {
    stopHttp(406, 'JSON responses only');
}

require __DIR__ . '/rpc.php';
$version = $_SERVER['HTTP_MCP_PROTOCOL_VERSION'] ?? '';
if ($version !== '' && !in_array($version, array_merge(EWC_MCP_PROTOCOLS, [EWC_MCP_STATELESS_VERSION]), true)) {
    stopHttp(400, 'Unsupported MCP-Protocol-Version');
}
$max = 1048576;
if ((int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > $max) {
    stopHttp(413);
}
$raw = file_get_contents('php://input', false, null, 0, $max + 1);
if ($raw === false || strlen($raw) > $max) {
    stopHttp(413);
}
$message = json_decode($raw);
header('Content-Type: application/json; charset=utf-8');
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    echo ewcJson(rpcError(null, -32700, 'Parse error'));
    exit;
}
if (is_object($message) && $version === EWC_MCP_STATELESS_VERSION &&
    ($message->method ?? null) !== 'initialize' &&
    (!isset($message->params) || is_object($message->params))) {
    $message->params = $message->params ?? new stdClass();
    if (!isset($message->params->_meta) || is_object($message->params->_meta)) {
        $message->params->_meta = $message->params->_meta ?? new stdClass();
        $key = 'io.modelcontextprotocol/protocolVersion';
        $message->params->_meta->$key = $message->params->_meta->$key ?? $version;
    }
}

try {
    $site = new EwcSite($ewcRoot, (string) getenv('EWC_MCP_STATE_DIR'), $ewcUrl);
} catch (Throwable $error) {
    error_log('EWC MCP: CMS configuration failed.');
    stopHttp(503, 'CMS configuration unavailable');
}
$allowed = ewcTools();
if (!$ewcWrites) {
    $allowed = array_filter($allowed, static function (array $tool): bool {
        return ($tool['annotations']['readOnlyHint'] ?? false) === true;
    });
}

if (isset($ewcPublish) && !$ewcPublish) {
    unset($allowed['publish']);
}

$response = handleMessage($message, $site, $allowed);
if ($response === null) {
    http_response_code(202);
    header_remove('Content-Type');
    exit;
}
echo ewcJson($response);
