<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['rater_id']) || !isset($_SESSION['is_authenticated']) || $_SESSION['is_authenticated'] !== true) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access. Please login.']);
    exit;
}

require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    send_json_response(false, 'Invalid request method');
}

// Get and sanitize input
$id = intval($_POST['id']);
$assessment_date = sanitize_input($_POST['assessment_date']);
$section1_score = floatval($_POST['section1_score']);
$section2_score = floatval($_POST['section2_score']);
$section3_score = floatval($_POST['section3_score']);
$section4_score = floatval($_POST['section4_score']);
$status = sanitize_input($_POST['status']);
$remarks = sanitize_input($_POST['remarks']);

// Validate required fields
if (empty($id) || empty($assessment_date)) {
    send_json_response(false, 'Missing required fields');
}

// Validate scores
if ($section1_score < 0 || $section1_score > 20 ||
    $section2_score < 0 || $section2_score > 20 ||
    $section3_score < 0 || $section3_score > 20 ||
    $section4_score < 0 || $section4_score > 40) {
    send_json_response(false, 'Section scores must be valid: Sections 1-3 (0-20), Section 4 (0-40)');
}

// Calculate total score
$total_score = $section1_score + $section2_score + $section3_score + $section4_score;

// Check if user is admin
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true;

if ($is_admin) {
    // Admin can update any assessment
    $sql = "UPDATE assessments SET
        assessment_date = ?,
        section1_score = ?,
        section2_score = ?,
        section3_score = ?,
        section4_score = ?,
        total_score = ?,
        status = ?,
        remarks = ?
        WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sdddddssi",
        $assessment_date,
        $section1_score,
        $section2_score,
        $section3_score,
        $section4_score,
        $total_score,
        $status,
        $remarks,
        $id
    );
} else {
    // Regular raters can only update their own assessments
    $rater_id = $_SESSION['rater_id'];

    $sql = "UPDATE assessments SET
        assessment_date = ?,
        section1_score = ?,
        section2_score = ?,
        section3_score = ?,
        section4_score = ?,
        total_score = ?,
        status = ?,
        remarks = ?
        WHERE id = ? AND rater_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sdddddssii",
        $assessment_date,
        $section1_score,
        $section2_score,
        $section3_score,
        $section4_score,
        $total_score,
        $status,
        $remarks,
        $id,
        $rater_id
    );
}

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        send_json_response(true, 'Assessment updated successfully!', [
            'id' => $id,
            'total_score' => $total_score
        ]);
    } else {
        send_json_response(false, 'No changes made, assessment not found, or access denied');
    }
} else {
    send_json_response(false, 'Failed to update assessment: ' . $conn->error);
}

$stmt->close();
$conn->close();
?>
