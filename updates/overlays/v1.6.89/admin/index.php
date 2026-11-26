<?php
define('SITE_ROOT', dirname(__DIR__));
require SITE_ROOT . '/app/site.php';
require __DIR__ . '/files.php';
require __DIR__ . '/local-assets.php';
require __DIR__ . '/access.php';
require __DIR__ . '/password-recovery.php';
require __DIR__ . '/config.php';
require __DIR__ . '/php-lint.php';
require __DIR__ . '/integration-config.php';
require __DIR__ . '/form-config.php';
require __DIR__ . '/iblock-config.php';
require __DIR__ . '/page-custom-fields.php';
require __DIR__ . '/appearance-config.php';
require __DIR__ . '/branding.php';
require __DIR__ . '/installation.php';
require __DIR__ . '/callback-config.php';
require __DIR__ . '/save.php';
require __DIR__ . '/media.php';
require __DIR__ . '/image-library.php';
require __DIR__ . '/banners.php';
require __DIR__ . '/inbox.php';
require __DIR__ . '/reviews.php';
require __DIR__ . '/page-tree.php';
require __DIR__ . '/maintenance-config.php';
require __DIR__ . '/maintenance-labels.php';
require __DIR__ . '/editor-labels.php';
require __DIR__ . '/service-files.php';
require __DIR__ . '/content-backup.php';
require __DIR__ . '/content-cleanup.php';
require __DIR__ . '/sales.php';
require __DIR__ . '/maintenance-jobs.php';
require __DIR__ . '/captcha-config.php';
require __DIR__ . '/live-editor.php';
require __DIR__ . '/settings-catalog.php';
require __DIR__ . '/cities.php';
require __DIR__ . '/page-templates.php';
require __DIR__ . '/design-themes.php';
require __DIR__ . '/update-progress.php';
require __DIR__ . '/update-overview.php';
require SITE_ROOT . '/app/updater.php';

function admin_input(array $values, string $key): string
{
    return isset($values[$key]) && is_string($values[$key]) ? $values[$key] : '';
}

function admin_json(int $status, array $result)
{
    http_response_code($status);
    header('Content-Type: application/json; charset=UTF-8');
    if (is_string($result['message'] ?? null)) {
        $result['message'] = admin_t($result['message']);
    }
    echo json_encode(['ok' => $status === 200] + $result, JSON_UNESCAPED_UNICODE);
    exit;
}

$base = site_base('admin/index.php');
$adminUrl = site_url($base, 'admin/');
$forgotUrl = $adminUrl . '?forgot=1';
$ftpResetUrl = $adminUrl . '?ftp-reset=1';
$recoveryMode = isset($_GET['reset']) ? 'reset' : (isset($_GET['verify-email']) ? 'verify-email'
    : (admin_input($_GET, 'forgot') === '1' ? 'forgot' : ''));
$recoveryToken = $recoveryMode === 'forgot' || $recoveryMode === '' ? '' : admin_input($_GET, $recoveryMode);
$recoveryNotice = '';
$recoveryError = '';
$recoveryDraft = [];
$setupEmail = '';
$setupRecoveryUrl = '';
$settingsUrl = $adminUrl . '?settings=1';
$contactsUrl = $adminUrl . '?contacts=1';
$citiesUrl = $adminUrl . '?cities=1';
$pageTemplatesUrl = $adminUrl . '?page_templates=1';
$themeUrl = $adminUrl . '?theme=1';
$fontsUrl = $adminUrl . '?fonts=1';
$componentsUrl = $adminUrl . '?components=1';
$updatesUrl = $adminUrl . '?updates=1';
$backupsUrl = $adminUrl . '?backups=1';
$shopUrl = $adminUrl . '?shop=1';
$usersUrl = $adminUrl . '?users=1';
$snippetsUrl = $adminUrl . '?snippets=1';
$domainsUrl = $adminUrl . '?domains=1';
$formsUrl = $adminUrl . '?forms=1';
$spamUrl = $adminUrl . '?spam=1';
$messagesUrl = $adminUrl . '?messages=1';
$reviewsUrl = $adminUrl . '?reviews=1';
$callbacksUrl = $adminUrl . '?callbacks=1';
$purchasesUrl = $adminUrl . '?purchases=1';
$iblocksUrl = $adminUrl . '?iblocks=1';
$helpUrl = $adminUrl . '?help=1';
$assetsUrl = $adminUrl . '?assets=1';
$layoutEditorUrl = $adminUrl . '?layout=1';
$designUrl = $adminUrl . '?design=1';
$headersUrl = $adminUrl . '?headers=1';
$functionsUrl = $adminUrl . '?functions=1';
$librariesUrl = $adminUrl . '?libraries=1';
$regionUrl = $adminUrl . '?region=1';
$integrationsUrl = $adminUrl . '?integrations=1';
$maintenanceUrl = $adminUrl . '?maintenance=1';
$captchaUrl = $adminUrl . '?captcha=1';
$modulesUrl = $adminUrl . '?modules=1';
$mapsDraft = null;
$installationUrl = $adminUrl . '?install=1';
$section = 'pages';
foreach (['settings', 'contacts', 'cities', 'page_templates', 'design', 'theme', 'fonts', 'components', 'updates', 'backups', 'shop', 'users', 'snippets', 'domains', 'forms', 'spam', 'messages', 'reviews', 'callbacks', 'purchases', 'iblocks', 'help', 'assets', 'layout', 'headers', 'functions', 'libraries', 'region', 'integrations', 'maintenance', 'captcha', 'modules', 'install', 'find'] as $name) {
    if (admin_input($_GET, $name) === '1') {
        $section = $name;
        break;
    }
}
$siteSettings = site_settings();
$regional = site_region_settings();
$regionDraft = $regional;
$regionRevision = admin_settings_revision($regional);
$adminLanguage = $regional['interface'];
$settingsDraft = $siteSettings;
$settingsRevision = admin_settings_revision($siteSettings);
$variablesDraft = admin_variables_text($siteSettings['variables']);
$appearanceDraft = null;
$assetsDraft = null;
$headersDraft = null;
$libraryDraft = null;
$installationDraft = null;
$installationRevision = '';
$pageAssets = null;
$callbackDraft = null;
$error = '';
$accessError = '';
$page = null;
$creating = admin_input($_GET, 'new') === '1';
$pageOperation = admin_input($_GET, 'confirm');
$loggedIn = false;
$username = '';
$auth = null;

try {
    $auth = admin_auth_state();
} catch (RuntimeException $exception) {
    $accessError = $exception->getMessage();
}
$configured = isset($auth['password']);
$installationPending = ($auth['setupPending'] ?? false) === true;
$localSetup = admin_local_setup() && !isset($auth['setup']);
$needsCode = !$localSetup;
$setupAvailable = $auth !== null && ($localSetup || isset($auth['setup']));
$currentHost = site_host();
$domains = [];
$domainsError = '';
$domainDraft = '';
$layoutSelection = $section === 'layout' ? 'shared' : admin_input($_GET, 'templates');
$layoutHost = $layoutSelection === 'current' ? $currentHost : site_normalize_domain($layoutSelection);
$layoutUrl = $section === 'layout' ? $layoutEditorUrl : $domainsUrl . '&templates=' . rawurlencode($layoutSelection);
$layoutDraft = null;
$snippet = null;
$snippetError = '';
$snippetEntries = [];
$formDraft = null;
$formEditorError = '';
$spamDraft = null;
$spamEditorError = '';
$formSelection = admin_input($_GET, 'id');
$iblockDraft = null;
$iblockEditorError = '';
$iblockIsNew = admin_input($_GET, 'new') === '1';
$iblockTextMode = admin_input($_GET, 'text') === '1';
$focusedBanner = $section === 'components' && admin_input($_GET, 'banner') !== ''
    && (admin_input($_GET, 'embed') === '1' || admin_input($_GET, 'wide') === '1');
$mediaKind = admin_input($_GET, 'media');
$mediaFolder = admin_input($_GET, 'folder');
$focusedMedia = $section === 'pages' && in_array($mediaKind, ['gal', 'fotos', 'files'], true)
    && (admin_input($_GET, 'embed') === '1' || admin_input($_GET, 'wide') === '1')
    && site_asset_path($mediaFolder, $mediaKind === 'files' ? 'files' : 'images') !== '';
$selectedModule = $section === 'modules' ? admin_input($_GET, 'module') : '';
$focusedModule = site_valid_snippet($selectedModule) && (admin_input($_GET, 'embed') === '1' || admin_input($_GET, 'wide') === '1');
$selectedLayout = in_array($section, ['layout', 'domains'], true) ? admin_input($_GET, 'template') : '';
$focusedLayout = $selectedLayout !== '' && isset(site_layout_blocks()[$selectedLayout]) && admin_input($_GET, 'embed') === '1';
$focusedPage = $section === 'pages' && !$creating && $pageOperation === '' && admin_input($_GET, 'page') !== ''
    && admin_input($_GET, 'embed') === '1';
$canFocusData = $focusedBanner || $focusedMedia || $focusedModule || $focusedLayout || $focusedPage || ($section === 'iblocks' && !$iblockIsNew && site_valid_iblock_name(admin_input($_GET, 'id')))
    || ($section === 'forms' && site_valid_form_id($formSelection))
    || ($section === 'snippets' && site_valid_snippet(admin_input($_GET, 'snippet')))
    || in_array($section, ['callbacks', 'contacts', 'integrations', 'settings', 'reviews'], true);
