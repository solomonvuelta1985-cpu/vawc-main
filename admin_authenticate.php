<?php
session_start();
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json_response(false, 'Invalid request method');
}

// Get credentials from request
$username = isset($_POST['username']) ? sanitize_input($_POST['username']) : '';
$password = isset($_POST['password']) ? trim($_POST['password']) : '';

// Validate inputs
if (empty($username) || empty($password)) {
    send_json_response(false, 'Please provide both username and password');
}

// Check admin in database
$sql = "SELECT id, username, password, full_name, email, is_active FROM admins WHERE username = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $admin = $result->fetch_assoc();

    // Check if account is active
    if ($admin['is_active'] != 1) {
        send_json_response(false, 'This admin account has been deactivated. Please contact system administrator.');
    }

    // Verify password
    if (password_verify($password, $admin['password'])) {
        // Set session variables
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['admin_name'] = $admin['full_name'];
        $_SESSION['admin_email'] = $admin['email'];
        $_SESSION['admin_login_time'] = date('Y-m-d H:i:s');
        $_SESSION['is_admin_authenticated'] = true;

        // Update last login
        $update_sql = "UPDATE admins SET last_login = NOW() WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("i", $admin['id']);
        $update_stmt->execute();
        $update_stmt->close();

        send_json_response(true, 'Login successful', [
            'full_name' => $admin['full_name'],
            'username' => $admin['username'],
            'email' => $admin['email']
        ]);
    } else {
        send_json_response(false, 'Invalid username or password. Access denied.');
    }
} else {
    send_json_response(false, 'Invalid username or password. Access denied.');
}

$stmt->close();
$conn->close();
