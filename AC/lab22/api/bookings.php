<?php
// Lab 22: Bookings API - VULNERABLE TO IDOR
// ⚠️ NO AUTHORIZATION CHECK - Any authenticated user can view ANY booking
require_once '../config.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    jsonResponse([
        'success' => false,
        'error' => 'Authentication required'
    ], 401);
}

$booking_id = $_GET['booking_id'] ?? '';

if (empty($booking_id)) {
    jsonResponse([
        'success' => false,
        'error' => 'booking_id parameter is required'
    ], 400);
}

try {
    $pdo = getDBConnection();
    
    // ⚠️ VULNERABLE QUERY - NO OWNERSHIP CHECK!
    // Secure version would add: AND passenger_id = :user_id
    $stmt = $pdo->prepare("
        SELECT 
            b.*,
            u.full_name as passenger_name,
            u.phone as passenger_phone,
            u.email as passenger_email
        FROM bookings b
        JOIN users u ON b.passenger_id = u.user_id
        WHERE b.booking_id = ?
    ");
    $stmt->execute([$booking_id]);
    $booking = $stmt->fetch();
    
    if (!$booking) {
        jsonResponse([
            'success' => false,
            'error' => 'Booking not found'
        ], 404);
    }
    
    // Return full booking details - SENSITIVE DATA EXPOSED!
    jsonResponse([
        'success' => true,
        'data' => [
            'booking_id' => $booking['booking_id'],
            'trip_no' => $booking['trip_no'],
            'status' => $booking['status'],
            'passenger' => [
                'id' => $booking['passenger_id'],
                'name' => $booking['passenger_name'],
                'phone' => $booking['passenger_phone'],
                'email' => $booking['passenger_email']
            ],
            'pickup' => [
                'address' => $booking['pickup_address'],
                'lat' => $booking['pickup_lat'],
                'lng' => $booking['pickup_lng']
            ],
            'dropoff' => [
                'address' => $booking['dropoff_address'],
                'lat' => $booking['dropoff_lat'],
                'lng' => $booking['dropoff_lng']
            ],
            'fare' => [
                'estimated' => (float)$booking['est_fare'],
                'actual' => $booking['actual_fare'] ? (float)$booking['actual_fare'] : null,
                'customer_bid' => (float)$booking['customer_bid'],
                'fare_range' => [
                    'lower' => (float)$booking['fare_lower'],
                    'upper' => (float)$booking['fare_upper']
                ]
            ],
            'trip_details' => [
                'type' => $booking['trip_type'],
                'distance_km' => (float)$booking['est_distance'],
                'time_minutes' => (int)$booking['est_time']
            ],
            'tracking' => [
                'link' => $booking['tracking_link'],
                'session_id' => $booking['session_id'],
                'order_id' => $booking['order_id']
            ],
            'timestamps' => [
                'created' => $booking['created_at'],
                'updated' => $booking['updated_at']
            ]
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
