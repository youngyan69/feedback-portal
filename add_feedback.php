<?php
session_start();
include 'db.php';

// Allow testing without login
// Normally, this would be: $user_id = $_SESSION['id'];
$user_id = 1; // TEMP for testing — change later to $_SESSION['id']

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_name = $conn->real_escape_string($_POST['course_name']);
    $comment = $conn->real_escape_string($_POST['comment']);
    $rating = (int) $_POST['rating'];

    $sql = "INSERT INTO feedback (user_id, course_name, comment, rating, created_at) 
            VALUES ('$user_id', '$course_name', '$comment', '$rating', NOW())";

    if ($conn->query($sql)) {
        header("Location: dashboard.php?success=1");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
