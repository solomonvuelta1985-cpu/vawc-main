<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['rater_id']) || !isset($_SESSION['is_authenticated']) || $_SESSION['is_authenticated'] !== true) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access. Please login.']);
    exit;
}

require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    send_json_response(false, 'Invalid request method');
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id === 0) {
    send_json_response(false, 'Invalid assessment ID');
}

// Check if user is admin
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;

if ($is_admin) {
    // Admin can delete any assessment
    // Check if assessment exists
    $check_sql = "SELECT id FROM assessments WHERE id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("i", $id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows === 0) {
        send_json_response(false, 'Assessment not found');
    }

    // Delete assessment
    $sql = "DELETE FROM assessments WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
} else {
    // Regular raters can only delete their own assessments
    $rater_id = $_SESSION['rater_id'];

    // Check if assessment exists and belongs to this rater
    $check_sql = "SELECT id FROM assessments WHERE id = ? AND rater_id = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $id, $rater_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows === 0) {
        send_json_response(false, 'Assessment not found or access denied');
    }

    // Delete assessment
    $sql = "DELETE FROM assessments WHERE id = ? AND rater_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id, $rater_id);
}

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        send_json_response(true, 'Assessment deleted successfully!');
    } else {
        send_json_response(false, 'Failed to delete assessment or access denied');
    }
} else {
    send_json_response(false, 'Database error: ' . $conn->error);
}

$stmt->close();
$conn->close();
