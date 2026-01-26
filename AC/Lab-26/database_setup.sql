-- Lab 26: IDOR in API Applications - Credential Leak (Pressable-style)
-- Based on HackerOne Report - API Application IDOR leading to Account Takeover

DROP DATABASE IF EXISTS ac_lab26;
CREATE DATABASE ac_lab26;
USE ac_lab26;

-- Users table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    full_name VARCHAR(100),
    company VARCHAR(100),
    role ENUM('user', 'admin') DEFAULT 'user',
    plan_type ENUM('starter', 'growth', 'scale', 'enterprise') DEFAULT 'starter',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- API Applications table - stores OAuth-like app credentials
CREATE TABLE api_applications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    client_id VARCHAR(64) NOT NULL,
    client_secret VARCHAR(64) NOT NULL,
    redirect_uri VARCHAR(255),
    scopes TEXT,
    status ENUM('active', 'inactive', 'revoked') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Sites/Projects managed via API
CREATE TABLE sites (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    domain VARCHAR(255),
    environment ENUM('production', 'staging', 'development') DEFAULT 'production',
    php_version VARCHAR(10) DEFAULT '8.2',
    wordpress_version VARCHAR(20),
    datacenter VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Collaborators (can be added via API - the takeover vector)
CREATE TABLE collaborators (
    id INT PRIMARY KEY AUTO_INCREMENT,
    site_id INT NOT NULL,
    email VARCHAR(100) NOT NULL,
    role ENUM('admin', 'developer', 'viewer') DEFAULT 'viewer',
    invited_by INT NOT NULL,
    status ENUM('pending', 'accepted') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (site_id) REFERENCES sites(id) ON DELETE CASCADE,
    FOREIGN KEY (invited_by) REFERENCES users(id)
);

-- API Access Logs
CREATE TABLE api_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    application_id INT,
    endpoint VARCHAR(255),
    method VARCHAR(10),
    ip_address VARCHAR(45),
    response_code INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (application_id) REFERENCES api_applications(id) ON DELETE SET NULL
);

-- Activity Log for tracking suspicious actions
CREATE TABLE activity_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(100),
    target_type VARCHAR(50),
    target_id INT,
    details TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Insert test users
INSERT INTO users (username, password, email, full_name, company, role, plan_type) VALUES
('attacker', 'attacker123', 'attacker@evil.com', 'Evil Attacker', 'HackCorp', 'user', 'starter'),
('victim', 'victim123', 'victim@bigcompany.com', 'Victor Williams', 'BigCompany Inc', 'user', 'enterprise'),
('sarah', 'sarah123', 'sarah@startup.io', 'Sarah Johnson', 'Startup.io', 'user', 'growth'),
('mike', 'mike123', 'mike@agency.net', 'Mike Chen', 'Digital Agency', 'user', 'scale'),
('admin', 'admin123', 'admin@pressable.com', 'Platform Admin', 'Pressable', 'admin', 'enterprise');

-- Insert API Applications (SEQUENTIAL IDs - vulnerability!)
-- Attacker's application (ID will be 1)
INSERT INTO api_applications (user_id, name, description, client_id, client_secret, redirect_uri, scopes) VALUES
(1, 'My Test App', 'Testing the API', 
 'cli_attacker_a1b2c3d4e5f6', 
 'sec_attacker_xyz789secret123',
 'http://localhost:8080/callback',
 'read:sites,write:sites');

-- Victim's applications (IDs 2-4) - THESE CONTAIN SENSITIVE SECRETS
INSERT INTO api_applications (user_id, name, description, client_id, client_secret, redirect_uri, scopes) VALUES
(2, 'BigCompany Production API', 'Main production API integration for all company sites', 
 'cli_bigco_prod_8x7k9m2p4q', 
 'sec_SUPER_SECRET_bigco_DO_NOT_SHARE_abc123xyz',
 'https://bigcompany.com/api/callback',
 'read:sites,write:sites,manage:collaborators,billing:read'),
 
