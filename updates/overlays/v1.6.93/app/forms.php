<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}

const SITE_FORMS_FILE = SITE_ROOT . '/admin/.forms.php';
const SITE_FORMS_RECORD_LIMIT = 1048576;
const SITE_INBOX = SITE_ROOT . '/admin/inbox';
const SITE_FORM_REQUEST_LIMIT = 131072;

function site_form_types(): array
{
    return ['text' => 'Текстовое поле', 'textarea' => 'Многострочный текст',
        'radio' => 'Переключатели (radio)', 'select' => 'Выпадающий список',
        'tel' => 'Телефон', 'email' => 'Email'];
}

function site_form_length_limits(): array
{
    return ['text' => [375, 1500], 'textarea' => [4000, 12000], 'radio' => [1500, 1500],
        'select' => [1500, 1500], 'tel' => [33, 33], 'email' => [254, 254]];
}

function site_form_field_length(array $field): int
{
    list($default, $maximum) = site_form_length_limits()[$field['type']] ?? [375, 1500];
    $length = $field['maxLength'] ?? null;
    return is_int($length) && $length >= 1 && $length <= $maximum ? $length : $default;
}

function site_form_text_length(string $text): int
{
    // maxlength counts UTF-16 units. Supplementary characters occupy two units.
    return preg_match_all('/./us', $text) + preg_match_all('/[\x{10000}-\x{10FFFF}]/u', $text);
}

function site_valid_form_id(string $id): bool
{
    return preg_match('/\Aform[1-9][0-9]{0,8}\z/', $id) === 1;
}

function site_default_forms(): array
{
    return [
        'form1' => [
            'button' => 'Отправить',
            'recipient' => '',
            'fields' => [
                ['name' => 'name', 'label' => 'Имя', 'type' => 'text', 'required' => true, 'options' => []],
                ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true, 'options' => []],
                ['name' => 'message', 'label' => 'Сообщение', 'type' => 'textarea', 'required' => true, 'options' => []],
            ],
        ],
        'form87' => [
            'title' => 'Оставить отзыв',
            'button' => 'Отправить отзыв',
            'recipient' => '',
            'fields' => [
                ['name' => 'name', 'label' => 'Имя для публикации', 'type' => 'text', 'required' => true, 'maxLength' => 90, 'options' => []],
                ['name' => 'rating', 'label' => 'Оценка', 'type' => 'select', 'required' => true, 'maxLength' => null, 'options' => ['5', '4', '3', '2', '1']],
                ['name' => 'message', 'label' => 'Расскажите о вашем опыте', 'type' => 'textarea', 'required' => true, 'maxLength' => 1500, 'options' => []],
                ['name' => 'permission', 'label' => 'Разрешаете опубликовать имя и текст отзыва?', 'type' => 'radio', 'required' => true, 'maxLength' => null, 'options' => ['Да', 'Нет']],
            ],
        ],
    ];
}

function site_forms(): array
{
    $defaultsFile = site_design_file(site_design_active(), 'form-defaults.php');
    $themeDefaults = $defaultsFile === '' ? [] : site_read_record($defaultsFile, SITE_FORMS_RECORD_LIMIT);
    return array_replace(site_default_forms(), $themeDefaults,
        site_read_record(site_content_record(SITE_FORMS_FILE), SITE_FORMS_RECORD_LIMIT));
}

function site_public_session_name(): string
{
    return site_content_root() === SITE_ROOT ? 'FILE_SITE_PUBLIC' : 'FILE_SITE_PUBLIC_' . substr(hash('sha256', site_design_active()), 0, 16);
}

function site_public_session(string $base): bool
{
    $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    $name = site_public_session_name();
    $previousName = session_name();
    session_name($name);
    if (session_id() !== '' && $previousName !== $name) {
        $cookie = $_COOKIE[$name] ?? '';
        session_id(is_string($cookie) && preg_match('/\A[A-Za-z0-9,-]{16,128}\z/', $cookie)
            ? $cookie : bin2hex(random_bytes(24)));
    }
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_set_cookie_params(0, site_url($base), '', $secure, true);
    session_cache_limiter('');
    return @session_start();
}

