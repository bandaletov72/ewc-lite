<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}
?>
<?php if ($error !== ''): ?>
    <p class="notice error" role="alert"><?= admin_l($error) ?></p>
<?php elseif ($notice !== ''): ?>
    <p class="notice" role="status"><?= admin_l($notice) ?></p>
<?php endif; ?>

<?php if ($section === 'settings'): ?>
    <h1><?= admin_l('Настройки сайта') ?></h1>
    <form class="panel form-stack" method="post" action="<?= site_escape($settingsUrl) ?>">
        <input type="hidden" name="action" value="save-settings">
        <input type="hidden" name="csrf" value="<?= site_escape($token) ?>">
        <input type="hidden" name="revision" value="<?= site_escape($settingsRevision) ?>">
        <?php $brandHelp = require SITE_ROOT . '/app/help-site-subtitle.php'; ?>
        <?php foreach (['name' => admin_t('Название сайта'), 'subtitle' => ($brandHelp[$adminLanguage] ?? $brandHelp['ru'])[0],
            'description' => admin_t('Метаописание по умолчанию'), 'footer' => admin_t('Текст подвала')] as $name => $label): ?>
            <div class="field">
                <label for="setting-<?= $name ?>"><?= admin_l($label) ?></label>
                <input id="setting-<?= $name ?>" name="<?= $name ?>" value="<?= site_escape($settingsDraft[$name]) ?>" <?= $name === 'name' ? 'required' : '' ?> <?= $name === 'footer' ? 'aria-describedby="footer-hint"' : '' ?> maxlength="375">
                <?php if ($name === 'footer'): ?>
                    <small class="hint" id="footer-hint"><?= admin_l('Используйте [year] для текущего года, например «© [year] Название сайта».') ?></small>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        <?php $contactLabels = site_city_contact_labels($adminLanguage); ?>
        <label class="field" id="message-recipients"><?= site_escape($contactLabels[0]) ?>
            <input type="email" name="message_recipients" multiple maxlength="<?= SITE_MAIL_ADDRESS_LIMIT ?>"
                value="<?= site_escape($settingsDraft['messageRecipients']) ?>" placeholder="office@example.com, sales@example.com">
            <small class="hint"><?= site_escape($contactLabels[2]) ?></small>
        </label>
        <label class="field"><?= admin_l('Публичный адрес сайта') ?>
            <input type="url" name="public_url" value="<?= site_escape($settingsDraft['publicUrl']) ?>" placeholder="https://example.com/" maxlength="2048">
            <small class="hint"><?= admin_l('Пустое поле использует адрес открытого сайта.') ?> <code>[site-url]</code></small>
        </label>
        <input type="hidden" name="breadcrumb_option" value="1">
        <label class="choice">
            <input type="checkbox" name="breadcrumbs" value="1"<?= $settingsDraft['breadcrumbs'] ? ' checked' : '' ?>>
            <?= admin_l('Показывать хлебные крошки') ?>
        </label>
        <label class="field" id="site-search-settings"><?= admin_l('Вид поиска') ?>
            <select name="search_mode">
                <?php foreach (['off' => admin_t('Выключен'), 'compact' => admin_t('Компактный (лупа)'), 'expanded' => admin_t('Расширенный')] as $value => $label): ?>
                    <option value="<?= $value ?>"<?= $settingsDraft['searchMode'] === $value ? ' selected' : '' ?>><?= admin_l($label) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <?php $phpLabels = site_php_admin_labels($adminLanguage); ?>
        <?php $animationLabels = site_scroll_animation_labels($adminLanguage); ?>
        <label class="field" id="scroll-animation"><?= site_escape($animationLabels[0]) ?>
            <select name="scroll_animation">
                <?php foreach (SITE_SCROLL_ANIMATIONS as $index => $effect): ?>
                    <option value="<?= $effect ?>"<?= $settingsDraft['scrollAnimation'] === $effect ? ' selected' : '' ?>><?= site_escape($animationLabels[$index + 1]) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label class="field" id="php-error-reporting"><?= site_escape($phpLabels[1]) ?> <code>error_reporting</code>
            <select name="error_reporting">
                <?php foreach (array_keys(site_error_reporting_levels()) as $index => $level): ?>
                    <option value="<?= $level ?>"<?= $settingsDraft['errorReporting'] === $level ? ' selected' : '' ?>><?= site_escape($phpLabels[2][$index]) ?></option>
                <?php endforeach; ?>
            </select>
            <small class="hint"><?= site_escape($phpLabels[4]) ?></small>
        </label>
        <div class="field">
            <label for="variables"><?= admin_l('Переменные для шаблонов') ?></label>
            <textarea class="short-editor" id="variables" name="variables" rows="4" spellcheck="false" aria-describedby="variables-hint" placeholder="<?= admin_l('[city] = Москва
