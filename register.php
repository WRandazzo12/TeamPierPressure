<?php
// register.php
session_start();
require_once __DIR__ . '/connection.php';

$error = '';
$success = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first  = trim($_POST['firstname'] ?? '');
    $last   = trim($_POST['lastname'] ?? '');
    $email  = trim($_POST['email'] ?? '');
    $pass   = $_POST['password'] ?? '';

    // basic validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif ($first === '' || $last === '') {
        $error = 'Please enter your first and last name.';
    } elseif ($pass === '') {
        $error = 'Please enter a password.';
    } else {
        // check if email already exists
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        if (!$check) {
            $error = 'Server error (prepare-1).';
        } else {
            $check->bind_param("s", $email);
            $check->execute();
            $exists = $check->get_result()->fetch_row();
            $check->close();

            if ($exists) {
                $error = 'That email is already registered.';
            } else {
                /**
                 * Table columns (from users-4.sql):
                 * users(id, firstname, lastname, phone, email, gender, level, pw, user_type, php)
                 *
                 * - phone is NOT NULL on your server => supply '' (empty string) to satisfy NOT NULL.
                 * - gender/level can be empty strings for now (your seed shows these can be NULL, but empty strings are fine).
                 * - user_type: 1 = normal user (seed uses 0 = admin).
                 * - php is an INT: must be a number or NULL (NOT an empty string). We’ll use NULL.
                 */
                $hash = password_hash($pass, PASSWORD_DEFAULT);

                $stmt = $conn->prepare("
                INSERT INTO users (first_name, last_name, email, password_hash)
                VALUES (?, ?, ?, ?)
            ");
            if (!$stmt) {
                $error = 'Server error (prepare-2).';
            } else {
                $stmt->bind_param("ssss", $first, $last, $email, $hash);
            
                    if ($stmt->execute()) {
                        $success = 'Account created! You can now log in.';
                    } else {
                        // Optional: uncomment to see the exact DB error while debugging
                        // $error = 'DB error: ' . $stmt->error;
                        $error = 'Could not create account. Please try again.';
                    }
                    $stmt->close();
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Register</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    body { font-family: system-ui, Arial, sans-serif; background:#f5f7fb; }
    .container { max-width: 420px; margin: 6rem auto; background:#fff; padding: 24px; border-radius: 14px; box-shadow: 0 10px 28px rgba(0,0,0,.08); }
    h2 { margin: 0 0 12px; color:#1e3a8a; }
    .msg { margin:.75rem 0; padding:.75rem 1rem; border-radius:8px; }
    .error { background:#fee2e2; color:#991b1b; }
    .success { background:#dcfce7; color:#14532d; }
    label { display:block; font-weight:600; margin-top:.5rem; }
    input { width:100%; box-sizing:border-box; padding:.7rem .85rem; margin-top:.25rem; border:2px solid #e2e8f0; border-radius:10px; }
    button { width:100%; padding:.9rem 1rem; margin-top:1rem; border:0; border-radius:10px; background:#1e40af; color:#fff; font-weight:700; cursor:pointer; }
    a { color:#1e40af; text-decoration:none; font-weight:600; }
    .muted { color:#475569; font-size:.9rem; margin-top:.5rem; }
  </style>
</head>
<body>
  <div class="container">
    <h2>Create Account</h2>
    <?php if ($error): ?><div class="msg error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div class="msg success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

    <form method="POST" action="register.php" novalidate>
      <label for="firstname">First name</label>
      <input id="firstname" name="firstname" type="text" required value="<?= htmlspecialchars($_POST['firstname'] ?? '') ?>">

      <label for="lastname">Last name</label>
      <input id="lastname" name="lastname" type="text" required value="<?= htmlspecialchars($_POST['lastname'] ?? '') ?>">

      <label for="email">Email</label>
      <input id="email" name="email" type="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">

      <label for="password">Password</label>
      <input id="password" name="password" type="password" required>

      <button type="submit">Create Account</button>
      <p class="muted">Already have an account? <a href="login.php">Log in</a></p>
    </form>

    <p class="muted">Note: This matches your class table’s plaintext password storage. For a real app, use <code>password_hash()</code> and <code>password_verify()</code>.</p>
  </div>
</body>
</html>
