<?php
if (!defined('SITE_ROOT')) { http_response_code(404); exit; }

function admin_update_progress_labels(string $language): array
{
    $catalog = require SITE_ROOT . '/app/help-update-progress.php';
    return $catalog[$language] ?? $catalog['ru'];
}

function admin_update_stream(callable $operation, string $mode, string $language)
{
    $labels = admin_update_progress_labels($language);
    session_write_close();
    ignore_user_abort(true);
    @set_time_limit(90);
    @ini_set('zlib.output_compression', '0');
    while (ob_get_level() > 0 && @ob_end_clean()) {
    }
    header('Content-Type: application/x-ndjson; charset=UTF-8');
    header('Cache-Control: no-store');
    header('X-Accel-Buffering: no');

    $send = static function (array $record, bool $first = false) {
        echo json_encode($record, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE),
            $first ? str_repeat(' ', 4096) : '', "\n";
        if (ob_get_level() > 0) @ob_flush();
        flush();
    };
    $labels['spam-save'] = admin_schedule_labels($language)[19];
    if ($mode === 'demo') {
        $storeLabels = admin_theme_store_labels($language);
        foreach (['demo-source','demo-package','demo-products','demo-backup','demo-save'] as $index => $step) {
            $labels[$step] = $storeLabels[22 + $index];
        }
        $labels['start'] = $storeLabels[21];
    }
    $ranges = [
        'theme' => ['theme-design' => [8, 12], 'theme-pages' => [8, 12], 'theme-missing' => [8, 12], 'backup' => [15, 34], 'backup-verify' => [35, 38],
            'backup-saved' => [40, 42], 'prepare' => [46, 74], 'install' => [75, 99]],
        'engine' => ['source' => [4, 8], 'archive' => [9, 54], 'signature' => [55, 62], 'verify' => [63, 76],
            'engine-content' => [77, 77], 'prepare' => [78, 90], 'install' => [91, 99]],
        'spam' => ['source' => [4, 8], 'archive' => [9, 68], 'signature' => [69, 79],
            'verify' => [80, 90], 'spam-save' => [95, 95]],
        'index' => ['index-scan' => [5, 5], 'index-pages' => [10, 92], 'index-save' => [95, 95], 'index-shop' => [98, 98]],
        'demo' => ['demo-source'=>[5, 5], 'demo-package'=>[10, 44], 'demo-products'=>[48, 83],
            'demo-backup'=>[88, 88], 'demo-save'=>[96, 96]],
    ][$mode === 'shop' ? 'engine' : $mode];
    $lastStep = '';
    $lastTime = 0.0;
    $send(['step' => 'start', 'percent' => 0, 'message' => $labels[$mode === 'index' ? 'index-start' : 'start']], true);
    $progress = static function (string $step, ?int $done = null, ?int $total = null, string $detail = '')
        use ($send, $labels, $ranges, $mode, &$lastStep, &$lastTime) {
        $now = microtime(true);
        if ($step === $lastStep && $now - $lastTime < .08 && ($total === null || $done !== $total)) return;
        list($start, $end) = $ranges[$step];
        $percent = $total > 0 ? $start + (int) floor(($end - $start) * min(1, $done / $total)) : $start;
        $isDownload = in_array($step, ['archive', 'signature'], true);
        if ($isDownload && $detail === '') $detail = ($mode === 'shop' ? SITE_SHOP_ASSET : SITE_UPDATE_ASSET) . ($step === 'signature' ? '.sig' : '');
        $send(['step' => $step, 'percent' => $percent,
            'message' => $labels[$step] . ($detail !== '' ? ' · ' . $detail : ''),
            'done' => $done, 'total' => $total, 'bytes' => $isDownload || $step === 'demo-package']);
        $lastStep = $step;
        $lastTime = $now;
    };
    try {
        $result = $operation($progress);
        $record = ['result' => 'success', 'percent' => 100, 'message' => admin_t($result)];
        if ($mode === 'engine') $record['version'] = site_update_version();
        if ($mode === 'shop') $record['version'] = site_shop_version();
        $send($record);
    } catch (RuntimeException $exception) {
        $send(['result' => 'error', 'message' => admin_t($exception->getMessage())]);
    } catch (Throwable $exception) {
        error_log('EWC update: ' . $exception->getMessage());
        $send(['result' => 'error', 'message' => $labels['failure']]);
    }
    exit;
}
