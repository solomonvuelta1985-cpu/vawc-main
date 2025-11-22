-- VAW Data Consolidator Database Schema
-- Create this database in phpMyAdmin or MySQL
CREATE DATABASE IF NOT EXISTS vaw_consolidator;
USE vaw_consolidator;

-- Table for storing raters/assessors
CREATE TABLE IF NOT EXISTS raters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    contact_number VARCHAR(50),
    pin VARCHAR(4) NOT NULL UNIQUE,
    position VARCHAR(100) DEFAULT 'Assessor',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table for storing barangays
CREATE TABLE IF NOT EXISTS barangays (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    municipality VARCHAR(255) DEFAULT 'Baggao',
    province VARCHAR(255) DEFAULT 'Cagayan',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table for storing assessments
CREATE TABLE IF NOT EXISTS assessments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rater_id INT NOT NULL,
    barangay_id INT NOT NULL,
    assessment_date DATE NOT NULL,
    section1_score DECIMAL(5,2) DEFAULT 0.00,
    section2_score DECIMAL(5,2) DEFAULT 0.00,
    section3_score DECIMAL(5,2) DEFAULT 0.00,
    section4_score DECIMAL(5,2) DEFAULT 0.00,
    total_score DECIMAL(5,2) DEFAULT 0.00,
    status ENUM('pending', 'in_progress', 'completed') DEFAULT 'pending',
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (rater_id) REFERENCES raters(id) ON DELETE CASCADE,
    FOREIGN KEY (barangay_id) REFERENCES barangays(id) ON DELETE CASCADE,
    UNIQUE KEY unique_assessment (rater_id, barangay_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table for storing detailed responses (optional - for future expansion)
CREATE TABLE IF NOT EXISTS assessment_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    assessment_id INT NOT NULL,
    section_number INT NOT NULL,
    question_number INT NOT NULL,
    question_text TEXT,
    answer TEXT,
    score DECIMAL(5,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (assessment_id) REFERENCES assessments(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert all 48 barangays of Baggao, Cagayan
INSERT INTO barangays (name) VALUES
('Adaoag'),
('Agaman (Proper)'),
('Agaman Norte'),
('Agaman Sur'),
('Alba'),
('Annayatan'),
('Asassi'),
('Asinga-Via'),
('Awallan'),
('Bacagan'),
('Bagunot'),
('Barsat East'),
('Barsat West'),
('Bitag Grande'),
('Bitag Pequeño'),
('Bunugan'),
('C. Verzosa (Valley Cove)'),
('Canagatan'),
('Carupian'),
('Catugay'),
('Dabbac Grande'),
('Dalin'),
('Dalla'),
('Hacienda Intal'),
('Ibulo'),
('Immurung'),
('J. Pallagao'),
('Lasilat'),
('Mabini'),
('Masical'),
('Mocag'),
('Nangalinan'),
('Poblacion (Centro)'),
('Remus'),
('San Antonio'),
('San Francisco'),
('San Isidro'),
('San Jose'),
('San Miguel'),
('San Vicente'),
('Santa Margarita'),
('Santor'),
('Taguing'),
('Taguntungan'),
('Tallang'),
('Taytay'),
('Temblique'),
('Tungel');

-- Insert authorized raters/assessors with PINs
-- VAW Assessment Team for Municipality of Baggao
INSERT INTO raters (name, email, pin, position) VALUES
('PNP BAGGAO', 'pnp.baggao@baggao.gov.ph', '1001', 'ASSESSOR 1'),
('MLGOO', 'mlgoo@baggao.gov.ph', '1002', 'ASSESSOR 2'),
('MSWDO1', 'mswdo1@baggao.gov.ph', '1003', 'ASSESSOR 3'),
('MSDWDO2', 'mswdo2@baggao.gov.ph', '1004', 'ASSESSOR 4'),
('WOMEN''S ORG', 'womens.org@baggao.gov.ph', '1005', 'ASSESSOR 5'),
('SB REPRESENTATIVE', 'sb.rep@baggao.gov.ph', '1006', 'ASSESSOR 6'),
('CHAIRMAN (PS)', 'chairman.ps@baggao.gov.ph', '1007', 'ASSESSOR 7'),
('MHO', 'mho@baggao.gov.ph', '1008', 'ASSESSOR 8'),
('GAD FOCAL PERSON', 'gad.focal@baggao.gov.ph', '1009', 'ASSESSOR 9'),
('ADMINISTRATOR', 'admin@baggao.gov.ph', '3030', 'SYSTEM ADMINISTRATOR');
