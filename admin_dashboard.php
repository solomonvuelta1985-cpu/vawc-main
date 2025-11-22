<?php
session_start();

// Check if authenticated and is admin (PIN 3030)
if (!isset($_SESSION['is_authenticated']) || $_SESSION['is_authenticated'] !== true || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('Location: login.php');
    exit;
}

$admin_name = isset($_SESSION['rater_name']) ? $_SESSION['rater_name'] : 'Administrator';
$admin_username = 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - VAW Data Consolidator</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <style>
        :root {
            --spacing-xs: clamp(0.25rem, 0.5vw, 0.5rem);
            --spacing-sm: clamp(0.5rem, 1vw, 1rem);
            --spacing-md: clamp(1rem, 2vw, 1.5rem);
            --spacing-lg: clamp(1.5rem, 3vw, 2rem);
            --spacing-xl: clamp(2rem, 4vw, 3rem);
        }

        body {
            background-color: #f8f9fa;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            font-size: clamp(0.875rem, 1.5vw, 1rem);
        }

        .navbar {
            background-color: #fff;
            border-bottom: 1px solid #dee2e6;
            box-shadow: 0 2px 4px rgba(0,0,0,0.04);
            padding: clamp(0.75rem, 2vw, 1.25rem) clamp(1rem, 3vw, 1.5rem);
        }

        .navbar-brand {
            font-weight: 700;
            color: #212529;
            font-size: clamp(1rem, 2.5vw, 1.25rem);
        }

        .navbar-brand i {
            font-size: clamp(1.25rem, 3vw, 1.5rem);
        }

        .badge-admin {
            background-color: #0d6efd;
            font-size: clamp(0.65rem, 1.5vw, 0.75rem);
            padding: clamp(0.25rem, 0.5vw, 0.35rem) clamp(0.5rem, 1vw, 0.65rem);
        }

        .main-content {
            padding: var(--spacing-lg) 0;
        }

        .nav-tabs {
            border-bottom: 2px solid #dee2e6;
            margin-bottom: var(--spacing-lg);
            gap: clamp(0.25rem, 0.5vw, 0.5rem);
            flex-wrap: wrap;
        }

        .nav-tabs .nav-link {
            border: none;
            color: #6c757d;
            font-weight: 500;
            padding: clamp(0.5rem, 1.5vw, 0.75rem) clamp(0.75rem, 2vw, 1.5rem);
            margin-bottom: -2px;
            font-size: clamp(0.75rem, 1.8vw, 0.875rem);
            white-space: nowrap;
        }

        .nav-tabs .nav-link i {
            font-size: clamp(0.875rem, 2vw, 1rem);
        }

        .nav-tabs .nav-link:hover {
            color: #0d6efd;
            border-color: transparent;
        }

        .nav-tabs .nav-link.active {
            color: #0d6efd;
            border-bottom: 2px solid #0d6efd;
            background-color: transparent;
        }

        .stat-card {
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: clamp(0.375rem, 1vw, 0.5rem);
            padding: var(--spacing-md);
            margin-bottom: var(--spacing-md);
            transition: all 0.2s;
        }

        .stat-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            transform: translateY(-2px);
        }

        .stat-card .stat-icon {
            font-size: clamp(1.5rem, 4vw, 2rem);
            color: #0d6efd;
        }

        .stat-card .stat-value {
            font-size: clamp(1.5rem, 4vw, 2rem);
            font-weight: 700;
            color: #212529;
            margin: clamp(0.25rem, 1vw, 0.5rem) 0;
        }

        .stat-card .stat-label {
            color: #6c757d;
            font-size: clamp(0.7rem, 1.5vw, 0.875rem);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-card .stat-detail {
            color: #adb5bd;
            font-size: clamp(0.65rem, 1.4vw, 0.813rem);
            margin-top: var(--spacing-xs);
        }

        .card {
            border: 1px solid #dee2e6;
            border-radius: clamp(0.375rem, 1vw, 0.5rem);
            margin-bottom: var(--spacing-md);
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }

        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #dee2e6;
            font-weight: 600;
            color: #212529;
            padding: clamp(0.75rem, 2vw, 1rem) clamp(1rem, 2.5vw, 1.25rem);
            font-size: clamp(0.875rem, 2vw, 1rem);
        }

        .card-header i {
            font-size: clamp(0.875rem, 2vw, 1rem);
        }

        .card-body {
            padding: clamp(1rem, 2.5vw, 1.25rem);
        }

        .table {
            margin-bottom: 0;
            font-size: clamp(0.75rem, 1.8vw, 0.875rem);
        }

        .table thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            color: #495057;
            font-weight: 600;
            font-size: clamp(0.65rem, 1.5vw, 0.813rem);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: clamp(0.5rem, 1.5vw, 0.75rem);
            white-space: nowrap;
        }

        .table tbody td {
            padding: clamp(0.5rem, 1.5vw, 0.75rem);
            vertical-align: middle;
            font-size: clamp(0.75rem, 1.8vw, 0.875rem);
        }

        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }

        .badge {
            font-weight: 500;
            padding: clamp(0.25rem, 0.5vw, 0.35rem) clamp(0.5rem, 1vw, 0.65rem);
            font-size: clamp(0.65rem, 1.5vw, 0.75rem);
        }

        .badge-score {
            font-weight: 600;
            font-size: clamp(0.75rem, 1.8vw, 0.875rem);
            padding: clamp(0.3rem, 0.8vw, 0.4rem) clamp(0.6rem, 1.2vw, 0.75rem);
        }

        .chart-container {
            position: relative;
            height: clamp(250px, 50vw, 350px);
            margin-bottom: var(--spacing-sm);
        }

        .btn {
            font-size: clamp(0.75rem, 1.8vw, 0.875rem);
            padding: clamp(0.375rem, 1vw, 0.5rem) clamp(0.75rem, 2vw, 1rem);
        }

        .btn-group-actions {
            margin-bottom: var(--spacing-md);
            display: flex;
            gap: var(--spacing-sm);
            flex-wrap: wrap;
        }

        .loading-container {
            text-align: center;
            padding: var(--spacing-xl);
            color: #6c757d;
        }

        .loading-container p {
            font-size: clamp(0.875rem, 2vw, 1rem);
        }

        .spinner-border {
            width: clamp(2rem, 5vw, 3rem);
            height: clamp(2rem, 5vw, 3rem);
        }

        .no-data {
            text-align: center;
            padding: var(--spacing-xl);
            color: #adb5bd;
            font-size: clamp(0.875rem, 2vw, 1rem);
        }

        .matrix-table {
            font-size: clamp(0.65rem, 1.5vw, 0.75rem);
        }

        .matrix-cell {
            text-align: center;
            padding: clamp(0.375rem, 1vw, 0.5rem) clamp(0.2rem, 0.5vw, 0.25rem) !important;
            font-weight: 600;
        }

        .matrix-cell.completed {
            background-color: #198754;
            color: white;
        }

        .matrix-cell.in-progress {
            background-color: #ffc107;
            color: #000;
        }

        .matrix-cell.pending {
            background-color: #dc3545;
            color: white;
        }

        .matrix-cell.not-started {
            background-color: #e9ecef;
            color: #adb5bd;
        }

        .rank-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: clamp(1.75rem, 4vw, 2rem);
            height: clamp(1.75rem, 4vw, 2rem);
            border-radius: 50%;
            font-weight: 700;
            font-size: clamp(0.75rem, 1.8vw, 0.875rem);
        }

        .rank-1 { background-color: #ffd700; color: #000; }
        .rank-2 { background-color: #c0c0c0; color: #000; }
        .rank-3 { background-color: #cd7f32; color: #fff; }

        .alert {
            font-size: clamp(0.75rem, 1.8vw, 0.875rem);
            padding: var(--spacing-sm) var(--spacing-md);
        }

        /* Ensure container responsiveness */
        .container-xl {
            padding-left: clamp(0.75rem, 2vw, 1.5rem);
            padding-right: clamp(0.75rem, 2vw, 1.5rem);
        }

        /* Responsive table wrapper */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* User info in navbar */
        .navbar .fw-semibold {
            font-size: clamp(0.813rem, 1.8vw, 0.875rem);
        }

        .navbar small {
            font-size: clamp(0.7rem, 1.5vw, 0.75rem);
        }

        @media print {
            .navbar, .nav-tabs, .btn-group-actions {
                display: none !important;
            }

            body {
                background: white;
            }

            .stat-card, .card {
                break-inside: avoid;
                box-shadow: none;
            }
        }

        /* Touch-friendly spacing on mobile */
        @media (max-width: 576px) {
            .nav-tabs {
                overflow-x: auto;
                overflow-y: hidden;
                -webkit-overflow-scrolling: touch;
                white-space: nowrap;
                flex-wrap: nowrap;
            }

            .table thead th {
                position: sticky;
                top: 0;
                z-index: 2;
            }
        }

        /* Smooth transitions for all devices */
        * {
            transition: font-size 0.2s ease, padding 0.2s ease, margin 0.2s ease;
        }

        button, a, .stat-card, .card {
            transition: all 0.2s ease;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="bi bi-shield-lock-fill text-primary me-2"></i>
                VAW Data Consolidator
                <span class="badge badge-admin ms-2">ADMIN</span>
            </a>
            <div class="d-flex align-items-center">
                <div class="me-3 text-end d-none d-md-block">
                    <div class="fw-semibold"><?php echo htmlspecialchars($admin_name); ?></div>
                    <small class="text-muted">Administrator</small>
                </div>
                <button class="btn btn-outline-danger btn-sm" onclick="logout()">
                    <i class="bi bi-box-arrow-right me-1"></i> Logout
                </button>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container-fluid main-content">
        <div class="container-xl">
            <!-- Tabs -->
            <ul class="nav nav-tabs" id="adminTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">
                        <i class="bi bi-speedometer2 me-1"></i> Overview
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="raters-tab" data-bs-toggle="tab" data-bs-target="#raters" type="button" role="tab">
                        <i class="bi bi-people-fill me-1"></i> Raters
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="barangays-tab" data-bs-toggle="tab" data-bs-target="#barangays" type="button" role="tab">
                        <i class="bi bi-geo-alt-fill me-1"></i> Barangays
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="rank-tab" data-bs-toggle="tab" data-bs-target="#rank" type="button" role="tab">
                        <i class="bi bi-trophy-fill me-1"></i> Rankings
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="matrix-tab" data-bs-toggle="tab" data-bs-target="#matrix" type="button" role="tab">
                        <i class="bi bi-grid-3x3-gap-fill me-1"></i> Matrix
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="assessments-tab" data-bs-toggle="tab" data-bs-target="#assessments" type="button" role="tab">
                        <i class="bi bi-file-text-fill me-1"></i> Assessments
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="adminTabsContent">
                <!-- Overview Tab -->
                <div class="tab-pane fade show active" id="overview" role="tabpanel">
                    <div id="overview-stats" class="row">
                        <div class="col-12 loading-container">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-3">Loading statistics...</p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <i class="bi bi-pie-chart-fill me-2"></i>Score Distribution
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="scoreDistributionChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 mb-4">
                            <div class="card">
                                <div class="card-header">
                                    <i class="bi bi-bar-chart-fill me-2"></i>Average Scores by Section
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="sectionScoresChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="recent-assessments-container"></div>
                </div>

                <!-- Raters Tab -->
                <div class="tab-pane fade" id="raters" role="tabpanel">
                    <div class="btn-group-actions">
                        <button class="btn btn-success btn-sm me-2" onclick="exportRatersReport()">
                            <i class="bi bi-download me-1"></i> Export to CSV
                        </button>
                        <button class="btn btn-primary btn-sm" onclick="printReport()">
                            <i class="bi bi-printer-fill me-1"></i> Print Report
                        </button>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <i class="bi bi-people-fill me-2"></i>Raters Detailed Report
                        </div>
                        <div class="card-body">
                            <div id="raters-table-container">
                                <div class="loading-container">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-3">Loading raters data...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Barangays Tab -->
                <div class="tab-pane fade" id="barangays" role="tabpanel">
                    <div class="btn-group-actions">
                        <button class="btn btn-success btn-sm me-2" onclick="exportBarangaysReport()">
                            <i class="bi bi-download me-1"></i> Export to CSV
                        </button>
                        <button class="btn btn-primary btn-sm" onclick="printReport()">
                            <i class="bi bi-printer-fill me-1"></i> Print Report
                        </button>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <i class="bi bi-geo-alt-fill me-2"></i>Barangays Detailed Report
                        </div>
                        <div class="card-body">
                            <div id="barangays-table-container">
                                <div class="loading-container">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-3">Loading barangays data...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rankings Tab -->
                <div class="tab-pane fade" id="rank" role="tabpanel">
                    <div class="btn-group-actions">
                        <button class="btn btn-success btn-sm me-2" onclick="exportRankings()">
                            <i class="bi bi-download me-1"></i> Export Rankings
                        </button>
                        <button class="btn btn-primary btn-sm" onclick="printReport()">
                            <i class="bi bi-printer-fill me-1"></i> Print Report
                        </button>
                    </div>

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <i class="bi bi-trophy-fill me-2"></i>Top Barangays by Average Score
                                </div>
                                <div class="card-body">
                                    <div id="barangay-rankings-container">
                                        <div class="loading-container">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <p class="mt-3">Loading rankings...</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <i class="bi bi-star-fill me-2"></i>Top Raters by Performance
                                </div>
                                <div class="card-body">
                                    <div id="rater-rankings-container">
                                        <div class="loading-container">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                            <p class="mt-3">Loading rankings...</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Completion Matrix Tab -->
                <div class="tab-pane fade" id="matrix" role="tabpanel">
                    <div class="btn-group-actions">
                        <button class="btn btn-success btn-sm me-2" onclick="exportMatrix()">
                            <i class="bi bi-download me-1"></i> Export Matrix
                        </button>
                        <button class="btn btn-primary btn-sm" onclick="printReport()">
                            <i class="bi bi-printer-fill me-1"></i> Print Matrix
                        </button>
                    </div>

                    <div class="alert alert-info">
                        <strong>Legend:</strong>
                        <span class="badge bg-success ms-2">Completed</span>
                        <span class="badge bg-warning text-dark ms-2">In Progress</span>
                        <span class="badge bg-danger ms-2">Pending</span>
                        <span class="badge bg-secondary ms-2">Not Started</span>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <i class="bi bi-grid-3x3-gap-fill me-2"></i>Completion Matrix (Raters × Barangays)
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <div id="matrix-container">
                                    <div class="loading-container">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="mt-3">Loading completion matrix...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Assessments Tab -->
                <div class="tab-pane fade" id="assessments" role="tabpanel">
                    <div class="btn-group-actions">
                        <button class="btn btn-success btn-sm me-2" onclick="exportAllAssessments()">
                            <i class="bi bi-download me-1"></i> Export All Data
                        </button>
                        <button class="btn btn-primary btn-sm" onclick="printReport()">
                            <i class="bi bi-printer-fill me-1"></i> Print Report
                        </button>
                    </div>
                    <div class="card">
                        <div class="card-header">
                            <i class="bi bi-file-text-fill me-2"></i>All Assessments (Detailed View)
                        </div>
                        <div class="card-body">
                            <div id="assessments-table-container">
                                <div class="loading-container">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-3">Loading assessments data...</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="admin_dashboard.js"></script>
</body>
</html>
