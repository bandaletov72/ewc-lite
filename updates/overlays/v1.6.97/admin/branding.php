<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}

function admin_brand_upload(array $file): string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return '';
    }
    $temp = $file['tmp_name'] ?? '';
    if (($file['error'] ?? null) !== UPLOAD_ERR_OK || !is_string($temp) || !is_uploaded_file($temp)) {
        throw new RuntimeException('Не удалось загрузить изображение. Проверьте лимит загрузки PHP.');
    }
    $bytes = @file_get_contents($temp, false, null, 0, 4194305);
    if ($bytes === false || strlen($bytes) > 4194304) {
        throw new RuntimeException('Изображение должно занимать не более 4 МБ.');
    }
    return $bytes;
}

function admin_brand_png(string $bytes, bool $icon): string
{
    $size = @getimagesizefromstring($bytes);
    $types = [IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF];
    if (defined('IMAGETYPE_WEBP')) {
        $types[] = IMAGETYPE_WEBP;
    }
    if ($size === false || !in_array($size[2], $types, true)
        || $size[0] < 1 || $size[1] < 1 || $size[0] > 4096 || $size[1] > 4096 || $size[0] * $size[1] > 8000000) {
        throw new RuntimeException('Выберите PNG, JPEG, GIF или WebP размером до 4096 пикселей и 8 Мп.');
    }
    $source = @imagecreatefromstring($bytes);
    if ($source === false) {
        throw new RuntimeException('PHP GD не смог прочитать это изображение.');
    }

    $edge = $icon ? 64 : 1200;
    $scale = $edge / max($size[0], $size[1]);
    if (!$icon) {
        $scale = min(1, $scale);
    }
    $width = max(1, (int) round($size[0] * $scale));
    $height = max(1, (int) round($size[1] * $scale));
    $canvas = imagecreatetruecolor($icon ? $edge : $width, $icon ? $edge : $height);
    if ($canvas === false) {
        throw new RuntimeException('Не удалось подготовить изображение.');
    }
    imagealphablending($canvas, false);
    imagesavealpha($canvas, true);
    imagefill($canvas, 0, 0, imagecolorallocatealpha($canvas, 0, 0, 0, 127));
    imagecopyresampled($canvas, $source, $icon ? (int) floor(($edge - $width) / 2) : 0,
        $icon ? (int) floor(($edge - $height) / 2) : 0, 0, 0, $width, $height, $size[0], $size[1]);
    ob_start();
    $saved = imagepng($canvas);
    $png = ob_get_clean();
    if (!$saved || !is_string($png) || $png === '') {
        throw new RuntimeException('Не удалось подготовить PNG.');
    }
    return $png;
}

function admin_image_name(string $filename): string
{
    $filename = str_replace('\\', '/', $filename);
    $filename = substr($filename, strrpos('/' . $filename, '/'));
    $dot = strrpos($filename, '.');
    $stem = $dot === false ? $filename : substr($filename, 0, $dot);
    if (class_exists('Normalizer')) {
        $normalized = Normalizer::normalize($stem, Normalizer::FORM_C);
        if (is_string($normalized)) $stem = $normalized;
    }
    $stem = strtr($stem, [
        'А'=>'A', 'Б'=>'B', 'В'=>'V', 'Г'=>'G', 'Д'=>'D', 'Е'=>'E', 'Ё'=>'Yo', 'Ж'=>'Zh', 'З'=>'Z',
        'И'=>'I', 'Й'=>'Y', 'К'=>'K', 'Л'=>'L', 'М'=>'M', 'Н'=>'N', 'О'=>'O', 'П'=>'P', 'Р'=>'R',
        'С'=>'S', 'Т'=>'T', 'У'=>'U', 'Ф'=>'F', 'Х'=>'Kh', 'Ц'=>'Ts', 'Ч'=>'Ch', 'Ш'=>'Sh', 'Щ'=>'Shch',
        'Ъ'=>'', 'Ы'=>'Y', 'Ь'=>'', 'Э'=>'E', 'Ю'=>'Yu', 'Я'=>'Ya',
        'а'=>'a', 'б'=>'b', 'в'=>'v', 'г'=>'g', 'д'=>'d', 'е'=>'e', 'ё'=>'yo', 'ж'=>'zh', 'з'=>'z',
        'и'=>'i', 'й'=>'y', 'к'=>'k', 'л'=>'l', 'м'=>'m', 'н'=>'n', 'о'=>'o', 'п'=>'p', 'р'=>'r',
        'с'=>'s', 'т'=>'t', 'у'=>'u', 'ф'=>'f', 'х'=>'kh', 'ц'=>'ts', 'ч'=>'ch', 'ш'=>'sh', 'щ'=>'shch',
        'ъ'=>'', 'ы'=>'y', 'ь'=>'', 'э'=>'e', 'ю'=>'yu', 'я'=>'ya',
        'І'=>'I', 'Ї'=>'Yi', 'Є'=>'Ye', 'Ґ'=>'G', 'і'=>'i', 'ї'=>'yi', 'є'=>'ye', 'ґ'=>'g',
        'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'AE', 'Ç'=>'C',
        'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N',
        'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Œ'=>'OE',
        'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y',
        'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'ae', 'ç'=>'c',
        'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ñ'=>'n',
        'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'œ'=>'oe',
        'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ü'=>'u', 'ý'=>'y', 'ÿ'=>'y', 'ß'=>'ss',
    ]);
    if (function_exists('iconv')) {
        $ascii = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $stem);
        if ($ascii !== false) {
            $stem = $ascii;
        }
    }
    return trim(substr(preg_replace('/[^A-Za-z0-9_-]+/', '-', $stem), 0, 64), '_-');
}

