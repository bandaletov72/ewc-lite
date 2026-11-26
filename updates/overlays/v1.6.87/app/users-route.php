<?php
define('SITE_ROOT', dirname(__DIR__));
require __DIR__ . '/site.php';
require __DIR__ . '/users.php';
require SITE_ROOT . '/admin/files.php';

$base = site_base('app/users-route.php');
$url = site_url($base, 'app/users-route.php');
if (!site_public_session($base)) { http_response_code(503); exit; }
$labels = site_account_labels();
header('Content-Type: text/html; charset=UTF-8');
header('Cache-Control: private, no-store');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: no-referrer');
header("Content-Security-Policy: default-src 'none'; style-src 'self'; form-action 'self'; base-uri 'none'; frame-ancestors 'none'");

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $action = site_account_input($_POST, 'action');
    $registered = false;
    $return = '';
    if ((int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 65536) { http_response_code(413); exit; }
    try {
        if (!hash_equals(site_account_token(), site_account_input($_POST, 'csrf'))) throw new RuntimeException($labels['error']);
        $user = site_account_current();
        if ($action === 'logout') {
            unset($_SESSION['accountId'], $_SESSION['accountKey'], $_SESSION['shopCart'], $_SESSION['shopCheckout'], $_SESSION['shopLastOrder']);
            session_regenerate_id(true);
        } elseif ($action === 'profile' && $user) {
            $profile = array_intersect_key($_POST, array_flip(['name','phone','addresses']));
            if (!site_shop_enabled()) $profile['addresses'] = $user['addresses'];
            site_account_save($profile, $user['id'], false);
            $_SESSION['accountNotice'] = $labels['saved'];
        } elseif ($action === 'password' && $user) {
            site_account_change_password(site_account_input($_POST, 'password'), site_account_input($_POST, 'old'), $user, '');
            $_SESSION['accountNotice'] = $labels['saved'];
        } elseif ($action === 'reset' && !$user) {
            site_account_change_password(site_account_input($_POST, 'password'), '', null, site_account_input($_POST, 'reset'));
            $_SESSION['accountNotice'] = $labels['saved'];
        } elseif ($action === 'forgot' && !$user) {
            site_account_reset_mail(strtolower(site_account_input($_POST,'email')));
            $_SESSION['accountNotice'] = $labels['reset_notice'];
            $return = '?forgot=1';
        } elseif ($action === 'edit' && $user && in_array('editor', explode(',', $user['roles']), true)) {
            site_account_edit(site_account_input($_POST, 'path'), site_account_input($_POST, 'revision'), site_account_input($_POST, 'text'));
            $_SESSION['accountNotice'] = $labels['saved'];
        } elseif ($action === 'approve' && $user && in_array('reviews', explode(',', $user['roles']), true)) {
            require SITE_ROOT . '/admin/reviews.php';
            admin_review_action('review-approve', $_POST, site_region_settings()['public']);
            $_SESSION['accountNotice'] = $labels['saved'];
        } elseif (!$user && in_array($action, ['register', 'login'], true)) {
            $ip = site_normalize_ip($_SERVER['REMOTE_ADDR'] ?? '');
            $rules = site_spam_rules();
            if (site_spam_request_blocked($ip, $_SERVER['HTTP_USER_AGENT'] ?? '', $rules)) throw new RuntimeException($labels['bad_login']);
            $lock = admin_lock();
            try {
                $buckets = site_read_record(SITE_ROOT . '/data/.account-rate.php', 262144);
                $now = time();
                $buckets = array_filter($buckets, static function (array $hit) use ($now): bool { return $hit[0] > $now - 900; });
                $key = hash('sha256', $ip);
                $attempt = $buckets[$key] ?? [$now, 0];
                if ($attempt[1] >= 20) throw new RuntimeException($labels['bad_login']);
                $attempt[1]++;
                $buckets[$key] = $attempt;
                admin_write_record(SITE_ROOT . '/data/.account-rate.php', array_slice($buckets, -1500, null, true), 262144);
            } finally {
                fclose($lock);
            }
            if ($action === 'register') {
                if (!site_account_registration()) throw new RuntimeException($labels['closed']);
                $user = site_account_save(array_intersect_key($_POST, array_flip(['email','password','name'])), '', false);
                $registered = true;
                $template = site_account_settings()['mail']['welcome'] ?? site_account_mail_defaults()['welcome'];
                site_send_template($user['email'], $template, ['name'=>$user['name']]);
            } else {
                $email = strtolower(site_account_input($_POST,'email'));
                $user = null;
                foreach (site_accounts() as $candidate) if ($candidate['email'] === $email) { $user = $candidate; break; }
                $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
                $hash = $user['password'] ?? '$2y$10$Ui8v8xR1Xq1ClncxUX7Ju.HcfxBGi7ksmXZjY7rOZ49T2zSIlOTf6';
                if (!site_account_password($password) || !password_verify($password, $hash) || !$user || !$user['enabled']) {
                    throw new RuntimeException($labels['bad_login']);
                }
            }
            session_regenerate_id(true);
            unset($_SESSION['accountCsrf']);
            $_SESSION['accountId'] = $user['id'];
            $_SESSION['accountKey'] = hash('sha256', $user['password']);
        } else {
            throw new RuntimeException($labels['error']);
        }
    } catch (RuntimeException $error) {
        $_SESSION['accountNotice'] = $error->getMessage();
    }
    session_write_close();
    header('Location: ' . $url . ($action === 'register' && !$registered ? '?register=1' : $return), true, 303);
    exit;
}

function admin_input(array $input, string $key): string
{
    return site_account_input($input, $key);
}

$notice = $_SESSION['accountNotice'] ?? '';
unset($_SESSION['accountNotice']);
$reset = site_account_input($_GET, 'reset');
if (!site_account_current() && (($_GET['forgot'] ?? '') === '1' || $reset !== '')) {
    $form = $reset === '' ? 'forgot' : 'reset';
    $markup = '<h1>' . site_escape($labels['reset']) . '</h1>' . site_account_form($base, $form, site_account_token());
    if ($reset === '') {
        $markup .= '<label>' . site_escape($labels['email']) . ' <input name="email" type="email" required></label>';
    } else {
        $markup .= '<input type="hidden" name="reset" value="' . site_escape($reset) . '"><label>' . site_escape($labels['password'])
            . ' <input name="password" type="password" minlength="10" maxlength="72" required autocomplete="new-password"></label>';
    }
    $markup .= '<button>' . site_escape($labels['reset']) . '</button></form><a href="' . site_escape($url) . '">' . site_escape($labels['login']) . '</a>';
} else {
    $markup = site_account_markup($base, ($_GET['register'] ?? '') === '1', site_account_input($_GET, 'draft'));
}
session_write_close();
?>
<!doctype html><html lang="<?= site_escape(site_region_settings()['public']) ?>"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= site_escape($labels['account']) ?></title><link rel="stylesheet" href="<?= site_escape(site_url($base, 'templates/css/site.css')) ?>"></head>
<body><main class="wrap"><p><a class="button" href="<?= site_escape(site_url($base)) ?>"><?= site_escape(site_settings()['name']) ?></a>
<?php if (site_shop_enabled()): ?><a class="button" href="<?= site_escape(site_url($base, 'shop/')) ?>">Shop</a><?php endif; ?></p>
<?php if ($notice !== ''): ?><p class="notice" role="status"><?= site_escape($notice) ?></p><?php endif; ?>
<?= $markup ?></main></body></html>
