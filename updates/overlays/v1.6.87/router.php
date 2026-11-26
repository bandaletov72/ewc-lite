<?php
if (PHP_SAPI !== 'cli-server') {
    http_response_code(404);
    exit;
}

$request = explode('?', $_SERVER['REQUEST_URI'] ?? '/', 2)[0];
$path = str_replace('\\', '/', rawurldecode($request));
$segments = [];
foreach (explode('/', $path) as $part) {
    if (DIRECTORY_SEPARATOR === '\\') {
        $part = rtrim($part, ' ');
    }
    if ($part === '..') {
        array_pop($segments);
    } elseif ($part !== '' && $part !== '.') {
        $segments[] = DIRECTORY_SEPARATOR === '\\' ? rtrim($part, '.') : $part;
    }
}

$root = strtolower($segments[0] ?? '');
$second = strtolower($segments[1] ?? '');
$themeCode = $root === 'templates' && $second === 'themes'
    && preg_match('~(?:^|/)(?:\.[^/]+|[^/]+\.php(?:[0-9]*)?)(?:/|$)~i', implode('/', $segments));
if ($root === 'shop' && count($segments) > 1 && preg_match('/\.(?:php|css|jpg|jpeg|png|webp|avif|gif)\z/i', end($segments))
    && $segments !== ['shop', 'index.php']) {
    http_response_code(404);
    return true;
}
if (in_array($root, ['data', 'extensions', 'router.php', 'functions.php', '.htaccess'], true) || strpos($root, '.save-') === 0
    || ($root === 'app' && $second === 'extensions') || ($root === 'admin' && $second === 'languages') || $themeCode) {
    http_response_code(404);
    exit;
}
if (preg_match('~\A/privacy\.php(?:/|$)~i', $path)) {
    http_response_code(404);
    return true;
}
// Keep the root definition local to the redirect helper so executed pages can define it normally.
if (strpos((string) @file_get_contents(__DIR__ . '/.htaccess', false, null, 0, 65537), '# BEGIN EUROWEBCART REDIRECTS') !== false) {
    define('SITE_ROUTER', true);
    require_once __DIR__ . '/app/redirect-rules.php';
    if (site_apply_redirect_rules('')) {
        return true;
    }
}
if (preg_match('~\A/(?:[A-Za-z0-9_-]+/)*index_[a-z]{2}\.php\z~', $path)
    && !is_file(__DIR__ . $path)) {
    $_SERVER['SCRIPT_NAME'] = '/app/page-language.php';
    require __DIR__ . '/app/page-language.php';
    return true;
}

$target = __DIR__ . '/' . implode('/', $segments);
if (!is_file($target) && !is_file($target . '/index.php') && !is_file($target . '/index.html')
    && is_file(__DIR__ . '/error/404/index.php')) {
    $_SERVER['SCRIPT_NAME'] = '/app/page-route.php';
    require __DIR__ . '/app/page-route.php';
    return true;
}

return false;
