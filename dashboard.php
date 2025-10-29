<?php
session_start();
include 'db.php';

// For testing, if no login session
$user_id = $_SESSION['user_id'] ?? 1;

// Fetch feedbacks from database
$feedbacks = $conn->query("SELECT * FROM feedback WHERE user_id = $user_id ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
    <div class="dashboard">
        <h1 class="welcome-text">Welcome to Geek's Feedback Portal</h1>
        
        <div class="feedback-grid">
            <!-- Add Feedback Card -->
            <div class="feedback-card add-card" id="openModal">
                <h2>＋ Add Feedback</h2>
                <p>Click to give your feedback about a course</p>
            </div>

            <!-- Show existing feedbacks -->
            <?php if ($feedbacks && $feedbacks->num_rows > 0): ?>
                <?php while($row = $feedbacks->fetch_assoc()): ?>
                    <div class="feedback-card">
                        <h3><?php echo htmlspecialchars($row['course_name']); ?></h3>
                        <p><?php echo htmlspecialchars($row['comment']); ?></p>
                        <span>⭐ <?php echo htmlspecialchars($row['rating']); ?>/5</span>
                        <?php if (!empty($row['admin_reply'])): ?>
                            <p class="reply">💬 Admin Reply: <?php echo htmlspecialchars($row['admin_reply']); ?></p>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>

            <!-- Placeholder cards -->
            <div class="feedback-card placeholder">
                <h3>Course Name</h3>
                <p>Your feedback will appear here.</p>
                <span>⭐ Rating</span>
            </div>
            <div class="feedback-card placeholder">
                <h3>Course Name</h3>
                <p>Your feedback will appear here.</p>
                <span>⭐ Rating</span>
            </div>
            <div class="feedback-card placeholder">
                <h3>Course Name</h3>
                <p>Your feedback will appear here.</p>
                <span>⭐ Rating</span>
            </div>
        </div>
    </div>

    <!-- 🌟 Modal for Adding Feedback -->
    <div id="feedbackModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeModal">&times;</span>
            <h2>Add New Feedback</h2>
            <form action="save_feedback.php" method="POST">
                <label>Course Name</label>
                <input type="text" name="course_name" required>

                <label>Rating</label>
                <select name="rating" required>
                    <option value="5">⭐⭐⭐⭐⭐ (5)</option>
                    <option value="4">⭐⭐⭐⭐ (4)</option>
                    <option value="3">⭐⭐⭐ (3)</option>
                    <option value="2">⭐⭐ (2)</option>
                    <option value="1">⭐ (1)</option>
                </select>

                <label>Comment</label>
                <textarea name="comment" required></textarea>

                <button type="submit">Submit Feedback</button>
            </form>
        </div>
    </div>

    <script>
        // JavaScript for modal
        const modal = document.getElementById("feedbackModal");
        const openBtn = document.getElementById("openModal");
        const closeBtn = document.getElementById("closeModal");

        openBtn.onclick = () => modal.style.display = "flex";
        closeBtn.onclick = () => modal.style.display = "none";
        window.onclick = (e) => { if (e.target === modal) modal.style.display = "none"; };
    </script>
</body>
</html>
