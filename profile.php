<?php
session_start();
include "connection.php"; // DB connection: sets up $dbc

if (!isset($_SESSION["id"])) {
    header("Location: login.php");
    exit();
}

$id = $_SESSION["id"];

// Initialize variables
$firstname = $lastname = $email = $phone = $gender = $level = "";
$firstnameErr = $lastnameErr = $phoneErr = $emailErr = $genderErr = $levelErr = $passwordErr = "";
$flag = 0;

// Fetch existing user details
$query = "SELECT * FROM users WHERE id = ?";
$stmt = mysqli_prepare($dbc, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    $dbfirstname = $row["firstname"];
    $dblastname  = $row["lastname"];
    $dbemail     = $row["email"];
    $dbphone     = $row["phone"];
    $dbgender    = $row["gender"];
    $dblevel     = $row["level"];
    $dbpw        = $row["pw"]; // assuming this is plain text (ideally it should be hashed)
} else {
    echo "User not found.";
    exit();
}

// On form submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = trim($_POST["firstname"]);
    $lastname  = trim($_POST["lastname"]);
    $email     = trim($_POST["email"]);
    $phone     = trim($_POST["phone"]);
    $gender    = isset($_POST["gender"]) ? trim($_POST["gender"]) : "";
    $level     = trim($_POST["level"]);
    $pw        = trim($_POST["pw"]);

    // Validate
    if (empty($firstname)) { $firstnameErr = "First name is required!"; $flag = 1; }
    if (empty($lastname)) { $lastnameErr = "Last name is required!"; $flag = 1; }
    if (empty($phone)) { $phoneErr = "Phone number is required!"; $flag = 1; }
    if (empty($email)) { $emailErr = "Email is required!"; $flag = 1; }
    if (empty($gender)) { $genderErr = "Gender is required!"; $flag = 1; }
    if (empty($level)) { $levelErr = "Credit level is required!"; $flag = 1; }
    if (empty($pw)) { $passwordErr = "Password is required!"; $flag = 1; }

    // Update only if changes made
    if ($flag == 0) {
        if ($firstname != $dbfirstname || $lastname != $dblastname || $email != $dbemail || $phone != $dbphone ||
            $gender != $dbgender || $level != $dblevel || $pw != $dbpw) {

            $updateQuery = "UPDATE users SET firstname=?, lastname=?, email=?, phone=?, gender=?, level=?, pw=? WHERE id=?";
            $stmt = mysqli_prepare($dbc, $updateQuery);
            mysqli_stmt_bind_param($stmt, "sssssssi", $firstname, $lastname, $email, $phone, $gender, $level, $pw, $id);
            mysqli_stmt_execute($stmt);

            if (mysqli_stmt_affected_rows($stmt) === 1) {
                echo "<p style='color:green;'>Profile updated successfully!</p>";
                $_SESSION["firstname"] = $firstname;
                // Update display vars
                $dbfirstname = $firstname;
                $dblastname = $lastname;
                $dbemail = $email;
                $dbphone = $phone;
                $dbgender = $gender;
                $dblevel = $level;
                $dbpw = $pw;
            } else {
                echo "<p style='color:blue;'>No changes were made.</p>";
            }
        } else {
            echo "<p style='color:blue;'>No changes detected.</p>";
        }
    }
}

mysqli_close($dbc);
?>
