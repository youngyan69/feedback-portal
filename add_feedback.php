<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $course = $conn->real_escape_string($_POST['course_name'] ?? $_POST['course'] ?? '');
    $comment = $conn->real_escape_string($_POST['comment'] ?? '');
    $stmt = $conn->prepare("INSERT INTO feedback (user_id, course_name, comment, status, created_at) VALUES (?, ?, ?, 'sent', NOW())");
    $stmt->bind_param("iss", $user_id, $course, $comment);
    $stmt->execute();
}

header("Location: student_dashboard.php");
exit();
