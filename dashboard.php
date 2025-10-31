<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$filter = $_GET['filter'] ?? 'all';

// build query — student sees their own feedback
switch ($filter) {
  case 'replied':
    $sql = "SELECT * FROM feedback WHERE user_id=$user_id AND status='replied' ORDER BY created_at DESC";
    break;
  case 'unreplied':
    $sql = "SELECT * FROM feedback WHERE user_id=$user_id AND status IN ('sent','read') ORDER BY created_at DESC";
    break;
  default:
    $sql = "SELECT * FROM feedback WHERE user_id=$user_id ORDER BY created_at DESC";
}
$feedbacks = $conn->query($sql);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Dashboard</title>
  <link rel="stylesheet" href="css/student_dashboard.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <div class="sidebar">
    <div class="logo">Portal</div>
    <a href="student_dashboard.php?filter=all" class="<?= $filter=='all'?'active':'' ?>"><i class="fas fa-list"></i><span>All</span></a>
    <a href="student_dashboard.php?filter=unreplied" class="<?= $filter=='unreplied'?'active':'' ?>"><i class="fas fa-envelope"></i><span>Unreplied</span></a>
    <a href="student_dashboard.php?filter=replied" class="<?= $filter=='replied'?'active':'' ?>"><i class="fas fa-check"></i><span>Replied</span></a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
  </div>

  <div class="main">
    <h1 class="neon-text">Welcome, <?= htmlspecialchars($_SESSION['name'] ?? 'Student') ?></h1>

    <div class="feedback-grid">
      <?php if ($feedbacks && $feedbacks->num_rows > 0): ?>
        <?php while ($row = $feedbacks->fetch_assoc()): 
          $id = (int)$row['id'];
          $course = htmlspecialchars($row['course_name'] ?? $row['course'] ?? 'Unknown Course');
          $comment = htmlspecialchars($row['comment'] ?? '');
          $admin_reply = htmlspecialchars($row['admin_reply'] ?? '');
          $rating = $row['rating'] ?? null;
          $status = $row['status'] ?? 'sent';
        ?>
        <div class="feedback-card" onclick="this.classList.toggle('expanded')">
          <h3><?= $course ?></h3>
          <p><?= $comment ?></p>

          <?php if ($admin_reply): ?>
            <div class="reply">💬 <?= $admin_reply ?></div>
            <?php if (empty($rating)): ?>
              <form method="POST" action="save_rating.php" class="rate-form">
                <input type="hidden" name="id" value="<?= $id ?>">
                <select name="rating" onchange="this.form.submit()">
                  <option value="">Rate admin reply</option>
                  <option value="5">⭐⭐⭐⭐⭐</option>
                  <option value="4">⭐⭐⭐⭐</option>
                  <option value="3">⭐⭐⭐</option>
                  <option value="2">⭐⭐</option>
                  <option value="1">⭐</option>
                </select>
              </form>
            <?php else: ?>
              <p class="rated">You rated: <?= intval($rating) ?>/5</p>
            <?php endif; ?>
          <?php else: ?>
            <p class="no-reply">Awaiting admin reply</p>
          <?php endif; ?>
        </div>
        <?php endwhile; ?>
      <?php else: ?>
        <div class="feedback-card">
          <h3>No feedback yet</h3>
          <p>Click + to add feedback</p>
        </div>
      <?php endif; ?>
    </div>

    <button class="add-btn" onclick="openAddModal()">+</button>
  </div>

  <!-- Add modal (student) -->
  <div id="addModal" class="modal">
    <div class="modal-content">
      <button class="close" onclick="closeAddModal()">&times;</button>
      <h2>Add Feedback</h2>
      <form method="POST" action="add_feedback.php">
        <input name="course_name" required placeholder="Course name">
        <textarea name="comment" required placeholder="Write your feedback"></textarea>
        <button class="neon-btn" type="submit">Send</button>
      </form>
    </div>
  </div>

<script>
function openAddModal(){ document.getElementById('addModal').style.display='flex'; }
function closeAddModal(){ document.getElementById('addModal').style.display='none'; }
window.onclick = e => { if (e.target === document.getElementById('addModal')) closeAddModal(); }
</script>
</body>
</html>
