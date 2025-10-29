<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) header("Location: login.php");

$id = $_GET['id'] ?? null;
$user_id = $_SESSION['user_id'];

$feedback = $conn->query("SELECT * FROM feedback WHERE id=$id AND user_id=$user_id")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $course = $_POST['course'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    $stmt = $conn->prepare("UPDATE feedback SET course_name=?, rating=?, comment=? WHERE id=? AND user_id=?");
    $stmt->bind_param("sisii", $course, $rating, $comment, $id, $user_id);
    if ($stmt->execute()) {
        header("Location: dashboard.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Edit Feedback</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body class="edit-page">
  <div class="form-container">
    <h2>Edit Feedback</h2>
    <form method="POST">
      <input type="text" name="course" value="<?= $feedback['course_name'] ?>" required>
      <select name="rating" required>
        <option><?= $feedback['rating'] ?></option>
        <option>1</option><option>2</option><option>3</option>
        <option>4</option><option>5</option>
      </select>
      <textarea name="comment" rows="4" required><?= $feedback['comment'] ?></textarea>
      <button type="submit">Update</button>
    </form>
    <p><a href="dashboard.php">⬅ Back</a></p>
  </div>
</body>
</html>