[spb] = Санкт-Петербург') ?>"><?= "\n" . site_escape($variablesDraft) ?></textarea>
            <small class="hint" id="variables-hint"><?= admin_l('Одна переменная на строку, например [city] = Москва. Для вставки используйте [city].') ?></small>
        </div>
        <div><button class="button-primary" type="submit"><?= admin_l('Сохранить') ?></button></div>
    </form>
    <?php require __DIR__ . '/recovery-settings-view.php'; ?>
    <?php require __DIR__ . '/branding-view.php'; ?>
    <form class="panel form-stack" id="account-registration" method="post" action="<?= site_escape($settingsUrl) ?>">
        <input type="hidden" name="csrf" value="<?= site_escape($token) ?>">
        <input type="hidden" name="action" value="account-registration">
        <label class="choice"><input type="checkbox" name="registration" value="1"<?= site_account_registration() ? ' checked' : '' ?>> <?= admin_l('Разрешить регистрацию пользователей') ?></label>
        <details><summary><?= admin_l('Шаблоны писем сайта') ?></summary>
            <?php foreach (site_account_mail_defaults() as $key => $default): ?>
                <?php $mail = site_account_settings()['mail'][$key] ?? $default; ?>
                <label class="field"><?= site_escape($default[0]) ?><input name="mail[<?= $key ?>][0]" value="<?= site_escape($mail[0]) ?>"></label>
                <label class="field"><?= admin_l('Текст письма') ?><textarea name="mail[<?= $key ?>][1]"><?= site_escape($mail[1]) ?></textarea></label>
            <?php endforeach; ?>
            <p><code>{{site}} {{name}} {{url}}</code></p>
        </details>
        <div><button><?= admin_l('Сохранить') ?></button></div>
    </form>
<?php elseif ($section === 'page_templates'): ?>
    <?php require __DIR__ . '/page-templates-view.php'; ?>
<?php elseif ($section === 'cities'): ?>
    <?php require __DIR__ . '/cities-view.php'; ?>
<?php elseif ($section === 'region'): ?>
    <?php require __DIR__ . '/region-view.php'; ?>
<?php elseif ($section === 'integrations'): ?>
    <?php require __DIR__ . '/integrations-view.php'; ?>
<?php elseif ($section === 'libraries'): ?>
    <?php require __DIR__ . '/libraries-view.php'; ?>
<?php elseif (in_array($section, ['layout', 'headers', 'functions', 'assets', 'design', 'theme', 'fonts'], true)): ?>
    <?php if ($section === 'design'): ?>
        <?php require __DIR__ . '/design-themes-view.php'; ?>
    <?php elseif ($section === 'theme'): ?>
        <?php require __DIR__ . '/appearance-view.php'; ?>
    <?php elseif ($section === 'fonts'): ?>
        <?php require __DIR__ . '/fonts-view.php'; ?>
    <?php elseif ($section === 'headers'): ?>
        <?php require __DIR__ . '/headers-view.php'; ?>
    <?php elseif ($section === 'functions'): ?>
        <?php require __DIR__ . '/functions-view.php'; ?>
    <?php elseif ($section === 'assets'): ?>
        <?php require __DIR__ . '/assets-view.php'; ?>
    <?php elseif (!empty($layoutError)): ?>
        <p class="notice error" role="alert"><?= admin_l($layoutError) ?></p>
    <?php elseif ($layoutDraft !== null): ?>
        <h1><?= admin_l('Шаблоны оформления') ?></h1>
        <?php require __DIR__ . '/domain-templates-view.php'; ?>
    <?php endif; ?>
