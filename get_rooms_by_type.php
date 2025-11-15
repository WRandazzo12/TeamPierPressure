<?php
// get_rooms_by_type.php
session_start();
require_once 'connection.php';

header('Content-Type: application/json');

$room_type = $_GET['room_type'] ?? '';

if (empty($room_type)) {
    echo json_encode([
        'success' => false,
        'message' => 'Room type is required',
        'rooms' => []
    ]);
    exit;
}

try {
    // Check if rooms table exists
    $table_check = $conn->query("SHOW TABLES LIKE 'rooms'");
    if ($table_check->num_rows === 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Rooms table not found',
            'rooms' => []
        ]);
        exit;
    }
    
    // Get available rooms for the specified room type
    $stmt = $conn->prepare("
        SELECT id, room_number, floor, room_type, price_per_night, description 
        FROM rooms 
        WHERE room_type = ? AND status = 'available' 
        ORDER BY floor, room_number
    ");
    $stmt->bind_param("s", $room_type);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $rooms = [];
    while ($row = $result->fetch_assoc()) {
        $rooms[] = [
            'id' => intval($row['id']),
            'room_number' => $row['room_number'],
            'floor' => intval($row['floor']),
            'room_type' => $row['room_type'],
            'price_per_night' => floatval($row['price_per_night']),
            'description' => $row['description']
        ];
    }
    
    $stmt->close();
    
    echo json_encode([
        'success' => true,
        'rooms' => $rooms
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'rooms' => []
    ]);
}
?>

