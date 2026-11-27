<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}

function admin_image_library_path(string $path, bool $allFiles = false): string
{
    $path = rtrim($path, '/');
    $theme = site_design_active();
    $asset = $allFiles ? '(?:images|files)' : 'images';
    $root = $theme === '' ? '/(?:templates/)?' . $asset : '/templates/themes/' . preg_quote($theme, '~') . '/' . $asset;
    if (!preg_match('~\A' . $root . '(?:/[^/]+)*\z~u', $path) || preg_match('~[\x00-\x1F\x7F\\\\:<>"|?*]~u', $path)) {
        throw new RuntimeException('Выберите папку изображений.');
    }
    foreach (explode('/', ltrim($path, '/')) as $part) {
        if ($part[0] === '.' || substr($part, -1) === '.' || substr($part, -1) === ' ') {
            throw new RuntimeException('Выберите папку изображений.');
        }
    }
    return $path;
}

function admin_image_library_roots(bool $allFiles = false): array
{
    $theme = site_design_active();
    if ($theme === '') {
        return $allFiles ? ['/templates/images', '/images', '/templates/files', '/files'] : ['/templates/images', '/images'];
    }
    $root = '/templates/themes/' . $theme;
    return $allFiles ? [$root . '/images', $root . '/files'] : [$root . '/images'];
}

function admin_image_library_has_directory(string $path): bool
{
    $folder = SITE_ROOT . $path;
    return is_dir($folder) && realpath($folder) === str_replace('/', DIRECTORY_SEPARATOR, $folder);
}

function admin_image_library_url(string $base, string $path, string $name): string
{
    return site_url($base, implode('/', array_map('rawurlencode', explode('/', ltrim($path, '/')))) . '/' . rawurlencode($name));
}

function admin_image_library_extensions(bool $allFiles): array
{
    $images = site_component_asset_extensions('gal');
    return $allFiles ? array_values(array_unique(array_merge($images, site_component_asset_extensions('files')))) : $images;
}

function admin_image_library_items(string $path, string $base, bool $allFiles = false): array
{
    $items = [];
    if (!admin_image_library_has_directory($path)) return $items;
    $extensions = admin_image_library_extensions($allFiles);
    $images = site_component_asset_extensions('gal');
    foreach (new FilesystemIterator(SITE_ROOT . $path, FilesystemIterator::SKIP_DOTS) as $file) {
        $name = $file->getFilename();
        if ($file->isLink() || $name[0] === '.' || preg_match('//u', $name) !== 1) continue;
        if ($file->isDir()) {
            try { admin_image_library_path($path . '/' . $name, $allFiles); } catch (RuntimeException $exception) { continue; }
            $items[] = ['name' => $name, 'type' => 'directory', 'modified' => $file->getMTime(), 'size' => 0];
        } elseif ($file->isFile() && in_array($extension = strtolower($file->getExtension()), $extensions, true)) {
            $items[] = ['name' => $name, 'type' => in_array($extension, $images, true) ? 'image' : 'file',
                'url' => admin_image_library_url($base, $path, $name), 'modified' => $file->getMTime(), 'size' => $file->getSize()];
        }
    }
    usort($items, static function (array $left, array $right): int {
        return strcmp($left['type'], $right['type']) ?: strnatcasecmp($left['name'], $right['name']);
    });
    return $items;
}

