<?php
if (!defined('SITE_ROOT')) { http_response_code(404); exit; }

const SITE_MAIL_LIBRARY_FILE = SITE_ROOT . '/data/.mail-templates.php';

function site_mail_library(): array
{
    return site_read_record(SITE_MAIL_LIBRARY_FILE, 2097152) + ['templates'=>[], 'events'=>[]];
}

function site_mail_template(string $event, array $fallback): array
{
    try {
        $library = site_mail_library();
        $assigned = $library['events'][$event] ?? $event;
        $template = $library['templates'][$assigned] ?? null;
        if (is_string($template['subject'] ?? null) && is_string($template['body'] ?? null)) {
            return [$template['subject'], $template['body']];
        }
    } catch (RuntimeException $exception) {
        error_log('EWC: could not read email templates.');
    }
    return $fallback;
}

function site_account_mail_defaults(): array
{
    $ru = site_region_settings()['public'] === 'ru';
    return $ru ? [
        'welcome'=>['Регистрация на {{site}}', "Здравствуйте, {{name}}!\nВаш аккаунт на {{site}} создан."],
        'reset'=>['Сброс пароля на {{site}}', "Здравствуйте, {{name}}!\nСсылка на 30 минут: {{url}}"],
    ] : [
        'welcome'=>['Registration on {{site}}', "Hi {{name}},\nYour account on {{site}} is ready."],
        'reset'=>['Reset your password on {{site}}', "Hi {{name}},\nThis link expires in 30 minutes: {{url}}"],
    ];
}

function site_account_settings(): array
{
    return site_read_record(SITE_ROOT . '/admin/.account-settings.php', 262144);
}

function site_account_registration(): bool
{
    return (site_account_settings()['registration'] ?? false) === true;
}

function site_message_template(array $input, array $defaults): array
{
    $templates = [];
    foreach ($defaults as $key => $template) {
        if (!is_array($input[$key] ?? null)) continue;
        [$subject, $body] = [trim((string) ($input[$key][0] ?? '')), trim((string) ($input[$key][1] ?? ''))];
        if (strlen($subject) > 200 || preg_match('/[\r\n]/', $subject) || strlen($body) > 12000 || preg_match('//u', $subject . $body) !== 1) {
            throw new RuntimeException('Проверьте тему и текст письма.');
        }
        $templates[$key] = [$subject ?: $template[0], $body ?: $template[1]];
    }
    return $templates;
}

function site_send_template(string $email, array $template, array $variables): bool
{
    if (!function_exists('mail') || !filter_var($email, FILTER_VALIDATE_EMAIL)) return false;
    $tokens = [];
    foreach ($variables + ['site'=>site_settings()['name']] as $key => $value) $tokens['{{' . $key . '}}'] = $value;
    $subject = str_replace(["\r","\n"], ' ', strtr($template[0], $tokens));
    return @mail($email, site_mail_encode_subject($subject), strtr($template[1], $tokens), 'Content-Type: text/plain; charset=UTF-8');
}
