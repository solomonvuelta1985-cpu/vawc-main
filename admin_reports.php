<?php
session_start();
require_once 'db.php';

// Check if admin is authenticated (PIN 3030)
if (!isset($_SESSION['is_authenticated']) || $_SESSION['is_authenticated'] !== true || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    send_json_response(false, 'Unauthorized access. Please login as admin.');
}

$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'overview':
        getOverviewStats();
        break;
    case 'raters_report':
        getRatersReport();
        break;
    case 'barangays_report':
        getBarangaysReport();
        break;
    case 'assessments_detail':
        getAssessmentsDetail();
        break;
    case 'score_distribution':
        getScoreDistribution();
        break;
    case 'completion_matrix':
        getCompletionMatrix();
        break;
    case 'export_data':
        getExportData();
        break;
    default:
        send_json_response(false, 'Invalid action');
}

// Get overview statistics
function getOverviewStats() {
    global $conn;

    $stats = [];

    // Total raters (excluding system administrator)
    $result = $conn->query("SELECT COUNT(*) as total FROM raters WHERE position != 'SYSTEM ADMINISTRATOR'");
    $stats['total_raters'] = $result->fetch_assoc()['total'];

    // Total barangays
    $result = $conn->query("SELECT COUNT(*) as total FROM barangays");
    $stats['total_barangays'] = $result->fetch_assoc()['total'];

    // Total assessments
    $result = $conn->query("SELECT COUNT(*) as total FROM assessments");
    $stats['total_assessments'] = $result->fetch_assoc()['total'];

    // Assessments by status
    $result = $conn->query("
        SELECT
            status,
            COUNT(*) as count
        FROM assessments
        GROUP BY status
    ");
    $stats['by_status'] = [];
    while ($row = $result->fetch_assoc()) {
        $stats['by_status'][$row['status']] = $row['count'];
    }

    // Average scores
    $result = $conn->query("
        SELECT
            AVG(section1_score) as avg_section1,
            AVG(section2_score) as avg_section2,
            AVG(section3_score) as avg_section3,
            AVG(section4_score) as avg_section4,
            AVG(total_score) as avg_total,
            MIN(total_score) as min_score,
            MAX(total_score) as max_score
        FROM assessments
    ");
    $stats['scores'] = $result->fetch_assoc();

    // Completion rate
    $total_possible = $stats['total_raters'] * $stats['total_barangays'];
    $completion_rate = $total_possible > 0 ? ($stats['total_assessments'] / $total_possible) * 100 : 0;
    $stats['completion_rate'] = round($completion_rate, 2);
    $stats['total_possible_assessments'] = $total_possible;
    $stats['remaining_assessments'] = $total_possible - $stats['total_assessments'];

    // Recent assessments (last 10)
    $result = $conn->query("
        SELECT
            a.id,
            a.assessment_date,
            a.total_score,
            a.status,
            r.name as rater_name,
            b.name as barangay_name,
            a.created_at
        FROM assessments a
        JOIN raters r ON a.rater_id = r.id
        JOIN barangays b ON a.barangay_id = b.id
        ORDER BY a.created_at DESC
        LIMIT 10
    ");
    $stats['recent_assessments'] = [];
    while ($row = $result->fetch_assoc()) {
        $stats['recent_assessments'][] = $row;
    }

    send_json_response(true, 'Overview statistics retrieved', $stats);
}

// Get detailed raters report
function getRatersReport() {
    global $conn;

    $sql = "
        SELECT
            r.id,
            r.name,
            r.email,
            r.contact_number,
            r.position,
            COUNT(a.id) as total_assessments,
            SUM(CASE WHEN a.status = 'completed' THEN 1 ELSE 0 END) as completed,
            SUM(CASE WHEN a.status = 'in_progress' THEN 1 ELSE 0 END) as in_progress,
            SUM(CASE WHEN a.status = 'pending' THEN 1 ELSE 0 END) as pending,
            AVG(a.total_score) as avg_score,
            MIN(a.total_score) as min_score,
            MAX(a.total_score) as max_score,
            AVG(a.section1_score) as avg_section1,
            AVG(a.section2_score) as avg_section2,
            AVG(a.section3_score) as avg_section3,
            AVG(a.section4_score) as avg_section4
        FROM raters r
        LEFT JOIN assessments a ON r.id = a.rater_id
        WHERE r.position != 'SYSTEM ADMINISTRATOR'
        GROUP BY r.id, r.name, r.email, r.contact_number, r.position
        ORDER BY r.name ASC
    ";

    $result = $conn->query($sql);
    $raters = [];

    while ($row = $result->fetch_assoc()) {
        $raters[] = $row;
    }

    send_json_response(true, 'Raters report retrieved', $raters);
}

// Get detailed barangays report
function getBarangaysReport() {
    global $conn;

    $sql = "
        SELECT
            b.id,
            b.name,
            b.municipality,
            b.province,
            COUNT(a.id) as total_assessments,
            SUM(CASE WHEN a.status = 'completed' THEN 1 ELSE 0 END) as completed,
            SUM(CASE WHEN a.status = 'in_progress' THEN 1 ELSE 0 END) as in_progress,
            SUM(CASE WHEN a.status = 'pending' THEN 1 ELSE 0 END) as pending,
            AVG(a.total_score) as avg_score,
            MIN(a.total_score) as min_score,
            MAX(a.total_score) as max_score,
            AVG(a.section1_score) as avg_section1,
            AVG(a.section2_score) as avg_section2,
            AVG(a.section3_score) as avg_section3,
            AVG(a.section4_score) as avg_section4
        FROM barangays b
        LEFT JOIN assessments a ON b.id = a.barangay_id
        GROUP BY b.id, b.name, b.municipality, b.province
        ORDER BY b.name ASC
    ";

    $result = $conn->query($sql);
    $barangays = [];

    while ($row = $result->fetch_assoc()) {
        $barangays[] = $row;
    }

    send_json_response(true, 'Barangays report retrieved', $barangays);
}

// Get detailed assessments with all information
function getAssessmentsDetail() {
    global $conn;

    $sql = "
        SELECT
            a.id,
            a.assessment_date,
            a.section1_score,
            a.section2_score,
            a.section3_score,
            a.section4_score,
            a.total_score,
            a.status,
            a.remarks,
            a.created_at,
            a.updated_at,
            r.id as rater_id,
            r.name as rater_name,
            r.position as rater_position,
            b.id as barangay_id,
            b.name as barangay_name,
            b.municipality,
            b.province
        FROM assessments a
        JOIN raters r ON a.rater_id = r.id
        JOIN barangays b ON a.barangay_id = b.id
        ORDER BY a.created_at DESC
    ";

    $result = $conn->query($sql);
    $assessments = [];

    while ($row = $result->fetch_assoc()) {
        $assessments[] = $row;
    }

    send_json_response(true, 'Detailed assessments retrieved', $assessments);
}

// Get score distribution
function getScoreDistribution() {
    global $conn;

    $distribution = [];

    // Score ranges
    $ranges = [
        ['min' => 0, 'max' => 25, 'label' => '0-25 (Poor)'],
        ['min' => 26, 'max' => 50, 'label' => '26-50 (Fair)'],
        ['min' => 51, 'max' => 75, 'label' => '51-75 (Good)'],
        ['min' => 76, 'max' => 100, 'label' => '76-100 (Excellent)']
    ];

    foreach ($ranges as $range) {
        $sql = "
            SELECT COUNT(*) as count
            FROM assessments
            WHERE total_score >= {$range['min']} AND total_score <= {$range['max']}
        ";
        $result = $conn->query($sql);
        $count = $result->fetch_assoc()['count'];

        $distribution[] = [
            'range' => $range['label'],
            'min' => $range['min'],
            'max' => $range['max'],
            'count' => $count
        ];
    }

    send_json_response(true, 'Score distribution retrieved', $distribution);
}

// Get completion matrix (raters vs barangays)
function getCompletionMatrix() {
    global $conn;

    // Get all raters (excluding system administrator)
    $raters_result = $conn->query("SELECT id, name FROM raters WHERE position != 'SYSTEM ADMINISTRATOR' ORDER BY name");
    $raters = [];
    while ($row = $raters_result->fetch_assoc()) {
        $raters[] = $row;
    }

    // Get all barangays
    $barangays_result = $conn->query("SELECT id, name FROM barangays ORDER BY name");
    $barangays = [];
    while ($row = $barangays_result->fetch_assoc()) {
        $barangays[] = $row;
    }

    // Get all assessments
    $assessments_result = $conn->query("
        SELECT rater_id, barangay_id, status, total_score
        FROM assessments
    ");
    $assessments_map = [];
    while ($row = $assessments_result->fetch_assoc()) {
        $key = $row['rater_id'] . '_' . $row['barangay_id'];
        $assessments_map[$key] = [
            'status' => $row['status'],
            'score' => $row['total_score']
        ];
    }

    // Build matrix
    $matrix = [];
    foreach ($raters as $rater) {
        $row = [
            'rater_id' => $rater['id'],
            'rater_name' => $rater['name'],
            'barangays' => []
        ];

        foreach ($barangays as $barangay) {
            $key = $rater['id'] . '_' . $barangay['id'];
            $row['barangays'][] = [
                'barangay_id' => $barangay['id'],
                'barangay_name' => $barangay['name'],
                'status' => isset($assessments_map[$key]) ? $assessments_map[$key]['status'] : 'not_started',
                'score' => isset($assessments_map[$key]) ? $assessments_map[$key]['score'] : null
            ];
        }

        $matrix[] = $row;
    }

    send_json_response(true, 'Completion matrix retrieved', [
        'raters' => $raters,
        'barangays' => $barangays,
        'matrix' => $matrix
    ]);
}

// Get all data for export
function getExportData() {
    global $conn;

    $sql = "
        SELECT
            r.name as rater_name,
            r.position as rater_position,
            b.name as barangay_name,
            b.municipality,
            b.province,
            a.assessment_date,
            a.section1_score,
            a.section2_score,
            a.section3_score,
            a.section4_score,
            a.total_score,
            a.status,
            a.remarks,
            a.created_at,
            a.updated_at
        FROM assessments a
        JOIN raters r ON a.rater_id = r.id
        JOIN barangays b ON a.barangay_id = b.id
        ORDER BY b.name, r.name
    ";

    $result = $conn->query($sql);
    $data = [];

    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    send_json_response(true, 'Export data retrieved', $data);
}

$conn->close();
?>
