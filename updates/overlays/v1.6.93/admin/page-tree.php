<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}

function admin_page_edit_url(string $adminUrl, string $path): string
{
    return $adminUrl . '?' . http_build_query(['page' => site_page_url('', $path)]);
}

function admin_folder_edit_url(string $adminUrl, string $folder, bool $hasPage): string
{
    return $hasPage ? admin_page_edit_url($adminUrl, $folder . '/index.php')
        : $adminUrl . '?' . http_build_query(['new' => '1', 'parent' => $folder, 'section' => '1']);
}

function admin_pages_ajax(string $action, array $input, string $base, string $adminUrl)
{
    $selected = admin_input($input, 'current_path');
    $path = admin_input($input, 'path');
    $revision = admin_input($input, 'revision');
    $touched = [];
    $originalSelected = $selected;
    $openFolder = '';
    try {
        if ($action === 'page-order') {
            $items = json_decode(admin_input($input, 'items'), true);
            admin_reorder_pages(admin_input($input, 'folder'), is_array($items) ? $items : []);
            $touched = array_column($items, 'revision', 'path');
            $message = 'Порядок страниц сохранён.';
        } elseif ($action === 'page-menu') {
            admin_update_page_menu($path, $revision, admin_input($input, 'visible') === '1' ? '1' : '0');
            $touched[$path] = $revision;
            $message = 'Меню обновлено.';
        } elseif ($action === 'folder-move') {
            $moved = admin_move_folder($path, admin_input($input, 'folder'));
            $openFolder = $moved['target'];
            $touched = $moved['revisions'];
            if (isset($touched[$selected])) {
                $selected = $openFolder . substr($selected, strlen($path));
            }
            $message = 'Папка перемещена в /' . $openFolder . '/.';
        } elseif ($action === 'page-move') {
            $destination = admin_move_page($path, $revision, admin_input($input, 'folder'));
            $touched[$path] = $revision;
            if ($selected === $path) {
                $selected = $destination;
            }
            $message = 'Страница перемещена на ' . site_page_url('', $destination);
        } else {
            $message = admin_update_page_status($path, $revision, admin_input($input, 'operation'));
            $touched[$path] = $revision;
        }
    } catch (RuntimeException $exception) {
        admin_json(409, ['message' => $exception->getMessage()]);
    }

    $result = [
        'message' => $message,
        'tree' => admin_page_tree_html(site_page_tree(site_pages()), $selected, $base, $adminUrl),
        'openFolder' => $openFolder,
    ];
    if (isset($touched[$originalSelected]) && hash_equals($touched[$originalSelected], admin_input($input, 'current_revision'))) {
        $current = site_read_page($selected);
        $result['page'] = $current === null ? null : [
            'path' => $selected,
            'revision' => $current['revision'],
            'menu' => $current['fields']['MenuVisible'] === '1',
            'hidden' => $current['fields']['Hidden'] === '1',
            'available' => site_page_is_public($current['fields']),
            'order' => $current['fields']['Order'],
            'address' => site_page_url('', $selected),
            'publicUrl' => site_page_url($base, $selected),
            'editUrl' => admin_page_edit_url($adminUrl, $selected),
        ];
    }
    admin_json(200, $result);
}

function admin_page_tree_html(array $tree, string $selected, string $base, string $adminUrl): string
{
    ob_start();
    admin_page_tree_branch($tree, '.', $selected, $base, $adminUrl);
    return (string) ob_get_clean();
}

