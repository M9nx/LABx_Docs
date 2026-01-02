-- Lab 28: Remove Users from Teams via IDOR + Information Disclosure
-- MTN Developers Portal Simulation
-- Based on HackerOne Report #1448475

DROP DATABASE IF EXISTS ac_lab28;
CREATE DATABASE ac_lab28;
USE ac_lab28;

-- Users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id VARCHAR(4) UNIQUE NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    company VARCHAR(100),
    role ENUM('developer', 'admin', 'enterprise') DEFAULT 'developer',
    api_key VARCHAR(64),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);

-- Teams table
CREATE TABLE teams (
    id INT PRIMARY KEY AUTO_INCREMENT,
    team_id VARCHAR(4) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    owner_user_id VARCHAR(4) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Team members table (junction table)
CREATE TABLE team_members (
    id INT PRIMARY KEY AUTO_INCREMENT,
    team_id VARCHAR(4) NOT NULL,
    user_id VARCHAR(4) NOT NULL,
    role ENUM('owner', 'admin', 'member') DEFAULT 'member',
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_membership (team_id, user_id)
);

-- Team invitations table
CREATE TABLE team_invitations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    team_id VARCHAR(4) NOT NULL,
    inviter_user_id VARCHAR(4) NOT NULL,
    invitee_user_id VARCHAR(4) NOT NULL,
    status ENUM('pending', 'accepted', 'declined') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    responded_at TIMESTAMP NULL
);

-- Activity log for tracking removals (simulates email notifications)
CREATE TABLE activity_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    action_type VARCHAR(50) NOT NULL,
    actor_user_id VARCHAR(4),
    target_user_id VARCHAR(4),
    target_team_id VARCHAR(4),
    details TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert test users (following the POC scenario)
-- Account A: Attacker
INSERT INTO users (user_id, username, email, password, full_name, phone, company, role, api_key) VALUES
('1111', 'attacker', 'attacker@evil.com', 'attacker123', 'Alex Attacker', '+1-555-0100', 'Security Research', 'developer', 'ak_1111_a1b2c3d4e5f6');

-- Account B: Bob (victim who owns Team B)
INSERT INTO users (user_id, username, email, password, full_name, phone, company, role, api_key) VALUES
('1112', 'bob_dev', 'bob@techcorp.com', 'bob123', 'Bob Developer', '+1-555-0101', 'TechCorp Ltd', 'developer', 'ak_1112_b2c3d4e5f6g7');

-- Account C: Carol (victim member of Team B)
INSERT INTO users (user_id, username, email, password, full_name, phone, company, role, api_key) VALUES
('1113', 'carol_admin', 'carol@enterprise.com', 'carol123', 'Carol Administrator', '+1-555-0102', 'Enterprise Inc', 'admin', 'ak_1113_c3d4e5f6g7h8');

-- Additional victim accounts
INSERT INTO users (user_id, username, email, password, full_name, phone, company, role, api_key) VALUES
('1114', 'dave_owner', 'dave@startup.io', 'dave123', 'Dave Owner', '+1-555-0103', 'Startup.io', 'enterprise', 'ak_1114_d4e5f6g7h8i9'),
('1115', 'eve_member', 'eve@freelance.net', 'eve123', 'Eve Freelancer', '+1-555-0104', 'Freelance Dev', 'developer', 'ak_1115_e5f6g7h8i9j0'),
('1116', 'frank_dev', 'frank@agency.com', 'frank123', 'Frank Developer', '+1-555-0105', 'Dev Agency', 'developer', 'ak_1116_f6g7h8i9j0k1'),
('1117', 'grace_lead', 'grace@bigtech.com', 'grace123', 'Grace Team Lead', '+1-555-0106', 'BigTech Ltd', 'admin', 'ak_1117_g7h8i9j0k1l2'),
('1118', 'henry_cto', 'henry@fintech.io', 'henry123', 'Henry CTO', '+1-555-0107', 'FinTech Solutions', 'enterprise', 'ak_1118_h8i9j0k1l2m3'),
('1119', 'mtn_admin', 'admin@mtn.com', 'admin123', 'MTN Administrator', '+1-555-0000', 'MTN Group', 'admin', 'ak_mtn_master_secret');

