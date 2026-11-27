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
$gptLabels = require __DIR__ . '/content-gpt-labels.php';
list($gptEnabling, $gptSetup, $gptSchema, $gptInstructions, $gptPrompt) = $gptLabels[$adminLanguage] ?? $gptLabels['en'];
$gptShopLabels = require __DIR__ . '/content-gpt-shop-labels.php';
list($gptShopLabel, $gptShopTip) = $gptShopLabels[$adminLanguage] ?? $gptShopLabels['en'];
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
            <label class="choice"><input type="checkbox" name="api_gpt" value="1"<?= $apiSettings['gpt'] ? ' checked' : '' ?>> <?= site_escape($gptEnabling) ?></label>
            <?php if (site_shop_enabled()): ?>
                <label class="choice"><input type="checkbox" name="api_gpt_shop" value="1"<?= $apiSettings['gpt_shop'] ? ' checked' : '' ?>> <?= site_escape($gptShopLabel) ?></label>
            <?php endif; ?>
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
    <details class="disclosure-panel">
        <summary><span class="disclosure-arrow" aria-hidden="true"></span> ChatGPT · GPT Actions</summary>
        <p><?= site_escape($gptSetup) ?></p>
        <?php
        $gptShopAvailable = !empty($apiSettings['gpt']) && !empty($apiSettings['gpt_shop']) && site_shop_enabled();
        $gptBlockPrompt = $adminLanguage === 'ru'
            ? 'Смотри components из readEwcPage. Для таблицы вызови readEwcInfoBlock, используй ключи столбцов и типы. Не придумывай действующие цены: если источник не дан, пометь суммы как предложения. Добавляй строки без затрагивания существующих, если не просят иначе. Покажи все затронутые строки и скажи, что инфоблок изменится везде, где вставлен. Только после подтверждения вызывай applyEwcInfoBlock с неизменённым запросом и токеном из previewEwcInfoBlock.'
            : 'Inspect components from readEwcPage. For a table, call readEwcInfoBlock and use its exact column keys and types. Label unverified prices as proposals. Preserve existing rows unless the user requests replacement. Use previewEwcInfoBlock, show all affected rows and explain that a shared table changes everywhere it is used. Ask for explicit confirmation before applying exactly the previewed request.';
        $gptPrompt .= "\n\n" . $gptBlockPrompt;
        $gptPrompt .= "\n\n" . ($adminLanguage === 'ru'
            ? 'Для замены фотографий прочитай страницу и проверь элементы image и gallery в components. Сначала покажи новый вариант изображений пользователю. По его согласию импортируй новые JPEG, PNG или WebP через importEwcImages. Используй полученные src в предлагаемом HTML, сохрани подходящие alt и пропорции. Затем покажи отдельный предпросмотр замены на странице и дождись подтверждения сохранения. Исходные фото не удаляй.'
            : 'For photo replacements, inspect image and gallery entries in components. Show generated or supplied candidates to the user first, then import approved JPEG, PNG or WebP files via importEwcImages. Use returned src paths, retain suitable alt text and aspect ratios, and request a separate page preview approval before changing the page. Keep the original media files.');
        if ($gptShopAvailable) {
            $gptPrompt .= "\n\n" . ($gptShopLabels['prompt'][$adminLanguage] ?? $gptShopLabels['prompt']['en']);
        }
        ?>
        <?php if (site_shop_enabled()): ?><p class="hint"><?= site_escape($gptShopTip) ?></p><?php endif; ?>
        <?= site_copy_code(trim(require __DIR__ . '/content-gpt-schema.php'), $adminLanguage, $gptSchema) ?>
        <?= site_copy_code($gptPrompt, $adminLanguage, $gptInstructions) ?>
        <p><a href="https://chatgpt.com/gpts/editor" target="_blank" rel="noopener noreferrer">ChatGPT · GPT Builder</a></p>
    </details>
    <details class="disclosure-panel">
        <summary><span class="disclosure-arrow" aria-hidden="true"></span> Gemini · Claude</summary>
        <?php $providerInfo = require __DIR__ . '/content-gpt-provider-labels.php'; ?>
        <p><?= site_escape($providerInfo[$adminLanguage] ?? $providerInfo['en']) ?></p>
        <?= site_copy_code('node editor.mjs gemini', $adminLanguage, 'Gemini') ?>
        <?= site_copy_code('node editor.mjs claude', $adminLanguage, 'Claude') ?>
        <p><a href="<?= site_escape(SITE_PRODUCT_REPOSITORY . '/tree/main/integrations/ewc-rest-ai') ?>" target="_blank" rel="noopener noreferrer"><?= $apiText('docs') ?></a></p>
    </details>
    <p><a href="<?= site_escape($helpUrl) ?>#help-content-api"><?= $apiText('docs') ?></a></p>
</section>
