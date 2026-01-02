-- Lab 27: IDOR in Stats API Endpoint
-- Exness-style Trading Platform
-- Allows viewing equity/net profit of any MT account

DROP DATABASE IF EXISTS ac_lab27;
CREATE DATABASE ac_lab27;
USE ac_lab27;

-- Users table (PA = Personal Area accounts)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    country VARCHAR(50),
    verified BOOLEAN DEFAULT FALSE,
    pa_id VARCHAR(20) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- MT Trading Accounts (MetaTrader accounts linked to PA)
CREATE TABLE mt_accounts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    account_number VARCHAR(20) NOT NULL UNIQUE,
    user_id INT NOT NULL,
    account_type ENUM('Standard', 'Pro', 'Raw Spread', 'Zero', 'Standard Cent') DEFAULT 'Standard',
    platform ENUM('MT4', 'MT5') DEFAULT 'MT5',
    currency VARCHAR(3) DEFAULT 'USD',
    leverage VARCHAR(10) DEFAULT '1:2000',
    balance DECIMAL(15,2) DEFAULT 0.00,
    equity DECIMAL(15,2) DEFAULT 0.00,
    margin DECIMAL(15,2) DEFAULT 0.00,
    free_margin DECIMAL(15,2) DEFAULT 0.00,
    margin_level DECIMAL(10,2) DEFAULT 0.00,
    status ENUM('Active', 'Inactive', 'Suspended') DEFAULT 'Active',
    server VARCHAR(50) DEFAULT 'Exness-MT5Real',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Trading Statistics (Daily stats for each account)
CREATE TABLE trading_stats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    account_number VARCHAR(20) NOT NULL,
    stat_date DATE NOT NULL,
    equity DECIMAL(15,2) DEFAULT 0.00,
    net_profit DECIMAL(15,2) DEFAULT 0.00,
    orders_count INT DEFAULT 0,
    trading_volume DECIMAL(15,4) DEFAULT 0.00,
    win_rate DECIMAL(5,2) DEFAULT 0.00,
    UNIQUE KEY unique_stat (account_number, stat_date),
    FOREIGN KEY (account_number) REFERENCES mt_accounts(account_number)
);

-- Orders/Trades History
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(20) NOT NULL UNIQUE,
    account_number VARCHAR(20) NOT NULL,
    symbol VARCHAR(20) NOT NULL,
    order_type ENUM('Buy', 'Sell', 'Buy Limit', 'Sell Limit', 'Buy Stop', 'Sell Stop') NOT NULL,
    volume DECIMAL(10,2) NOT NULL,
    open_price DECIMAL(15,5) NOT NULL,
    close_price DECIMAL(15,5),
    stop_loss DECIMAL(15,5),
    take_profit DECIMAL(15,5),
    profit DECIMAL(15,2),
    commission DECIMAL(10,2) DEFAULT 0.00,
    swap DECIMAL(10,2) DEFAULT 0.00,
    status ENUM('Open', 'Closed', 'Pending', 'Cancelled') DEFAULT 'Open',
    open_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    close_time TIMESTAMP NULL,
    FOREIGN KEY (account_number) REFERENCES mt_accounts(account_number)
);

-- API Access Logs (for monitoring)
CREATE TABLE api_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    endpoint VARCHAR(255),
    requested_account VARCHAR(20),
    ip_address VARCHAR(45),
    user_agent TEXT,
    is_idor_attempt BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Activity Log
CREATE TABLE activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100),
    details TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert test users
INSERT INTO users (username, password, email, full_name, phone, country, verified, pa_id) VALUES
('attacker', 'attacker123', 'attacker@email.com', 'Alex Trader', '+1-555-0101', 'United States', TRUE, 'PA-10001'),
('victim', 'victim123', 'victim@trading.com', 'Victor Wealthy', '+44-555-0202', 'United Kingdom', TRUE, 'PA-10002'),
('whale', 'whale123', 'whale@bigmoney.com', 'Warren Whale', '+41-555-0303', 'Switzerland', TRUE, 'PA-10003'),
('sarah', 'sarah123', 'sarah@daytrader.com', 'Sarah Swift', '+61-555-0404', 'Australia', TRUE, 'PA-10004'),
('admin', 'admin123', 'admin@exness.com', 'System Admin', '+357-555-0505', 'Cyprus', TRUE, 'PA-00001');

-- Insert MT Accounts
-- Attacker's accounts (modest balance)
INSERT INTO mt_accounts (account_number, user_id, account_type, platform, currency, leverage, balance, equity, margin, free_margin, margin_level, status) VALUES
('MT5-100001', 1, 'Standard', 'MT5', 'USD', '1:2000', 1250.00, 1287.50, 125.00, 1162.50, 1030.00, 'Active'),
('MT5-100002', 1, 'Standard Cent', 'MT5', 'USC', '1:2000', 5000.00, 4875.00, 0.00, 4875.00, 0.00, 'Active');

-- Victim's accounts (HIGH VALUE - TARGET!)
INSERT INTO mt_accounts (account_number, user_id, account_type, platform, currency, leverage, balance, equity, margin, free_margin, margin_level, status) VALUES
('MT5-200001', 2, 'Pro', 'MT5', 'USD', '1:500', 87500.00, 92750.00, 8750.00, 84000.00, 1060.00, 'Active'),
('MT5-200002', 2, 'Raw Spread', 'MT5', 'USD', '1:500', 125000.00, 131250.00, 12500.00, 118750.00, 1050.00, 'Active'),
('MT4-200003', 2, 'Standard', 'MT4', 'EUR', '1:2000', 45000.00, 47250.00, 4500.00, 42750.00, 1050.00, 'Active');

