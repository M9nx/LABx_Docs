-- Lab 23: IDOR on AddTagToAssets - Custom Tag Enumeration
-- Database Setup for TagScope Asset Management Platform

DROP DATABASE IF EXISTS ac_lab23;
CREATE DATABASE ac_lab23 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ac_lab23;

-- Users table (security researchers and organizations)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(50) UNIQUE NOT NULL,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(150) NOT NULL,
    full_name VARCHAR(200),
    organization VARCHAR(200),
    user_role ENUM('researcher', 'organization', 'admin') DEFAULT 'researcher',
    api_token VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Assets/Scope table (domains, IPs, URLs that users track)
CREATE TABLE assets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    asset_id VARCHAR(50) UNIQUE NOT NULL,
    user_id VARCHAR(50) NOT NULL,
    asset_type ENUM('domain', 'ip', 'url', 'cidr', 'wildcard') DEFAULT 'domain',
    asset_value VARCHAR(500) NOT NULL,
    asset_name VARCHAR(200),
    description TEXT,
    status ENUM('active', 'inactive', 'pending') DEFAULT 'active',
    risk_level ENUM('critical', 'high', 'medium', 'low', 'info') DEFAULT 'info',
    discovered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_scanned TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Custom Tags table (users create private tags to organize assets)
-- THIS IS THE TARGET OF THE IDOR - tags can be enumerated!
CREATE TABLE tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tag_id VARCHAR(50) UNIQUE NOT NULL,
    internal_id INT UNIQUE NOT NULL,  -- The enumerable ID (like gid://hackerone/AsmTag/4979xxxx)
    user_id VARCHAR(50) NOT NULL,
    tag_name VARCHAR(100) NOT NULL,
    tag_color VARCHAR(20) DEFAULT '#6366f1',
    description TEXT,
    is_private BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Asset-Tag relationship (many-to-many)
CREATE TABLE asset_tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    asset_id VARCHAR(50) NOT NULL,
    tag_id VARCHAR(50) NOT NULL,
    added_by VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_asset_tag (asset_id, tag_id),
    FOREIGN KEY (asset_id) REFERENCES assets(asset_id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(tag_id) ON DELETE CASCADE,
    FOREIGN KEY (added_by) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Activity log for tracking tag operations
CREATE TABLE activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(50) NOT NULL,
    action VARCHAR(100) NOT NULL,
    target_type VARCHAR(50),
    target_id VARCHAR(50),
    details TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ===========================================
-- SAMPLE DATA
-- ===========================================

-- Insert Users
-- Victim: Creates private custom tags
-- Attacker: Will enumerate and discover victim's tags
INSERT INTO users (user_id, username, password, email, full_name, organization, user_role, api_token) VALUES
('USR_VICTIM_001', 'victim_org', 'victim123', 'security@victimcorp.com', 'Victor Secure', 'VictimCorp Security', 'organization', 'tok_victim_a1b2c3d4e5f6'),
('USR_ATTACKER_001', 'attacker_user', 'attacker123', 'hacker@attacker.com', 'Alex Hacker', 'Independent Researcher', 'researcher', 'tok_attacker_x9y8z7w6'),
('USR_ADMIN_001', 'admin', 'admin123', 'admin@tagscope.com', 'Admin User', 'TagScope Inc', 'admin', 'tok_admin_super123'),
('USR_USER_003', 'researcher_bob', 'bob123', 'bob@security.io', 'Bob Security', 'SecLabs', 'researcher', 'tok_bob_qwerty');

-- Insert Victim's Assets (scope)
INSERT INTO assets (asset_id, user_id, asset_type, asset_value, asset_name, description, status, risk_level) VALUES
('AST_V_001', 'USR_VICTIM_001', 'domain', 'victimcorp.com', 'Main Domain', 'Primary corporate domain', 'active', 'medium'),
('AST_V_002', 'USR_VICTIM_001', 'domain', 'api.victimcorp.com', 'API Subdomain', 'Main API endpoint', 'active', 'high'),
('AST_V_003', 'USR_VICTIM_001', 'domain', 'staging.victimcorp.com', 'Staging Environment', 'Pre-production server', 'active', 'critical'),
('AST_V_004', 'USR_VICTIM_001', 'ip', '192.168.100.50', 'Internal Server', 'Database server IP', 'active', 'critical'),
('AST_V_005', 'USR_VICTIM_001', 'wildcard', '*.victimcorp.com', 'All Subdomains', 'Wildcard scope', 'active', 'high');

-- Insert Attacker's Assets
INSERT INTO assets (asset_id, user_id, asset_type, asset_value, asset_name, description, status, risk_level) VALUES
('AST_A_001', 'USR_ATTACKER_001', 'domain', 'attacker-test.com', 'Test Domain', 'My test domain', 'active', 'low'),
('AST_A_002', 'USR_ATTACKER_001', 'domain', 'bugbounty-target.com', 'Bounty Target', 'Active bounty program', 'active', 'medium');

-- Insert Bob's Assets
INSERT INTO assets (asset_id, user_id, asset_type, asset_value, asset_name, description, status, risk_level) VALUES
('AST_B_001', 'USR_USER_003', 'domain', 'seclabs.io', 'Company Site', 'Main website', 'active', 'low');

-- ===========================================
-- VICTIM'S PRIVATE CUSTOM TAGS (TARGET!)
-- These have sequential internal_ids that can be enumerated
-- ===========================================
INSERT INTO tags (tag_id, internal_id, user_id, tag_name, tag_color, description, is_private) VALUES
-- Victim's PRIVATE tags - sensitive categorization!
('TAG_V_001', 49790001, 'USR_VICTIM_001', 'Production-Critical', '#ef4444', 'Critical production systems - DO NOT TOUCH', TRUE),
('TAG_V_002', 49790002, 'USR_VICTIM_001', 'Contains-PII', '#f59e0b', 'Systems containing personally identifiable information', TRUE),
('TAG_V_003', 49790003, 'USR_VICTIM_001', 'AWS-Infrastructure', '#3b82f6', 'AWS cloud infrastructure components', TRUE),
('TAG_V_004', 49790004, 'USR_VICTIM_001', 'Payment-Systems', '#10b981', 'Payment processing and financial systems', TRUE),
('TAG_V_005', 49790005, 'USR_VICTIM_001', 'Internal-Only', '#8b5cf6', 'Internal systems - not public facing', TRUE),
('TAG_V_006', 49790006, 'USR_VICTIM_001', 'Vulnerable-Legacy', '#dc2626', 'Legacy systems with known vulnerabilities', TRUE),
('TAG_V_007', 49790007, 'USR_VICTIM_001', 'Admin-Access', '#f97316', 'Systems with admin panel access', TRUE);

-- Attacker's tags (for comparison)
INSERT INTO tags (tag_id, internal_id, user_id, tag_name, tag_color, description, is_private) VALUES
('TAG_A_001', 49790100, 'USR_ATTACKER_001', 'My-Targets', '#06b6d4', 'Active targets for testing', TRUE),
('TAG_A_002', 49790101, 'USR_ATTACKER_001', 'High-Priority', '#ec4899', 'High priority findings', TRUE);

-- Bob's tags
INSERT INTO tags (tag_id, internal_id, user_id, tag_name, tag_color, description, is_private) VALUES
('TAG_B_001', 49790200, 'USR_USER_003', 'Client-Projects', '#84cc16', 'Client project assets', TRUE);

-- Assign some tags to victim's assets (normal usage)
INSERT INTO asset_tags (asset_id, tag_id, added_by) VALUES
('AST_V_001', 'TAG_V_001', 'USR_VICTIM_001'),
('AST_V_002', 'TAG_V_004', 'USR_VICTIM_001'),
('AST_V_003', 'TAG_V_006', 'USR_VICTIM_001'),
('AST_V_004', 'TAG_V_005', 'USR_VICTIM_001');

-- Assign tags to attacker's assets
INSERT INTO asset_tags (asset_id, tag_id, added_by) VALUES
('AST_A_001', 'TAG_A_001', 'USR_ATTACKER_001');

-- Activity log entries
INSERT INTO activity_log (user_id, action, target_type, target_id, details, ip_address) VALUES
('USR_VICTIM_001', 'create_tag', 'tag', 'TAG_V_001', 'Created tag: Production-Critical', '10.0.0.1'),
('USR_VICTIM_001', 'create_tag', 'tag', 'TAG_V_002', 'Created tag: Contains-PII', '10.0.0.1'),
('USR_VICTIM_001', 'add_tag_to_asset', 'asset', 'AST_V_001', 'Added Production-Critical tag', '10.0.0.1'),
('USR_ATTACKER_001', 'create_asset', 'asset', 'AST_A_001', 'Added asset: attacker-test.com', '192.168.1.100');

-- Create indexes for performance
CREATE INDEX idx_assets_user ON assets(user_id);
CREATE INDEX idx_tags_user ON tags(user_id);
CREATE INDEX idx_tags_internal_id ON tags(internal_id);
CREATE INDEX idx_asset_tags_asset ON asset_tags(asset_id);
CREATE INDEX idx_asset_tags_tag ON asset_tags(tag_id);
CREATE INDEX idx_activity_user ON activity_log(user_id);