function admin_page_tree_branch(array $tree, string $folder, string $selected, string $base, string $adminUrl)
{
    $nodes = $tree[$folder] ?? [];
    if (!$nodes) {
        return;
    }
    $sortable = count(array_filter($nodes, static function (array $node): bool {
        return isset($node['page']['path']) && $node['page']['path'] !== 'index.php';
    })) > 1;
    ?>
    <ul class="page-list page-tree-list" data-page-group="<?= site_escape($folder) ?>">
        <?php foreach ($nodes as $node): ?>
            <?php $item = $node['page']; $directory = $node['folder']; ?>
            <li data-page-node<?= $item !== null ? ' data-page-path="' . site_escape($item['path']) . '" data-page-revision="' . site_escape($item['revision']) . '"' : '' ?>>
                <?php if ($directory !== ''): ?>
                    <?php $expandable = count(array_filter($tree[$directory] ?? [], static function (array $child): bool { return $child['folder'] !== ''; })) > 0; ?>
                    <?php if ($expandable): ?>
                        <details data-page-folder="<?= site_escape($directory) ?>"<?= strpos($selected, $directory . '/') === 0 ? ' open' : '' ?>>
                            <summary class="page-folder-label"><span class="page-tree-caret" aria-hidden="true"></span>
                    <?php else: ?>
                        <div class="page-folder-label" data-page-folder="<?= site_escape($directory) ?>">
                    <?php endif; ?>
                            <a class="page-tree-caption" href="<?= site_escape(admin_folder_edit_url($adminUrl, $directory, $item !== null)) ?>"<?= $selected === $directory . '/index.php' ? ' aria-current="page"' : '' ?>>
                                <i class="fa-regular fa-folder" aria-hidden="true"></i> <?= site_escape($directory === 'shop' && site_shop_widgets_enabled() ? site_shop_public_folder() : basename($directory)) ?>/
                            </a>
                            <?php if ($item !== null && $sortable): ?>
                                <?php admin_page_drag_handle(); ?>
                            <?php endif; ?>
                            <a class="folder-relocate" href="<?= site_escape(admin_folder_edit_url($adminUrl, $directory, $item !== null)) ?>#section-parent"
                                title="<?= admin_l('Переместить папку') ?>" aria-label="<?= admin_l('Переместить папку') ?> <?= site_escape($directory) ?>"><i class="fa-solid fa-folder-tree" aria-hidden="true"></i></a>
                    <?php if ($expandable): ?></summary><?php else: ?></div><?php endif; ?>
                <?php endif; ?>
                <?php if ($item !== null && $directory === ''): ?>
                    <?php
                    $path = $item['path'];
                    $editUrl = admin_page_edit_url($adminUrl, $path);
                    $hidden = $item['fields']['Hidden'] === '1';
                    $title = $item['fields']['Title'];
                    if ($path === 'index.php' && $title === 'Главная') {
                        $homeLabels = ['en' => 'Home', 'uk' => 'Головна', 'ja' => 'ホーム', 'vi' => 'Trang chủ',
                            'fr' => 'Accueil', 'de' => 'Startseite', 'es' => 'Inicio', 'pt' => 'Página inicial',
                            'ko' => '홈', 'zh' => '首页'];
                        $title = $homeLabels[$GLOBALS['adminLanguage'] ?? 'ru'] ?? $title;
                    }
                    ?>
                    <div class="page-list-entry">
                        <div class="page-entry-title">
                            <?php if ($directory === '' && $path !== 'index.php' && $sortable): ?>
                                <?php admin_page_drag_handle(); ?>
                            <?php endif; ?>
                            <a href="<?= site_escape($editUrl) ?>"<?= $selected === $path ? ' aria-current="page"' : '' ?>>
                                <i class="fa-regular fa-file-lines" aria-hidden="true"></i> <?= site_escape($title) ?><?= $hidden ? ' <i class="fa-solid fa-eye-slash" title="' . admin_l('Скрыта') . '" aria-label="' . admin_l('Скрыта') . '"></i>' : '' ?>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if ($directory !== '' && $expandable): ?>
                        <?php admin_page_tree_branch($tree, $directory, $selected, $base, $adminUrl); ?>
                    </details>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
    <?php
}

function admin_page_drag_handle()
{
    ?>
    <button type="button" class="page-drag-handle" draggable="true" data-page-drag
        aria-label="<?= admin_l('Изменить порядок страницы. Клавиши со стрелками вверх и вниз.') ?>"
        title="<?= admin_l('Перетащить страницу или нажать ↑ / ↓') ?>">⠿</button>
    <?php
}
