<?php
session_start();

// Check if user is logged in (optional - comment out if you want public access during development)
if (!isset($_SESSION['rater_id']) || !isset($_SESSION['is_authenticated']) || $_SESSION['is_authenticated'] !== true) {
    // For development, you can comment out the redirect to view logs without login
    header('Location: login.php');
    exit;
}

$logs_dir = __DIR__ . '/logs/';
$available_logs = [
    'php_errors' => 'PHP Errors',
    'debug' => 'Debug Log'
];

$current_log = isset($_GET['log']) ? $_GET['log'] : 'php_errors';
$log_file = $logs_dir . $current_log . '.log';

// Read log file
$log_content = '';
if (file_exists($log_file)) {
    $log_content = file_get_contents($log_file);
    if (empty(trim($log_content))) {
        $log_content = "No errors logged yet. System is running smoothly!";
    }
} else {
    $log_content = "Log file not found.";
}

// Get file size
$file_size = file_exists($log_file) ? filesize($log_file) : 0;
$file_size_kb = round($file_size / 1024, 2);

// Count lines
$line_count = substr_count($log_content, "\n");

// Get last modified time
$last_modified = file_exists($log_file) ? date('Y-m-d H:i:s', filemtime($log_file)) : 'N/A';

// Clear log functionality
if (isset($_POST['clear_log']) && $_POST['log'] === $current_log) {
    file_put_contents($log_file, '');
    header('Location: view_logs.php?log=' . $current_log . '&cleared=1');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Logs - VAW Data Consolidator</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .header {
            background: #252526;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid #3e3e42;
        }

        .header h1 {
            color: #4ec9b0;
            font-size: 24px;
            margin-bottom: 10px;
        }

        .header p {
            color: #858585;
            font-size: 14px;
        }

        .nav-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .nav-tab {
            padding: 12px 24px;
            background: #252526;
            border: 1px solid #3e3e42;
            border-radius: 6px;
            color: #d4d4d4;
            text-decoration: none;
            transition: all 0.2s;
            font-size: 14px;
        }

        .nav-tab:hover {
            background: #2d2d30;
            border-color: #007acc;
        }

        .nav-tab.active {
            background: #007acc;
            border-color: #007acc;
            color: white;
        }

        .log-info {
            background: #252526;
            padding: 15px 20px;
            border-radius: 6px;
            margin-bottom: 10px;
            border: 1px solid #3e3e42;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .log-stats {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
        }

        .log-stat {
            font-size: 13px;
        }

        .log-stat strong {
            color: #4ec9b0;
        }

        .log-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            font-size: 13px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.2s;
        }

        .btn-primary {
            background: #007acc;
            color: white;
        }

        .btn-primary:hover {
            background: #005a9e;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .log-container {
            background: #1e1e1e;
            border: 1px solid #3e3e42;
            border-radius: 6px;
            overflow: hidden;
        }

        .log-content {
            padding: 20px;
            font-size: 13px;
            line-height: 1.6;
            overflow-x: auto;
            max-height: 70vh;
            overflow-y: auto;
            white-space: pre-wrap;
            word-wrap: break-word;
        }

        .log-content::-webkit-scrollbar {
            width: 12px;
            height: 12px;
        }

        .log-content::-webkit-scrollbar-track {
            background: #252526;
        }

        .log-content::-webkit-scrollbar-thumb {
            background: #424242;
            border-radius: 6px;
        }

        .log-content::-webkit-scrollbar-thumb:hover {
            background: #4e4e4e;
        }

        /* Syntax highlighting for log levels */
        .log-content {
            color: #d4d4d4;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 4px;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .back-link {
            margin-top: 20px;
            display: inline-block;
        }

        @media (max-width: 768px) {
            .log-info {
                flex-direction: column;
                align-items: flex-start;
            }

            .log-stats {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📋 System Logs Viewer</h1>
            <p>Monitor and debug your VAW Data Consolidator application</p>
        </div>

        <?php if (isset($_GET['cleared'])): ?>
            <div class="alert alert-success">
                ✅ Log file cleared successfully!
            </div>
        <?php endif; ?>

        <div class="nav-tabs">
            <?php foreach ($available_logs as $log_key => $log_name): ?>
                <a href="?log=<?php echo $log_key; ?>"
                   class="nav-tab <?php echo $current_log === $log_key ? 'active' : ''; ?>">
                    <?php echo $log_name; ?>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="log-info">
            <div class="log-stats">
                <div class="log-stat">
                    <strong>File:</strong> <?php echo basename($log_file); ?>
                </div>
                <div class="log-stat">
                    <strong>Size:</strong> <?php echo $file_size_kb; ?> KB
                </div>
                <div class="log-stat">
                    <strong>Lines:</strong> <?php echo $line_count; ?>
                </div>
                <div class="log-stat">
                    <strong>Last Modified:</strong> <?php echo $last_modified; ?>
                </div>
            </div>

            <div class="log-actions">
                <button onclick="location.reload()" class="btn btn-primary">🔄 Refresh</button>
                <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to clear this log file?');">
                    <input type="hidden" name="log" value="<?php echo htmlspecialchars($current_log); ?>">
                    <button type="submit" name="clear_log" class="btn btn-danger">🗑️ Clear Log</button>
                </form>
            </div>
        </div>

        <div class="log-container">
            <div class="log-content"><?php echo htmlspecialchars($log_content); ?></div>
        </div>

        <a href="index.php" class="btn btn-secondary back-link">← Back to Application</a>
    </div>

    <script>
        // Auto-scroll to bottom on page load
        window.addEventListener('load', function() {
            const logContent = document.querySelector('.log-content');
            logContent.scrollTop = logContent.scrollHeight;
        });

        // Highlight error levels
        const logContent = document.querySelector('.log-content');
        let html = logContent.innerHTML;

        // Color coding for different log levels
        html = html.replace(/\[ERROR\]/g, '<span style="color: #f48771; font-weight: bold;">[ERROR]</span>');
        html = html.replace(/\[WARNING\]/g, '<span style="color: #dcdcaa; font-weight: bold;">[WARNING]</span>');
        html = html.replace(/\[NOTICE\]/g, '<span style="color: #4ec9b0; font-weight: bold;">[NOTICE]</span>');
        html = html.replace(/\[FATAL ERROR\]/g, '<span style="color: #f14c4c; font-weight: bold; background: #5a1d1d; padding: 2px 6px;">[FATAL ERROR]</span>');
        html = html.replace(/\[EXCEPTION\]/g, '<span style="color: #ce9178; font-weight: bold;">[EXCEPTION]</span>');

        // Highlight dates/timestamps
        html = html.replace(/(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})/g, '<span style="color: #569cd6;">$1</span>');

        logContent.innerHTML = html;
    </script>
</body>
</html>
