-- Lab 22: IDOR in Booking Detail and Bids - Information Disclosure
-- Database Setup for RideKea Booking Application

DROP DATABASE IF EXISTS ac_lab22;
CREATE DATABASE ac_lab22 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ac_lab22;

-- Users table (passengers and drivers)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id VARCHAR(50) UNIQUE NOT NULL,
    username VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(150),
    full_name VARCHAR(200),
    user_type ENUM('passenger', 'driver') DEFAULT 'passenger',
    cnic VARCHAR(20),
    address TEXT,
    city VARCHAR(100) DEFAULT 'Karachi',
    profile_rating DECIMAL(3,2) DEFAULT 5.00,
    access_token VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Bookings/Trips table
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id VARCHAR(50) UNIQUE NOT NULL,
    trip_no VARCHAR(20) UNIQUE NOT NULL,
    passenger_id VARCHAR(50) NOT NULL,
    driver_id VARCHAR(50) DEFAULT NULL,
    
    -- Pickup info
    pickup_lat DECIMAL(10, 8) NOT NULL,
    pickup_lng DECIMAL(11, 8) NOT NULL,
    pickup_address TEXT NOT NULL,
    
    -- Dropoff info
    dropoff_lat DECIMAL(10, 8) NOT NULL,
    dropoff_lng DECIMAL(11, 8) NOT NULL,
    dropoff_address TEXT NOT NULL,
    
    -- Fare info
    est_fare DECIMAL(10, 2) NOT NULL,
    actual_fare DECIMAL(10, 2),
    customer_bid DECIMAL(10, 2),
    fare_upper DECIMAL(10, 2),
    fare_lower DECIMAL(10, 2),
    
    -- Distance & time
    est_distance INT DEFAULT 0,
    est_time INT DEFAULT 0,
    
    -- Status
    status ENUM('pending', 'accepted', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
    cancel_reason VARCHAR(255),
    cancelled_by VARCHAR(50),
    
    -- Tracking
    tracking_link VARCHAR(255),
    service_code INT DEFAULT 23,
    trip_type VARCHAR(50) DEFAULT 'Sawari',
    
    -- Metadata
    creator_type VARCHAR(20) DEFAULT 'iOS',
    session_id VARCHAR(100),
    order_id VARCHAR(100),
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (passenger_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Bids table (driver bids on bookings)
CREATE TABLE bids (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bid_id VARCHAR(50) UNIQUE NOT NULL,
    booking_id VARCHAR(50) NOT NULL,
    driver_id VARCHAR(50) NOT NULL,
    bid_amount DECIMAL(10, 2) NOT NULL,
    driver_eta INT DEFAULT 5,
    driver_distance DECIMAL(5, 2),
    driver_rating DECIMAL(3, 2),
    driver_name VARCHAR(100),
    driver_phone VARCHAR(20),
    vehicle_type VARCHAR(50),
    vehicle_number VARCHAR(20),
    status ENUM('pending', 'accepted', 'rejected', 'expired') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (booking_id) REFERENCES bookings(booking_id) ON DELETE CASCADE,
    FOREIGN KEY (driver_id) REFERENCES users(user_id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Bids config table (per-region configuration)
CREATE TABLE bids_config (
    id INT AUTO_INCREMENT PRIMARY KEY,
    config_id VARCHAR(50) UNIQUE NOT NULL,
    city VARCHAR(100) NOT NULL,
    service_code INT NOT NULL,
    min_bid INT DEFAULT 20,
    max_bid INT DEFAULT 500,
    bid_increment INT DEFAULT 20,
    bid_values JSON,
    durations JSON,
    config_hash VARCHAR(64),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Insert sample users
INSERT INTO users (user_id, username, password, phone, email, full_name, user_type, cnic, address, city, access_token) VALUES
-- Passengers
('USR_P_65a1b2c3d4e5', 'victim_user', 'victim123', '+92-300-1234567', 'victim@email.com', 'Ahmed Khan (Victim)', 'passenger', '42101-1234567-1', '123 Main Street, Clifton Block 5', 'Karachi', 'tok_victim_abc123xyz789'),
('USR_P_78f9g0h1i2j3', 'attacker_user', 'attacker123', '+92-301-9876543', 'attacker@email.com', 'Ali Hassan (Attacker)', 'passenger', '42101-7654321-9', '456 Defense Road, DHA Phase 6', 'Karachi', 'tok_attacker_def456uvw012'),
('USR_P_34k5l6m7n8o9', 'passenger3', 'pass123', '+92-302-5551234', 'sara@email.com', 'Sara Ahmed', 'passenger', '42101-1112223-3', '789 Gulshan Block 14', 'Karachi', 'tok_sara_ghi789abc456'),
('USR_P_90p1q2r3s4t5', 'passenger4', 'pass123', '+92-303-5554321', 'omar@email.com', 'Omar Farooq', 'passenger', '42101-4445556-7', '321 North Nazimabad', 'Karachi', 'tok_omar_jkl012def789'),

-- Drivers
('USR_D_12u3v4w5x6y7', 'driver1', 'driver123', '+92-310-1111111', 'driver1@ridekea.com', 'Muhammad Imran', 'driver', '42101-9998887-6', '111 Korangi Industrial Area', 'Karachi', 'tok_driver1_mno345ghi012'),
('USR_D_56z7a8b9c0d1', 'driver2', 'driver123', '+92-311-2222222', 'driver2@ridekea.com', 'Bilal Ahmed', 'driver', '42101-6665554-3', '222 Landhi Town', 'Karachi', 'tok_driver2_pqr678jkl345'),
('USR_D_89e0f1g2h3i4', 'driver3', 'driver123', '+92-312-3333333', 'driver3@ridekea.com', 'Faisal Khan', 'driver', '42101-3332221-0', '333 Malir Cantt', 'Karachi', 'tok_driver3_stu901mno678');

-- Insert sample bookings (victim's bookings with sensitive data)
INSERT INTO bookings (booking_id, trip_no, passenger_id, driver_id, pickup_lat, pickup_lng, pickup_address, dropoff_lat, dropoff_lng, dropoff_address, est_fare, actual_fare, customer_bid, fare_upper, fare_lower, est_distance, est_time, status, tracking_link, session_id, order_id) VALUES
-- Victim's bookings (sensitive!)
('BKG_65f4e3d2c1b0', 'PKX170955617', 'USR_P_65a1b2c3d4e5', 'USR_D_12u3v4w5x6y7', 24.8607, 67.0011, '123 Main Street, Clifton Block 5, Karachi - VICTIM HOME ADDRESS', 24.8918, 67.0822, 'Jinnah International Airport, Karachi', 850.00, 900.00, 800.00, 950.00, 750.00, 15200, 2400, 'completed', 'https://track.ridekea.net/PKX170955617', 'sess_victim_001', 'ORD_V001'),
('BKG_78a9b0c1d2e3', 'PKX170955618', 'USR_P_65a1b2c3d4e5', 'USR_D_56z7a8b9c0d1', 24.8607, 67.0011, '123 Main Street, Clifton Block 5, Karachi - VICTIM HOME', 24.8255, 67.0340, 'Dolmen Mall Clifton, Karachi', 250.00, 280.00, 250.00, 300.00, 200.00, 4500, 900, 'completed', 'https://track.ridekea.net/PKX170955618', 'sess_victim_002', 'ORD_V002'),
('BKG_90c1d2e3f4g5', 'PKX170955619', 'USR_P_65a1b2c3d4e5', NULL, 24.8607, 67.0011, '123 Main Street, Clifton Block 5, Karachi - VICTIM HOME', 24.9056, 67.1378, 'Aga Khan Hospital, Stadium Road, Karachi', 450.00, NULL, 400.00, 500.00, 350.00, 8200, 1500, 'pending', 'https://track.ridekea.net/PKX170955619', 'sess_victim_003', 'ORD_V003'),

-- Attacker's bookings
('BKG_12e3f4g5h6i7', 'PKX170955620', 'USR_P_78f9g0h1i2j3', 'USR_D_89e0f1g2h3i4', 24.8138, 67.0648, '456 Defense Road, DHA Phase 6, Karachi', 24.8607, 67.0011, 'Clifton Beach, Karachi', 350.00, 380.00, 350.00, 400.00, 300.00, 6800, 1200, 'completed', 'https://track.ridekea.net/PKX170955620', 'sess_attacker_001', 'ORD_A001'),

-- Other users' bookings
('BKG_34g5h6i7j8k9', 'PKX170955621', 'USR_P_34k5l6m7n8o9', 'USR_D_12u3v4w5x6y7', 24.9342, 67.0822, '789 Gulshan Block 14, Karachi - SARA HOME', 24.8918, 67.0822, 'Airport', 600.00, 650.00, 600.00, 700.00, 500.00, 11000, 1800, 'completed', 'https://track.ridekea.net/PKX170955621', 'sess_sara_001', 'ORD_S001'),
('BKG_56i7j8k9l0m1', 'PKX170955622', 'USR_P_90p1q2r3s4t5', NULL, 24.9515, 67.0356, '321 North Nazimabad, Karachi - OMAR HOME', 24.8607, 67.0011, 'Clifton', 400.00, NULL, 380.00, 450.00, 350.00, 7500, 1400, 'pending', 'https://track.ridekea.net/PKX170955622', 'sess_omar_001', 'ORD_O001');

-- Insert sample bids (sensitive driver info!)
INSERT INTO bids (bid_id, booking_id, driver_id, bid_amount, driver_eta, driver_distance, driver_rating, driver_name, driver_phone, vehicle_type, vehicle_number, status) VALUES
-- Bids on victim's pending booking
('BID_001_v3', 'BKG_90c1d2e3f4g5', 'USR_D_12u3v4w5x6y7', 420.00, 5, 1.2, 4.85, 'Muhammad Imran', '+92-310-1111111', 'Bike', 'KHI-1234', 'pending'),
('BID_002_v3', 'BKG_90c1d2e3f4g5', 'USR_D_56z7a8b9c0d1', 400.00, 7, 2.1, 4.72, 'Bilal Ahmed', '+92-311-2222222', 'Bike', 'KHI-5678', 'pending'),
('BID_003_v3', 'BKG_90c1d2e3f4g5', 'USR_D_89e0f1g2h3i4', 450.00, 4, 0.8, 4.91, 'Faisal Khan', '+92-312-3333333', 'Bike', 'KHI-9012', 'pending'),

-- Bids on other pending booking
('BID_004_o1', 'BKG_56i7j8k9l0m1', 'USR_D_12u3v4w5x6y7', 390.00, 6, 1.5, 4.85, 'Muhammad Imran', '+92-310-1111111', 'Bike', 'KHI-1234', 'pending'),
('BID_005_o1', 'BKG_56i7j8k9l0m1', 'USR_D_56z7a8b9c0d1', 380.00, 8, 2.5, 4.72, 'Bilal Ahmed', '+92-311-2222222', 'Bike', 'KHI-5678', 'pending');

-- Insert bids configuration
INSERT INTO bids_config (config_id, city, service_code, min_bid, max_bid, bid_increment, bid_values, durations, config_hash) VALUES
('CFG_KHI_23', 'Karachi', 23, 20, 500, 20, '[20, 40, 60, 80, 100, 120, 140, 160, 180, 200, 250, 300, 350, 400, 450, 500]', '[3, 3, 3]', 'hash_khi_23_secret_abc123'),
('CFG_KHI_24', 'Karachi', 24, 50, 1000, 50, '[50, 100, 150, 200, 250, 300, 400, 500, 600, 700, 800, 900, 1000]', '[5, 5, 5]', 'hash_khi_24_secret_def456'),
('CFG_LHR_23', 'Lahore', 23, 20, 400, 20, '[20, 40, 60, 80, 100, 120, 140, 160, 180, 200, 250, 300, 350, 400]', '[3, 3, 3]', 'hash_lhr_23_secret_ghi789'),
('CFG_ISB_23', 'Islamabad', 23, 30, 600, 30, '[30, 60, 90, 120, 150, 180, 210, 240, 270, 300, 350, 400, 500, 600]', '[4, 4, 4]', 'hash_isb_23_secret_jkl012');

-- Create indexes for performance
CREATE INDEX idx_bookings_passenger ON bookings(passenger_id);
CREATE INDEX idx_bookings_status ON bookings(status);
CREATE INDEX idx_bids_booking ON bids(booking_id);
CREATE INDEX idx_bids_driver ON bids(driver_id);
