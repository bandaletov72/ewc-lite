<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}
?>
<details class="form-stack">
    <summary><?= $aiText('guide') ?></summary>
    <h3><?= $aiText('tunnel') ?></h3>
    <ol>
        <li><?= $aiText('platform') ?> <a href="https://developers.openai.com/api/docs/guides/secure-mcp-tunnels" rel="noopener noreferrer" target="_blank"><?= $aiText('docs') ?></a></li>
        <li><?= $aiText('host') ?> <code>instance-from-cms.sh</code></li>
        <li><?= $aiText('plugin') ?> <a href="https://developers.openai.com/plugins/deploy/connect-chatgpt" rel="noopener noreferrer" target="_blank"><?= $aiText('docs') ?></a></li>
    </ol>
    <h3><?= $aiText('direct') ?></h3>
    <p><?= $aiText('directGuide') ?></p>
    <p><?= $aiText('oauth') ?> <a href="https://developers.openai.com/plugins/build/auth" rel="noopener noreferrer" target="_blank">OAuth</a></p>
    <h3><?= $aiText('editTitle') ?></h3>
    <p><code>list_pages</code> → <code>draft_page</code> → <code>preview_draft</code> → <?= $aiText('approval') ?> → <code>publish</code>.</p>
    <p><?= $aiText('editor') ?></p>
</details>
