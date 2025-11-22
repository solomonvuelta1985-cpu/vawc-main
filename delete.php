<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    send_json_response(false, 'Invalid request method');
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id === 0) {
    send_json_response(false, 'Invalid assessment ID');
}

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

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        send_json_response(true, 'Assessment deleted successfully!');
    } else {
        send_json_response(false, 'Failed to delete assessment');
    }
} else {
    send_json_response(false, 'Database error: ' . $conn->error);
}

$stmt->close();
$conn->close();
?>
