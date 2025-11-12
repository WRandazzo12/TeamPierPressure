<?php
// book_room.php
session_start();
require_once 'connection.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please log in to book a room.']);
    exit;
}

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Get form data
$room_id = $_POST['room_id'] ?? null;
$room_number = $_POST['room_number'] ?? null;
$room_type = $_POST['room_type'] ?? null;
$price_per_night = $_POST['price_per_night'] ?? null;
$cruise_name = $_POST['cruise_name'] ?? null;
$departure_date = $_POST['departure_date'] ?? null;
$passengers = $_POST['passengers'] ?? 2;
$nights = $_POST['nights'] ?? 7;

$user_id = $_SESSION['user_id'];

// Validate required fields
if (!$room_id || !$room_number || !$cruise_name || !$departure_date) {
    echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
    exit;
}

// Calculate total price
$total_price = floatval($price_per_night) * intval($nights) * intval($passengers);

try {
    // Check if rooms table exists
    $table_check = $conn->query("SHOW TABLES LIKE 'rooms'");
    if ($table_check->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Rooms table not found. Please run rooms_setup.sql to create the rooms table.']);
        exit;
    }
    
    // Check if bookings table exists
    $table_check = $conn->query("SHOW TABLES LIKE 'bookings'");
    if ($table_check->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Bookings table not found. Please run rooms_setup.sql to create the bookings table.']);
        exit;
    }
    
    // Start transaction
    $conn->begin_transaction();
    
    // Check if room is still available
    $stmt = $conn->prepare("SELECT id, status FROM rooms WHERE id = ? AND status = 'available'");
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Room is no longer available.']);
        exit;
    }
    
    $stmt->close();
    
    // Create booking
    $stmt = $conn->prepare("INSERT INTO bookings (user_id, room_id, room_number, cruise_name, departure_date, passengers, total_price, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'confirmed')");
    $stmt->bind_param("iisssid", $user_id, $room_id, $room_number, $cruise_name, $departure_date, $passengers, $total_price);
    
    if (!$stmt->execute()) {
        throw new Exception("Failed to create booking: " . $stmt->error);
    }
    
    $booking_id = $conn->insert_id;
    $stmt->close();
    
    // Update room status to booked
    $stmt = $conn->prepare("UPDATE rooms SET status = 'booked' WHERE id = ?");
    $stmt->bind_param("i", $room_id);
    
    if (!$stmt->execute()) {
        throw new Exception("Failed to update room status: " . $stmt->error);
    }
    
    $stmt->close();
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Booking confirmed successfully!',
        'booking_id' => $booking_id
    ]);
    
} catch (Exception $e) {
    // Rollback on error
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>

