-- Lab 30: Stocky Inventory App Database Setup
-- IDOR Vulnerability in Settings Management

CREATE DATABASE IF NOT EXISTS ac_lab30;
USE ac_lab30;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    store_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Settings for Low Stock Variants display
CREATE TABLE IF NOT EXISTS settings_for_low_stock_variants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    show_grade TINYINT(1) DEFAULT 1,
    show_product_title TINYINT(1) DEFAULT 1,
    show_variant_title TINYINT(1) DEFAULT 1,
    show_sku TINYINT(1) DEFAULT 1,
    show_lost_per_day TINYINT(1) DEFAULT 0,
    show_reorder_point TINYINT(1) DEFAULT 1,
    show_lead_time TINYINT(1) DEFAULT 0,
    show_need TINYINT(1) DEFAULT 1,
    show_depletion_days TINYINT(1) DEFAULT 0,
    show_depletion_date TINYINT(1) DEFAULT 0,
    show_next_due_date TINYINT(1) DEFAULT 0,
    show_stock TINYINT(1) DEFAULT 1,
    show_on_po TINYINT(1) DEFAULT 0,
    show_on_order TINYINT(1) DEFAULT 0,
    show_shopify_products_only TINYINT(1) DEFAULT 1,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_title VARCHAR(200) NOT NULL,
    variant_title VARCHAR(100),
    sku VARCHAR(50),
    stock INT DEFAULT 0,
    reorder_point INT DEFAULT 10,
    lead_time INT DEFAULT 7,
    grade CHAR(1) DEFAULT 'B',
    on_po INT DEFAULT 0,
    on_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Activity log table
CREATE TABLE IF NOT EXISTS activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action VARCHAR(50) NOT NULL,
    details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert test users (password: password)
-- NOTE: Hash below is for 'password'. Use setup_db.php for password123
INSERT INTO users (username, password, store_name, email) VALUES
('alice_shop', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', "Alice's Fashion Boutique", 'alice@fashion.example'),
('bob_tech', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', "Bob's Tech Store", 'bob@tech.example'),
('carol_home', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', "Carol's Home Goods", 'carol@home.example'),
('david_sports', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', "David's Sports Outlet", 'david@sports.example');

-- Insert settings (each user has unique column preferences)
-- IMPORTANT: Settings ID corresponds to user ID for easy IDOR testing
INSERT INTO settings_for_low_stock_variants 
(user_id, show_grade, show_product_title, show_variant_title, show_sku, show_lost_per_day, show_reorder_point, show_lead_time, show_need, show_depletion_days, show_depletion_date, show_next_due_date, show_stock, show_on_po, show_on_order, show_shopify_products_only) VALUES
(1, 1, 1, 1, 1, 0, 1, 0, 1, 0, 0, 0, 1, 0, 0, 1),  -- Alice: Default view
(2, 1, 1, 1, 0, 1, 1, 1, 0, 1, 1, 1, 1, 1, 1, 0),  -- Bob: Detailed tech view
(3, 0, 1, 0, 1, 0, 1, 0, 1, 0, 0, 1, 1, 0, 1, 1),  -- Carol: Simple home view
(4, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0);  -- David: All columns view

-- Insert sample products for each store
-- Alice's Fashion products
INSERT INTO products (user_id, product_title, variant_title, sku, stock, reorder_point, lead_time, grade, on_po, on_order) VALUES
(1, 'Summer Dress', 'Blue / Medium', 'DRESS-BLU-M', 5, 10, 5, 'A', 0, 15),
(1, 'Designer Bag', 'Black Leather', 'BAG-BLK-01', 2, 5, 14, 'B', 5, 0),
(1, 'Silk Scarf', 'Red Pattern', 'SCARF-RED-01', 8, 15, 7, 'C', 0, 10);

-- Bob's Tech products
INSERT INTO products (user_id, product_title, variant_title, sku, stock, reorder_point, lead_time, grade, on_po, on_order) VALUES
(2, 'Wireless Mouse', 'Black', 'MOUSE-WL-BLK', 15, 20, 10, 'A', 30, 0),
(2, 'USB-C Hub', '7-Port', 'HUB-USB7-01', 3, 10, 21, 'B', 0, 20),
(2, 'Mechanical Keyboard', 'RGB', 'KB-MECH-RGB', 7, 8, 14, 'A', 10, 0);

-- Carol's Home products
INSERT INTO products (user_id, product_title, variant_title, sku, stock, reorder_point, lead_time, grade, on_po, on_order) VALUES
(3, 'Cotton Towels', 'Set of 4', 'TOWEL-SET-4', 12, 25, 5, 'C', 0, 30),
(3, 'Candle Set', 'Vanilla Scent', 'CANDLE-VAN-3', 4, 10, 7, 'B', 15, 0);

-- David's Sports products
INSERT INTO products (user_id, product_title, variant_title, sku, stock, reorder_point, lead_time, grade, on_po, on_order) VALUES
(4, 'Running Shoes', 'Size 10', 'SHOE-RUN-10', 6, 8, 10, 'A', 0, 12),
(4, 'Yoga Mat', 'Purple', 'MAT-YOGA-PUR', 18, 15, 7, 'B', 0, 0),
(4, 'Dumbbells', '10kg Pair', 'DUMB-10KG-PR', 2, 5, 30, 'A', 5, 0);
