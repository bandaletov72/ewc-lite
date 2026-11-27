<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}

function admin_media_file_matches(string $path, string $extension): bool
{
    $header = file_get_contents($path, false, null, 0, 32);
    if (!is_string($header) || $header === '') return false;
    switch ($extension) {
        case 'pdf':
            return strpos($header, '%PDF-') === 0;
        case 'mp3':
            return strpos($header, 'ID3') === 0 || preg_match('/\A\xff[\xe2-\xff]/s', $header) === 1;
        case 'mp4':
        case 'mov':
            return substr($header, 4, 4) === 'ftyp';
        case '7z':
            return substr($header, 0, 6) === "7z\xbc\xaf\x27\x1c";
        case 'doc':
        case 'xls':
            return substr($header, 0, 8) === "\xd0\xcf\x11\xe0\xa1\xb1\x1a\xe1";
        case 'zip':
            return in_array(substr($header, 0, 4), ["PK\x03\x04", "PK\x05\x06", "PK\x07\x08"], true);
        case 'docx':
        case 'xlsx':
            if (!class_exists('ZipArchive') || substr($header, 0, 4) !== "PK\x03\x04") return false;
            $archive = new ZipArchive();
            if ($archive->open($path, ZipArchive::CHECKCONS) !== true) return false;
            try {
                return $archive->locateName('[Content_Types].xml') !== false
                    && $archive->locateName($extension === 'docx' ? 'word/document.xml' : 'xl/workbook.xml') !== false;
            } finally {
                $archive->close();
            }
        default:
            return false;
    }
}

function admin_upload_component_asset(array $upload, string $folder, array $extensions, bool $validateMedia = false): string
{
    $name = $upload['name'] ?? '';
    $temporary = $upload['tmp_name'] ?? '';
    if (($upload['error'] ?? null) !== UPLOAD_ERR_OK || !is_string($temporary)
        || !is_uploaded_file($temporary) || !is_string($name)) {
        throw new RuntimeException('Не удалось загрузить файл. Проверьте ограничения загрузки PHP.');
    }
    $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
    if (!in_array($extension, $extensions, true) || filesize($temporary) > 33554432) {
        throw new RuntimeException('Этот формат файла не поддерживается или файл превышает 32 МиБ.');
    }
    $imageTypes = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png',
        'gif' => 'image/gif', 'webp' => 'image/webp', 'avif' => 'image/avif'];
    if (isset($imageTypes[$extension])) {
        $image = @getimagesize($temporary);
        if (($image['mime'] ?? '') !== $imageTypes[$extension]
            && ($extension !== 'avif' || !preg_match('/\A....ftypavi[fs]/s', file_get_contents($temporary, false, null, 0, 16)))) {
            throw new RuntimeException('Содержимое изображения не соответствует его расширению.');
        }
    } elseif ($validateMedia && !admin_media_file_matches($temporary, $extension)) {
        throw new RuntimeException(admin_localized_label('Содержимое файла не соответствует расширению.', 'The file contents do not match its extension.'));
    }
    $stem = admin_image_name($name) ?: 'file';
    if (!site_valid_part($stem)) $stem = 'file-' . $stem;
    $target = $folder . '/' . $stem . '.' . $extension;
    for ($number = 2; file_exists($target) || is_link($target); $number++) {
        $target = $folder . '/' . $stem . '-' . $number . '.' . $extension;
    }
    if (!move_uploaded_file($temporary, $target)) {
        throw new RuntimeException('Не удалось записать файл в папку.');
    }
    clearstatcache(true);
    return basename($target);
}

