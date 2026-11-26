<?php
if (!defined('SITE_ROOT')) { http_response_code(404); exit; }

const SITE_ACCOUNTS_FILE = SITE_ROOT . '/data/.accounts.php';
const SITE_ACCOUNTS_LIMIT = 16777216;
const SITE_ACCOUNT_ROLES = ['buyer', 'catalog', 'orders', 'editor', 'reviews'];

function site_account_labels(): array
{
    $ru = ($GLOBALS['adminLanguage'] ?? site_region_settings()['public']) === 'ru';
    return $ru ? [
        'users'=>'Пользователи','account'=>'Личный кабинет','register'=>'Регистрация','login'=>'Войти','logout'=>'Выйти','name'=>'Имя',
        'forgot'=>'Забыли пароль?','reset'=>'Сброс пароля','old_password'=>'Текущий пароль','change_password'=>'Заменить пароль',
        'reset_notice'=>'Если аккаунт найден, письмо со ссылкой отправлено.','closed'=>'Регистрация закрыта.',
        'password'=>'Пароль','email'=>'Электронная почта','phone'=>'Телефон','address'=>'Адреса доставки (по одному на строку)','save'=>'Сохранить',
        'search'=>'Найти пользователя','role'=>'Роль','active'=>'Активен','blocked'=>'Заблокирован','all'=>'Все','profile'=>'Профиль',
        'buyer'=>'Покупатель','catalog'=>'Редактор каталога','orders'=>'Менеджер заказов','editor'=>'Правка текстов','reviews'=>'Модератор отзывов',
        'error'=>'Проверьте поля. Пароль должен содержать от 10 до 72 байт.','bad_login'=>'Не удалось войти. Проверьте данные или попробуйте позже.',
        'editable'=>'Дополнительный текст страницы','saved'=>'Данные сохранены.','new'=>'Добавить пользователя','pages'=>'Страницы','work'=>'Рабочая область','review_queue'=>'Отзывы на модерации',
        'approve'=>'Одобрить','revision'=>'Страница уже изменена. Откройте её заново.','keep_password'=>'Пустое поле сохраняет текущий пароль',
    ] : [
        'users'=>'Users','account'=>'My account','register'=>'Register','login'=>'Log in','logout'=>'Log out','name'=>'Name',
        'forgot'=>'Forgot your password?','reset'=>'Reset password','old_password'=>'Current password','change_password'=>'Change password',
        'reset_notice'=>'If the account exists, a reset link has been sent.','closed'=>'Registration is closed.',
        'password'=>'Password','email'=>'Email','phone'=>'Phone','address'=>'Delivery addresses, one per line','save'=>'Save',
        'search'=>'Find users','role'=>'Role','active'=>'Active','blocked'=>'Blocked','all'=>'All','profile'=>'Profile',
        'buyer'=>'Customer','catalog'=>'Catalog editor','orders'=>'Order manager','editor'=>'Edit page text','reviews'=>'Review moderator',
        'error'=>'Check the fields. Use a password of 10 to 72 bytes.','bad_login'=>'Sign-in failed. Check your details or try again later.',
        'editable'=>'Additional page text','saved'=>'Saved.','new'=>'Add user','pages'=>'Pages','work'=>'Workspace','review_queue'=>'Reviews awaiting moderation',
        'approve'=>'Approve','revision'=>'The page was changed. Reload it.','keep_password'=>'Leave empty to keep the current password',
    ];
}

function site_accounts(): array
{
    return site_read_record(SITE_ACCOUNTS_FILE, SITE_ACCOUNTS_LIMIT);
}

function site_account_input(array $input, string $key): string
{
    return is_string($input[$key] ?? null) ? trim($input[$key]) : '';
}

function site_account_current(): ?array
{
    $id = $_SESSION['accountId'] ?? '';
    if ($id === '') return null;
    $user = site_accounts()[$id] ?? null;
    return $user && $user['enabled'] && hash_equals(hash('sha256', $user['password']), $_SESSION['accountKey'] ?? '') ? $user : null;
}

