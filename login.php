<?php
session_start();

// Security: Regenerate session ID to prevent session fixation
if (!isset($_SESSION['initiated'])) {
    session_regenerate_id(true);
    $_SESSION['initiated'] = true;
}

// If already logged in, redirect to main page
if (isset($_SESSION['rater_id']) && isset($_SESSION['rater_name'])) {
    header('Location: index.php');
    exit;
}

// Generate CSRF token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Initialize login attempts tracking
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['last_attempt_time'] = time();
}

// Reset login attempts after 15 minutes
if (isset($_SESSION['last_attempt_time']) && (time() - $_SESSION['last_attempt_time']) > 900) {
    $_SESSION['login_attempts'] = 0;
}

// Check if account is locked (5 failed attempts)
$is_locked = false;
$lockout_time_remaining = 0;
if (isset($_SESSION['login_attempts']) && $_SESSION['login_attempts'] >= 5) {
    $lockout_time_remaining = 900 - (time() - $_SESSION['last_attempt_time']);
    if ($lockout_time_remaining > 0) {
        $is_locked = true;
    } else {
        $_SESSION['login_attempts'] = 0;
    }
}

// Security headers
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Content-Security-Policy: default-src 'self' https://cdn.jsdelivr.net; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; img-src 'self' data:;");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VAWC Assessment - Login</title>

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            width: 100%;
            height: 100%;
            overflow: hidden;
            position: fixed;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #E6E6FA 0%, #D8BFD8 50%, #DDA0DD 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .bg-pattern {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image:
                radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 20%, rgba(138, 43, 226, 0.05) 0%, transparent 50%);
            pointer-events: none;
        }

        .animation-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            background: linear-gradient(135deg, #E6E6FA 0%, #D8BFD8 50%, #DDA0DD 100%);
            animation: fadeOut 0.8s ease 2.5s forwards;
        }

        @keyframes fadeOut {
            to {
                opacity: 0;
                pointer-events: none;
            }
        }

        .logo-animation {
            width: clamp(120px, 25vw, 180px);
            height: clamp(120px, 25vw, 180px);
            margin-bottom: clamp(1rem, 3vw, 1.5rem);
            opacity: 0;
            animation: zoomInLogo 1s ease 0s forwards;
            border-radius: 50%;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        }

        @keyframes zoomInLogo {
            0% {
                opacity: 0;
                transform: scale(0.3) rotate(-10deg);
            }
            70% {
                transform: scale(1.1) rotate(0deg);
            }
            100% {
                opacity: 1;
                transform: scale(1) rotate(0deg);
            }
        }

        .main-title {
            text-align: center;
            opacity: 0;
            animation: fadeInTitle 0.8s ease 0.8s forwards;
        }

        .main-title h1 {
            font-size: clamp(48px, 10vw, 72px);
            font-weight: 900;
            color: #6B46C1;
            text-shadow: 0 4px 12px rgba(107, 70, 193, 0.2);
            margin: 0;
            line-height: 1.2;
            letter-spacing: 1px;
        }

        .main-title .subtitle {
            font-size: clamp(14px, 3vw, 18px);
            font-weight: 600;
            color: #805AD5;
            margin-top: clamp(0.5rem, 2vw, 0.75rem);
        }

        @keyframes fadeInTitle {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: clamp(16px, 3vw, 24px);
            box-shadow: 0 20px 60px rgba(107, 70, 193, 0.15);
            padding: clamp(1.75rem, 4.5vw, 2.25rem) clamp(2rem, 5vw, 2.5rem);
            max-width: 480px;
            width: 90%;
            opacity: 0;
            animation: fadeInLogin 0.8s ease 3s forwards;
            position: relative;
            z-index: 10;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        @keyframes fadeInLogin {
            0% {
                opacity: 0;
                transform: translateY(30px) scale(0.95);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .login-header {
            text-align: center;
            margin-bottom: clamp(1.5rem, 4vw, 2rem);
        }

        .logo-container {
            width: clamp(80px, 18vw, 100px);
            height: clamp(80px, 18vw, 100px);
            margin: 0 auto clamp(1rem, 3vw, 1.25rem);
            border-radius: 50%;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(107, 70, 193, 0.2);
            border: 3px solid #fff;
        }

        .logo-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .login-header h1 {
            font-size: clamp(1.25rem, 4vw, 1.75rem);
            color: #6B46C1;
            margin-bottom: clamp(0.25rem, 1vw, 0.5rem);
            font-weight: 700;
        }

        .login-header p {
            font-size: clamp(0.813rem, 2vw, 0.938rem);
            color: #718096;
            font-weight: 500;
        }

        .security-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: linear-gradient(135deg, #667EEA 0%, #764BA2 100%);
            color: white;
            padding: clamp(0.375rem, 1vw, 0.5rem) clamp(0.75rem, 2vw, 1rem);
            border-radius: 20px;
            font-size: clamp(0.688rem, 1.5vw, 0.75rem);
            font-weight: 600;
            margin-bottom: clamp(1rem, 2.5vw, 1.5rem);
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
        }

        .form-group {
            margin-bottom: clamp(1.25rem, 3vw, 1.5rem);
        }

        .form-group label {
            display: block;
            font-size: clamp(0.813rem, 2vw, 0.938rem);
            font-weight: 600;
            color: #4A5568;
            margin-bottom: clamp(0.75rem, 2vw, 1rem);
            text-align: center;
        }

        .pin-boxes {
            display: flex;
            gap: clamp(8px, 2vw, 12px);
            justify-content: center;
            margin-bottom: clamp(1rem, 2.5vw, 1.25rem);
        }

        .pin-box {
            width: clamp(50px, 12vw, 60px);
            height: clamp(50px, 12vw, 60px);
            border: 2px solid #E2E8F0;
            border-radius: clamp(10px, 2vw, 14px);
            text-align: center;
            font-size: clamp(20px, 5vw, 28px);
            font-weight: 700;
            color: #6B46C1;
            outline: none;
            transition: all 0.3s ease;
            background: #F7FAFC;
        }

        .pin-box:focus {
            border-color: #805AD5;
            box-shadow: 0 0 0 4px rgba(128, 90, 213, 0.15);
            background: white;
            transform: scale(1.05);
        }

        .pin-box:disabled {
            background: #EDF2F7;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .login-btn {
            width: 100%;
            padding: clamp(0.875rem, 2.5vw, 1.125rem);
            background: linear-gradient(135deg, #667EEA 0%, #764BA2 100%);
            color: white;
            border: none;
            border-radius: clamp(8px, 2vw, 12px);
            font-size: clamp(0.938rem, 2.5vw, 1.063rem);
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 16px rgba(102, 126, 234, 0.3);
        }

        .login-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        .login-btn:active:not(:disabled) {
            transform: translateY(0);
        }

        .login-btn:disabled {
            background: #CBD5E0;
            cursor: not-allowed;
            box-shadow: none;
        }

        .lockout-warning {
            background: linear-gradient(135deg, #FED7D7 0%, #FC8181 100%);
            color: #742A2A;
            padding: clamp(0.75rem, 2vw, 1rem);
            border-radius: clamp(8px, 2vw, 10px);
            margin-bottom: clamp(1rem, 2.5vw, 1.25rem);
            font-size: clamp(0.813rem, 2vw, 0.875rem);
            font-weight: 600;
            text-align: center;
            border: 1px solid #FC8181;
        }

        .attempts-warning {
            background: #FFF5F5;
            color: #C53030;
            padding: clamp(0.625rem, 1.5vw, 0.75rem);
            border-radius: clamp(6px, 1.5vw, 8px);
            margin-bottom: clamp(0.75rem, 2vw, 1rem);
            font-size: clamp(0.75rem, 1.8vw, 0.813rem);
            text-align: center;
            border: 1px solid #FEB2B2;
        }

        .footer-text {
            text-align: center;
            margin-top: clamp(1.25rem, 3vw, 1.5rem);
            font-size: clamp(0.75rem, 1.8vw, 0.813rem);
            color: #718096;
            line-height: 1.6;
        }

        .footer-text strong {
            color: #4A5568;
            display: block;
            margin-bottom: 4px;
        }

        .developer-badge {
            text-align: center;
            margin-top: clamp(1rem, 2.5vw, 1.25rem);
            padding-top: clamp(1rem, 2.5vw, 1.25rem);
            border-top: 1px solid #E2E8F0;
        }

        .badge-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: linear-gradient(135deg, #48BB78 0%, #38A169 100%);
            color: white;
            padding: clamp(0.5rem, 1.5vw, 0.625rem) clamp(0.875rem, 2.5vw, 1.125rem);
            border-radius: 20px;
            font-size: clamp(0.688rem, 1.5vw, 0.75rem);
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(72, 187, 120, 0.3);
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }

        .loading-pulse {
            animation: pulse 1.5s ease-in-out infinite;
        }

        @media (max-width: 600px) {
            .login-container {
                padding: clamp(1.5rem, 4vw, 2rem);
                width: 95%;
            }
        }
    </style>
</head>
<body>
    <div class="bg-pattern"></div>

    <!-- Splash Animation -->
    <div class="animation-container">
        <img src="logo.jpg" alt="Baggao Logo" class="logo-animation">
        <div class="main-title">
            <h1>VAWC</h1>
            <div class="subtitle">Functionality Audit System</div>
        </div>
    </div>

    <!-- Login Form -->
    <div class="login-container">
        <div class="login-header">
            <div class="logo-container">
                <img src="logo.jpg" alt="Municipality of Baggao">
            </div>
            <h1>Assessor Login</h1>
            <p>Municipality of Baggao, Cagayan</p>
        </div>

        <div class="security-badge">
            <i class="bi bi-shield-check"></i>
            <span>Secured Login</span>
        </div>

        <?php if ($is_locked): ?>
            <div class="lockout-warning">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <strong>Account Temporarily Locked</strong><br>
                Too many failed attempts. Please wait <span id="lockoutTimer"><?php echo ceil($lockout_time_remaining / 60); ?></span> minutes before trying again.
            </div>
        <?php elseif (isset($_SESSION['login_attempts']) && $_SESSION['login_attempts'] > 0 && $_SESSION['login_attempts'] < 5): ?>
            <div class="attempts-warning">
                <i class="bi bi-exclamation-circle"></i>
                Warning: <?php echo $_SESSION['login_attempts']; ?> failed attempt(s). Account will lock after 5 failed attempts.
            </div>
        <?php endif; ?>

        <form id="loginForm" onsubmit="handleLogin(event)">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

            <div class="form-group">
                <label><i class="bi bi-key-fill"></i> Enter Your 4-Digit PIN</label>
                <div class="pin-boxes">
                    <input type="text" class="pin-box" id="pin1" maxlength="1" inputmode="numeric" autocomplete="off" required <?php echo $is_locked ? 'disabled' : ''; ?>>
                    <input type="text" class="pin-box" id="pin2" maxlength="1" inputmode="numeric" autocomplete="off" required <?php echo $is_locked ? 'disabled' : ''; ?>>
                    <input type="text" class="pin-box" id="pin3" maxlength="1" inputmode="numeric" autocomplete="off" required <?php echo $is_locked ? 'disabled' : ''; ?>>
                    <input type="text" class="pin-box" id="pin4" maxlength="1" inputmode="numeric" autocomplete="off" required <?php echo $is_locked ? 'disabled' : ''; ?>>
                </div>
            </div>

            <button type="submit" class="login-btn" id="loginBtn" <?php echo $is_locked ? 'disabled' : ''; ?>>
                <i class="bi bi-box-arrow-in-right"></i> Verify & Login
            </button>
        </form>



        <div class="footer-text">
            <strong>VAWC Functionality Audit 2025</strong>
            Municipality of Baggao, Cagayan<br>
            <small>Secure • Reliable • Efficient</small>
        </div>

        <div class="developer-badge">
            <div class="badge-chip">
                <i class="bi bi-code-square"></i>
                <span>Developed by Richmond Rosete</span>
            </div>
        </div>
    </div>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const pinBoxes = document.querySelectorAll('.pin-box');
        const isLocked = <?php echo $is_locked ? 'true' : 'false'; ?>;
        let lockoutTime = <?php echo $lockout_time_remaining; ?>;

        // Countdown timer for lockout
        if (isLocked && lockoutTime > 0) {
            const timerElement = document.getElementById('lockoutTimer');
            const countdown = setInterval(() => {
                lockoutTime--;
                if (lockoutTime <= 0) {
                    clearInterval(countdown);
                    window.location.reload();
                } else {
                    timerElement.textContent = Math.ceil(lockoutTime / 60);
                }
            }, 1000);
        }

        // PIN box auto-tab functionality with security
        pinBoxes.forEach((box, index) => {
            // Input validation and auto-tab
            box.addEventListener('input', function(e) {
                // Only allow numeric input
                this.value = this.value.replace(/[^0-9]/g, '');

                // Auto-tab to next box
                if (this.value.length === 1 && index < pinBoxes.length - 1) {
                    pinBoxes[index + 1].focus();
                }
            });

            // Backspace navigation
            box.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && this.value === '' && index > 0) {
                    pinBoxes[index - 1].focus();
                }

                // Prevent non-numeric keys except navigation
                if (e.key !== 'Backspace' && e.key !== 'Tab' && e.key !== 'ArrowLeft' && e.key !== 'ArrowRight') {
                    if (e.key < '0' || e.key > '9') {
                        e.preventDefault();
                    }
                }
            });

            // Prevent paste of non-numeric content
            box.addEventListener('paste', function(e) {
                e.preventDefault();
                const pasteData = e.clipboardData.getData('text');
                const numericData = pasteData.replace(/[^0-9]/g, '');

                if (numericData.length > 0) {
                    // Distribute pasted digits across boxes
                    for (let i = 0; i < Math.min(numericData.length, pinBoxes.length - index); i++) {
                        pinBoxes[index + i].value = numericData[i];
                    }
                    // Focus next empty box or last box
                    const nextIndex = Math.min(index + numericData.length, pinBoxes.length - 1);
                    pinBoxes[nextIndex].focus();
                }
            });

            // Select all on focus for easy replacement
            box.addEventListener('focus', function() {
                this.select();
            });
        });

        // Auto-focus on first PIN box after animation
        setTimeout(() => {
            if (!isLocked) {
                document.getElementById('pin1').focus();
            }
        }, 3500);

        // Handle login with security measures
        function handleLogin(event) {
            event.preventDefault();

            if (isLocked) {
                Swal.fire({
                    icon: 'error',
                    title: 'Account Locked',
                    text: 'Too many failed attempts. Please wait before trying again.',
                    confirmButtonColor: '#E53E3E'
                });
                return;
            }

            const pin =
                document.getElementById('pin1').value +
                document.getElementById('pin2').value +
                document.getElementById('pin3').value +
                document.getElementById('pin4').value;

            // Validate PIN format
            if (pin.length !== 4 || !/^\d{4}$/.test(pin)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid PIN',
                    text: 'Please enter exactly 4 digits',
                    confirmButtonColor: '#E53E3E'
                });
                return;
            }

            // Get CSRF token
            const csrfToken = document.querySelector('input[name="csrf_token"]').value;

            // Show loading with security icon
            Swal.fire({
                title: '<i class="bi bi-shield-check" style="font-size: 48px; color: #805AD5;"></i>',
                html: '<strong>Verifying PIN...</strong><br><small>Checking credentials securely</small>',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Send to authenticate.php with CSRF token
            fetch('authenticate.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'pin=' + encodeURIComponent(pin) + '&csrf_token=' + encodeURIComponent(csrfToken)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Welcome!',
                        html: '<strong>' + data.data.name + '</strong><br><small>Login successful</small>',
                        confirmButtonColor: '#48BB78',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        // Redirect to admin dashboard if admin, otherwise to regular page
                        if (data.data.is_admin) {
                            window.location.href = 'admin_dashboard.php';
                        } else {
                            window.location.href = 'index.php';
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Access Denied',
                        text: data.message,
                        confirmButtonColor: '#E53E3E'
                    }).then(() => {
                        // Check if account is now locked
                        if (data.locked) {
                            window.location.reload();
                        }
                    });

                    // Shake animation for PIN boxes
                    pinBoxes.forEach(box => {
                        box.value = '';
                        box.style.animation = 'shake 0.5s ease';
                    });

                    setTimeout(() => {
                        pinBoxes.forEach(box => {
                            box.style.animation = '';
                        });
                        document.getElementById('pin1').focus();
                    }, 500);
                }
            })
            .catch(error => {
                console.error('Login error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Connection Error',
                    text: 'Failed to connect to server. Please check your connection and try again.',
                    confirmButtonColor: '#E53E3E'
                });
            });
        }

        // Prevent form resubmission on page refresh
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>
</html>
