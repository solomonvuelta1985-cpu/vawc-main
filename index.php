<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['rater_id']) || !isset($_SESSION['is_authenticated']) || $_SESSION['is_authenticated'] !== true) {
    header('Location: login.php');
    exit;
}

require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - <?php echo APP_MUNICIPALITY; ?></title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <!-- External CSS -->
    <link rel="stylesheet" href="style.css">

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        /* Navbar Styles */
        .custom-navbar {
            background: linear-gradient(135deg, #1e3a5f 0%, #2c4f7c 100%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            padding: 0.75rem 1.5rem;
        }

        .navbar-brand {
            font-weight: 700;
            color: #ffffff !important;
            font-size: clamp(1rem, 2.5vw, 1.25rem);
        }

        .navbar-brand i {
            font-size: clamp(1.25rem, 3vw, 1.5rem);
            color: #ffffff;
        }

        .avatar-circle {
            width: clamp(35px, 8vw, 40px);
            height: clamp(35px, 8vw, 40px);
            border-radius: 50%;
            background: linear-gradient(135deg, #1a73e8 0%, #0d47a1 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: clamp(1rem, 2.5vw, 1.25rem);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .avatar-circle:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(26, 115, 232, 0.3);
        }

        .dropdown-toggle::after {
            display: none;
        }

        .dropdown-menu {
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            border: 1px solid #dee2e6;
            padding: 0.5rem;
        }

        .dropdown-item {
            border-radius: 6px;
            padding: 0.5rem 1rem;
            font-size: clamp(0.875rem, 2vw, 0.9375rem);
        }

        .dropdown-item:hover {
            background-color: #f8f9fa;
        }

        .dropdown-divider {
            margin: 0.5rem 0;
        }

        .user-info-dropdown {
            padding: 0.75rem 1rem;
            background-color: #f8f9fa;
            border-radius: 6px;
            margin-bottom: 0.5rem;
        }

        .user-info-dropdown .user-name {
            font-weight: 600;
            color: #212529;
            font-size: clamp(0.875rem, 2vw, 0.9375rem);
            margin-bottom: 0.25rem;
        }

        .user-info-dropdown .user-role {
            font-size: clamp(0.75rem, 1.8vw, 0.8125rem);
            color: #6c757d;
        }

        /* Adjust container to account for fixed navbar */
        body {
            padding-top: 60px;
        }

        .custom-navbar.fixed-top {
            z-index: 1030;
        }

        /* Network Speed Toast Notification */
        .network-toast {
            position: fixed;
            top: 80px;
            right: 20px;
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
            color: white;
            padding: clamp(0.875rem, 2vw, 1rem) clamp(1rem, 2.5vw, 1.25rem);
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            z-index: 1040;
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 300px;
            max-width: 400px;
            animation: slideIn 0.3s ease-out;
            font-size: clamp(0.875rem, 2vw, 0.9375rem);
        }

        .network-toast.fade-out {
            animation: fadeOut 0.3s ease-out forwards;
        }

        .network-toast .toast-icon {
            font-size: clamp(1.25rem, 3vw, 1.5rem);
            flex-shrink: 0;
        }

        .network-toast .toast-content {
            flex: 1;
        }

        .network-toast .toast-title {
            font-weight: 600;
            margin-bottom: 4px;
        }

        .network-toast .toast-message {
            font-size: clamp(0.75rem, 1.8vw, 0.8125rem);
            opacity: 0.95;
        }

        .network-toast .toast-close {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            flex-shrink: 0;
        }

        .network-toast .toast-close:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }

        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes fadeOut {
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }

        @media (max-width: 600px) {
            .network-toast {
                right: 10px;
                left: 10px;
                min-width: auto;
            }
        }

        /* Fix tab switching layout shift */
        .tab-content {
            min-height: 400px;
        }

        /* Barangay dropdown styling */
        select#barangay_id {
            line-height: 1.8 !important;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        /* Barangay options styling */
        select#barangay_id option {
            padding: clamp(8px, 2vw, 10px) clamp(12px, 3vw, 16px) !important;
            font-size: clamp(13px, 3vw, 15px) !important;
            color: #202124 !important;
            background: white !important;
        }

        /* Disabled/Already rated barangays */
        select#barangay_id option:disabled {
            color: #999 !important;
            font-style: italic !important;
            background: #f5f5f5 !important;
            text-decoration: line-through;
        }

        /* Step 0 form - Keep 3 columns side-by-side */
        #step0 .form-row {
            display: grid !important;
            grid-template-columns: 1fr 1fr 1fr !important;
            gap: clamp(12px, 3vw, 20px) !important;
            margin-bottom: clamp(16px, 4vw, 20px);
        }

        /* Responsive: Stack on very small screens only */
        @media (max-width: 768px) {
            #step0 .form-row {
                grid-template-columns: 1fr !important;
            }
        }

        /* Medium screens: 2 columns then 1 */
        @media (min-width: 769px) and (max-width: 991px) {
            #step0 .form-row {
                grid-template-columns: 1fr 1fr !important;
            }

            #step0 .form-row .form-group:last-child {
                grid-column: 1 / -1;
            }
        }

        /* Mobile responsiveness improvements */
        @media (max-width: 576px) {
            body {
                padding-top: 70px !important; /* Increased padding for mobile navbar */
                padding-left: 8px !important;
                padding-right: 8px !important;
            }

            .custom-navbar {
                padding: 0.75rem 1rem !important; /* Reduced horizontal padding on mobile */
            }

            .navbar-brand {
                font-size: 0.85rem !important;
                line-height: 1.3;
            }

            .navbar-brand i {
                font-size: 1.1rem !important;
                margin-right: 0.25rem !important;
            }

            .avatar-circle {
                width: 35px !important;
                height: 35px !important;
                font-size: 1rem !important;
            }

            .container {
                max-width: 100% !important;
                margin: 0 !important;
                border-radius: 8px !important;
            }

            .content {
                padding: 12px !important;
            }

            .progress-text-container {
                padding: 10px 8px !important;
            }

            .progress-badge {
                padding: 6px 16px !important;
                font-size: 11px !important;
            }

            .nav-tabs {
                gap: 4px !important;
                margin-bottom: 16px !important;
                padding-bottom: 0 !important;
                overflow-x: auto;
                overflow-y: hidden;
                -webkit-overflow-scrolling: touch;
                white-space: nowrap;
                flex-wrap: nowrap !important;
                border-bottom: 2px solid #e8eaed;
            }

            .nav-tab {
                padding: 10px 14px !important;
                font-size: 13px !important;
                flex-shrink: 0;
            }

            .nav-tab i {
                font-size: 12px !important;
            }

            .card {
                padding: 14px !important;
                margin-bottom: 14px !important;
            }

            .card-title {
                font-size: 16px !important;
                margin-bottom: 12px !important;
                padding-bottom: 8px !important;
            }

            .form-group {
                margin-bottom: 14px !important;
            }

            .form-group label {
                font-size: 12px !important;
                margin-bottom: 6px !important;
            }

            .alert {
                padding: 10px !important;
                font-size: 12px !important;
                margin-bottom: 14px !important;
            }

            .alert strong {
                font-size: 13px !important;
            }

            /* Ensure dropdown is accessible on mobile */
            .dropdown-menu {
                position: absolute !important;
                right: 0 !important;
                left: auto !important;
                margin-top: 0.5rem;
                min-width: 180px;
            }

            .network-toast {
                top: 75px !important;
                right: 10px !important;
                left: 10px !important;
                min-width: auto !important;
                max-width: none !important;
                padding: 0.75rem 1rem !important;
                font-size: 0.8rem !important;
            }

            .network-toast .toast-icon {
                font-size: 1.1rem !important;
            }
        }

        /* Extra small devices (phones in portrait) */
        @media (max-width: 400px) {
            body {
                padding-top: 75px !important;
            }

            .navbar-brand {
                font-size: 0.75rem !important;
            }

            .navbar-brand i {
                display: none; /* Hide icon to save space */
            }

            .nav-tab {
                padding: 8px 12px !important;
                font-size: 12px !important;
            }

            .progress-badge {
                padding: 5px 12px !important;
                font-size: 10px !important;
            }
        }

        /* Landscape phones */
        @media (max-width: 767px) and (orientation: landscape) {
            body {
                padding-top: 65px !important;
            }

            .custom-navbar {
                padding: 0.5rem 1rem !important;
            }
        }
    </style>
