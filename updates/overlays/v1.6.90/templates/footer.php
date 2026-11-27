<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}
$footer = trim(str_ireplace('[author]', '', $settings['footer']));
if ($footer === '') {
    $footerText = site_escape('© ' . ($snippets['year'] ?? date('Y')) . ' ' . $settings['name']);
} else {
    $footerText = site_escape(site_expand_text($footer, $snippets));
}
?>
<footer class="site-footer">
    <div class="container footer-layout">
        <div class="footer-site">
            <p class="footer-copy"><?= $footerText ?></p>
            <nav class="site-footer-links" aria-label="<?= site_escape(site_translation('Подвал', $locale ?? 'ru')) ?>">
                <?= site_layout_block('conf-policy', $context) ?>
                <?= site_layout_block('cookies', $context) ?>
                <?= site_layout_block('top', $context) ?>
            </nav>
        </div>
        <?= site_author_credit($locale ?? 'ru') ?>
    </div>
</footer>
