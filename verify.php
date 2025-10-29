<?php
include 'db.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Manual PHPMailer includes (no Composer needed)
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

//require 'vendor/autoload.php';
session_start();

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code = $_POST['code'];

    $stmt = $conn->prepare("SELECT * FROM verification_codes WHERE user_id=? AND code=? ORDER BY created_at DESC LIMIT 1");
    $stmt->bind_param("is", $user_id, $code);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $conn->query("UPDATE users SET verified=1 WHERE id=$user_id");
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid verification code.";
    }
} else {
    // Generate and send code
    $code = rand(100000, 999999);
    $conn->query("INSERT INTO verification_codes (user_id, code) VALUES ($user_id, '$code')");

    $res = $conn->query("SELECT email FROM users WHERE id=$user_id");
    $email = $res->fetch_assoc()['email'];

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'raiyanabdulrashid127@gmail.com';
        $mail->Password = 'vrja jqco avho qsqx';
      // $mail->SMTPSecure = 'tls';
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
       $mail->Port = 465;

        $mail->setFrom('youremail@gmail.com', 'Student Portal');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Your Verification Code';
        $mail->Body = "Your verification code is <b>$code</b>";

        $mail->send();
    } catch (Exception $e) {
        $error = "Error sending email: " . $mail->ErrorInfo;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Verify Email</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="verify-page">
    <div class="form-container">
        <h2>Email Verification</h2>
        <p>We sent a code to your email.</p>
        <form method="POST">
            <input type="text" name="code" placeholder="Enter code" required>
            <button type="submit">Verify</button>
        </form>
        <p class="msg"><?= $error ?? "" ?></p>
    </div>
</body>
</html>
