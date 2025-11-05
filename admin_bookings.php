<?php
session_start();
require_once __DIR__ . '/connection.php';

// Restrict access admins only
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 0) {
    echo "<h2 style='color:red; text-align:center;'>Access Denied – Admins Only</h2>";
    exit();
}

// Fetch all bookings and user details
$sql = "
    SELECT 
        b.id AS booking_id, 
        b.room_id, 
        b.date_booked,
        u.firstname, 
        u.lastname, 
        u.email
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    ORDER BY b.date_booked DESC
";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin – All Booked Rooms</title>
    <style>
        table {
            width: 95%;
            margin: 20px auto; 
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            border: 1px solid #333;
        }
        th {
            background: #1d4ed8;
            color: white;
        }
    </style>
</head>
<body>

<h2 style="text-align:center;">Admin Panel – Booked Rooms</h2>

<table>
    <tr>
        <th>Booking ID</th>
        <th>Room</th>
        <th>Date Booked</th>
        <th>User</th>
        <th>Email</th>
        <th>Actions</th>
    </tr>

    <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['booking_id'] ?></td>
                <td><?= $row['room_id'] ?></td>
                <td><?= $row['date_booked'] ?></td>
                <td><?= $row['firstname'] . " " . $row['lastname'] ?></td>
                <td><?= $row['email'] ?></td>
                <td>
                    <a href="edit_booking.php?id=<?= $row['booking_id'] ?>">Edit</a> |
                    <a href="delete_booking.php?id=<?= $row['booking_id'] ?>" onclick="return confirm('Delete this booking?');">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="6" style="text-align:center;">No bookings found.</td></tr>
    <?php endif; ?>
</table>

<div style="text-align:center; margin-top:20px;">
    <a href="index.php">Back to Home</a>
</div>

</body>
</html>
