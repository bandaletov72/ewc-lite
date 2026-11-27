<?php
if (!defined('SITE_ROOT')) { http_response_code(404); exit; }

function shop_integration_drivers(): array
{
    return [
        'yandex-market'=>['Яндекс Маркет','marketplace_api',['campaign_id'],['api_key'],'https://yandex.ru/dev/market/partner-api/'],
        'ozon'=>['Ozon','marketplace_api',['client_id'],['api_key'],'https://docs.ozon.ru/api/seller/'],
        'wb'=>['Wildberries','marketplace_api',[],['api_key'],'https://dev.wildberries.ru/'],
        'russian-post'=>['Почта России','delivery_api',[],['app_token','user_key'],'https://otpravka.pochta.ru/specification'],
        'yandex-delivery'=>['Яндекс Доставка','delivery_api',['source_address'],['api_key'],'https://yandex.ru/support/delivery-profile/ru/api/express/'],
        'cdek'=>['СДЭК','delivery_api',['client_id'],['client_secret'],'https://api-docs.cdek.ru/'],
        'sberpay'=>['SberPay','payment_api',['shop_id'],['secret_key'],'https://yookassa.ru/developers/'],
        'sbp'=>['СБП','payment_api',['shop_id'],['secret_key'],'https://yookassa.ru/developers/'],
        'yoomoney'=>['ЮMoney','payment_api',['shop_id'],['secret_key'],'https://yookassa.ru/developers/'],
        'yookassa'=>['ЮKassa · VISA / Mastercard / Мир','payment_api',['shop_id'],['secret_key'],'https://yookassa.ru/developers/'],
        'tbank'=>['Т-Банк · карты','payment_api',['terminal_key'],['terminal_password'],'https://developer.tbank.ru/eacq/'],
        'cloudpayments'=>['CloudPayments · карты','payment_api',['public_id'],['api_secret'],'https://developers.cloudpayments.ru/'],
        'robokassa'=>['Robokassa · карты','payment_api',['merchant_login'],['password1'],'https://docs.robokassa.ru/ru/pay-interface'],
    ];
}

function shop_integration_catalog(): array
{
    $modules = [];
    foreach (shop_integration_drivers() as $id => $driver) $modules[$id] = ['driver'=>$id, 'name'=>$driver[0], 'version'=>'1.0.0'];
    return site_read_record(SHOP_DIR . '/integration-modules.php', 262144) + $modules;
}

function shop_integration_config(): array
{
    return site_read_record(SHOP_DIR . '/integrations.php', 262144);
}

function shop_integration_key(bool $create = false): string
{
    $filename = SHOP_DIR . '/integration-key.php';
    $record = site_read_record($filename, 8192);
    if (!$record && $create) {
        $record = ['key'=>bin2hex(random_bytes(32))];
        admin_write_record($filename, $record, 8192);
    }
    if (!preg_match('/\A[0-9a-f]{64}\z/', $record['key'] ?? '')) throw shop_error('storage');
    return hex2bin($record['key']);
}

function shop_integration_secret(string $value, string $binding, bool $encrypt): string
{
    if (!function_exists('openssl_encrypt')) throw shop_error('integration_unavailable');
    $key = shop_integration_key($encrypt);
    if ($encrypt) {
        $iv = random_bytes(12);
        $data = openssl_encrypt($value, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag, $binding);
        if (!is_string($data)) throw shop_error('storage');
        return base64_encode($iv . $tag . $data);
    }
    $raw = base64_decode($value, true);
    if ($raw === false || strlen($raw) <= 28) throw shop_error('storage');
    $data = openssl_decrypt(substr($raw, 28), 'aes-256-gcm', $key, OPENSSL_RAW_DATA, substr($raw, 0, 12), substr($raw, 12, 16), $binding);
    if (!is_string($data)) throw shop_error('storage');
    return $data;
}

function shop_integration_values(string $id, array $config): array
{
    $values = $config['fields'] ?? [];
    foreach ($config['secrets'] ?? [] as $name => $secret) {
        $values[$name] = shop_integration_secret($secret, $id . ':' . $name, false);
    }
    return $values;
}

