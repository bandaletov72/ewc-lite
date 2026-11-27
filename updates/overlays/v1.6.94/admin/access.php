<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}

const ADMIN_ACCESS_FILE = SITE_ROOT . '/admin/.access.php';

function admin_valid_username(string $name): bool
{
    return preg_match('/\A[a-zA-Z0-9][a-zA-Z0-9_.-]{0,63}\z/', $name) === 1;
}

function admin_auth_state(): array
{
    $state = site_read_record(ADMIN_ACCESS_FILE);
    if (array_key_exists('password', $state)) {
        if (!isset($state['user']) || !is_string($state['user']) || !admin_valid_username($state['user'])
            || !is_string($state['password']) || password_get_info($state['password'])['algoName'] === 'unknown') {
            throw new RuntimeException('Неверный формат файла учётной записи.');
        }
        return $state;
    }

    if ($state && (!isset($state['setup']) || !is_string($state['setup'])
            || preg_match('/\A[a-f0-9]{64}\z/', $state['setup']) !== 1)) {
        throw new RuntimeException('Неверный формат файла установки.');
    }

    $legacyHash = getenv('SITE_ADMIN_PASSWORD_HASH');
    if (is_string($legacyHash) && password_get_info($legacyHash)['algoName'] !== 'unknown') {
        return ['user' => 'admin', 'password' => $legacyHash];
    }
    return $state;
}

function admin_local_setup(): bool
{
    $remote = $_SERVER['REMOTE_ADDR'] ?? '';
    $host = $_SERVER['HTTP_HOST'] ?? '';
    return PHP_SAPI === 'cli-server'
        && preg_match('/\A(?:localhost|127\.0\.0\.1|\[::1\])(?::[0-9]+)?\z/i', $host) === 1
        && in_array($remote, ['127.0.0.1', '::1'], true)
        && !isset($_SERVER['HTTP_FORWARDED'])
        && !isset($_SERVER['HTTP_X_FORWARDED_FOR'])
        && !isset($_SERVER['HTTP_X_FORWARDED_HOST'])
        && !isset($_SERVER['HTTP_X_FORWARDED_PROTO'])
        && !isset($_SERVER['HTTP_X_REAL_IP']);
}

function admin_make_setup_code(): string
{
    $lock = admin_lock();
    try {
        $state = admin_auth_state();
        if (isset($state['password'])) {
            throw new RuntimeException('Учётная запись уже настроена.');
        }

        $code = bin2hex(random_bytes(16));
        admin_write_record(ADMIN_ACCESS_FILE, ['setup' => hash('sha256', $code)]);
        return $code;
    } finally {
        fclose($lock);
    }
}

function admin_password_hash(string $password, string $confirmation): string
{
    if (strlen($password) > 72 || strpos($password, "\0") !== false || preg_match('/\A.{8,}\z/us', $password) !== 1) {
        throw new RuntimeException('Пароль должен содержать от 8 символов и занимать не более 72 байт.');
    }
    if ($password !== $confirmation) {
        throw new RuntimeException('Пароли не совпадают.');
    }
    $hash = password_hash($password, PASSWORD_BCRYPT);
    if (!is_string($hash)) {
        throw new RuntimeException('Не удалось сохранить пароль.');
    }
    return $hash;
}

function admin_install(string $user, string $password, string $confirmation, string $code, bool $local, array $recovery = []): array
{
    if (!admin_valid_username($user)) {
        throw new RuntimeException('Укажите логин из латинских букв и цифр. Внутри логина допустимы точка, дефис и подчёркивание.');
    }
    $lock = admin_lock();
    try {
        $state = admin_auth_state();
        if (isset($state['password'])) {
            throw new RuntimeException('Учётная запись уже создана. Обновите страницу для входа.');
        }
        if (isset($state['setup']) || !$local) {
            if (!isset($state['setup']) || !hash_equals($state['setup'], hash('sha256', trim($code)))) {
                throw new RuntimeException('Неверный одноразовый код установки.');
            }
        }

        $state = ['user' => $user, 'password' => admin_password_hash($password, $confirmation), 'setupPending' => true, 'chatgptSetupPending' => true] + $recovery;
        admin_write_record(ADMIN_ACCESS_FILE, $state);
        return $state;
    } finally {
        fclose($lock);
    }
}

function admin_auth_key(array $state): string
{
    return hash('sha256', $state['user'] . ':' . $state['password']);
}

function admin_sign_in(array $state, string $adminUrl)
{
    session_regenerate_id(true);
    $_SESSION['auth'] = admin_auth_key($state);
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
    $_SESSION['public_scope'] = true;
    header('Location: ' . $adminUrl, true, 303);
    exit;
}
