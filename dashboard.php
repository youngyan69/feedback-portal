<?php
session_start();
include 'db.php';

$user_id = $_SESSION['user_id'] ?? 1; // for testing
$filter = $_GET['filter'] ?? 'all';

// Build query based on sidebar selection
switch ($filter) {
    case 'replied':
        $sql = "SELECT * FROM feedback WHERE user_id=$user_id AND status='replied' ORDER BY created_at DESC";
        break;
    case 'unreplied':
        $sql = "SELECT * FROM feedback WHERE user_id=$user_id AND status='sent' ORDER BY created_at DESC";
        break;
    default:
        $sql = "SELECT * FROM feedback WHERE user_id=$user_id ORDER BY created_at DESC";
}
$feedbacks = $conn->query($sql);
?>
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
        <?php if (isset($_GET['success'])): ?>
            <div class="alert success">Feedback added successfully!</div>
        <?php endif; ?>

        <h1 class="welcome-text">Welcome to Geek's Feedback Portal</h1>
        
        <div class="feedback-grid">

            <!-- Add Feedback Card (opens modal) -->
            <div class="feedback-card add-card" onclick="openAddFeedback()">
                <div class="plus-sign">+</div>
                <p>Add Feedback</p>
            </div>

            <!-- Feedback cards from DB -->
            <?php if ($feedbacks && $feedbacks->num_rows > 0): ?>
                <?php while ($row = $feedbacks->fetch_assoc()): ?>
                    <div class="feedback-card expandable" onclick="expandCard(this)">
                        <h3><?php echo htmlspecialchars($row['course_name']); ?></h3>
                        <p><?php echo htmlspecialchars($row['comment']); ?></p>
                        <p><strong>Rating:</strong> <?php echo htmlspecialchars($row['rating']); ?>/5</p>

                        <?php if (!empty($row['admin_reply'])): ?>
                            <p class="reply">💬 <?php echo htmlspecialchars($row['admin_reply']); ?></p>
                        <?php else: ?>
                            <p class="no-reply">No reply yet.</p>
                        <?php endif; ?>

                        <div class="actions">
                            <?php if ($row['status'] == 'sent'): ?>
                                <a href="update_feedback.php?id=<?php echo $row['id']; ?>">✏️ Edit</a>
                                <a href="delete_feedback.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Delete this feedback?');">🗑 Delete</a>
                            <?php elseif ($row['status'] == 'replied' && empty($row['rating'])): ?>
                                <form action="save_rating.php" method="POST">
                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
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

<!-- Modal for adding feedback (used by add-card and floating button) -->
<div id="addFeedbackModal" class="modal">
    <div class="modal-content">
        <span class="close" id="closeAddModal">&times;</span>
        <h2>Add New Feedback</h2>
        <form id="addFeedbackForm" action="save_feedback.php" method="POST">
            <label>Course Name</label>
            <input type="text" name="course_name" required>

            <label>Rating</label>
            <select name="rating" required>
                <option value="5">⭐⭐⭐⭐⭐</option>
                <option value="4">⭐⭐⭐⭐</option>
                <option value="3">⭐⭐⭐</option>
                <option value="2">⭐⭐</option>
                <option value="1">⭐</option>
            </select>

            <label>Comment</label>
            <textarea name="comment" required></textarea>

            <button type="submit">Submit</button>
        </form>
    </div>
</div>

<script>
/* Modal open/close */
const addModal = document.getElementById('addFeedbackModal');
const closeAddBtn = document.getElementById('closeAddModal');
function openAddFeedback() { addModal.style.display = 'flex'; }
function closeAddFeedback() { addModal.style.display = 'none'; }
closeAddBtn.onclick = () => closeAddFeedback();
window.onclick = (e) => {
    if (e.target === addModal) addModal.style.display = 'none';
};

/* Expand card behavior (toggle) */
function expandCard(card) {
  card.classList.toggle('expanded');
}

/* Sidebar link active visual (purely client side) */
const links = document.querySelectorAll('.sidebar ul li a');
links.forEach(link => {
    link.addEventListener('click', e => {
        links.forEach(l => l.classList.remove('active'));
        e.target.classList.add('active');
    });
});
</script>

<!-- Floating Add Feedback Button -->
<a href="javascript:openAddFeedback()" class="floating-btn" title="Add feedback">
  <span>+</span>
</a>

</body>
</html>
