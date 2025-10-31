<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$filter = $_GET['filter'] ?? 'all';

// If admin clicked to mark a feedback as read (when they open it)
if (isset($_GET['mark_read'])) {
    $mark_id = (int)$_GET['mark_read'];
    $conn->query("UPDATE feedback SET status = 'read' WHERE id = $mark_id AND status = 'sent'");
    header("Location: admin_dashboard.php?filter=" . urlencode($filter));
    exit();
}

switch ($filter) {
    case 'unreplied':
        $sql = "SELECT f.*, u.name AS student_name FROM feedback f JOIN users u ON f.user_id = u.id WHERE f.status IN ('sent','read') ORDER BY f.created_at DESC";
        $title = "Unreplied Feedback";
        break;
    case 'read':
        $sql = "SELECT f.*, u.name AS student_name FROM feedback f JOIN users u ON f.user_id = u.id WHERE f.status = 'read' ORDER BY f.created_at DESC";
        $title = "Read but Unreplied";
        break;
    case 'replied':
        $sql = "SELECT f.*, u.name AS student_name FROM feedback f JOIN users u ON f.user_id = u.id WHERE f.status = 'replied' ORDER BY f.created_at DESC";
        $title = "Replied Feedback";
        break;
    default:
        $sql = "SELECT f.*, u.name AS student_name FROM feedback f JOIN users u ON f.user_id = u.id ORDER BY f.created_at DESC";
        $title = "All Feedback";
        break;
}

$feedbacks = $conn->query($sql);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="css/admin_dashboard.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <div class="sidebar">
    <div class="logo">Admin</div>
    <a href="admin_dashboard.php?filter=all" class="<?= $filter=='all'?'active':'' ?>"><i class="fas fa-list"></i><span>All</span></a>
    <a href="admin_dashboard.php?filter=unreplied" class="<?= $filter=='unreplied'?'active':'' ?>"><i class="fas fa-envelope-open"></i><span>Unreplied</span></a>
    <a href="admin_dashboard.php?filter=read" class="<?= $filter=='read'?'active':'' ?>"><i class="fas fa-eye"></i><span>Read but Unreplied</span></a>
    <a href="admin_dashboard.php?filter=replied" class="<?= $filter=='replied'?'active':'' ?>"><i class="fas fa-check"></i><span>Replied</span></a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
  </div>

  <div class="main">
    <h1 class="neon-text"><?= htmlspecialchars($title) ?></h1>

    <div class="card-grid">
      <?php if ($feedbacks && $feedbacks->num_rows > 0): ?>
        <?php while ($row = $feedbacks->fetch_assoc()): 
          $id = (int)$row['id'];
          $course = htmlspecialchars($row['course_name'] ?? $row['course'] ?? 'Unknown Course');
          $comment = htmlspecialchars($row['comment'] ?? '');
          $student_name = htmlspecialchars($row['student_name'] ?? 'Student');
          $admin_reply = htmlspecialchars($row['admin_reply'] ?? '');
          $status = $row['status'] ?? 'sent';
        ?>
        <div class="card <?= $admin_reply ? 'replied-card' : '' ?>" id="card-<?= $id ?>">
          <div class="card-head">
            <div>
              <h3><?= $course ?></h3>
              <small><?= $student_name ?> • <?= date("M j, Y g:i a", strtotime($row['created_at'])) ?></small>
            </div>
            <div class="card-actions">
              <a href="admin_dashboard.php?mark_read=<?= $id ?>&filter=<?= urlencode($filter) ?>" title="Mark read">Mark read</a>
              <?php if (!$admin_reply): ?>
                <button onclick="openReplyModal(<?= $id ?>); event.stopPropagation()">Reply</button>
              <?php else: ?>
                <span class="badge">Replied</span>
              <?php endif; ?>
            </div>
          </div>

          <div class="card-body" onclick="toggleExpand(this)">
            <p class="comment"><?= $comment ?></p>

            <?php if ($admin_reply): ?>
              <div class="admin-reply"><strong>Admin:</strong> <?= $admin_reply ?></div>
            <?php endif; ?>
          </div>
        </div>
        <?php endwhile; ?>
      <?php else: ?>
        <div class="card empty">
          <p>No feedback in this category.</p>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Reply Modal -->
  <div id="replyModal" class="modal">
    <div class="modal-inner">
      <button class="close" onclick="closeReplyModal()">&times;</button>
      <h2>Send Reply</h2>
      <form method="POST" action="reply_feedback.php">
        <input type="hidden" name="id" id="replyFeedbackId" value="">
        <textarea name="reply" id="replyText" rows="6" placeholder="Write reply..." required></textarea>
        <button type="submit" class="primary">Send Reply</button>
      </form>
    </div>
  </div>

<script>
function toggleExpand(el){
  el.classList.toggle('expanded');
}

function openReplyModal(id){
  document.getElementById('replyFeedbackId').value = id;
  document.getElementById('replyText').value = '';
  document.getElementById('replyModal').style.display = 'flex';
}

function closeReplyModal(){
  document.getElementById('replyModal').style.display = 'none';
}

window.onclick = function(e){
  const modal = document.getElementById('replyModal');
  if(e.target === modal) closeReplyModal();
}
</script>
</body>
</html>
