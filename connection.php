<?php
$hostname="localhost";
$username = "dbrowner_DqBrow";
$password = "Dquavious1";
$dbname = "dbrowner_users";

$conn = mysqli_connect($hostname, $username, $password, $dbname) OR die ("Cannot connect to database,
error...");
//echo "Connected to the database ".$dbname." successfully! <br>";
?>
<?php
$hostname = "localhost";
$username = "dpierred_derron";
$password = " IloveGGC123@";
$database = "dpierred_CruiseLineDataBase";

$conn = mysqli_connect($hostname, $username, $password, $database);
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}
echo "Connected successfully!";
?>