function site_account_form(string $base, string $action, string $token): string
{
    return '<form method="post" class="form-stack" action="' . site_escape(site_url($base, 'app/users-route.php')) . '">'
        . '<input type="hidden" name="action" value="' . site_escape($action) . '"><input type="hidden" name="csrf" value="'
        . site_escape($token) . '">';
}

function site_account_token(): string
{
    if (!isset($_SESSION['accountCsrf'])) $_SESSION['accountCsrf'] = bin2hex(random_bytes(32));
    return $_SESSION['accountCsrf'];
}

function site_account_password(string $password): bool
{
    return strlen($password) >= 10 && strlen($password) <= 72 && preg_match('/[\x00-\x1f]/', $password) !== 1;
}

function site_account_save(array $input, string $id, bool $admin): array
{
    $labels = site_account_labels();
    $lock = admin_lock();
    try {
        $users = site_accounts();
        $existing = $users[$id] ?? [];
        if ($id !== '' && !$existing) throw new RuntimeException($labels['error']);
        $email = strtolower(site_account_input($input, 'email') ?: ($existing['email'] ?? ''));
        $name = site_account_input($input, 'name');
        $phone = site_account_input($input, 'phone');
        $addresses = site_account_input($input, 'addresses');
        $password = is_string($input['password'] ?? null) ? $input['password'] : '';
        if ($name === '' || strlen($name) > 160 || strlen($phone) > 100 || strlen($addresses) > 2000
            || strlen($email) > 200 || !filter_var($email, FILTER_VALIDATE_EMAIL)
            || (($password !== '' || !$existing) && !site_account_password($password))) throw new RuntimeException($labels['error']);
        foreach ($users as $user) {
            if ($user['id'] !== $id && $user['email'] === $email) throw new RuntimeException($labels['error']);
        }
        $roles = $existing['roles'] ?? 'buyer';
        if ($admin) {
            $roles = implode(',', array_intersect(SITE_ACCOUNT_ROLES, is_array($input['roles'] ?? null)
                ? array_filter($input['roles'], 'is_string') : []));
        }
        $id = $id ?: bin2hex(random_bytes(12));
        $record = ['id'=>$id,'name'=>$name,'email'=>$email,'phone'=>$phone,'addresses'=>$addresses,'roles'=>$roles,
            'enabled'=>$admin ? site_account_input($input, 'enabled') === '1' : ($existing['enabled'] ?? true),
            'password'=>$password !== '' ? password_hash($password, PASSWORD_DEFAULT) : $existing['password']];
        $users[$id] = $record;
        admin_write_record(SITE_ACCOUNTS_FILE, $users, SITE_ACCOUNTS_LIMIT);
        return $record;
    } finally {
        fclose($lock);
    }
}

