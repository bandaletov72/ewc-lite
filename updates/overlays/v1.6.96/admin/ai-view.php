<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}
$aiText = static function (string $key) use ($aiLabels): string {
    return site_escape($aiLabels[$key]);
};
?>
<?php if (!$aiOnboarding): ?><h1><?= $aiText('tab') ?></h1><?php endif; ?>
<section class="panel form-stack<?= $aiOnboarding ? ' setup-panel' : '' ?>" id="chatgpt">
    <?php if ($aiOnboarding): ?>
        <p class="hint"><?= $aiText('optional') ?></p>
        <h1><?= $aiText('title') ?></h1>
    <?php else: ?>
        <h2><?= $aiText('title') ?></h2>
    <?php endif; ?>
    <p><?= $aiText('intro') ?></p>
    <?php if ($notice !== ''): ?><p class="notice success" role="status"><?= site_escape($notice) ?></p><?php endif; ?>
    <?php if ($error !== ''): ?>
        <p class="notice error" role="alert"><?= site_escape($error) ?></p>
    <?php endif; ?>
    <form class="form-stack" method="post" action="<?= site_escape($aiFormUrl) ?>">
        <input type="hidden" name="csrf" value="<?= site_escape($token) ?>">
        <input type="hidden" name="revision" value="<?= site_escape($aiRevision) ?>">
        <div class="field">
            <label for="ai-site-url"><?= $aiText('url') ?></label>
            <input id="ai-site-url" type="url" name="site_url" required maxlength="2048"
                   value="<?= site_escape($aiDraft['url']) ?>" placeholder="https://example.com/">
        </div>
        <div class="field">
            <label for="ai-mode"><?= $aiText('mode') ?></label>
            <select id="ai-mode" name="mode">
                <?php foreach (['tunnel', 'direct'] as $mode): ?>
                    <option value="<?= $mode ?>"<?= $aiDraft['mode'] === $mode ? ' selected' : '' ?>><?= $aiText($mode) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="field">
            <label for="ai-tunnel-id">Tunnel ID</label>
            <input id="ai-tunnel-id" name="tunnel_id" maxlength="135" spellcheck="false"
                   autocomplete="off" value="<?= site_escape($aiDraft['tunnel']) ?>" placeholder="tunnel_…">
        </div>
        <p class="hint"><?= $aiText('secret') ?></p>
        <?php if (!empty($aiSaved)): ?><p class="hint"><?= $aiText('status') ?></p><?php endif; ?>
        <div class="header-actions">
            <button class="button-primary" type="submit" name="action" value="save-chatgpt"<?= $aiReadError ? ' disabled' : '' ?>><?= $aiText('save') ?></button>
            <?php if ($aiOnboarding): ?>
                <button type="submit" name="action" value="skip-chatgpt" formnovalidate><?= $aiText('skip') ?></button>
            <?php endif; ?>
            <a href="https://developers.openai.com/api/docs/guides/secure-mcp-tunnels" target="_blank" rel="noopener noreferrer"><?= $aiText('docs') ?></a>
        </div>
    </form>
    <?php require __DIR__ . '/ai-guide.php'; ?>
</section>
<?php if (!$installationPending): ?>
    <?php require __DIR__ . '/content-api-view.php'; ?>
<?php endif; ?>
