<?php

include_once __DIR__ . "/../config/database.php";

if (!isset($_GET['id'])) {
    $_SESSION['error'] = "Invalid request!";
    header("Location: staff.php");
    exit;
}

$staff_id = $_GET['id'];

// Delete staff from the database
$stmt = $pdo->prepare("DELETE FROM staff WHERE staff_id = ?");
if ($stmt->execute([$staff_id])) {
    $_SESSION['success'] = "Staff deleted successfully!";
} else {
    $_SESSION['error'] = "Failed to delete staff!";
}

header("Location: staff.php");
exit;
