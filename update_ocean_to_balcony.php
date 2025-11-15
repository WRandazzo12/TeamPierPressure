<?php
// update_ocean_to_balcony.php
// This script updates existing "ocean" room types to "balcony" to match the frontend
require_once 'connection.php';

header('Content-Type: text/html; charset=utf-8');

try {
    // Check if rooms table exists
    $table_check = $conn->query("SHOW TABLES LIKE 'rooms'");
    if ($table_check->num_rows === 0) {
        die("❌ Rooms table not found. Please run rooms_setup.sql first.");
    }
    
    // First, check what room types exist
    $check_result = $conn->query("SELECT room_type, COUNT(*) as count FROM rooms GROUP BY room_type");
    echo "<h3>Before Update:</h3><ul>";
    while ($row = $check_result->fetch_assoc()) {
        echo "<li>" . htmlspecialchars($row['room_type']) . ": " . $row['count'] . " rooms</li>";
    }
    echo "</ul>";
    
    // Update all "ocean" room types to "balcony"
    $stmt = $conn->prepare("UPDATE rooms SET room_type = 'balcony', price_per_night = 189.00, description = 'Private balcony with breathtaking ocean panoramas', features = 'Private balcony, Queen bed, Sitting area, Mini-bar, Priority boarding' WHERE room_type = 'ocean'");
    
    if ($stmt->execute()) {
        $affected_rows = $conn->affected_rows;
        echo "<h3 style='color: green;'>✅ Successfully updated $affected_rows room(s) from 'ocean' to 'balcony'</h3>";
        echo "<p>✅ Updated price to \$189.00 per night</p>";
        echo "<p>✅ Updated description and features</p>";
        
        // Show after update
        $check_result2 = $conn->query("SELECT room_type, COUNT(*) as count FROM rooms GROUP BY room_type");
        echo "<h3>After Update:</h3><ul>";
        while ($row = $check_result2->fetch_assoc()) {
            echo "<li>" . htmlspecialchars($row['room_type']) . ": " . $row['count'] . " rooms</li>";
        }
        echo "</ul>";
        
        echo "<br><p><strong>Now refresh your main page and try booking a Balcony Cabin!</strong></p>";
        echo "<a href='index.php' style='background: #10b981; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>← Back to Home</a>";
    } else {
        throw new Exception("Failed to update rooms: " . $stmt->error);
    }
    
    $stmt->close();
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>

