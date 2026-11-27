<?php
if (!defined('SITE_ROOT')) {
    http_response_code(404);
    exit;
}

const SITE_JOBS_CONFIG = SITE_ROOT . '/admin/.maintenance-jobs.php';
const SITE_JOBS_STATE = SITE_ROOT . '/admin/.maintenance-state.php';

function admin_schedule_clock($value): bool
{
    return is_string($value) && preg_match('/\A(?:[01][0-9]|2[0-3]):[0-5][0-9]\z/', $value) === 1;
}

function admin_schedule_settings($input): array
{
    $input = is_array($input) ? $input : [];
    $days = is_array($input['days'] ?? null) ? $input['days'] : range(1, 7);
    return ['days' => array_values(array_intersect(range(1, 7), array_filter($days, 'is_scalar'))),
        'from' => admin_schedule_clock($input['from'] ?? null) ? $input['from'] : '00:00',
        'to' => admin_schedule_clock($input['to'] ?? null) ? $input['to'] : '00:00'];
}

function admin_jobs_settings(): array
{
    try {
        $settings = site_read_record(SITE_JOBS_CONFIG);
    } catch (RuntimeException $exception) {
        $settings = [];
    }
    $hours = (int) ($settings['updates'] ?? 0);
    $hours = in_array($hours, [1, 6, 12, 24], true) ? $hours : 0;
    $updates = is_array($settings['updateSchedule'] ?? null) ? $settings['updateSchedule'] : [];
    $backup = is_array($settings['backupSchedule'] ?? null) ? $settings['backupSchedule'] : [];
    return [
        'index' => ($settings['index'] ?? false) === true,
        'legacyHours' => isset($settings['updateSchedule']) ? 0 : $hours,
        'updateSchedule' => admin_schedule_settings($updates) + [
            'engine' => ($updates['engine'] ?? ($hours > 0)) === true,
            'theme' => ($updates['theme'] ?? false) === true,
            'spam' => ($updates['spam'] ?? false) === true,
        ],
        'backupSchedule' => admin_schedule_settings($backup) + ['backup' => ($backup['backup'] ?? false) === true],
    ];
}

function admin_save_jobs(array $input)
{
    $lock = admin_lock();
    try {
        $settings = site_read_record(SITE_JOBS_CONFIG);
        $settings['index'] = ($input['index'] ?? '') === '1';
        admin_write_record(SITE_JOBS_CONFIG, $settings);
    } finally {
        fclose($lock);
    }
}

function admin_save_schedule(string $key, array $input, array $switches, string $language)
{
    $schedule = admin_schedule_settings($input);
    if (!is_array($input['days'] ?? null)) $schedule['days'] = [];
    $enabled = false;
    foreach ($switches as $switch) {
        $schedule[$switch] = ($input[$switch] ?? '') === '1';
        $enabled = $enabled || $schedule[$switch];
    }
    if (!admin_schedule_clock($input['from'] ?? null) || !admin_schedule_clock($input['to'] ?? null)
        || (!$schedule['days'] && $enabled)) {
        throw new RuntimeException(admin_schedule_labels($language)[7]);
    }
    $lock = admin_lock();
    try {
        $settings = site_read_record(SITE_JOBS_CONFIG);
        $settings[$key] = $schedule;
        if ($key === 'updateSchedule') unset($settings['updates']);
        admin_write_record(SITE_JOBS_CONFIG, $settings);
    } finally {
        fclose($lock);
    }
}

function admin_schedule_labels(string $language): array
{
    $labels = require SITE_ROOT . '/app/help-admin-schedule.php';
    return $labels[$language] ?? $labels['ru'];
}

function admin_schedule_day(array $schedule, DateTimeImmutable $now): string
{
    $time = $now->format('H:i');
    $from = $schedule['from'];
    $to = $schedule['to'];
    if ($from < $to && ($time < $from || $time >= $to)) return '';
    if ($from > $to) {
        if ($time < $from && $time >= $to) return '';
        if ($time < $to) $now = $now->modify('-1 day');
    }
    return in_array((int) $now->format('N'), $schedule['days'], true) ? $now->format('Y-m-d') : '';
}

