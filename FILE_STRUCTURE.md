# VAW Data Consolidator - File Structure & Documentation

## Complete File List

```
vaw_consolidator/
│
├── index.php                    # Main application interface
├── config.php                   # Configuration settings
├── db.php                       # Database connection
├── insert.php                   # INSERT operations
├── update.php                   # UPDATE operations
├── delete.php                   # DELETE operations
├── get_data.php                 # SELECT/READ operations
├── style.css                    # All CSS styling
├── script.js                    # All JavaScript code
├── database.sql                 # Database schema
├── SETUP_INSTRUCTIONS.md        # Installation guide
└── FILE_STRUCTURE.md            # This file
```

## File Descriptions

### 1. index.php
**Purpose:** Main HTML interface with 3 tabs

**Features:**
- Tab 1: Add Assessment Form
- Tab 2: View All Assessments Table
- Tab 3: Reports & Statistics
- Uses SweetAlert2 for notifications
- Responsive design

**Dependencies:**
- style.css (styling)
- script.js (functionality)
- config.php (app settings)
- SweetAlert2 CDN

---

### 2. config.php
**Purpose:** Central configuration file

**Contains:**
- Database credentials
- Application name and settings
- Municipality and province info
- Coordinator name
- Timezone settings

**Usage:** Required by all PHP files

---

### 3. db.php
**Purpose:** Database connection and helper functions

**Functions:**
- `$conn` - MySQLi database connection
- `sanitize_input($data)` - Clean user input
- `send_json_response($success, $message, $data)` - Send JSON responses

**Security:**
- Real escape strings
- HTML special chars
- Prepared statements support

---

### 4. insert.php
**Purpose:** Handle adding new assessments

**Method:** POST

**Required Parameters:**
- rater_id
- barangay_id
- assessment_date
- section1_score (0-25)
- section2_score (0-25)
- section3_score (0-25)
- section4_score (0-25)
- status
- remarks (optional)

**Validations:**
- Required fields check
- Score range validation (0-25)
- Duplicate assessment check
- Automatic total calculation

**Returns:** JSON response
```json
{
  "success": true/false,
  "message": "Success/Error message",
  "data": {
    "id": 123,
    "total_score": 85.5
  }
}
```

---

### 5. update.php
**Purpose:** Handle updating existing assessments

**Method:** POST

**Required Parameters:**
- id (assessment ID)
- assessment_date
- section1_score (0-25)
- section2_score (0-25)
- section3_score (0-25)
- section4_score (0-25)
- status
- remarks

**Validations:**
- ID exists
- Score range validation
- Automatic total recalculation

**Returns:** JSON response
```json
{
  "success": true/false,
  "message": "Success/Error message",
  "data": {
    "id": 123,
    "total_score": 88.0
  }
}
```

---

### 6. delete.php
**Purpose:** Handle deleting assessments

**Method:** GET

**Required Parameters:**
- id (assessment ID)

**Validations:**
- ID exists check
- Confirm before delete

**Returns:** JSON response
```json
{
  "success": true/false,
  "message": "Success/Error message"
}
```

---

### 7. get_data.php
**Purpose:** Fetch all data from database

**Method:** GET

**Actions:**

#### a) Get Raters
```
get_data.php?action=raters
```
Returns all raters with id, name, email, contact

#### b) Get Barangays
```
get_data.php?action=barangays
```
Returns all barangays with id, name, municipality, province

#### c) Get All Assessments
```
get_data.php?action=assessments
```
Returns all assessments with joins to raters and barangays

#### d) Get Single Assessment
```
get_data.php?action=assessment&id=123
```
Returns single assessment details

#### e) Get Reports
```
get_data.php?action=reports
```
Returns comprehensive statistics:
- Total raters, barangays, assessments
- Average scores
- Stats by barangay
- Stats by rater

---

### 8. style.css
**Purpose:** All CSS styling (separated from HTML)

**Sections:**
- Reset & base styles
- Layout (container, header, content)
- Navigation tabs
- Forms (inputs, selects, textareas, buttons)
- Tables
- Cards
- Status badges
- Loading spinners
- Alerts
- Responsive design (mobile)

**Design Features:**
- Gradient backgrounds
- Smooth transitions
- Hover effects
- Modern, clean interface
- Mobile-responsive

---

### 9. script.js
**Purpose:** All JavaScript functionality (separated from HTML)

**Functions:**

#### Navigation
- `switchTab(tabName)` - Switch between tabs

#### Assessment Operations
- `addAssessment()` - Submit new assessment
- `loadAssessments()` - Fetch and display all assessments
- `editAssessment(id)` - Open edit dialog
- `updateAssessment(id, data)` - Update assessment
- `deleteAssessment(id)` - Delete with confirmation

#### Reports
- `loadReports()` - Load statistics and reports

#### Utilities
- `loadDropdowns()` - Populate rater and barangay selects

**Features:**
- SweetAlert2 integration
- Fetch API for AJAX
- Form validation
- Error handling
- Loading states

---

### 10. database.sql
**Purpose:** Database schema and initial data

**Creates:**
- Database: vaw_consolidator
- Table: raters
- Table: barangays
- Table: assessments
- Table: assessment_details (for future)

**Initial Data:**
- 35 barangays from Baggao
- 3 sample raters

**Relationships:**
- assessments.rater_id → raters.id (FK)
- assessments.barangay_id → barangays.id (FK)

