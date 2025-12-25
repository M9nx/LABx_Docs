<?php
// Lab 22: Bids API - VULNERABLE TO IDOR
// ⚠️ NO AUTHORIZATION CHECK - Any user can view bids for ANY booking
require_once '../config.php';

header('Content-Type: application/json');

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
    
    // First verify booking exists (but still no ownership check!)
    $stmt = $pdo->prepare("SELECT booking_id, passenger_id, trip_no FROM bookings WHERE booking_id = ?");
    $stmt->execute([$booking_id]);
    $booking = $stmt->fetch();
    
    if (!$booking) {
        jsonResponse([
            'success' => false,
            'error' => 'Booking not found'
        ], 404);
    }
    
    // ⚠️ VULNERABLE: Get bids without checking if current user owns the booking
    // Secure version would verify: booking.passenger_id = current_user_id
    $stmt = $pdo->prepare("
        SELECT * FROM bids WHERE booking_id = ? ORDER BY bid_amount ASC
    ");
    $stmt->execute([$booking_id]);
    $bids = $stmt->fetchAll();
    
    $bidsData = [];
    foreach ($bids as $bid) {
        $bidsData[] = [
            'bid_id' => $bid['bid_id'],
            'driver' => [
                'id' => $bid['driver_id'],
                'name' => $bid['driver_name'],
                'phone' => $bid['driver_phone'],
                'rating' => (float)$bid['driver_rating']
            ],
            'bid_amount' => (float)$bid['bid_amount'],
            'eta_minutes' => (int)$bid['driver_eta'],
            'distance_km' => (float)$bid['driver_distance'],
            'vehicle' => [
                'type' => $bid['vehicle_type'],
                'number' => $bid['vehicle_number']
            ],
            'status' => $bid['status'],
            'created_at' => $bid['created_at']
        ];
    }
    
    jsonResponse([
        'success' => true,
        'data' => [
            'booking_id' => $booking['booking_id'],
            'trip_no' => $booking['trip_no'],
            'total_bids' => count($bidsData),
            'bids' => $bidsData
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
