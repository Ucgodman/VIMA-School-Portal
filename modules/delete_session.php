<?php
include_once __DIR__ . "/../config/database.php";

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('Invalid session ID.'); window.location.href='sessions.php';</script>";
    exit;
}

$sessionId = $_GET['id'];
$stmt = $pdo->prepare("DELETE FROM sessions WHERE session_id = ?");
$stmt->execute([$sessionId]);

echo "<script>alert('Session Deleted Successfully!'); window.location.href='sessions.php';</script>";
?>