(2, 'BigCompany Staging Integration', 'Staging environment API access', 
 'cli_bigco_stag_3n5v7b9d1f', 
 'sec_staging_secret_bigco_test456',
 'https://staging.bigcompany.com/callback',
 'read:sites,write:sites'),
 
(2, 'Automated Backup System', 'Nightly backup automation credentials', 
 'cli_bigco_backup_2m4n6p8r0t', 
 'sec_BACKUP_MASTER_KEY_critical789',
 'https://backup.bigcompany.internal/webhook',
 'read:sites,manage:backups,read:files');

-- Sarah's applications (IDs 5-6)
INSERT INTO api_applications (user_id, name, description, client_id, client_secret, redirect_uri, scopes) VALUES
(3, 'Startup.io Main App', 'Primary API for startup platform', 
 'cli_startup_main_5k7m9p1r3t', 
 'sec_startup_production_key_SECRET',
 'https://app.startup.io/oauth/callback',
 'read:sites,write:sites,manage:collaborators'),
 
(3, 'CI/CD Pipeline', 'Deployment automation', 
 'cli_startup_cicd_8x2v4b6n0q', 
 'sec_deployment_PRIVATE_key_xyz',
 'https://deploy.startup.io/hook',
 'write:sites,manage:deployments');

-- Mike's applications (IDs 7-8)
INSERT INTO api_applications (user_id, name, description, client_id, client_secret, redirect_uri, scopes) VALUES
(4, 'Agency Client Portal', 'Client site management portal', 
 'cli_agency_portal_1a3c5e7g9i', 
 'sec_agency_MASTER_SECRET_manage123',
 'https://portal.agency.net/auth',
 'read:sites,write:sites,manage:collaborators,billing:read'),
 
(4, 'White Label Dashboard', 'Reseller dashboard integration', 
 'cli_agency_resell_2b4d6f8h0j', 
 'sec_reseller_CONFIDENTIAL_key456',
 'https://dashboard.agency.net/callback',
 'read:sites,write:sites');

-- Admin's application (ID 9) - MOST SENSITIVE
INSERT INTO api_applications (user_id, name, description, client_id, client_secret, redirect_uri, scopes) VALUES
(5, 'Platform Admin Tools', 'Internal administration API - RESTRICTED', 
 'cli_admin_INTERNAL_9z8y7x6w5v', 
 'sec_ADMIN_MASTER_KEY_DO_NOT_LEAK_superadmin999',
 'https://admin.pressable.com/internal/auth',
 'admin:full,read:all,write:all,manage:users,billing:manage');

-- Insert some sites for the victim
INSERT INTO sites (user_id, name, domain, environment, php_version, wordpress_version, datacenter) VALUES
(2, 'BigCompany Main', 'www.bigcompany.com', 'production', '8.2', '6.4.2', 'us-east-1'),
(2, 'BigCompany Blog', 'blog.bigcompany.com', 'production', '8.1', '6.4.1', 'us-east-1'),
(2, 'BigCompany Staging', 'staging.bigcompany.com', 'staging', '8.2', '6.4.2', 'us-west-2'),
(3, 'Startup.io App', 'app.startup.io', 'production', '8.2', '6.4.2', 'eu-west-1'),
(4, 'Agency Portfolio', 'portfolio.agency.net', 'production', '8.1', '6.4.0', 'us-central-1');

-- Insert some collaborators
INSERT INTO collaborators (site_id, email, role, invited_by, status) VALUES
(1, 'dev@bigcompany.com', 'developer', 2, 'accepted'),
(1, 'designer@bigcompany.com', 'viewer', 2, 'accepted'),
(2, 'editor@bigcompany.com', 'developer', 2, 'pending');

-- Create indexes for performance
CREATE INDEX idx_api_apps_user ON api_applications(user_id);
CREATE INDEX idx_sites_user ON sites(user_id);
CREATE INDEX idx_activity_user ON activity_log(user_id);
