<?php
/**
 * Custom Error Logging System
 * This file enables detailed error logging for debugging
 */

// Set custom error log file location
$error_log_file = __DIR__ . '/logs/php_errors.log';
$debug_log_file = __DIR__ . '/logs/debug.log';

// Create logs directory if it doesn't exist
if (!file_exists(__DIR__ . '/logs')) {
    mkdir(__DIR__ . '/logs', 0755, true);
}

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors (will break JSON)
ini_set('log_errors', 1);
ini_set('error_log', $error_log_file);

// Custom error handler
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    global $error_log_file;

    $error_types = [
        E_ERROR => 'ERROR',
        E_WARNING => 'WARNING',
        E_PARSE => 'PARSE',
        E_NOTICE => 'NOTICE',
        E_CORE_ERROR => 'CORE_ERROR',
        E_CORE_WARNING => 'CORE_WARNING',
        E_COMPILE_ERROR => 'COMPILE_ERROR',
        E_COMPILE_WARNING => 'COMPILE_WARNING',
        E_USER_ERROR => 'USER_ERROR',
        E_USER_WARNING => 'USER_WARNING',
        E_USER_NOTICE => 'USER_NOTICE',
        E_STRICT => 'STRICT',
        E_RECOVERABLE_ERROR => 'RECOVERABLE_ERROR',
        E_DEPRECATED => 'DEPRECATED',
        E_USER_DEPRECATED => 'USER_DEPRECATED'
    ];

    $type = isset($error_types[$errno]) ? $error_types[$errno] : 'UNKNOWN';

    $message = sprintf(
        "[%s] %s: %s in %s on line %d\n",
        date('Y-m-d H:i:s'),
        $type,
        $errstr,
        $errfile,
        $errline
    );

    error_log($message, 3, $error_log_file);

    // Don't execute PHP internal error handler
    return true;
}

// Custom exception handler
function customExceptionHandler($exception) {
    global $error_log_file;

    $message = sprintf(
        "[%s] EXCEPTION: %s in %s on line %d\nStack trace:\n%s\n",
        date('Y-m-d H:i:s'),
        $exception->getMessage(),
        $exception->getFile(),
        $exception->getLine(),
        $exception->getTraceAsString()
    );

    error_log($message, 3, $error_log_file);
}

// Set custom handlers
set_error_handler('customErrorHandler');
set_exception_handler('customExceptionHandler');

// Debug logging function
function debug_log($message, $data = null) {
    global $debug_log_file;

    $log_message = sprintf(
        "[%s] %s",
        date('Y-m-d H:i:s'),
        $message
    );

    if ($data !== null) {
        $log_message .= "\nData: " . print_r($data, true);
    }

    $log_message .= "\n";

    error_log($log_message, 3, $debug_log_file);
}

// Shutdown function to catch fatal errors
function shutdownHandler() {
    global $error_log_file;

    $error = error_get_last();

    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        $message = sprintf(
            "[%s] FATAL ERROR: %s in %s on line %d\n",
            date('Y-m-d H:i:s'),
            $error['message'],
            $error['file'],
            $error['line']
        );

        error_log($message, 3, $error_log_file);
    }
}

register_shutdown_function('shutdownHandler');
?>
