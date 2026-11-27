<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}
$loadHelp = static function (string $file) use ($adminLanguage): array {
    return site_localized_help(require SITE_ROOT . '/app/' . $file . '.php', $adminLanguage);
};
$guide = require SITE_ROOT . '/app/template-guide.php';
$faq = require SITE_ROOT . '/app/admin-faq.php';
$contentUrlHelp = require SITE_ROOT . '/app/help-content-urls.php';
$passwordHelp = $loadHelp('help-password-reset');
$passwordHelp = $passwordHelp[$adminLanguage] ?? $passwordHelp['ru'];
$ftpSetupHelp = require SITE_ROOT . '/app/help-ftp-setup.php';
$ftpSetupHelp = $ftpSetupHelp[$adminLanguage] ?? $ftpSetupHelp['ru'];
list($localized, $releaseExamples) = $loadHelp('help-release');
$releaseHelp = $localized[$adminLanguage] ?? $localized['ru'];
$currentHelp = $loadHelp('help-current');
$siteQuickstart = $loadHelp('help-site-quickstart');
$siteQuickstart = $siteQuickstart[$adminLanguage] ?? $siteQuickstart['ru'];
$i18nHelp = $loadHelp('help-i18n');
$functionsHelp = $loadHelp('help-functions');
list($headerHelp, $navigationHelp, $layoutHelp) = $loadHelp('help-header');
list($headerTopic, $headerText) = $headerHelp[$adminLanguage] ?? $headerHelp['ru'];
$maintenanceHelp = $loadHelp('help-maintenance');
$externalHelp = $loadHelp('help-integrations');
$editorHelp = $loadHelp('help-editor');
$iconFilterHelp = $loadHelp('help-icon-filter');
$maplistHelp = $loadHelp('help-maplist');
$maplistHelp = $maplistHelp[$adminLanguage] ?? $maplistHelp['ru'];
$branchHelp = $loadHelp('help-menu-branches');
$branchHelp = $branchHelp[$adminLanguage] ?? $branchHelp['ru'];
$routeHelp = $loadHelp('help-route');
$routeHelp = $routeHelp[$adminLanguage] ?? $routeHelp['ru'];
$routingHelp = $loadHelp('help-routing');
$dataEditorHelp = $loadHelp('help-data-editor');
list($dataHelpTitle, $dataHelpText) = $dataEditorHelp[$adminLanguage] ?? $dataEditorHelp['ru'];
$pageEditorHelp = $loadHelp('help-page-editor');
$pageEditorHelp = $pageEditorHelp[$adminLanguage] ?? $pageEditorHelp['ru'];
$announcementHelp = $loadHelp('help-announcements');
$announcementHelp = $announcementHelp[$adminLanguage] ?? $announcementHelp['ru'];
$languageHelp = $loadHelp('help-page-languages');
$languageHelp = $languageHelp[$adminLanguage] ?? $languageHelp['ru'];
$runtimeHelp = $loadHelp('help-runtime');
$runtimeHelp = $runtimeHelp[$adminLanguage] ?? $runtimeHelp['ru'];
$workflowHelp = $loadHelp('help-workflow');
$workflowHelp = $workflowHelp[$adminLanguage] ?? $workflowHelp['ru'];
$mapPickerHelp = $loadHelp('help-map-picker');
$mapPickerHelp = $mapPickerHelp[$adminLanguage] ?? $mapPickerHelp['ru'];
$mediaCleanupHelp = $loadHelp('help-media-cleanup');
$mediaCleanupHelp = $mediaCleanupHelp[$adminLanguage] ?? $mediaCleanupHelp['ru'];
$editorInterfaceHelp = $loadHelp('help-editor-interface');
$salesHelp = site_sale_label($adminLanguage);
$brandEditorHelp = $loadHelp('help-brand-editor');
$chartHelp = site_iblock_chart_text($adminLanguage);
$componentInsertionHelp = require SITE_ROOT . '/app/help-component-insertion.php';
$adminBrandHelp = require SITE_ROOT . '/app/help-admin-brand.php';
$sidebarHelp = require SITE_ROOT . '/app/help-page-sidebar.php';
$searchMenuHelp = require SITE_ROOT . '/app/help-search-menu.php';
$subtitleHelp = require SITE_ROOT . '/app/help-site-subtitle.php';
$cityHelp = site_city_language($adminLanguage);
$animationHelp = site_scroll_animation_labels($adminLanguage);
$pageTemplateHelp = admin_page_template_labels($adminLanguage);
$presetClasses = require SITE_ROOT . '/app/help-preset-classes.php';
$presetHelp = $presetClasses[$adminLanguage] ?? $presetClasses['ru'];
$layoutListHelp = require SITE_ROOT . '/app/help-layout-list.php';
$formUx = require SITE_ROOT . '/app/help-form-ux.php';
$designHelp = site_design_labels($adminLanguage);
$componentSizes = require SITE_ROOT . '/app/help-component-sizes.php';
$breadcrumbHelp = require SITE_ROOT . '/app/help-breadcrumb-siblings.php';
$bannerHelp = $loadHelp('help-banners');
$bannerInlineHelp = require SITE_ROOT . '/app/help-banner-inline.php';
$componentEditingHelp = $loadHelp('help-component-editing');
$componentEditingHelp = $componentEditingHelp[$adminLanguage] ?? $componentEditingHelp['ru'];
list($bannerHelpTitle, $bannerHelpText) = $bannerHelp[$adminLanguage] ?? $bannerHelp['ru'];
list($maintenanceTopic, $serviceText, $mapsText, $customLayoutsText) = $maintenanceHelp[$adminLanguage] ?? $maintenanceHelp['ru'];
$contentApiHelp = admin_content_api_labels($adminLanguage);
$accountAccessHelp = require SITE_ROOT . '/app/help-account-access.php';
$accountAccessHelp = $accountAccessHelp[$adminLanguage] ?? $accountAccessHelp['en'];
$mediaHelpLocales = require SITE_ROOT . '/app/help-media-library.php';
$mediaHelp = $mediaHelpLocales[$adminLanguage] ?? $mediaHelpLocales['en'];
$mediaHelpText = $mediaHelpLocales['help'][$adminLanguage] ?? $mediaHelpLocales['help']['en'];
?>
<h1><?= admin_l('Помощь') ?></h1>
<section class="panel form-stack" id="help-site-quickstart">
    <h2><?= site_escape($siteQuickstart[0]) ?></h2>
    <ul>
        <?php foreach (array_slice($siteQuickstart, 1) as $item): ?><li><?= site_escape($item) ?></li><?php endforeach; ?>
        <li><?= site_escape($contentUrlHelp[$adminLanguage] ?? $contentUrlHelp['ru']) ?></li>
    </ul>
    <p><a href="https://github.com/bandaletov72/ewc-lite/blob/main/docs/ai-site-prompts.md" target="_blank" rel="noopener">EWC · AI prompts</a></p>
