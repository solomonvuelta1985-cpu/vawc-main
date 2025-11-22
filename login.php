<?php
session_start();

// If already logged in, redirect to main page
if (isset($_SESSION['rater_id']) && isset($_SESSION['rater_name'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VAW Assessment - Login</title>

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

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
            background: linear-gradient(135deg, #2c5aa0 0%, #1a73e8 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .bg-circle {
            position: fixed;
            border-radius: 50%;
            background: rgba(138, 43, 226, 0.05);
            animation: float 20s infinite ease-in-out;
            pointer-events: none;
        }

        .bg-circle:nth-child(1) {
            width: 300px;
            height: 300px;
            top: -100px;
            left: -100px;
            animation-delay: 0s;
        }

        .bg-circle:nth-child(2) {
            width: 400px;
            height: 400px;
            bottom: -150px;
            right: -150px;
            animation-delay: 7s;
        }

        .bg-circle:nth-child(3) {
            width: 200px;
            height: 200px;
            top: 50%;
            right: 10%;
            animation-delay: 14s;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(20px, -20px) scale(1.05); }
            66% { transform: translate(-15px, 15px) scale(0.95); }
        }

        .animation-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            background: linear-gradient(135deg, #2c5aa0 0%, #1a73e8 100%);
            animation: fadeOut 0.8s ease 3.2s forwards;
        }

        @keyframes fadeOut {
            to {
                opacity: 0;
                pointer-events: none;
            }
        }

        .main-title {
            text-align: center;
            opacity: 0;
            animation: zoomIn 1.2s ease 0s forwards;
        }

        .main-title h1 {
            font-size: 80px;
            font-weight: 900;
            color: #fff;
            text-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            margin: 0;
            line-height: 1;
            letter-spacing: 2px;
        }

        @keyframes zoomIn {
            0% {
                opacity: 0;
                transform: scale(0.5) rotate(-5deg);
            }
            70% {
                transform: scale(1.05) rotate(0deg);
            }
            100% {
                opacity: 1;
                transform: scale(1) rotate(0deg);
            }
        }

        .subtitle {
            position: absolute;
            font-size: 20px;
            font-weight: 600;
            color: #fff;
            opacity: 0;
            animation: fadeInSubtitle 1s ease 1.5s forwards;
            margin-top: 100px;
            text-align: center;
        }

        .subtitle-location {
            position: absolute;
            font-size: 16px;
            font-weight: 500;
            color: #fff;
            opacity: 0;
            animation: fadeInLocation 1s ease 2s forwards;
            margin-top: 140px;
            text-align: center;
        }

        @keyframes fadeInSubtitle {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInLocation {
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
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 30px 35px;
            max-width: 450px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            opacity: 0;
            animation: fadeInLogin 1s ease 3.7s forwards;
            position: relative;
            z-index: 10;
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
            margin-bottom: 25px;
        }

        .login-header h1 {
            font-size: 24px;
            color: #1a73e8;
            margin-bottom: 6px;
            font-weight: 700;
        }

        .login-header p {
            font-size: 14px;
            color: #666;
            font-weight: 500;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #333;
            margin-bottom: 12px;
            text-align: center;
        }

        .pin-boxes {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-bottom: 20px;
        }

        .pin-box {
            width: 55px;
            height: 55px;
            border: 2px solid #dadce0;
            border-radius: 12px;
            text-align: center;
            font-size: 24px;
            font-weight: 700;
            color: #1a73e8;
            outline: none;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .pin-box:focus {
            border-color: #1a73e8;
            box-shadow: 0 0 0 4px rgba(26, 115, 232, 0.1);
            background: white;
        }

        .login-btn {
            width: 100%;
            padding: 14px;
            background: #1a73e8;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-top: 5px;
        }

        .login-btn:hover {
            background: #1557b0;
        }

        .login-btn:active {
            transform: scale(0.98);
        }

        .footer-text {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #666;
        }

        .developer-badge {
            text-align: center;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #e0e0e0;
        }

        .badge-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #1a73e8;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(26, 115, 232, 0.2);
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        @media (max-width: 600px) {
            .login-container {
                padding: 25px 20px;
            }

            .main-title h1 {
                font-size: 60px;
            }

            .subtitle {
                font-size: 16px;
                margin-top: 80px;
            }

            .subtitle-location {
                font-size: 14px;
                margin-top: 110px;
            }

            .pin-box {
                width: 50px;
                height: 50px;
                font-size: 22px;
            }

            .pin-boxes {
                gap: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="bg-circle"></div>
    <div class="bg-circle"></div>
    <div class="bg-circle"></div>

    <div class="animation-container">
        <div class="main-title">
            <h1>VAWC</h1>
        </div>
        <div class="subtitle">Data Consolidator System</div>
        <div class="subtitle-location">Municipality of Baggao, Cagayan</div>
    </div>

    <div class="login-container">
        <div class="login-header">
            <h1>Assessor Login</h1>
            <p>Enter your 4-digit PIN to continue</p>
        </div>

        <form id="loginForm" onsubmit="handleLogin(event)">
            <div class="form-group">
                <label>Enter PIN Code</label>
                <div class="pin-boxes">
                    <input type="text" class="pin-box" id="pin1" maxlength="1" inputmode="numeric" autocomplete="off" required>
                    <input type="text" class="pin-box" id="pin2" maxlength="1" inputmode="numeric" autocomplete="off" required>
                    <input type="text" class="pin-box" id="pin3" maxlength="1" inputmode="numeric" autocomplete="off" required>
                    <input type="text" class="pin-box" id="pin4" maxlength="1" inputmode="numeric" autocomplete="off" required>
                </div>
            </div>

            <button type="submit" class="login-btn" id="loginBtn">Verify & Login</button>
        </form>

        <div class="footer-text">
            <strong>VAW Data Consolidator 2025</strong><br>
            Municipality of Baggao, Cagayan
        </div>

        <div class="developer-badge">
            <div class="badge-chip">
                <span>💻 Developed by Richmond Rosete</span>
            </div>
        </div>
    </div>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const pinBoxes = document.querySelectorAll('.pin-box');

        // PIN box auto-tab functionality
        pinBoxes.forEach((box, index) => {
            box.addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');

                if (this.value.length === 1 && index < pinBoxes.length - 1) {
                    pinBoxes[index + 1].focus();
                }
            });

            box.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace' && this.value === '' && index > 0) {
                    pinBoxes[index - 1].focus();
                }
            });

            box.addEventListener('keypress', function(e) {
                if (e.key < '0' || e.key > '9') {
                    e.preventDefault();
                }
            });

            box.addEventListener('focus', function() {
                this.select();
            });
        });

        // Auto-focus on first PIN box
        setTimeout(() => {
            document.getElementById('pin1').focus();
        }, 4200);

        // Handle login
        function handleLogin(event) {
            event.preventDefault();

            const pin =
                document.getElementById('pin1').value +
                document.getElementById('pin2').value +
                document.getElementById('pin3').value +
                document.getElementById('pin4').value;

            if (pin.length !== 4) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid PIN',
                    text: 'Please enter all 4 digits',
                    confirmButtonColor: '#DC3545'
                });
                return;
            }

            // Show loading
            Swal.fire({
                title: 'Verifying PIN...',
                text: 'Please wait',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Send to authenticate.php
            fetch('authenticate.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'pin=' + pin
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Welcome!',
                        text: 'Login successful: ' + data.data.name,
                        confirmButtonColor: '#28A745',
                        timer: 2000
                    }).then(() => {
                        window.location.href = 'index.php';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Access Denied',
                        text: data.message,
                        confirmButtonColor: '#DC3545'
                    });

                    // Clear PIN boxes
                    pinBoxes.forEach(box => {
                        box.value = '';
                        box.style.animation = 'shake 0.5s ease';
                    });
                    setTimeout(() => {
                        pinBoxes.forEach(box => {
                            box.style.animation = '';
                        });
                    }, 500);

                    document.getElementById('pin1').focus();
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Connection Error',
                    text: 'Failed to connect to server: ' + error,
                    confirmButtonColor: '#DC3545'
                });
            });
        }
    </script>
</body>
</html>
