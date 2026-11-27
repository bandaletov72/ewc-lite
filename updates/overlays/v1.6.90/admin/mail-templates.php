<?php
if (!defined('SITE_ROOT')) { http_response_code(404); exit; }

function admin_mail_events(string $language): array
{
    $labels = admin_mail_labels($language);
    $account = site_account_settings()['mail'] ?? [];
    $defaults = site_account_mail_defaults();
    $events = [
        'account-welcome'=>['label'=>$labels['welcome'], 'message'=>$account['welcome'] ?? $defaults['welcome'], 'tokens'=>['{{site}}','{{name}}']],
        'account-reset'=>['label'=>$labels['reset'], 'message'=>$account['reset'] ?? $defaults['reset'], 'tokens'=>['{{site}}','{{name}}','{{url}}']],
    ];
    if (site_shop_enabled()) {
        require_once site_shop_directory() . '/module.php';
        $settings = shop_settings()['mail'] ?? [];
        foreach (shop_mail_defaults() as $id => $default) {
            $events['shop-' . $id] = ['label'=>$labels[$id], 'message'=>$settings[$id] ?? $default,
                'tokens'=>['{{site}}','{{name}}','{{order}}','{{status}}','{{total}}']];
        }
    }
    foreach (site_forms() as $id => $form) {
        $mail = site_form_mail_settings($form);
        $events[site_form_mail_event((string) $id)] = ['label'=>admin_form_name((string) $id, $form, $language) . ' · [' . $id . ']',
            'message'=>[$mail['mail_subject'], $mail['mail_body']], 'form'=>$form, 'id'=>$id,
            'tokens'=>array_map(static function (string $field): string { return '{' . $field . '}'; }, array_column($form['fields'], 'name'))];
    }
    return $events;
}

function admin_save_mail_template(array $input, string $language): string
{
    $labels = admin_mail_labels($language);
    $id = admin_input($input, 'template');
    $revision = admin_input($input, 'revision');
    $lock = admin_lock();
    try {
        $events = admin_mail_events($language);
        $event = $events[$id] ?? null;
        if (isset($event['form'])) {
            if (!hash_equals(admin_settings_revision($event['form']), $revision)) throw new RuntimeException($labels['conflict']);
            $forms = site_forms();
            $forms[$event['id']] = admin_validate_form_mail(site_form_mail_settings($input) + $event['form']);
            admin_write_record(site_content_record(SITE_FORMS_FILE), $forms, SITE_FORMS_RECORD_LIMIT);
            return $id;
        }
        $library = site_mail_library();
        if (!hash_equals(admin_settings_revision($library), $revision)) throw new RuntimeException($labels['conflict']);
        if ($id !== 'new' && !$event && !isset($library['templates'][$id])) throw new RuntimeException($labels['conflict']);
        $name = $event['label'] ?? trim(admin_input($input, 'name'));
        $subject = trim(admin_input($input, 'mail_subject'));
        $body = trim(str_replace(["\r\n", "\r"], "\n", admin_input($input, 'mail_body')));
        $binding = $event ? '' : admin_input($input, 'event');
        if ($binding !== '' && !isset($events[$binding])) throw new RuntimeException($labels['conflict']);
        if (!$event && (!admin_valid_form_label($name) || strlen($name) > 240)) throw new RuntimeException($labels['invalid']);
        $forForm = isset($events[$binding]['form']);
        admin_validate_form_mail(['recipient'=>'','copies'=>'','mail_subject'=>$subject,'mail_body'=>$body]);
        if ((!$forForm && strlen($subject) > 200) || $subject === '' || $body === '') throw new RuntimeException($labels['invalid']);
        if ($id === 'new') $id = 'mail-' . bin2hex(random_bytes(12));
        $library['templates'][$id] = ['name'=>$name,'subject'=>$subject,'body'=>$body];
        if (!$event) {
            $library['events'] = array_filter($library['events'], static function (string $target) use ($id): bool { return $target !== $id; });
            if ($binding !== '') $library['events'][$binding] = $id;
        }
        admin_write_record(SITE_MAIL_LIBRARY_FILE, $library, 2097152);
        return $id;
    } finally {
        fclose($lock);
    }
}
