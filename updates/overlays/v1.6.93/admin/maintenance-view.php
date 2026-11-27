<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}
$index = site_search_index();
?>
<h1><?= admin_l('Обслуживание') ?></h1>
<?php $phpLabels = site_php_admin_labels($adminLanguage); ?>
<section class="panel form-stack" id="php-info">
    <h2><?= site_escape($phpLabels[0]) ?></h2>
    <p>PHP <?= site_escape(PHP_VERSION) ?> · <code><?= site_escape(PHP_SAPI) ?></code></p>
    <div><a class="button" href="<?= site_escape($maintenanceUrl . '&phpinfo=1') ?>" target="_blank" rel="noopener">phpinfo()</a></div>
</section>
<p id="content-backups"><a href="<?= site_escape($backupsUrl) ?>"><?= site_escape(admin_schedule_labels($adminLanguage)[0]) ?></a></p>
<?php require __DIR__ . '/content-cleanup-view.php'; ?>
<div class="form-stack">
    <form method="post" action="<?= site_escape($maintenanceUrl) ?>" class="panel form-stack" id="search-index-settings" data-update-stream>
        <h2><?= admin_l('Индексация разделов и страниц') ?></h2>
        <input type="hidden" name="csrf" value="<?= site_escape($token) ?>">
        <p><?= site_escape(admin_l('Страниц в индексе')) ?> — <?= count($index['pages'] ?? []) ?></p>
        <?php if (isset($index['built'])): ?>
            <p><time datetime="<?= date('c', $index['built']) ?>"><?= site_escape(date(site_region_settings()['date'] . ' ' . site_region_settings()['time'], $index['built'])) ?></time></p>
        <?php endif; ?>
        <p class="hint"><?= admin_l('После правок в админке индекс обновляется автоматически. Для файлов, изменённых по FTP, используйте расписание или кнопку ниже.') ?></p>
        <?php if (site_shop_version() !== '' && is_file(site_shop_directory() . '/widgets.php')): ?>
            <p class="hint"><?= site_escape(admin_update_progress_labels($adminLanguage)['index-shop']) ?></p>
        <?php endif; ?>
        <input type="hidden" name="action" value="index-pages">
        <div><button class="button-primary" type="submit"><?= admin_l('Перестроить индекс') ?></button></div>
        <?php $progressMode = 'index'; require __DIR__ . '/update-progress-view.php'; ?>
    </form>
    <?php $jobs = admin_jobs_settings(); ?>
    <form method="post" action="<?= site_escape($maintenanceUrl) ?>" class="panel form-stack">
        <h2><?= admin_l('Обслуживание по расписанию') ?></h2>
        <input type="hidden" name="csrf" value="<?= site_escape($token) ?>">
        <input type="hidden" name="action" value="save-jobs">
        <label class="choice"><input type="checkbox" name="index" value="1"<?= $jobs['index'] ? ' checked' : '' ?>> <?= admin_l('Переиндексировать страницы каждые 15 минут') ?></label>
        <p class="hint"><?= admin_l('Добавьте команду в планировщик сервера с запуском каждые 15 минут.') ?> <code>php update.php --scheduled</code></p>
        <div><button class="button-primary"><?= admin_l('Сохранить') ?></button></div>
    </form>
    <form method="post" action="<?= site_escape($maintenanceUrl) ?>" class="panel form-stack">
        <h2><?= site_escape(admin_l('Кеш страниц')) ?></h2>
        <input type="hidden" name="csrf" value="<?= site_escape($token) ?>">
        <label class="choice"><input type="checkbox" name="cache" value="1"<?= site_performance_settings()['cache'] ? ' checked' : '' ?>>
            <?= site_escape(admin_l('Кешировать контент')) ?></label>
        <p class="hint"><?= site_escape(admin_l('Кеш хранит разобранные страницы. Формы и динамические компоненты обрабатываются при каждом запросе.')) ?></p>
        <div class="header-actions">
            <button class="button-primary" type="submit" name="action" value="save-cache"><?= site_escape(admin_l('Сохранить')) ?></button>
            <button type="submit" name="action" value="clear-cache"><?= site_escape(admin_l('Очистить кеш')) ?></button>
        </div>
    </form>
