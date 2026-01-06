<?php

if ($_SERVER['REQUEST_URI'] === '/health') {
    http_response_code(200);
    echo 'OK';
    exit;
}

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
