<?php

include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../functions/helper_functions.php";

// Check if notice ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = "Invalid notice ID!";
    header("Location: noticeboard.php");
    exit();
}

$notice_id = $_GET['id'];

// Check if notice exists
$stmt = $pdo->prepare("SELECT noticeboard_id FROM noticeboard WHERE noticeboard_id = :id");
$stmt->execute([':id' => $notice_id]);
$notice = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$notice) {
    $_SESSION['message'] = "Notice not found!";
    header("Location: noticeboard.php");
    exit();
}

// Delete notice
$delete_sql = "DELETE FROM noticeboard WHERE noticeboard_id = :id";
$stmt = $pdo->prepare($delete_sql);
$stmt->execute([':id' => $notice_id]);

$_SESSION['message'] = "Notice deleted successfully!";
header("Location: noticeboard.php");
exit();
?>