-- Create teams (following the POC scenario)
-- Team A: Attacker's team (team_id=0001)
INSERT INTO teams (team_id, name, description, owner_user_id) VALUES
('0001', 'Attacker Research Team', 'Security research and testing team', '1111');

-- Team B: Bob's team (team_id=0002) - TARGET for exploitation
INSERT INTO teams (team_id, name, description, owner_user_id) VALUES
('0002', 'TechCorp API Development', 'Official TechCorp MTN API integration team', '1112');

-- Team C: Carol's team (team_id=0003)
INSERT INTO teams (team_id, name, description, owner_user_id) VALUES
('0003', 'Enterprise Integration Squad', 'Enterprise-grade API integration specialists', '1113');

-- Additional teams for realistic scenario
INSERT INTO teams (team_id, name, description, owner_user_id) VALUES
('0004', 'Startup Innovators', 'Fast-moving startup building on MTN platform', '1114'),
('0005', 'BigTech Mobile Division', 'Mobile app development team', '1117'),
('0006', 'FinTech Payment Integration', 'Payment gateway specialists', '1118'),
('0007', 'MTN Internal Dev Team', 'Internal MTN development - CONFIDENTIAL', '1119');

-- Set up team memberships (following the POC scenario)

-- Team A (0001): Attacker owns, Bob is member (Step 2: A invited B)
INSERT INTO team_members (team_id, user_id, role) VALUES
('0001', '1111', 'owner'),
('0001', '1112', 'member');

-- Team B (0002): Bob owns, Carol is member (Step 3: B invited C) - THIS IS THE TARGET
INSERT INTO team_members (team_id, user_id, role) VALUES
('0002', '1112', 'owner'),
('0002', '1113', 'admin'),
('0002', '1115', 'member');

-- Team C (0003): Carol owns with members
INSERT INTO team_members (team_id, user_id, role) VALUES
('0003', '1113', 'owner'),
('0003', '1114', 'admin'),
('0003', '1116', 'member');

-- Team 0004: Dave's startup team
INSERT INTO team_members (team_id, user_id, role) VALUES
('0004', '1114', 'owner'),
('0004', '1115', 'admin'),
('0004', '1116', 'member'),
('0004', '1117', 'member');

-- Team 0005: Grace's BigTech team
INSERT INTO team_members (team_id, user_id, role) VALUES
('0005', '1117', 'owner'),
('0005', '1118', 'admin'),
('0005', '1112', 'member'),
('0005', '1113', 'member');

-- Team 0006: Henry's FinTech team
INSERT INTO team_members (team_id, user_id, role) VALUES
('0006', '1118', 'owner'),
('0006', '1117', 'admin'),
('0006', '1116', 'member'),
('0006', '1115', 'member');

-- Team 0007: MTN Internal (high-value target)
INSERT INTO team_members (team_id, user_id, role) VALUES
('0007', '1119', 'owner'),
('0007', '1117', 'admin'),
('0007', '1118', 'admin');

-- Some pending invitations
INSERT INTO team_invitations (team_id, inviter_user_id, invitee_user_id, status) VALUES
('0002', '1112', '1116', 'pending'),
('0004', '1114', '1111', 'pending'),
('0005', '1117', '1114', 'pending');

-- Create indexes for performance
CREATE INDEX idx_team_members_team ON team_members(team_id);
CREATE INDEX idx_team_members_user ON team_members(user_id);
CREATE INDEX idx_invitations_invitee ON team_invitations(invitee_user_id);
CREATE INDEX idx_activity_actor ON activity_log(actor_user_id);
CREATE INDEX idx_activity_target ON activity_log(target_user_id);