function shop_integration_save(string $id, array $input): void
{
    $driver = shop_integration_drivers()[shop_integration_catalog()[$id]['driver'] ?? ''] ?? null;
    if (!$driver) throw shop_error('not_found');
    $config = shop_integration_config();
    $item = $config[$id] ?? ['fields'=>[], 'secrets'=>[]];
    if (!hash_equals(hash('sha256', serialize($item)), shop_input($input, 'revision'))) throw shop_error('conflict');
    $item['enabled'] = shop_input($input, 'enabled') === '1';
    foreach (array_merge($driver[2], $driver[3]) as $key) {
        $value = shop_input($input, $key);
        if (strlen($value) > 4096 || preg_match('/[\p{C}]/u', $value) !== 0) throw shop_error('row', shop_label($key));
        if (in_array($key, $driver[2], true)) {
            $item['fields'][$key] = $value;
        } elseif ($value !== '') {
            $item['secrets'][$key] = shop_integration_secret($value, $id . ':' . $key, true);
        } elseif (isset($input['clear'][$key]) && $input['clear'][$key] === '1') {
            unset($item['secrets'][$key]);
        }
    }
    if ($item['enabled']) {
        foreach ($driver[2] as $key) {
            if (($item['fields'][$key] ?? '') === '') throw shop_error('row', shop_label($key));
        }
        foreach ($driver[3] as $key) {
            if (!isset($item['secrets'][$key])) throw shop_error('row', shop_label($key));
        }
    }
    $config[$id] = $item;
    admin_write_record(SHOP_DIR . '/integrations.php', $config, 262144);
}

function shop_integration_request(string $url, array $headers = [], ?array $data = null, bool $form = false): array
{
    if (!function_exists('curl_init')) throw shop_error('integration_unavailable');
    $connection = curl_init($url);
    $response = '';
    if ($data !== null) {
        $headers[] = $form ? 'Content-Type: application/x-www-form-urlencoded' : 'Content-Type: application/json';
        curl_setopt($connection, CURLOPT_POSTFIELDS, $form ? http_build_query($data) : json_encode($data, JSON_UNESCAPED_UNICODE));
    }
    curl_setopt_array($connection, [
        CURLOPT_PROTOCOLS=>CURLPROTO_HTTPS, CURLOPT_FOLLOWLOCATION=>false, CURLOPT_HTTPHEADER=>$headers,
        CURLOPT_CONNECTTIMEOUT=>5, CURLOPT_TIMEOUT=>12, CURLOPT_SSL_VERIFYPEER=>true, CURLOPT_SSL_VERIFYHOST=>2,
        CURLOPT_WRITEFUNCTION=>static function ($connection, string $chunk) use (&$response): int {
            if (strlen($response) + strlen($chunk) > 2097152) return 0;
            $response .= $chunk;
            return strlen($chunk);
        },
    ]);
    $ok = curl_exec($connection);
    $status = (int) curl_getinfo($connection, CURLINFO_HTTP_CODE);
    curl_close($connection);
    if (!$ok || $status < 200 || $status >= 300) throw shop_error('integration_unavailable', $status ? 'HTTP ' . $status : '');
    $decoded = json_decode($response, true);
    return is_array($decoded) ? $decoded : [];
}

function shop_integration_can_check(string $driver): bool
{
    return !in_array($driver, ['tbank','robokassa'], true);
}

function shop_integration_check(string $id): void
{
    $module = shop_integration_catalog()[$id] ?? [];
    $type = $module['driver'] ?? '';
    $driver = shop_integration_drivers()[$type] ?? null;
    if (!$driver || !shop_integration_can_check($type)) throw shop_error('integration_unavailable');
    $values = shop_integration_values($id, shop_integration_config()[$id] ?? []);
    foreach (array_merge($driver[2], $driver[3]) as $key) {
        if (($values[$key] ?? '') === '') throw shop_error('row', shop_label($key));
    }
    switch ($type) {
        case 'yandex-market':
            shop_integration_request('https://api.partner.market.yandex.ru/v2/campaigns/' . rawurlencode($values['campaign_id']),
                ['Api-Key: ' . $values['api_key']]);
            break;
        case 'ozon':
            shop_integration_request('https://api-seller.ozon.ru/v3/product/list',
                ['Client-Id: ' . $values['client_id'], 'Api-Key: ' . $values['api_key']], ['filter'=>(object) [], 'limit'=>1]);
            break;
        case 'wb':
            shop_integration_request('https://common-api.wildberries.ru/ping', ['Authorization: ' . $values['api_key']]);
            break;
        case 'russian-post':
            shop_integration_request('https://otpravka-api.pochta.ru/1.0/user/settings', [
                'Authorization: AccessToken ' . $values['app_token'], 'X-User-Authorization: Basic ' . $values['user_key'],
            ]);
            break;
        case 'yandex-delivery':
            shop_integration_request('https://b2b.taxi.yandex.net/b2b/cargo/integration/v2/tariffs',
                ['Authorization: Bearer ' . $values['api_key'], 'Accept-Language: ru'], ['address'=>$values['source_address']]);
            break;
        case 'cdek':
            $result = shop_integration_request('https://api.cdek.ru/v2/oauth/token', [], [
                'grant_type'=>'client_credentials', 'client_id'=>$values['client_id'], 'client_secret'=>$values['client_secret'],
            ], true);
            if (empty($result['access_token'])) throw shop_error('integration_unavailable');
            break;
        case 'cloudpayments':
            $result = shop_integration_request('https://api.cloudpayments.ru/test', [
                'Authorization: Basic ' . base64_encode($values['public_id'] . ':' . $values['api_secret']),
            ], []);
            if (empty($result['Success'])) throw shop_error('integration_unavailable');
            break;
        default:
            shop_integration_request('https://api.yookassa.ru/v3/payments?limit=1', [
                'Authorization: Basic ' . base64_encode($values['shop_id'] . ':' . $values['secret_key']),
            ]);
    }
}

