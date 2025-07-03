<?php

include_once __DIR__ . "/../config/database.php";

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "Invalid circular ID.";
    header("Location: circulars.php");
    exit();
}

$circular_id = $_GET['id'];

// Delete the club
$stmt = $pdo->prepare("DELETE FROM circulars WHERE circular_id = :circular_id");
$stmt->bindParam(':circular_id', $circular_id, PDO::PARAM_INT);

if ($stmt->execute()) {
    $_SESSION['success'] = "Circular deleted successfully!";
} else {
    $_SESSION['error'] = "Failed to delete circular.";
}

header("Location: circulars.php");
exit();
?>