function admin_brand_write(string $kind, string $png, string $originalName = ''): string
{
    $theme = site_design_active();
    $directory = 'templates/' . ($theme === '' ? '' : 'themes/' . $theme . '/')
        . ($kind === 'favicon' ? 'icons/branding' : 'images/branding');
    $absolute = SITE_ROOT . '/' . $directory;
    if ((!is_dir($absolute) && !@mkdir($absolute, 0755, true))
        || realpath($absolute) !== str_replace('/', DIRECTORY_SEPARATOR, $absolute)) {
        throw new RuntimeException('Не удалось подготовить папку для изображений в templates.');
    }
    $name = admin_image_name($originalName);
    $path = $directory . '/' . $kind . '-' . ($name === '' ? '' : $name . '-') . hash('sha256', $png) . '.png';
    if (!admin_replace_file(SITE_ROOT . '/' . $path, $png)) {
        throw new RuntimeException('Не удалось записать изображение. Проверьте права на запись в templates.');
    }
    return $path;
}

function admin_brand_revision(array $brand): string
{
    return admin_settings_revision([site_design_active(), $brand]);
}

function admin_save_brand(array $files, bool $generate, string $revision)
{
    if (!function_exists('imagecreatefromstring') || !function_exists('imagepng')) {
        throw new RuntimeException('Для загрузки логотипа и создания favicon требуется расширение PHP GD.');
    }
    $logo = admin_brand_upload($files['logo'] ?? []);
    $dark = admin_brand_upload($files['logo_dark'] ?? []);
    $favicon = $generate ? '' : admin_brand_upload($files['favicon'] ?? []);
    if ($logo === '' && $dark === '' && $favicon === '' && !$generate) {
        throw new RuntimeException('Выберите логотип или favicon для загрузки.');
    }

    $lock = admin_lock();
    try {
        $file = site_brand_storage_file();
        $stored = site_read_record($file);
        $brand = site_brand_settings();
        if (!hash_equals(admin_brand_revision($brand), $revision)) {
            throw new RuntimeException('Изображения уже изменились. Откройте настройки заново.');
        }
        if ($generate && $logo === '' && $dark === '') {
            $savedLogo = $brand['logo'] ?: $brand['logo_dark'];
            if ($savedLogo === '') {
                throw new RuntimeException('Загрузите логотип, чтобы создать из него favicon.');
            }
            $favicon = site_brand_image_bytes($savedLogo);
            if ($favicon === false || $favicon === '') {
                throw new RuntimeException('Не удалось прочитать логотип.');
            }
        } elseif ($generate) {
            $favicon = $logo !== '' ? $logo : $dark;
        }
        $logoPng = $logo !== '' ? admin_brand_png($logo, false) : '';
        $darkPng = $dark !== '' ? admin_brand_png($dark, false) : '';
        $iconPng = $favicon !== '' ? admin_brand_png($favicon, true) : '';
        $updated = [];
        if ($logoPng !== '') {
            $updated['logo'] = admin_brand_write('logo', $logoPng, $files['logo']['name']);
        }
        if ($darkPng !== '') {
            $updated['logo_dark'] = admin_brand_write('logo-dark', $darkPng, $files['logo_dark']['name']);
        }
        if ($iconPng !== '') {
            $iconName = $generate ? ($logo !== '' ? $files['logo']['name'] : ($files['logo_dark']['name'] ?? '')) : $files['favicon']['name'];
            $updated['favicon'] = admin_brand_write('favicon', $iconPng, $iconName);
        }
        admin_write_record($file, array_replace($file === SITE_BRAND_FILE ? $brand : $stored, $updated));
    } finally {
        fclose($lock);
    }
}