<?php elseif (in_array($section, ['contacts', 'components'], true)): ?>
    <?php require __DIR__ . '/appearance-view.php'; ?>
<?php elseif ($section === 'shop'): ?>
    <?php require __DIR__ . '/shop-module-view.php'; ?>
<?php elseif ($section === 'users'): ?>
    <?php require_once SITE_ROOT . '/app/users.php'; require __DIR__ . '/users-view.php'; ?>
<?php elseif ($section === 'backups'): ?>
    <?php require __DIR__ . '/content-backup-view.php'; ?>
<?php elseif ($section === 'updates'): ?>
    <?php require __DIR__ . '/updates-view.php'; ?>
<?php elseif ($section === 'domains'): ?>
    <h1><?= admin_l('Домены и поддомены') ?></h1>
    <?php if ($domainsError !== ''): ?>
        <p class="notice error" role="alert"><?= admin_l($domainsError) ?></p>
    <?php else: ?>
        <div class="editor-grid">
            <section class="panel">
                <h2><?= admin_l('Добавленные домены') ?></h2>
                <div class="header-actions">
                    <a class="button" href="<?= site_escape($layoutEditorUrl) ?>"><?= admin_l('Общие шаблоны') ?></a>
                    <?php if ($currentHost !== '' && !in_array($currentHost, $domains, true)): ?>
                        <a class="button" href="<?= site_escape($domainsUrl . '&templates=current') ?>"<?= $layoutSelection === 'current' ? ' aria-current="page"' : '' ?>><?= admin_l('Шаблоны текущего домена') ?></a>
                    <?php endif; ?>
                </div>
                <?php if (!$domains): ?>
                    <p><?= admin_l('Список пока пуст.') ?></p>
                <?php else: ?>
                    <ul class="page-list">
                        <?php foreach ($domains as $domain): ?>
                            <li class="domain-template-row">
                                <strong><?= site_escape($domain) ?></strong>
                                <div class="header-actions">
                                    <a href="<?= site_escape($domainsUrl . '&templates=' . rawurlencode($domain)) ?>"<?= $layoutHost === $domain && $layoutSelection !== '' ? ' aria-current="page"' : '' ?>><?= admin_l('Шаблоны') ?></a>
                                    <a href="<?= site_escape($snippetsUrl . '&domain=' . rawurlencode($domain)) ?>"><?= admin_l('Подшаблоны') ?></a>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </section>
            <form class="panel form-stack" id="add-domain" method="post" action="<?= site_escape($domainsUrl) ?>">
                <input type="hidden" name="action" value="add-domain">
                <input type="hidden" name="csrf" value="<?= site_escape($token) ?>">
                <div class="field">
                    <label for="domain"><?= admin_l('Имя нового поддомена') ?></label>
                    <input id="domain" name="domain" value="<?= site_escape($domainDraft) ?>" placeholder="msk.example.ru" maxlength="253" required>
                </div>
                <div><button class="button-primary" type="submit"><?= admin_l('Добавить поддомен') ?></button></div>
            </form>
        </div>
        <?php if (!empty($layoutError)): ?>
            <p class="notice error" role="alert"><?= admin_l($layoutError) ?></p>
        <?php elseif ($layoutDraft !== null): ?>
            <?php require __DIR__ . '/domain-templates-view.php'; ?>
        <?php endif; ?>
    <?php endif; ?>
