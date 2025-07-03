<?php

include_once __DIR__ . "/../config/database.php";

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "Invalid activity ID.";
    header("Location: student_activity.php");
    exit();
}

$activity_id = $_GET['id'];

// Delete activity
$stmt = $pdo->prepare("DELETE FROM activities WHERE activity_id = :id");
$stmt->bindParam(':id', $activity_id, PDO::PARAM_INT);

if ($stmt->execute()) {
    $_SESSION['success'] = "Activity deleted successfully!";
} else {
    $_SESSION['error'] = "Failed to delete activity.";
}

header("Location: student_activity.php");
exit();
?>
