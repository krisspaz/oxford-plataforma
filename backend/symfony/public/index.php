<?php

if ($_SERVER['REQUEST_URI'] === '/health') {
    http_response_code(200);
    echo 'OK';
    exit;
}

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

// Fix for Nginx/Apache stripping Authorization header
// Strategy 1: REDIRECT_ prefix
if (!isset($_SERVER['HTTP_AUTHORIZATION']) && isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
    $_SERVER['HTTP_AUTHORIZATION'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
}

// Strategy 2: Apache Request Headers
if (!isset($_SERVER['HTTP_AUTHORIZATION']) && function_exists('apache_request_headers')) {
    $headers = apache_request_headers();
    if (isset($headers['Authorization'])) {
        $_SERVER['HTTP_AUTHORIZATION'] = $headers['Authorization'];
    }
}

// Strategy 3: X-Auth-Token Side Channel (Bypass for aggressive stripping)
if (empty($_SERVER['HTTP_AUTHORIZATION']) && !empty($_SERVER['HTTP_X_AUTH_TOKEN'])) {
    $_SERVER['HTTP_AUTHORIZATION'] = 'Bearer ' . $_SERVER['HTTP_X_AUTH_TOKEN'];
    // Validating overwrite
    // file_put_contents('/tmp/auth_debug_2.log', "Overwrote AUTH with: " . $_SERVER['HTTP_AUTHORIZATION'] . "\n", FILE_APPEND);
}

// DEBUG: Log Headers to verify what is actually arriving
$logData = date('[Y-m-d H:i:s] ') . $_SERVER['REQUEST_URI'] . "\n";
$logData .= "AUTH: " . (isset($_SERVER['HTTP_AUTHORIZATION']) ? $_SERVER['HTTP_AUTHORIZATION'] : 'NO_AUTH') . "\n";
$logData .= "X-AUTH: " . (isset($_SERVER['HTTP_X_AUTH_TOKEN']) ? $_SERVER['HTTP_X_AUTH_TOKEN'] : 'NO_X_AUTH') . "\n";
// Log all HTTP headers for forensic analysis
foreach ($_SERVER as $key => $value) {
    if (strpos($key, 'HTTP_') === 0) {
        $logData .= "$key: $value\n";
    }
}
$logData .= "-----------------------------------\n";
file_put_contents('/tmp/auth_debug.log', $logData, FILE_APPEND);

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
