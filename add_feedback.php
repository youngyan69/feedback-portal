<?php
session_start();
include 'db.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['id'] ?? 1; // temporary fallback for testing
    $course = $_POST['course'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    $stmt = $conn->prepare("INSERT INTO feedback (user_id, course_name, rating, comment, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("isis", $user_id, $course, $rating, $comment);
    $stmt->execute();

    header("Location: dashboard.php?success=1");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Feedback</title>
    <link rel="stylesheet" href="css/student.css">
</head>
<body>
<div class="container">
    <h1>Add New Feedback</h1>
    <form method="POST">
        <label>Course Name:</label>
        <input type="text" name="course" required>

        <label>Rating (1–5):</label>
        <input type="number" name="rating" min="1" max="5" required>

        <label>Comment:</label>
        <textarea name="comment" required></textarea>

        <button type="submit" class="btn">Submit Feedback</button>
    </form>
</div>
</body>
</html>
