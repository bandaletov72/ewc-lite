<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}
$description = site_expand_text($fields['Description'] !== '' ? $fields['Description'] : $settings['description'], $snippets);
$keywords = site_expand_text($fields['Keywords'] ?? '', $snippets);
$assetUrls = site_asset_urls($base, $page);
$designCss = site_design_asset($base, 'css/theme.css');
$designJs = site_design_asset($base, 'js/theme.js');
$canonical = site_canonical_url($base, $virtual['canonicalPath'] ?? $page['path']);
try {
    $headerInserts = site_header_inserts();
} catch (RuntimeException $exception) {
    $headerInserts = ['head' => '', 'bodyEnd' => ''];
}
?>
<!doctype html>
<html lang="<?= site_escape($locale) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php if (isset($public['fragment'])): ?>
        <meta name="site-fragment-token" content="<?= site_escape($public['fragment']) ?>">
    <?php endif; ?>
    <title><?= site_escape($title . ' | ' . $settings['name']) ?></title>
    <?php if ($searchMode || http_response_code() === 404 || !empty($virtual['noindex'])): ?>
        <meta name="robots" content="noindex,follow">
    <?php endif; ?>
    <?php if (!$searchMode && http_response_code() !== 404 && $canonical !== ''): ?>
        <link rel="canonical" href="<?= site_escape($canonical) ?>">
    <?php endif; ?>
    <?php if ($description !== ''): ?>
        <meta name="description" content="<?= site_escape($description) ?>">
    <?php endif; ?>
    <?php if ($keywords !== ''): ?>
        <meta name="keywords" content="<?= site_escape($keywords) ?>">
    <?php endif; ?>
    <link rel="icon" type="image/png" href="<?= site_escape(site_url($base, $brand['favicon'] ?: 'templates/icons/site.png')) ?>">
    <?php require site_template_file('library-assets.php'); ?>
    <?php if ($editor): ?>
        <script src="<?= site_escape(site_url($base, 'templates/copy-code-script.php') . '?v=' . (require SITE_ROOT . '/app/version.php')) ?>" defer></script>
        <script src="<?= site_escape(site_url($base, 'templates/iblock-file-picker-script.php') . '?v=' . (require SITE_ROOT . '/app/version.php')) ?>" defer></script>
    <?php endif; ?>
    <?php if (($editor || site_shop_enabled()) && empty($libraries['enabled']['fontawesome'])): ?>
        <link rel="stylesheet" href="<?= site_escape(site_font_awesome_stylesheet($base)) ?>">
    <?php endif; ?>
    <?php if (site_shop_enabled()): ?>
        <link rel="stylesheet" href="<?= site_escape(site_url($base, 'shop/') . '?asset=css&v=' . site_shop_version() . '.' . filemtime(site_shop_directory() . '/shop.css')) ?>">
    <?php endif; ?>
    <link rel="stylesheet" href="<?= site_escape(site_url($base, 'templates/css/site.css') . '?v=' . (require SITE_ROOT . '/app/version.php') . '.' . filemtime(SITE_ROOT . '/templates/css/site.css')) ?>">
    <link rel="stylesheet" href="<?= site_escape(site_url($base, 'templates/theme.php')) ?>">
    <?php if ($designCss !== ''): ?><link rel="stylesheet" href="<?= site_escape($designCss) ?>"><?php endif; ?>
    <?php foreach ($assetUrls['CSS'] as $url): ?>
        <link rel="stylesheet" href="<?= site_escape($url) ?>">
    <?php endforeach; ?>
    <script src="<?= site_escape(site_url($base, 'templates/js/site.js') . '?v=' . (require SITE_ROOT . '/app/version.php') . '.' . filemtime(SITE_ROOT . '/templates/js/site.js')) ?>" defer></script>
    <?php if ($settings['searchMode'] !== 'off'): ?>
        <script src="<?= site_escape(site_url($base, 'templates/search-browser.php') . '?v=' . (require SITE_ROOT . '/app/version.php')) ?>" defer></script>
    <?php endif; ?>
    <?php if ($editor && site_code_editor_on()): ?>
        <script src="<?= site_escape(site_code_editor_url($base)) ?>" defer></script>
    <?php endif; ?>
    <?php $analytics = site_cookie_allows('analytics') ? site_public_analytics() : []; ?>
    <?php if ($analytics): ?>
        <meta name="site-analytics" content="<?= site_escape((string) json_encode($analytics)) ?>">
    <?php endif; ?>
    <?php if (site_cookie_allows('analytics')): ?>
        <?php $botfaqtor = site_integration_settings()['botfaqtor'] ?? []; ?>
        <?php if (!empty($botfaqtor['enabled']) && is_string($botfaqtor['code'] ?? null) && site_valid_botfaqtor_code($botfaqtor['code'])): ?>
            <?= $botfaqtor['code'] ?>
        <?php endif; ?>
    <?php endif; ?>
    <?php $jivo = site_cookie_allows('chat') ? site_jivo_widget() : ''; ?>
    <?php if ($jivo !== ''): ?>
        <script async src="https://code.jivosite.com/widget/<?= site_escape($jivo) ?>"></script>
    <?php endif; ?>
    <?php foreach ($assetUrls['JS'] as $url): ?>
        <script src="<?= site_escape($url) ?>" defer></script>
    <?php endforeach; ?>
    <?php if ($designJs !== ''): ?><script src="<?= site_escape($designJs) ?>" defer></script><?php endif; ?>
    <?= $headerInserts['head'] ?>