function site_account_markup(string $base, bool $register = false, string $draft = ''): string
{
    $labels = site_account_labels();
    $active = session_status() === PHP_SESSION_ACTIVE;
    if (!$active && !site_public_session($base)) return '';
    $token = site_account_token();
    $user = site_account_current();
    $html = '<section class="form-stack">';
    if ($user) {
        $html .= '<h2>' . site_escape($labels['profile']) . '</h2>' . site_account_form($base, 'profile', $token);
        foreach (['name', 'phone'] as $key) {
            $html .= '<label>' . site_escape($labels[$key]) . ' <input name="' . $key . '" value="' . site_escape($user[$key]) . '"></label>';
        }
        if (site_shop_enabled()) {
            $html .= '<label>' . site_escape($labels['address']) . '<textarea name="addresses">' . site_escape($user['addresses']) . '</textarea></label>';
        }
        $html .= '<button>' . site_escape($labels['save']) . '</button></form>'
            . site_account_form($base,'logout',$token) . '<button>' . site_escape($labels['logout']) . '</button></form>'
            . site_account_form($base,'password',$token)
            . '<label>' . site_escape($labels['old_password']) . ' <input name="old" type="password" autocomplete="current-password" required></label>'
            . '<label>' . site_escape($labels['password']) . ' <input name="password" type="password" autocomplete="new-password" required minlength="10" maxlength="72"></label>'
            . '<button>' . site_escape($labels['change_password']) . '</button></form>';
        if (site_shop_enabled()) {
            require_once SITE_ROOT . '/shop/module.php';
            $html .= shop_account_html($base, ['user'=>$user], shop_settings(), false);
        }
        $roles = explode(',', $user['roles']);
        if (array_intersect(['editor','reviews'], $roles)) {
            $html .= '<h2>' . site_escape($labels['work']) . '</h2>';
            if (in_array('editor', $roles, true)) {
                $html .= '<form method="get" action="' . site_escape(site_url($base,'app/users-route.php')) . '"><label>'
                    . site_escape($labels['pages']) . '<select name="draft">';
                foreach (site_pages(true) as $page) {
                    if ($page['path'] === 'error/404/index.php') continue;
                    $html .= '<option value="' . site_escape($page['path']) . '">' . site_escape($page['path']) . '</option>';
                }
                $html .= '</select></label><button>' . site_escape($labels['work']) . '</button></form>';
                $page = $draft === '' ? null : site_read_page($draft);
                if ($page && !empty($page['path'])) {
                    preg_match('~<!-- user-editor -->(.*?)<!-- /user-editor -->\s*\z~s', $page['html'], $matched);
                    $html .= site_account_form($base, 'edit', $token) . '<input type="hidden" name="path" value="' . site_escape($page['path'])
                        . '"><input type="hidden" name="revision" value="' . site_escape($page['revision']) . '"><label>' . site_escape($labels['editable']) . '<textarea name="text" rows="6">'
                        . site_escape(trim(html_entity_decode(strip_tags($matched[1] ?? ''), ENT_QUOTES, 'UTF-8')))
                        . '</textarea></label><button>' . site_escape($labels['save']) . '</button></form>';
                }
            }
            if (in_array('reviews', $roles, true)) {
                $html .= '<h3>' . site_escape($labels['review_queue']) . '</h3>' . site_account_review_queue($base, $token, $labels['approve']);
            }
        }
    } elseif ($register && !site_account_registration()) {
        $html .= '<p>' . site_escape($labels['closed']) . '</p>';
    } else {
        $html .= '<h2>' . site_escape($labels[$register ? 'register' : 'login']) . '</h2>'
            . site_account_form($base, $register ? 'register' : 'login', $token);
        if ($register) $html .= '<label>' . site_escape($labels['name']) . ' <input name="name" required maxlength="160" autocomplete="name"></label>';
        $html .= '<label>' . site_escape($labels['email']) . ' <input name="email" required type="email" autocomplete="username"></label>'
            . '<label>' . site_escape($labels['password']) . ' <input type="password" name="password" autocomplete="'
            . ($register ? 'new-password' : 'current-password') . '" required maxlength="72"></label>'
            . '<button>' . site_escape($labels[$register ? 'register' : 'login']) . '</button></form>'
            . '<a href="' . site_escape(site_url($base,'app/users-route.php') . '?forgot=1') . '">' . site_escape($labels['forgot']) . '</a>';
        if ($register || site_account_registration()) {
            $html .= '<a href="' . site_escape(site_url($base,'app/users-route.php') . ($register ? '' : '?register=1')) . '">'
                . site_escape($labels[$register ? 'login' : 'register']) . '</a>';
        }
    }
    if (!$active) session_write_close();
    return $html . '</section>';
}

function site_account_review_queue(string $base, string $token, string $button): string
{
    [$rows] = site_review_page('', 60, false);
    $html = '';
    foreach ($rows as $id => $row) {
        if ($row['status'] !== 'pending') continue;
        $html .= '<article class="panel"><p>' . site_escape($row['name'] . ' · ' . $row['review']) . '</p>'
            . site_account_form($base, 'approve', $token)
            . '<input type="hidden" name="review_id" value="' . site_escape($id) . '"><input type="hidden" name="revision" value="'
            . hash('sha256', serialize($row)) . '"><button>' . site_escape($button) . '</button></form></article>';
    }
    return $html;
}

