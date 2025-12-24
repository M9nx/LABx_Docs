-- Lab 15: IDOR PII Leakage Database Schema
-- Based on MTN MobAd vulnerability - getUserNotes endpoint

DROP DATABASE IF EXISTS ac_lab15;
CREATE DATABASE ac_lab15;
USE ac_lab15;

-- Users table (simulating MTN MobAd platform users)
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(255) NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    address VARCHAR(500),
    account_type ENUM('basic', 'business', 'enterprise') DEFAULT 'basic',
    company_name VARCHAR(255),
    tax_id VARCHAR(50),
    bank_account VARCHAR(50),
    api_key VARCHAR(64),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE
);

-- User notes table (private notes/memos for each user)
CREATE TABLE user_notes (
    note_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    note_type ENUM('personal', 'business', 'confidential') DEFAULT 'personal',
    is_private BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Ad campaigns table (business data)
CREATE TABLE ad_campaigns (
    campaign_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    campaign_name VARCHAR(255) NOT NULL,
    budget DECIMAL(10, 2) NOT NULL,
    target_audience VARCHAR(500),
    status ENUM('draft', 'active', 'paused', 'completed') DEFAULT 'draft',
    impressions INT DEFAULT 0,
    clicks INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Account settings (sensitive configurations)
CREATE TABLE account_settings (
    setting_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    notification_email VARCHAR(255),
    backup_phone VARCHAR(20),
    two_factor_secret VARCHAR(32),
    recovery_codes TEXT,
    billing_address VARCHAR(500),
    payment_method VARCHAR(100),
    card_last_four VARCHAR(4),
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Audit log for tracking access
CREATE TABLE audit_log (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    target_email VARCHAR(255),
    ip_address VARCHAR(45),
    user_agent TEXT,
    request_data TEXT,
    response_status VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert sample users with realistic PII data
INSERT INTO users (email, password, full_name, phone_number, address, account_type, company_name, tax_id, bank_account, api_key) VALUES
-- Attacker account
('attacker@example.com', 'attacker123', 'Alex Thompson', '+234-800-111-0001', '123 Hacker Street, Lagos', 'basic', NULL, NULL, NULL, 'atk_a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6'),

-- Victim accounts with sensitive PII
('victim1@mtnbusiness.com', 'victim123', 'Sarah Johnson', '+234-803-456-7890', '45 Victoria Island, Lagos, Nigeria', 'business', 'Johnson Enterprises Ltd', 'TIN-2024-00123', 'GTB-0012345678', 'api_v1_x9y8z7w6v5u4t3s2r1q0p9o8n7m6l5k4'),

('ceo@bigcorp.ng', 'ceo2024secure', 'Michael Adebayo', '+234-802-111-2222', 'Plot 42, Lekki Phase 1, Lagos', 'enterprise', 'BigCorp Nigeria PLC', 'TIN-2024-99999', 'ZENITH-9876543210', 'api_ent_abc123def456ghi789jkl012mno345'),

('finance@acme.com.ng', 'finance@2024', 'Chioma Okonkwo', '+234-805-333-4444', '15 Broad Street, Lagos Island', 'business', 'ACME Solutions Nigeria', 'TIN-2024-55555', 'ACCESS-1122334455', 'api_biz_qrs789tuv012wxy345zab678cde901'),

('admin@mtnmobad.com', 'admin@mtn2024!', 'System Administrator', '+234-800-MTN-ADMIN', 'MTN Headquarters, Ikoyi, Lagos', 'enterprise', 'MTN Nigeria Communications PLC', 'TIN-MTN-OFFICIAL', 'STANBIC-CORPORATE-01', 'api_admin_SUPERKEY123456789ABCDEFGH'),

('john.doe@startup.io', 'startup2024', 'John Doe', '+234-809-555-6666', '8B Admiralty Way, Lekki', 'basic', 'TechStartup.io', NULL, 'FCMB-5566778899', 'api_std_mno345pqr678stu901vwx234yz'),

('mary.smith@agency.ng', 'agency123', 'Mary Smith', '+234-807-777-8888', '22 Allen Avenue, Ikeja', 'business', 'Digital Marketing Agency', 'TIN-2024-77777', 'UBA-1357924680', 'api_biz_hij567klm890nop123qrs456tuv');

-- Insert private notes for each user
INSERT INTO user_notes (user_id, title, content, note_type) VALUES
-- Attacker's notes
(1, 'My Todo List', 'Learn about IDOR vulnerabilities', 'personal'),
(1, 'Testing Notes', 'API endpoint seems vulnerable', 'personal'),

-- Victim 1 (Sarah) - sensitive business notes
(2, 'Q4 Financial Review', 'Revenue target: ₦50M. Current: ₦42M. Need to push ad campaigns harder.', 'business'),
(2, 'Client Contracts', 'NDA with GlobalTech expires March 2025. Renewal terms: 15% increase.', 'confidential'),
(2, 'Bank PIN Reminder', 'New ATM PIN: 4829 (change after first use)', 'confidential'),
(2, 'Meeting Notes - Investors', 'Seed round: $500K at 10% equity. Lead investor: TechVentures Africa', 'confidential'),

-- CEO (Michael) - highly sensitive data
(3, 'Board Meeting Agenda', 'Discuss acquisition of CompetitorX for $2.5M. Keep confidential.', 'confidential'),
(3, 'Personal Investment Portfolio', 'Stock holdings: MTN (50K shares), Dangote (10K), Nigerian Breweries (25K)', 'confidential'),
(3, 'Private Contact - Minister', 'Hon. Adewale: +234-802-PRIVATE. Re: Government contract discussion.', 'confidential'),
(3, 'Medical Records Reference', 'Dr. Akin at Lagos General. File #LG2024-1234. Annual checkup scheduled.', 'personal'),

-- Finance (Chioma) - financial PII
(4, 'Monthly Budget', 'Marketing: ₦5M, Operations: ₦3M, Salaries: ₦8M. Total: ₦16M', 'business'),
(4, 'Tax Filing Notes', 'Corporate tax due: ₦2.5M by March 31. VAT refund pending: ₦800K', 'confidential'),
(4, 'Payroll Credentials', 'Payroll system: payroll.acme.ng - user: chioma.fin / pass: [see password manager]', 'confidential'),

-- Admin - system secrets
(5, 'System Maintenance', 'Database backup schedule: Daily at 2AM. Retention: 30 days.', 'business'),
(5, 'API Keys Rotation', 'Master API key rotates monthly. Current expires: 2025-01-15', 'confidential'),
(5, 'Emergency Contacts', 'CTO: +234-802-000-0001, Security Team: security@mtn.ng', 'confidential'),
(5, 'Server Credentials', 'Production DB: db.mtnmobad.internal:3306 - Credentials in HashiCorp Vault', 'confidential'),

-- John Doe - startup founder
(6, 'Investor Pitch Deck', 'Key metrics: 10K MAU, 25% MoM growth, $50K ARR', 'business'),
(6, 'Personal Loan Details', 'Bank loan: ₦5M at 18% APR. Monthly payment: ₦150K', 'confidential'),
(6, 'Co-founder Agreement', 'Equity split: Me 60%, Partner 40%. Vesting: 4 years with 1-year cliff', 'confidential'),

-- Mary Smith - agency
(7, 'Client List', 'Active clients: Coca-Cola NG, Nestle, Unilever. Pipeline: Guinness, P&G', 'business'),
(7, 'Commission Structure', 'Base: ₦500K + 5% of campaign spend. Q4 bonus target: ₦2M', 'confidential'),
(7, 'Health Insurance', 'HMO Provider: Hygeia. Policy #: HYG-2024-88776. Family plan.', 'personal');

-- Insert ad campaigns
INSERT INTO ad_campaigns (user_id, campaign_name, budget, target_audience, status, impressions, clicks) VALUES
(2, 'Black Friday Sale 2024', 500000.00, 'Lagos, 25-45, Business Owners', 'active', 125000, 3200),
(2, 'Product Launch - Q1 2025', 750000.00, 'Nigeria, 18-35, Tech Enthusiasts', 'draft', 0, 0),
(3, 'Corporate Brand Awareness', 2000000.00, 'West Africa, C-Suite Executives', 'active', 500000, 12000),
(4, 'Recruitment Campaign', 150000.00, 'Lagos, 22-35, Finance Professionals', 'paused', 45000, 890),
(6, 'Startup Launch Campaign', 100000.00, 'Nigeria, 20-40, Early Adopters', 'active', 28000, 720),
(7, 'Agency Portfolio Showcase', 300000.00, 'Lagos, Business Decision Makers', 'completed', 180000, 4500);

-- Insert account settings with sensitive data
INSERT INTO account_settings (user_id, notification_email, backup_phone, two_factor_secret, recovery_codes, billing_address, payment_method, card_last_four) VALUES
(1, 'attacker.backup@example.com', '+234-800-111-0002', NULL, NULL, '123 Hacker Street', 'none', NULL),
(2, 'sarah.backup@gmail.com', '+234-803-456-7891', 'JBSWY3DPEHPK3PXP', '["ABC123", "DEF456", "GHI789", "JKL012"]', '45 Victoria Island, Lagos', 'credit_card', '4532'),
(3, 'michael.personal@yahoo.com', '+234-802-111-2223', 'HXDMVJECJJWSRB3H', '["CEO001", "CEO002", "CEO003", "CEO004"]', 'Plot 42, Lekki Phase 1', 'bank_transfer', NULL),
(4, 'chioma.personal@outlook.com', '+234-805-333-4445', 'GEZDGNBVGY3TQOJQ', '["FIN111", "FIN222", "FIN333", "FIN444"]', '15 Broad Street, Lagos', 'credit_card', '8876'),
(5, 'admin.backup@mtn.com', '+234-800-MTN-BACKUP', 'ADMIN2FASECRETKY', '["MTN001", "MTN002", "MTN003", "MTN004"]', 'MTN HQ, Ikoyi', 'corporate_account', NULL),
(6, 'john.personal@gmail.com', '+234-809-555-6667', NULL, NULL, '8B Admiralty Way', 'credit_card', '1234'),
(7, 'mary.backup@agency.ng', '+234-807-777-8889', 'MFZWQ3DFOR2GKMTF', '["AGY100", "AGY200", "AGY300", "AGY400"]', '22 Allen Avenue', 'credit_card', '9900');

-- Create index for faster email lookups
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_notes_user ON user_notes(user_id);
CREATE INDEX idx_audit_target ON audit_log(target_email);
