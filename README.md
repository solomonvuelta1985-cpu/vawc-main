# VAW Data Consolidator System

A complete **Violence Against Women (VAW) Data Consolidation System** for Municipality of Baggao, Cagayan with PIN-based authentication, database storage, and modern responsive design.

## 🎯 Features

✅ **PIN-Based Authentication System**
- 4-digit PIN login for 9 authorized assessors
- Session management with PHP
- Secure logout functionality

✅ **CRUD Operations**
- Create new assessments
- Read/View all assessments
- Update existing assessments
- Delete assessments

✅ **Database-Driven**
- MySQL database using XAMPP
- No local storage - all data in database
- 35 pre-loaded barangays from Baggao
- 9 authorized raters with PINs

✅ **Modern Design**
- Clean, minimal interface
- Blue theme (#2c5aa0, #1a73e8, #1e8e3e)
- Fully responsive (mobile-first design)
- SweetAlert2 notifications
- Smooth animations

✅ **Comprehensive Reports**
- Statistics by barangay
- Statistics by rater
- Average scores and totals
- Assessment status tracking

## 📁 File Structure

```
vaw_consolidator/
│
├── login.php              # PIN-based login page
├── index.php             # Main application (with authentication)
├── logout.php            # Logout handler
│
├── config.php            # Configuration settings
├── db.php                # Database connection
├── authenticate.php      # Login authentication
│
├── insert.php            # Add new assessment
├── update.php            # Update assessment
├── delete.php            # Delete assessment
├── get_data.php          # Fetch all data
│
├── style.css             # All CSS styling (Blue theme)
├── script.js             # All JavaScript functionality
│
├── database.sql          # Complete database schema
│
├── README.md             # This file
├── SETUP_INSTRUCTIONS.md # Detailed setup guide
└── FILE_STRUCTURE.md     # Technical documentation
```

## 🚀 Quick Start

### 1. Install XAMPP

Download and install [XAMPP](https://www.apachefriends.org/)

### 2. Import Database

1. Start Apache and MySQL in XAMPP Control Panel
2. Open phpMyAdmin: `http://localhost/phpmyadmin`
3. Click "Import" → Choose `database.sql` → Click "Go"

### 3. Deploy Files

Copy all files to: `C:\xampp\htdocs\vaw_consolidator\`

### 4. Access Application

Open browser and go to: `http://localhost/vaw_consolidator/login.php`

## 🔐 Default Login PINs

| Name | PIN | Position |
|------|-----|----------|
| Richmond Rosete | 1001 | Job Order |
| Maria Santos | 1002 | Assessor |
| Juan Cruz | 1003 | Assessor |
| Ana Reyes | 1004 | Assessor |
| Pedro Garcia | 1005 | Assessor |
| Linda Ramos | 1006 | Assessor |
| Carlos Mendoza | 1007 | Assessor |
| Sofia Torres | 1008 | Assessor |
| Miguel Flores | 1009 | Assessor |

## 💾 Database Structure

### Tables

**1. raters** - Assessor information
- id, name, email, contact_number, pin, position

**2. barangays** - 35 Barangays from Baggao
- id, name, municipality, province

**3. assessments** - All assessment data
- id, rater_id, barangay_id, assessment_date
- section1_score, section2_score, section3_score, section4_score
- total_score, status, remarks

### Assessment Scoring

- **Section 1:** Establishment (0-25 points)
- **Section 2:** Resources (0-25 points)
- **Section 3:** Policies & Plans (0-25 points)
- **Section 4:** Accomplishments (0-25 points)
- **Total Score:** 0-100 points

## 🎨 Design Theme

- **Primary Blue:** #2c5aa0
- **Accent Blue:** #1a73e8
- **Success Green:** #1e8e3e
- **Warning Yellow:** #f9ab00
- **Danger Red:** #d93025
- **Gray:** #5f6368

## 📱 Application Tabs

### 1. Add Assessment Tab
- Select rater and barangay
- Enter assessment date
- Input scores for 4 sections (0-25 each)
- Add remarks (optional)
- Auto-calculates total score
- SweetAlert success confirmation

### 2. View Assessments Tab
- Table showing all assessments
- Displays: ID, Rater, Barangay, Date, Scores, Status
- Edit button (opens SweetAlert dialog)
- Delete button (with confirmation)
- Status badges (Pending, In Progress, Completed)

### 3. Reports Tab
- Summary cards (Total Raters, Barangays, Assessments, Avg Score)
- Assessments by Barangay table
- Assessments by Rater table
- Statistics and analytics

## 🔧 Configuration

Edit `config.php` to customize:

```php
// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'vaw_consolidator');

// Application settings
define('APP_NAME', 'VAW Data Consolidator');
define('APP_MUNICIPALITY', 'Baggao');
define('APP_PROVINCE', 'Cagayan');
define('COORDINATOR_NAME', 'Richmond Rosete');
```

## 📊 API Endpoints

All endpoints return JSON responses:

| Endpoint | Method | Purpose |
|----------|--------|---------|
| `authenticate.php` | POST | Login authentication |
| `insert.php` | POST | Add assessment |
| `update.php` | POST | Update assessment |
| `delete.php` | GET | Delete assessment |
| `get_data.php?action=raters` | GET | Get all raters |
| `get_data.php?action=barangays` | GET | Get all barangays |
| `get_data.php?action=assessments` | GET | Get all assessments |
| `get_data.php?action=assessment&id=X` | GET | Get single assessment |
| `get_data.php?action=reports` | GET | Get statistics |

## 🛡️ Security Features

- ✅ PHP session management
- ✅ Login authentication required
- ✅ Input sanitization
- ✅ Prepared SQL statements
- ✅ XSS protection
- ✅ SQL injection prevention

## 📱 Responsive Design

Optimized for:
- 📱 Mobile phones (320px+)
- 📱 Tablets (768px+)
- 💻 Desktop (1024px+)
- 🖥️ Large screens (1200px+)

Uses CSS `clamp()` for fluid typography and spacing.

## 🎭 SweetAlert Features

Beautiful popups for:
- ✅ Success messages
- ❌ Error messages
- ⚠️ Warnings
- 📝 Edit forms
- ❓ Confirmations
- ⏳ Loading states

## 🔄 Workflow

1. **Login** → Enter 4-digit PIN
2. **Add Assessment** → Fill form and submit
3. **View** → See all assessments in table
4. **Edit** → Click edit button, modify data
5. **Delete** → Click delete with confirmation
6. **Reports** → View statistics and analytics
7. **Logout** → End session

## 🐛 Troubleshooting

### Database Connection Error
- Ensure Apache and MySQL are running in XAMPP
- Check credentials in `config.php`
- Verify database exists in phpMyAdmin

### Page Not Loading
- Files must be in `C:\xampp\htdocs\vaw_consolidator\`
- Access via `http://localhost/vaw_consolidator/login.php`
- Check Apache is running

### SweetAlert Not Showing
- Check internet connection (CDN required)
- Open browser console (F12) for errors

### Login Not Working
- Verify PIN in database `raters` table
- Check PHP session is enabled
- Clear browser cookies

## 📚 Technology Stack

- **Backend:** PHP 7.4+ (Pure/Procedural)
- **Database:** MySQL 5.7+
- **Frontend:** HTML5, CSS3, JavaScript ES6
- **Alerts:** SweetAlert2 (CDN)
- **Server:** Apache (XAMPP)
- **Architecture:** Non-MVC (Simple structure)

## 🎯 Key Differences from Original

### ✅ What Changed:
- ❌ Removed local storage → ✅ MySQL database
- ❌ Removed MIT App Inventor references
- ❌ Removed Android Studio dependencies
- ✅ Added PIN-based authentication
- ✅ Added session management
- ✅ Separated CSS and JavaScript
- ✅ Implemented SweetAlert2
- ✅ Applied vaw_assessment_app.html design theme
- ✅ Made fully responsive
- ✅ Added CRUD operations

### ✅ What Stayed:
- ✔️ Assessment scoring system (4 sections, 0-25 each)
- ✔️ Barangay data (35 barangays)
- ✔️ Report generation concepts
- ✔️ User-friendly interface

## 📖 Additional Documentation

- **[SETUP_INSTRUCTIONS.md](SETUP_INSTRUCTIONS.md)** - Detailed installation guide
- **[FILE_STRUCTURE.md](FILE_STRUCTURE.md)** - Technical documentation
- **[database.sql](database.sql)** - Database schema with comments

## 🤝 Support

For issues or questions:
1. Check troubleshooting section
2. Review documentation files
3. Check browser console (F12)
4. Verify database connection
5. Contact system administrator

## 📝 Notes

- This system is for **local XAMPP deployment**
- For production: Add HTTPS, stronger authentication, CSRF protection
- Default database password is empty (XAMPP default)
- Change PINs in database for security

## 🏆 Credits

**Developed for:** Municipality of Baggao, Cagayan
**Coordinator:** Richmond Rosete
**System:** VAW Data Consolidator 2025
**Design:** Inspired by vaw_assessment_app.html

---

## 📄 License

Developed for internal use by Municipality of Baggao, Cagayan.

---

**Version:** 2.0
**Last Updated:** 2025
**Status:** Production Ready ✅