</head>
<body id="site-top"<?= $settings['scrollAnimation'] !== 'off'
    ? ' data-scroll-animation="' . site_escape($settings['scrollAnimation']) . '"' : '' ?><?= $editor ? ' data-code-auto="' . site_escape(site_translation('Авто', $editor['language']))
    . '" data-code-syntax="' . site_escape(site_translation('Язык подсветки', $editor['language'])) . '"' : '' ?>>
    <a class="skip-link" href="#content"><?= site_escape(site_translation('Перейти к содержимому', $locale)) ?></a>
    <?php if ($editor): require site_template_file('editor-bar.php'); endif; ?>
    <?php if ($components['layoutEdit']): ?>
        <div class="layout-edit-outline" data-layout-edit-outline
            data-page-label="<?= site_escape(site_translation('Править страницу', $editor['language'])) ?>"
            data-snippet-label="<?= site_escape(site_translation('Редактировать подшаблон', $editor['language'])) ?>"
            data-page-hint="<?= site_escape(site_edit_layout_labels($editor['language'])[7]) ?>" hidden>
            <div class="layout-edit-actions">
                <a class="button" data-layout-edit-control href="<?= site_escape(site_url($base, 'admin/') . '?layout=1') ?>"><?= site_escape(site_edit_layout_labels($editor['language'])[0]) ?></a>
                <small data-layout-edit-hint><?= site_escape(site_edit_layout_labels($editor['language'])[5]) ?></small>
            </div>
        </div>
    <?php endif; ?>
    <?php if ($editor): ?>
        <?php if (!$searchMode && $page['path'] !== ''): ?>
            <?php $pageEditLabel = site_escape(site_translation('Править страницу', $editor['language'])); ?>
            <dialog class="editor-dialog component-data-dialog" id="page-content-editor" aria-labelledby="page-content-editor-title">
                <div class="header-actions editor-dialog-heading">
                    <h2 id="page-content-editor-title"><?= $pageEditLabel ?></h2>
                    <button class="dialog-close" type="button" data-close-page-dialog aria-label="<?= site_escape(site_translation('Закрыть', $editor['language'])) ?>"></button>
                </div>
                <iframe data-component-data-iframe title="<?= $pageEditLabel ?>"></iframe>
            </dialog>
        <?php endif; ?>
        <dialog class="editor-dialog component-data-dialog" id="page-layout-editor" aria-labelledby="page-layout-editor-title">
            <div class="header-actions editor-dialog-heading">
                <h2 id="page-layout-editor-title"><?= site_escape(site_edit_layout_labels($editor['language'])[0]) ?></h2>
                <button class="dialog-close" type="button" data-close-page-dialog aria-label="<?= site_escape(site_translation('Закрыть', $editor['language'])) ?>"></button>
            </div>
            <iframe data-component-data-iframe title="<?= site_escape(site_edit_layout_labels($editor['language'])[0]) ?>"></iframe>
        </dialog>
    <?php endif; ?>
    <header class="site-header">
        <div class="container header-row">
            <div class="desktop-header"><?= site_layout_block('header', $components) ?></div>
            <?= site_layout_block('mobile-menu', $components) ?>
            <div class="layout-desktop-only header-language"><?= site_layout_placeholder('lang', true) ?></div>
        </div>
    </header>

    <?= site_layout_placeholder('main-menu', true) ?>
    <?= site_layout_placeholder('search', true) ?>
    <?= site_layout_placeholder('bread-crumbs', true) ?>
    <?php $sidebar = site_layout_block('menu', $components); ?>
    <div class="container site-layout<?= trim($sidebar) !== '' ? ' has-sidebar' : '' ?>">
        <?php $embeddedContent = true; require site_template_file('content.php'); ?>
        <?php if (trim($sidebar) !== ''): ?>
            <aside class="site-sidebar"><?= $sidebar ?></aside>
        <?php endif; ?>
    </div>

    <?= site_layout_block('footer', $components) ?>
    <?= $headerInserts['bodyEnd'] ?>
</body>
</html>
