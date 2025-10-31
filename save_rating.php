<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['rating'])) {
    $id = (int)$_POST['id'];
    $rating = (int)$_POST['rating'];
    if ($rating >=1 && $rating <=5) {
        $stmt = $conn->prepare("UPDATE feedback SET rating = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("iii", $rating, $id, $_SESSION['user_id']);
        $stmt->execute();
    }
}

header("Location: student_dashboard.php");
exit();
