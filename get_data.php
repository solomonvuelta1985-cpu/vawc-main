<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['rater_id']) || !isset($_SESSION['is_authenticated']) || $_SESSION['is_authenticated'] !== true) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

require_once 'db.php';

$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'raters':
        getRaters();
        break;

    case 'barangays':
        getBarangays();
        break;

    case 'assessments':
        getAssessments();
        break;

    case 'assessment':
        getAssessment();
        break;

    case 'reports':
        getReports();
        break;

    default:
        send_json_response(false, 'Invalid action');
}

function getRaters() {
    global $conn;

    $sql = "SELECT id, name, email, contact_number FROM raters ORDER BY name ASC";
    $result = $conn->query($sql);

    $raters = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $raters[] = $row;
        }
    }

    send_json_response(true, 'Raters retrieved successfully', $raters);
}

function getBarangays() {
    global $conn;

    $sql = "SELECT id, name, municipality, province FROM barangays ORDER BY name ASC";
    $result = $conn->query($sql);

    if (!$result) {
        send_json_response(false, 'Database error: ' . $conn->error);
    }

    $barangays = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $barangays[] = $row;
        }
    }

    send_json_response(true, 'Barangays retrieved successfully', $barangays);
}

function getAssessments() {
    global $conn;

    $sql = "SELECT
        a.id,
        a.assessment_date,
        a.section1_score,
        a.section2_score,
        a.section3_score,
        a.section4_score,
        a.total_score,
        a.status,
        a.remarks,
        r.name as rater_name,
        b.name as barangay_name
    FROM assessments a
    JOIN raters r ON a.rater_id = r.id
    JOIN barangays b ON a.barangay_id = b.id
    ORDER BY a.assessment_date DESC, a.id DESC";

    $result = $conn->query($sql);

    $assessments = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $assessments[] = $row;
        }
    }

    send_json_response(true, 'Assessments retrieved successfully', $assessments);
}

function getAssessment() {
    global $conn;

    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if ($id === 0) {
        send_json_response(false, 'Invalid assessment ID');
    }

    $sql = "SELECT
        a.*,
        r.name as rater_name,
        b.name as barangay_name
    FROM assessments a
    JOIN raters r ON a.rater_id = r.id
    JOIN barangays b ON a.barangay_id = b.id
    WHERE a.id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $assessment = $result->fetch_assoc();
        send_json_response(true, 'Assessment retrieved successfully', $assessment);
    } else {
        send_json_response(false, 'Assessment not found');
    }
}

function getReports() {
    global $conn;

    // Overall statistics
    $stats = [];

    // Total raters
    $sql = "SELECT COUNT(*) as total FROM raters";
    $result = $conn->query($sql);
    $stats['total_raters'] = $result->fetch_assoc()['total'];

    // Total barangays
    $sql = "SELECT COUNT(*) as total FROM barangays";
    $result = $conn->query($sql);
    $stats['total_barangays'] = $result->fetch_assoc()['total'];

    // Total assessments
    $sql = "SELECT COUNT(*) as total FROM assessments";
    $result = $conn->query($sql);
    $stats['total_assessments'] = $result->fetch_assoc()['total'];

    // Average score
    $sql = "SELECT AVG(total_score) as avg_score FROM assessments WHERE status = 'completed'";
    $result = $conn->query($sql);
    $avg = $result->fetch_assoc()['avg_score'];
    $stats['average_score'] = $avg ? number_format($avg, 1) : '0.0';

    // Stats by barangay
    $sql = "SELECT
        b.name as barangay,
        COUNT(a.id) as total,
        ROUND(AVG(a.total_score), 1) as avg_total,
        ROUND(AVG(a.section1_score), 1) as avg_section1,
        ROUND(AVG(a.section2_score), 1) as avg_section2,
        ROUND(AVG(a.section3_score), 1) as avg_section3,
        ROUND(AVG(a.section4_score), 1) as avg_section4
    FROM barangays b
    LEFT JOIN assessments a ON b.id = a.barangay_id
    WHERE a.status = 'completed'
    GROUP BY b.id, b.name
    ORDER BY avg_total DESC";

    $result = $conn->query($sql);
    $stats['by_barangay'] = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $stats['by_barangay'][] = $row;
        }
    }

    // Stats by rater
    $sql = "SELECT
        r.name as rater,
        COUNT(a.id) as total,
        ROUND(AVG(a.total_score), 1) as avg_score,
        SUM(CASE WHEN a.status = 'completed' THEN 1 ELSE 0 END) as completed,
        SUM(CASE WHEN a.status = 'in_progress' THEN 1 ELSE 0 END) as in_progress,
        SUM(CASE WHEN a.status = 'pending' THEN 1 ELSE 0 END) as pending
    FROM raters r
    LEFT JOIN assessments a ON r.id = a.rater_id
    GROUP BY r.id, r.name
    ORDER BY total DESC";

    $result = $conn->query($sql);
    $stats['by_rater'] = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $stats['by_rater'][] = $row;
        }
    }

    send_json_response(true, 'Reports retrieved successfully', $stats);
}

$conn->close();
?>
