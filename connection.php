<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host     = '127.0.0.1';
$user     = 'root';
$pass     = '';
$database = 'derronpcuriseshipdb';   // <-- COPY EXACT NAME FROM PHPMYADMIN
$port     = 3306;                    // XAMPP shows MySQL on 3306

try {
  $conn = new mysqli($host, $user, $pass, $database, $port);
  $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
  die("❌ Database connection failed: " . $e->getMessage());
}
?>