function admin_component_media(string $action, array $input, string $base)
{
    $type = admin_input($input, 'type');
    $argument = admin_input($input, 'folder');
    $asset = $type === 'files' ? 'files' : 'images';
    $path = in_array($type, ['files', 'gal', 'fotos', 'banners'], true) ? site_asset_path($argument, $asset) : '';
    if ($path === '') {
        admin_json(422, ['message' => 'Укажите допустимую папку компонента.']);
    }
    $extensions = site_component_asset_extensions($type);
    $storage = site_asset_path($argument, $asset, true);
    $getFiles = static function () use ($argument, $base, $asset, $extensions, $type, $input, $storage): array {
        $path = site_asset_path($argument, $asset);
        $files = site_asset_files($argument, $base, $asset, $extensions);
        if ($type === 'banners') {
            $set = strtolower(admin_input($input, 'banner'));
            if (!site_valid_banner_set_id($set) && !site_valid_snippet($set)) $set = '';
            $set = site_banner_id($set, $argument);
            foreach ($files as &$file) {
                $file['edit'] = site_banner_admin_url($base, $set, $argument,
                    ['slide' => site_banner_file_slide_id($path, $file['name'])]) . '#banner-editor';
            }
        }
        $result = ['files' => $files, 'storage' => '/' . $storage . '/'];
        if (admin_input($input, 'browse') === '1') {
            $root = SITE_ROOT . '/' . $path;
            $result['folders'] = [];
            if (is_dir($root) && realpath($root) === str_replace('/', DIRECTORY_SEPARATOR, $root)) {
                foreach (scandir($root) ?: [] as $name) {
                    if (site_valid_part($name) && is_dir($root . '/' . $name) && !is_link($root . '/' . $name)
                        && realpath($root . '/' . $name) === str_replace('/', DIRECTORY_SEPARATOR, $root . '/' . $name)) {
                        $result['folders'][] = $name;
                    }
                }
            }
        }
        return $result;
    };
    if ($action === 'list-media') {
        admin_json(200, $getFiles());
    }

    try {
        $lock = admin_lock();
        try {
            $folder = SITE_ROOT . '/' . $storage;
            if ($storage !== $path && $action !== 'delete-media-folder') {
                admin_materialize_theme_media($path, $storage, $extensions);
            }
            if ($action === 'create-media-folder') {
                foreach (explode('/', $storage) as $part) {
                    if (!site_valid_part($part)) throw new RuntimeException('В именах папок разрешены A–Z, a–z, 0–9, дефис и подчёркивание.');
                }
                admin_make_page_directory($storage . '/file');
            } elseif ($action === 'upload-media') {
                admin_make_page_directory($storage . '/file');
                admin_upload_component_asset($_FILES['upload'] ?? [], $folder, $extensions);
            } elseif ($action === 'delete-media-folder') {
                $name = admin_input($input, 'file');
                $language = $GLOBALS['adminLanguage'] ?? 'ru';
                if (!site_valid_part($name) || admin_input($input, 'confirm') !== '1') {
                    throw new RuntimeException(site_media_folder_labels($language)[3]);
                }
                admin_remove_media_directory($storage . '/' . $name, $extensions, $language);
            } else {
                $list = array_column(site_asset_files($argument, $base, $asset, $extensions), 'name');
                if ($action === 'delete-media') {
                    $name = admin_input($input, 'file');
                    if (!in_array($name, $list, true) || !@unlink($folder . '/' . $name)) {
                        throw new RuntimeException('Файл уже удалён или недоступен для удаления.');
                    }
                } else {
                    $order = json_decode(admin_input($input, 'order'), true);
                    if (!is_array($order) || array_values($order) !== $order || count($order) !== count($list)) {
                        throw new RuntimeException('Список файлов изменился. Обновите его.');
                    }
                    $left = $list;
                    foreach ($order as $name) {
                        $position = array_search($name, $left, true);
                        if ($position === false) {
                            throw new RuntimeException('Список файлов изменился. Обновите его.');
                        }
                        unset($left[$position]);
                    }
                    admin_write_record($folder . '/.asset-order.php', $order, 131072);
                }
            }
        } finally {
            fclose($lock);
        }
        admin_json(200, $getFiles());
    } catch (RuntimeException $exception) {
        admin_json(422, ['message' => $exception->getMessage()]);
    }
}

function admin_materialize_theme_media(string $shared, string $themed, array $extensions)
{
    $source = SITE_ROOT . '/' . $shared;
    $target = SITE_ROOT . '/' . $themed;
    if (is_dir($target) || !is_dir($source) || realpath($source) !== str_replace('/', DIRECTORY_SEPARATOR, $source)) return;
    admin_make_page_directory(str_replace(DIRECTORY_SEPARATOR, '/', dirname($themed)) . '/file');
    $staged = dirname($target) . '/.media-' . bin2hex(random_bytes(8));
    if (!@mkdir($staged, 0700)) throw new RuntimeException('Не удалось подготовить файлы темы дизайна.');
    $copied = [];
    try {
        foreach (new FilesystemIterator($source, FilesystemIterator::SKIP_DOTS) as $file) {
            if ($file->isLink() || !$file->isFile()) continue;
            $name = $file->getFilename();
            if ($name !== '.asset-order.php' && !in_array(strtolower($file->getExtension()), $extensions, true)) continue;
            $new = $staged . '/' . $name;
            $copied[] = $new;
            if (!@copy($file->getPathname(), $new)) throw new RuntimeException('Не удалось скопировать файл в тему дизайна.');
        }
        if (!@rename($staged, $target)) throw new RuntimeException('Не удалось сохранить папку темы дизайна.');
        $copied = [];
        clearstatcache(true);
    } finally {
        foreach ($copied as $file) @unlink($file);
        if (is_dir($staged)) @rmdir($staged);
    }
}
