<?php
session_start();
include 'db.php';

// Check if ID is provided
if (!isset($_GET['id'])) {
    die("Feedback ID is missing.");
}

$feedback_id = intval($_GET['id']);

// Fetch the feedback details
$feedback = $conn->query("SELECT f.*, u.name AS student_name, u.email 
    FROM feedback f 
    JOIN users u ON f.user_id = u.id 
    WHERE f.id = $feedback_id")->fetch_assoc();

if (!$feedback) {
    die("Feedback not found.");
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reply = trim($_POST['reply']);
    $admin_id = $_SESSION['id'] ?? 1; // fallback for testing

    if (!empty($reply)) {
        // Insert reply into database
        $stmt = $conn->prepare("INSERT INTO replies (feedback_id, reply, admin_id) VALUES (?, ?, ?)");
        $stmt->bind_param("isi", $feedback_id, $reply, $admin_id);
        if ($stmt->execute()) {
            header("Location: admin_dashboard.php?msg=ReplySent");
            exit();
        } else {
            $error = "Error sending reply: " . $conn->error;
        }
    } else {
        $error = "Reply cannot be empty.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reply to Feedback</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>

<?php include('admin_sidebar.php'); ?>

<div class="main-content">
    <h2>Reply to Feedback</h2>
    <div class="feedback-card">
        <h3><?php echo htmlspecialchars($feedback['student_name']); ?> (<?php echo htmlspecialchars($feedback['email']); ?>)</h3>
        <p><strong>Course:</strong> <?php echo htmlspecialchars($feedback['course_name']); ?></p>
        <p><strong>Feedback:</strong> <?php echo htmlspecialchars($feedback['comment']); ?></p>

        <form method="POST" style="margin-top:20px;">
            <textarea name="reply" placeholder="Type your reply..." rows="5" required></textarea><br>
            <button type="submit" class="reply-btn">Send Reply</button>
            <a href="admin_dashboard.php" class="cancel-btn">Cancel</a>
        </form>

        <?php if (isset($error)): ?>
            <p style="color:red;"><?php echo $error; ?></p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
