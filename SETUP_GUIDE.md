# VAW Data Consolidator - Setup Guide

## Prerequisites
- XAMPP installed (Apache & MySQL running)
- Web browser (Chrome, Firefox, Edge)

## Step-by-Step Setup Instructions

### 1. Database Setup

#### Option A: Using phpMyAdmin (Recommended)
1. Open your browser and go to: `http://localhost/phpmyadmin`
2. Click on "SQL" tab at the top
3. Copy the entire contents of `database.sql` file
4. Paste it into the SQL query box
5. Click "Go" button to execute
6. You should see a success message

#### Option B: Using MySQL Command Line
1. Open Command Prompt (Windows) or Terminal (Mac/Linux)
2. Navigate to your project folder:
   ```
   cd c:\xampp\htdocs\vawc-main
   ```
3. Run the SQL file:
   ```
   mysql -u root -p < database.sql
   ```
4. Press Enter (leave password blank if you haven't set one)

### 2. Verify Database Setup

1. Go to `http://localhost/phpmyadmin`
2. You should see a database named `vaw_consolidator` in the left sidebar
3. Click on it to expand
4. Verify these tables exist:
   - `raters` (9 sample raters with PINs)
   - `barangays` (34 barangays in Baggao)
   - `assessments` (empty, will store assessment data)
   - `assessment_details` (empty, for future expansion)

### 3. Configuration Check

The `config.php` file should have these default settings:
```php
DB_HOST: localhost
DB_USER: root
DB_PASS: (empty)
DB_NAME: vaw_consolidator
```

If your MySQL has a different username or password, update `config.php` accordingly.

### 4. Start the Application

1. Make sure Apache and MySQL are running in XAMPP Control Panel
2. Open your browser and go to: `http://localhost/vawc-main/login.php`
3. Enter a PIN from the list below to login

### 5. Default User PINs

Use any of these PINs to login (VAW Assessment Team):

| Name | PIN | Position |
|------|-----|----------|
| PNP BAGGAO | 1001 | ASSESSOR 1 |
| MLGOO | 1002 | ASSESSOR 2 |
| MSWDO1 | 1003 | ASSESSOR 3 |
| MSDWDO2 | 1004 | ASSESSOR 4 |
| WOMEN'S ORG | 1005 | ASSESSOR 5 |
| SB REPRESENTATIVE | 1006 | ASSESSOR 6 |
| CHAIRMAN (PS) | 1007 | ASSESSOR 7 |
| MHO | 1008 | ASSESSOR 8 |
| GAD FOCAL PERSON | 1009 | ASSESSOR 9 |

### 6. Start Using the System

Once logged in, you can:
1. **Add Assessment** - Create new VAW desk assessments
2. **View Assessments** - See all submitted assessments
3. **Reports** - View statistics and analytics

## Common Issues & Solutions

### Issue: "Failed to connect to server" error
**Solution:**
- Make sure MySQL is running in XAMPP Control Panel
- Verify database was created by checking phpMyAdmin
- Check that `config.php` has correct database credentials

### Issue: Blank page or white screen
**Solution:**
- Check PHP error log in `c:\xampp\php\logs\php_error_log`
- Make sure all PHP files are in the correct folder
- Verify XAMPP Apache is running

### Issue: "Unauthorized access" message
**Solution:**
- Go to login page: `http://localhost/vawc-main/login.php`
- Enter a valid PIN from the list above

### Issue: No barangays showing in dropdown
**Solution:**
- Run the `database.sql` file again to populate barangays
- Check that `barangays` table has 34 rows in phpMyAdmin

## File Structure

```
vawc-main/
├── authenticate.php           - Handles PIN login
├── config.php                - Database & app configuration
├── database.sql              - Database schema & real VAW team data
├── update_raters.sql         - Update script for real raters
├── db.php                    - Database connection & error logging
├── delete.php                - Delete assessment
├── error_log.php             - Custom error logging engine
├── get_data.php              - Fetch data (API endpoints)
├── index.php                 - Main assessment form (multi-step)
├── insert.php                - Save new assessment
├── login.php                 - PIN login page
├── logout.php                - Logout handler
├── script.js                 - Client-side JavaScript
├── style.css                 - Responsive styles (clean, no gradients)
├── update.php                - Update assessment
├── view_logs.php             - Log viewer interface
├── SETUP_GUIDE.md            - This file
├── RATERS_QUICK_REFERENCE.md - PIN quick reference card
└── logs/                     - Error and debug logs directory
    ├── php_errors.log        - PHP errors & exceptions
    ├── debug.log             - Custom debug messages
    ├── .htaccess             - Security protection
    └── README.md             - Logs documentation
```

## Error Logging & Debugging

### View System Logs
The application includes a comprehensive error logging system. To view logs:

1. **Access Log Viewer**: `http://localhost/vawc-main/view_logs.php`
2. **Available Logs**:
   - **PHP Errors**: Shows all PHP errors, warnings, and exceptions
   - **Debug Log**: Custom debug messages for troubleshooting

### Log Files Location
All logs are stored in: `c:\xampp\htdocs\vawc-main\logs\`

- `php_errors.log` - PHP errors and exceptions
- `debug.log` - Debug messages

### Features
- ✅ Real-time error logging
- ✅ Syntax highlighting for error levels
- ✅ Auto-scroll to latest logs
- ✅ Clear logs functionality
- ✅ File size and line count tracking
- ✅ Protected from public access (.htaccess)

### Debugging Tips
1. **Check logs first** when something isn't working
2. **Refresh logs page** after reproducing an error
3. **Clear logs** after fixing issues to track new ones
4. **Browser console** (F12) for JavaScript errors

## Support

If you encounter any issues not covered here, check:
1. **System Logs**: `http://localhost/vawc-main/view_logs.php`
2. **Browser Console**: Press F12 → Console tab
3. **XAMPP Logs**: Check XAMPP Control Panel → Logs button

---
**System developed for**: Municipality of Baggao, Cagayan
**For**: VAW Desk Assessment & Data Consolidation