-- Whale's accounts (MASSIVE VALUE)
INSERT INTO mt_accounts (account_number, user_id, account_type, platform, currency, leverage, balance, equity, margin, free_margin, margin_level, status) VALUES
('MT5-300001', 3, 'Zero', 'MT5', 'USD', '1:200', 2500000.00, 2625000.00, 250000.00, 2375000.00, 1050.00, 'Active'),
('MT5-300002', 3, 'Pro', 'MT5', 'USD', '1:500', 750000.00, 787500.00, 75000.00, 712500.00, 1050.00, 'Active');

-- Sarah's accounts
INSERT INTO mt_accounts (account_number, user_id, account_type, platform, currency, leverage, balance, equity, margin, free_margin, margin_level, status) VALUES
('MT5-400001', 4, 'Standard', 'MT5', 'USD', '1:2000', 15000.00, 15750.00, 1500.00, 14250.00, 1050.00, 'Active'),
('MT4-400002', 4, 'Standard', 'MT4', 'USD', '1:2000', 8500.00, 8925.00, 850.00, 8075.00, 1050.00, 'Active');

-- Admin's internal test account
INSERT INTO mt_accounts (account_number, user_id, account_type, platform, currency, leverage, balance, equity, margin, free_margin, margin_level, status) VALUES
('MT5-000001', 5, 'Pro', 'MT5', 'USD', '1:500', 10000000.00, 10500000.00, 1000000.00, 9500000.00, 1050.00, 'Active');

-- Generate Trading Stats for last 365 days
-- This creates realistic looking trading data

DELIMITER //
CREATE PROCEDURE generate_stats()
BEGIN
    DECLARE i INT DEFAULT 0;
    DECLARE stat_day DATE;
    DECLARE acct VARCHAR(20);
    DECLARE base_equity DECIMAL(15,2);
    DECLARE daily_profit DECIMAL(15,2);
    DECLARE daily_orders INT;
    DECLARE daily_volume DECIMAL(15,4);
    
    -- For each account
    DECLARE done INT DEFAULT FALSE;
    DECLARE cur CURSOR FOR SELECT account_number, equity FROM mt_accounts;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    
    OPEN cur;
    
    read_loop: LOOP
        FETCH cur INTO acct, base_equity;
        IF done THEN
            LEAVE read_loop;
        END IF;
        
        SET i = 0;
        WHILE i < 365 DO
            SET stat_day = DATE_SUB(CURDATE(), INTERVAL i DAY);
            
            -- Generate random but realistic looking data
            SET daily_profit = (RAND() * 2 - 1) * base_equity * 0.02; -- +/- 2% daily
            SET daily_orders = FLOOR(RAND() * 50) + 1;
            SET daily_volume = RAND() * base_equity * 0.1;
            
            INSERT INTO trading_stats (account_number, stat_date, equity, net_profit, orders_count, trading_volume, win_rate)
            VALUES (
                acct,
                stat_day,
                base_equity + (daily_profit * (365 - i) / 365),
                daily_profit,
                daily_orders,
                daily_volume,
                50 + (RAND() * 30) -- 50-80% win rate
            )
            ON DUPLICATE KEY UPDATE equity = VALUES(equity);
            
            SET i = i + 1;
        END WHILE;
    END LOOP;
    
    CLOSE cur;
END //
DELIMITER ;

CALL generate_stats();
DROP PROCEDURE generate_stats;

-- Insert some sample orders for victim's high-value account
INSERT INTO orders (order_id, account_number, symbol, order_type, volume, open_price, close_price, profit, commission, swap, status, open_time, close_time) VALUES
('ORD-2001', 'MT5-200001', 'EURUSD', 'Buy', 5.00, 1.08500, 1.09250, 3750.00, -25.00, -12.50, 'Closed', DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY)),
('ORD-2002', 'MT5-200001', 'XAUUSD', 'Sell', 2.00, 2050.00, 2025.00, 5000.00, -20.00, -8.00, 'Closed', DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY)),
('ORD-2003', 'MT5-200001', 'GBPUSD', 'Buy', 3.00, 1.26500, NULL, 1250.00, -15.00, 0.00, 'Open', DATE_SUB(NOW(), INTERVAL 1 DAY), NULL),
('ORD-2004', 'MT5-200002', 'BTCUSD', 'Buy', 1.00, 42500.00, 44750.00, 22500.00, -50.00, -25.00, 'Closed', DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_SUB(NOW(), INTERVAL 3 DAY)),
('ORD-2005', 'MT5-300001', 'XAUUSD', 'Buy', 50.00, 2000.00, 2075.00, 375000.00, -500.00, -250.00, 'Closed', DATE_SUB(NOW(), INTERVAL 7 DAY), DATE_SUB(NOW(), INTERVAL 5 DAY));

-- Grant privileges
GRANT ALL PRIVILEGES ON ac_lab27.* TO 'root'@'localhost';
FLUSH PRIVILEGES;
