<?php
// Clean output buffer to prevent whitespace/BOM issues
if (ob_get_level()) ob_end_clean();
ob_start();

// Include custom error logging
require_once 'error_log.php';

require_once 'config.php';

// Create database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die(json_encode([
        'success' => false,
        'message' => 'Database connection failed: ' . $conn->connect_error
    ]));
}

// Set charset to utf8mb4
$conn->set_charset("utf8mb4");

// Function to sanitize input
function sanitize_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $conn->real_escape_string($data);
}

// Function to send JSON response
function send_json_response($success, $message, $data = null) {
    // Clean any output that may have been generated
    if (ob_get_level()) ob_end_clean();

    // Prevent any caching
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Content-Type: application/json; charset=utf-8');

    $response = json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);

    // Log if JSON encoding failed
    if ($response === false) {
        error_log('JSON encoding failed: ' . json_last_error_msg());
        $response = json_encode([
            'success' => false,
            'message' => 'Internal server error'
        ]);
    }

    echo $response;
    exit;
}