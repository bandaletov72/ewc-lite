<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}

const SITE_MAIL_DEFAULT_SUBJECT = 'Новое сообщение с сайта';
const SITE_MAIL_ADDRESS_LIMIT = 900;
const SITE_MAIL_SUBJECT_LIMIT = 300;
const SITE_MAIL_TEMPLATE_LIMIT = 12000;

function site_form_mail_settings(array $form): array
{
    $settings = ['recipient' => '', 'copies' => '', 'mail_subject' => '', 'mail_body' => ''];
    foreach ($settings as $key => $default) {
        if (isset($form[$key]) && is_string($form[$key])) {
            $settings[$key] = $form[$key];
        }
    }
    return $settings;
}

/** @return array|null */
function site_mail_addresses(string $list)
{
    if (strlen($list) > SITE_MAIL_ADDRESS_LIMIT || preg_match('/[\x00-\x1F\x7F]/', $list)) {
        return null;
    }
    if (trim($list) === '') {
        return [];
    }
    $addresses = [];
    foreach (explode(',', $list) as $item) {
        $address = trim($item);
        if (strlen($address) > 254 || !filter_var($address, FILTER_VALIDATE_EMAIL)) {
            return null;
        }
        $key = strtolower($address);
        $addresses[$key] = $addresses[$key] ?? $address;
    }
    return strlen(implode(', ', $addresses)) <= SITE_MAIL_ADDRESS_LIMIT ? $addresses : null;
}

function site_message_recipients(string $base, string $fallback = ''): string
{
    $city = site_request_city($base);
    $cityAddress = $city['email'] ?? '';
    foreach ([$cityAddress, site_settings()['messageRecipients'], $fallback] as $value) {
        $addresses = is_string($value) ? site_mail_addresses($value) : null;
        if ($addresses) return implode(', ', $addresses);
    }
    return '';
}

function site_mail_utf8_prefix(string $value, int $limit): string
{
    $prefix = substr($value, 0, $limit);
    for ($i = 0; $i < 3 && $prefix !== '' && preg_match('//u', $prefix) !== 1; $i++) {
        $prefix = substr($prefix, 0, -1);
    }
    return preg_match('//u', $prefix) === 1 ? $prefix : '';
}

function site_mail_expand(string $template, array $values, int $limit): string
{
    $result = '';
    $parts = preg_split('/(\{[a-z][a-z0-9_]{0,63}\})/', $template, -1, PREG_SPLIT_DELIM_CAPTURE);
    foreach ($parts as $part) {
        $value = $values[$part] ?? $part;
        $remaining = $limit - strlen($result);
        if (strlen($value) > $remaining) {
            return $result . site_mail_utf8_prefix($value, $remaining);
        }
        $result .= $value;
    }
    return $result;
}

function site_mail_encode_subject(string $subject): string
{
    $subject = trim(preg_replace('/[\p{Cc}\p{Zl}\p{Zp}]+/u', ' ', $subject) ?? '');
    $remaining = site_mail_utf8_prefix($subject === '' ? SITE_MAIL_DEFAULT_SUBJECT : $subject, 480);
    $encoded = [];
    while ($remaining !== '') {
        // Each UTF-8 encoded word must fit within the RFC 2047 limit of 75 characters.
        $part = site_mail_utf8_prefix($remaining, 42);
        $encoded[] = '=?UTF-8?B?' . base64_encode($part) . '?=';
        $remaining = substr($remaining, strlen($part));
    }
    return implode(' ', $encoded);
}

function site_form_mail_event(string $id): string
{
    $design = site_design_active();
    return 'form-' . $id . ($design === '' ? '' : '-' . $design);
}

/** @return array|null */
function site_form_mail_message(array $form, string $replyTo, array $message)
{
    $settings = site_form_mail_settings($form);
    $recipients = site_mail_addresses($settings['recipient']);
    $copies = site_mail_addresses($settings['copies']);
    if (!$recipients || $copies === null) {
        return null;
    }

    $values = [];
    $lines = ['Форма: [' . $message['form'] . ']', 'Домен: ' . $message['host']];
    foreach ($message['fields'] as $key => $field) {
        $values['{' . $key . '}'] = $field['value'];
        $lines[] = $field['label'] . ': ' . $field['value'];
    }

    [$subjectText, $bodyText] = site_mail_template(site_form_mail_event($message['form']), [$settings['mail_subject'], $settings['mail_body']]);
    $subject = site_mail_expand($subjectText, $values, 480);
    $body = $bodyText === '' ? implode("\n", $lines) : site_mail_expand($bodyText, $values, 65536);
    $body = str_replace(["\r\n", "\r"], "\n", $body);
    $headers = [
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8',
        'Content-Transfer-Encoding: quoted-printable',
    ];
    $copies = array_diff_key($copies, $recipients);
    if ($copies) {
        $headers[] = 'Cc: ' . implode(', ', $copies);
    }
    if ($replyTo !== '' && strlen($replyTo) <= 254 && !preg_match('/[\x00-\x1F\x7F]/', $replyTo)
        && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
        $headers[] = 'Reply-To: ' . $replyTo;
    }

    return [implode(', ', $recipients), site_mail_encode_subject($subject),
        quoted_printable_encode(str_replace("\n", "\r\n", $body)), implode("\r\n", $headers)];
}

function site_notify_message(array $form, string $replyTo, array $message)
{
    $mail = site_form_mail_message($form, $replyTo, $message);
    if ($mail !== null && function_exists('mail')) {
        @mail(...$mail);
    }
}
