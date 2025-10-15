<?php
$servername = "localhost";
$username   = "wrandazz_William";
$password   = "PierPressure1!";
$database   = "wrandazz_CruiseShipDB";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
