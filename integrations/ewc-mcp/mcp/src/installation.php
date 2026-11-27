<?php
declare(strict_types=1);

/** Read the CMS's guarded JSON record without evaluating PHP or reading any key. */
function ewcInstallationConfig(string $root, string $mode): array
{
    $root = realpath($root);
    if ($root === false || !is_file($root . '/app/site.php')) {
        throw new RuntimeException('Set EWC_MCP_SITE_ROOT to an EWC-lite installation.');
    }
    $file = $root . '/admin/.ai-chatgpt.php';
    $prefix = "<?php http_response_code(404); exit; __halt_compiler();\n";
    $raw = !is_link($file) ? @file_get_contents($file, false, null, 0, 8193) : false;
    if ($raw === false || strlen($raw) > 8192 || strpos($raw, $prefix) !== 0) {
        throw new RuntimeException('Save AI settings in the EWC-lite admin panel first.');
    }
    $settings = json_decode(substr($raw, strlen($prefix)), true);
    if (!is_array($settings) || ($settings['mode'] ?? '') !== $mode
        || !is_string($settings['url'] ?? null)
        || preg_match('~\Ahttps?://[a-zA-Z0-9.:[\]-]+/[a-zA-Z0-9_/-]*\z~', $settings['url']) !== 1
        || ($mode === 'tunnel' && (!is_string($settings['tunnel'] ?? null)
            || preg_match('/\Atunnel_[a-zA-Z0-9]{16,128}\z/', $settings['tunnel']) !== 1))) {
        throw new RuntimeException('Save valid settings for the selected AI connection mode.');
    }
    return $settings;
}