</div>
<section class="panel form-stack" id="service-files">
    <?php $fileLabels = admin_maintenance_labels($adminLanguage); ?>
    <h2><?= site_escape($fileLabels['files']) ?></h2>
    <?php foreach (ADMIN_SERVICE_FILES as $name => $limit): ?>
        <details class="field-config"<?= admin_input($_POST, 'file') === $name ? ' open' : '' ?>>
            <summary><code><?= site_escape($name) ?></code></summary>
            <?php try { $snapshot = admin_service_snapshot($name); } catch (RuntimeException $exception) { ?>
                <p class="notice error"><?= admin_l($exception->getMessage()) ?></p>
                </details>
                <?php continue; ?>
            <?php } ?>
            <form class="form-stack" method="post" action="<?= site_escape($maintenanceUrl) ?>#service-files">
                <input type="hidden" name="csrf" value="<?= site_escape($token) ?>">
                <input type="hidden" name="file" value="<?= site_escape($name) ?>">
                <input type="hidden" name="revision" value="<?= site_escape($snapshot['revision']) ?>">
                <textarea class="short-editor" rows="10" name="code" aria-label="<?= site_escape($name) ?>" spellcheck="false"<?= $name === 'sitemap.xml' ? ' data-code-mode="html"' : '' ?>><?= "\n" . site_escape($error !== '' && admin_input($_POST, 'file') === $name ? admin_input($_POST, 'code') : $snapshot['content']) ?></textarea>
                <div class="header-actions">
                    <button class="button-primary" name="action" value="save-service-file"><?= admin_l('Сохранить') ?></button>
                    <?php if ($name === 'sitemap.xml'): ?>
                        <button name="action" value="generate-sitemap"><?= site_escape($fileLabels['generate']) ?></button>
                    <?php elseif ($name === '.htaccess' && is_file(SITE_ROOT . '/admin/.htaccess-backup.php')): ?>
                        <button name="action" value="restore-htaccess"><?= site_escape($fileLabels['restore']) ?></button>
                    <?php endif; ?>
                </div>
            </form>
        </details>
    <?php endforeach; ?>
</section>
<?php try { $redirectFile = admin_service_snapshot('.htaccess'); } catch (RuntimeException $exception) { $redirectFile = null; } ?>
<?php if ($redirectFile !== null): ?>
    <form class="panel form-stack" id="redirects" method="post" action="<?= site_escape($maintenanceUrl) ?>#redirects">
        <h2><?= site_escape(admin_route_labels($adminLanguage)['redirects']) ?></h2>
        <input type="hidden" name="action" value="save-redirects">
        <input type="hidden" name="csrf" value="<?= site_escape($token) ?>">
        <input type="hidden" name="revision" value="<?= site_escape($redirectFile['revision']) ?>">
        <label class="field"><?= site_escape(admin_route_labels($adminLanguage)['redirect-rules']) ?>
            <?php $ruleLines = []; foreach (site_redirect_rules($redirectFile['content']) as $from => $to) { $ruleLines[] = $from . ' => ' . $to; } ?>
            <textarea name="rules" class="short-editor" rows="7" spellcheck="false" placeholder="/contact.html =&gt; /contacts/"><?= "\n" . site_escape($error !== '' && admin_input($_POST, 'action') === 'save-redirects' ? admin_input($_POST, 'rules') : implode("\n", $ruleLines)) ?></textarea>
        </label>
        <div><button class="button-primary" type="submit"><?= admin_l('Сохранить') ?></button></div>
    </form>
<?php endif; ?>
<section class="panel form-stack" id="error-pages">
    <h2><?= site_escape(admin_route_labels($adminLanguage)['error-pages']) ?></h2>
    <div><a class="button" href="<?= site_escape(admin_page_edit_url($adminUrl, 'error/404/index.php')) ?>">404 · <?= admin_l('Править страницу') ?></a></div>
    <?php foreach (SITE_ERROR_PAGES as $status => $label): ?>
        <?php $serviceFile = 'error/' . $status . '.html'; ?>
        <?php try { $snapshot = admin_service_snapshot($serviceFile); } catch (RuntimeException $exception) { continue; } ?>
        <details class="field-config"<?= admin_input($_POST, 'file') === $serviceFile ? ' open' : '' ?>>
            <summary><?= $status ?> · <code><?= site_escape('/' . $serviceFile) ?></code></summary>
            <form class="form-stack" method="post" action="<?= site_escape($maintenanceUrl) ?>#error-pages">
                <input type="hidden" name="csrf" value="<?= site_escape($token) ?>">
                <input type="hidden" name="file" value="<?= site_escape($serviceFile) ?>">
                <input type="hidden" name="revision" value="<?= site_escape($snapshot['revision']) ?>">
                <textarea name="code" class="short-editor" rows="8" data-code-mode="html" aria-label="<?= $status ?>" spellcheck="false"><?= "\n" . site_escape($error !== '' && admin_input($_POST, 'file') === $serviceFile ? admin_input($_POST, 'code') : $snapshot['content']) ?></textarea>
                <div class="header-actions">
                    <button class="button-primary" name="action" value="save-service-file"><?= admin_l('Сохранить') ?></button>
                    <a href="<?= site_escape(site_url($base, $serviceFile)) ?>"><?= admin_l('Открыть страницу') ?></a>
                </div>
            </form>
        </details>
    <?php endforeach; ?>
</section>
