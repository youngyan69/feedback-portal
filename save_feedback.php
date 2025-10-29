<?php
include 'db.php';
session_start();

$user_id = $_SESSION['user_id'] ?? 1; // temporary for testing

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $course_name = $_POST['course_name'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    $stmt = $conn->prepare("INSERT INTO feedback (user_id, course_name, rating, comment, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("isis", $user_id, $course_name, $rating, $comment);
    $stmt->execute();
    header("Location: dashboard.php");
    exit();
}
?>
