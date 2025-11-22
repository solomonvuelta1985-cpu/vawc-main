# Logs Directory

This directory contains all system logs for the VAW Data Consolidator application.

## Log Files

### php_errors.log
**Purpose**: Records all PHP errors, warnings, notices, and exceptions

**Contains**:
- Fatal errors
- Warnings
- Notices
- Exceptions
- Parse errors
- Database errors

**Example Entry**:
```
[2025-11-22 17:51:01] EXCEPTION: Database connection failed
Stack trace:
#0 /path/to/file.php(42): function_name()
#1 {main}
```

### debug.log
**Purpose**: Custom debug messages for troubleshooting

**Contains**:
- Debug messages
- Variable dumps
- Function call traces
- Custom log entries

**Example Entry**:
```
[2025-11-22 17:51:01] User login attempt
Data: Array
(
    [user_id] => 1
    [username] => Richmond Rosete
)
```

## Viewing Logs

### Option 1: Web Interface (Recommended)
Access the log viewer at: `http://localhost/vawc-main/view_logs.php`

Features:
- Syntax highlighting
- Real-time refresh
- Clear logs functionality
- File statistics
- Auto-scroll to latest entries

### Option 2: Direct File Access
Open files directly with a text editor:
- Location: `c:\xampp\htdocs\vawc-main\logs\`
- Files: `php_errors.log`, `debug.log`

### Option 3: Command Line
```bash
# View last 50 lines of error log
tail -50 c:\xampp\htdocs\vawc-main\logs\php_errors.log

# Follow log in real-time
tail -f c:\xampp\htdocs\vawc-main\logs\php_errors.log
```

## Maintenance

### Clearing Logs
**Via Web Interface**:
1. Go to `view_logs.php`
2. Select the log to clear
3. Click "Clear Log" button

**Manual Method**:
- Delete or empty the log files
- They will be recreated automatically

### Log Rotation
For production, consider implementing log rotation:
- Archive old logs monthly
- Keep only last 3 months
- Compress archived logs

## Security

### Protection Measures
- `.htaccess` file prevents direct web access
- Only accessible through log viewer (requires login)
- Logs stored outside web root in production

### Sensitive Data
**Do NOT log**:
- Passwords
- PINs
- Credit card numbers
- Personal identification numbers

## Troubleshooting

### Log File Not Created
**Cause**: Permission issues

**Solution**:
```bash
# Make logs directory writable
chmod 755 c:\xampp\htdocs\vawc-main\logs
```

### Can't View Logs
**Cause**: Not logged in

**Solution**:
- Login at `http://localhost/vawc-main/login.php`
- Use PIN from setup guide

### Empty Logs
**Cause**: No errors yet (good!) or logging disabled

**Solution**:
- Check if error_log.php is included in db.php
- Verify file permissions

## Custom Logging

To add custom debug messages in your code:

```php
// In any PHP file, after including db.php
debug_log('User performed action', [
    'user_id' => $_SESSION['rater_id'],
    'action' => 'submit_assessment',
    'timestamp' => date('Y-m-d H:i:s')
]);
```

## File Management

### Recommended Limits
- **Max file size**: 10 MB
- **Max lines**: 50,000 lines
- **Retention**: 30 days

### When to Clear
- After fixing known issues
- When file size exceeds 10 MB
- Monthly maintenance
- Before major deployments

---
**Last Updated**: 2025-11-22
**Location**: c:\xampp\htdocs\vawc-main\logs\
