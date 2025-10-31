<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $role = $_POST['role'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND role = ?");
    $stmt->bind_param("ss", $email, $role);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];

        if ($role === 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: dashboard.php");
        }
        exit();
    } else {
        $error = "Invalid credentials or role.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login | Student Feedback Portal</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body {
      background: radial-gradient(circle at top, #00111a, #000);
      color: white;
      font-family: 'Poppins', sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .login-container {
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(0, 255, 255, 0.2);
      border-radius: 20px;
      box-shadow: 0 0 20px rgba(0,255,255,0.3);
      padding: 40px;
      width: 350px;
      text-align: center;
      backdrop-filter: blur(10px);
    }
    input, select {
      width: 90%;
      padding: 10px;
      margin: 10px 0;
      border: none;
      border-radius: 8px;
      background: rgba(0,0,0,0.3);
      color: cyan;
      text-align: center;
    }
    button {
      width: 100%;
      padding: 10px;
      background: cyan;
      border: none;
      border-radius: 8px;
      color: black;
      font-weight: bold;
      cursor: pointer;
      transition: 0.3s;
    }
    button:hover {
      box-shadow: 0 0 15px cyan;
      transform: scale(1.05);
    }
  </style>
</head>
<body>
  <form method="POST" class="login-container">
    <h2 style="color: cyan;">Student Feedback Portal</h2>
    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <select name="role" required>
      <option value="">Select Role</option>
      <option value="student">Student</option>
      <option value="admin">Admin</option>
    </select>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
    <p>Don’t have an account? <a href="register.php" style="color: cyan;">Register</a></p>
  </form>
</body>
</html>