function site_public_context(string $base, bool $fragment): array
{
    header('Cache-Control: private, no-store');
    $presented = $_SERVER['HTTP_X_SITE_FRAGMENT'] ?? '';
    $scheme = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
    $requestOrigin = $scheme . '://' . strtolower($_SERVER['HTTP_HOST'] ?? '');
    $scope = $requestOrigin . site_url($base);
    if ($fragment) {
        $fetchSite = $_SERVER['HTTP_SEC_FETCH_SITE'] ?? '';
        $origin = $_SERVER['HTTP_ORIGIN'] ?? '';
        if (!in_array($_SERVER['REQUEST_METHOD'] ?? '', ['GET', 'HEAD'], true)
            || ($fetchSite !== '' && $fetchSite !== 'same-origin')
            || ($origin !== '' && strtolower($origin) !== $requestOrigin)
            || !isset($_COOKIE[site_public_session_name()])
            || preg_match('/\A[0-9a-f]{64}\z/', $presented) !== 1) {
            return [];
        }
    }

    if (!site_public_session($base)) {
        return [];
    }
    if ($fragment && (($_SESSION['fragmentScope'] ?? '') !== $scope
            || !hash_equals($_SESSION['fragment'] ?? '', $presented))) {
        session_write_close();
        return [];
    }

    if (!isset($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    if (($_SESSION['fragmentScope'] ?? '') !== $scope || !isset($_SESSION['fragment'])) {
        $_SESSION['fragment'] = bin2hex(random_bytes(32));
        $_SESSION['fragmentScope'] = $scope;
    }
    $context = ['token' => $_SESSION['csrf'], 'fragment' => $_SESSION['fragment']];
    session_write_close();
    return $context;
}

function site_form_signature(string $token, string $id, string $page): string
{
    return hash_hmac('sha256', $id . "\0" . $page, $token);
}

function site_valid_phone(string $phone): bool
{
    return preg_match('/\A\+?[0-9 ().-]{7,32}\z/', $phone) === 1
        && preg_match('/\A[0-9]{7,15}\z/', preg_replace('/[^0-9]/', '', $phone)) === 1;
}

function site_phone_countries(): array
{
    return [
        'RU' => ['Россия', '7', '78'], 'KZ' => ['Қазақстан', '7', '78'],
        'BY' => ['Беларусь', '375', '8'], 'UA' => ['Україна', '380', '0'],
        'US' => ['United States', '1', '1'], 'CA' => ['Canada', '1', '1'],
        'GB' => ['United Kingdom', '44', '0'], 'DE' => ['Deutschland', '49', '0'],
        'FR' => ['France', '33', '0'], 'ES' => ['España', '34', ''],
        'PT' => ['Portugal', '351', ''], 'IT' => ['Italia', '39', ''],
        'CH' => ['Schweiz', '41', '0'], 'PL' => ['Polska', '48', ''],
        'JP' => ['日本', '81', '0'], 'VN' => ['Việt Nam', '84', '0'],
        'KR' => ['대한민국', '82', '0'], 'CN' => ['中国', '86', '0'],
        'BR' => ['Brasil', '55', ''], 'IN' => ['भारत', '91', '0'],
        'AU' => ['Australia', '61', '0'], 'INT' => ['International', '', ''],
    ];
}

function site_phone_country_labels(string $language): array
{
    static $labels;
    if ($labels === null) $labels = require __DIR__ . '/help-phone-country.php';
    return $labels[$language] ?? $labels['ru'];
}

function site_phone_profile(): array
{
    $country = site_phone_countries()[site_region_settings()['country']];
    return ['code' => $country[1], 'prefixes' => $country[2]];
}

function site_normalize_form_phone(string $phone, string $country = 'RU'): string
{
    if (!site_valid_phone($phone)) {
        return '';
    }

    $digits = preg_replace('/[^0-9]/', '', $phone);
    if ($phone[0] !== '+') {
        $settings = site_phone_countries()[$country] ?? site_phone_countries()['INT'];
        list(, $code, $prefixes) = $settings;
        if ($code === '') return '';
        if (strpos($prefixes, $digits[0]) !== false) $digits = substr($digits, 1);
        $digits = $code . $digits;
    }
    if (!preg_match('/\A[1-9][0-9]{6,14}\z/', $digits)) return '';
    if ($digits[0] !== '7') {
        return '+' . $digits;
    }
    if (strlen($digits) !== 11) {
        return '';
    }

    return '+7(' . substr($digits, 1, 3) . ')' . substr($digits, 4, 3)
        . '-' . substr($digits, 7, 2) . '-' . substr($digits, 9, 2);
}

function site_validate_message(array $form, array $submitted): array
{
    $fields = [];
    $errors = [];
    $replyTo = '';
    foreach ($form['fields'] as $field) {
        $name = $field['name'];
        $input = $submitted[$name] ?? '';
        $value = '';
        $type = $field['type'];
        $limit = site_form_field_length($field);
        $lengthError = 'Максимальная длина поля — ' . $limit . '.';
        if (!is_string($input)) {
            $errors[$name] = 'Проверьте формат значения.';
        } elseif (strlen($input) > $limit * 3) {
            $errors[$name] = $lengthError;
        } elseif (preg_match('//u', $input) !== 1 || preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', $input)
            || ($type !== 'textarea' && preg_match('/[\r\n]/', $input))) {
            $errors[$name] = 'Проверьте формат значения.';
        } else {
            $input = $type === 'textarea' ? str_replace(["\r\n", "\r"], "\n", $input) : $input;
            if (site_form_text_length($input) > $limit) {
                $errors[$name] = $lengthError;
            } else {
                $value = trim($input);
            }
        }
        if (isset($errors[$name])) {
            continue;
        }
        if ($value === '') {
            if ($field['required']) {
                $errors[$name] = 'Заполните это поле.';
            }
        } elseif (in_array($type, ['text', 'textarea'], true)
            && preg_match('~<\s*(?:/?[a-z][a-z0-9:-]*(?=[\s/>])|[!?])[^>]*>~i',
                html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8'))) {
            $errors[$name] = 'Введите обычный текст без HTML-разметки.';
        } elseif ($type === 'email' && (strlen($value) > 254 || !filter_var($value, FILTER_VALIDATE_EMAIL))) {
            $errors[$name] = 'Укажите действительный email.';
        } elseif ($type === 'tel') {
            $normalized = site_normalize_form_phone($value, site_region_settings()['country']);
            if ($normalized === '') {
                $errors[$name] = 'Укажите полный номер телефона, включая код страны.';
            } elseif (strlen($normalized) > $limit) {
                $errors[$name] = $lengthError;
            } else {
                $value = $normalized;
            }
        } elseif (in_array($type, ['radio', 'select'], true) && !in_array($value, $field['options'], true)) {
            $errors[$name] = 'Выберите один из предложенных вариантов.';
        }

        if ($type === 'email' && $replyTo === '' && $value !== '' && !isset($errors[$name])) {
            $replyTo = $value;
        }
        $fields[$name] = ['label' => $field['label'], 'value' => $value, 'type' => $type];
    }
    return ['fields' => $fields, 'errors' => $errors, 'replyTo' => $replyTo];
}

function site_save_message(array $message)
{
    $id = gmdate('YmdHis') . '-' . bin2hex(random_bytes(8));
    admin_write_record(SITE_INBOX . '/' . $id . '.php', $message);
}