$embeddedData = $canFocusData && admin_input($_GET, 'embed') === '1';
$dataViewSuffix = $embeddedData ? '&embed=1' : ($canFocusData && admin_input($_GET, 'wide') === '1' ? '&wide=1' : '');
$iblockViewSuffix = $section === 'iblocks' ? $dataViewSuffix : '';
$formsUrl .= $section === 'forms' ? $dataViewSuffix : '';
$callbacksUrl .= $section === 'callbacks' ? $dataViewSuffix : '';
$integrationsUrl .= $section === 'integrations' ? $dataViewSuffix : '';
$settingsUrl .= $section === 'settings' ? $dataViewSuffix : '';
$contactsUrl .= $section === 'contacts' ? $dataViewSuffix : '';
$reviewsUrl .= $section === 'reviews' ? $dataViewSuffix : '';
if ($focusedModule) $modulesUrl .= '&module=' . rawurlencode($selectedModule) . $dataViewSuffix;
if ($focusedLayout) $layoutUrl .= '&template=' . rawurlencode($selectedLayout) . $dataViewSuffix . '#layout-' . rawurlencode($selectedLayout);
$iblockCsv = null;
$domainSelection = admin_input($_GET, 'domain');
$sharedScope = admin_input($_GET, 'scope') === 'shared';
$scopeHost = $sharedScope ? '' : ($domainSelection !== '' ? site_normalize_domain($domainSelection) : $currentHost);
$scopeUrl = $snippetsUrl . ($section === 'snippets' ? $dataViewSuffix : '');
if ($sharedScope) {
    $scopeUrl .= '&scope=shared';
} elseif ($domainSelection !== '') {
    $scopeUrl .= '&domain=' . rawurlencode($domainSelection);
}

header('Content-Type: text/html; charset=UTF-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: no-referrer');
if (admin_input($_GET, 'ftp-reset') === '1' && ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET') {
    header('Content-Type: text/html; charset=UTF-8');
    header('Content-Disposition: attachment; filename="ewc-ftp-password-reset.html"');
    require __DIR__ . '/ftp-reset-download.php';
    exit;
}
$fontStyleOrigin = $section === 'libraries' ? ' https://fonts.googleapis.com' : '';
$fontOrigin = $section === 'libraries' ? ' https://fonts.gstatic.com' : '';
$editorFrame = in_array($section, ['pages', 'components', 'libraries', 'shop'], true) ? " frame-src 'self';" : '';
$frameAncestors = $embeddedData ? "'self'" : "'none'";
$adminPolicy = "default-src 'none'; style-src 'self'{$fontStyleOrigin}; img-src 'self'; font-src 'self'{$fontOrigin}; script-src 'self'; connect-src 'self'; form-action 'self'; base-uri 'none'; frame-ancestors {$frameAncestors};{$editorFrame}";
header('Content-Security-Policy: ' . $adminPolicy);

