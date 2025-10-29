<?php
include 'db.php';
session_start();

// Only admins can reply
if ($_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit();
}

if (isset($_POST['id']) && isset($_POST['reply'])) {
    $id = intval($_POST['id']);
    $reply = trim($_POST['reply']);

    $stmt = $conn->prepare("UPDATE feedback SET admin_reply = ? WHERE id = ?");
    $stmt->bind_param("si", $reply, $id);

    if ($stmt->execute()) {
        header("Location: admin_dashboard.php?msg=replied");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
} else {
    echo "Invalid data.";
}
?>
