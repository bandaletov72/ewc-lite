<?php if (!defined('SITE_ROOT')) { http_response_code(404); exit; } ?>
<?php $dialogId = $componentDataId ?? 'page-component-data'; ?>
<dialog class="editor-dialog component-data-dialog" id="<?= site_escape($dialogId) ?>" aria-labelledby="<?= site_escape($dialogId) ?>-label">
    <div class="header-actions editor-dialog-heading">
        <h2 id="<?= site_escape($dialogId) ?>-label"><?= site_escape($editSettingsText) ?></h2>
        <?php if ($dialogId === 'page-component-data'): ?><a class="button" data-component-data-wide target="_blank" rel="noopener"><?= site_escape($editWideText) ?></a><?php endif; ?>
        <button class="dialog-close" type="button" data-close-page-dialog aria-label="<?= $editText('Закрыть') ?>"></button>
    </div>
    <?php $frameLabels = site_component_frame_labels($editor['language']); ?>
    <div class="form-stack" data-component-frame-status hidden>
        <p class="notice" role="status" data-component-frame-message
            data-loading="<?= site_escape($frameLabels[0]) ?>" data-error="<?= site_escape($frameLabels[1]) ?>"></p>
        <div><button type="button" data-component-frame-retry hidden><?= site_escape($frameLabels[2]) ?></button></div>
    </div>
    <iframe data-component-data-iframe title="<?= site_escape($editSettingsText) ?>"></iframe>
</dialog>
