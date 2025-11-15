<?php
// check_room_types.php - Quick check to see what room types exist in database
require_once 'connection.php';

header('Content-Type: text/html; charset=utf-8');

try {
    $result = $conn->query("SELECT room_type, COUNT(*) as count, GROUP_CONCAT(room_number) as rooms FROM rooms GROUP BY room_type");
    
    echo "<h2>Current Room Types in Database:</h2>";
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr><th>Room Type</th><th>Count</th><th>Room Numbers</th></tr>";
    
    $hasBalcony = false;
    $hasOcean = false;
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td><strong>" . htmlspecialchars($row['room_type']) . "</strong></td>";
        echo "<td>" . $row['count'] . "</td>";
        echo "<td>" . htmlspecialchars($row['rooms']) . "</td>";
        echo "</tr>";
        
        if ($row['room_type'] === 'balcony') $hasBalcony = true;
        if ($row['room_type'] === 'ocean') $hasOcean = true;
    }
    
    echo "</table><br><br>";
    
    if ($hasOcean && !$hasBalcony) {
        echo "<div style='background: #fef2f2; border: 2px solid #ef4444; padding: 15px; border-radius: 8px;'>";
        echo "<h3 style='color: #991b1b;'>⚠️ Issue Found!</h3>";
        echo "<p>Your database has 'ocean' rooms but no 'balcony' rooms.</p>";
        echo "<p><strong>Solution:</strong> <a href='update_ocean_to_balcony.php' style='background: #10b981; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;'>Click here to update ocean → balcony</a></p>";
        echo "</div>";
    } elseif ($hasBalcony) {
        echo "<div style='background: #ecfdf5; border: 2px solid #10b981; padding: 15px; border-radius: 8px;'>";
        echo "<h3 style='color: #059669;'>✅ Database looks good!</h3>";
        echo "<p>You have 'balcony' rooms in your database. If you're still seeing 'No rooms available', try:</p>";
        echo "<ul>";
        echo "<li>Refreshing the page</li>";
        echo "<li>Checking if the rooms have status = 'available'</li>";
        echo "<li>Opening the browser console (F12) to check for JavaScript errors</li>";
        echo "</ul>";
        echo "</div>";
    }
    
    echo "<br><a href='index.php'>← Back to Home</a>";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>

