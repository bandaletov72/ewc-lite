<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}
?>
<!doctype html>
<html lang="<?= site_escape($adminLanguage) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= site_escape(SITE_PRODUCT_NAME) ?> | <?= site_escape($siteSettings['name']) ?></title>
    <link rel="icon" type="image/svg+xml" href="<?= site_escape(site_url($base, 'templates/product-icon.php') . '?v=' . site_update_version()) ?>">
    <link rel="stylesheet" href="<?= site_escape(site_font_awesome_stylesheet($base)) ?>">
    <?php if ($loggedIn || count($regional['languages']) > 1): ?>
        <script src="<?= site_escape(site_url($base, 'templates/search-browser.php') . '?v=' . site_update_version()) ?>" defer></script>
    <?php endif; ?>
    <link rel="stylesheet" href="<?= site_escape(site_url($base, 'templates/css/site.css') . '?v=' . site_update_version() . '.' . filemtime(SITE_ROOT . '/templates/css/site.css')) ?>">
    <link rel="stylesheet" href="<?= site_escape(site_url($base, 'templates/theme.php')) ?>" data-admin-theme>
    <?php if ($loggedIn && $section === 'shop' && ($_GET['tab'] ?? '') === 'sales'
        && site_library_settings(site_read_record(SITE_LIBRARIES_FILE))['enabled']['chartjs']): ?>
        <?= site_library_assets('chartjs', site_active_libraries()['chartjs'], $base) ?>
    <?php endif; ?>
    <?php if ($loggedIn): ?>
        <script src="<?= site_escape(site_url($base, 'templates/copy-code-script.php') . '?v=' . site_update_version()) ?>" defer></script>
    <?php endif; ?>
    <?php if ($loggedIn && $section === 'pages'): ?>
        <script src="<?= site_escape(site_url($base, 'templates/iblock-file-picker-script.php') . '?v=' . site_update_version()) ?>" defer></script>
    <?php endif; ?>
    <?php if ($loggedIn && $section === 'pages' && empty($creating) && !empty($page['revision'])): ?>
        <script src="<?= site_escape(site_url($base, 'templates/js/site.js') . '?v=' . site_update_version()) ?>" defer></script>
    <?php endif; ?>
    <?php if ($loggedIn && $section === 'fonts'): ?>
        <link rel="stylesheet" href="<?= site_escape(site_url($base, 'templates/font-previews.php') . '?v=' . site_update_version()) ?>">
    <?php endif; ?>
    <?php if ($loggedIn && in_array($section, ['pages', 'components', 'page_templates', 'shop'], true)): ?>
        <script src="<?= site_escape(site_url($base, 'templates/icon-picker.php') . '?v=' . site_update_version()) ?>" defer></script>
    <?php endif; ?>
    <?php if ($loggedIn && ($dataViewSuffix !== '' || in_array($section, ['pages', 'page_templates', 'mail_templates', 'forms', 'iblocks', 'contacts', 'cities', 'components', 'theme', 'libraries', 'region', 'updates', 'spam', 'design', 'maintenance', 'layout', 'domains', 'shop', 'users'], true))): ?>
        <?php
        $messages = [];
        foreach (require SITE_ROOT . '/app/admin-js-keys.php' as $key) {
            $messages[$key] = admin_t($key);
        }
        ?>
        <script id="admin-messages" type="application/json"><?= json_encode($messages, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?></script>
        <script src="<?= site_escape(site_url($base, 'templates/js/admin.js') . '?v=' . site_update_version() . '.' . filemtime(SITE_ROOT . '/templates/js/admin.js')) ?>" defer></script>
    <?php endif; ?>
    <?php if ($loggedIn && in_array($section, ['pages', 'components', 'shop'], true)): ?>
        <script src="<?= site_escape(site_url($base, 'templates/content-editor.php') . '?v=' . site_update_version()) ?>" defer></script>
    <?php endif; ?>
    <?php if ($loggedIn && site_code_editor_on()): ?>
        <script src="<?= site_escape(site_code_editor_url($base)) ?>" defer></script>
    <?php endif; ?>
</head>
<body data-code-auto="<?= admin_l('Авто') ?>" data-code-syntax="<?= admin_l('Язык подсветки') ?>">
    <?php if ($loggedIn && $section === 'pages'): ?>
        <?php
        $languageLabels = site_localized_help(require SITE_ROOT . '/app/help-page-languages.php', $adminLanguage);
        $languageLabels = $languageLabels[$adminLanguage] ?? $languageLabels['ru'];
        ?>
    <?php endif; ?>
    <?php if ($loggedIn && $dataViewSuffix !== ''): ?>
        <main class="focused-data-editor"<?= $embeddedData ? ' data-component-data-frame' : '' ?>>
            <?php if ($embeddedData && $notice !== ''): ?><span data-component-data-saved hidden></span><?php endif; ?>
            <?php if ($focusedPage): ?>
                <?php if ($error !== ''): ?><p class="notice error" role="alert"><?= admin_l($error) ?></p><?php endif; ?>
                <?php if ($notice !== ''): ?><p class="notice success" role="status"><?= admin_l($notice) ?></p><?php endif; ?>
                <?php if ($page !== null): ?>
                    <section class="panel" data-page-admin data-focused-page-editor data-admin-url="<?= site_escape($adminUrl) ?>" data-csrf="<?= site_escape($token) ?>">
                        <p data-page-feedback class="notice" role="status" hidden></p>
                        <?php require __DIR__ . '/page-editor-view.php'; ?>
                    </section>
                <?php endif; ?>
            <?php elseif ($focusedBanner): ?>
                <?php if ($error !== ''): ?><p class="notice error" role="alert"><?= admin_l($error) ?></p><?php endif; ?>
                <?php if ($notice !== ''): ?><p class="notice success" role="status"><?= admin_l($notice) ?></p><?php endif; ?>
                <?php require __DIR__ . '/banners-view.php'; ?>
            <?php elseif ($focusedMedia): ?>
                <section class="panel form-stack" data-page-admin data-admin-url="<?= site_escape($adminUrl) ?>" data-csrf="<?= site_escape($token) ?>">
                    <div data-media-context>
                        <?php $resolvedMediaFolder = '/' . site_asset_path($mediaFolder, $mediaKind === 'files' ? 'files' : 'images', true) . '/'; ?>
                        <textarea hidden><?= site_escape('[' . $mediaKind . ']' . $mediaFolder . '[/' . $mediaKind . ']') ?></textarea>
                        <?php require __DIR__ . '/component-media-view.php'; ?>
                    </div>
                </section>
            <?php elseif ($focusedLayout): ?>
                <?php if (!empty($layoutError) || $error !== ''): ?>
                    <p class="notice error" role="alert"><?= admin_l($layoutError ?? $error) ?></p>
                <?php elseif ($notice !== ''): ?>
                    <p class="notice" role="status"><?= admin_l($notice) ?></p>
                <?php endif; ?>
                <?php if ($layoutDraft !== null): require __DIR__ . '/domain-templates-view.php'; endif; ?>
            <?php else: ?>
                <?php require __DIR__ . '/config-view.php'; ?>
            <?php endif; ?>
        </main>
        </body></html>
        <?php return; ?>
    <?php endif; ?>
    <header class="site-header admin-header">
        <div class="container header-row">
            <a class="brand product-brand" href="<?= site_escape($adminUrl) ?>" aria-label="<?= site_escape(SITE_PRODUCT_NAME) ?>">
                <?= site_product_spiral('admin-product-symbol') ?>
                <span class="admin-product-initials" aria-hidden="true">EWC</span>
                <span class="admin-product-meta">
                    <span class="admin-product-edition" aria-hidden="true">CMS Lite</span>
                    <small class="admin-product-version">v.<?= site_escape(site_update_version()) ?></small>
                </span>
            </a>
            <div class="header-actions admin-header-actions">
                <?php if (count($regional['languages']) > 1): ?>
                    <details class="admin-language-picker">
                        <summary class="admin-header-button button" title="<?= admin_l('Язык') ?>">
                            <i class="fa-solid fa-language" aria-hidden="true"></i><span class="admin-nav-label"><?= site_escape(site_languages()[$adminLanguage]) ?></span>
                        </summary>
                        <nav class="site-menu" aria-label="<?= admin_l('Язык') ?>">
                            <?php foreach ($regional['languages'] as $code): ?>
                                <?php $params = ['ui_lang' => $code] + array_diff_key($_GET, ['check' => true]); ?>
                                <a lang="<?= $code ?>" href="<?= site_escape($adminUrl . '?' . http_build_query($params)) ?>"<?= $code === $adminLanguage ? ' aria-current="true"' : '' ?>><?= site_escape(site_languages()[$code]) ?></a>
                            <?php endforeach; ?>
                        </nav>
                    </details>
                <?php endif; ?>
                <a class="button admin-header-button" href="<?= site_escape(site_url($base)) ?>" title="<?= admin_l('Открыть сайт') ?>">
                    <i class="fa-solid fa-globe" aria-hidden="true"></i><span class="admin-nav-label"><?= admin_l('Открыть сайт') ?></span>
                </a>
                <?php if ($loggedIn): ?>
                    <form method="post" action="<?= site_escape($adminUrl) ?>">
                        <input type="hidden" name="action" value="logout">
                        <input type="hidden" name="csrf" value="<?= site_escape($token) ?>">
                        <button class="admin-header-button" type="submit" title="<?= admin_l('Выйти') ?>">
                            <i class="fa-solid fa-arrow-right-from-bracket" aria-hidden="true"></i><span class="admin-nav-label"><?= admin_l('Выйти') ?></span>
                        </button>
                    </form>
                    <details class="site-search-compact admin-settings-search"<?= $section === 'find' ? ' open' : '' ?>>
                        <summary class="admin-header-button button" aria-label="<?= admin_l('Поиск настроек') ?>" title="<?= admin_l('Поиск настроек') ?>">
                            <i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
                        </summary>
                        <form method="get" action="<?= site_escape($adminUrl) ?>" class="site-search" role="search" data-admin-search>
                            <input type="hidden" name="find" value="1">
                            <input type="search" name="q" aria-label="<?= admin_l('Поиск настроек') ?>" aria-controls="admin-search-suggestions" autocomplete="off"
                                placeholder="<?= admin_l('Поиск настроек') ?>" maxlength="100" required
                                value="<?= $section === 'find' ? site_escape(site_search_query($_GET['q'] ?? '')) : '' ?>">
                            <ul class="site-search-suggestions" id="admin-search-suggestions" hidden></ul>
                        </form>
                    </details>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <main class="container admin-main">
        <?php if ($accessError !== ''): ?>
            <section class="panel login-panel">
                <h1><?= admin_l('Настройка входа') ?></h1>
                <p class="notice error" role="alert"><?= admin_l($accessError) ?></p>
            </section>
        <?php elseif (!$configured): ?>
            <section class="panel login-panel">
                <p class="hint"><?= admin_l('Шаг 1 из 2') ?></p>
                <h1><?= admin_l('Мастер установки') ?></h1>
                <h2><?= admin_l('Создать учётную запись') ?></h2>
                <?php if ($error !== ''): ?>
                    <p class="notice error" role="alert"><?= admin_l($error) ?></p>
                <?php endif; ?>
                <?php if ($recoveryError !== ''): ?>
                    <p class="notice error" role="alert"><?= site_escape($recoveryError) ?></p>
                <?php endif; ?>
                <?php if ($setupAvailable): ?>
                    <form method="post" action="<?= site_escape($adminUrl) ?>" class="form-stack">
                        <input type="hidden" name="action" value="setup">
                        <input type="hidden" name="csrf" value="<?= site_escape($token) ?>">
                        <?php if ($needsCode): ?>
                            <div class="field">
                                <label for="setup-code"><?= admin_l('Одноразовый код установки') ?></label>
                                <input id="setup-code" name="setup_code" autocomplete="off" required>
                            </div>
                        <?php endif; ?>
                        <div class="field">
                            <label for="setup-username"><?= admin_l('Логин') ?></label>
                            <input id="setup-username" name="username" value="<?= site_escape($username) ?>" placeholder="admin" autocomplete="username" maxlength="64" required>
                        </div>
                        <div class="field">
                            <label for="setup-password"><?= admin_l('Пароль') ?></label>
                            <input id="setup-password" type="password" name="password" autocomplete="new-password" minlength="8" aria-describedby="password-hint" required>
                            <small class="hint" id="password-hint"><?= admin_l('От 8 символов, не более 72 байт.') ?></small>
                        </div>
                        <div class="field">
                            <label for="setup-confirmation"><?= admin_l('Повторите пароль') ?></label>
                            <input id="setup-confirmation" type="password" name="confirmation" autocomplete="new-password" required>
                        </div>
                        <label class="field">
                            <?= site_escape($recoveryLabels['email']) ?>
                            <input type="email" name="email" value="<?= site_escape($setupEmail) ?>" autocomplete="email" maxlength="254">
                            <small class="hint"><?= site_escape($recoveryLabels['setup-hint']) ?></small>
                        </label>
                        <label class="field">
                            <?= site_escape($recoveryLabels['url']) ?>
                            <input type="url" name="recovery_url" value="<?= site_escape($setupRecoveryUrl) ?>" maxlength="2048" placeholder="https://example.com/">
                            <small class="hint"><?= site_escape($recoveryLabels['url-hint']) ?></small>
                        </label>
                        <div><button class="button-primary" type="submit"><?= admin_l('Создать учётную запись и продолжить') ?></button></div>
                    </form>
                <?php else: ?>
                    <?php
                    $ftpSetupText = require SITE_ROOT . '/app/help-ftp-setup.php';
                    $ftpSetupText = $ftpSetupText[$adminLanguage] ?? $ftpSetupText['ru'];
                    ?>
                    <div class="form-stack">
                        <div><a class="button button-primary" href="<?= site_escape($ftpResetUrl) ?>" download><?= site_escape($ftpSetupText[0]) ?></a></div>
                        <ol>
                            <li><?= site_escape($ftpSetupText[1]) ?></li>
                            <li><?= sprintf(site_escape($ftpSetupText[2]), '<code>.access.php</code>', '<code>admin/</code>') ?></li>
                            <li><?= site_escape($ftpSetupText[3]) ?></li>
                        </ol>
                    </div>
                <?php endif; ?>
            </section>
        <?php elseif ($recoveryMode !== '' && (!$loggedIn || $recoveryMode !== 'forgot')): ?>
            <?php require __DIR__ . '/password-recovery-view.php'; ?>
        <?php elseif (!$loggedIn): ?>
            <section class="panel login-panel">
                <h1><?= admin_l('Вход в админку') ?></h1>
                <?php if ($recoveryNotice !== ''): ?>
                    <p class="notice" role="status"><?= site_escape($recoveryLabels[$recoveryNotice]) ?></p>
                <?php endif; ?>
                <?php if ($error !== ''): ?>
                    <p class="notice error" role="alert"><?= admin_l($error) ?></p>
                <?php endif; ?>
                <form method="post" action="<?= site_escape($adminUrl) ?>" class="form-stack">
                    <input type="hidden" name="action" value="login">
                    <input type="hidden" name="csrf" value="<?= site_escape($token) ?>">
                    <div class="field">
                            <label for="username"><?= admin_l('Логин') ?></label>
                        <input id="username" name="username" value="<?= site_escape($username) ?>" autocomplete="username" required>
                    </div>
                    <div class="field">
                        <label for="password"><?= admin_l('Пароль') ?></label>
                        <input id="password" type="password" name="password" autocomplete="current-password" required>
                    </div>
                    <div class="header-actions">
                        <button class="button-primary" type="submit"><?= admin_l('Войти') ?></button>
                        <a href="<?= site_escape($forgotUrl) ?>"><?= site_escape($recoveryLabels['forgot-title']) ?></a>
                    </div>
                </form>
            </section>
        <?php elseif ($section === 'install'): ?>
            <?php require __DIR__ . '/installation-view.php'; ?>
        <?php else: ?>
            <?php if ($installationPending): ?>
                <p class="notice"><a href="<?= site_escape($installationUrl) ?>"><?= admin_l('Завершить первичную настройку сайта') ?></a></p>
            <?php endif; ?>
            <?php require __DIR__ . '/navigation-view.php'; ?>
            <?php if ($section !== 'pages'): ?>
                <?php require __DIR__ . '/config-view.php'; ?>
            <?php else: ?>
            <?php
            $sidebarLabels = require SITE_ROOT . '/app/help-page-sidebar.php';
            list($hideSidebar, $showSidebar) = $sidebarLabels[$adminLanguage] ?? $sidebarLabels['ru'];
            ?>
            <div class="admin-heading">
                <h1><?= admin_l('Страницы') ?></h1>
                <a class="button button-primary" data-new-root-section href="<?= site_escape($adminUrl . '?new=1') ?>"><i class="fa-solid fa-plus" aria-hidden="true"></i> <?= site_escape($languageLabels[9]) ?></a>
            </div>
            <p data-page-feedback class="notice" role="status" hidden></p>
            <div class="editor-grid" data-page-admin data-admin-url="<?= site_escape($adminUrl) ?>" data-csrf="<?= site_escape($token) ?>">
                <nav class="panel" id="admin-page-sidebar" aria-label="<?= admin_l('Папки и страницы') ?>">
                    <div class="page-sidebar-actions">
                        <button type="button" data-page-sidebar-toggle="hide" aria-controls="admin-page-sidebar" aria-expanded="true"
                            aria-label="<?= site_escape($hideSidebar) ?>" title="<?= site_escape($hideSidebar) ?>" hidden>
                            <i class="fa-solid fa-arrow-left" aria-hidden="true"></i>
                        </button>
                    </div>
                    <div data-page-tree>
                        <?= admin_page_tree_html($tree, $creating ? '' : ($page['path'] ?? ''), $base, $adminUrl) ?>
                    </div>
                </nav>
                <section class="panel">
                    <button type="button" data-page-sidebar-toggle="show" aria-controls="admin-page-sidebar" aria-expanded="false" hidden>
                        <i class="fa-solid fa-arrow-right" aria-hidden="true"></i> <?= site_escape($showSidebar) ?>
                    </button>
                    <?php if ($error !== ''): ?>
                        <p class="notice error" role="alert"><?= admin_l($error) ?></p>
                    <?php elseif ($notice !== ''): ?>
                        <p class="notice" role="status"><?= admin_l($notice) ?></p>
                    <?php endif; ?>
                    <?php if ($page !== null && !$creating && in_array($pageOperation, ['hide', 'show', 'delete'], true)
                        && ($pageOperation !== 'delete' || $page['path'] !== 'index.php')): ?>
                        <?php
                        $address = site_page_url('', $page['path']);
                        $editUrl = $adminUrl . '?' . http_build_query(['page' => $address]);
                        $operationLabel = ['hide' => admin_t('Скрыть страницу'), 'show' => admin_t('Опубликовать страницу'), 'delete' => admin_t('Удалить страницу')];
                        ?>
                        <h2><?= $operationLabel[$pageOperation] ?>?</h2>
                        <p><?= site_escape($page['fields']['Title'] . ' · ' . $address) ?></p>
                        <?php if ($pageOperation === 'delete'): ?>
                            <p class="hint"><?= admin_l('Файл страницы будет удалён. Остальные файлы в её папке останутся.') ?></p>
                        <?php elseif ($pageOperation === 'hide'): ?>
                            <p class="hint"><?= admin_l('Страница исчезнет из меню и будет недоступна по прямой ссылке, пока вы её не опубликуете.') ?></p>
                        <?php endif; ?>
                        <form class="header-actions" method="post" action="<?= site_escape($editUrl . '&confirm=' . $pageOperation) ?>">
                            <input type="hidden" name="action" value="change-page-status">
                            <input type="hidden" name="csrf" value="<?= site_escape($token) ?>">
                            <input type="hidden" name="path" value="<?= site_escape($address) ?>">
                            <input type="hidden" name="revision" value="<?= site_escape($page['revision']) ?>">
                            <input type="hidden" name="operation" value="<?= $pageOperation ?>">
                            <button class="<?= $pageOperation === 'delete' ? 'button-danger' : 'button-primary' ?>" type="submit"><?= $operationLabel[$pageOperation] ?></button>
                            <a class="button" href="<?= site_escape($editUrl) ?>"><?= admin_l('Отмена') ?></a>
                        </form>
                    <?php elseif ($page !== null): ?>
                        <?php require __DIR__ . '/page-editor-view.php'; ?>
                    <?php endif; ?>
                </section>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </main>
    <footer class="site-footer admin-footer">
        <div class="container footer-layout">
            <p class="admin-footer-author"><?= admin_l('Автор') ?>: <?= site_escape(SITE_PRODUCT_AUTHOR) ?></p>
            <a class="admin-footer-github" href="<?= site_escape(SITE_PRODUCT_REPOSITORY) ?>"><i class="fa-brands fa-github" aria-hidden="true"></i> GitHub</a>
        </div>
    </footer>
</body>
</html>
