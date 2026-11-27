<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
require __DIR__ . '/src/installation.php';
try {
    $settings = ewcInstallationConfig($argv[1] ?? '', 'tunnel');
    echo $settings['tunnel'], "\t", $settings['url'], "\n";
} catch (RuntimeException $error) {
    fwrite(STDERR, $error->getMessage() . PHP_EOL);
    exit(1);
}