</section>
<?php $reviewHelp = site_review_labels($adminLanguage); ?>
<nav class="site-menu scope-menu" aria-label="<?= admin_l('Разделы справки') ?>">
    <a href="#help-content-api">REST API</a>
    <a href="#help-account-access"><?= site_escape($accountAccessHelp[1]) ?></a>
    <a href="#help-media-library"><?= site_escape($mediaHelp[0]) ?></a>
    <a href="#help-reviews"><?= site_escape($reviewHelp[0]) ?></a>
    <a href="#help-password-reset"><?= site_escape($passwordHelp[0]) ?></a>
    <a href="#help-runtime"><?= site_escape($runtimeHelp[0]) ?></a>
    <a href="#help-content-backups"><?= site_escape($workflowHelp['backup'][0]) ?></a>
    <a href="#help-unused-media"><?= site_escape($mediaCleanupHelp[0]) ?></a>
    <a href="#help-sales"><?= site_escape($salesHelp['tab']) ?></a>
    <a href="#help-iblock-charts"><?= site_escape($chartHelp['chart']) ?></a>
    <a href="#help-map-picker"><?= site_escape($mapPickerHelp[0]) ?></a>
    <a href="#help-page-languages"><?= site_escape($languageHelp[0]) ?></a>
    <a href="#help-page-edit"><?= site_escape($pageEditorHelp[1]) ?></a>
    <a href="#help-banners"><?= site_escape($bannerHelpTitle) ?></a>
    <a href="#help-data-editor"><?= site_escape($dataHelpTitle) ?></a>
    <a href="#help-service-files"><?= site_escape($maintenanceTopic) ?></a>
    <a href="#help-admin-header"><?= site_escape($headerTopic) ?></a>
    <a href="#help-cities"><?= site_escape($cityHelp['cities']) ?></a>
    <a href="#help-scroll-animation"><?= site_escape($animationHelp[0]) ?></a>
    <a href="#help-page-presets"><?= site_escape($pageTemplateHelp[0]) ?></a>
    <a href="#help-design-themes"><?= site_escape($designHelp[0]) ?></a>
    <a href="#help-functions"><?= admin_l('Новое в версии 1.6.3') ?></a>
    <a href="#help-i18n"><?= admin_l('Новое в версии 1.6.2') ?></a>
    <a href="#help-current"><?= admin_l('Новое в версии 1.6.1') ?></a>
    <a href="#help-release"><?= admin_l('Новое в версии 1.6.0') ?></a>
    <a href="#help-admin-faq"><?= admin_l('FAQ по админке') ?></a>
    <?php foreach ($guide as $anchor => $group): ?>
        <a href="#help-<?= site_escape($anchor) ?>"><?= admin_l($group['title']) ?></a>
    <?php endforeach; ?>
