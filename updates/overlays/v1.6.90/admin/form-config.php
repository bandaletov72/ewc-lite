<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}

function admin_form_draft(array $submitted): array
{
    $fields = [];
    foreach (is_array($submitted['fields'] ?? null) ? $submitted['fields'] : [] as $row) {
        if (!is_array($row)) {
            continue;
        }
        $fields[] = [
            'name' => trim(admin_input($row, 'name')),
            'label' => trim(admin_input($row, 'label')),
            'type' => admin_input($row, 'type'),
            'required' => admin_input($row, 'required') === '1',
            'options' => admin_rule_lines(admin_input($row, 'options')),
            'maxLength' => admin_input($row, 'maxLength'),
        ];
    }
    return [
        'title' => trim(admin_input($submitted, 'title')),
        'button' => trim(admin_input($submitted, 'button')),
        'fields' => $fields,
        'captcha' => [
            'enabled' => admin_input($submitted, 'captcha_enabled') === '1',
            'sitekey' => trim(admin_input($submitted, 'captcha_sitekey')),
            'secret' => trim(admin_input($submitted, 'captcha_secret')),
        ],
    ] + array_intersect_key(site_form_mail_settings($submitted), $submitted);
}

function admin_form_name(string $id, array $form, string $language): string
{
    $title = trim($form['title'] ?? '');
    if ($title !== '') return $title;
    $button = trim($form['button'] ?? '');
    return $id === 'form1' && $button === 'Отправить' ? site_edit_layout_labels($language)[2] : $button;
}

function admin_rule_lines(string $text): array
{
    return array_values(array_unique(array_filter(
        array_map('trim', preg_split('/\r\n|\n|\r/', $text)),
        static function (string $line): bool { return $line !== ''; }
    )));
}

function admin_valid_form_label(string $text): bool
{
    return $text !== '' && strlen($text) <= 1500 && preg_match('//u', $text) === 1
        && !preg_match('/[\x00-\x1F\x7F]/', $text);
}

function admin_validate_form_mail(array $form): array
{
    foreach (['recipient' => 'Кому', 'copies' => 'Копии'] as $key => $label) {
        $addresses = site_mail_addresses($form[$key]);
        if ($addresses === null) {
            throw new RuntimeException('Проверьте адреса в поле «' . $label . '». Укажите email через запятую.');
        }
        $form[$key] = implode(', ', $addresses);
    }
    if (preg_match('//u', $form['mail_subject']) !== 1 || preg_match('/[\p{Cc}\p{Zl}\p{Zp}]/u', $form['mail_subject'])
        || site_form_text_length($form['mail_subject']) > SITE_MAIL_SUBJECT_LIMIT) {
        throw new RuntimeException('Тема письма должна быть одной строкой длиной до ' . SITE_MAIL_SUBJECT_LIMIT . ' символов.');
    }
    if (strlen($form['mail_body']) > SITE_MAIL_TEMPLATE_LIMIT || preg_match('//u', $form['mail_body']) !== 1
        || preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', $form['mail_body'])) {
        throw new RuntimeException('Текст письма должен быть в UTF-8 и занимать не более 12 000 байт.');
    }
    $form['mail_subject'] = trim($form['mail_subject']);
    $form['mail_body'] = trim(str_replace(["\r\n", "\r"], "\n", $form['mail_body']));
    return $form;
}

function admin_store_form(string $id, array $form, string $revision)
{
    if (!site_valid_form_id($id)) {
        throw new RuntimeException('Введите код формы в формате form1, form2 и так далее.');
    }
    if (!admin_valid_form_label($form['button']) || !$form['fields']) {
        throw new RuntimeException('Укажите текст кнопки и добавьте хотя бы одно поле.');
    }
    if (($form['title'] ?? '') !== '' && (!admin_valid_form_label($form['title']) || site_form_text_length($form['title']) > 120)) {
        throw new RuntimeException('Проверьте длину и формат полей настроек.');
    }
    $hasMail = (bool) array_intersect_key($form, site_form_mail_settings([]));
    if ($hasMail) $form = admin_validate_form_mail($form + site_form_mail_settings([]));

    $names = [];
    $types = site_form_types();
    $lengthLimits = site_form_length_limits();
    foreach ($form['fields'] as &$field) {
        if (!preg_match('/\A[a-z][a-z0-9_]{0,63}\z/', $field['name']) || isset($names[$field['name']])) {
            throw new RuntimeException('У каждого поля должен быть уникальный ключ из латинских букв, цифр и подчёркиваний.');
        }
        $names[$field['name']] = true;
        if (!admin_valid_form_label($field['label']) || !isset($types[$field['type']])) {
            throw new RuntimeException('Укажите подпись и тип каждого поля.');
        }
        $limit = $field['maxLength'];
        $maximum = $lengthLimits[$field['type']][1];
        if ($limit === '') {
            unset($field['maxLength']);
        } elseif (!preg_match('/\A[0-9]{1,5}\z/', $limit) || (int) $limit < 1 || (int) $limit > $maximum) {
            throw new RuntimeException('Укажите максимальную длину от 1 до ' . $maximum . ' для поля «' . $field['label'] . '».');
        } else {
            $field['maxLength'] = (int) $limit;
        }
        if (in_array($field['type'], ['radio', 'select'], true)) {
            if (!$field['options']) {
                throw new RuntimeException('Добавьте варианты для полей с выбором.');
            }
            foreach ($field['options'] as $option) {
                if (!admin_valid_form_label($option)) {
                    throw new RuntimeException('Проверьте длину и формат вариантов выбора.');
                }
                if (site_form_text_length($option) > site_form_field_length($field)) {
                    throw new RuntimeException('Вариант в поле «' . $field['label'] . '» превышает его максимальную длину.');
                }
            }
        } else {
            $field['options'] = [];
        }
    }
    unset($field);

    $lock = admin_lock();
    try {
        $forms = site_forms();
        $current = isset($forms[$id]) ? admin_settings_revision($forms[$id]) : '';
        if (!hash_equals($current, $revision)) {
            throw new RuntimeException('Форма уже существует или была изменена. Откройте её заново.');
        }
        $captcha = site_captcha_settings($form, false);
        $previousCaptcha = site_captcha_settings($forms[$id] ?? [], false);
        if ($captcha['secret'] === '') {
            $captcha['secret'] = $previousCaptcha['secret'];
        }
        if (($captcha['sitekey'] !== '' && !site_valid_captcha_key($captcha['sitekey']))
            || ($captcha['secret'] !== '' && !site_valid_captcha_key($captcha['secret']))) {
            throw new RuntimeException('Проверьте формат ключей CAPTCHA.');
        }
        $effectiveCaptcha = site_captcha_settings(['captcha' => $captcha]);
        if ($effectiveCaptcha['enabled'] && ($effectiveCaptcha['sitekey'] === '' || $effectiveCaptcha['secret'] === '')) {
            throw new RuntimeException('Для включения CAPTCHA укажите ключ сайта и секретный ключ reCAPTCHA v2.');
        }
        if ($captcha['enabled'] || $captcha['sitekey'] !== '' || $captcha['secret'] !== '') {
            $form['captcha'] = $captcha;
        } else {
            unset($form['captcha']);
        }
        if (!$hasMail) $form += site_form_mail_settings($forms[$id] ?? []);
        $forms[$id] = $form;
        uksort($forms, 'strnatcmp');
        admin_write_record(site_content_record(SITE_FORMS_FILE), $forms, SITE_FORMS_RECORD_LIMIT);
    } finally {
        fclose($lock);
    }
}

