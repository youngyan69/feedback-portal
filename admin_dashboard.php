<?php
session_start();
include 'db.php';

// Only admins should access
// if ($_SESSION['role'] != 'admin') {
//     header("Location: index.php");
//     exit();
// }

// Fetch all feedbacks and their replies
$feedbacks = $conn->query("
    SELECT f.*, u.name AS student_name, u.email, r.reply AS admin_reply
    FROM feedback f
    JOIN users u ON f.user_id = u.id
    LEFT JOIN replies r ON f.id = r.feedback_id
    ORDER BY f.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>

    <!-- Include the sidebar -->
    <?php include('admin_sidebar.php'); ?>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <h1>Admin Dashboard</h1>
        <p class="subtitle">Manage and reply to student feedback</p>

        <div class="feedback-grid">
            <?php if ($feedbacks->num_rows > 0): ?>
                <?php while($row = $feedbacks->fetch_assoc()): ?>
                <div class="feedback-card">
                    <div class="card-header">
                        <div>
                            <h2><?php echo htmlspecialchars($row['student_name']); ?></h2>
                            <p class="email"><?php echo htmlspecialchars($row['email']); ?></p>
                        </div>
                    </div>

                    <div class="card-body">
                        <p><strong>Course:</strong> <?php echo htmlspecialchars($row['course_name']); ?></p>
                        <p><strong>Rating:</strong> ⭐ <?php echo htmlspecialchars($row['rating']); ?>/5</p>
                        <p><strong>Comment:</strong> <?php echo htmlspecialchars($row['comment']); ?></p>

                        <?php if ($row['admin_reply']): ?>
                            <p class="reply"><strong>Admin Reply:</strong> <?php echo htmlspecialchars($row['admin_reply']); ?></p>
                        <?php else: ?>
                            <a href="reply_feedback.php?id=<?php echo $row['id']; ?>" class="reply-btn">Reply</a>
                        <?php endif; ?>
                    </div>

                    <div class="card-footer">
                        <small>🕒 <?php echo date("F j, Y, g:i a", strtotime($row['created_at'])); ?></small>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="text-align:center; color:gray;">No feedback found.</p>
            <?php endif; ?>
        </div>
    </div>

</body>
</html>
