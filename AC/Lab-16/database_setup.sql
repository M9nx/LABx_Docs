-- Lab 16: IDOR Slowvote Visibility Bypass
-- Database: ac_lab16

CREATE DATABASE IF NOT EXISTS ac_lab16;
USE ac_lab16;

-- Users table
DROP TABLE IF EXISTS poll_permissions;
DROP TABLE IF EXISTS poll_votes;
DROP TABLE IF EXISTS poll_options;
DROP TABLE IF EXISTS slowvotes;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Slowvotes (polls) table
CREATE TABLE slowvotes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    creator_id INT NOT NULL,
    visibility ENUM('everyone', 'specific', 'nobody') DEFAULT 'everyone',
    allow_multiple BOOLEAN DEFAULT FALSE,
    is_closed BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    closes_at DATETIME NULL,
    FOREIGN KEY (creator_id) REFERENCES users(id)
);

-- Poll options
CREATE TABLE poll_options (
    id INT PRIMARY KEY AUTO_INCREMENT,
    poll_id INT NOT NULL,
    option_text VARCHAR(255) NOT NULL,
    vote_count INT DEFAULT 0,
    FOREIGN KEY (poll_id) REFERENCES slowvotes(id) ON DELETE CASCADE
);

-- Poll votes (who voted for what)
CREATE TABLE poll_votes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    poll_id INT NOT NULL,
    option_id INT NOT NULL,
    user_id INT NOT NULL,
    voted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (poll_id) REFERENCES slowvotes(id) ON DELETE CASCADE,
    FOREIGN KEY (option_id) REFERENCES poll_options(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id),
    UNIQUE KEY unique_vote (poll_id, user_id, option_id)
);

-- Poll permissions (who can see specific visibility polls)
CREATE TABLE poll_permissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    poll_id INT NOT NULL,
    user_id INT NOT NULL,
    can_view BOOLEAN DEFAULT TRUE,
    can_vote BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (poll_id) REFERENCES slowvotes(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id),
    UNIQUE KEY unique_permission (poll_id, user_id)
);

-- Insert sample users
-- User A (poll creator): alice / alice123
-- User B (no permission): bob / bob123  
-- User C (has permission): charlie / charlie123
-- Admin: admin / admin123

INSERT INTO users (username, password, email, full_name, role) VALUES
('admin', 'admin123', 'admin@phabricator.local', 'System Administrator', 'admin'),
('alice', 'alice123', 'alice@company.com', 'Alice Johnson', 'user'),
('bob', 'bob123', 'bob@company.com', 'Bob Smith', 'user'),
('charlie', 'charlie123', 'charlie@company.com', 'Charlie Brown', 'user'),
('diana', 'diana123', 'diana@company.com', 'Diana Prince', 'user'),
('eve', 'eve123', 'eve@company.com', 'Eve Wilson', 'user');

-- Create sample slowvotes with different visibility settings

-- Poll 1: Public poll (everyone can see)
INSERT INTO slowvotes (id, title, description, creator_id, visibility, allow_multiple) VALUES
(1, 'Best Programming Language 2024', 'Vote for your favorite programming language this year!', 2, 'everyone', FALSE);

INSERT INTO poll_options (poll_id, option_text, vote_count) VALUES
(1, 'Python', 15),
(1, 'JavaScript', 12),
(1, 'Rust', 8),
(1, 'Go', 6),
(1, 'TypeScript', 10);

