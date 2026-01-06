<?php
// Standalone health check - No framework dependencies
http_response_code(200);
header('Content-Type: text/plain');
echo 'OK';
