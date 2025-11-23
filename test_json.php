<?php
// Simple JSON test - helps diagnose production issues
header('Content-Type: application/json; charset=utf-8');

$test_response = [
    'success' => true,
    'message' => 'JSON test successful',
    'server_info' => [
        'php_version' => phpversion(),
        'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
        'output_buffering' => ini_get('output_buffering'),
        'default_charset' => ini_get('default_charset')
    ]
];

echo json_encode($test_response);
exit;