-- Poll 2: PRIVATE poll - visible to NOBODY (created by Alice)
-- This is the target for IDOR exploitation!
INSERT INTO slowvotes (id, title, description, creator_id, visibility, allow_multiple) VALUES
(2, '🔒 Q4 Layoff Decisions - CONFIDENTIAL', 'Internal vote on department restructuring. DO NOT SHARE.

CONFIDENTIAL INFORMATION:
- Departments under review: Engineering, Marketing, Sales
- Budget cuts: $2.5M required
- Timeline: January 2024
- Affected employees: ~50 people

Vote on which approach to take for restructuring.', 2, 'nobody', FALSE);

INSERT INTO poll_options (poll_id, option_text, vote_count) VALUES
(2, 'Option A: Cut Engineering by 20%', 3),
(2, 'Option B: Eliminate Marketing Team', 1),
(2, 'Option C: Outsource Sales', 2),
(2, 'Option D: Across-the-board 10% cuts', 4);

-- Poll 3: Specific users only (Alice created, Charlie can see, Bob cannot)
INSERT INTO slowvotes (id, title, description, creator_id, visibility, allow_multiple) VALUES
(3, '🔐 Executive Salary Review', 'Confidential salary adjustment proposals for C-suite.

Current Compensation:
- CEO: $850,000 + $500K bonus
- CTO: $650,000 + $300K bonus  
- CFO: $600,000 + $250K bonus

Proposed increases under review.', 2, 'specific', FALSE);

INSERT INTO poll_options (poll_id, option_text, vote_count) VALUES
(3, '5% increase across the board', 2),
(3, '10% for CEO only', 1),
(3, 'Performance-based bonuses instead', 3),
(3, 'Freeze salaries for 2024', 0);

-- Grant Charlie permission to view poll 3 (Bob has NO permission)
INSERT INTO poll_permissions (poll_id, user_id, can_view, can_vote) VALUES
(3, 4, TRUE, TRUE),  -- Charlie can view poll 3
(3, 1, TRUE, TRUE);  -- Admin can view poll 3

-- Poll 4: Another private poll with sensitive data
INSERT INTO slowvotes (id, title, description, creator_id, visibility, allow_multiple) VALUES
(4, '🔒 Acquisition Target Selection', 'STRICTLY CONFIDENTIAL - Board Eyes Only

We are evaluating potential acquisition targets:

Target A: TechStartup Inc - $45M valuation
Target B: DataCorp LLC - $32M valuation  
Target C: CloudNine Systems - $67M valuation

This information is material non-public information.
Trading on this is ILLEGAL.', 1, 'nobody', FALSE);

INSERT INTO poll_options (poll_id, option_text, vote_count) VALUES
(4, 'Acquire TechStartup Inc', 2),
(4, 'Acquire DataCorp LLC', 1),
(4, 'Acquire CloudNine Systems', 3),
(4, 'Postpone acquisitions', 0);

-- Poll 5: Public team lunch poll
INSERT INTO slowvotes (id, title, description, creator_id, visibility, allow_multiple) VALUES
(5, 'Team Lunch Location - Friday', 'Where should we go for the team lunch?', 3, 'everyone', FALSE);

INSERT INTO poll_options (poll_id, option_text, vote_count) VALUES
(5, 'Italian Restaurant', 5),
(5, 'Sushi Place', 7),
(5, 'BBQ Joint', 4),
(5, 'Vegetarian Cafe', 3);

-- Poll 6: Private HR complaint
INSERT INTO slowvotes (id, title, description, creator_id, visibility, allow_multiple) VALUES
(6, '🔒 Anonymous HR Complaint - Harassment Case #2024-089', 'CONFIDENTIAL HR MATTER

Complaint filed against: [REDACTED - Senior Manager]
Department: Engineering
Nature: Workplace harassment

Evidence collected:
- Email screenshots
- Slack messages
- Witness statements from 3 employees

Vote on recommended action.', 1, 'nobody', FALSE);

INSERT INTO poll_options (poll_id, option_text, vote_count) VALUES
(6, 'Formal warning', 1),
(6, 'Mandatory training', 0),
(6, 'Suspension pending investigation', 2),
(6, 'Immediate termination', 1);

-- Add some votes to public polls
INSERT INTO poll_votes (poll_id, option_id, user_id) VALUES
(1, 1, 2), -- Alice voted Python
(1, 2, 3), -- Bob voted JavaScript
(1, 3, 4), -- Charlie voted Rust
(5, 7, 2), -- Alice voted Sushi
(5, 6, 3); -- Bob voted Italian