function admin_image_library_transform(string $file, array $input): string
{
    if (!function_exists('imagecreatefromstring')) throw new RuntimeException('Для изменения изображения требуется расширение GD.');
    $info = @getimagesize($file);
    $dimension = static function (string $raw): int {
        if ($raw === '') return 0;
        if (!preg_match('/\A[1-9][0-9]{0,3}\z/', $raw) || (int) $raw > 6000) throw new RuntimeException('Укажите размер от 1 до 6000 пикселей.');
        return (int) $raw;
    };
    $width = $dimension(admin_input($input, 'width'));
    $height = $dimension(admin_input($input, 'height'));
    $angle = admin_input($input, 'rotate');
    $overwrite = admin_input($input, 'overwrite') === '1';
    if ($overwrite && !$width && !$height && $angle === '0') return basename($file);
    if (!$info || $info[0] * $info[1] > 12000000 || filesize($file) > 33554432 || !in_array($angle, ['0', '90', '180', '270'], true)) {
        throw new RuntimeException('Проверьте формат, поворот и размер исходного изображения.');
    }
    if ($info[2] === IMAGETYPE_GIF) throw new RuntimeException('GIF можно вставлять без преобразования. Для поворота или изменения размера выберите JPG, PNG или WebP.');
    $raw = @file_get_contents($file);
    $image = is_string($raw) ? @imagecreatefromstring($raw) : false;
    if (!$image) throw new RuntimeException('GD не удалось прочитать выбранный формат.');
    try {
        if ($angle !== '0') {
            $rotated = @imagerotate($image, -(int) $angle, imagecolorallocatealpha($image, 0, 0, 0, 127));
            if (!$rotated) throw new RuntimeException('Не удалось повернуть изображение.');
            $image = $rotated;
            unset($rotated);
        }
        $sourceWidth = imagesx($image);
        $sourceHeight = imagesy($image);
        if ($width || $height) {
            $width = $width ?: max(1, (int) round($height * $sourceWidth / $sourceHeight));
            $height = $height ?: max(1, (int) round($width * $sourceHeight / $sourceWidth));
            if ($width > 6000 || $height > 6000 || $width * $height > 12000000) throw new RuntimeException('Уменьшите размеры новой копии.');
            $resized = imagecreatetruecolor($width, $height);
            if (!$resized) throw new RuntimeException('Не удалось создать изображение.');
            imagealphablending($resized, false);
            imagecopyresampled($resized, $image, 0, 0, 0, 0, $width, $height, $sourceWidth, $sourceHeight);
            $image = $resized;
        }
        imagesavealpha($image, true);
        $encoders = [IMAGETYPE_JPEG => ['imagejpeg', 90], IMAGETYPE_PNG => ['imagepng', 5],
            IMAGETYPE_WEBP => ['imagewebp', 90]];
        if (defined('IMAGETYPE_AVIF')) $encoders[IMAGETYPE_AVIF] = ['imageavif', 90];
        $type = $overwrite || $info[2] === IMAGETYPE_JPEG ? $info[2] : IMAGETYPE_PNG;
        if (!isset($encoders[$type]) || !function_exists($encoders[$type][0])) {
            throw new RuntimeException('GD не поддерживает сохранение выбранного формата.');
        }
        list($encode, $quality) = $encoders[$type];
        $name = basename($file);
        if (!$overwrite) {
            $suffix = $type === IMAGETYPE_JPEG ? '.jpg' : '.png';
            $stem = (admin_image_name($name) ?: 'photo') . '-edit';
            $name = $stem . $suffix;
            for ($counter = 2; file_exists(dirname($file) . '/' . $name) || is_link(dirname($file) . '/' . $name); $counter++) {
                $name = $stem . '-' . $counter . $suffix;
            }
        }
        $temporary = tempnam(sys_get_temp_dir(), 'ew-image-');
        if ($temporary === false) throw new RuntimeException('Не удалось подготовить копию изображения.');
        try {
            $written = $encode($image, $temporary, $quality);
            $bytes = $written ? @file_get_contents($temporary) : false;
            if (!is_string($bytes) || $bytes === '' || !admin_replace_file(dirname($file) . '/' . $name, $bytes)) {
                throw new RuntimeException('Не удалось сохранить изображение.');
            }
        } finally {
            @unlink($temporary);
        }
        return $name;
    } finally {
        unset($image);
    }
}

function admin_image_library_page(array $items, array $input, string $path, string $selected): array
{
    $query = site_search_query($input['q'] ?? '');
    if ($query !== '') {
        $items = array_values(array_filter($items, static function (array $item) use ($query): bool {
            return preg_match('~' . preg_quote($query, '~') . '~iu', $item['name']) === 1;
        }));
    }
    $sort = admin_input($input, 'sort');
    if (in_array($sort, ['name', 'modified', 'size'], true)) {
        $direction = admin_input($input, 'direction') === 'desc' ? -1 : 1;
        usort($items, static function (array $left, array $right) use ($sort, $direction): int {
            $groups = (int) ($right['type'] === 'directory') <=> (int) ($left['type'] === 'directory');
            if ($groups !== 0) return $groups;
            $name = strnatcasecmp($left['name'], $right['name']) ?: strcmp($left['name'], $right['name']);
            $value = $sort === 'name' ? $name : (($left[$sort] ?? 0) <=> ($right[$sort] ?? 0));
            return $value === 0 ? $name : $direction * $value;
        });
    }
    $limit = 24;
    $total = count($items);
    $pages = max(1, (int) ceil($total / $limit));
    $page = min($pages, max(1, (int) admin_input($input, 'p')));
    if ($selected !== '') {
        $position = array_search($selected, array_column($items, 'name'), true);
        if ($position !== false) $page = intdiv($position, $limit) + 1;
    }
    return ['path'=>$path,'items'=>array_slice($items, ($page - 1) * $limit, $limit),
        'selected'=>$selected,'page'=>$page,'pages'=>$pages,'total'=>$total];
}

