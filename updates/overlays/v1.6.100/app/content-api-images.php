<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}

const SITE_CONTENT_IMAGE_LIMIT = 8 * 1024 * 1024;
const SITE_CONTENT_IMAGE_COUNT = 5;

function site_content_image_url_allowed(string $url): bool
{
    return strlen($url) <= 4096 && preg_match(
        '~\Ahttps://files\.oaiusercontent\.com/file-[A-Za-z0-9_-]+\?[^\x00-\x20\x7f#\\\\]+\z~',
        $url
    ) === 1;
}

function site_content_image_download(string $url): array
{
    $curl = curl_init($url);
    $bytes = '';
    $limit = SITE_CONTENT_IMAGE_LIMIT;
    curl_setopt_array($curl, [
        (defined('CURLOPT_PROTOCOLS_STR') ? CURLOPT_PROTOCOLS_STR : CURLOPT_PROTOCOLS) => defined('CURLOPT_PROTOCOLS_STR') ? 'HTTPS' : CURLPROTO_HTTPS,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_CONNECTTIMEOUT => 4,
        CURLOPT_TIMEOUT => 7,
        CURLOPT_WRITEFUNCTION => static function ($curl, string $chunk) use (&$bytes, $limit): int {
            $size = strlen($chunk);
            if (strlen($bytes) + $size > $limit) return 0;
            $bytes .= $chunk;
            return $size;
        },
    ]);
    try {
        if (curl_exec($curl) !== true || curl_getinfo($curl, CURLINFO_RESPONSE_CODE) !== 200) {
            throw new RuntimeException('image_download_failed');
        }
    } finally {
        curl_close($curl);
    }
    return site_content_image_data($bytes);
}

function site_content_image_data(string $bytes): array
{
    $image = @getimagesizefromstring($bytes);
    $formats = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp'];
    if (!$image || !isset($formats[$image[2]]) || $image[0] < 1 || $image[1] < 1 || $image[0] > 8192 || $image[1] > 8192) {
        throw new RuntimeException('unsupported_image');
    }
    return ['bytes' => $bytes, 'extension' => $formats[$image[2]], 'width' => $image[0], 'height' => $image[1]];
}

function site_content_uploaded_images(array $files): array
{
    $errors = $files['error'] ?? null;
    if (!is_array($errors) || !$errors || count($errors) > SITE_CONTENT_IMAGE_COUNT) {
        throw new RuntimeException('invalid_image_import', 422);
    }
    $remaining = SITE_CONTENT_IMAGE_LIMIT;
    $images = [];
    foreach ($errors as $index => $error) {
        if ($error === UPLOAD_ERR_INI_SIZE || $error === UPLOAD_ERR_FORM_SIZE) {
            throw new RuntimeException('image_upload_too_large', 413);
        }
        $file = $files['tmp_name'][$index] ?? null;
        if ($error !== UPLOAD_ERR_OK || !is_string($file) || !is_uploaded_file($file)) {
            throw new RuntimeException('image_upload_failed', 422);
        }
        $size = filesize($file);
        if ($size === false || $size === 0) throw new RuntimeException('image_upload_failed', 422);
        if ($size > $remaining) throw new RuntimeException('image_upload_too_large', 413);
        $bytes = file_get_contents($file, false, null, 0, $size + 1);
        if ($bytes === false || strlen($bytes) !== $size) throw new RuntimeException('image_upload_failed', 422);
        $images[] = site_content_image_data($bytes);
        $remaining -= $size;
    }
    return $images;
}

function site_content_image_import(string $base, string $tokenHash, string $access)
{
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
        header('Allow: POST');
        site_content_api_error(405, 'method_not_allowed');
        return;
    }
    if ($access !== 'write') {
        site_content_api_error(403, 'write_access_required');
        return;
    }
    $contentType = strtolower(trim(explode(';', $_SERVER['CONTENT_TYPE'] ?? '', 2)[0]));
    $multipart = $contentType === 'multipart/form-data';
    if ($multipart) {
        if ((int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > SITE_CONTENT_IMAGE_LIMIT + 65536) {
            site_content_api_error(413, 'image_upload_too_large');
            return;
        }
        $path = $_POST['path'] ?? null;
    } else {
        $body = site_content_api_json(32768);
        if ($body === null) return;
        $path = $body->path ?? null;
    }
    if (!is_string($path) || !site_content_api_page_path($path)) {
        site_content_api_error(422, 'invalid_image_import');
        return;
    }
    $initialRoot = site_content_root();
    if (site_read_page($path, $initialRoot) === null) {
        site_content_api_error(404, 'page_not_found');
        return;
    }
    try {
        $images = $multipart
            ? site_content_uploaded_images($_FILES['images'] ?? [])
            : site_content_remote_images($body->openaiFileIdRefs ?? null);
    } catch (RuntimeException $exception) {
        $status = $exception->getCode();
        site_content_api_error(in_array($status, [413, 503], true) ? $status : 422, $exception->getMessage());
        return;
    }
    require_once SITE_ROOT . '/admin/files.php';
    require_once SITE_ROOT . '/admin/image-library.php';
    $lock = admin_lock();
    try {
        if (!site_content_api_write_access($tokenHash, 'gpt')) return;
        if ($initialRoot !== site_content_root() || site_read_page($path, $initialRoot) === null) {
            site_content_api_error(412, 'page_has_changed');
            return;
        }
        $root = admin_image_library_roots()[0];
        $parent = SITE_ROOT . $root;
        if ((!is_dir($parent) && !@mkdir($parent, 0755)) || !admin_image_library_has_directory($root)) {
            throw new RuntimeException('Could not open the image library.');
        }
        $relative = $root . '/ai-' . bin2hex(random_bytes(10));
        $directory = SITE_ROOT . $relative;
        if (!@mkdir($directory, 0755)) throw new RuntimeException('Could not create an image folder.');
        $result = [];
        $destinations = [];
        try {
            foreach ($images as $i => $image) {
                $name = sprintf('%02d.%s', $i + 1, $image['extension']);
                $file = $directory . '/' . $name;
                $destinations[] = $file;
                if (!admin_replace_file($file, $image['bytes'])) throw new RuntimeException('Could not save image.');
                $result[] = ['src' => $relative . '/' . $name, 'url' => site_url($base, ltrim($relative . '/' . $name, '/')),
                    'width' => $image['width'], 'height' => $image['height']];
            }
        } catch (RuntimeException $exception) {
            foreach ($destinations as $file) if (is_file($file)) @unlink($file);
            @rmdir($directory);
            throw $exception;
        }
        site_content_api_response(200, ['path' => $path, 'folder' => $relative, 'images' => $result]);
    } finally {
        fclose($lock);
    }
}

function site_content_remote_images($files): array
{
    if (!is_array($files) || !$files || count($files) > SITE_CONTENT_IMAGE_COUNT) {
        throw new RuntimeException('invalid_image_import');
    }
    $links = [];
    foreach ($files as $file) {
        $url = is_object($file) ? ($file->download_link ?? null) : null;
        if (!is_string($url) || !site_content_image_url_allowed($url)) {
            throw new RuntimeException('unsupported_image_origin');
        }
        $links[] = $url;
    }
    if (!function_exists('curl_init')) throw new RuntimeException('image_import_requires_curl', 503);
    return array_map('site_content_image_download', $links);
}
