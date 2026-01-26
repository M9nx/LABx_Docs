-- Lab 19: IDOR - Delete Users Saved Projects
-- Database Schema for Project Portfolio Platform

CREATE DATABASE IF NOT EXISTS ac_lab19;
USE ac_lab19;

-- Users table
DROP TABLE IF EXISTS saved_projects;
DROP TABLE IF EXISTS projects;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    bio TEXT,
    avatar_color VARCHAR(7) DEFAULT '#6366f1',
    role ENUM('user', 'pro', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Projects table (public portfolio items)
CREATE TABLE projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    category VARCHAR(50),
    image_url VARCHAR(255),
    likes_count INT DEFAULT 0,
    views_count INT DEFAULT 0,
    is_featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Saved Projects (bookmarks) - This is where the IDOR vulnerability exists
CREATE TABLE saved_projects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    project_id INT NOT NULL,
    notes TEXT,
    saved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
    UNIQUE KEY unique_save (user_id, project_id)
);

-- Insert sample users
INSERT INTO users (username, password, email, display_name, bio, avatar_color, role) VALUES
('admin', 'admin123', 'admin@projecthub.io', 'Platform Admin', 'Managing ProjectHub since 2020', '#ef4444', 'admin'),
('victim_designer', 'victim123', 'sarah@design.co', 'Sarah Chen', 'UI/UX Designer at TechCorp. Love minimalist design.', '#8b5cf6', 'pro'),
('attacker_user', 'attacker123', 'mike@hack.net', 'Mike Wilson', 'Curious developer exploring new horizons.', '#10b981', 'user'),
('alice_artist', 'alice123', 'alice@artstation.io', 'Alice Morgan', '3D Artist & Illustrator. Creating worlds one pixel at a time.', '#f59e0b', 'pro'),
('bob_developer', 'bob123', 'bob@devhub.com', 'Bob Taylor', 'Full-stack developer. React & Node enthusiast.', '#3b82f6', 'user');

-- Insert sample projects
INSERT INTO projects (user_id, title, description, category, likes_count, views_count, is_featured) VALUES
-- Admin's projects
(1, 'ProjectHub Brand Guidelines', 'Complete brand identity system for our platform', 'Branding', 245, 1820, TRUE),
(1, 'Admin Dashboard Redesign', 'Modern admin panel with dark mode support', 'UI/UX', 189, 1450, TRUE),

-- Sarah's projects (victim)
(2, 'E-commerce App Concept', 'Clean shopping experience for fashion brands', 'Mobile', 567, 3200, TRUE),
(2, 'Banking Dashboard UI', 'Financial dashboard with data visualization', 'UI/UX', 423, 2800, FALSE),
(2, 'Travel App Redesign', 'Reimagining travel booking experience', 'Mobile', 312, 1900, FALSE),
(2, 'Healthcare Portal', 'Patient management system interface', 'Web Design', 198, 1200, FALSE),

-- Mike's projects (attacker)
(3, 'Portfolio Website', 'My personal portfolio built with React', 'Web Design', 89, 450, FALSE),
(3, 'Weather App UI', 'Simple weather application concept', 'Mobile', 67, 320, FALSE),

-- Alice's projects
(4, '3D Character Design', 'Fantasy character for game project', '3D Art', 892, 5600, TRUE),
(4, 'Environment Concept Art', 'Sci-fi landscape illustration', 'Illustration', 654, 4100, FALSE),
(4, 'Product Visualization', '3D renders for tech products', '3D Art', 445, 2900, FALSE),

-- Bob's projects
(5, 'Developer Portfolio', 'Code-themed portfolio design', 'Web Design', 234, 1100, FALSE),
(5, 'API Documentation Site', 'Clean docs template for developers', 'Web Design', 178, 890, FALSE);

-- Insert saved projects (bookmarks)
-- victim_designer (user_id: 2) has saved several projects - these are the targets
INSERT INTO saved_projects (id, user_id, project_id, notes) VALUES
(101, 2, 1, 'Great brand guidelines to reference'),
(102, 2, 9, 'Love this 3D style!'),
(103, 2, 10, 'Inspiration for my next project'),
(104, 2, 12, 'Clean portfolio layout'),
(105, 2, 11, 'Product rendering reference');

-- attacker_user (user_id: 3) has their own saved projects
INSERT INTO saved_projects (id, user_id, project_id, notes) VALUES
(201, 3, 3, 'Nice e-commerce concept'),
(202, 3, 4, 'Dashboard inspiration'),
(203, 3, 9, 'Amazing 3D work');

-- Other users' saved projects
INSERT INTO saved_projects (id, user_id, project_id, notes) VALUES
(301, 4, 3, 'Beautiful mobile design'),
(302, 4, 7, 'Cool portfolio idea'),
(401, 5, 9, 'Character design reference'),
(402, 5, 1, 'Brand guidelines to study');
