<?php
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
if ($section1_score < 0 || $section1_score > 25 ||
    $section2_score < 0 || $section2_score > 25 ||
    $section3_score < 0 || $section3_score > 25 ||
    $section4_score < 0 || $section4_score > 25) {
    send_json_response(false, 'Each section score must be between 0 and 25');
}

// Calculate total score
$total_score = $section1_score + $section2_score + $section3_score + $section4_score;

// Update assessment
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
    "sdddddsi",
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

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        send_json_response(true, 'Assessment updated successfully!', [
            'id' => $id,
            'total_score' => $total_score
        ]);
    } else {
        send_json_response(false, 'No changes made or assessment not found');
    }
} else {
    send_json_response(false, 'Failed to update assessment: ' . $conn->error);
}

$stmt->close();
$conn->close();
?>