function admin_image_library(array $input, string $base)
{
    try {
        $allFiles = admin_input($input, 'library') === 'all';
        if (admin_input($input, 'path') === ':shop:' && site_shop_enabled()) {
            if (admin_input($input, 'operation') !== 'browse') throw new RuntimeException('Invalid image operation.');
            require_once site_shop_directory() . '/module.php';
            $items = [];
            foreach (glob(SHOP_IMAGES_DIR . '/*') ?: [] as $file) {
                $name = basename($file);
                if (!is_file($file) || is_link($file) || !shop_image_name($name)) continue;
                $items[] = ['name'=>$name, 'type'=>'image', 'url'=>shop_image_url($base, $name), 'shopValue'=>$name,
                    'modified'=>filemtime($file), 'size'=>filesize($file)];
            }
            usort($items, static function (array $left, array $right): int { return strnatcasecmp($left['name'], $right['name']); });
            admin_json(200, admin_image_library_page($items, $input, ':shop:', ''));
            return;
        }
        $path = admin_image_library_path(admin_input($input, 'path'), $allFiles);
        $extensions = admin_image_library_extensions($allFiles);
        $operation = admin_input($input, 'operation');
        $name = admin_input($input, 'name');
        $selected = '';
        if ($operation !== 'browse') {
            $lock = admin_lock();
            try {
                if ($operation === 'mkdir' || $operation === 'upload') {
                    if (!$allFiles && !admin_image_library_has_directory($path) && !in_array($path, admin_image_library_roots(), true)) throw new RuntimeException('Папка больше недоступна.');
                    admin_make_page_directory(ltrim($path, '/') . '/file.jpg');
                    if ($operation === 'mkdir') {
                        if (!site_valid_part($name) || !@mkdir(SITE_ROOT . $path . '/' . $name, 0755)) throw new RuntimeException('Укажите свободное имя папки из латинских букв, цифр, дефисов и подчёркиваний.');
                    } else {
                        $selected = admin_upload_component_asset($_FILES['upload'] ?? [], SITE_ROOT . $path, $extensions, $allFiles);
                    }
                } else {
                    $item = array_column(admin_image_library_items($path, $base, $allFiles), 'type', 'name')[$name] ?? null;
                    if ($item === null || !in_array($operation, ['delete', 'transform'], true) || ($operation === 'transform' && $item !== 'image')) {
                        throw new RuntimeException('Выберите доступное изображение или папку.');
                    }
                    $target = SITE_ROOT . $path . '/' . $name;
                    if (realpath($target) !== str_replace('/', DIRECTORY_SEPARATOR, $target)) {
                        throw new RuntimeException('Выберите файл или папку внутри каталога изображений.');
                    }
                    if ($operation === 'transform') {
                        if (admin_input($input, 'overwrite') === '1' && admin_input($input, 'confirm') !== '1') {
                            throw new RuntimeException(admin_localized_label('Подтвердите изменение исходного изображения.', 'Confirm updating the original image.'));
                        }
                        $selected = admin_image_library_transform($target, $input);
                    } elseif ($item === 'directory') {
                        $language = $GLOBALS['adminLanguage'] ?? 'ru';
                        if (admin_input($input, 'confirm') !== '1') throw new RuntimeException(site_media_folder_labels($language)[3]);
                        admin_remove_media_directory(ltrim($path, '/') . '/' . $name, $extensions, $language);
                    } elseif (!@unlink($target)) {
                        throw new RuntimeException('Не удалось удалить изображение.');
                    }
                }
            } finally {
                fclose($lock);
            }
        }
        admin_json(200, admin_image_library_page(admin_image_library_items($path, $base, $allFiles), $input, $path, $selected));
    } catch (RuntimeException $exception) {
        admin_json(422, ['message' => $exception->getMessage()]);
    }
}
