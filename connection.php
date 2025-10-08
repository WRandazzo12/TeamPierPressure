<?php
$hostname="localhost";
$username = "dbrowner_DqBrow";
$password = "Dquavious1";
$dbname = "dbrowner_users";

$conn = mysqli_connect($hostname, $username, $password, $dbname) OR die ("Cannot connect to database,
error...");
//echo "Connected to the database ".$dbname." successfully! <br>";
?>
