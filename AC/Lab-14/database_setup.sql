-- Lab 14: IDOR Banner Deletion Vulnerability
-- Database Schema for Revive Adserver Simulation

CREATE DATABASE IF NOT EXISTS ac_lab14;
USE ac_lab14;

-- Drop existing tables
DROP TABLE IF EXISTS banners;
DROP TABLE IF EXISTS campaigns;
DROP TABLE IF EXISTS clients;
DROP TABLE IF EXISTS managers;
DROP TABLE IF EXISTS csrf_tokens;
DROP TABLE IF EXISTS deletion_logs;

-- Managers table (users who manage ad campaigns)
CREATE TABLE managers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    agency VARCHAR(100) DEFAULT 'Default Agency',
    role ENUM('manager', 'admin') DEFAULT 'manager',
    api_key VARCHAR(64),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);

-- Clients table (advertisers that managers handle)
CREATE TABLE clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    manager_id INT NOT NULL,
    client_name VARCHAR(100) NOT NULL,
    contact_email VARCHAR(100),
    contact_phone VARCHAR(20),
    company VARCHAR(100),
    budget DECIMAL(10,2) DEFAULT 0.00,
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (manager_id) REFERENCES managers(id) ON DELETE CASCADE
);

-- Campaigns table (ad campaigns under clients)
CREATE TABLE campaigns (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    campaign_name VARCHAR(100) NOT NULL,
    description TEXT,
    start_date DATE,
    end_date DATE,
    daily_budget DECIMAL(10,2) DEFAULT 0.00,
    total_impressions INT DEFAULT 0,
    total_clicks INT DEFAULT 0,
    status ENUM('active', 'paused', 'completed', 'draft') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE
);

-- Banners table (ads within campaigns) - TARGET OF IDOR
CREATE TABLE banners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    campaign_id INT NOT NULL,
    banner_name VARCHAR(100) NOT NULL,
    banner_type ENUM('image', 'html', 'video', 'native') DEFAULT 'image',
    content_url VARCHAR(255),
    target_url VARCHAR(255),
    width INT DEFAULT 300,
    height INT DEFAULT 250,
    impressions INT DEFAULT 0,
    clicks INT DEFAULT 0,
    ctr DECIMAL(5,2) DEFAULT 0.00,
    status ENUM('active', 'paused', 'pending', 'rejected') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (campaign_id) REFERENCES campaigns(id) ON DELETE CASCADE
);

-- CSRF tokens table
CREATE TABLE csrf_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    manager_id INT NOT NULL,
    token VARCHAR(64) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NULL,
    FOREIGN KEY (manager_id) REFERENCES managers(id) ON DELETE CASCADE
);

-- Deletion logs for audit trail
CREATE TABLE deletion_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    manager_id INT NOT NULL,
    action VARCHAR(50) NOT NULL,
    target_type VARCHAR(50) NOT NULL,
    target_id INT NOT NULL,
    target_name VARCHAR(100),
    client_id_used INT,
    campaign_id_used INT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (manager_id) REFERENCES managers(id) ON DELETE CASCADE
);

-- Insert sample managers
INSERT INTO managers (username, password, email, full_name, agency, role, api_key) VALUES
('manager_a', 'attacker123', 'manager.a@agency-x.com', 'Alice Anderson', 'Agency X', 'manager', 'mgrA-api-key-9a8b7c6d5e4f3g2h1i0j'),
('manager_b', 'victim456', 'manager.b@agency-y.com', 'Bob Builder', 'Agency Y', 'manager', 'mgrB-api-key-1a2b3c4d5e6f7g8h9i0j'),
('manager_c', 'charlie789', 'manager.c@agency-x.com', 'Charlie Chen', 'Agency X', 'manager', 'mgrC-api-key-0a1b2c3d4e5f6g7h8i9j'),
('admin', 'admin', 'admin@revive.local', 'System Administrator', 'Revive Corp', 'admin', 'admin-api-key-xyz123abc456def789');

-- Insert clients for Manager A (ID: 1)
INSERT INTO clients (manager_id, client_name, contact_email, contact_phone, company, budget, status) VALUES
(1, 'TechCorp Ads', 'ads@techcorp.com', '555-0101', 'TechCorp Inc.', 50000.00, 'active'),
(1, 'FoodBrand Marketing', 'marketing@foodbrand.com', '555-0102', 'FoodBrand LLC', 25000.00, 'active');

-- Insert clients for Manager B (ID: 2) - VICTIM
INSERT INTO clients (manager_id, client_name, contact_email, contact_phone, company, budget, status) VALUES
(2, 'MegaRetail Promotions', 'promo@megaretail.com', '555-0201', 'MegaRetail Corp', 100000.00, 'active'),
(2, 'HealthPlus Campaigns', 'campaigns@healthplus.com', '555-0202', 'HealthPlus Inc.', 75000.00, 'active');

-- Insert clients for Manager C (ID: 3)
INSERT INTO clients (manager_id, client_name, contact_email, contact_phone, company, budget, status) VALUES
(3, 'AutoMax Advertising', 'ads@automax.com', '555-0301', 'AutoMax Motors', 60000.00, 'active');

