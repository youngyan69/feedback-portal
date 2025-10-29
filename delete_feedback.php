<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) header("Location: login.php");

$id = $_GET['id'] ?? null;
$user_id = $_SESSION['user_id'];

if ($id) {
    $stmt = $conn->prepare("DELETE FROM feedback WHERE id=? AND user_id=?");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
}
header("Location: dashboard.php");
exit();
?>