---

## Data Flow

### Adding Assessment
```
User fills form →
script.js addAssessment() →
Fetch POST to insert.php →
Validate & sanitize →
Insert to database →
Return JSON response →
SweetAlert notification →
Reload assessments table
```

### Viewing Assessments
```
Page load/Tab click →
script.js loadAssessments() →
Fetch GET to get_data.php?action=assessments →
Query with JOINs →
Return JSON data →
Populate HTML table
```

### Editing Assessment
```
Click Edit button →
script.js editAssessment(id) →
Fetch GET to get_data.php?action=assessment&id=X →
Get assessment data →
Show SweetAlert dialog with form →
User edits → Click Update →
script.js updateAssessment() →
Fetch POST to update.php →
Update database →
Return JSON response →
SweetAlert notification →
Reload table
```

### Deleting Assessment
```
Click Delete button →
script.js deleteAssessment(id) →
SweetAlert confirmation →
If confirmed →
Fetch GET to delete.php?id=X →
Delete from database →
Return JSON response →
SweetAlert notification →
Reload table
```

### Viewing Reports
```
Click Reports tab →
script.js loadReports() →
Fetch GET to get_data.php?action=reports →
Multiple aggregate queries →
Calculate statistics →
Return JSON data →
Display summary cards and tables
```

---

## API Endpoints Summary

| Endpoint | Method | Purpose | Parameters |
|----------|--------|---------|------------|
| insert.php | POST | Add assessment | rater_id, barangay_id, date, scores, status, remarks |
| update.php | POST | Update assessment | id, date, scores, status, remarks |
| delete.php | GET | Delete assessment | id |
| get_data.php?action=raters | GET | Get all raters | - |
| get_data.php?action=barangays | GET | Get all barangays | - |
| get_data.php?action=assessments | GET | Get all assessments | - |
| get_data.php?action=assessment | GET | Get one assessment | id |
| get_data.php?action=reports | GET | Get statistics | - |

---

## Database Schema

### raters
| Column | Type | Description |
|--------|------|-------------|
| id | INT PK AI | Unique identifier |
| name | VARCHAR(255) | Rater name |
| email | VARCHAR(255) | Email address |
| contact_number | VARCHAR(50) | Phone number |
| created_at | TIMESTAMP | Created date |
| updated_at | TIMESTAMP | Last updated |

### barangays
| Column | Type | Description |
|--------|------|-------------|
| id | INT PK AI | Unique identifier |
| name | VARCHAR(255) | Barangay name |
| municipality | VARCHAR(255) | Municipality (Baggao) |
| province | VARCHAR(255) | Province (Cagayan) |
| created_at | TIMESTAMP | Created date |

### assessments
| Column | Type | Description |
|--------|------|-------------|
| id | INT PK AI | Unique identifier |
| rater_id | INT FK | Foreign key to raters |
| barangay_id | INT FK | Foreign key to barangays |
| assessment_date | DATE | Date of assessment |
| section1_score | DECIMAL(5,2) | Score for section 1 (0-25) |
| section2_score | DECIMAL(5,2) | Score for section 2 (0-25) |
| section3_score | DECIMAL(5,2) | Score for section 3 (0-25) |
| section4_score | DECIMAL(5,2) | Score for section 4 (0-25) |
| total_score | DECIMAL(5,2) | Total score (0-100) |
| status | ENUM | pending, in_progress, completed |
| remarks | TEXT | Additional notes |
| created_at | TIMESTAMP | Created date |
| updated_at | TIMESTAMP | Last updated |

---

## Key Features

✅ **No Local Storage** - All data stored in MySQL database
✅ **No MIT App Inventor** - Pure web application
✅ **No Android Studio** - Browser-based system
✅ **Non-MVC Structure** - Simple procedural PHP
✅ **Separated Files** - CSS and JS in separate files
✅ **SweetAlert Integration** - Beautiful notifications
✅ **XAMPP Compatible** - Works with XAMPP out of the box
✅ **Pure PHP & JavaScript** - No frameworks required
✅ **CRUD Operations** - Create, Read, Update, Delete
✅ **Responsive Design** - Works on mobile and desktop

---

## Customization Guide

### Change Database Credentials
Edit `config.php`:
```php
define('DB_HOST', 'your_host');
define('DB_USER', 'your_user');
define('DB_PASS', 'your_password');
define('DB_NAME', 'your_database');
```

### Change Application Name
Edit `config.php`:
```php
define('APP_NAME', 'Your App Name');
define('APP_MUNICIPALITY', 'Your Municipality');
define('COORDINATOR_NAME', 'Your Name');
```

### Change Colors
Edit `style.css` - look for gradient colors:
```css
background: linear-gradient(135deg, #0066CC 0%, #004C99 100%);
```

### Add More Validations
Edit `insert.php` or `update.php` - add validation logic before database operations

### Modify Reports
Edit `get_data.php` - modify SQL queries in `getReports()` function

---

## Version Information

- **Version:** 1.0
- **Structure:** Non-MVC (Procedural PHP)
- **Database:** MySQL
- **Server:** Apache (XAMPP)
- **Dependencies:** SweetAlert2 (CDN)

---

**Developed for:** Municipality of Baggao, Cagayan
**Coordinator:** Richmond Rosete
**System:** VAW Data Consolidator