-- Insert campaigns for Manager A's clients
INSERT INTO campaigns (client_id, campaign_name, description, start_date, end_date, daily_budget, total_impressions, total_clicks, status) VALUES
(1, 'Summer Tech Sale', 'Q3 Summer promotion for tech products', '2025-06-01', '2025-08-31', 500.00, 150000, 4500, 'active'),
(1, 'Black Friday Prep', 'Early Black Friday awareness campaign', '2025-10-01', '2025-11-30', 750.00, 0, 0, 'draft'),
(2, 'Organic Food Launch', 'New organic product line launch', '2025-07-01', '2025-09-30', 300.00, 85000, 2100, 'active');

-- Insert campaigns for Manager B's clients (VICTIM)
INSERT INTO campaigns (client_id, campaign_name, description, start_date, end_date, daily_budget, total_impressions, total_clicks, status) VALUES
(3, 'Holiday Mega Sale', 'Christmas and New Year mega sale event', '2025-11-15', '2026-01-15', 1500.00, 500000, 15000, 'active'),
(3, 'Spring Collection', 'Spring 2026 fashion collection launch', '2026-02-01', '2026-04-30', 800.00, 0, 0, 'draft'),
(4, 'Wellness Program', 'Annual wellness program promotion', '2025-01-01', '2025-12-31', 400.00, 320000, 9600, 'active');

-- Insert campaigns for Manager C's clients
INSERT INTO campaigns (client_id, campaign_name, description, start_date, end_date, daily_budget, total_impressions, total_clicks, status) VALUES
(5, 'New Car Launch', '2026 Model year vehicle launch', '2025-09-01', '2025-12-31', 1000.00, 250000, 7500, 'active');

-- Insert banners for Manager A's campaigns (Campaign IDs: 1, 2, 3)
INSERT INTO banners (campaign_id, banner_name, banner_type, content_url, target_url, width, height, impressions, clicks, status) VALUES
(1, 'TechCorp-Summer-Banner-1', 'image', '/banners/tech_summer_1.jpg', 'https://techcorp.com/summer-sale', 728, 90, 50000, 1500, 'active'),
(1, 'TechCorp-Summer-Banner-2', 'image', '/banners/tech_summer_2.jpg', 'https://techcorp.com/deals', 300, 250, 45000, 1200, 'active'),
(1, 'TechCorp-Summer-Video', 'video', '/banners/tech_summer.mp4', 'https://techcorp.com/watch', 640, 360, 55000, 1800, 'active'),
(2, 'TechCorp-BF-Draft', 'image', '/banners/tech_bf_draft.jpg', 'https://techcorp.com/bf', 300, 600, 0, 0, 'pending'),
(3, 'FoodBrand-Organic-1', 'image', '/banners/food_organic_1.jpg', 'https://foodbrand.com/organic', 300, 250, 42500, 1050, 'active');

-- Insert banners for Manager B's campaigns (Campaign IDs: 4, 5, 6) - VICTIM'S BANNERS
INSERT INTO banners (campaign_id, banner_name, banner_type, content_url, target_url, width, height, impressions, clicks, status) VALUES
(4, 'MegaRetail-Holiday-Hero', 'image', '/banners/mega_holiday_hero.jpg', 'https://megaretail.com/holiday', 970, 250, 200000, 6000, 'active'),
(4, 'MegaRetail-Holiday-Sidebar', 'image', '/banners/mega_holiday_side.jpg', 'https://megaretail.com/deals', 300, 600, 150000, 4500, 'active'),
(4, 'MegaRetail-Holiday-Mobile', 'html', '/banners/mega_holiday_mobile.html', 'https://megaretail.com/m/holiday', 320, 50, 150000, 4500, 'active'),
(5, 'MegaRetail-Spring-Draft', 'image', '/banners/mega_spring_draft.jpg', 'https://megaretail.com/spring', 300, 250, 0, 0, 'pending'),
(6, 'HealthPlus-Wellness-Main', 'image', '/banners/health_wellness.jpg', 'https://healthplus.com/wellness', 728, 90, 160000, 4800, 'active'),
(6, 'HealthPlus-Wellness-Native', 'native', '/banners/health_native.json', 'https://healthplus.com/learn', 300, 250, 160000, 4800, 'active');

-- Insert banners for Manager C's campaigns (Campaign ID: 7)
INSERT INTO banners (campaign_id, banner_name, banner_type, content_url, target_url, width, height, impressions, clicks, status) VALUES
(7, 'AutoMax-NewCar-Billboard', 'image', '/banners/auto_billboard.jpg', 'https://automax.com/2026', 970, 90, 125000, 3750, 'active'),
(7, 'AutoMax-NewCar-Square', 'video', '/banners/auto_square.mp4', 'https://automax.com/test-drive', 300, 300, 125000, 3750, 'active');

-- Summary of IDs for reference:
-- Manager A (ID: 1): Clients 1-2, Campaigns 1-3, Banners 1-5
-- Manager B (ID: 2): Clients 3-4, Campaigns 4-6, Banners 6-11 (VICTIM)
-- Manager C (ID: 3): Client 5, Campaign 7, Banners 12-13
