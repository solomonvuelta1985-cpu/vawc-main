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

// Use session rater_id (not from POST - security measure)
$rater_id = $_SESSION['rater_id'];

// Get and sanitize input
$barangay_id = sanitize_input($_POST['barangay_id']);
$assessment_date = sanitize_input($_POST['assessment_date']);
$section1_score = floatval($_POST['section1_score']);
$section2_score = floatval($_POST['section2_score']);
$section3_score = floatval($_POST['section3_score']);
$section4_score = floatval($_POST['section4_score']);
$status = sanitize_input($_POST['status']);
$remarks = sanitize_input($_POST['remarks']);

// Validate required fields
if (empty($barangay_id) || empty($assessment_date)) {
    send_json_response(false, 'Please fill in all required fields');
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

// Check if assessment already exists for this rater and barangay
$check_sql = "SELECT id FROM assessments WHERE rater_id = ? AND barangay_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("ii", $rater_id, $barangay_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    send_json_response(false, 'You have already created an assessment for this barangay. Please update the existing assessment instead.');
}

// Insert assessment
$sql = "INSERT INTO assessments (
    rater_id,
    barangay_id,
    assessment_date,
    section1_score,
    section2_score,
    section3_score,
    section4_score,
    total_score,
    status,
    remarks
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "iisdddddss",
    $rater_id,
    $barangay_id,
    $assessment_date,
    $section1_score,
    $section2_score,
    $section3_score,
    $section4_score,
    $total_score,
    $status,
    $remarks
);

if ($stmt->execute()) {
    send_json_response(true, 'Assessment added successfully!', [
        'id' => $conn->insert_id,
        'total_score' => $total_score
    ]);
} else {
    send_json_response(false, 'Failed to add assessment: ' . $conn->error);
}

$stmt->close();
$conn->close();
?>
