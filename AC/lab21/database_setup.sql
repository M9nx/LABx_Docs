-- Lab 21: IDOR on Stocky Application - Low Stock Variants Column Settings
-- Database Setup Script

CREATE DATABASE IF NOT EXISTS ac_lab21;
USE ac_lab21;

-- Drop existing tables
DROP TABLE IF EXISTS column_settings;
DROP TABLE IF EXISTS low_stock_variants;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS stores;
DROP TABLE IF EXISTS users;

-- Users table (Stocky app users)
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Stores table (like Shopify stores)
CREATE TABLE stores (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    store_name VARCHAR(100) NOT NULL,
    store_slug VARCHAR(50) UNIQUE NOT NULL,
    domain VARCHAR(100) NOT NULL,
    plan_type ENUM('basic', 'professional', 'enterprise') DEFAULT 'basic',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Products table
CREATE TABLE products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    sku VARCHAR(50) NOT NULL,
    variant_title VARCHAR(100),
    price DECIMAL(10,2) NOT NULL,
    cost DECIMAL(10,2) DEFAULT 0,
    stock_quantity INT DEFAULT 0,
    reorder_point INT DEFAULT 10,
    lead_time_days INT DEFAULT 7,
    supplier VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (store_id) REFERENCES stores(id)
);

-- Low Stock Variants Settings - Column visibility preferences
-- This is where the IDOR vulnerability exists
CREATE TABLE column_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    store_id INT NOT NULL,
    -- Column visibility toggles
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
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (store_id) REFERENCES stores(id)
);

-- Insert sample users
INSERT INTO users (username, password, email, full_name) VALUES
('user_a', 'usera123', 'usera@test.myshopify.com', 'Alice Anderson'),
('user_b', 'userb123', 'userb@test1.myshopify.com', 'Bob Brown'),
('admin_stocky', 'admin123', 'admin@stockyhq.com', 'Stocky Admin'),
('charlie', 'charlie123', 'charlie@shop.myshopify.com', 'Charlie Chen'),
('david', 'david123', 'david@store.myshopify.com', 'David Davis');

-- Insert stores
INSERT INTO stores (user_id, store_name, store_slug, domain, plan_type) VALUES
(1, 'Test Store A', 'test', 'test.myshopify.com', 'professional'),
(2, 'Test Store B', 'test1', 'test1.myshopify.com', 'basic'),
(3, 'Stocky HQ Store', 'stockyhq', 'stockyhq.myshopify.com', 'enterprise'),
(4, 'Charlie Shop', 'charlieshop', 'charlie.myshopify.com', 'professional'),
(5, 'David Store', 'davidstore', 'david.myshopify.com', 'basic');

-- Insert products for User A's store (store_id = 1)
INSERT INTO products (store_id, title, sku, variant_title, price, cost, stock_quantity, reorder_point, lead_time_days, supplier) VALUES
(1, 'Premium Wireless Headphones', 'WH-001', 'Black', 149.99, 75.00, 5, 10, 14, 'AudioTech Supplies'),
(1, 'Premium Wireless Headphones', 'WH-002', 'White', 149.99, 75.00, 3, 10, 14, 'AudioTech Supplies'),
(1, 'USB-C Charging Cable', 'CC-001', '1m', 19.99, 5.00, 8, 20, 7, 'Cable Co'),
(1, 'USB-C Charging Cable', 'CC-002', '2m', 24.99, 6.00, 2, 15, 7, 'Cable Co'),
(1, 'Laptop Stand Pro', 'LS-001', 'Silver', 79.99, 35.00, 4, 8, 21, 'DeskGear Inc'),
(1, 'Mechanical Keyboard', 'MK-001', 'RGB', 129.99, 60.00, 6, 10, 14, 'KeyMaster');

-- Insert products for User B's store (store_id = 2)
INSERT INTO products (store_id, title, sku, variant_title, price, cost, stock_quantity, reorder_point, lead_time_days, supplier) VALUES
(2, 'Organic Green Tea', 'TEA-001', '100g Pack', 12.99, 4.00, 15, 25, 10, 'TeaWorld'),
(2, 'Organic Green Tea', 'TEA-002', '250g Pack', 28.99, 9.00, 7, 15, 10, 'TeaWorld'),
(2, 'Herbal Honey', 'HON-001', '500ml', 18.99, 7.00, 3, 12, 14, 'BeeHive Farms'),
(2, 'Ceramic Mug Set', 'MUG-001', 'Set of 4', 34.99, 12.00, 4, 10, 21, 'CeramicArts'),
(2, 'Bamboo Coasters', 'BC-001', '6 Pack', 14.99, 3.00, 9, 20, 7, 'EcoHome');

-- Insert products for Admin store (store_id = 3)
INSERT INTO products (store_id, title, sku, variant_title, price, cost, stock_quantity, reorder_point, lead_time_days, supplier) VALUES
(3, 'Enterprise Server Rack', 'ESR-001', '42U', 2499.99, 1200.00, 2, 3, 30, 'ServerPro'),
(3, 'Network Switch', 'NS-001', '48 Port', 899.99, 450.00, 5, 8, 14, 'NetGear Pro');

-- Insert column settings for each store
-- THESE ARE THE VULNERABLE RECORDS - settings can be modified via IDOR
INSERT INTO column_settings (id, store_id, show_grade, show_product_title, show_variant_title, show_sku, show_lost_per_day, show_reorder_point, show_lead_time, show_need, show_depletion_days, show_depletion_date, show_next_due_date, show_stock, show_on_po, show_on_order, show_shopify_products_only) VALUES
-- User A's settings (ID: 111111) - VICTIM's settings
(111111, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
-- User B's settings (ID: 111112) - ATTACKER's settings
(111112, 2, 1, 1, 1, 1, 0, 1, 0, 1, 0, 0, 0, 1, 0, 0, 1),
-- Admin's settings (ID: 111113)
(111113, 3, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0),
-- Charlie's settings (ID: 111114)
(111114, 4, 1, 1, 0, 1, 0, 1, 0, 1, 0, 0, 0, 1, 0, 0, 1),
-- David's settings (ID: 111115)
(111115, 5, 1, 1, 1, 0, 0, 0, 0, 1, 0, 0, 0, 1, 0, 0, 1);
