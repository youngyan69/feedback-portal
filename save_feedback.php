<?php
session_start();
include 'db.php';

$user_id = $_SESSION['user_id'] ?? 1; // use real session ID in production

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course = $conn->real_escape_string($_POST['course_name'] ?? '');
    $comment = $conn->real_escape_string($_POST['comment'] ?? '');
    $rating = intval($_POST['rating'] ?? 0);

    // status 'sent' by default
    $stmt = $conn->prepare("INSERT INTO feedback (user_id, course_name, rating, comment, status, created_at) VALUES (?, ?, ?, ?, 'sent', NOW())");
    $stmt->bind_param("isiss", $user_id, $course, $rating, $comment);
    if ($stmt->execute()) {
        header("Location: dashboard.php?success=1");
        exit();
    } else {
        echo "Error saving feedback: " . $conn->error;
    }
} else {
    header("Location: dashboard.php");
    exit();
}