function admin_spam_rule(string $field, string $value): string
{
    return $field === 'ips' ? site_spam_ip_rule($value) : (admin_valid_form_label($value) ? $value : '');
}

function admin_spam_github_presets(string $locale, ?callable $progress = null): array
{
    $labels = site_spam_admin_labels($locale);
    $repository = site_update_github_repo();
    if ($repository === '') throw new RuntimeException($labels[8]);
    if ($progress !== null) $progress('source');
    $source = site_update_github_source($repository, true);
    return site_update_use_signed_archive($source, static function (string $file) use ($labels): array {
        $zip = new ZipArchive();
        if ($zip->open($file, ZipArchive::CHECKCONS) !== true) throw new RuntimeException($labels[8]);
        try {
            $size = $zip->statName(SITE_SPAM_PRESET_PATH)['size'] ?? 0;
            if ($size <= 0 || $size > SITE_SPAM_PRESET_LIMIT) throw new RuntimeException($labels[8]);
            $bytes = $zip->getFromName(SITE_SPAM_PRESET_PATH);
            if ($bytes === false || strpos($bytes, SITE_RECORD_HEADER) !== 0) throw new RuntimeException($labels[8]);
            $data = json_decode(substr($bytes, strlen(SITE_RECORD_HEADER)), true);
            $version = $data['version'] ?? '1.0.0';
            if (!site_spam_valid_version($version)) throw new RuntimeException($labels[8]);
            $presets = ['version' => $version];
            foreach (['words', 'agents', 'ips'] as $field) {
                if (!is_array($data[$field] ?? null)) throw new RuntimeException($labels[8]);
                foreach ($data[$field] as $value) {
                    if (!is_string($value) || admin_spam_rule($field, $value) === '') throw new RuntimeException($labels[8]);
                    $presets[$field][] = admin_spam_rule($field, $value);
                }
                $presets[$field] = array_values(array_unique($presets[$field] ?? []));
            }
            return $presets;
        } finally {
            $zip->close();
        }
    }, $progress);
}

function admin_save_spam(array $draft, ?string $revision, string $locale, ?array $downloaded = null)
{
    $labels = site_spam_admin_labels($locale);
    $lock = admin_lock();
    try {
        $record = site_read_record(SITE_SPAM_FILE);
        $current = site_spam_rules($record);
        if ($revision !== null && !hash_equals(admin_settings_revision($current), $revision)) {
            throw new RuntimeException('Правила уже изменились. Скопируйте правки и откройте раздел заново.');
        }
        $oldPresets = site_spam_presets($record);
        if ($downloaded !== null && (!site_spam_valid_version($record['downloaded']['version'] ?? '')
            || version_compare($downloaded['version'], $record['downloaded']['version'], '>='))) {
            $record['downloaded'] = $downloaded;
        }
        $newPresets = site_spam_presets($record);
        foreach (['words', 'agents', 'ips'] as $field) {
            $rules = [];
            foreach (isset($draft[$field]) ? admin_rule_lines($draft[$field]) : $current[$field] as $value) {
                $rule = admin_spam_rule($field, $value);
                if ($rule === '') throw new RuntimeException($labels[$field === 'ips' ? 5 : 6]);
                $rules[] = $rule;
            }
            $record[$field] = array_values(array_unique(array_merge($rules, array_diff($newPresets[$field], $oldPresets[$field]))));
        }
        $record['seen'] = $newPresets;
        admin_write_record(SITE_SPAM_FILE, $record);
    } finally {
        fclose($lock);
    }
}
