<?php
// add_ocean_rooms.php
// This script adds "ocean" rooms back to the database
require_once 'connection.php';

header('Content-Type: text/html; charset=utf-8');

try {
    // Check if rooms table exists
    $table_check = $conn->query("SHOW TABLES LIKE 'rooms'");
    if ($table_check->num_rows === 0) {
        die("❌ Rooms table not found. Please run rooms_setup.sql first.");
    }
    
    // Check if ocean rooms already exist
    $check = $conn->query("SELECT COUNT(*) as count FROM rooms WHERE room_type = 'ocean'");
    $exists = $check->fetch_assoc()['count'];
    
    if ($exists > 0) {
        echo "<h3>✅ Ocean rooms already exist in database ($exists rooms)</h3>";
        echo "<p>No need to add them again.</p>";
        echo "<a href='index.php'>← Back to Home</a>";
        exit;
    }
    
    // Add ocean view rooms (one per floor, room numbers ending in 4)
    $ocean_rooms = [
        ['104', 1, 129.00],
        ['204', 2, 129.00],
        ['304', 3, 129.00],
        ['404', 4, 129.00]
    ];
    
    $stmt = $conn->prepare("INSERT INTO rooms (room_number, floor, room_type, price_per_night, status, description, features) VALUES (?, ?, 'ocean', ?, 'available', ?, ?)");
    
    $description = 'Enjoy stunning ocean views from your private window';
    $features = 'Ocean view window, Queen bed, Sitting area, Mini-fridge, Room service';
    
    $added = 0;
    foreach ($ocean_rooms as $room) {
        $stmt->bind_param("sidss", $room[0], $room[1], $room[2], $description, $features);
        if ($stmt->execute()) {
            $added++;
        }
    }
    
    $stmt->close();
    
    echo "<h3 style='color: green;'>✅ Successfully added $added Ocean View rooms!</h3>";
    echo "<p>Added rooms: 104, 204, 304, 404</p>";
    echo "<p>Price: \$129.00 per night</p>";
    
    // Show current room types
    $result = $conn->query("SELECT room_type, COUNT(*) as count FROM rooms GROUP BY room_type ORDER BY room_type");
    echo "<h3>Current Room Types:</h3><ul>";
    while ($row = $result->fetch_assoc()) {
        echo "<li>" . htmlspecialchars($row['room_type']) . ": " . $row['count'] . " rooms</li>";
    }
    echo "</ul>";
    
    echo "<br><p><strong>Now refresh your main page and try booking an Ocean View Cabin!</strong></p>";
    echo "<a href='index.php' style='background: #10b981; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>← Back to Home</a>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>