</nav>
<div class="form-stack template-guide">
    <section class="panel form-stack" id="help-content-api">
        <h2>REST API</h2>
        <p><?= site_escape($contentApiHelp['guide']) ?></p>
        <?php $gptHelp = require __DIR__ . '/content-gpt-labels.php'; ?>
        <p><?= site_escape(($gptHelp[$adminLanguage] ?? $gptHelp['en'])[5]) ?></p>
        <ul>
            <li><code>GET /api/v1/pages?page=1&amp;per_page=25</code></li>
            <li><code>GET /api/v1/pages/index.php</code></li>
            <li><code>PATCH /api/v1/pages/index.php</code></li>
        </ul>
        <p><a href="<?= site_escape($aiUrl) ?>#content-api"><?= site_escape($contentApiHelp['access']) ?> · REST API</a></p>
    </section>
    <section class="panel form-stack" id="help-account-access">
        <h2><?= site_escape($accountAccessHelp[1]) ?></h2>
        <p><?= site_escape($accountAccessHelp[3]) ?></p>
        <p><a href="<?= site_escape($usersUrl . '&tab=access') ?>"><?= admin_l('Пользователи') ?></a></p>
    </section>
    <section class="panel form-stack" id="help-media-library">
        <h2><?= site_escape($mediaHelp[0]) ?></h2>
        <p><?= site_escape($mediaHelpText) ?></p>
        <p><a href="<?= site_escape($mediaUrl) ?>"><?= site_escape($mediaHelp[0]) ?></a></p>
    </section>
    <section class="panel form-stack" id="help-shop">
        <h2><i class="fa-solid fa-store" aria-hidden="true"></i> <?= admin_l('Маркетплейс') ?></h2>
        <?php
        $shopHelp = require SITE_ROOT . '/app/help-shop.php';
        $shopHelp = $shopHelp[$adminLanguage] ?? $shopHelp['en'];
        $topics = ['catalog&sheet=products'=>'boxes-stacked','settings'=>'store','templates'=>'file-code',
            'orders'=>'receipt','export'=>'file-export','integrations'=>'plug'];
        ?>
        <div class="shop-help-topics">
            <?php $position = 0; foreach ($topics as $tab => $icon): ?>
                <?php [$caption, $description] = explode('|', $shopHelp[$position++], 2); ?>
                <article class="shop-help-topic">
                    <h3><a href="<?= site_escape($shopUrl . '&tab=' . $tab) ?>"><i class="fa-solid fa-<?= $icon ?>" aria-hidden="true"></i>
                        <span><?= site_escape($caption) ?></span><i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a></h3>
                    <p><?= site_escape($description) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
        <details class="shop-help-examples">
            <summary><i class="fa-solid fa-code" aria-hidden="true"></i> <?= site_escape($shopHelp[6]) ?></summary>
            <pre><code>[shop]
[items category="drills" layout="grid" limit="24"]
[carousel category="drills" mobile="2" desktop="auto"]
[categories category="drills" layout="list"]
<?php foreach (site_shop_tags() as $tag): ?>[shop-<?= $tag ?>]
<?php endforeach; ?>[register]
[account]</code></pre>
            <div class="header-actions">
                <code>/<?= site_escape(site_shop_public_folder()) ?>/</code>
                <code>/<?= site_escape(site_shop_public_folder()) ?>/category/slug/</code>
                <code>/product/slug/</code>
                <a href="<?= site_escape($settingsUrl . '#account-registration') ?>"><?= admin_l('Разрешить регистрацию пользователей') ?></a>
            </div>
        </details>
    </section>
    <section class="panel form-stack" id="help-reviews">
        <h2><?= site_escape($reviewHelp[0]) ?></h2>
        <pre><code>[reviews]
