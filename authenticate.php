<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json_response(false, 'Invalid request method');
}

// Get PIN from request
$pin = isset($_POST['pin']) ? sanitize_input($_POST['pin']) : '';

// Validate PIN
if (empty($pin) || strlen($pin) !== 4 || !ctype_digit($pin)) {
    send_json_response(false, 'Invalid PIN format');
}

// Check PIN in database
$sql = "SELECT id, name, email, pin FROM raters WHERE pin = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $pin);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $rater = $result->fetch_assoc();

    // Set session variables
    $_SESSION['rater_id'] = $rater['id'];
    $_SESSION['rater_name'] = $rater['name'];
    $_SESSION['rater_email'] = $rater['email'];
    $_SESSION['login_time'] = date('Y-m-d H:i:s');
    $_SESSION['is_authenticated'] = true;

    send_json_response(true, 'Login successful', [
        'name' => $rater['name'],
        'email' => $rater['email']
    ]);
} else {
    send_json_response(false, 'Invalid PIN. Access denied.');
}

$stmt->close();
$conn->close();
?>
