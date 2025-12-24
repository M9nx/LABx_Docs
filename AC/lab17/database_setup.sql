-- Lab 17: IDOR External Status Check Information Disclosure
-- Database: ac_lab17

CREATE DATABASE IF NOT EXISTS ac_lab17;
USE ac_lab17;

-- Drop tables in correct order (foreign keys)
DROP TABLE IF EXISTS status_check_responses;
DROP TABLE IF EXISTS external_status_checks;
DROP TABLE IF EXISTS protected_branches;
DROP TABLE IF EXISTS merge_requests;
DROP TABLE IF EXISTS project_members;
DROP TABLE IF EXISTS projects;
DROP TABLE IF EXISTS personal_access_tokens;
DROP TABLE IF EXISTS users;

-- Users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    avatar_url VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Personal Access Tokens for API
CREATE TABLE personal_access_tokens (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    token VARCHAR(64) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    scopes VARCHAR(255) DEFAULT 'api',
    expires_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Projects table
CREATE TABLE projects (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    path VARCHAR(100) NOT NULL,
    description TEXT,
    owner_id INT NOT NULL,
    visibility ENUM('public', 'internal', 'private') DEFAULT 'private',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_id) REFERENCES users(id)
);

-- Project members (for access control)
CREATE TABLE project_members (
    id INT PRIMARY KEY AUTO_INCREMENT,
    project_id INT NOT NULL,
    user_id INT NOT NULL,
    access_level INT DEFAULT 30, -- 10=Guest, 20=Reporter, 30=Developer, 40=Maintainer, 50=Owner
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id),
    UNIQUE KEY unique_member (project_id, user_id)
);

-- Merge Requests
CREATE TABLE merge_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    iid INT NOT NULL, -- Internal ID within project
    project_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    source_branch VARCHAR(100) NOT NULL,
    target_branch VARCHAR(100) DEFAULT 'main',
    author_id INT NOT NULL,
    state ENUM('opened', 'merged', 'closed') DEFAULT 'opened',
    sha VARCHAR(40) NOT NULL, -- Current commit SHA
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (author_id) REFERENCES users(id),
    UNIQUE KEY unique_mr (project_id, iid)
);

-- Protected Branches
CREATE TABLE protected_branches (
    id INT PRIMARY KEY AUTO_INCREMENT,
    project_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    push_access_level INT DEFAULT 40,
    merge_access_level INT DEFAULT 30,
    allow_force_push BOOLEAN DEFAULT FALSE,
    code_owner_approval_required BOOLEAN DEFAULT FALSE,
    allowed_user_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (allowed_user_id) REFERENCES users(id)
);

-- External Status Checks (the vulnerable feature)
CREATE TABLE external_status_checks (
    id INT PRIMARY KEY AUTO_INCREMENT,
    project_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    external_url VARCHAR(255) NOT NULL,
    protected_branch_id INT NULL,
    api_key VARCHAR(100) NULL, -- Sensitive! Should be hidden
    webhook_secret VARCHAR(100) NULL, -- Sensitive! Should be hidden
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    FOREIGN KEY (protected_branch_id) REFERENCES protected_branches(id) ON DELETE SET NULL
);

-- Status Check Responses
CREATE TABLE status_check_responses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    merge_request_id INT NOT NULL,
    external_status_check_id INT NOT NULL,
    sha VARCHAR(40) NOT NULL,
    status ENUM('pending', 'passed', 'failed') DEFAULT 'pending',
    responded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (merge_request_id) REFERENCES merge_requests(id) ON DELETE CASCADE,
    FOREIGN KEY (external_status_check_id) REFERENCES external_status_checks(id) ON DELETE CASCADE
);

-- ============================================
-- SEED DATA
-- ============================================

-- Users
-- victim01/victim123 - Has private project with sensitive status checks
-- attacker01/attacker123 - Will exploit the IDOR
-- admin/admin123 - System admin

INSERT INTO users (id, username, password, email, full_name, role) VALUES
(1, 'admin', 'admin123', 'admin@gitlab.local', 'GitLab Administrator', 'admin'),
(2, 'victim01', 'victim123', 'victim01@company.com', 'Victor Martinez', 'user'),
(3, 'attacker01', 'attacker123', 'attacker01@evil.com', 'Alex Thompson', 'user'),
(4, 'developer01', 'dev123', 'dev01@company.com', 'David Chen', 'user'),
(5, 'security_team', 'security123', 'security@company.com', 'Security Team Account', 'user');

-- Personal Access Tokens
INSERT INTO personal_access_tokens (user_id, token, name, scopes) VALUES
(2, 'glpat-victim-token-secret-abc123', 'Victim API Token', 'api,read_api'),
(3, 'glpat-attacker-token-xyz789', 'Attacker API Token', 'api,read_api'),
(4, 'glpat-developer-token-def456', 'Dev API Token', 'api');