[reviews page=current]</code></pre>
        <p><?= site_escape($reviewHelp[11]) ?></p>
        <p><?= site_escape($reviewHelp[12]) ?></p>
        <p><?= site_escape($reviewHelp[26]) ?></p>
        <p><?= site_escape($reviewHelp[29]) ?></p>
        <?php $spamHelp = site_spam_admin_labels($adminLanguage); ?>
        <p><?= site_escape($reviewHelp[31]) ?> <a href="<?= site_escape($spamUrl) ?>"><?= site_escape($spamHelp[2]) ?></a>.</p>
        <p><?= site_escape($spamHelp[4]) ?> <?= site_escape($spamHelp[9]) ?> <?= site_escape($spamHelp[16]) ?></p>
        <p><a href="<?= site_escape($reviewsUrl) ?>"><?= site_escape($reviewHelp[0]) ?></a></p>
        <p><?= site_escape(site_edit_layout_labels($adminLanguage)[9]) ?></p>
        <?php $themeUpdateHelp = require SITE_ROOT . '/app/help-theme-update.php'; $themeUpdateHelp = $themeUpdateHelp[$adminLanguage] ?? $themeUpdateHelp['ru']; ?>
        <p><?= site_escape($themeUpdateHelp[14]) ?></p>
    </section>
    <section class="panel form-stack" id="help-password-reset">
        <h2><?= site_escape($passwordHelp[0]) ?></h2>
        <p><?= site_escape($ftpSetupHelp[1]) ?></p>
        <?php foreach (array_slice($passwordHelp, 1) as $paragraph): ?>
            <p><?= site_escape($paragraph) ?></p>
        <?php endforeach; ?>
        <p><a href="<?= site_escape($ftpResetUrl) ?>" download><?= site_escape($recoveryLabels['ftp-download']) ?></a></p>
    </section>
    <section class="panel form-stack" id="help-iblock-charts">
        <h2><?= site_escape($chartHelp['chart']) ?></h2>
        <p><?= site_escape($chartHelp['help']) ?></p>
        <p><?= admin_l('Тип «Кнопка вызова формы» добавляет в строку таблицы кнопку «Заказать». Выберите форму по названию в выпадающем списке. В CSV укажите код формы, например form86.') ?></p>
        <p><?= admin_l('Тип «Скрытый тег» добавляет фильтры над таблицей. В ячейке укажите один или несколько тегов через запятую. Кнопка «ВСЕ» показывает все строки.') ?></p>
        <p><?= site_escape($componentInsertionHelp[$adminLanguage] ?? $componentInsertionHelp['ru']) ?></p>
        <pre><code>[iblock=table]/data/myiblock.txt[/iblock]
[iblock=charts type=line labels=month values=sales,orders]/data/myiblock.txt[/iblock]
[banners set=summer-promo]</code></pre>
    </section>
    <section class="panel form-stack" id="help-sales">
        <h2><?= site_escape($salesHelp['tab']) ?> · 1.6.11</h2>
        <p><?= site_escape($salesHelp['help']) ?></p>
        <p><?= site_escape(implode(' · ', $salesHelp['statuses'])) ?></p>
        <pre><code>[item-price]
[item-buy-button]</code></pre>
    </section>
    <section class="panel form-stack" id="help-runtime">
        <h2><?= site_escape($runtimeHelp[0]) ?> · 1.6.10</h2>
        <?php foreach (array_slice($runtimeHelp, 1) as $paragraph): ?><p><?= site_escape($paragraph) ?></p><?php endforeach; ?>
        <?php $phpLabels = site_php_admin_labels($adminLanguage); ?>
        <h3><?= site_escape($phpLabels[0]) ?></h3>
        <p><?= site_escape($phpLabels[5]) ?></p>
    </section>
    <section class="panel form-stack" id="help-content-backups">
        <h2><?= site_escape($workflowHelp['backup'][0]) ?> · 1.6.10</h2>
        <p><?= site_escape($workflowHelp['backup'][5]) ?></p>
        <p><?= site_escape(admin_backup_delete_labels($adminLanguage)[4]) ?></p>
        <p><?= site_escape($workflowHelp['backup'][6]) ?></p>
    </section>
    <section class="panel form-stack" id="help-map-picker">
        <h2><?= site_escape($mapPickerHelp[0]) ?> · 1.6.10</h2>
        <p><?= site_escape($mapPickerHelp[1]) ?></p>
        <p><?= site_escape($workflowHelp['help']) ?></p>
        <p><?= site_escape($editorInterfaceHelp[$adminLanguage] ?? $editorInterfaceHelp['ru']) ?></p>
        <p><?= site_escape($brandEditorHelp[$adminLanguage] ?? $brandEditorHelp['ru']) ?></p>
        <pre><code>[button action="url" label="Contacts" url="/contacts/"]
