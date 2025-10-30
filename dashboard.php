<?php
session_start();
include 'db.php';

$user_id = $_SESSION['user_id'] ?? 1; // for testing
$filter = $_GET['filter'] ?? 'all';

// Build query based on sidebar selection
switch ($filter) {
    case 'replied':
        $feedbacks = $conn->query("SELECT * FROM feedback WHERE user_id=$user_id AND status='replied'");
        break;
    case 'unreplied':
        $feedbacks = $conn->query("SELECT * FROM feedback WHERE user_id=$user_id AND status='sent'");
        break;
    default:
        $feedbacks = $conn->query("SELECT * FROM feedback WHERE user_id=$user_id");
}
?>

<?php if (isset($_GET['success'])): ?>
    <div class="alert success">Feedback added successfully!</div>
<?php endif; ?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
<div class="dashboard-container">

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>📘 Menu</h2>
        <ul>
            <li><a href="dashboard.php?filter=all" class="<?= $filter=='all'?'active':'' ?>">All Feedback</a></li>
            <li><a href="dashboard.php?filter=replied" class="<?= $filter=='replied'?'active':'' ?>">Replied Feedback</a></li>
            <li><a href="dashboard.php?filter=unreplied" class="<?= $filter=='unreplied'?'active':'' ?>">Unreplied Feedback</a></li>
        </ul>
    </div>

    <!-- Main content -->
    <div class="main-content">
        <h1 class="welcome-text">Welcome to Geek's Feedback Portal</h1>
        
        <div class="feedback-grid">
            <!-- Add Card -->
            <div class="feedback-card add-card" id="openModal">
                <h2>＋ Add Feedback</h2>
                <p>Click to give your feedback</p>
            </div>

            <!-- Show Feedback -->
            <?php if ($feedbacks && $feedbacks->num_rows > 0): ?>
                <?php while($row = $feedbacks->fetch_assoc()): ?>
                    <div class="feedback-card expandable" onclick="expandCard(this)">
                        <h3><?php echo htmlspecialchars($row['course_name']); ?></h3>
                        <p><?php echo htmlspecialchars($row['comment']); ?></p>

                        <?php if ($row['admin_reply']): ?>
                            <p class="reply">💬 <?php echo htmlspecialchars($row['admin_reply']); ?></p>
                        <?php endif; ?>

                        <div class="actions">
                            <?php if ($row['status'] == 'sent'): ?>
                                <a href="update_feedback.php?id=<?= $row['id'] ?>">✏️ Edit</a>
                                <a href="delete_feedback.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete this feedback?');">🗑 Delete</a>
                            <?php elseif ($row['status'] == 'replied' && empty($row['rating'])): ?>
                                <form action="save_rating.php" method="POST">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    <select name="rating" onchange="this.form.submit()">
                                        <option value="">Rate Reply ⭐</option>
                                        <option value="5">⭐⭐⭐⭐⭐</option>
                                        <option value="4">⭐⭐⭐⭐</option>
                                        <option value="3">⭐⭐⭐</option>
                                        <option value="2">⭐⭐</option>
                                        <option value="1">⭐</option>
                                    </select>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php endif; ?>

            <!-- Always show one placeholder -->
            <div class="feedback-card placeholder">
                <h3>Course Name</h3>
                <p>Your feedback will appear here.</p>
                <span>⭐ Rating</span>
            </div>
        </div>
    </div>
</div>

<!-- Modal for adding feedback -->
<div id="feedbackModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeModal">&times;</span>
        <h2>Add Feedback</h2>
        <form action="save_feedback.php" method="POST">
            <label>Course Name</label>
            <input type="text" name="course_name" required>
            <label>Comment</label>
            <textarea name="comment" required></textarea>
            <button type="submit">Submit</button>
        </form>
    </div>
</div>

<script>
const modal = document.getElementById("feedbackModal");
document.getElementById("openModal").onclick = () => modal.style.display = "flex";
document.getElementById("closeModal").onclick = () => modal.style.display = "none";
window.onclick = e => { if (e.target == modal) modal.style.display = "none"; };

function expandCard(card) {
  card.classList.toggle("expanded");
}

<
    function openAddModal() {
        document.getElementById('addFeedbackModal').style.display = 'flex';
    }
    function closeAddModal() {
        document.getElementById('addFeedbackModal').style.display = 'none';
    }



const links = document.querySelectorAll('.sidebar ul li a');
links.forEach(link => {
    link.addEventListener('click', e => {
        links.forEach(l => l.classList.remove('active'));
        e.target.classList.add('active');
    });
});



</script>

</body>
</html>
