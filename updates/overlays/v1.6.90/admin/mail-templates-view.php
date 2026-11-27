<?php
if (!defined('SITE_ROOT')) { http_response_code(404); exit; }
$mailLabels = admin_mail_labels($adminLanguage);
$mailEvents = admin_mail_events($adminLanguage);
$mailLibrary = site_mail_library();
$mailId = admin_input($_GET, 'template');
$mailEvent = $mailEvents[$mailId] ?? null;
$mailRecord = $mailLibrary['templates'][$mailId] ?? null;
$mailForm = $mailEvent['form'] ?? null;
$mailCustom = !$mailEvent && ($mailRecord || $mailId === 'new');
$mailBinding = $mailCustom ? array_search($mailId, $mailLibrary['events'], true) : false;
if ($mailBinding !== false && !isset($mailEvents[$mailBinding])) $mailBinding = false;
$mailDefaults = $mailForm ? site_form_mail_settings($mailForm) : [];
$mailData = $mailDefaults + ['mail_subject'=>$mailRecord['subject'] ?? ($mailEvent['message'][0] ?? ''),
    'mail_body'=>$mailRecord['body'] ?? ($mailEvent['message'][1] ?? ''), 'recipient'=>'','copies'=>''];
if ($error !== '' && admin_input($_POST, 'action') === 'save-mail-template') {
    $mailData = site_form_mail_settings($_POST);
    if ($mailCustom) $mailBinding = admin_input($_POST, 'event');
}
$mailLink = static function (string $id) use ($mailTemplatesUrl): string {
    return site_escape($mailTemplatesUrl . '&template=' . rawurlencode($id) . '#mail-template-editor');
};
$mailGroups = [
    'system'=>array_filter($mailEvents, static function (array $event): bool { return !isset($event['form']); }),
    'forms'=>array_filter($mailEvents, static function (array $event): bool { return isset($event['form']); }),
    'custom'=>array_filter($mailLibrary['templates'], static function (string $id): bool { return strpos($id, 'mail-') === 0; }, ARRAY_FILTER_USE_KEY),
];
?>
<div class="admin-heading">
    <h1><?= site_escape($mailLabels['title']) ?></h1>
    <a class="button button-primary" href="<?= $mailLink('new') ?>"><i class="fa-solid fa-plus" aria-hidden="true"></i> <?= site_escape($mailLabels['add']) ?></a>
</div>
<div class="form-stack">
    <?php foreach ($mailGroups as $group => $entries): if (!$entries) continue; ?>
        <section class="panel form-stack">
            <h2><?= site_escape($mailLabels[$group]) ?></h2>
            <div class="header-actions">
                <?php foreach ($entries as $id => $entry): ?>
                    <a class="button<?= $id === $mailId ? ' button-primary' : '' ?>" href="<?= $mailLink($id) ?>"<?= $id === $mailId ? ' aria-current="page"' : '' ?>>
                        <?= site_escape($entry['label'] ?? $entry['name']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endforeach; ?>
    <?php if ($mailEvent || $mailCustom): ?>
        <form class="panel form-stack" method="post" id="mail-template-editor" action="<?= $mailLink($mailId) ?>" data-mail-editor>
            <h2><?= site_escape($mailEvent['label'] ?? ($mailRecord['name'] ?? $mailLabels['add'])) ?></h2>
            <?php $assignedTemplate = $mailLibrary['events'][$mailId] ?? ''; ?>
            <?php if (isset($mailLibrary['templates'][$assignedTemplate])): ?>
                <p><?= site_escape($mailLabels['assigned']) ?> <a href="<?= $mailLink($assignedTemplate) ?>"><?= site_escape($mailLibrary['templates'][$assignedTemplate]['name']) ?></a></p>
            <?php endif; ?>
            <input type="hidden" name="csrf" value="<?= site_escape($token) ?>">
            <input type="hidden" name="action" value="save-mail-template">
            <input type="hidden" name="template" value="<?= site_escape($mailId) ?>">
            <input type="hidden" name="revision" value="<?= site_escape(admin_settings_revision($mailForm ?? $mailLibrary)) ?>">
            <?php if ($mailCustom): ?>
                <div class="fields">
                    <label class="field"><?= site_escape($mailLabels['name']) ?><input name="name" maxlength="240" required
                        value="<?= site_escape($error !== '' ? admin_input($_POST, 'name') : ($mailRecord['name'] ?? '')) ?>"></label>
                    <label class="field"><?= site_escape($mailLabels['event']) ?><select name="event">
                        <option value=""><?= site_escape($mailLabels['unassigned']) ?></option>
                        <?php foreach ($mailEvents as $id => $option): ?>
                            <option value="<?= site_escape($id) ?>"<?= $mailBinding === $id ? ' selected' : '' ?>><?= site_escape($option['label']) ?></option>
                        <?php endforeach; ?>
                    </select></label>
                </div>
            <?php endif; ?>
            <?php if ($mailForm): ?>
                <div class="fields">
                    <label class="field"><?= admin_l('Кому') ?><input name="recipient" type="email" multiple maxlength="<?= SITE_MAIL_ADDRESS_LIMIT ?>" value="<?= site_escape($mailData['recipient']) ?>"></label>
                    <label class="field"><?= admin_l('Копии (Cc)') ?><input name="copies" type="email" multiple maxlength="<?= SITE_MAIL_ADDRESS_LIMIT ?>" value="<?= site_escape($mailData['copies']) ?>"></label>
                </div>
            <?php endif; ?>
            <label class="field"><?= admin_l('Тема письма') ?><input name="mail_subject" data-mail-template maxlength="<?= SITE_MAIL_SUBJECT_LIMIT ?>"<?= $mailForm ? '' : ' required' ?> value="<?= site_escape($mailData['mail_subject']) ?>"></label>
            <label class="field"><?= admin_l('Текст письма') ?><textarea name="mail_body" data-mail-template rows="8" maxlength="<?= SITE_MAIL_TEMPLATE_LIMIT ?>"<?= $mailForm ? '' : ' required' ?>><?= "\n" . site_escape($mailData['mail_body']) ?></textarea></label>
            <?php $mailTokens = $mailEvent['tokens'] ?? ($mailEvents[$mailBinding]['tokens'] ?? []); ?>
            <?php if ($mailTokens): ?><div class="header-actions">
                <?php foreach ($mailTokens as $variable): ?><button type="button" data-mail-variable="<?= site_escape($variable) ?>"><code><?= site_escape($variable) ?></code></button><?php endforeach; ?>
            </div><?php endif; ?>
            <div><button class="button-primary" type="submit"><?= admin_l('Сохранить') ?></button></div>
        </form>
    <?php endif; ?>
</div>