[button action="form" label="Write to us" form="form1"]
[button action="callback" label="Call me"]
[button action="payment" label="Pay" amount="1250.00" currency="RUB" yoomoney="410010000000000"]</code></pre>
    </section>
    <section class="panel form-stack" id="help-unused-media">
        <h2><?= site_escape($mediaCleanupHelp[0]) ?></h2>
        <p><?= site_escape($mediaCleanupHelp[4]) ?></p>
        <p><?= site_escape($mediaCleanupHelp[8]) ?></p>
    </section>
    <section class="panel form-stack" id="help-page-languages">
        <h2><?= site_escape($languageHelp[0]) ?> · 1.6.9</h2>
        <p><?= site_escape($languageHelp[7]) ?></p>
        <p><?= site_escape($languageHelp[10]) ?></p>
    </section>
    <section class="panel form-stack" id="help-page-edit">
        <h2><?= site_escape($pageEditorHelp[1]) ?> · 1.6.8</h2>
        <p><?= site_escape($pageEditorHelp[13]) ?></p>
        <p><?= site_escape($branchHelp[2]) ?></p>
        <h3><?= site_escape(admin_page_custom_labels()[0]) ?></h3>
        <p><?= site_escape(admin_page_custom_labels()[4]) ?></p>
        <p><?= site_escape(admin_page_custom_labels()[8]) ?></p>
        <h3><?= site_escape(admin_page_custom_labels()[11]) ?></h3>
        <p><?= site_escape(admin_page_custom_labels()[12]) ?></p>
        <?php $componentHelp = $loadHelp('help-page-component-settings'); ?>
        <p><?= site_escape(($componentHelp[$adminLanguage] ?? $componentHelp['ru'])[0]) ?></p>
        <p><?= site_escape($pageEditorHelp[6]) ?></p>
        <h3><?= site_escape($announcementHelp[0]) ?></h3>
        <p><?= site_escape($announcementHelp[1]) ?></p>
        <p><?= site_escape($announcementHelp[12]) ?></p>
        <ul>
            <li><code>[map]</code> — <?= site_escape($pageEditorHelp[11]) ?></li>
            <li><code>[maps]</code> — <?= site_escape($pageEditorHelp[12]) ?></li>
            <li><code>[maplist]</code> — <?= site_escape($maplistHelp[2]) ?></li>
            <li><?= site_escape($maplistHelp[6]) ?></li>
            <li><code>[route]</code> — <?= site_escape($routeHelp[1]) ?></li>
        </ul>
    </section>
    <section class="panel form-stack" id="help-banners">
        <h2><?= site_escape($bannerHelpTitle) ?> · 1.6.8</h2>
        <p><?= site_escape($bannerHelpText) ?></p>
        <p><?= site_escape($bannerInlineHelp[$adminLanguage] ?? $bannerInlineHelp['ru']) ?></p>
        <p><?= site_escape($componentEditingHelp[1]) ?></p>
        <p><?= site_escape($componentEditingHelp[2]) ?></p>
        <p><?= site_escape($componentEditingHelp[3]) ?></p>
        <p><?= site_escape($componentEditingHelp[4]) ?></p>
        <p><?= site_escape(site_component_frame_labels($adminLanguage)[3]) ?></p>
        <p><?= site_escape(site_media_folder_labels($adminLanguage)[7]) ?></p>
        <pre><code>[banners]
