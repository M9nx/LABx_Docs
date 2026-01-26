-- Lab 20: IDOR API Key Management
-- Database Setup Script

CREATE DATABASE IF NOT EXISTS ac_lab20;
USE ac_lab20;

-- Drop existing tables
DROP TABLE IF EXISTS api_keys;
DROP TABLE IF EXISTS org_members;
DROP TABLE IF EXISTS organizations;
DROP TABLE IF EXISTS users;

-- Users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    uuid VARCHAR(50) UNIQUE NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Organizations table
CREATE TABLE organizations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    uuid VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(50) UNIQUE NOT NULL,
    description TEXT,
    owner_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_id) REFERENCES users(id)
);

-- Organization members table
CREATE TABLE org_members (
    id INT PRIMARY KEY AUTO_INCREMENT,
    org_id INT NOT NULL,
    user_id INT NOT NULL,
    role ENUM('owner', 'admin', 'member') NOT NULL DEFAULT 'member',
    invited_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (org_id) REFERENCES organizations(id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    UNIQUE KEY unique_membership (org_id, user_id)
);

-- API Keys table
CREATE TABLE api_keys (
    id INT PRIMARY KEY AUTO_INCREMENT,
    uuid VARCHAR(50) UNIQUE NOT NULL,
    org_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    api_key VARCHAR(100) NOT NULL,
    description TEXT,
    permissions VARCHAR(255) DEFAULT 'read',
    created_by INT NOT NULL,
    last_used TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (org_id) REFERENCES organizations(id),
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Insert sample users
INSERT INTO users (uuid, username, password, email, full_name) VALUES
('usr-11111111-1111-1111-1111-111111111111', 'victim_owner', 'victim123', 'victim@techcorp.io', 'Victor Owner'),
('usr-22222222-2222-2222-2222-222222222222', 'attacker_member', 'attacker123', 'attacker@email.com', 'Adam Attacker'),
('usr-33333333-3333-3333-3333-333333333333', 'alice_admin', 'alice123', 'alice@techcorp.io', 'Alice Admin'),
('usr-44444444-4444-4444-4444-444444444444', 'bob_member', 'bob123', 'bob@techcorp.io', 'Bob Member'),
('usr-55555555-5555-5555-5555-555555555555', 'other_owner', 'other123', 'other@company.io', 'Oscar Owner');

-- Insert organizations
INSERT INTO organizations (uuid, name, slug, description, owner_id) VALUES
('org-aaaaaaaa-aaaa-aaaa-aaaa-aaaaaaaaaaaa', 'TechCorp Inc', 'techcorp', 'Leading technology solutions provider', 1),
('org-bbbbbbbb-bbbb-bbbb-bbbb-bbbbbbbbbbbb', 'StartupXYZ', 'startupxyz', 'Innovative startup company', 5);

-- Insert organization memberships
INSERT INTO org_members (org_id, user_id, role) VALUES
-- TechCorp Inc members
(1, 1, 'owner'),      -- victim_owner is the owner
(1, 2, 'member'),     -- attacker_member is just a member (limited permissions)
(1, 3, 'admin'),      -- alice is admin
(1, 4, 'member'),     -- bob is member
-- StartupXYZ members
(2, 5, 'owner'),
(2, 4, 'member');

-- Insert API Keys for TechCorp (victim's organization)
-- These are SENSITIVE keys that members should NOT be able to view/delete/create
INSERT INTO api_keys (uuid, org_id, name, api_key, description, permissions, created_by) VALUES
('key-11111111-1111-1111-1111-111111111111', 1, 'Production API Key', 'ak_prod_8f7d6e5c4b3a2918273645', 'Main production API access - DO NOT SHARE', 'read,write,delete', 1),
('key-22222222-2222-2222-2222-222222222222', 1, 'Database Sync Key', 'ak_db_sync_1a2b3c4d5e6f7890abcd', 'Database synchronization service key', 'read,write', 1),
('key-33333333-3333-3333-3333-333333333333', 1, 'Analytics Dashboard', 'ak_analytics_xyz789abc123def456', 'Analytics and reporting access', 'read', 3),
('key-44444444-4444-4444-4444-444444444444', 1, 'Payment Gateway', 'ak_payment_SUPER_SECRET_KEY_789', 'Payment processing - HIGHLY SENSITIVE', 'read,write,delete', 1),
('key-55555555-5555-5555-5555-555555555555', 1, 'CI/CD Pipeline', 'ak_cicd_build_deploy_key_2024', 'Continuous integration deployment key', 'read,write', 3);

-- Insert API Key for StartupXYZ
INSERT INTO api_keys (uuid, org_id, name, api_key, description, permissions, created_by) VALUES
('key-66666666-6666-6666-6666-666666666666', 2, 'Startup Main Key', 'ak_startup_main_key_abc123', 'Main API key for StartupXYZ', 'read,write', 5);