-- Projects
-- Victim's PRIVATE project with sensitive data
INSERT INTO projects (id, name, path, description, owner_id, visibility) VALUES
(1, 'secret-infrastructure', 'secret-infrastructure', 'CONFIDENTIAL: Production infrastructure automation and deployment scripts. Contains AWS credentials and internal API endpoints.', 2, 'private'),
(2, 'internal-security-tools', 'internal-security-tools', 'PRIVATE: Security scanning tools and vulnerability reports. Access restricted to security team.', 5, 'private'),
(3, 'attacker-project', 'attacker-project', 'Public test project for learning GitLab features.', 3, 'public'),
(4, 'company-website', 'company-website', 'Public company website repository.', 2, 'public'),
(5, 'financial-reports', 'financial-reports', 'CONFIDENTIAL: Quarterly financial reports and projections. Board access only.', 1, 'private');

-- Project Members
INSERT INTO project_members (project_id, user_id, access_level) VALUES
(1, 2, 50), -- victim01 owns secret-infrastructure
(1, 4, 30), -- developer01 has developer access
(2, 5, 50), -- security_team owns internal-security-tools
(2, 1, 40), -- admin has maintainer access
(3, 3, 50), -- attacker01 owns attacker-project
(4, 2, 50), -- victim01 owns company-website
(5, 1, 50); -- admin owns financial-reports

-- Protected Branches
INSERT INTO protected_branches (id, project_id, name, push_access_level, merge_access_level, allow_force_push, code_owner_approval_required, allowed_user_id) VALUES
(1, 1, 'main', 40, 40, FALSE, TRUE, 2),
(2, 1, 'production', 50, 50, FALSE, TRUE, 2),
(3, 2, 'main', 40, 30, FALSE, TRUE, 5),
(4, 3, 'main', 30, 30, TRUE, FALSE, NULL),
(5, 5, 'main', 50, 50, FALSE, TRUE, 1);

-- External Status Checks (SENSITIVE DATA - this is what leaks via IDOR!)
INSERT INTO external_status_checks (id, project_id, name, external_url, protected_branch_id, api_key, webhook_secret) VALUES
-- Victim's sensitive status checks (IDs 1-3)
(1, 1, 'AWS Deployment Validator', 'https://internal-deploy.victim-corp.com/api/validate', 1, 'AKIAIOSFODNN7EXAMPLE', 'wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY'),
(2, 1, 'Security Scanner - Prod', 'https://security-scanner.internal.victim-corp.com:8443/scan', 2, 'sec-api-key-prod-xyz789', 'webhook-secret-prod-abc123'),
(3, 2, 'Vulnerability Assessment Tool', 'https://vuln-scanner.security.victim-corp.local/api/v2/assess', 3, 'vuln-api-key-secret-456', 'vuln-webhook-hmac-secret'),

-- Financial project status check (ID 4)
(4, 5, 'Financial Compliance Check', 'https://compliance.finance.victim-corp.com/verify', 5, 'finance-api-key-confidential', 'finance-webhook-secret-789'),

-- Attacker's status check (ID 5) - legitimate, used to trigger the attack
(5, 3, 'Test Status Check', 'https://attacker-webhook.example.com/test', 4, 'attacker-test-key', 'attacker-webhook-secret');

-- Merge Requests
INSERT INTO merge_requests (id, iid, project_id, title, description, source_branch, target_branch, author_id, state, sha) VALUES
-- Victim's MRs (in private project)
(1, 1, 1, 'Update AWS credentials rotation', 'Implementing automatic credential rotation for production', 'feature/cred-rotation', 'main', 2, 'opened', 'a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6q7r8s9t0'),
(2, 2, 1, 'Add new deployment region', 'Adding eu-west-2 deployment configuration', 'feature/eu-west-2', 'production', 4, 'opened', 'b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6q7r8s9t0u1'),

-- Security project MRs
(3, 1, 2, 'Update vulnerability definitions', 'Q4 vulnerability signature updates', 'feature/vuln-update', 'main', 5, 'opened', 'c3d4e5f6g7h8i9j0k1l2m3n4o5p6q7r8s9t0u1v2'),

-- Attacker's MR (used to exploit IDOR)
(4, 1, 3, 'Test merge request', 'Testing GitLab features and API', 'feature/test-branch', 'main', 3, 'opened', 'x9y8z7w6v5u4t3s2r1q0p9o8n7m6l5k4j3i2h1g0'),

-- Financial MRs
(5, 1, 5, 'Q4 Financial Report Draft', 'Quarterly financial report for board review', 'feature/q4-report', 'main', 1, 'opened', 'd4e5f6g7h8i9j0k1l2m3n4o5p6q7r8s9t0u1v2w3');

-- Status Check Responses (some existing responses)
INSERT INTO status_check_responses (merge_request_id, external_status_check_id, sha, status) VALUES
(1, 1, 'a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6q7r8s9t0', 'passed'),
(3, 3, 'c3d4e5f6g7h8i9j0k1l2m3n4o5p6q7r8s9t0u1v2', 'pending');