if ($auth !== null) {
    $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_name('FILE_SITE_ADMIN');
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_set_cookie_params(0, site_url($base), '', $secure, true);
    if (!session_start()) {
        http_response_code(503);
        exit('Не удалось открыть сессию. Проверьте настройки PHP.');
    }

    $loggedIn = $configured && isset($_SESSION['auth']) && hash_equals(admin_auth_key($auth), $_SESSION['auth']);
    if ($loggedIn) {
        site_install_privacy_page();
        ew_install_functions_file();
        try {
            admin_ensure_error_pages($base);
        } catch (RuntimeException $exception) {
            error_log('Error pages: ' . $exception->getMessage());
        }
    }
    if ($loggedIn && empty($_SESSION['public_scope'])) {
        setcookie('FILE_SITE_ADMIN', '', 1, $adminUrl, '', $secure, true);
        setcookie('FILE_SITE_ADMIN', session_id(), 0, site_url($base), '', $secure, true);
        $_SESSION['public_scope'] = true;
    }
    if (!isset($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    $token = $_SESSION['csrf'];
    $requestedLanguage = admin_input($_GET, 'ui_lang');
    if (in_array($requestedLanguage, $regional['languages'], true)) {
        $_SESSION['ui_language'] = $requestedLanguage;
    }
    if (in_array($_SESSION['ui_language'] ?? '', $regional['languages'], true)) {
        $adminLanguage = $_SESSION['ui_language'];
    }
    $recoveryTranslations = require SITE_ROOT . '/app/help-admin-recovery.php';
    $recoveryLabels = $recoveryTranslations[$adminLanguage] ?? $recoveryTranslations['ru'];
    $recoveryNotice = $_SESSION['recovery_notice'] ?? '';
    unset($_SESSION['recovery_notice']);

    if ($section === 'maintenance' && admin_input($_GET, 'phpinfo') === '1'
        && ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET') {
        if (!$loggedIn) {
            http_response_code(403);
            exit(admin_l('Войдите в админку.'));
        }
        session_write_close();
        if (!is_callable('phpinfo')) {
            http_response_code(503);
            exit(site_escape(site_php_admin_labels($adminLanguage)[3]));
        }
        header("Content-Security-Policy: default-src 'none'; style-src 'unsafe-inline'; img-src data:; frame-ancestors 'none'");
        header('X-Robots-Tag: noindex, nofollow');
        phpinfo(INFO_GENERAL | INFO_CONFIGURATION | INFO_MODULES);
        exit;
    }

    if ($section === 'pages' && isset($_GET['preset_data']) && ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET') {
        if (!$loggedIn) admin_json(403, []);
        session_write_close();
        try {
            $preset = admin_page_templates($adminLanguage)[admin_input($_GET, 'preset_data')] ?? null;
        } catch (RuntimeException $exception) {
            admin_json(500, []);
        }
        admin_json($preset === null ? 404 : 200, ['preset' => $preset]);
    }

    if ($section === 'find' && admin_input($_GET, 'suggest') === '1' && ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET') {
        if (!$loggedIn) {
            admin_json(403, ['suggestions' => []]);
        }
        session_write_close();
        admin_json(200, ['suggestions' => admin_find_settings(site_search_query($_GET['q'] ?? ''), $adminUrl, $adminLanguage, 6)]);
    }

    if (in_array($section, ['updates', 'spam'], true) && admin_input($_GET, 'check') === '1' && ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET') {
        if (!$loggedIn) {
            admin_json(403, ['message' => 'Войдите в админку заново.']);
        }
        session_write_close();
        $repository = site_update_github_repo();
        if ($repository === '' && $section === 'spam') {
            admin_json(503, ['message' => 'Источник обновлений не настроен.']);
        }
        if ($section === 'spam') {
            try {
                $latest = site_update_github_spam_version($repository);
                $local = site_spam_version();
                admin_json(200, ['version' => $latest, 'outdated' => $latest !== '' && version_compare($latest, $local, '>')]);
            } catch (RuntimeException $exception) {
                admin_json(503, []);
            }
        }
        $overview = admin_update_overview($repository, admin_input($_GET, 'theme'));
        header('Cache-Control: private, max-age=60');
        admin_json(200, $overview + ['version' => $overview['engine']['version'], 'notes' => $overview['engine']['notes']]);
    }

    if (in_array($section, ['maintenance', 'backups'], true) && isset($_GET['download_backup']) && ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET') {
        $file = $loggedIn ? admin_backup_path(admin_input($_GET, 'download_backup')) : '';
        if ($file === '') {
            http_response_code(404);
            exit;
        }
        session_write_close();
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        header('Cache-Control: private, no-store');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit;
    }

    if ($loggedIn && ($section === 'domains' || $section === 'snippets')) {
        try {
            $domains = site_read_record(SITE_DOMAINS_FILE);
        } catch (RuntimeException $exception) {
            $domainsError = $exception->getMessage();
        }
    }
    $scopeValid = $sharedScope || ($scopeHost !== ''
        && ($domainSelection === '' || in_array($scopeHost, $domains, true)));
    $layoutValid = $domainsError === '' && ($layoutSelection === 'shared'
        || ($layoutHost !== '' && ($layoutSelection === 'current' || in_array($layoutHost, $domains, true))));
    if ($layoutSelection === 'shared') {
        $layoutHost = '';
    }

    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
        $action = admin_input($_POST, 'action');
        $streamUpdate = admin_input($_POST, 'update_progress') === '1'
            && ((in_array($action, ['update-engine', 'refresh-spam'], true) && $section === 'updates')
                || ($action === 'design-update' && in_array($section, ['design', 'updates'], true))
                || ($action === 'index-pages' && $section === 'maintenance')
                || ($action === 'shop-package' && $section === 'shop'));
        if ($streamUpdate && (!$loggedIn || !hash_equals($token, admin_input($_POST, 'csrf')))) {
            admin_json(403, ['message' => admin_t($loggedIn ? 'Сессия формы истекла. Обновите страницу.' : 'Войдите в админку заново.')]);
        }
        if (in_array($action, ['editor-history', 'create-iblock-file'], true)) {
            if (!$loggedIn) admin_json(403, ['message' => 'Войдите в админку заново.']);
            if (!hash_equals($token, admin_input($_POST, 'csrf'))) {
                admin_json(403, ['message' => 'Сессия формы истекла. Обновите страницу.']);
            }
            if ($action === 'editor-history') {
                require __DIR__ . '/editor-history.php';
                admin_handle_editor_history($_POST);
            }
            try {
                admin_json(200, admin_create_iblock_file(admin_input($_POST, 'name'), admin_input($_POST, 'view')));
            } catch (RuntimeException $exception) {
                admin_json(400, ['message' => $exception->getMessage()]);
            }
        }
        if ($loggedIn && hash_equals($token, admin_input($_POST, 'csrf'))) {
            admin_refresh_index_after_edit($action);
        }
        $iblockAjax = $section === 'iblocks' && in_array($action, ['save-iblock', 'save-iblock-text'], true)
            && admin_input($_POST, 'ajax') === '1';
        $pagesAjax = $section === 'pages' && in_array($action, ['page-menu', 'page-order', 'page-move', 'folder-move', 'change-page-status'], true)
            && admin_input($_POST, 'ajax') === '1';
        $mediaAjax = $section === 'pages' && in_array($action, ['list-media', 'upload-media', 'delete-media', 'delete-media-folder', 'order-media', 'create-media-folder'], true)
            && admin_input($_POST, 'ajax') === '1';
        if (($iblockAjax || $pagesAjax || $mediaAjax) && !$loggedIn) {
            admin_json(403, ['message' => 'Войдите в админку заново.']);
        }
        if (!hash_equals($token, admin_input($_POST, 'csrf'))) {
            if ($iblockAjax || $pagesAjax || $mediaAjax) {
                admin_json(403, ['message' => 'Сессия формы истекла. Обновите страницу.']);
            }
            http_response_code(403);
            $error = 'Сессия формы истекла. Обновите страницу и повторите действие.';
        } elseif ($configured && $action === 'forgot-password' && $recoveryMode === 'forgot') {
            try {
                admin_request_password_reset(trim(admin_input($_POST, 'email')), $recoveryLabels);
            } catch (RuntimeException $exception) {
                error_log('EWC: could not start password recovery.');
            }
            $_SESSION['recovery_notice'] = 'request-notice';
            header('Location: ' . $forgotUrl, true, 303);
            exit;
        } elseif ($configured && $action === 'complete-recovery' && in_array($recoveryMode, ['reset', 'verify-email'], true)) {
            try {
                admin_complete_recovery($recoveryMode, $recoveryToken,
                    admin_input($_POST, 'password'), admin_input($_POST, 'confirmation'));
                if ($recoveryMode === 'reset') {
                    unset($_SESSION['auth'], $_SESSION['public_scope']);
                    session_regenerate_id(true);
                    $_SESSION['csrf'] = bin2hex(random_bytes(32));
                }
                $_SESSION['recovery_notice'] = $recoveryMode === 'reset' ? 'password-updated' : 'email-confirmed';
                header('Location: ' . ($recoveryMode === 'verify-email' && $loggedIn ? $settingsUrl : $adminUrl), true, 303);
                exit;
            } catch (InvalidArgumentException $exception) {
                $recoveryError = $recoveryLabels[$exception->getMessage()];
            } catch (RuntimeException $exception) {
                $error = $exception->getMessage();
            }
        } elseif ($loggedIn && $section === 'settings' && $action === 'save-recovery-email') {
            $recoveryDraft = ['email' => trim(admin_input($_POST, 'email')),
                'url' => trim(admin_input($_POST, 'recovery_url'))];
            try {
                $sent = admin_change_recovery_email($auth, admin_input($_POST, 'current_password'),
                    $recoveryDraft['email'], $recoveryDraft['url'], $recoveryLabels);
                $_SESSION['recovery_notice'] = $sent ? 'confirmation-sent' : 'mail-failed';
                header('Location: ' . $settingsUrl . '#recovery-email', true, 303);
                exit;
            } catch (InvalidArgumentException $exception) {
                $recoveryError = $recoveryLabels[$exception->getMessage()];
            } catch (RuntimeException $exception) {
                $error = $exception->getMessage();
            }
        } elseif ($action === 'setup' && !$configured && $setupAvailable) {
            $username = trim(admin_input($_POST, 'username'));
            $setupEmail = trim(admin_input($_POST, 'email'));
            $setupRecoveryUrl = trim(admin_input($_POST, 'recovery_url'));
            try {
                $recovery = [];
                if ($setupEmail !== '') {
                    list($verificationToken, $recovery) = admin_prepare_recovery_email($setupEmail, $setupRecoveryUrl);
                }
                $auth = admin_install($username, admin_input($_POST, 'password'),
                    admin_input($_POST, 'confirmation'), admin_input($_POST, 'setup_code'), $localSetup, $recovery);
                if ($recovery) {
                    $sent = admin_recovery_mail($recovery['email_pending'], 'verify-email', $verificationToken, $recoveryLabels);
                    $_SESSION['recovery_notice'] = $sent ? 'confirmation-sent' : 'mail-failed';
                }
                admin_sign_in($auth, $installationUrl);
            } catch (InvalidArgumentException $exception) {
                $recoveryError = $recoveryLabels[$exception->getMessage()];
            } catch (RuntimeException $exception) {
                $error = $exception->getMessage();
            }
        } elseif ($action === 'login' && $configured && !$loggedIn) {
            $username = trim(admin_input($_POST, 'username'));
            $password = admin_input($_POST, 'password');
            if ($password !== '' && password_verify($password, $auth['password']) && hash_equals($auth['user'], $username)) {
                admin_sign_in($auth, $installationPending ? $installationUrl : $adminUrl);
            }
            usleep(300000);
            $error = 'Неверный логин или пароль.';
        } elseif ($loggedIn && $installationPending && $section === 'install'
            && in_array($action, ['finish-installation', 'skip-installation'], true)) {
            $installationDraft = [];
            foreach (['name', 'description', 'footer', 'mode', 'preset'] as $key) {
                $installationDraft[$key] = trim(admin_input($_POST, $key));
            }
            $installationRevision = admin_input($_POST, 'revision');
            try {
                admin_finish_installation($auth, $installationDraft, $_FILES['logo'] ?? [],
                    $installationRevision, $action === 'skip-installation');
                $_SESSION['notice'] = 'Первичная настройка сайта завершена.';
                header('Location: ' . $adminUrl, true, 303);
                exit;
            } catch (RuntimeException $exception) {
                $error = $exception->getMessage();
            }
        } elseif ($loggedIn && $section === 'purchases' && $action === 'purchase-status') {
            try {
                admin_change_purchase_status(admin_input($_POST, 'purchase'), admin_input($_POST, 'state'), admin_input($_POST, 'revision'));
                $_SESSION['notice'] = admin_t('Настройки сохранены.');
                $purchasePage = max(1, min(100000, (int) admin_input($_GET, 'p')));
                header('Location: ' . $purchasesUrl . '&p=' . $purchasePage, true, 303);
                exit;
            } catch (RuntimeException $exception) {
                $error = $exception->getMessage();
            }
        } elseif ($action === 'logout' && $loggedIn) {
            $_SESSION = [];
            session_regenerate_id(true);
            header('Location: ' . $adminUrl, true, 303);
            exit;
        } elseif ($loggedIn && $section === 'backups' && $action === 'save-backup-jobs') {
            try {
                admin_save_schedule('backupSchedule', $_POST, ['backup'], $adminLanguage);
                $_SESSION['notice'] = 'Настройки сохранены.';
                header('Location: ' . $backupsUrl . '#backup-schedule', true, 303);
                exit;
            } catch (RuntimeException $exception) {
                $error = $exception->getMessage();
            }
        } elseif ($loggedIn && in_array($section, ['maintenance', 'backups'], true) && in_array($action, ['create-content-backup', 'import-content-backup', 'restore-content-backup'], true)) {
            try {
                $name = admin_backup_action($action, $_POST, $_FILES['archive'] ?? []);
                $_SESSION['notice'] = admin_t($action === 'restore-content-backup' ? 'Содержимое сайта восстановлено.' : 'Резервная копия сохранена.') . ' ' . $name;
                header('Location: ' . $backupsUrl . '#content-backups', true, 303);
                exit;
            } catch (RuntimeException $exception) {
                $error = $exception->getMessage();
            }
        } elseif ($loggedIn && $section === 'maintenance' && $action === 'remove-unused-media') {
            try {
                $_SESSION['notice'] = admin_t('Удалено файлов') . ' — ' . admin_remove_unused_media($_POST);
                header('Location: ' . $maintenanceUrl . '&scan_media=1#unused-media', true, 303);
                exit;
            } catch (RuntimeException $exception) {
                $error = $exception->getMessage();
            }
        } elseif ($loggedIn && $section === 'maintenance' && $action === 'save-redirects') {
            try {
                admin_save_redirect_rules($_POST);
                $_SESSION['notice'] = 'Настройки сохранены.';
                header('Location: ' . $maintenanceUrl . '#redirects', true, 303);
                exit;
            } catch (RuntimeException $exception) {
                $error = $exception->getMessage();
            }
        } elseif ($loggedIn && $section === 'maintenance' && in_array($action, ['save-service-file', 'generate-sitemap', 'restore-htaccess'], true)) {
            try {
                admin_save_service_file($action, $_POST, $base);
                $_SESSION['notice'] = 'Настройки сохранены.';
                $anchor = strpos(admin_input($_POST, 'file'), 'error/') === 0 ? '#error-pages' : '#service-files';
                header('Location: ' . $maintenanceUrl . $anchor, true, 303);
                exit;
            } catch (RuntimeException $exception) {
                $error = $exception->getMessage();
            }
        } elseif ($loggedIn && $section === 'maintenance' && in_array($action, ['index-pages', 'save-cache', 'clear-cache', 'save-jobs'], true)) {
            if ($streamUpdate) {
                admin_update_stream(static function (callable $progress): string {
                    return admin_maintenance('index-pages', $_POST, $progress);
                }, 'index', $adminLanguage);
            }
            try {
                if ($action === 'save-jobs') {
                    admin_save_jobs($_POST);
                    $_SESSION['notice'] = admin_l('Настройки сохранены.');
                } else {
                    $_SESSION['notice'] = admin_maintenance($action, $_POST);
                }
                header('Location: ' . $maintenanceUrl, true, 303);
                exit;
            } catch (RuntimeException $exception) {
                $error = $exception->getMessage();
            }
        } elseif ($loggedIn && (($section === 'updates' && $action === 'design-update') || ($section === 'design'
            && in_array($action, ['design-create', 'design-apply', 'design-copy', 'design-save', 'design-reset', 'design-update'], true)))) {
            if ($streamUpdate) {
                admin_update_stream(static function (callable $progress) use ($action, $adminLanguage) {
                    admin_design_action($action, $_POST, $adminLanguage, $progress);
                    return admin_update_progress_labels($adminLanguage)['theme-done'];
                }, 'theme', $adminLanguage);
            }
            try {
                list($designId, $designPath) = admin_design_action($action, $_POST, $adminLanguage);
                $_SESSION['notice'] = 'Настройки сохранены.';
                $destination = array_filter(['theme' => $designId, 'file' => $designPath], 'strlen');
                header('Location: ' . ($section === 'updates' ? $updatesUrl . '#update-theme'
                    : $designUrl . ($destination ? '&' . http_build_query($destination) : '')), true, 303);
                exit;
            } catch (RuntimeException $exception) {
                $error = $exception->getMessage();
            }
        } elseif ($loggedIn && $section === 'layout' && $action === 'create-layout-template') {
            $newLayout = trim(admin_input($_POST, 'template_name'));
            try {
                if (admin_input($_POST, 'design_scope') !== site_design_active()) throw new RuntimeException(site_design_labels($adminLanguage)[10]);
                admin_add_layout_template($newLayout, trim(admin_input($_POST, 'template_label')));
                $_SESSION['notice'] = 'Настройки сохранены.';
                header('Location: ' . $layoutEditorUrl . '#layout-' . rawurlencode($newLayout), true, 303);
                exit;
            } catch (RuntimeException $exception) {
                $error = $exception->getMessage();
            }
        } elseif ($loggedIn && $section === 'captcha' && $action === 'save-captcha') {
            try {
                admin_save_captcha($_POST);
                $_SESSION['notice'] = admin_l('Настройки сохранены.');
                header('Location: ' . $captchaUrl, true, 303);
                exit;
            } catch (RuntimeException $exception) {
                $error = $exception->getMessage();
            }
        } elseif ($loggedIn && $section === 'functions' && $action === 'save-functions') {
            try {
                admin_save_functions(admin_input($_POST, 'code'), admin_input($_POST, 'revision'));
                $_SESSION['notice'] = admin_t('Функции сохранены.');
                header('Location: ' . $functionsUrl, true, 303);
                exit;
            } catch (RuntimeException $exception) {
                $error = $exception->getMessage();
            }
        } elseif ($loggedIn && $section === 'modules' && in_array($action, ['save-module', 'save-module-source'], true)) {
            try {
                if ($action === 'save-module-source') {
                    admin_save_module_source($_POST);
                } else {
                    admin_save_module($_POST);
                }
                $_SESSION['notice'] = admin_l('Настройки сохранены.');
                header('Location: ' . $modulesUrl, true, 303);
                exit;
            } catch (RuntimeException $exception) {
                $error = $exception->getMessage();
            }
        } elseif ($loggedIn && $section === 'pages' && in_array($action, ['public-edit-mode', 'live-page-menu', 'live-component', 'live-page-asset'], true)) {
            try {
                admin_live_edit($action, $_POST, $base, $focusedPage);
            } catch (RuntimeException $exception) {
                http_response_code(409);
                $error = $exception->getMessage();
            }
        } elseif ($mediaAjax) {
            admin_component_media($action, $_POST, $base);
        } elseif ($pagesAjax) {
            admin_pages_ajax($action, $_POST, $base, $adminUrl);
        } elseif ($action === 'save-page-custom-definitions' && $loggedIn && $section === 'pages') {
            try {
                admin_save_page_custom_definitions($_POST);
                $_SESSION['notice'] = 'Настройки сохранены.';
                $path = admin_page_path(admin_input($_POST, 'path'));
                header('Location: ' . admin_page_edit_url($adminUrl, $path) . ($focusedPage ? $dataViewSuffix : '') . '#page-custom-fields', true, 303);
                exit;
            } catch (RuntimeException $exception) {
                $error = $exception->getMessage();
            }
        } elseif ($action === 'save' && $loggedIn && $section === 'pages') {
            $creating = admin_input($_POST, 'new') === '1';
            $enteredPath = admin_input($_POST, 'path');
            $path = admin_page_path($enteredPath);
            $fields = [];
            foreach (site_page_fields() as $name) {
                if ($name === 'Keywords' && !$creating && !isset($_POST[$name])) {
                    continue;
                }
                $fields[$name] = trim(admin_input($_POST, $name), ' ');
            }
            if (admin_input($_POST, 'menu_visibility') === '1') {
                $fields['MenuVisible'] = admin_input($_POST, 'MenuVisible') === '1' ? '1' : '0';
                if (isset($_POST['MobileMenu'])) {
                    $fields['MobileMenu'] = admin_input($_POST, 'MobileMenu');
                }
            }
            $legacyDiscovery = admin_input($_POST, 'page_discovery') === '1';
            if (admin_input($_POST, 'page_sitemap') === '1' || $legacyDiscovery) {
                $fields['Sitemap'] = admin_input($_POST, 'Sitemap') === '1' ? '1' : '0';
            }
            if (admin_input($_POST, 'page_coordinates') === '1' || $legacyDiscovery) {
                $fields['Lat'] = str_replace(',', '.', trim(admin_input($_POST, 'Lat')));
                $fields['Lng'] = str_replace(',', '.', trim(admin_input($_POST, 'Lng')));
            }
            if (admin_input($_POST, 'page_menu_expand') === '1') {
                $fields['MenuExpand'] = admin_input($_POST, 'MenuExpand') === '1' ? '1' : '0';
            }
            if (isset($_POST['Redirect'])) {
                $fields['Redirect'] = trim(admin_input($_POST, 'Redirect'));
            }
            if (admin_input($_POST, 'page_activity') === '1') {
                $fields['Active'] = admin_input($_POST, 'Active') === '1' ? '1' : '0';
                $fields['ActiveFrom'] = admin_input($_POST, 'ActiveFrom');
                $fields['ActiveUntil'] = admin_input($_POST, 'ActiveUntil');
            }
            $html = admin_input($_POST, 'html');
            $revision = admin_input($_POST, 'revision');
            $page = ['path' => $path, 'fields' => $fields, 'html' => $html, 'revision' => $revision];

            try {
                if (isset($_POST['announcement'])) {
                    $announcement = admin_input($_POST, 'announcement');
                    $announcementLabels = require SITE_ROOT . '/app/help-announcements.php';
                    $announcementLabels = $announcementLabels[$adminLanguage] ?? $announcementLabels['ru'];
                    $fields['AnnouncementImage'] = trim(admin_input($_POST, 'AnnouncementImage'));
                    $fields['AnnouncementIcon'] = trim(admin_input($_POST, 'AnnouncementIcon'));
                    $page['fields'] += $fields;
                    $page['announcementDraft'] = $announcement;
                    if (strlen($announcement) > SITE_ASSET_LIMIT || strpos($announcement, "\0") !== false || preg_match('//u', $announcement) !== 1) {
                        throw new RuntimeException($announcementLabels[9]);
                    }
                    $fields['Announcement'] = base64_encode($announcement);
                    if (($fields['AnnouncementImage'] !== '' && (strlen($fields['AnnouncementImage']) > 1500 || site_map_url($fields['AnnouncementImage']) === ''))
                        || ($fields['AnnouncementIcon'] !== '' && site_parse_page_icon($fields['AnnouncementIcon']) === null)) {
                        throw new RuntimeException($announcementLabels[10]);
                    }
                }
                if (is_array($_POST['sale'] ?? null) && site_page_translation($path)[1] === '') {
                    $fields['Sale'] = admin_page_sale($_POST['sale'], $path);
                    $page['fields']['Sale'] = $fields['Sale'];
                }
                if (is_array($_POST['assets'] ?? null)) {
                    $pageAssets = ['CSS' => admin_input($_POST['assets'], 'CSS'), 'JS' => admin_input($_POST['assets'], 'JS')];
                    foreach ($pageAssets as $kind => $code) {
                        $fields[$kind] = admin_encode_asset($code);
                    }
                }
                admin_save_page($path, $fields, $html, $revision, $creating, $_POST, $base);
                $_SESSION['notice'] = 'Страница сохранена.';
                $editorTab = admin_input($_POST, 'editor_tab');
                header('Location: ' . $adminUrl . '?' . http_build_query(['page' => site_page_url('', $path)])
                    . ($focusedPage ? $dataViewSuffix : '')
                    . (in_array($editorTab, ['announcement', 'settings', 'seo', 'maps', 'sale', 'languages', 'custom-fields', 'components'], true) ? '#page-' . $editorTab : ''), true, 303);
                exit;
            } catch (RuntimeException $exception) {
                $error = $exception->getMessage();
                $page['customDraft'] = is_array($_POST['custom_values'] ?? null) ? $_POST['custom_values'] : [];
                $page['unsavedDraft'] = true;
                if ($creating) {
                    $page['path'] = $enteredPath;
                } else {
                    $previous = site_read_page($path);
                    $page['fields'] += $previous['fields'] ?? ['Hidden' => '0'];
                }
            }
        } elseif ($action === 'page-move' && $loggedIn && $section === 'pages') {
            try {
                $path = admin_move_page(admin_input($_POST, 'path'), admin_input($_POST, 'revision'), admin_input($_POST, 'folder') ?: admin_input($_POST, 'existing_folder'));
                $_SESSION['notice'] = 'Страница перемещена.';
                header('Location: ' . admin_page_edit_url($adminUrl, $path), true, 303);
                exit;
            } catch (RuntimeException $exception) {
                $error = $exception->getMessage();
            }
        } elseif ($action === 'change-page-status' && $loggedIn && $section === 'pages') {
            $path = admin_page_path(trim(admin_input($_POST, 'path')));
            $operation = admin_input($_POST, 'operation');
            try {
                $_SESSION['notice'] = admin_update_page_status($path, admin_input($_POST, 'revision'), $operation);
                $destination = $operation === 'delete' ? $adminUrl : $adminUrl . '?' . http_build_query(['page' => site_page_url('', $path)]);
                header('Location: ' . $destination, true, 303);
                exit;
            } catch (RuntimeException $exception) {
                $error = $exception->getMessage();
            }
        } elseif ($action === 'save-assets' && $loggedIn && $section === 'assets') {
            $values = is_array($_POST['assets'] ?? null) ? $_POST['assets'] : [];
            $assetsDraft = ['CSS' => admin_input($values, 'CSS'), 'JS' => admin_input($values, 'JS')];
            $assetsRevision = admin_input($_POST, 'revision');
            try {
                admin_save_global_assets($assetsDraft, $assetsRevision);
                $_SESSION['notice'] = 'Стили и скрипты сохранены.';
                header('Location: ' . $assetsUrl, true, 303);
                exit;
            } catch (RuntimeException $exception) {
                $error = $exception->getMessage();
            }
        } elseif ($action === 'save-region' && $loggedIn && $section === 'region') {
            $choices = is_array($_POST['languages'] ?? null) ? $_POST['languages'] : [];
            $regionDraft['languages'] = ['ru'];
            foreach (array_keys(site_languages()) as $code) {
                if ($code !== 'ru' && in_array($code, $choices, true)) {
                    $regionDraft['languages'][] = $code;
                }
            }
            foreach (array_keys($regionDraft) as $key) {
                if ($key !== 'languages' && ($key !== 'country' || isset($_POST['country']))) {
                    $regionDraft[$key] = admin_input($_POST, $key);
                }
            }
            try {
                admin_save_region($regionDraft, admin_input($_POST, 'revision'));
                $_SESSION['ui_language'] = $regionDraft['interface'];
                $_SESSION['notice'] = 'Региональные настройки сохранены.';
                header('Location: ' . $regionUrl, true, 303);
                exit;
            } catch (RuntimeException $exception) {
                $error = $exception->getMessage();
            }
        } elseif ($loggedIn && $section === 'libraries' && preg_match('/\Alocalize-library:([a-z]+)\z/', $action, $operation)) {
            try {
                admin_localize_library($operation[1]);
                $_SESSION['notice'] = 'Настройки сохранены.';
                header('Location: ' . $librariesUrl, true, 303);
                exit;
            } catch (RuntimeException $exception) {
                $error = $exception->getMessage();
            }
        } elseif ($loggedIn && $section === 'libraries' && preg_match('/\A(library-update|library-rollback):([a-z]+)\z/', $action, $operation)) {
            try {
                $_SESSION['notice'] = admin_change_library_version($operation[2], $operation[1] === 'library-rollback');
                header('Location: ' . $librariesUrl, true, 303);
                exit;
            } catch (RuntimeException $exception) {
                $error = $exception->getMessage();
            }
        } elseif ($loggedIn && $section === 'fonts' && ($action === 'save-fonts' || strpos($action, 'localize-font:') === 0)) {
            try {
                if ($action === 'save-fonts') admin_save_fonts($_POST, admin_input($_POST, 'revision'));
                else admin_localize_font(substr($action, strlen('localize-font:')));
                $_SESSION['notice'] = 'Настройки сохранены.';
                header('Location: ' . $fontsUrl, true, 303);
                exit;
            } catch (RuntimeException $exception) {
                $error = $exception->getMessage();
            }
        } elseif ($action === 'save-libraries' && $loggedIn && $section === 'libraries') {
            $libraryInputs = is_array($_POST['enabled'] ?? null) ? $_POST['enabled'] : [];
            $enabled = [];
            foreach (site_library_catalog() as $id => $library) {
                $enabled[$id] = admin_input($libraryInputs, $id) === '1';
            }
            $libraryRevision = admin_input($_POST, 'revision');
            try {
                $legacyFont = site_library_settings(site_read_record(SITE_LIBRARIES_FILE))['font'];
                $libraryDraft = site_library_settings(['enabled' => $enabled, 'font' => $legacyFont]);
                admin_save_library_settings($libraryDraft, $libraryRevision);
                $_SESSION['notice'] = 'Настройки сохранены.';
                header('Location: ' . $librariesUrl, true, 303);
                exit;
            } catch (RuntimeException $exception) {
                $error = $exception->getMessage();
            }
        } elseif ($action === 'save-headers' && $loggedIn && $section === 'headers') {
            $headersDraft = ['head' => admin_input($_POST, 'head'), 'bodyEnd' => admin_input($_POST, 'bodyEnd')];
            $headersRevision = admin_input($_POST, 'revision');
            try {
                admin_save_header_inserts($headersDraft, $headersRevision);
                $_SESSION['notice'] = 'Служебные заголовки сохранены.';
                header('Location: ' . $headersUrl, true, 303);
                exit;
            } catch (RuntimeException $exception) {
                $error = $exception->getMessage();
            }
        } elseif ($action === 'save-page-template' && $loggedIn && $section === 'page_templates') {
            $pageTemplateDraft = admin_page_template_draft($_POST);
            $pageTemplateDraftId = admin_input($_POST, 'template');
            try {
                $id = admin_save_page_template($pageTemplateDraftId, $pageTemplateDraft, admin_input($_POST, 'revision'));
                $_SESSION['notice'] = 'Настройки сохранены.';
                header('Location: ' . $pageTemplatesUrl . '&template=' . $id . '#template-edit', true, 303);
                exit;
            } catch (RuntimeException $exception) {
                $error = $exception->getMessage();
            }
        } elseif ($action === 'save-city' && $loggedIn && $section === 'cities') {
            $cityDraft = admin_city_draft($_POST);
            $cityDraftId = admin_input($_POST, 'city');
            try {
                if (admin_input($_POST, 'delete_city') === '1') {
                    admin_delete_city($cityDraftId, admin_input($_POST, 'revision'));
                    $_SESSION['notice'] = 'Настройки сохранены.';
                    header('Location: ' . $citiesUrl, true, 303);
                    exit;
                }
                $id = admin_save_city($cityDraftId, $cityDraft, admin_input($_POST, 'revision'), $currentHost, $adminLanguage);
                $_SESSION['notice'] = 'Настройки сохранены.';
                header('Location: ' . $citiesUrl . '&city=' . $id, true, 303);
                exit;
            } catch (RuntimeException $exception) {
                $error = $exception->getMessage();
            }
        } elseif ($loggedIn && $section === 'shop' && strpos($action, 'shop-') === 0) {
            try {
                require_once SITE_ROOT . '/app/shop-package.php';
                if (in_array($action, ['shop-toggle','shop-check','shop-package','shop-remove'], true)) {
                    if ($action === 'shop-package' && $streamUpdate) {
                        $source = $_SESSION['shopRelease'] ?? [];
                        admin_update_stream(static function (callable $progress) use ($source): string {
                            return site_shop_update_from_github($source, $progress);
                        }, 'shop', $adminLanguage);
                    }
                    if (in_array($action, ['shop-check','shop-package'], true)) @set_time_limit(90);
                    admin_shop_module($_POST);
                } elseif (site_shop_enabled()) {
                    require_once SITE_ROOT . '/shop/module.php';
                    require_once SITE_ROOT . '/shop/admin.php';
                    require_once SITE_ROOT . '/shop/import.php';
                    shop_admin_action($_POST, $_FILES, true);
                } else throw new RuntimeException('Модуль отключён');
                if ($action !== 'shop-check') $_SESSION['notice'] = 'Настройки сохранены.';
                $shopKeys = ['tab','sheet','q','category','active','p'];
                if (!in_array($action, ['shop-product-save','shop-product-delete'], true)) $shopKeys[] = 'product';
                $shopQuery = array_intersect_key($_GET, array_flip($shopKeys));
                header('Location: ' . $shopUrl . ($shopQuery ? '&' . http_build_query($shopQuery) : '')
                    . (in_array($action, ['shop-check','shop-package'], true) ? '#shop-github' : ''), true, 303);
                exit;
            } catch (RuntimeException $exception) {
                $error = $exception->getMessage();
            }
        } elseif ($loggedIn && $section === 'users' && $action === 'account-save') {
            try {
                require_once SITE_ROOT . '/app/users.php';
                $user = site_account_save($_POST, admin_input($_POST, 'id'), true);
                $_SESSION['notice'] = 'Настройки сохранены.';
                header('Location: ' . $usersUrl . '&user=' . $user['id'], true, 303);
                exit;
            } catch (RuntimeException $exception) {
                $error = $exception->getMessage();
            }
        } elseif ($loggedIn && $section === 'settings' && $action === 'account-registration') {
            try {
                $lock = admin_lock();
                try {
                    $accountSettings = site_account_settings();
                    $accountSettings['registration'] = admin_input($_POST,'registration') === '1';
                    $accountSettings['mail'] = site_message_template(is_array($_POST['mail'] ?? null) ? $_POST['mail'] : [], site_account_mail_defaults());
                    admin_write_record(SITE_ROOT . '/admin/.account-settings.php', $accountSettings, 262144);
                }
                finally { fclose($lock); }
                $_SESSION['notice'] = 'Настройки сохранены.';
                header('Location: ' . $settingsUrl . '#account-registration', true, 303);
                exit;
            } catch (RuntimeException $exception) {
                $error = $exception->getMessage();
            }
        } elseif ($action === 'save-settings' && $loggedIn && $section === 'settings') {
            foreach (['name', 'description', 'footer'] as $name) {
                $settingsDraft[$name] = trim(admin_input($_POST, $name));
            }
            if (isset($_POST['subtitle'])) {
                $settingsDraft['subtitle'] = trim(admin_input($_POST, 'subtitle'));
            }
            $variablesDraft = admin_input($_POST, 'variables');
            $settingsRevision = admin_input($_POST, 'revision');
            try {
                if (admin_input($_POST, 'breadcrumb_option') === '1') {
                    $settingsDraft['breadcrumbs'] = admin_input($_POST, 'breadcrumbs') === '1';
                }
                if (isset($_POST['search_mode'])) {
                    $settingsDraft['searchMode'] = admin_input($_POST, 'search_mode');
                }
                if (isset($_POST['public_url'])) {
                    $settingsDraft['publicUrl'] = trim(admin_input($_POST, 'public_url'));
                }
                if (isset($_POST['error_reporting'])) {
                    $settingsDraft['errorReporting'] = admin_input($_POST, 'error_reporting');
                }
                if (isset($_POST['scroll_animation'])) {
                    $settingsDraft['scrollAnimation'] = admin_input($_POST, 'scroll_animation');
                }
                if (isset($_POST['message_recipients'])) {
                    $settingsDraft['messageRecipients'] = admin_input($_POST, 'message_recipients');
                }
                $fields = array_intersect_key($settingsDraft, array_flip(['name', 'subtitle', 'description', 'footer', 'breadcrumbs', 'searchMode', 'publicUrl', 'errorReporting', 'scrollAnimation', 'messageRecipients']));
                admin_save_settings($fields, $variablesDraft, $settingsRevision);
                $_SESSION['notice'] = 'Настройки сохранены.';
                header('Location: ' . $settingsUrl, true, 303);
                exit;
            } catch (RuntimeException $exception) {
                $error = $exception->getMessage();
            }
        } elseif ($loggedIn && $section === 'settings' && in_array($action, ['save-brand', 'generate-favicon'], true)) {
            try {
                admin_save_brand($_FILES, $action === 'generate-favicon', admin_input($_POST, 'revision'));
                $_SESSION['notice'] = $action === 'generate-favicon' ? 'Favicon создан и применён.' : 'Изображения сохранены.';
                header('Location: ' . $settingsUrl, true, 303);
                exit;
            } catch (RuntimeException $exception) {
                $error = $exception->getMessage();
            }
        } elseif ($loggedIn && $section === 'theme' && in_array($action, [
            'save-theme', 'save-theme-preset', 'apply-theme-preset', 'default-theme-preset',
            'delete-theme-preset', 'restore-default-theme',
        ], true)) {
            $appearanceRevision = admin_input($_POST, 'revision');
            $themeName = trim(admin_input($_POST, 'theme_name'));
            if ($action === 'save-theme' || $action === 'save-theme-preset') {
                $appearanceDraft = admin_theme_draft($_POST);
            }
            try {
                $_SESSION['notice'] = admin_update_theme(
                    $action, $appearanceDraft ?? [], $themeName, admin_input($_POST, 'preset'), $appearanceRevision
                );
                header('Location: ' . $themeUrl, true, 303);
                exit;
            } catch (RuntimeException $exception) {
                $error = $exception->getMessage();
            }
        } elseif ($loggedIn && $action === 'image-library') {
            admin_image_library($_POST, $base);
        } elseif ($loggedIn && $section === 'components' && in_array($action, ['save-banner', 'delete-banner', 'move-banner', 'restore-banner'], true)) {
            try {
                $redirect = admin_save_banner($action, $_GET, $_POST, $base);
                if ($focusedBanner) {
                    $redirect = str_replace('#banner-editor', $dataViewSuffix . '#banner-editor', $redirect);
                }
                $_SESSION['notice'] = 'Настройки сохранены.';
                header('Location: ' . $redirect, true, 303);
                exit;
            } catch (RuntimeException $exception) {
                $error = $exception->getMessage();
            }
        } elseif ($loggedIn && (($action === 'save-contacts' && $section === 'contacts')
            || ($action === 'save-components' && $section === 'components'))) {
            $appearanceRevision = admin_input($_POST, 'revision');
            try {
                if ($section === 'contacts') {
                    $appearanceDraft = admin_contacts_draft($_POST);
                    admin_save_contacts($appearanceDraft, $appearanceRevision);
                    $redirect = $contactsUrl;
                } else {
                    $appearanceDraft = admin_components_draft($_POST);
                    admin_save_components($appearanceDraft, $appearanceRevision);
                    $redirect = $componentsUrl;
                }
                $_SESSION['notice'] = 'Настройки сохранены.';
                header('Location: ' . $redirect, true, 303);
                exit;
            } catch (RuntimeException $exception) {
                $error = $exception->getMessage();
            }
        } elseif ($loggedIn && $section === 'integrations' && $action === 'zadarma-test') {
            try {
                $_SESSION['notice'] = admin_test_zadarma($_POST);
                header('Location: ' . $integrationsUrl . '#zadarma-tests', true, 303);
                exit;
            } catch (RuntimeException $exception) {
                $error = $exception->getMessage();
            }
        } elseif ($loggedIn && $section === 'integrations' && in_array($action, ['save-maps', 'save-integrations'], true)) {
            try {
                if ($action === 'save-maps') {
                    foreach (array_keys(site_map_settings()) as $key) {
                        $mapsDraft[$key] = trim(admin_input($_POST, $key));
                    }
                    $mapsRevision = admin_input($_POST, 'revision');
                    $mapsDraft['cluster'] = $mapsDraft['cluster'] === '1' ? '1' : '0';
                    admin_save_maps($mapsDraft, $mapsRevision);
                    $_SESSION['notice'] = 'Настройки карт сохранены.';
                } else {
                    admin_save_integrations($_POST, admin_input($_POST, 'revision'));
                    $_SESSION['notice'] = 'Настройки внешних сервисов сохранены.';
                }
                header('Location: ' . $integrationsUrl, true, 303);
                exit;
            } catch (RuntimeException $exception) {
                $error = $exception->getMessage();
            }
        } elseif ($loggedIn && $section === 'reviews'
            && in_array($action, ['reviews-settings', 'review-approve', 'review-reject', 'review-edit', 'review-delete'], true)) {
            try {
                admin_review_action($action, $_POST, $adminLanguage);
                $_SESSION['notice'] = admin_t('Настройки сохранены.');
                header('Location: ' . $reviewsUrl, true, 303);
                exit;
            } catch (RuntimeException $exception) {
                $error = $exception->getMessage();
            }
        } elseif ($loggedIn && (in_array($section, ['callbacks', 'integrations'], true) && $action === 'save-callback-settings'
            || $section === 'callbacks' && $action === 'send-callback')) {
            try {
                if ($action === 'save-callback-settings') {
                    $callbackDraft = admin_callback_draft($_POST);
                    $callbackRevision = admin_input($_POST, 'revision');
                    admin_save_callback_settings($callbackDraft, $callbackRevision);
                    $_SESSION['notice'] = 'Настройки обратного звонка сохранены.';
                } else {
                    site_callback_dispatch(admin_input($_POST, 'callback_id'), site_callback_settings());
                    $_SESSION['notice'] = 'Заявка передана в телефонию.';
                }
                header('Location: ' . ($section === 'integrations' ? $integrationsUrl : $callbacksUrl), true, 303);
                exit;
            } catch (RuntimeException $exception) {
                $error = $exception->getMessage();
            }
        } elseif ($section === 'updates' && $loggedIn && in_array($action, ['save-update-jobs', 'refresh-spam'], true)) {
            if ($streamUpdate) {
                admin_update_stream(static function (callable $progress) use ($adminLanguage) {
                    $downloaded = admin_spam_github_presets($adminLanguage, $progress);
                    $progress('spam-save');
                    admin_save_spam([], null, $adminLanguage, $downloaded);
                    return site_spam_admin_labels($adminLanguage)[7];
                }, 'spam', $adminLanguage);
            }
            try {
                if ($action === 'save-update-jobs') {
                    admin_save_schedule('updateSchedule', $_POST, ['engine', 'theme', 'spam'], $adminLanguage);
                    $_SESSION['notice'] = 'Настройки сохранены.';
                } else {
                    @set_time_limit(90);
                    admin_save_spam([], null, $adminLanguage, admin_spam_github_presets($adminLanguage));
                    $_SESSION['notice'] = site_spam_admin_labels($adminLanguage)[7];
                }
                header('Location: ' . $updatesUrl . ($action === 'refresh-spam' ? '#update-spam' : '#update-schedule'), true, 303);
                exit;
            } catch (RuntimeException $exception) {
                $error = $exception->getMessage();
            }
        } elseif ($action === 'update-engine' && $loggedIn && $section === 'updates') {
            if ($streamUpdate) admin_update_stream('site_update_from_github', 'engine', $adminLanguage);
            try {
                @set_time_limit(90);
                $_SESSION['notice'] = site_update_from_github();
                header('Location: ' . $updatesUrl, true, 303);
                exit;
            } catch (RuntimeException $exception) {
                $error = $exception->getMessage();
            }
        } elseif ($action === 'save-domain-templates' && $loggedIn && in_array($section, ['domains', 'layout'], true) && $layoutValid) {
            $layoutDraft = admin_domain_template_draft(is_array($_POST['blocks'] ?? null) ? $_POST['blocks'] : []);
            $layoutRevision = admin_input($_POST, 'revision');
            try {
                if (admin_input($_POST, 'design_scope') !== site_design_active()) throw new RuntimeException(site_design_labels($adminLanguage)[10]);
                admin_save_domain_templates($layoutHost, $layoutDraft, $layoutRevision, admin_input($_POST, 'block_action'));
                $_SESSION['notice'] = 'Шаблоны сохранены.';
                header('Location: ' . $layoutUrl, true, 303);
                exit;
            } catch (RuntimeException $exception) {
                $error = $exception->getMessage();
            }
        } elseif ($action === 'add-domain' && $loggedIn && $section === 'domains') {
            $domainDraft = trim(admin_input($_POST, 'domain'));
            try {
                admin_add_domain($domainDraft);
                $_SESSION['notice'] = 'Домен добавлен.';
                header('Location: ' . $domainsUrl, true, 303);
                exit;
            } catch (RuntimeException $exception) {
                $error = $exception->getMessage();
            }
        } elseif ($action === 'save-form' && $loggedIn && $section === 'forms') {
            $formId = trim(admin_input($_POST, 'id'));
            $formRevision = admin_input($_POST, 'revision');
            $formDraft = admin_form_draft($_POST);
            try {
                admin_store_form($formId, $formDraft, $formRevision);
                $_SESSION['notice'] = 'Форма сохранена.';
                header('Location: ' . $formsUrl . '&id=' . rawurlencode($formId), true, 303);
                exit;
            } catch (RuntimeException $exception) {
                $error = $exception->getMessage();
            }
        } elseif (in_array($action, ['save-spam', 'refresh-spam'], true) && $loggedIn && $section === 'spam') {
            $spamDraft = ['words' => admin_input($_POST, 'words'), 'ips' => admin_input($_POST, 'ips')];
            if (array_key_exists('agents', $_POST)) $spamDraft['agents'] = admin_input($_POST, 'agents');
            $spamRevision = admin_input($_POST, 'revision');
            try {
                $refresh = $action === 'refresh-spam';
                if ($refresh) @set_time_limit(90);
                $downloaded = $refresh ? admin_spam_github_presets($adminLanguage) : null;
                admin_save_spam($spamDraft, $spamRevision, $adminLanguage, $downloaded);
                $_SESSION['notice'] = $refresh ? site_spam_admin_labels($adminLanguage)[7] : 'Правила сохранены.';
                header('Location: ' . $spamUrl, true, 303);
                exit;
            } catch (RuntimeException $exception) {
                $error = $exception->getMessage();
            }
        } elseif (in_array($action, ['save-iblock', 'save-iblock-text'], true) && $loggedIn && $section === 'iblocks') {
            $iblockId = admin_input($_POST, 'id');
            $iblockRevision = admin_input($_POST, 'revision');
            $iblockTextMode = $action === 'save-iblock-text';
            $iblockDraft = $iblockTextMode ? ['columns' => [], 'rows' => []] : admin_iblock_draft($_POST);
            if ($iblockTextMode) {
                $iblockCsv = admin_input($_POST, 'csv');
            }
            try {
                if ($iblockTextMode) {
                    $iblockDraft = site_iblock_from_csv($iblockCsv);
                } elseif (admin_input($_POST, 'complete') !== '1') {
                    throw new RuntimeException('Получены не все значения. Проверьте лимит max_input_vars в PHP перед сохранением.');
                }
                $savedRevision = admin_save_iblock($iblockId, $iblockDraft, $iblockRevision);
                $blockUrl = $iblocksUrl . '&id=' . rawurlencode($iblockId) . $iblockViewSuffix;
                if ($iblockAjax) {
                    admin_json(200, ['message' => 'Инфоблок сохранён.', 'id' => $iblockId, 'revision' => $savedRevision,
                        'url' => $blockUrl . ($iblockTextMode ? '&text=1' : ''),
                        'modeUrl' => $blockUrl . ($iblockTextMode ? '' : '&text=1'),
                        'shortcode' => admin_iblock_shortcode($iblockId)]);
                }
                $_SESSION['notice'] = 'Инфоблок сохранён.';
                header('Location: ' . $blockUrl . ($iblockTextMode ? '&text=1' : ''), true, 303);
                exit;
            } catch (RuntimeException $exception) {
                if ($iblockAjax) {
                    admin_json(422, ['message' => $exception->getMessage()]);
                }
                $error = $exception->getMessage();
            }
        } elseif (in_array($action, ['save-snippet', 'delete-snippet'], true)
            && $loggedIn && $section === 'snippets' && $scopeValid) {
            $snippetName = trim(admin_input($_POST, 'snippet'));
            if (preg_match('/\A\[(.*)\]\z/', $snippetName, $match)) {
                $snippetName = $match[1];
            }
            $revision = admin_input($_POST, 'revision');
            $values = [
                'description' => trim(admin_input($_POST, 'description')),
                'value' => admin_input($_POST, 'value'),
            ];
            $lockedName = admin_input($_POST, 'locked') === '1';
            $snippet = ['name' => $snippetName, 'values' => $values, 'revision' => $revision, 'locked' => $lockedName];
            $deleting = $action === 'delete-snippet';
            try {
                admin_update_snippet($scopeHost, $snippetName, $values, $revision, $deleting);
                $_SESSION['notice'] = $deleting ? 'Значение удалено.' : 'Подшаблон сохранён.';
                $redirect = $deleting ? $scopeUrl : $scopeUrl . '&snippet=' . rawurlencode($snippetName);
                header('Location: ' . $redirect, true, 303);
                exit;
            } catch (RuntimeException $exception) {
                $error = $exception->getMessage();
            }
        }
    }

    if ($loggedIn && $section === 'install') {
        if (!$installationPending) {
            header('Location: ' . $adminUrl, true, 303);
            exit;
        }
        if ($installationDraft === null) {
            $installationDraft = $siteSettings + ['mode' => site_theme_settings()['mode'], 'preset' => ''];
            $installationRevision = admin_installation_revision();
        }
    }
} else {
    http_response_code(503);
}

if ($loggedIn) {
    admin_install_language_packs();
    if ($section === 'shop' && ($_GET['columns_set'] ?? '') === '1') {
        $columns = is_array($_GET['columns'] ?? null) ? $_GET['columns'] : [];
        $_SESSION['shopProductColumns'] = array_values(array_intersect(['barcode','image','category','stock','status','type','created'],
            array_filter($columns, 'is_string')));
    }
    $notice = $_SESSION['notice'] ?? '';
    unset($_SESSION['notice']);
    session_write_close();
    if ($section === 'theme') {
        $themeState = site_theme_state();
        $themeName = $themeName ?? '';
        if ($appearanceDraft === null) {
            $appearanceDraft = $themeState['theme'];
            $appearanceRevision = admin_settings_revision($themeState);
        }
    } elseif ($appearanceDraft === null && in_array($section, ['contacts', 'components'], true)) {
        $appearanceDraft = $section === 'contacts' ? site_contact_settings() : site_component_settings();
        $appearanceRevision = admin_settings_revision($appearanceDraft);
    }
    if ($section === 'pages') {
        $tree = site_page_tree(site_pages());
    }
    if ($section === 'assets' && $assetsDraft === null) {
        try {
            $encodedAssets = site_global_assets();
            $assetsRevision = admin_settings_revision($encodedAssets);
            $assetsDraft = array_map('site_asset_decode', $encodedAssets);
        } catch (RuntimeException $exception) {
            $error = $exception->getMessage();
        }
    }
    if ($section === 'libraries' && $libraryDraft === null) {
        try {
            $libraryDraft = site_library_settings(site_read_record(SITE_LIBRARIES_FILE));
            $libraryRevision = admin_settings_revision($libraryDraft);
        } catch (RuntimeException $exception) {
            $error = $exception->getMessage();
        }
    }
    if ($section === 'headers' && $headersDraft === null) {
        try {
            $headersDraft = site_header_inserts();
            $headersRevision = admin_settings_revision($headersDraft);
        } catch (RuntimeException $exception) {
            $error = $exception->getMessage();
        }
    }
    if ($section === 'pages' && $page === null) {
        if ($creating) {
            $variant = admin_page_path(admin_input($_GET, 'translation'));
            list($basePage, $language) = site_page_translation($variant);
            $translationAvailable = $language !== '' && $language !== 'ru' && in_array($language, $regional['languages'], true)
                && site_read_page($basePage) !== null && site_read_page($variant) === null;
            $page = [
                'path' => $translationAvailable ? site_page_url('', $variant) : '',
                'fields' => ['Title' => '', 'Heading' => '', 'Description' => '', 'Keywords' => '', 'Menu' => '', 'Order' => '100', 'Hidden' => '0'],
                'html' => '',
                'revision' => '',
            ];
            $parent = trim(admin_input($_GET, 'parent'), '/');
            if (!$translationAvailable && $parent !== '' && site_valid_path($parent . '/index.php')) {
                $page['path'] = '/' . $parent . '/';
                if (admin_input($_GET, 'section') === '1') {
                    $page['fields']['Title'] = basename($parent);
                    $page['fields']['MenuVisible'] = '1';
                }
            }
            if (isset($_GET['preset'])) {
                try {
                    $preset = admin_page_templates($adminLanguage)[admin_input($_GET, 'preset')] ?? null;
                    if ($preset !== null) {
                        $page['fields'] = array_intersect_key($preset, array_flip(['Title', 'Heading', 'Description'])) + $page['fields'];
                        $page['html'] = $preset['html'];
                        $pageAssets = $preset['assets'];
                    }
                } catch (RuntimeException $exception) {
                    $error = $exception->getMessage();
                }
            }
        } else {
            $path = admin_page_path(admin_input($_GET, 'page'));
            $page = site_read_page($path === '' ? 'index.php' : $path);
            if ($page === null && $error === '') {
                http_response_code(404);
                $error = 'Страница не найдена.';
            }
        }
    }
    if ($section === 'layout' || ($section === 'domains' && $layoutSelection !== '')) {
        if (!$layoutValid) {
            http_response_code(404);
            $layoutError = 'Выберите домен из списка.';
        } else {
            try {
                $ownLayouts = site_read_layout_snippets($layoutHost);
                $sharedLayouts = $layoutHost === '' ? [] : site_read_layout_snippets();
                if ($layoutDraft === null) {
                    $layoutDraft = [];
                    $layoutRevision = admin_settings_revision($ownLayouts);
                }
                foreach (admin_domain_template_labels() as $name => $label) {
                    $own = $ownLayouts[$name] ?? [];
                    $mode = $own ? ($own['mode'] ?? 'custom') : 'inherit';
                    $layoutDraft[$name] = $layoutDraft[$name] ?? [
                        'mode' => $mode,
                        'value' => admin_layout_editor_value($name, $mode, $own, $sharedLayouts[$name] ?? []),
                    ];
                    $layoutDraft[$name]['visibility'] = $layoutDraft[$name]['visibility'] ?? $ownLayouts['_layoutVisibility'][$name]
                        ?? $sharedLayouts['_layoutVisibility'][$name] ?? site_layout_visibility_default($name);
                }
            } catch (RuntimeException $exception) {
                $layoutError = $exception->getMessage();
            }
        }
    }
    if ($section === 'snippets') {
        if (!$scopeValid) {
            http_response_code(404);
            $snippetError = 'Выберите домен из списка или откройте общие значения.';
        } else {
            try {
                $shared = site_read_snippets();
                $own = $sharedScope ? $shared : site_read_snippets($scopeHost);
                $snippetEntries = array_replace($shared, $own);
                $snippetEntries += site_header_contact_snippets($adminLanguage, $snippetEntries);
                unset($snippetEntries['_layoutHistory'], $snippetEntries['_layoutVisibility']);
                ksort($snippetEntries, SORT_STRING);
                $selected = admin_input($_GET, 'snippet');
                if ($snippet === null && $selected !== '') {
                    if (site_valid_snippet($selected) && isset($snippetEntries[$selected])) {
                        $snippet = [
                            'name' => $selected,
                            'values' => $snippetEntries[$selected],
                            'revision' => isset($own[$selected]) ? admin_snippet_revision($own[$selected]) : '',
                            'locked' => true,
                        ];
                    } else {
                        http_response_code(404);
                        $snippetError = 'Подшаблон не найден.';
                    }
                }
            } catch (RuntimeException $exception) {
                $snippetError = $exception->getMessage();
            }
        }
    }
    if ($section === 'forms') {
        try {
            $forms = site_forms();
            $formIsNew = $formSelection === 'new';
            if ($formDraft === null) {
                if ($formIsNew) {
                    for ($number = 2; isset($forms['form' . $number]); $number++) {
                        // Use the first free shortcode.
                    }
                    $formId = 'form' . $number;
                    $formRevision = '';
                    $formDraft = ['button' => 'Отправить', 'recipient' => '', 'fields' => [
                        ['name' => '', 'label' => '', 'type' => 'text', 'required' => false, 'options' => []],
                    ]];
                } else {
                    $formId = $formSelection === '' ? 'form1' : $formSelection;
                    if (!isset($forms[$formId])) {
                        http_response_code(404);
                        $formEditorError = 'Форма не найдена.';
                    } else {
                        $formDraft = $forms[$formId];
                        $formRevision = admin_settings_revision($formDraft);
                    }
                }
            }
        } catch (RuntimeException $exception) {
            $forms = [];
            $formEditorError = $exception->getMessage();
        }
    }
    if ($section === 'spam' && $spamDraft === null) {
        try {
            $rules = site_spam_rules();
            $spamDraft = array_map(static function (array $values): string { return implode("\n", $values); }, $rules);
            $spamRevision = admin_settings_revision($rules);
        } catch (RuntimeException $exception) {
            $spamEditorError = $exception->getMessage();
        }
    }
    if ($section === 'messages') {
        $messagePagination = admin_inbox_page(SITE_INBOX, admin_input($_GET, 'inbox_page'));
        $messageId = admin_input($_GET, 'message');
        $message = [];
        if (isset($messagePagination['all'][$messageId])) {
            try {
                $message = site_read_record(SITE_INBOX . '/' . $messageId . '.php');
                if ($message && $messagePagination['all'][$messageId]) {
                    admin_mark_inbox_read(SITE_INBOX, $messageId);
                }
            } catch (RuntimeException $exception) {
                $error = $exception->getMessage();
            }
        } elseif ($messageId !== '') {
            http_response_code(404);
        }
    }
    if (in_array($section, ['callbacks', 'integrations'], true)) {
        $callbackRecords = [];
        $callbackConfig = null;
        $callbackRecord = [];
        $callbackIdSelected = '';
        try {
            $callbackConfig = site_callback_settings();
            if ($callbackDraft === null) {
                $callbackDraft = $callbackConfig;
                $callbackRevision = admin_settings_revision($callbackDraft);
            }
            $callbackPagination = $section === 'callbacks' ? admin_inbox_page(SITE_CALLBACKS_DIR, admin_input($_GET, 'inbox_page')) : ['rows' => [], 'all' => []];
            $callbackIdSelected = $section === 'callbacks' ? admin_input($_GET, 'callback') : '';
            if (isset($callbackPagination['all'][$callbackIdSelected])) {
                $callbackRecord = site_callback_read($callbackIdSelected);
                if ($callbackRecord && $callbackPagination['all'][$callbackIdSelected]) {
                    admin_mark_inbox_read(SITE_CALLBACKS_DIR, $callbackIdSelected);
                }
            } elseif ($callbackIdSelected !== '') {
                http_response_code(404);
            }
            foreach (array_keys($callbackPagination['rows']) as $callbackId) {
                $record = site_callback_read($callbackId);
                if ($record) {
                    $callbackRecords[$callbackId] = $record;
                }
            }
        } catch (RuntimeException $exception) {
            $error = $exception->getMessage();
        }
    }
    if ($section === 'iblocks') {
        $iblockNames = site_iblock_names();
        if ($iblockDraft === null) {
            $iblockId = admin_input($_GET, 'id');
            if ($iblockIsNew || ($iblockId === '' && !$iblockNames)) {
                $iblockIsNew = true;
                $iblockDraft = ['columns' => [['key' => 'name', 'label' => admin_t('Название'), 'type' => 'text']], 'rows' => []];
                $iblockRevision = '';
            } else {
                $iblockId = $iblockId !== '' ? $iblockId : $iblockNames[0];
                try {
                    $record = site_read_iblock($iblockId);
                    if (!isset($record['columns'], $record['rows'])) {
                        http_response_code(404);
                        $iblockEditorError = 'Инфоблок не найден.';
                    } else {
                        $iblockDraft = $record;
                        $iblockRevision = admin_settings_revision($record);
                    }
                } catch (RuntimeException $exception) {
                    $iblockEditorError = $exception->getMessage();
                }
            }
        }
        if ($iblockTextMode && $iblockDraft !== null && $iblockCsv === null) {
            $iblockCsv = site_iblock_csv($iblockDraft);
        }
    }
}

if ($embeddedData && !$loggedIn) {
    http_response_code(403);
    echo admin_l('Войдите в админку.');
    return;
}
if ($loggedIn && $section === 'pages' && $page !== null && ($dataViewSuffix === '' || $focusedPage)) {
    $mapSettings = site_map_settings();
    if ($mapSettings['provider'] === 'yandex' && $mapSettings['yandex_key'] !== '') {
        $api = 'https://api-maps.yandex.ru https://*.api-maps.yandex.ru';
        $sources = $api . ' https://*.maps.yandex.net https://yandex.ru';
        header('Content-Security-Policy: ' . strtr($adminPolicy, [
            "style-src 'self'" => "style-src 'self' blob:",
            "img-src 'self'" => "img-src 'self' data: " . $sources,
            "script-src 'self'" => "script-src 'self' " . $sources . ' https://suggest-maps.yandex.ru https://yastatic.net',
            "connect-src 'self'" => "connect-src 'self' " . $sources . ' https://suggest-maps.yandex.ru',
            "frame-src 'self'" => "frame-src 'self' https://api-maps.yandex.ru",
        ]));
    }
}
require __DIR__ . '/view.php';
