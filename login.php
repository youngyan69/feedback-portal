<?php
include 'db.php';
session_start();
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            if ($user['verified'] == 0) {
                header("Location: verify.php");
                exit();
            } else {
                if ($user['role'] == 'admin') {
                    header("Location: admin_dashboard.php");
                } else {
                    header("Location: dashboard.php");
                }
                exit();
            }
        } else {
            $message = "Invalid password.";
        }
    } else {
        $message = "User not found.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Login</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body class="login-page">
  <div class="form-container">
    <h2>Login</h2>
    <form method="POST">
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Login</button>
    </form>
    <p>Don’t have an account? <a href="register.php">Register</a></p>
    <p class="msg"><?= $message ?></p>
  </div>
</body>
</html>