<?php elseif ($section === 'snippets'): ?>
    <?php if (!$embeddedData): ?>
    <h1><?= admin_l('Подшаблоны') ?></h1>
    <nav class="site-menu scope-menu" aria-label="<?= admin_l('Область подшаблонов') ?>">
        <?php if ($currentHost !== ''): ?>
            <a href="<?= site_escape($snippetsUrl) ?>"<?= !$sharedScope && $domainSelection === '' ? ' aria-current="page"' : '' ?>><?= admin_l('Текущий домен') ?></a>
        <?php endif; ?>
        <a href="<?= site_escape($snippetsUrl . '&scope=shared') ?>"<?= $sharedScope ? ' aria-current="page"' : '' ?>><?= admin_l('Общие значения') ?></a>
        <?php foreach ($domains as $domain): ?>
            <a href="<?= site_escape($snippetsUrl . '&domain=' . rawurlencode($domain)) ?>"<?= !$sharedScope && $domainSelection !== '' && $scopeHost === $domain ? ' aria-current="page"' : '' ?>><?= site_escape($domain) ?></a>
        <?php endforeach; ?>
        <a class="button" href="<?= site_escape($domainsUrl) ?>#add-domain">+ <?= $adminLanguage === 'ru' ? 'Добавить домен' : admin_l('Добавить поддомен') ?></a>
    </nav>
    <?php endif; ?>
    <?php if ($snippetError !== ''): ?>
        <p class="notice error" role="alert"><?= admin_l($snippetError) ?></p>
    <?php endif; ?>
    <?php if ($scopeValid && ($embeddedData ? $snippet !== null : ($snippetError === '' || $snippetEntries))): ?>
        <p class="hint"><?= $sharedScope ? admin_t('Общие значения применяются на доменах без своего значения.') : admin_t('Значения домена ') . site_escape($scopeHost) . admin_t(' имеют приоритет перед общими.') ?></p>
        <?php if (!$embeddedData): ?>
        <div class="editor-grid">
            <nav class="panel" aria-label="<?= admin_l('Список подшаблонов') ?>">
                <ul class="page-list">
                    <li><a href="<?= site_escape($scopeUrl) ?>"<?= $snippet === null ? ' aria-current="page"' : '' ?>><?= admin_l('Добавить подшаблон') ?></a></li>
                    <?php foreach ($snippetEntries as $key => $item): ?>
                        <li><a href="<?= site_escape($scopeUrl . '&snippet=' . rawurlencode($key)) ?>"<?= $snippet !== null && $snippet['name'] === $key ? ' aria-current="page"' : '' ?>>
                            <code>[<?= site_escape($key) ?>]</code>
                            <small><?= site_escape($item['description']) ?><?= !isset($own[$key]) ? admin_t(' · общее значение') : '' ?></small>
                        </a></li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        <?php endif; ?>
            <?php
            $editor = $snippet ?? ['name' => '', 'values' => ['description' => '', 'value' => ''], 'revision' => '', 'locked' => false];
            $inherited = !$sharedScope && $editor['locked'] && $editor['revision'] === '';
            ?>
            <form class="panel form-stack" method="post" action="<?= site_escape($scopeUrl . ($editor['locked'] ? '&snippet=' . rawurlencode($editor['name']) : '')) ?>">
                <h2><?= $inherited ? admin_t('Переопределить общее значение') : ($editor['locked'] ? admin_t('Редактировать подшаблон') : admin_t('Новый подшаблон')) ?></h2>
                <input type="hidden" name="csrf" value="<?= site_escape($token) ?>">
                <input type="hidden" name="revision" value="<?= site_escape($editor['revision']) ?>">
                <input type="hidden" name="locked" value="<?= $editor['locked'] ? '1' : '0' ?>">
                <div class="field">
                    <?php $snippetCode = $editor['name'] === '' ? '' : '[' . $editor['name'] . ']'; ?>
                    <?php if ($editor['locked']): ?>
                        <input type="hidden" name="snippet" value="<?= site_escape($snippetCode) ?>">
                        <?= site_copy_code($snippetCode, $adminLanguage, admin_t('Подшаблон')) ?>
                    <?php else: ?>
                        <label for="snippet"><?= admin_l('Подшаблон') ?></label>
                        <input id="snippet" name="snippet" value="<?= site_escape($snippetCode) ?>" placeholder="[tel]" maxlength="66" required>
                    <?php endif; ?>
                </div>
                <div class="field">
                    <label for="snippet-description"><?= admin_l('Описание') ?></label>
                    <input id="snippet-description" name="description" value="<?= site_escape($editor['values']['description']) ?>" placeholder="<?= admin_l('Телефон для связи') ?>" maxlength="375" required>
                </div>
                <div class="field">
                    <label for="snippet-value"><?= admin_l('Значение') ?></label>
                    <textarea id="snippet-value" class="short-editor" name="value" rows="5" spellcheck="false" aria-describedby="snippet-hint"><?= "\n" . site_escape($editor['values']['value']) ?></textarea>
                    <small class="hint" id="snippet-hint"><?= admin_l('Здесь можно указать текст или HTML. Имя в квадратных скобках вставляется в страницу или её заголовок.') ?></small>
                </div>
                <div class="header-actions">
                    <button class="button-primary" type="submit" name="action" value="save-snippet"><?= admin_l('Сохранить') ?></button>
                    <?php if (!$embeddedData && $editor['revision'] !== ''): ?>
                        <button type="submit" name="action" value="delete-snippet" formnovalidate><?= !$sharedScope && isset($shared[$editor['name']]) ? admin_t('Вернуть общее значение') : admin_t('Удалить значение') ?></button>
                    <?php endif; ?>
                </div>
            </form>
        <?php if (!$embeddedData): ?></div><?php endif; ?>
    <?php endif; ?>
