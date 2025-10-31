<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['reply'])) {
    $id = (int)$_POST['id'];
    $reply = $conn->real_escape_string(trim($_POST['reply']));

    // Update feedback with admin reply and set status to replied
    $sql = "UPDATE feedback SET admin_reply = ?, status = 'replied' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $reply, $id);
    $stmt->execute();

    header("Location: admin_dashboard.php?filter=replied");
    exit();
}

header("Location: admin_dashboard.php");
