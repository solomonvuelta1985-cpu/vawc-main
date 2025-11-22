# VAW Data Consolidator - Setup Instructions

## System Requirements

- XAMPP (Apache + MySQL + PHP)
- Web Browser (Chrome, Firefox, Edge, etc.)

## Installation Steps

### 1. Install XAMPP

1. Download XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. Install XAMPP on your computer
3. Start Apache and MySQL from XAMPP Control Panel

### 2. Create Database

1. Open phpMyAdmin in your browser: `http://localhost/phpmyadmin`
2. Click on "Import" tab
3. Click "Choose File" and select the `database.sql` file
4. Click "Go" button to execute the SQL script
5. The database `vaw_consolidator` will be created with all tables and sample data

**OR** you can manually create the database:
1. Click "New" in phpMyAdmin
2. Create a database named `vaw_consolidator`
3. Select the database
4. Click "Import" and import the `database.sql` file

### 3. Configure Application

1. Open `config.php` file
2. Verify the database settings:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');  // Default XAMPP has no password
   define('DB_NAME', 'vaw_consolidator');
   ```
3. Change these settings if your XAMPP has different credentials

### 4. Deploy Application

1. Copy all project files to XAMPP's `htdocs` folder
   - Default location: `C:\xampp\htdocs\vaw_consolidator\`
2. Make sure all these files are in the folder:
   - index.php
   - config.php
   - db.php
   - insert.php
   - update.php
   - delete.php
   - get_data.php
   - style.css
   - script.js
   - database.sql

### 5. Access Application

1. Open your web browser
2. Go to: `http://localhost/vaw_consolidator/index.php`
3. The application should now be running!

## Default Database Contents

The database comes pre-populated with:

- **35 Barangays** from Baggao, Cagayan
- **3 Sample Raters** (you can add more)

## Application Structure

```
vaw_consolidator/
│
├── index.php              # Main application page (with tabs)
├── config.php             # Database and app configuration
├── db.php                 # Database connection and helper functions
│
├── insert.php             # Handles adding new assessments
├── update.php             # Handles updating assessments
├── delete.php             # Handles deleting assessments
├── get_data.php           # Handles fetching all data
│
├── style.css              # All styling (separated)
├── script.js              # All JavaScript logic (separated)
│
└── database.sql           # Database schema and initial data
```

## Features

### 1. Add Assessment Tab
- Select rater/assessor
- Select barangay
- Enter assessment date
- Enter scores for 4 sections (0-25 each)
- Add remarks
- Automatic total calculation
- SweetAlert notifications

### 2. View Assessments Tab
- View all assessments in a table
- Edit any assessment
- Delete assessments (with confirmation)
- Shows rater, barangay, date, scores, and status

### 3. Reports Tab
- Overall statistics summary
- Total raters, barangays, assessments
- Average scores
- Stats by barangay
- Stats by rater
- Assessment status breakdown

## Using the Application

### Adding an Assessment

1. Click "Add Assessment" tab
2. Select a rater from dropdown
3. Select a barangay from dropdown
4. Choose the assessment date
5. Enter scores for each section (0-25)
6. Select status (Pending/In Progress/Completed)
7. Add any remarks (optional)
8. Click "Add Assessment"
9. You'll see a success message with SweetAlert

### Editing an Assessment

1. Go to "View Assessments" tab
2. Find the assessment you want to edit
3. Click "Edit" button
4. Modify the data in the SweetAlert popup
5. Click "Update"
6. Assessment will be updated

### Deleting an Assessment

1. Go to "View Assessments" tab
2. Find the assessment you want to delete
3. Click "Delete" button
4. Confirm deletion in SweetAlert popup
5. Assessment will be removed

### Viewing Reports

1. Click "Reports" tab
2. View overall statistics
3. See detailed breakdown by barangay
4. See detailed breakdown by rater
5. Analyze assessment progress

## Database Structure

### Tables

1. **raters** - Stores rater/assessor information
2. **barangays** - Stores barangay information
3. **assessments** - Stores all assessment data
4. **assessment_details** - (Future expansion) Detailed question-level data

### Assessment Scores

- Section 1: Establishment (0-25 points)
- Section 2: Resources (0-25 points)
- Section 3: Policies & Plans (0-25 points)
- Section 4: Accomplishments (0-25 points)
- **Total Score: 0-100 points**

## Troubleshooting

### Database Connection Error

- Make sure Apache and MySQL are running in XAMPP
- Check database credentials in `config.php`
- Verify database exists in phpMyAdmin

### Page Not Loading

- Check if files are in the correct folder (`htdocs`)
- Make sure Apache is running
- Try accessing: `http://localhost/vaw_consolidator/index.php`

### SweetAlert Not Showing

- Check internet connection (CDN required for SweetAlert)
- View browser console for errors (F12)

### Data Not Saving

- Check browser console for errors (F12)
- Verify database connection
- Check PHP error logs in XAMPP

## Adding More Raters

You can add more raters directly in the database:

1. Open phpMyAdmin
2. Select `vaw_consolidator` database
3. Click on `raters` table
4. Click "Insert" tab
5. Add rater name, email, and contact number
6. Click "Go"

## Security Notes

This is a basic implementation for local use. For production deployment:

- Change database password
- Add user authentication
- Implement input validation
- Use prepared statements (already implemented)
- Add CSRF protection
- Use HTTPS

## Technology Stack

- **Backend:** PHP (Pure/Procedural - No MVC)
- **Database:** MySQL
- **Frontend:** HTML5, CSS3, JavaScript
- **Alerts:** SweetAlert2
- **Server:** Apache (XAMPP)

## Support

For issues or questions, contact the system administrator.

---

**Developed for:** Municipality of Baggao, Cagayan
**Coordinator:** Richmond Rosete
**Purpose:** VAW Desk Assessment Data Consolidation