</head>
<body>
    <!-- Top Navbar -->
    <nav class="navbar custom-navbar fixed-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="bi bi-clipboard-data-fill me-2"></i>
                <?php echo APP_NAME; ?>
            </a>
            <div class="d-flex align-items-center">
                <div class="dropdown">
                    <button class="btn p-0 dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="avatar-circle" title="<?php echo htmlspecialchars($_SESSION['rater_name']); ?>">
                            <?php
                                $name = $_SESSION['rater_name'];
                                echo strtoupper(substr($name, 0, 1));
                            ?>
                        </div>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li>
                            <div class="user-info-dropdown">
                                <div class="user-name"><?php echo htmlspecialchars($_SESSION['rater_name']); ?></div>
                                <div class="user-role">Assessor</div>
                            </div>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="#" onclick="confirmLogout(); return false;">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        <!-- Progress Bar -->
        <div class="progress-bar">
            <div class="progress-fill" id="progressFill" style="width: 0%"></div>
        </div>
        <div class="progress-text-container">
            <span class="progress-badge" id="progressText">Step 0 of 5</span>
        </div>

        <div class="content">
            <!-- Navigation Tabs -->
            <div class="nav-tabs">
                <button class="nav-tab active" onclick="switchTab('addTab', event)"><i class="bi bi-plus-circle me-1"></i> Add Assessment</button>
                <button class="nav-tab" onclick="switchTab('viewTab', event)"><i class="bi bi-list-ul me-1"></i> View Assessments</button>
            </div>

            <!-- Add Assessment Tab -->
            <div id="addTab" class="tab-content active">
                <!-- Step 0: Initial Information -->
                <div class="step active" id="step0">
                    <div class="card">
                        <h2 class="card-title">Assessment Information</h2>

                        <div class="alert alert-info">
                            <strong>📋 Instructions:</strong><br>
                            Fill in the basic information to start the assessment. All data will be saved as you progress through each section.
                        </div>

                        <form id="assessmentForm">
                            <!-- Hidden input for rater_id from session -->
                            <input type="hidden" id="rater_id" name="rater_id" value="<?php echo $_SESSION['rater_id']; ?>">

                            <div class="form-row">
                                <div class="form-group">
                                    <label>Rater/Assessor</label>
                                    <div class="readonly-field">
                                        <?php echo htmlspecialchars($_SESSION['rater_name']); ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="barangay_id">Barangay *</label>
                                    <select id="barangay_id" name="barangay_id" required onchange="validateStep0()">
                                        <option value="">Select Barangay</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="assessment_date">Assessment Date *</label>
                                    <input type="date" id="assessment_date" name="assessment_date" required onchange="validateStep0()">
                                </div>
                            </div>

                            <div class="button-group">
                                <button type="button" class="btn btn-primary" onclick="nextStep()" id="step0Next" disabled>Next →</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Step 1: Section 1 - Establishment -->
                <div class="step" id="step1">
                    <div class="card">
                        <h2 class="card-title">Section 1: Establishment</h2>

                        <div class="barangay-info">
                            <strong>Barangay:</strong> <span id="barangay_name_1"></span><br>
                            <strong>Date:</strong> <span id="assessment_date_1"></span><br>
                            <strong>Score:</strong> <span id="score1_display" style="font-size: 20px; color: #1e8e3e;">0/20</span>
                        </div>

                        <!-- Hidden input to store calculated score -->
                        <input type="hidden" id="section1_score" name="section1_score" value="0">

                        <div class="form-group">
                            <label>1.a. The Barangay VAW Desk is established through: (5%)</label>
                            <div class="radio-group">
                                <div class="radio-option" onclick="selectRadio('q1a', 'ordinance')">
                                    <input type="radio" name="q1a" value="ordinance" id="q1a_ordinance" onchange="calculateScores()">
                                    <label for="q1a_ordinance">Barangay Ordinance</label>
                                    <span class="point-badge">5%</span>
                                </div>
                                <div class="radio-option" onclick="selectRadio('q1a', 'eo')">
                                    <input type="radio" name="q1a" value="eo" id="q1a_eo" onchange="calculateScores()">
                                    <label for="q1a_eo">Executive Order</label>
                                    <span class="point-badge">5%</span>
                                </div>
                                <div class="radio-option" onclick="selectRadio('q1a', 'both')">
                                    <input type="radio" name="q1a" value="both" id="q1a_both" onchange="calculateScores()">
                                    <label for="q1a_both">Both Ordinance & EO</label>
                                    <span class="point-badge">5%</span>
                                </div>
                                <div class="radio-option" onclick="selectRadio('q1a', 'none')">
                                    <input type="radio" name="q1a" value="none" id="q1a_none" onchange="calculateScores()">
                                    <label for="q1a_none">None</label>
                                    <span class="point-badge">0%</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>1.b. Barangay VAW Desk Person has attended: (5% total)</label>
                            <div class="checkbox-group">
                                <div class="checkbox-option" onclick="toggleCheckbox('q1b_antivaw')">
                                    <input type="checkbox" id="q1b_antivaw" onchange="calculateScores()">
                                    <label for="q1b_antivaw">Orientation on Anti-VAW Laws (RA 9262, 9208, etc.)</label>
                                    <span class="point-badge">2%</span>
                                </div>
                                <div class="checkbox-option" onclick="toggleCheckbox('q1b_gender')">
                                    <input type="checkbox" id="q1b_gender" onchange="calculateScores()">
                                    <label for="q1b_gender">Gender-Sensitivity Training</label>
                                    <span class="point-badge">2%</span>
                                </div>
                                <div class="checkbox-option" onclick="toggleCheckbox('q1b_crisis')">
                                    <input type="checkbox" id="q1b_crisis" onchange="calculateScores()">
                                    <label for="q1b_crisis">Basic Crisis Intervention</label>
                                    <span class="point-badge">1%</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>1.c. VAW Desk Location: (5%)</label>
                            <div class="checkbox-group">
                                <div class="checkbox-option" onclick="toggleCheckbox('q1c_location')">
                                    <input type="checkbox" id="q1c_location" onchange="calculateScores()">
                                    <label for="q1c_location">Located within the barangay hall or near Punong Barangay office</label>
                                    <span class="point-badge">5%</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>1.d. Interview Room: (5%)</label>
                            <div class="checkbox-group">
                                <div class="checkbox-option" onclick="toggleCheckbox('q1d_room')">
                                    <input type="checkbox" id="q1d_room" onchange="calculateScores()">
                                    <label for="q1d_room">Has a separate room or enclosed area for private interviews</label>
                                    <span class="point-badge">5%</span>
                                </div>
                            </div>
                        </div>

                        <div class="button-group">
                            <button type="button" class="btn btn-secondary" onclick="previousStep()">← Previous</button>
                            <button type="button" class="btn btn-primary" onclick="nextStep()">Next →</button>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Section 2 - Resources -->
                <div class="step" id="step2">
                    <div class="card">
                        <h2 class="card-title">Section 2: Resources</h2>

                        <div class="barangay-info">
                            <strong>Barangay:</strong> <span id="barangay_name_2"></span><br>
                            <strong>Date:</strong> <span id="assessment_date_2"></span><br>
                            <strong>Score:</strong> <span id="score2_display" style="font-size: 20px; color: #1e8e3e;">0/20</span>
                        </div>

                        <!-- Hidden input to store calculated score -->
                        <input type="hidden" id="section2_score" name="section2_score" value="0">

                        <div class="form-group">
                            <label>2.a. Furniture and Vehicle: (4% total)</label>
                            <div class="checkbox-group">
                                <div class="checkbox-option" onclick="toggleCheckbox('q2a_table')">
                                    <input type="checkbox" id="q2a_table" onchange="calculateScores()">
                                    <label for="q2a_table">Table and chair</label>
                                    <span class="point-badge">1%</span>
                                </div>
                                <div class="checkbox-option" onclick="toggleCheckbox('q2a_cabinet')">
                                    <input type="checkbox" id="q2a_cabinet" onchange="calculateScores()">
                                    <label for="q2a_cabinet">Filing cabinet/storage area</label>
                                    <span class="point-badge">1%</span>
                                </div>
                                <div class="checkbox-option" onclick="toggleCheckbox('q2a_bed')">
                                    <input type="checkbox" id="q2a_bed" onchange="calculateScores()">
                                    <label for="q2a_bed">Sofa bed, folding bed, or mat</label>
                                    <span class="point-badge">1%</span>
                                </div>
                                <div class="checkbox-option" onclick="toggleCheckbox('q2a_vehicle')">
                                    <input type="checkbox" id="q2a_vehicle" onchange="calculateScores()">
                                    <label for="q2a_vehicle">Availability of vehicle/transportation for victim-survivors</label>
                                    <span class="point-badge">1%</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>2.b. Equipment and Supplies: (6% total)</label>
                            <div class="checkbox-group">
                                <div class="checkbox-option" onclick="toggleCheckbox('q2b_phone')">
                                    <input type="checkbox" id="q2b_phone" onchange="calculateScores()">
                                    <label for="q2b_phone">Landline or mobile phone</label>
                                    <span class="point-badge">2%</span>
                                </div>
                                <div class="checkbox-option" onclick="toggleCheckbox('q2b_computer')">
                                    <input type="checkbox" id="q2b_computer" onchange="calculateScores()">
                                    <label for="q2b_computer">Computer or typewriter for logging/monitoring</label>
                                    <span class="point-badge">1%</span>
                                </div>
                                <div class="checkbox-option" onclick="toggleCheckbox('q2b_camera')">
                                    <input type="checkbox" id="q2b_camera" onchange="calculateScores()">
                                    <label for="q2b_camera">Camera for documenting physical evidence</label>
                                    <span class="point-badge">1%</span>
                                </div>
                                <div class="checkbox-option" onclick="toggleCheckbox('q2b_recorder')">
                                    <input type="checkbox" id="q2b_recorder" onchange="calculateScores()">
                                    <label for="q2b_recorder">Tape or voice recorder for case narratives</label>
                                    <span class="point-badge">1%</span>
                                </div>
                                <div class="checkbox-option" onclick="toggleCheckbox('q2b_firstaid')">
                                    <input type="checkbox" id="q2b_firstaid" onchange="calculateScores()">
                                    <label for="q2b_firstaid">First aid kit and other medicines</label>
                                    <span class="point-badge">1%</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>2.c. Monitoring Tools: (5% total)</label>
                            <div class="checkbox-group">
                                <div style="margin-bottom: 15px; font-weight: 600; color: #333;">Logbooks:</div>
                                <div class="checkbox-option" onclick="toggleCheckbox('q2c_logbook1')">
                                    <input type="checkbox" id="q2c_logbook1" onchange="calculateScores()">
                                    <label for="q2c_logbook1">Logbook 1: for RA 9262 cases</label>
                                    <span class="point-badge">1.5%</span>
                                </div>
                                <div class="checkbox-option" onclick="toggleCheckbox('q2c_logbook2')">
                                    <input type="checkbox" id="q2c_logbook2" onchange="calculateScores()">
                                    <label for="q2c_logbook2">Logbook 2: for other VAW related cases</label>
                                    <span class="point-badge">1.5%</span>
                                </div>

                                <div style="margin: 15px 0; font-weight: 600; color: #333;">Forms:</div>
                                <div class="checkbox-option" onclick="toggleCheckbox('q2c_intake')">
                                    <input type="checkbox" id="q2c_intake" onchange="calculateScores()">
                                    <label for="q2c_intake">VAW Desk Intake Form</label>
                                    <span class="point-badge">0.5%</span>
                                </div>
                                <div class="checkbox-option" onclick="toggleCheckbox('q2c_referral')">
                                    <input type="checkbox" id="q2c_referral" onchange="calculateScores()">
                                    <label for="q2c_referral">Referral Form</label>
                                    <span class="point-badge">0.5%</span>
                                </div>
                                <div class="checkbox-option" onclick="toggleCheckbox('q2c_feedback')">
                                    <input type="checkbox" id="q2c_feedback" onchange="calculateScores()">
                                    <label for="q2c_feedback">Feedback Form</label>
                                    <span class="point-badge">0.5%</span>
                                </div>
                                <div class="checkbox-option" onclick="toggleCheckbox('q2c_bpo')">
                                    <input type="checkbox" id="q2c_bpo" onchange="calculateScores()">
                                    <label for="q2c_bpo">BPO Application Form</label>
                                    <span class="point-badge">0.5%</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>2.d. References: (5% total)</label>
                            <div class="checkbox-group">
                                <div class="checkbox-option" onclick="toggleCheckbox('q2d_directory')">
                                    <input type="checkbox" id="q2d_directory" onchange="calculateScores()">
                                    <label for="q2d_directory">Directory of service providers</label>
                                    <span class="point-badge">2%</span>
                                </div>
                                <div class="checkbox-option" onclick="toggleCheckbox('q2d_handbook')">
                                    <input type="checkbox" id="q2d_handbook" onchange="calculateScores()">
                                    <label for="q2d_handbook">VAW Desk Handbook</label>
                                    <span class="point-badge">1%</span>
                                </div>
                                <div class="checkbox-option" onclick="toggleCheckbox('q2d_books')">
                                    <input type="checkbox" id="q2d_books" onchange="calculateScores()">
                                    <label for="q2d_books">VAW-related reference books, brochures</label>
                                    <span class="point-badge">1%</span>
                                </div>
                                <div class="checkbox-option" onclick="toggleCheckbox('q2d_bpo_flow')">
                                    <input type="checkbox" id="q2d_bpo_flow" onchange="calculateScores()">
                                    <label for="q2d_bpo_flow">Flowchart on BPO issuance</label>
                                    <span class="point-badge">0.5%</span>
                                </div>
                                <div class="checkbox-option" onclick="toggleCheckbox('q2d_vaw_flow')">
                                    <input type="checkbox" id="q2d_vaw_flow" onchange="calculateScores()">
                                    <label for="q2d_vaw_flow">Flowchart on Handling of VAW Cases</label>
                                    <span class="point-badge">0.5%</span>
                                </div>
                            </div>
                        </div>

                        <div class="button-group">
                            <button type="button" class="btn btn-secondary" onclick="previousStep()">← Previous</button>
                            <button type="button" class="btn btn-primary" onclick="nextStep()">Next →</button>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Section 3 - Policies & Plans -->
                <div class="step" id="step3">
                    <div class="card">
                        <h2 class="card-title">Section 3: Policies, Plans, and Budget</h2>

                        <div class="barangay-info">
                            <strong>Barangay:</strong> <span id="barangay_name_3"></span><br>
                            <strong>Date:</strong> <span id="assessment_date_3"></span><br>
                            <strong>Score:</strong> <span id="score3_display" style="font-size: 20px; color: #1e8e3e;">0/20</span>
                        </div>

                        <!-- Hidden input to store calculated score -->
                        <input type="hidden" id="section3_score" name="section3_score" value="0">

                        <div class="form-group">
                            <label>3.a. Annual Investment Program (AIP): (5%)</label>
                            <div class="checkbox-group">
                                <div class="checkbox-option" onclick="toggleCheckbox('q3a_aip')">
                                    <input type="checkbox" id="q3a_aip" onchange="calculateScores()">
                                    <label for="q3a_aip">Annual Investment Program (AIP) reflecting the approved Barangay GAD Plan and Budget</label>
                                    <span class="point-badge">5%</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>3.b. Certificate of Review: (5%)</label>
                            <div class="checkbox-group">
                                <div class="checkbox-option" onclick="toggleCheckbox('q3b_cert')">
                                    <input type="checkbox" id="q3b_cert" onchange="calculateScores()">
                                    <label for="q3b_cert">Certificate of Review and endorsement of the 2024 GAD Plan and Budget</label>
                                    <span class="point-badge">5%</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>3.c. GAD Plan Programs: (5%)</label>
                            <div class="checkbox-group">
                                <div class="checkbox-option" onclick="toggleCheckbox('q3c_gad')">
                                    <input type="checkbox" id="q3c_gad" onchange="calculateScores()">
                                    <label for="q3c_gad">Gender-responsive program and activities to address gender-based violence is indicated in the approved Barangay GAD Plan and Budget</label>
                                    <span class="point-badge">5%</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>3.d. BDP Programs: (5%)</label>
                            <div class="checkbox-group">
                                <div class="checkbox-option" onclick="toggleCheckbox('q3d_bdp')">
                                    <input type="checkbox" id="q3d_bdp" onchange="calculateScores()">
                                    <label for="q3d_bdp">Gender-responsive program and activities to address gender-based violence is integrated in the Barangay Development Plan (BDP)</label>
                                    <span class="point-badge">5%</span>
                                </div>
                            </div>
                        </div>

                        <div class="button-group">
                            <button type="button" class="btn btn-secondary" onclick="previousStep()">← Previous</button>
                            <button type="button" class="btn btn-primary" onclick="nextStep()">Next →</button>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Section 4 - Accomplishments -->
                <div class="step" id="step4">
                    <div class="card">
                        <h2 class="card-title">Section 4: Accomplishments</h2>

                        <div class="barangay-info">
                            <strong>Barangay:</strong> <span id="barangay_name_4"></span><br>
                            <strong>Date:</strong> <span id="assessment_date_4"></span><br>
                            <strong>Score:</strong> <span id="score4_display" style="font-size: 20px; color: #1e8e3e;">0/40</span>
                        </div>

                        <!-- Hidden input to store calculated score -->
                        <input type="hidden" id="section4_score" name="section4_score" value="0">

                        <div class="form-group">
                            <label>4.a. Annual Accomplishment Report: (10%)</label>
                            <div class="checkbox-group">
                                <div class="checkbox-option" onclick="toggleCheckbox('q4a_annual')">
                                    <input type="checkbox" id="q4a_annual" onchange="calculateScores()">
                                    <label for="q4a_annual">Annual Accomplishment Report based on the approved Barangay GAD Plan and Budget</label>
                                    <span class="point-badge">10%</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>4.b. Quarterly Accomplishment Reports: (10%)</label>
                            <div class="radio-group">
                                <div class="radio-option" onclick="selectRadio('q4b', '4')">
                                    <input type="radio" name="q4b" value="4" id="q4b_4" onchange="calculateScores()">
                                    <label for="q4b_4">4 quarterly accomplishment reports submitted (with received stamp)</label>
                                    <span class="point-badge">10%</span>
                                </div>
                                <div class="radio-option" onclick="selectRadio('q4b', '3')">
                                    <input type="radio" name="q4b" value="3" id="q4b_3" onchange="calculateScores()">
                                    <label for="q4b_3">3 quarterly accomplishment reports</label>
                                    <span class="point-badge">7.5%</span>
                                </div>
                                <div class="radio-option" onclick="selectRadio('q4b', '2')">
                                    <input type="radio" name="q4b" value="2" id="q4b_2" onchange="calculateScores()">
                                    <label for="q4b_2">2 quarterly accomplishment reports</label>
                                    <span class="point-badge">5%</span>
                                </div>
                                <div class="radio-option" onclick="selectRadio('q4b', '1')">
                                    <input type="radio" name="q4b" value="1" id="q4b_1" onchange="calculateScores()">
                                    <label for="q4b_1">1 quarterly accomplishment report</label>
                                    <span class="point-badge">2.5%</span>
                                </div>
                                <div class="radio-option" onclick="selectRadio('q4b', '0')">
                                    <input type="radio" name="q4b" value="0" id="q4b_0" onchange="calculateScores()">
                                    <label for="q4b_0">None</label>
                                    <span class="point-badge">0%</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>4.c. Updated Database/Records: (10%)</label>
                            <div class="checkbox-group">
                                <div class="checkbox-option" onclick="toggleCheckbox('q4c_database')">
                                    <input type="checkbox" id="q4c_database" onchange="calculateScores()">
                                    <label for="q4c_database">Updated database/records of all VAW cases reported in the barangay</label>
                                    <span class="point-badge">10%</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>4.d. State of Barangay Address (SOBA) Inclusion: (10%)</label>
                            <div class="radio-group">
                                <div class="radio-option" onclick="selectRadio('q4d', '2')">
                                    <input type="radio" name="q4d" value="2" id="q4d_2" onchange="calculateScores()">
                                    <label for="q4d_2">Accomplishments of VAW Desk included in two (2) SOBA</label>
                                    <span class="point-badge">10%</span>
                                </div>
                                <div class="radio-option" onclick="selectRadio('q4d', '1')">
                                    <input type="radio" name="q4d" value="1" id="q4d_1" onchange="calculateScores()">
                                    <label for="q4d_1">Accomplishments of VAW Desk included in one (1) SOBA</label>
                                    <span class="point-badge">5%</span>
                                </div>
                                <div class="radio-option" onclick="selectRadio('q4d', '0')">
                                    <input type="radio" name="q4d" value="0" id="q4d_0" onchange="calculateScores()">
                                    <label for="q4d_0">Accomplishments of VAW Desk not included in SOBA</label>
                                    <span class="point-badge">0%</span>
                                </div>
                            </div>
                        </div>

                        <div class="button-group">
                            <button type="button" class="btn btn-secondary" onclick="previousStep()">← Previous</button>
                            <button type="button" class="btn btn-primary" onclick="nextStep()">Next →</button>
                        </div>
                    </div>
                </div>

                <!-- Step 5: Review & Submit -->
                <div class="step" id="step5">
                    <div class="card">
                        <h2 class="card-title">Review & Submit</h2>

                        <div class="summary-section">
                            <h3>Assessment Summary</h3>
                            <div class="summary-item">
                                <span>Barangay:</span>
                                <strong id="summary_barangay"></strong>
                            </div>
                            <div class="summary-item">
                                <span>Assessment Date:</span>
                                <strong id="summary_date"></strong>
                            </div>
                            <div class="summary-item">
                                <span>Rater:</span>
                                <strong id="summary_rater"></strong>
                            </div>
                        </div>

                        <div class="summary-section">
                            <h3>Section Scores</h3>
                            <div class="summary-item">
                                <span>Section 1: Establishment</span>
                                <strong id="summary_score1">0/20</strong>
                            </div>
                            <div class="summary-item">
                                <span>Section 2: Resources</span>
                                <strong id="summary_score2">0/20</strong>
                            </div>
                            <div class="summary-item">
                                <span>Section 3: Policies & Plans</span>
                                <strong id="summary_score3">0/20</strong>
                            </div>
                            <div class="summary-item">
                                <span>Section 4: Accomplishments</span>
                                <strong id="summary_score4">0/40</strong>
                            </div>
                        </div>

                        <div class="score-display">
                            <div class="label">Total Score</div>
                            <div class="big-score" id="total_score_display">0/100</div>
                        </div>

                        <div class="form-group">
                            <label for="status">Status *</label>
                            <select id="status" name="status" required>
                                <option value="pending">Pending</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed" selected>Completed</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="remarks">Remarks</label>
                            <textarea id="remarks" name="remarks" rows="4" placeholder="Enter any additional notes or observations..."></textarea>
                        </div>

                        <div class="button-group">
                            <button type="button" class="btn btn-secondary" onclick="previousStep()">← Previous</button>
                            <button type="button" class="btn btn-success" onclick="submitAssessment()">Submit Assessment</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- View Assessments Tab -->
            <div id="viewTab" class="tab-content">
                <div class="card">
                    <h2 class="card-title">All Assessments</h2>

                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Rater</th>
                                    <th>Barangay</th>
                                    <th>Date</th>
                                    <th>Sec 1</th>
                                    <th>Sec 2</th>
                                    <th>Sec 3</th>
                                    <th>Sec 4</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="assessmentsTableBody">
                                <tr>
                                    <td colspan="11" class="loading">
                                        <div class="loading-spinner"></div>
                                        Loading assessments...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- External JS -->
    <script src="script.js"></script>

    <script>
        // Logout confirmation function
        function confirmLogout() {
            Swal.fire({
                title: 'Logout',
                text: 'Are you sure you want to logout?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, logout',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'logout.php';
                }
            });
        }

        // Network Speed Monitoring
        let networkToastElement = null;
        let lastSpeedCheck = 0;

        function showNetworkToast(speed) {
            // Remove existing toast if any
            if (networkToastElement) {
                networkToastElement.remove();
            }

            // Create toast
            const toast = document.createElement('div');
            toast.className = 'network-toast';
            toast.innerHTML = `
                <div class="toast-icon">
                    <i class="bi bi-wifi-off"></i>
                </div>
                <div class="toast-content">
                    <div class="toast-title">Slow Connection Detected</div>
                    <div class="toast-message">Your connection speed is ${speed} kbps. Consider switching to a stronger network.</div>
                </div>
                <button class="toast-close" onclick="closeNetworkToast()">
                    <i class="bi bi-x"></i>
                </button>
            `;

            document.body.appendChild(toast);
            networkToastElement = toast;

            // Auto-fade after 3 seconds
            setTimeout(() => {
                closeNetworkToast();
            }, 3000);
        }

        function closeNetworkToast() {
            if (networkToastElement) {
                networkToastElement.classList.add('fade-out');
                setTimeout(() => {
                    if (networkToastElement) {
                        networkToastElement.remove();
                        networkToastElement = null;
                    }
                }, 300);
            }
        }

        async function checkNetworkSpeed() {
            try {
                // Use Navigator.connection API if available
                if ('connection' in navigator) {
                    const connection = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
                    if (connection && connection.downlink !== undefined) {
                        // downlink is in Mbps, convert to kbps
                        const speedKbps = Math.round(connection.downlink * 1000);

                        if (speedKbps < 700) {
                            showNetworkToast(speedKbps);
                        }
                        return;
                    }
                }

                // Fallback: Measure actual download speed
                const imageAddr = "https://www.google.com/images/phd/px.gif";
                const startTime = new Date().getTime();
                const cacheBuster = "?nnn=" + startTime;

                const download = new Image();
                download.onload = function() {
                    const endTime = new Date().getTime();
                    const duration = (endTime - startTime) / 1000; // in seconds
                    const bitsLoaded = 43 * 8; // 43 bytes = px.gif size
                    const speedBps = (bitsLoaded / duration).toFixed(2);
                    const speedKbps = Math.round(speedBps / 1024);

                    if (speedKbps < 700) {
                        showNetworkToast(speedKbps);
                    }
                };

                download.onerror = function() {
                    // If test fails, show warning
                    showNetworkToast('unknown');
                };

                download.src = imageAddr + cacheBuster;
            } catch (error) {
                console.log('Network speed check failed:', error);
            }
        }

        // Check network speed every 4 minutes (240000 ms)
        function startNetworkMonitoring() {
            // Initial check after 5 seconds
            setTimeout(checkNetworkSpeed, 5000);

            // Then check every 4 minutes
            setInterval(checkNetworkSpeed, 240000);
        }

        // Fetch user's existing assessments to disable already-rated barangays
        let userAssessments = [];
        let assessmentsLoaded = false;
        let barangaysLoaded = false;

        async function fetchUserAssessments() {
            try {
                const response = await fetch('get_data.php?action=assessments');
                const data = await response.json();

                console.log('Assessment fetch response:', data);

                if (data.success && data.data) {
                    userAssessments = data.data;
                    assessmentsLoaded = true;
                    console.log('✓ Assessments loaded:', userAssessments.length);
                    console.log('Assessment data:', userAssessments);
                    tryUpdateBarangayDropdown();
                } else {
                    console.warn('No assessments found or error:', data.message);
                    assessmentsLoaded = true; // Still mark as loaded even if empty
                    tryUpdateBarangayDropdown();
                }
            } catch (error) {
                console.error('Error fetching assessments:', error);
                assessmentsLoaded = true; // Mark as loaded to prevent indefinite waiting
                tryUpdateBarangayDropdown();
            }
        }

        function tryUpdateBarangayDropdown() {
            // Only update when both barangays and assessments are loaded
            if (barangaysLoaded && assessmentsLoaded) {
                console.log('✓ Both loaded - updating dropdown now');
                updateBarangayDropdown();
            } else {
                console.log('⏳ Waiting... Barangays:', barangaysLoaded, 'Assessments:', assessmentsLoaded);
            }
        }

        function updateBarangayDropdown() {
            const barangaySelect = document.getElementById('barangay_id');
            if (!barangaySelect) {
                console.error('❌ Barangay select element not found');
                return;
            }

            // Get all options (including those in optgroups)
            const allOptions = barangaySelect.querySelectorAll('option');
            console.log('Total options found:', allOptions.length);

            if (allOptions.length <= 1) { // Only the placeholder "Select Barangay"
                console.warn('⚠ No barangay options loaded yet');
                // Retry after a short delay
                setTimeout(updateBarangayDropdown, 500);
                return;
            }

            // Get list of already-rated barangay IDs
            const ratedBarangayIds = userAssessments.map(assessment => {
                const id = String(assessment.barangay_id);
                console.log(`Mapping assessment ${assessment.id}: Barangay "${assessment.barangay_name}" (ID: ${id})`);
                return id;
            });
            console.log('📋 Already rated barangay IDs:', ratedBarangayIds);

            // Disable already-rated barangays
            let disabledCount = 0;
            allOptions.forEach(option => {
                const optionValue = String(option.value);
                if (option.value && ratedBarangayIds.includes(optionValue)) {
                    option.disabled = true;
                    const originalText = option.textContent.trim();
                    if (!originalText.includes('(Already Rated)')) {
                        option.textContent = originalText + ' (Already Rated)';
                    }
                    option.style.color = '#999 !important';
                    option.style.fontStyle = 'italic';
                    option.style.textDecoration = 'line-through';
                    console.log(`🔒 Disabled: ${originalText} (value: ${optionValue})`);
                    disabledCount++;
                }
            });

            console.log(`✓ Successfully disabled ${disabledCount} barangay(s)`);

            if (disabledCount === 0 && ratedBarangayIds.length > 0) {
                console.warn('⚠ Warning: You have rated barangays but none were disabled. Check if barangay IDs match.');
            }
        }

        // Signal that barangays are loaded (called from script.js)
        window.onBarangaysLoaded = function() {
            barangaysLoaded = true;
            console.log('✓ Barangays loaded signal received');
            tryUpdateBarangayDropdown();
        };

        // Start monitoring when page loads
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function() {
                startNetworkMonitoring();
                fetchUserAssessments();
            });
        } else {
            startNetworkMonitoring();
            fetchUserAssessments();
        }
    </script>
</body>
</html>