function shop_integration_github_catalog(): array
{
    $url = 'https://raw.githubusercontent.com/' . SITE_SHOP_REPOSITORY . '/main/marketplace-integrations/catalog.json';
    $raw = json_decode(site_update_download($url, '*/*', 65536), true);
    $trusted = shop_integration_drivers();
    $catalog = [];
    foreach (($raw['modules'] ?? []) as $id => $module) {
        if (!shop_id((string) $id) || !is_array($module) || !isset($trusted[$module['driver'] ?? ''])
            || !is_string($module['version'] ?? null) || !preg_match('/\A\d+\.\d+\.\d+\z/', $module['version'])) continue;
        $catalog[$id] = ['driver'=>$module['driver'], 'version'=>$module['version']];
    }
    return $catalog;
}

function shop_integration_install(string $id, array $remote): void
{
    $incoming = $remote[$id] ?? null;
    $existing = shop_integration_catalog()[$id] ?? null;
    if (!$incoming || ($existing && ($existing['driver'] !== $incoming['driver']
        || version_compare($incoming['version'], $existing['version'], '<=')))) throw shop_error('integration_unavailable');
    $url = 'https://raw.githubusercontent.com/' . SITE_SHOP_REPOSITORY . '/main/marketplace-integrations/'
        . rawurlencode($id) . '/' . $incoming['version'] . '.zip';
    $module = site_update_use_signed_archive(['archive'=>$url, 'signature'=>$url . '.sig', 'accept'=>'*/*'],
        static function (string $archive) use ($id, $incoming): array {
            $zip = new ZipArchive();
            if ($zip->open($archive, ZipArchive::CHECKCONS) !== true) throw shop_error('integration_unavailable');
            try {
                $manifest = json_decode($zip->numFiles === 1 ? ($zip->getFromName('integration.json', 16384) ?: '') : '', true);
                if (($manifest['id'] ?? '') !== $id || ($manifest['driver'] ?? '') !== $incoming['driver']
                    || ($manifest['version'] ?? '') !== $incoming['version'] || !is_string($manifest['name'] ?? null)
                    || strlen($manifest['name']) > 200 || preg_match('/\A[^\p{C}]+\z/u', $manifest['name']) !== 1) {
                    throw shop_error('integration_unavailable');
                }
                return $incoming + ['name'=>$manifest['name']];
            } finally {
                $zip->close();
            }
        });
    $installed = site_read_record(SHOP_DIR . '/integration-modules.php', 262144);
    $installed[$id] = $module;
    admin_write_record(SHOP_DIR . '/integration-modules.php', $installed, 262144);
}

function shop_payment_options(array $order): array
{
    if ($order['head'][11] !== 'RUB' || ($order['head'][2] !== 'new' && $order['head'][2] !== 'awaiting_payment')) return [];
    $payment = site_read_record(SHOP_DIR . '/payment-' . $order['id'] . '.php', 16384);
    $drivers = shop_integration_drivers();
    $config = shop_integration_config();
    return array_filter(shop_integration_catalog(), static function (array $module, string $id) use ($drivers, $config, $payment): bool {
        return $drivers[$module['driver']][1] === 'payment_api' && !empty($config[$id]['enabled'])
            && (empty($payment['integration']) || $payment['integration'] === $id);
    }, ARRAY_FILTER_USE_BOTH);
}

