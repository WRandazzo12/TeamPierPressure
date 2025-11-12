<?php
session_start();
require_once __DIR__ . '/connection.php';


$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = 'Please enter both email and password.';
    } else {
        $sql = "SELECT id, first_name, last_name, email, password_hash
                FROM users
                WHERE email = ?
                LIMIT 1";

        // TEMP DEBUG (remove after)
        echo "<pre>SQL BEING PREPARED:\n{$sql}</pre>";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();

        if ($row && password_verify($password, $row['password_hash'])) {
            $_SESSION['user_id']   = $row['id'];
            $_SESSION['firstname'] = $row['first_name'];
            $_SESSION['lastname']  = $row['last_name'];
            $_SESSION['email']     = $row['email'];
            header('Location: index.php');
            exit;
        } else {
            $error = 'Invalid email or password.';
        }
    }
} 
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f5f5f5; }
    .container { max-width: 400px; margin: 100px auto; background: #fff; padding: 20px; border-radius: 8px; }
    input { width: 100%; padding: 10px; margin: 10px 0; }
    button { width: 100%; padding: 10px; background: #0077cc; color: white; border: none; border-radius: 4px; cursor: pointer; }
    .error { color: red; margin: 10px 0; }
  </style>
</head>
<body>
  <div class="container">
    <h2>Login</h2>
    <?php if ($error): ?><p class="error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <form method="POST">
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>
    <p><a href="register.php">Create an account</a></p>
  </div>
</body>
</html>
