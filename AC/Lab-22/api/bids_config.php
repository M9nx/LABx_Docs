<?php
// Lab 22: Bids Config API - VULNERABLE TO IDOR
// ⚠️ NO AUTHORIZATION CHECK - Any user can view bid configuration for ANY booking
require_once '../config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    jsonResponse([
        'success' => false,
        'error' => 'Authentication required'
    ], 401);
}

$trip_id = $_GET['trip_id'] ?? '';

if (empty($trip_id)) {
    jsonResponse([
        'success' => false,
        'error' => 'trip_id parameter is required'
    ], 400);
}

try {
    $pdo = getDBConnection();
    
    // ⚠️ VULNERABLE: Get config without checking if current user owns the booking
    $stmt = $pdo->prepare("
        SELECT bc.*, b.passenger_id, b.trip_no 
        FROM bids_config bc
        JOIN bookings b ON bc.booking_id = b.booking_id
        WHERE bc.booking_id = ?
    ");
    $stmt->execute([$trip_id]);
    $config = $stmt->fetch();
    
    if (!$config) {
        jsonResponse([
            'success' => false,
            'error' => 'Configuration not found'
        ], 404);
    }
    
    // Return sensitive bid configuration data
    jsonResponse([
        'success' => true,
        'data' => [
            'config_id' => $config['config_id'],
            'booking_id' => $config['booking_id'],
            'trip_no' => $config['trip_no'],
            'bid_settings' => [
                'min_bid' => (float)$config['min_bid_amount'],
                'max_bid' => (float)$config['max_bid_amount'],
                'bid_increment' => (float)$config['bid_increment'],
                'max_bids_allowed' => (int)$config['max_bids']
            ],
            'driver_settings' => [
                'max_distance_km' => (float)$config['max_driver_distance'],
                'min_rating' => (float)$config['min_driver_rating'],
                'allowed_vehicle_types' => explode(',', $config['allowed_vehicle_types'])
            ],
            'preferences' => [
                'priority' => $config['bid_priority'],
                'auto_accept' => (bool)$config['auto_accept'],
                'auto_accept_threshold' => (float)$config['auto_accept_threshold']
            ],
            'created_at' => $config['created_at']
        ],
        'api_version' => '1.0',
        'timestamp' => date('c')
    ]);
    
} catch (PDOException $e) {
    jsonResponse([
        'success' => false,
        'error' => 'Database error'
    ], 500);
}
?>
