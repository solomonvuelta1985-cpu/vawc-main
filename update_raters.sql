-- Update Raters Script
-- Run this SQL script if you already set up the database with sample raters
-- This will update the raters table with the real VAW Assessment Team

USE vaw_consolidator;

-- First, delete all existing raters (this will also delete related assessments due to CASCADE)
-- WARNING: This will delete all existing assessments!
-- If you have important data, backup first!
DELETE FROM raters;

-- Reset auto-increment to start from 1
ALTER TABLE raters AUTO_INCREMENT = 1;

-- Insert the real VAW Assessment Team for Municipality of Baggao
INSERT INTO raters (name, email, pin, position) VALUES
('PNP BAGGAO', 'pnp.baggao@baggao.gov.ph', '1001', 'ASSESSOR 1'),
('MLGOO', 'mlgoo@baggao.gov.ph', '1002', 'ASSESSOR 2'),
('MSWDO1', 'mswdo1@baggao.gov.ph', '1003', 'ASSESSOR 3'),
('MSDWDO2', 'mswdo2@baggao.gov.ph', '1004', 'ASSESSOR 4'),
('WOMEN''S ORG', 'womens.org@baggao.gov.ph', '1005', 'ASSESSOR 5'),
('SB REPRESENTATIVE', 'sb.rep@baggao.gov.ph', '1006', 'ASSESSOR 6'),
('CHAIRMAN (PS)', 'chairman.ps@baggao.gov.ph', '1007', 'ASSESSOR 7'),
('MHO', 'mho@baggao.gov.ph', '1008', 'ASSESSOR 8'),
('GAD FOCAL PERSON', 'gad.focal@baggao.gov.ph', '1009', 'ASSESSOR 9');

-- Verify the update
SELECT * FROM raters ORDER BY id;
