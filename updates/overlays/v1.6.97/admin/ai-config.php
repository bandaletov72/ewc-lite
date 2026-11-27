<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}

const ADMIN_AI_FILE = SITE_ROOT . '/admin/.ai-chatgpt.php';
require_once SITE_ROOT . '/app/content-api.php';

function admin_content_api_save(array $auth, string $action, string $access, string $revision, array $features): string
{
    if (!site_content_api_secure() || !in_array($access, ['read', 'write'], true)) {
        throw new RuntimeException('Invalid content API settings.');
    }
    $lock = admin_lock();
    try {
        $current = admin_auth_state();
        if (!isset($current['password']) || !hash_equals(admin_auth_key($current), admin_auth_key($auth))) {
            throw new RuntimeException('Administrator session has changed.');
        }
        $settings = site_content_api_settings();
        if (!hash_equals(admin_settings_revision($settings), $revision)) {
            throw new InvalidArgumentException('revisionError');
        }
        $token = '';
        if ($action === 'content-api-revoke') {
            $settings = [];
        } elseif ($action === 'content-api-issue') {
            $token = 'ewc_' . bin2hex(random_bytes(32));
            $settings = ['hash' => hash('sha256', $token), 'access' => $access, 'preview_key' => bin2hex(random_bytes(32))] + $features;
        } elseif ($settings['hash'] !== '') {
            $settings['access'] = $access;
            $settings = array_replace($settings, $features);
            if ($settings['preview_key'] === '') $settings['preview_key'] = bin2hex(random_bytes(32));
        } else {
            throw new RuntimeException('No content API token.');
        }
        admin_write_record(SITE_CONTENT_API_CONFIG, $settings, SITE_CONTENT_API_CONFIG_LIMIT);
        return $token;
    } finally {
        fclose($lock);
    }
}

function admin_content_api_labels(string $language): array
{
    $labels = require __DIR__ . '/content-api-labels.php';
    return array_combine(array_keys($labels['en']), $labels[$language] ?? array_values($labels['en']));
}

function admin_ai_labels(string $language): array
{
    $labels = require __DIR__ . '/ai-labels.php';
    return $labels[$language] ?? $labels['ru'];
}

/** A canonical display/link base, not an MCP endpoint or a network destination. */
function admin_ai_url(string $value): string
{
    $url = parse_url($value);
    if (strlen($value) > 2048 || preg_match('~[\x00-\x20\x7f\\\\]~', $value)
        || !is_array($url) || !isset($url['scheme'], $url['host'])
        || array_intersect(['user', 'pass', 'query', 'fragment'], array_keys($url))
        || !preg_match('~\A/[A-Za-z0-9_/-]*\z~', $url['path'] ?? '/')
        || (isset($url['port']) && $url['port'] < 1)) {
        throw new InvalidArgumentException('urlError');
    }
    $host = strtolower($url['host']);
    $scheme = strtolower($url['scheme']);
    $local = in_array($host, ['localhost', '127.0.0.1', '[::1]'], true);
    if ((!$local && !preg_match('~\A(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\z~', $host))
        || ($scheme !== 'https' && !($local && $scheme === 'http'))) {
        throw new InvalidArgumentException('urlError');
    }
    return $scheme . '://' . $host . (isset($url['port']) ? ':' . $url['port'] : '')
        . rtrim($url['path'] ?? '/', '/') . '/';
}

function admin_ai_validate(string $url, string $tunnel, string $mode): array
{
    if (!in_array($mode, ['tunnel', 'direct'], true)) {
        throw new InvalidArgumentException('modeError');
    }
    if ($mode === 'tunnel' && !preg_match('/\Atunnel_[a-zA-Z0-9]{16,128}\z/', $tunnel)) {
        throw new InvalidArgumentException('tunnelError');
    }
    return ['url' => admin_ai_url($url), 'tunnel' => $mode === 'tunnel' ? $tunnel : '', 'mode' => $mode];
}

function admin_ai_settings(): array
{
    return site_read_record(ADMIN_AI_FILE, 8192);
}

function admin_ai_save(array $auth, array $config, string $revision, bool $onboarding, bool $skip)
{
    $lock = admin_lock();
    try {
        $current = admin_auth_state();
        if (!isset($current['password']) || !hash_equals(admin_auth_key($current), admin_auth_key($auth))
            || ($onboarding && empty($current['chatgptSetupPending']))) {
            throw new InvalidArgumentException('sessionError');
        }
        if (!$skip) {
            if (!hash_equals(admin_settings_revision(admin_ai_settings()), $revision)) {
                throw new InvalidArgumentException('revisionError');
            }
            admin_write_record(ADMIN_AI_FILE, $config, 8192);
        }
        if ($onboarding) {
            unset($current['chatgptSetupPending']);
            admin_write_record(ADMIN_ACCESS_FILE, $current);
        }
    } finally {
        fclose($lock);
    }
}