[banners set=summer arrows=on numbers=off]
[banners]/images/myfotos/[/banners]</code></pre>
    </section>
    <section class="panel form-stack">
        <h2><?= admin_l('Мобильное меню') ?> · 1.6.7</h2>
        <ul>
            <?php foreach (admin_page_visibility_labels($adminLanguage) as $visibilityLabel): ?><li><?= site_escape($visibilityLabel) ?></li><?php endforeach; ?>
        </ul>
    </section>
    <section class="panel form-stack" id="help-data-editor">
        <h2><?= site_escape($dataHelpTitle) ?> · 1.6.6</h2>
        <p><?= site_escape($dataHelpText) ?></p>
    </section>
    <section class="panel form-stack" id="help-service-files">
        <h2><?= site_escape($maintenanceTopic) ?> · 1.6.5</h2>
        <p><?= site_escape($serviceText) ?></p>
        <?php $progressHelp = admin_update_progress_labels($adminLanguage); ?>
        <div class="help-entry">
            <h3><?= site_escape($progressHelp['progress']) ?> · 100% · <?= site_escape($progressHelp['done']) ?></h3>
            <p><?= site_escape($progressHelp['release-notes-help']) ?></p>
            <ul>
                <?php foreach (['archive', 'signature', 'verify', 'engine-content', 'prepare', 'theme-design', 'theme-pages', 'theme-missing', 'backup', 'backup-saved'] as $step): ?>
                    <li><?= site_escape($progressHelp[$step]) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="help-entry">
            <h3><?= site_escape($progressHelp['index-progress']) ?></h3>
            <ol>
                <?php foreach (['index-scan', 'index-pages', 'index-save'] as $step): ?>
                    <li><?= site_escape($progressHelp[$step]) ?></li>
                <?php endforeach; ?>
            </ol>
        </div>
        <p><?= site_escape($mapsText) ?></p>
        <p><?= site_escape($customLayoutsText) ?></p>
        <p><?= site_escape($externalHelp[$adminLanguage] ?? $externalHelp['ru']) ?></p>
        <p><a href="<?= site_escape($aiUrl) ?>"><?= site_escape($aiLabels['title']) ?></a></p>
        <?php $aiText = static function (string $key) use ($aiLabels): string { return site_escape($aiLabels[$key]); }; ?>
        <?php require __DIR__ . '/ai-guide.php'; ?>
        <p><?= site_escape($editorHelp[$adminLanguage] ?? $editorHelp['ru']) ?></p>
        <p><?= site_escape(($iconFilterHelp[$adminLanguage] ?? $iconFilterHelp['ru'])[3]) ?></p>
        <p><?= site_escape($routingHelp[$adminLanguage] ?? $routingHelp['ru']) ?></p>
        <pre><code><?= site_escape("[map]\n[maps center=\"55.7558,37.6173\" zoom=11]\n[maplist]/sample/[/maplist]\n[points]/sample/[/points]\n[main-menu]\n[lang]\n[privacy]\n[payment]\n\$var = translit(\"Москва\"); // Moskva") ?></code></pre>
    </section>
    <section class="panel form-stack" id="help-admin-header">
        <h2><?= site_escape($headerTopic) ?> · 1.6.4</h2>
        <p><?= site_escape($headerText) ?></p>
        <p><?= site_escape($navigationHelp[$adminLanguage] ?? $navigationHelp['ru']) ?></p>
        <p><?= site_escape($layoutHelp[$adminLanguage] ?? $layoutHelp['ru']) ?></p>
        <p><?= site_escape(($layoutListHelp[$adminLanguage] ?? $layoutListHelp['ru'])[2]) ?></p>
        <p><?= site_escape(site_edit_layout_labels($adminLanguage)[4]) ?></p>
        <p><?= site_escape(site_edit_layout_labels($adminLanguage)[6]) ?></p>
        <p><?= site_escape(site_edit_layout_labels($adminLanguage)[8]) ?></p>
        <h3><?= site_escape(($adminBrandHelp[$adminLanguage] ?? $adminBrandHelp['ru'])[0]) ?></h3>
        <p><?= site_escape(($adminBrandHelp[$adminLanguage] ?? $adminBrandHelp['ru'])[1]) ?></p>
        <p><?= site_escape(($sidebarHelp[$adminLanguage] ?? $sidebarHelp['ru'])[2]) ?></p>
        <?php list($searchMenuTopic, $searchMenuDetails) = $searchMenuHelp[$adminLanguage] ?? $searchMenuHelp['ru']; ?>
        <h3><?= site_escape($searchMenuTopic) ?></h3>
        <p><?= site_escape($searchMenuDetails) ?></p>
        <p><?= site_escape($presetHelp[3]) ?></p>
        <pre><code>[search]
[lang]</code></pre>
        <?php list($subtitleTopic, $subtitleDetails) = $subtitleHelp[$adminLanguage] ?? $subtitleHelp['ru']; ?>
        <h3><?= site_escape($subtitleTopic) ?></h3>
        <p><?= site_escape($subtitleDetails) ?></p>
        <p><?= site_escape($breadcrumbHelp[$adminLanguage] ?? $breadcrumbHelp['ru']) ?></p>
        <pre><code>[bread-crumbs]