function site_account_edit(string $path, string $revision, string $text): void
{
    $labels = site_account_labels();
    $lock = admin_lock();
    try {
        $page = $path === 'error/404/index.php' ? null : site_read_page($path);
        if (!$page || !hash_equals($page['revision'], $revision)) throw new RuntimeException($labels['revision']);
        if (strlen($text) > 10000 || preg_match('//u', $text) !== 1) throw new RuntimeException($labels['error']);
        $file = site_content_root() . '/' . $path;
        $source = file_get_contents($file);
        $markup = preg_replace('~\n?<!-- user-editor -->.*?<!-- /user-editor -->\s*\z~s', '', $source);
        if ($text !== '') {
            // Delegated editors can update a dedicated text block without injecting markup or shortcodes.
            $safe = str_replace(['[', ']'], ['&#91;', '&#93;'], site_escape($text));
            $markup .= "\n<!-- user-editor --><p>" . nl2br($safe, false) . "</p><!-- /user-editor -->\n";
        }
        if (strlen($markup) > SITE_PAGE_RECORD_LIMIT || !admin_replace_file($file, $markup)) throw new RuntimeException($labels['error']);
    } finally {
        fclose($lock);
    }
}

function site_account_reset_mail(string $email): void
{
    $public = site_settings()['publicUrl'];
    if ($public === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) return;
    $lock = admin_lock();
    try {
        $user = null;
        foreach (site_accounts() as $candidate) if ($candidate['enabled'] && $candidate['email'] === $email) { $user = $candidate; break; }
        if (!$user) return;
        $token = bin2hex(random_bytes(32));
        $resets = site_read_record(SITE_ROOT . '/data/.account-resets.php', SITE_ACCOUNTS_LIMIT);
        $now = time();
        $resets = array_filter($resets, static function (array $reset) use ($now): bool { return $reset['expires'] > $now; });
        if (($resets[$user['id']]['expires'] ?? 0) > $now + 1740) return;
        $resets[$user['id']] = ['digest'=>hash('sha256', $token),'expires'=>$now + 1800,'revision'=>hash('sha256', $user['password'])];
        admin_write_record(SITE_ROOT . '/data/.account-resets.php', $resets, SITE_ACCOUNTS_LIMIT);
    } finally {
        fclose($lock);
    }
    $template = site_account_settings()['mail']['reset'] ?? site_account_mail_defaults()['reset'];
    $url = rtrim($public, '/') . '/app/users-route.php?reset=' . $user['id'] . '.' . $token;
    if (!site_send_template($user['email'], $template, ['name'=>$user['name'], 'url'=>$url])) error_log('EWC user password reset email could not be sent.');
}

function site_account_change_password(string $password, string $old, ?array $user, string $reset): void
{
    $labels = site_account_labels();
    if (!site_account_password($password)) throw new RuntimeException($labels['error']);
    $lock = admin_lock();
    try {
        $users = site_accounts();
        if ($reset !== '') {
            if (!preg_match('/\A([a-f0-9]{24})\.([a-f0-9]{64})\z/', $reset, $parts)) throw new RuntimeException($labels['error']);
            $id = $parts[1];
            $resets = site_read_record(SITE_ROOT . '/data/.account-resets.php', SITE_ACCOUNTS_LIMIT);
            $entry = $resets[$id] ?? null;
            if (!$entry || $entry['expires'] < time() || !hash_equals($entry['digest'], hash('sha256', $parts[2]))
                || !hash_equals($entry['revision'] ?? '', hash('sha256', $users[$id]['password'] ?? '')) || empty($users[$id]['enabled'])) throw new RuntimeException($labels['error']);
        } else {
            if (!$user || !site_account_password($old) || !password_verify($old, $user['password'])
                || ($users[$user['id']]['password'] ?? '') !== $user['password']) throw new RuntimeException($labels['error']);
            $id = $user['id'];
        }
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $users[$id]['password'] = $hash;
        admin_write_record(SITE_ACCOUNTS_FILE, $users, SITE_ACCOUNTS_LIMIT);
        if ($reset === '') $_SESSION['accountKey'] = hash('sha256', $hash);
    } finally {
        fclose($lock);
    }
}
