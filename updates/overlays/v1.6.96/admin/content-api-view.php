<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}
$apiLabels = admin_content_api_labels($adminLanguage);
$apiText = static function (string $key) use ($apiLabels): string {
    return site_escape($apiLabels[$key]);
};
$apiSecure = site_content_api_secure();
$apiEndpoint = (site_canonical_url($base, 'index.php') ?: site_url($base)) . 'api/v1/pages';
$apiToken = $contentApiToken ?? '';
$apiSettings = null;
try {
    $apiSettings = site_content_api_settings();
} catch (RuntimeException $exception) {
    $contentApiError = $apiLabels['error'];
}
?>
<section class="panel form-stack" id="content-api">
    <h2><i class="fa-solid fa-code" aria-hidden="true"></i> REST API</h2>
    <p class="hint"><?= $apiText('intro') ?></p>
    <div class="field">
        <label for="content-api-url"><?= $apiText('endpoint') ?></label>
        <input id="content-api-url" type="text" value="<?= site_escape($apiEndpoint) ?>" readonly>
    </div>
    <?php if (!empty($contentApiError)): ?>
        <p class="notice error" role="alert"><?= site_escape($contentApiError) ?></p>
    <?php elseif (!empty($contentApiSaved)): ?>
        <p class="notice success" role="status"><?= $apiText('saved') ?></p>
    <?php endif; ?>
    <?php if (!$apiSecure): ?>
        <p class="notice error" role="alert"><?= $apiText('https') ?></p>
    <?php elseif ($apiSettings !== null): ?>
        <?php $apiActive = $apiSettings['hash'] !== ''; ?>
        <?php if (is_string($apiToken) && $apiToken !== '' && hash_equals($apiSettings['hash'], hash('sha256', $apiToken))): ?>
            <div class="field">
                <label for="content-api-key"><?= $apiText('key') ?></label>
                <input id="content-api-key" type="text" value="<?= site_escape($apiToken) ?>" spellcheck="false" autocomplete="off" readonly aria-describedby="content-api-key-hint">
                <p class="hint" id="content-api-key-hint"><?= $apiText('once') ?></p>
            </div>
        <?php endif; ?>
        <p class="hint"><?= $apiText($apiActive ? 'active' : 'inactive') ?></p>
        <form method="post" action="<?= site_escape($aiUrl) ?>#content-api" class="form-stack">
            <input type="hidden" name="csrf" value="<?= site_escape($token) ?>">
            <input type="hidden" name="api_revision" value="<?= site_escape(admin_settings_revision($apiSettings)) ?>">
            <div class="field">
                <label for="content-api-access"><?= $apiText('access') ?></label>
                <select id="content-api-access" name="api_access">
                    <option value="read"<?= $apiSettings['access'] === 'read' ? ' selected' : '' ?>><?= $apiText('read') ?></option>
                    <option value="write"<?= $apiSettings['access'] === 'write' ? ' selected' : '' ?>><?= $apiText('write') ?></option>
                </select>
            </div>
            <div class="header-actions">
                <?php if ($apiActive): ?>
                    <button class="button-primary" name="action" value="content-api-save" type="submit"><?= $apiText('save') ?></button>
                <?php endif; ?>
                <button<?= $apiActive ? '' : ' class="button-primary"' ?> name="action" value="content-api-issue" type="submit"><?= $apiText($apiActive ? 'rotate' : 'issue') ?></button>
                <?php if ($apiActive): ?>
                    <button name="action" value="content-api-revoke" type="submit"><?= $apiText('revoke') ?></button>
                <?php endif; ?>
            </div>
        </form>
    <?php endif; ?>
    <p><a href="<?= site_escape($helpUrl) ?>#help-content-api"><?= $apiText('docs') ?></a></p>
</section>