[search]
[privacy]</code></pre>
    </section>
    <section class="panel form-stack" id="help-admin-navigation">
        <?php $scheduleHelp = admin_schedule_labels($adminLanguage); ?>
        <h2><?= admin_l('Обновления') ?> · <?= site_escape($scheduleHelp[0]) ?></h2>
        <p><a href="<?= site_escape($updatesUrl) ?>"><?= admin_l('Обновления') ?></a> ·
            <?= admin_l('Обновления движка') ?>, <?= site_escape($scheduleHelp[12]) ?>, <?= site_escape($scheduleHelp[11]) ?></p>
        <p><a href="<?= site_escape($backupsUrl) ?>"><?= site_escape($scheduleHelp[0]) ?></a> · <?= site_escape($scheduleHelp[1]) ?></p>
        <p><?= site_escape($scheduleHelp[8]) ?> · <?= site_escape($scheduleHelp[3]) ?> · <?= site_escape($scheduleHelp[4]) ?> / <?= site_escape($scheduleHelp[5]) ?></p>
        <p><?= admin_l('Добавьте команду в планировщик сервера с запуском каждые 15 минут.') ?> <code>php update.php --scheduled</code></p>
        <p><a href="<?= site_escape($snippetsUrl) ?>"><?= admin_l('Шаблоны оформления') ?> → <?= admin_l('Подшаблоны') ?></a></p>
    </section>
    <section class="panel form-stack" id="help-cities">
        <h2><?= site_escape($cityHelp['group']) ?></h2>
        <p><?= site_escape($cityHelp['group']) ?> → <a href="<?= site_escape($citiesUrl) ?>"><?= site_escape($cityHelp['cities']) ?></a> /
            <a href="<?= site_escape($domainsUrl) ?>"><?= admin_l('Домены') ?></a></p>
        <p><?= site_escape($cityHelp['help']) ?></p>
        <p><?= site_escape($cityHelp['optional']) ?></p>
        <p><?= site_escape($cityHelp['fallback']) ?></p>
        <p><?= site_escape(site_city_contact_labels($adminLanguage)[6]) ?></p>
        <pre><code>[address] [email] [hours]</code></pre>
        <p><?= site_escape(site_header_contact_labels($adminLanguage)[3]) ?></p>
        <pre><code>[cities]
[phones]
[tel]</code></pre>
    </section>
    <section class="panel form-stack" id="help-scroll-animation">
        <h2><?= site_escape($animationHelp[0]) ?></h2>
        <p><?= site_escape($animationHelp[5]) ?></p>
    </section>
    <section class="panel form-stack" id="help-component-sizes">
        <h2><?= site_escape(site_component_size_text('Ширина блока', $adminLanguage)) ?></h2>
        <p><?= site_escape(($componentSizes[$adminLanguage] ?? $componentSizes['ru'])[7]) ?></p>
        <pre><code>[banners width=page height=520 mobile-height=240]
[gal width=content height=auto]/images/demo/[/gal]
[form1 width=parent]</code></pre>
    </section>
    <section class="panel form-stack" id="help-form-ux">
        <h2><?= admin_l('Формы') ?></h2>
        <p><?= site_escape($formUx[$adminLanguage] ?? $formUx['ru']) ?></p>
        <p><?= site_escape(site_phone_country_labels($adminLanguage)[2]) ?></p>
    </section>
    <section class="panel form-stack" id="help-page-presets">
        <h2><?= site_escape($pageTemplateHelp[0]) ?></h2>
        <p><?= site_escape($pageTemplateHelp[12]) ?></p>
        <p><?= site_escape(admin_page_template_catalog_labels($adminLanguage)['help']) ?></p>
        <p><?= site_escape($presetHelp[0]) ?></p>
        <h3><?= site_escape($presetHelp[1]) ?></h3>
        <p><?= site_escape($presetHelp[2]) ?></p>
        <pre><code>.page-preset
.page-preset-grid
.page-preset-hero
.page-preset-card
.page-preset-price</code></pre>
    </section>
    <section class="panel form-stack" id="help-design-themes">
        <h2><?= site_escape($designHelp[0]) ?></h2>
        <?php $themesHelp = require SITE_ROOT . '/app/help-theme-content.php'; $themesHelp = $themesHelp[$adminLanguage] ?? $themesHelp['ru']; ?>
        <p><?= site_escape($themesHelp[0]) ?></p>
        <p><?= site_escape($themesHelp[3]) ?></p>
        <p><?= site_escape($designHelp[9]) ?></p>
        <p><?= site_escape($designHelp[13]) ?></p>
        <?php $gardenHelp = require SITE_ROOT . '/app/help-tihiy-sad.php'; ?>
        <p><?= site_escape($gardenHelp[$adminLanguage] ?? $gardenHelp['ru']) ?></p>
        <?php $dezHelp = require SITE_ROOT . '/app/help-dezinfektori.php'; ?>
        <p><?= site_escape($dezHelp[$adminLanguage] ?? $dezHelp['ru']) ?></p>
        <pre><code>[iblock=reviews limit=6]/data/dez-reviews.txt[/iblock]</code></pre>
        <pre><code>templates/themes/demo/header.php