<?php elseif ($section === 'forms'): ?>
    <?php require __DIR__ . '/forms-view.php'; ?>
<?php elseif ($section === 'spam'): ?>
    <?php $spamLabels = site_spam_admin_labels($adminLanguage); ?>
    <h1><?= admin_l('Антиспам') ?></h1>
    <?php if ($spamEditorError !== ''): ?>
        <p class="notice error" role="alert"><?= admin_l($spamEditorError) ?></p>
    <?php elseif ($spamDraft !== null): ?>
        <form class="panel form-stack" method="post" action="<?= site_escape($spamUrl) ?>">
            <input type="hidden" name="csrf" value="<?= site_escape($token) ?>">
            <input type="hidden" name="revision" value="<?= site_escape($spamRevision) ?>">
            <p class="hint"><?= site_escape($spamLabels[3]) ?></p>
            <div class="fields">
                <div class="field">
                    <label for="spam-words"><?= admin_l('Запрещённые слова и выражения') ?></label>
                    <textarea class="short-editor" id="spam-words" name="words" rows="7" spellcheck="false" placeholder="casino"><?= "\n" . site_escape($spamDraft['words']) ?></textarea>
                </div>
                <div class="field">
                    <label for="spam-ips"><?= admin_l('Заблокированные IP-адреса') ?></label>
                    <textarea class="short-editor" id="spam-ips" name="ips" rows="7" spellcheck="false" placeholder="192.0.2.*"><?= "\n" . site_escape($spamDraft['ips']) ?></textarea>
                </div>
                <div class="field field-wide">
                    <label for="spam-agents"><?= site_escape($spamLabels[1]) ?></label>
                    <textarea class="short-editor" id="spam-agents" name="agents" rows="5" spellcheck="false" placeholder="Bad Robot"><?= "\n" . site_escape($spamDraft['agents'] ?? '') ?></textarea>
                </div>
            </div>
            <p class="hint"><?= site_escape($spamLabels[4]) ?></p>
            <div class="header-actions">
                <button class="button-primary" type="submit" name="action" value="save-spam"><?= admin_l('Сохранить правила') ?></button>
                <a class="button" href="<?= site_escape($updatesUrl . '#update-spam') ?>"><?= site_escape($spamLabels[0]) ?></a>
            </div>
        </form>
    <?php endif; ?>
<?php elseif ($section === 'messages'): ?>
    <?php require __DIR__ . '/messages-view.php'; ?>
<?php elseif ($section === 'reviews'): ?>
    <?php require __DIR__ . '/reviews-view.php'; ?>
<?php elseif ($section === 'callbacks'): ?>
    <?php require __DIR__ . '/callbacks-view.php'; ?>
<?php elseif ($section === 'purchases'): ?>
    <?php require __DIR__ . '/purchases-view.php'; ?>
<?php elseif ($section === 'iblocks'): ?>
    <?php require __DIR__ . '/iblocks-view.php'; ?>
<?php elseif ($section === 'captcha'): ?>
    <?php require __DIR__ . '/captcha-view.php'; ?>
<?php elseif ($section === 'maintenance'): ?>
    <?php require __DIR__ . '/maintenance-view.php'; ?>
<?php elseif ($section === 'modules'): ?>
    <?php require __DIR__ . '/modules-view.php'; ?>
<?php elseif ($section === 'find'): ?>
    <?php require __DIR__ . '/settings-search.php'; ?>
<?php elseif ($section === 'help'): ?>
    <?php require __DIR__ . '/help-view.php'; ?>
<?php endif; ?>
