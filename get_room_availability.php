<?php
// get_room_availability.php
session_start();
require_once 'connection.php';

header('Content-Type: application/json');

try {
    // Check if rooms table exists
    $table_check = $conn->query("SHOW TABLES LIKE 'rooms'");
    if ($table_check->num_rows === 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Rooms table not found',
            'floors' => 0,
            'total_rooms' => 0,
            'available_rooms' => 0,
            'rooms_by_type' => []
        ]);
        exit;
    }
    
    // Get total number of floors
    $floor_result = $conn->query("SELECT COUNT(DISTINCT floor) as total_floors FROM rooms");
    $floor_data = $floor_result->fetch_assoc();
    $total_floors = intval($floor_data['total_floors']);
    
    // Get total rooms and available rooms
    $room_stats = $conn->query("
        SELECT 
            COUNT(*) as total_rooms,
            SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) as available_rooms,
            room_type,
            COUNT(*) as type_count,
            SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) as type_available
        FROM rooms 
        GROUP BY room_type
    ");
    
    $rooms_by_type = [];
    $total_rooms = 0;
    $total_available = 0;
    
    while ($row = $room_stats->fetch_assoc()) {
        $rooms_by_type[$row['room_type']] = [
            'total' => intval($row['type_count']),
            'available' => intval($row['type_available'])
        ];
        $total_rooms += intval($row['type_count']);
        $total_available += intval($row['type_available']);
    }
    
    // Get rooms by floor
    $floor_rooms = $conn->query("
        SELECT 
            floor,
            COUNT(*) as total_rooms,
            SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) as available_rooms
        FROM rooms 
        GROUP BY floor
        ORDER BY floor
    ");
    
    $floors_data = [];
    while ($row = $floor_rooms->fetch_assoc()) {
        $floors_data[] = [
            'floor' => intval($row['floor']),
            'total_rooms' => intval($row['total_rooms']),
            'available_rooms' => intval($row['available_rooms'])
        ];
    }
    
    echo json_encode([
        'success' => true,
        'total_floors' => $total_floors,
        'total_rooms' => $total_rooms,
        'available_rooms' => $total_available,
        'rooms_by_type' => $rooms_by_type,
        'floors' => $floors_data
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'floors' => 0,
        'total_rooms' => 0,
        'available_rooms' => 0,
        'rooms_by_type' => []
    ]);
}
?>

