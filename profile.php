<?php
include "connection.php"; // Include database connection

// -------------------------
// No session — using static ID for testing
$id = 1; // Change this to match a real user ID in your DB
// -------------------------

// Fetch existing user details
$query = "SELECT * FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    $dbfirstname = $row["firstname"];
    $dblastname  = $row["lastname"];
    $dbemail     = $row["email"];
    $dbphone     = $row["phone"];
    $dbgender    = $row["gender"];
    $dbpw        = $row["pw"];
} else {
    echo "User not found.";
    exit();
}

// Initialize error messages
$firstnameErr = $lastnameErr = $phoneErr = $emailErr = $genderErr = $passwordErr = "";
$flag = 0;

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = trim($_POST["firstname"]);
    $lastname  = trim($_POST["lastname"]);
    $email     = trim($_POST["email"]);
    $phone     = trim($_POST["phone"]);
    $gender    = isset($_POST["gender"]) ? trim($_POST["gender"]) : "";
    $pw        = trim($_POST["pw"]);

    // Validate inputs
    if (empty($firstname)) { $firstnameErr = "First name is required!"; $flag = 1; }
    if (empty($lastname))  { $lastnameErr = "Last name is required!"; $flag = 1; }
    if (empty($email))     { $emailErr = "Email is required!"; $flag = 1; }
    if (empty($phone))     { $phoneErr = "Phone number is required!"; $flag = 1; }
    if (empty($gender))    { $genderErr = "Gender is required!"; $flag = 1; }
    if (empty($pw))        { $passwordErr = "Password is required!"; $flag = 1; }

    // Update only if no errors and values have changed
    if ($flag == 0) {
        if (
            $firstname != $dbfirstname || $lastname != $dblastname || $email != $dbemail ||
            $phone != $dbphone || $gender != $dbgender || $pw != $dbpw
        ) {
            $updateQuery = "UPDATE users SET firstname=?, lastname=?, email=?, phone=?, gender=?, pw=? WHERE id=?";
            $stmt = mysqli_prepare($conn, $updateQuery);
            mysqli_stmt_bind_param($stmt, "ssssssi", $firstname, $lastname, $email, $phone, $gender, $pw, $id);
            mysqli_stmt_execute($stmt);

            if (mysqli_stmt_affected_rows($stmt) === 1) {
                echo "<p style='color:green;'>Profile updated successfully!</p>";
                // Update current values for redisplay
                $dbfirstname = $firstname;
                $dblastname  = $lastname;
                $dbemail     = $email;
                $dbphone     = $phone;
                $dbgender    = $gender;
                $dbpw        = $pw;
            } else {
                echo "<p style='color:blue;'>No changes made to your profile.</p>";
            }
        } else {
            echo "<p style='color:blue;'>No updates detected. Your profile info is the same.</p>";
        }
    }
}

mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile Update</title>
    <style>
        .error { color: red; }
        body { font-family: Arial, sans-serif; margin: 20px; }
        form { max-width: 500px; margin: auto; }
        input[type="text"], input[type="password"] {
            width: 100%; padding: 8px; margin: 5px 0 15px 0;
        }
        input[type="submit"] {
            padding: 10px 20px; background-color: #333; color: #fff; border: none;
        }
        h3 { text-align: center; }
    </style>
</head>
<body>

<h3>Update Your Profile Information</h3>

<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
    First Name:<br>
    <input type="text" name="firstname" value="<?php echo htmlspecialchars($dbfirstname); ?>">
    <span class="error">* <?php echo $firstnameErr; ?></span><br><br>

    Last Name:<br>
    <input type="text" name="lastname" value="<?php echo htmlspecialchars($dblastname); ?>">
    <span class="error">* <?php echo $lastnameErr; ?></span><br><br>

    Email:<br>
    <input type="text" name="email" value="<?php echo htmlspecialchars($dbemail); ?>">
    <span class="error">* <?php echo $emailErr; ?></span><br><br>

    Phone:<br>
    <input type="text" name="phone" value="<?php echo htmlspecialchars($dbphone); ?>">
    <span class="error">* <?php echo $phoneErr; ?></span><br><br>

    Gender:<br>
    <input type="radio" name="gender" value="Female" <?php if($dbgender=="Female") echo "checked"; ?>> Female
    <input type="radio" name="gender" value="Male" <?php if($dbgender=="Male") echo "checked"; ?>> Male
    <input type="radio" name="gender" value="Other" <?php if($dbgender=="Other") echo "checked"; ?>> Other
    <span class="error">* <?php echo $genderErr; ?></span><br><br>

    Password:<br>
    <input type="password" name="pw" value="<?php echo htmlspecialchars($dbpw); ?>">
    <span class="error">* <?php echo $passwordErr; ?></span><br><br>

    <input type="submit" name="edit" value="CONFIRM EDIT">
</form>

</body>
</html>
