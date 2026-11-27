<?php
declare(strict_types=1);

ini_set('display_errors', '0');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');

function stopHttp(int $status, string $message = ''): void
{
    http_response_code($status);
    if ($message !== '') {
        header('Content-Type: application/json');
        echo json_encode(['error' => $message]);
    }
    exit;
}