function shop_payment_url(string $id, array $module, array $order, string $returnUrl, int $invoice): string
{
    $values = shop_integration_values($id, shop_integration_config()[$id] ?? []);
    $minor = (int) $order['head'][10];
    $amount = number_format($minor / 100, 2, '.', '');
    $orderId = $order['id'];
    $description = 'Order ' . $orderId;
    $type = $module['driver'];
    if ($type === 'robokassa') {
        $signature = md5(implode(':', [$values['merchant_login'], $amount, $invoice, $values['password1'], 'Shp_order=' . $orderId]));
        return 'https://auth.robokassa.ru/Merchant/Index.aspx?' . http_build_query([
            'MerchantLogin'=>$values['merchant_login'], 'OutSum'=>$amount, 'InvId'=>$invoice, 'Description'=>$description,
            'SignatureValue'=>$signature, 'Shp_order'=>$orderId,
        ], '', '&', PHP_QUERY_RFC3986);
    }
    if ($type === 'tbank') {
        $request = ['TerminalKey'=>$values['terminal_key'], 'Amount'=>$minor, 'OrderId'=>$orderId,
            'Description'=>$description, 'SuccessURL'=>$returnUrl, 'FailURL'=>$returnUrl];
        $signature = $request + ['Password'=>$values['terminal_password']];
        ksort($signature, SORT_STRING);
        $request['Token'] = hash('sha256', implode('', $signature));
        $result = shop_integration_request('https://securepay.tinkoff.ru/v2/Init', [], $request);
        $url = !empty($result['Success']) ? ($result['PaymentURL'] ?? '') : '';
        $hosts = ['securepay.tinkoff.ru','securepay.tbank.ru'];
    } elseif ($type === 'cloudpayments') {
        $result = shop_integration_request('https://api.cloudpayments.ru/orders/create', [
            'Authorization: Basic ' . base64_encode($values['public_id'] . ':' . $values['api_secret']),
        ], ['Amount'=>$minor / 100, 'Currency'=>'RUB', 'Description'=>$description, 'InvoiceId'=>$orderId,
            'SuccessRedirectUrl'=>$returnUrl, 'FailRedirectUrl'=>$returnUrl]);
        $url = !empty($result['Success']) ? ($result['Model']['Url'] ?? '') : '';
        $hosts = ['orders.cloudpayments.ru','pay.cloudpayments.ru'];
    } else {
        $method = ['yookassa'=>'bank_card','sberpay'=>'sberbank','sbp'=>'sbp','yoomoney'=>'yoo_money'][$type];
        $result = shop_integration_request('https://api.yookassa.ru/v3/payments', [
            'Authorization: Basic ' . base64_encode($values['shop_id'] . ':' . $values['secret_key']),
            'Idempotence-Key: ' . hash('sha256', $orderId . ':' . $id),
        ], ['amount'=>['value'=>$amount,'currency'=>'RUB'], 'capture'=>true, 'description'=>$description,
            'payment_method_data'=>['type'=>$method], 'confirmation'=>['type'=>'redirect','return_url'=>$returnUrl],
            'metadata'=>['order'=>$orderId]]);
        $url = $result['confirmation']['confirmation_url'] ?? '';
        $hosts = ['yookassa.ru','yoomoney.ru'];
    }
    $parts = is_string($url) && strlen($url) <= 4096 ? parse_url($url) : false;
    if (!$parts || ($parts['scheme'] ?? '') !== 'https' || !in_array(strtolower($parts['host'] ?? ''), $hosts, true)
        || isset($parts['user']) || isset($parts['port']) || preg_match('/[\x00-\x20\\\\]/', $url)) throw shop_error('integration_unavailable');
    return $url;
}

function shop_start_payment(string $base, string $id, string $orderId): string
{
    if (!hash_equals($_SESSION['shopLastOrder'] ?? '', $orderId) || $orderId === '') throw shop_error('session');
    $lock = shop_lock();
    $file = SHOP_DIR . '/payment-' . $orderId . '.php';
    $invoice = 0;
    try {
        $order = shop_order($orderId);
        $module = $order ? (shop_payment_options($order)[$id] ?? null) : null;
        if (!$module) throw shop_error('integration_unavailable');
        $previous = site_read_record($file, 16384);
        if (!empty($previous['url'])) return $previous['url'];
        // Record the attempt before contacting a provider. A timeout can leave an invoice active remotely.
        if ($previous) throw shop_error('payment_pending');
        if ($module['driver'] === 'robokassa') {
            $sequence = SHOP_DIR . '/payment-sequence.php';
            $invoice = max(time(), (int) (site_read_record($sequence, 8192)['number'] ?? 0) + 1);
            if ($invoice >= 2147483647) throw shop_error('integration_unavailable');
            admin_write_record($sequence, ['number'=>$invoice], 8192);
        }
        $attempt = ['integration'=>$id, 'time'=>time(), 'invoice'=>$invoice];
        admin_write_record($file, $attempt, 16384);
    } finally {
        fclose($lock);
    }
    $url = shop_payment_url($id, $module, $order, site_canonical_url($base, 'shop/index.php') . '?view=checkout', $invoice);
    $lock = shop_lock();
    try {
        admin_write_record($file, $attempt + ['url'=>$url], 16384);
    } finally {
        fclose($lock);
    }
    return $url;
}
