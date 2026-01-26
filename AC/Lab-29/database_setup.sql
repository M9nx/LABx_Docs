-- Lab 29: Unauthorized User can View Subscribers of Other Users Newsletters
-- LinkedIn-style Newsletter Platform - IDOR Vulnerability
-- Database: ac_lab29

DROP DATABASE IF EXISTS ac_lab29;
CREATE DATABASE ac_lab29;
USE ac_lab29;

-- Users table (creators and subscribers)
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT UNIQUE NOT NULL,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    headline VARCHAR(200) DEFAULT 'Professional',
    profile_picture VARCHAR(255) DEFAULT 'default-avatar.png',
    location VARCHAR(100) DEFAULT 'Unknown',
    connections INT DEFAULT 0,
    is_creator BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Newsletters table
CREATE TABLE newsletters (
    id INT PRIMARY KEY AUTO_INCREMENT,
    newsletter_urn VARCHAR(50) UNIQUE NOT NULL,
    creator_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    cover_image VARCHAR(255) DEFAULT 'default-newsletter.png',
    frequency VARCHAR(50) DEFAULT 'Weekly',
    subscriber_count INT DEFAULT 0,
    is_public BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (creator_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- Subscribers table
CREATE TABLE subscribers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    newsletter_id INT NOT NULL,
    user_id INT NOT NULL,
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    notification_enabled BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (newsletter_id) REFERENCES newsletters(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    UNIQUE KEY unique_subscription (newsletter_id, user_id)
);

-- Newsletter articles/posts
CREATE TABLE articles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    newsletter_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT,
    published_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    views INT DEFAULT 0,
    FOREIGN KEY (newsletter_id) REFERENCES newsletters(id) ON DELETE CASCADE
);

-- Activity log for tracking
CREATE TABLE activity_log (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(100),
    target_type VARCHAR(50),
    target_id VARCHAR(100),
    ip_address VARCHAR(45),
    details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- INSERT TEST DATA
-- ============================================

-- Users (mix of creators and regular users)
INSERT INTO users (user_id, username, password, email, full_name, headline, location, connections, is_creator) VALUES
-- Attackers/Test users
(1001, 'attacker', 'attacker123', 'attacker@evil.com', 'Attacker User', 'Security Researcher', 'Unknown', 50, FALSE),
(1002, 'curious_user', 'curious123', 'curious@test.com', 'Curious Cat', 'Just Looking Around', 'Internet', 25, FALSE),

-- Newsletter Creators (victims)
(2001, 'alice_ceo', 'alice123', 'alice@techcorp.com', 'Alice Johnson', 'CEO at TechCorp | Forbes 30 Under 30', 'San Francisco, CA', 15420, TRUE),
(2002, 'bob_investor', 'bob123', 'bob@ventures.com', 'Robert Martinez', 'Partner at Apex Ventures | Angel Investor', 'New York, NY', 28750, TRUE),
(2003, 'carol_professor', 'carol123', 'carol@stanford.edu', 'Dr. Carol Williams', 'Professor of Computer Science at Stanford', 'Palo Alto, CA', 9840, TRUE),

-- Subscribers (sensitive data - these users subscribed to newsletters)
(3001, 'john_executive', 'john123', 'john.smith@megacorp.com', 'John Smith', 'VP of Engineering at MegaCorp', 'Seattle, WA', 5420, FALSE),
(3002, 'emma_founder', 'emma123', 'emma@startup.io', 'Emma Chen', 'Founder & CEO at StartupIO | YC W23', 'Austin, TX', 8920, FALSE),
(3003, 'david_cto', 'david123', 'david.brown@enterprise.com', 'David Brown', 'CTO at Enterprise Solutions', 'Boston, MA', 12450, FALSE),
(3004, 'sarah_director', 'sarah123', 'sarah@fintech.com', 'Sarah Miller', 'Director of Product at FinTech Inc', 'Chicago, IL', 6780, FALSE),
(3005, 'michael_partner', 'michael123', 'michael@lawfirm.com', 'Michael Davis', 'Managing Partner at Davis & Associates', 'Los Angeles, CA', 4320, FALSE),
(3006, 'jennifer_vp', 'jennifer123', 'jennifer@bank.com', 'Jennifer Wilson', 'VP of Strategy at Global Bank', 'London, UK', 18920, FALSE),
(3007, 'robert_ciso', 'robert123', 'robert@security.com', 'Robert Taylor', 'CISO at SecureTech Industries', 'Washington, DC', 7650, FALSE),
(3008, 'lisa_investor', 'lisa123', 'lisa@angelnetwork.com', 'Lisa Anderson', 'Angel Investor | Board Member', 'Miami, FL', 22340, FALSE),
(3009, 'james_architect', 'james123', 'james@cloudco.com', 'James Thomas', 'Principal Architect at CloudCo', 'Denver, CO', 3890, FALSE),
(3010, 'maria_researcher', 'maria123', 'maria@research.edu', 'Dr. Maria Garcia', 'Senior Researcher at MIT AI Lab', 'Cambridge, MA', 5670, FALSE);

-- Newsletters
INSERT INTO newsletters (newsletter_urn, creator_id, title, description, frequency, subscriber_count) VALUES
-- Alice's Newsletter (victim 1)
('fsd_contentSeries:7890123456', 2001, 'Tech Leadership Weekly', 
 'Insights on leading engineering teams, startup culture, and emerging technologies. Join 5000+ tech leaders who read this every week.',
 'Weekly', 8),

-- Bob's Newsletter (victim 2) - More sensitive financial subscribers
('fsd_contentSeries:8901234567', 2002, 'Venture Capital Insider', 
 'Exclusive insights into VC deals, startup valuations, and investment strategies. For accredited investors and founders.',
 'Bi-weekly', 6),

-- Carol's Newsletter (victim 3)
('fsd_contentSeries:9012345678', 2003, 'AI Research Digest', 
 'Weekly digest of the latest AI/ML research papers, industry applications, and academic insights.',
 'Weekly', 4);

-- Subscribers for Alice's Newsletter (Newsletter ID: 1)
INSERT INTO subscribers (newsletter_id, user_id) VALUES
(1, 3001), -- John Smith
(1, 3002), -- Emma Chen
(1, 3003), -- David Brown
(1, 3004), -- Sarah Miller
(1, 3007), -- Robert Taylor
(1, 3008), -- Lisa Anderson
(1, 3009), -- James Thomas
(1, 3010); -- Maria Garcia

-- Subscribers for Bob's Newsletter (Newsletter ID: 2) - High-value targets
INSERT INTO subscribers (newsletter_id, user_id) VALUES
(2, 3002), -- Emma Chen (Founder)
(2, 3005), -- Michael Davis (Partner)
(2, 3006), -- Jennifer Wilson (VP at Bank)
(2, 3008), -- Lisa Anderson (Angel Investor)
(2, 3001), -- John Smith
(2, 3004); -- Sarah Miller

-- Subscribers for Carol's Newsletter (Newsletter ID: 3)
INSERT INTO subscribers (newsletter_id, user_id) VALUES
(3, 3003), -- David Brown
(3, 3007), -- Robert Taylor
(3, 3009), -- James Thomas
(3, 3010); -- Maria Garcia

-- Sample Articles
INSERT INTO articles (newsletter_id, title, content, views) VALUES
(1, 'The Future of Remote Engineering Teams', 'In this issue, we explore how top tech companies are adapting...', 4521),
(1, '5 Mistakes First-Time CTOs Make', 'After mentoring dozens of first-time CTOs, here are the patterns I see...', 3892),
(2, 'Q4 2024 VC Market Analysis', 'Deal flow has increased by 23% this quarter...', 2341),
(2, 'How to Evaluate Early-Stage Startups', 'The framework I use when looking at seed deals...', 1987),
(3, 'Transformer Architecture Deep Dive', 'A technical analysis of attention mechanisms...', 1245),
(3, 'Ethics in AI: Current Challenges', 'Addressing bias, fairness, and transparency...', 982);

-- Create indexes for performance
CREATE INDEX idx_newsletters_creator ON newsletters(creator_id);
CREATE INDEX idx_subscribers_newsletter ON subscribers(newsletter_id);
CREATE INDEX idx_subscribers_user ON subscribers(user_id);
CREATE INDEX idx_activity_user ON activity_log(user_id);