templates/themes/demo/css/theme.css
templates/themes/demo/components/banners.php</code></pre>
    </section>
    <section class="panel form-stack" id="help-portfolio">
        <?php $portfolioLabels = require SITE_ROOT . '/app/help-portfolio.php'; $portfolioLabels = $portfolioLabels[$adminLanguage] ?? $portfolioLabels['ru']; ?>
        <h2><?= site_escape($portfolioLabels[0]) ?></h2>
        <p><?= site_escape($themesHelp[6]) ?></p>
        <?php $iblockLimitHelp = require SITE_ROOT . '/app/help-iblock-limit.php'; ?>
        <p><?= site_escape(($iblockLimitHelp[$adminLanguage] ?? $iblockLimitHelp['ru'])[1]) ?></p>
        <?php $iblockFileHelp = require SITE_ROOT . '/app/help-iblock-file-picker.php'; ?>
        <p><?= site_escape(($iblockFileHelp[$adminLanguage] ?? $iblockFileHelp['ru'])[3]) ?></p>
        <pre><code>[portfolio]
[iblock=portfolio]/data/kontur-works.txt[/iblock]
[iblock=portfolio limit=2]/data/kontur-works.txt[/iblock]</code></pre>
    </section>
    <section class="panel form-stack" id="help-fonts">
        <?php $appearanceHelp = site_appearance_labels($adminLanguage); ?>
        <h2><?= site_escape($appearanceHelp[0] . ' · ' . $appearanceHelp[1]) ?></h2>
        <p><?= admin_l('Шаблоны оформления') ?> → <?= site_escape($appearanceHelp[0] . ' / ' . $appearanceHelp[1]) ?></p>
        <p><?= site_escape($appearanceHelp[8]) ?></p>
        <p><?= admin_l('Настройки') ?> → <?= site_escape($appearanceHelp[2] . ' → ' . $appearanceHelp[6]) ?></p>
    </section>
    <section class="panel form-stack" id="help-functions">
        <h2><?= admin_l('Новое в версии 1.6.3') ?></h2>
        <?php foreach ($functionsHelp as $topic => $instructions): ?>
            <div class="help-entry"><h3><?= admin_l($topic) ?></h3><p><?= admin_l($instructions) ?></p></div>
        <?php endforeach; ?>
    </section>
    <section class="panel form-stack" id="help-i18n">
        <h2><?= admin_l('Новое в версии 1.6.2') ?></h2>
        <?php foreach ($i18nHelp as $topic => $instructions): ?>
            <div class="help-entry"><h3><?= admin_l($topic) ?></h3><p><?= admin_l($instructions) ?></p></div>
        <?php endforeach; ?>
    </section>
    <section class="panel form-stack" id="help-current">
        <h2><?= admin_l('Новое в версии 1.6.1') ?></h2>
        <?php foreach (($currentHelp[$adminLanguage] ?? $currentHelp['ru']) as $topic => $instructions): ?>
            <div class="help-entry"><h3><?= site_escape($topic) ?></h3><p><?= site_escape($instructions) ?></p></div>
        <?php endforeach; ?>
    </section>
    <section class="panel form-stack" id="help-release">
        <h2><?= admin_l('Новое в версии 1.6.0') ?></h2>
        <?php foreach ($releaseHelp as $index => list($topic, $instructions)): ?>
            <div class="help-entry">
                <h3><?= site_escape($topic) ?></h3>
                <p><?= site_escape($instructions) ?></p>
                <?php foreach (($releaseExamples[$index] ?? []) as $example): ?>
                    <pre><code><?= site_escape($example) ?></code></pre>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </section>
    <section class="panel form-stack admin-faq" id="help-admin-faq">
        <h2><?= admin_l('FAQ по админке') ?></h2>
        <?php foreach ($faq as $topic => $questions): ?>
            <section class="faq-topic">
                <h3><?= admin_l($topic) ?></h3>
                <?php foreach ($questions as $question => $answer): ?>
                    <details>
                        <summary><?= admin_l($question) ?></summary>
                        <p><?= admin_l($answer) ?></p>
                    </details>
                <?php endforeach; ?>
            </section>
        <?php endforeach; ?>
    </section>
    <h2><?= admin_l('Помощь по шаблонам и компонентам') ?></h2>
    <?php foreach ($guide as $anchor => $group): ?>
        <section class="panel form-stack" id="help-<?= site_escape($anchor) ?>">
            <h2><?= admin_l($group['title']) ?></h2>
            <?php foreach ($group['entries'] as list($title, $description, $examples)): ?>
                <div class="help-entry">
                    <h3><?= admin_l($title) ?></h3>
                    <p><?= admin_l($description) ?></p>
                    <?php foreach ($examples as $example): ?>
                        <pre><code><?= admin_l($example) ?></code></pre>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </section>
    <?php endforeach; ?>
</div>
