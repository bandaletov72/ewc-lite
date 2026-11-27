<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}
$appearanceLabels = site_appearance_labels($adminLanguage);
$cityLabels = site_city_language($adminLanguage);
$scheduleLabels = admin_schedule_labels($adminLanguage);
$groups = [
    $cityLabels['group'] => [
        'cities' => [$cityLabels['cities'], $citiesUrl],
        'domains' => ['Домены', $domainsUrl],
    ],
    'Настройки' => [
        'settings' => ['Основные', $settingsUrl],
        'contacts' => ['Контакты и соцсети', $contactsUrl],
        'components' => ['Компоненты', $componentsUrl],
        'libraries' => [$appearanceLabels[2], $librariesUrl],
        'region' => ['Региональные настройки', $regionUrl],
        'captcha' => ['CAPTCHA', $captchaUrl],
    ],
    'Шаблоны оформления' => [
        'layout' => ['Шаблоны оформления', $layoutEditorUrl],
        'mail_templates' => [admin_mail_labels($adminLanguage)['title'], $mailTemplatesUrl],
        'design' => [site_design_labels($adminLanguage)[0], $designUrl],
        'snippets' => ['Подшаблоны', $snippetsUrl],
        'theme' => [$appearanceLabels[0], $themeUrl],
        'fonts' => [$appearanceLabels[1], $fontsUrl],
        'headers' => ['Служебные заголовки', $headersUrl],
        'functions' => ['Функции', $functionsUrl],
        'assets' => ['CSS и JS', $assetsUrl],
    ],
    'Содержимое' => [
        'iblocks' => ['Инфоблоки', $iblocksUrl],
        'modules' => ['PHP-модули', $modulesUrl],
    ],
    'Формы и заявки' => [
        'forms' => ['Формы', $formsUrl],
        'messages' => ['Сообщения', $messagesUrl],
        'reviews' => [site_review_labels($adminLanguage)[0], $reviewsUrl],
        'purchases' => [site_sale_label($adminLanguage)['purchases'], $purchasesUrl],
        'callbacks' => ['Обратные звонки', $callbacksUrl],
        'spam' => ['Антиспам', $spamUrl],
    ],
    'Сайт и сервисы' => [
        'integrations' => ['Интеграции', $integrationsUrl],
        'maintenance' => ['Обслуживание', $maintenanceUrl],
        'users' => ['Пользователи', $usersUrl],
    ],
];
$groupLabels = ['Настройки' => 'Разделы настроек', 'Шаблоны оформления' => 'Оформление сайта'];
$navigation = [
    ['Страницы', 'file-lines', $adminUrl, $section === 'pages'],
    [admin_page_template_labels($adminLanguage)[0], 'file-code', $pageTemplatesUrl, $section === 'page_templates'],
    [$cityLabels['group'], 'location-dot', $citiesUrl, isset($groups[$cityLabels['group']][$section])],
    ['Настройки', 'gear', $settingsUrl, isset($groups['Настройки'][$section])],
    ['Шаблоны оформления', 'palette', $layoutEditorUrl, isset($groups['Шаблоны оформления'][$section])],
    ['Содержимое', 'layer-group', $iblocksUrl, isset($groups['Содержимое'][$section])],
    ['Формы и заявки', 'inbox', $formsUrl, isset($groups['Формы и заявки'][$section])],
    ['Сайт и сервисы', 'screwdriver-wrench', $integrationsUrl, isset($groups['Сайт и сервисы'][$section])],
    [$aiLabels['tab'], 'wand-magic-sparkles', $aiUrl, $section === 'ai'],
    ['Маркетплейс', 'store', $shopUrl, $section === 'shop'],
    ['Помощь', 'circle-question', $helpUrl, $section === 'help'],
    [$scheduleLabels[0], 'box-archive', $backupsUrl, $section === 'backups'],
    ['Обновления', 'arrows-rotate', $updatesUrl, $section === 'updates'],
];
?>
<nav class="site-menu admin-menu" aria-label="<?= admin_l('Разделы админки') ?>">
    <?php foreach ($navigation as list($label, $icon, $url, $current)): ?>
        <a class="admin-nav-item" data-nav-tooltip="<?= admin_l($label) ?>" href="<?= site_escape($url) ?>"<?= $current ? ' aria-current="page"' : '' ?>>
            <i class="fa-solid fa-<?= $icon ?>" aria-hidden="true"></i><span class="admin-nav-label"><?= admin_l($label) ?></span>
            <?php if ($url === $backupsUrl && admin_backup_needs_attention()): ?>
                <span class="admin-update-badge" role="img" title="<?= site_escape($scheduleLabels[1]) ?>" aria-label="<?= site_escape($scheduleLabels[1]) ?>">!</span>
            <?php elseif ($url === $updatesUrl): ?>
                <span class="admin-update-badge" data-update-badge="<?= site_escape($updatesUrl . '&check=1') ?>" hidden></span>
            <?php endif; ?>
        </a>
    <?php endforeach; ?>
</nav>
<?php foreach ($groups as $label => $links): ?>
    <?php if (isset($links[$section])): ?>
        <nav class="site-menu scope-menu" aria-label="<?= admin_l($groupLabels[$label] ?? $label) ?>">
            <?php foreach ($links as $name => list($title, $url)): ?>
                <a href="<?= site_escape($url) ?>"<?= $section === $name ? ' aria-current="page"' : '' ?>><?= admin_l($title) ?></a>
            <?php endforeach; ?>
        </nav>
        <?php break; ?>
    <?php endif; ?>
<?php endforeach; ?>
