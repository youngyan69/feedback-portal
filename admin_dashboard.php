<?php
session_start();
include 'db.php';

// Only admins can access this page
//if ($_SESSION['role'] != 'admin') {
    //header("Location: index.php");
    //exit();
//}

// Handle reply submission
if (isset($_POST['reply'])) {
    $feedback_id = $_POST['feedback_id'];
    $reply_text = $_POST['reply_text'];
    $conn->query("UPDATE feedback SET admin_reply='$reply_text' WHERE id=$feedback_id");
}

// Fetch all feedbacks
$feedbacks = $conn->query("
    SELECT f.*, u.name AS student_name, u.email 
    FROM feedback f 
    JOIN users u ON f.user_id = u.id 
    ORDER BY f.created_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Feedback Portal</title>
    <link rel="stylesheet" href="css/admin.css">
</head>
<body>
    <div class="container">
        <h1>Admin Dashboard</h1>
        <p class="subtitle">View student feedback and reply</p>

        <div class="feedback-grid">
            <?php while($row = $feedbacks->fetch_assoc()): ?>
            <div class="feedback-card">
                <div class="card-header">
                    <h2><?php echo htmlspecialchars($row['student_name']); ?></h2>
                    <p><?php echo htmlspecialchars($row['email']); ?></p>
                </div>

                <div class="card-body">
                    <p><strong>Course:</strong> <?php echo htmlspecialchars($row['course_name']); ?></p>
                    <p><strong>Feedback:</strong> <?php echo htmlspecialchars($row['comment']); ?></p>

                    <?php if (!empty($row['admin_reply'])): ?>
                        <p class="reply"><strong>Your Reply:</strong> <?php echo htmlspecialchars($row['admin_reply']); ?></p>
                    <?php else: ?>
                        <form method="POST" class="reply-form">
                            <input type="hidden" name="feedback_id" value="<?php echo $row['id']; ?>">
                            <textarea name="reply_text" placeholder="Write a reply..." required></textarea>
                            <button type="submit" name="reply">Send Reply</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
