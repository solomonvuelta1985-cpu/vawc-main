<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json_response(false, 'Invalid request method');
}

// CSRF token validation
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    send_json_response(false, 'Invalid security token. Please refresh the page and try again.');
}

// Initialize login attempts if not set
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['last_attempt_time'] = time();
}

// Reset attempts after 15 minutes
if (isset($_SESSION['last_attempt_time']) && (time() - $_SESSION['last_attempt_time']) > 900) {
    $_SESSION['login_attempts'] = 0;
}

// Check if account is locked (5 failed attempts)
if ($_SESSION['login_attempts'] >= 5) {
    $lockout_time_remaining = 900 - (time() - $_SESSION['last_attempt_time']);
    if ($lockout_time_remaining > 0) {
        send_json_response(false, 'Account locked for security. Please wait ' . ceil($lockout_time_remaining / 60) . ' minutes before trying again.', ['locked' => true]);
    } else {
        // Reset attempts after lockout period
        $_SESSION['login_attempts'] = 0;
    }
}

// Get PIN from request
$pin = isset($_POST['pin']) ? sanitize_input($_POST['pin']) : '';

// Validate PIN format
if (empty($pin) || strlen($pin) !== 4 || !ctype_digit($pin)) {
    // Increment failed attempts
    $_SESSION['login_attempts']++;
    $_SESSION['last_attempt_time'] = time();

    $remaining_attempts = 5 - $_SESSION['login_attempts'];
    if ($remaining_attempts > 0) {
        send_json_response(false, 'Invalid PIN format. ' . $remaining_attempts . ' attempt(s) remaining.');
    } else {
        send_json_response(false, 'Account locked for 15 minutes due to too many failed attempts.', ['locked' => true]);
    }
}

// Rate limiting: Add small delay to prevent brute force
usleep(500000); // 0.5 second delay

// Check PIN in database using prepared statement
$sql = "SELECT id, name, email, pin FROM raters WHERE pin = ? LIMIT 1";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    send_json_response(false, 'Database error. Please try again later.');
}

$stmt->bind_param("s", $pin);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $rater = $result->fetch_assoc();

    // Successful login - Reset login attempts
    $_SESSION['login_attempts'] = 0;

    // Regenerate session ID to prevent session fixation
    session_regenerate_id(true);

    // Set session variables
    $_SESSION['rater_id'] = $rater['id'];
    $_SESSION['rater_name'] = $rater['name'];
    $_SESSION['rater_email'] = $rater['email'];
    $_SESSION['login_time'] = date('Y-m-d H:i:s');
    $_SESSION['is_authenticated'] = true;
    $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR'];
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];

    // Check if this is admin (PIN 3030)
    $is_admin = ($pin === '3030');
    $_SESSION['is_admin'] = $is_admin;

    // Log successful login (optional - you can add logging to database)
    error_log("Successful login: " . $rater['name'] . " from IP: " . $_SERVER['REMOTE_ADDR']);

    send_json_response(true, 'Login successful', [
        'name' => $rater['name'],
        'email' => $rater['email'],
        'is_admin' => $is_admin
    ]);
} else {
    // Failed login - Increment attempts
    $_SESSION['login_attempts']++;
    $_SESSION['last_attempt_time'] = time();

    // Log failed login attempt
    error_log("Failed login attempt with PIN: " . $pin . " from IP: " . $_SERVER['REMOTE_ADDR']);

    $remaining_attempts = 5 - $_SESSION['login_attempts'];
    if ($remaining_attempts > 0) {
        send_json_response(false, 'Invalid PIN. Access denied. ' . $remaining_attempts . ' attempt(s) remaining.');
    } else {
        send_json_response(false, 'Account locked for 15 minutes due to too many failed attempts.', ['locked' => true]);
    }
}

$stmt->close();
$conn->close();
?>