function admin_run_scheduled_jobs(): array
{
    $jobs = admin_jobs_settings();
    $updates = $jobs['updateSchedule'];
    $backup = $jobs['backupSchedule'];
    if (!$jobs['index'] && !$updates['engine'] && !$updates['theme'] && !$updates['spam'] && !$backup['backup']) return [];

    $path = SITE_ROOT . '/admin/.maintenance-lock.php';
    $lock = !is_link($path) ? @fopen($path, 'c+b') : false;
    if ($lock === false) throw new RuntimeException('Не удалось открыть блокировку обслуживания.');
    try {
        if (!flock($lock, LOCK_EX | LOCK_NB)) return [];
        $state = site_read_record(SITE_JOBS_STATE);
        $now = new DateTimeImmutable('now');
        $stamp = $now->getTimestamp();
        $messages = [];
        if ($jobs['index'] && $stamp - ($state['index'] ?? 0) >= 900) {
            try {
                $files = admin_lock();
                try {
                    $messages[] = 'Страниц в индексе — ' . admin_rebuild_search(null, true);
                    $state['index'] = $stamp;
                } finally {
                    fclose($files);
                }
                admin_write_record(SITE_JOBS_STATE, $state);
            } catch (RuntimeException $exception) {
                $messages[] = $exception->getMessage();
            }
        }
        $updateDay = admin_schedule_day($updates, $now);
        $backupDay = admin_schedule_day($backup, $now);
        $tasks = ['backup' => $backup['backup'] ? $backupDay : '',
            'spam' => $updates['spam'] ? $updateDay : '', 'theme' => $updates['theme'] ? $updateDay : '',
            'engine' => $updates['engine'] ? $updateDay : ''];
        foreach ($tasks as $name => $day) {
            $legacy = $name === 'engine' && $jobs['legacyHours'] > 0;
            if ($day === '' || ($legacy ? $stamp - ($state['update'] ?? 0) < $jobs['legacyHours'] * 3600
                : ($state[$name . 'Day'] ?? '') === $day)) continue;
            try {
                $version = $name === 'engine' ? site_update_version() : '';
                $message = admin_run_scheduled_task($name);
                if ($message !== '') $messages[] = $message;
                // Check the new bundle on the next scheduler run when the engine has just changed.
                if ($name === 'engine' && $version !== site_update_version()) unset($state['themeDay']);
                $state[$name . 'Day'] = $day;
                if ($legacy) $state['update'] = $stamp;
                admin_write_record(SITE_JOBS_STATE, $state);
            } catch (RuntimeException $exception) {
                $messages[] = $exception->getMessage();
            }
        }
        return $messages;
    } finally {
        fclose($lock);
    }
}

function admin_run_scheduled_task(string $name): string
{
    if ($name === 'engine') return site_update_from_github();
    if ($name === 'backup') {
        $lock = admin_lock();
        try {
            return 'Резервная копия сохранена. ' . admin_backup_capture(admin_backup_files());
        } finally {
            fclose($lock);
        }
    }
    if ($name === 'spam') {
        $repository = site_update_github_repo();
        $version = $repository === '' ? '' : site_update_github_spam_version($repository);
        if ($version === '') throw new RuntimeException(site_spam_admin_labels('ru')[8]);
        if (!version_compare($version, site_spam_version(), '>')) return '';
        admin_save_spam([], null, 'ru', admin_spam_github_presets('ru'));
        return site_spam_admin_labels('ru')[7];
    }
    $lock = admin_lock();
    try {
        $theme = site_design_active();
        if (!isset(admin_design_starter_names()[$theme])) return '';
        $versions = admin_design_versions($theme);
        if (!admin_update_item($versions['installed'], $versions['available'], '')['outdated']) return '';
        admin_design_update_starter($theme, 'design', null, true);
        return 'Тема обновлена до версии ' . $versions['available'] . '.';
    } finally {
        fclose($lock);
    }
}
