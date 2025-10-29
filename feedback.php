<?php
session_start();
include 'db.php';
//if (!isset($_SESSION['user_id'])) {
    //header("Location: login.php");
    //exit();
//}

$message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $course = $_POST['course'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO feedback (user_id, course_name, rating, comment) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isis", $user_id, $course, $rating, $comment);
    if ($stmt->execute()) {
        $message = "Feedback submitted successfully!";
    } else {
        $message = "Error submitting feedback.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Submit Feedback</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body class="feedback-page">
  <div class="form-container">
    <h2>Submit Feedback</h2>
    <form method="POST">
      <input type="text" name="course" placeholder="Course Name" required>
      <select name="rating" required>
        <option value="">Select Rating</option>
        <option>1</option><option>2</option><option>3</option>
        <option>4</option><option>5</option>
      </select>
      <textarea name="comment" rows="4" placeholder="Your feedback..." required></textarea>
      <button type="submit">Submit</button>
    </form>
    <p class="msg"><?= $message ?></p>
    <p><a href="dashboard.php">⬅ Back to Dashboard</a></p>
  </div>
</body>
</html>
