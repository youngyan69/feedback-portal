<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    $query = "INSERT INTO feedback (user_id, message, created_at) VALUES ('$user_id', '$message', NOW())";
    if ($conn->query($query)) {
        header("Location: student_dashboard.php?success=1");
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
