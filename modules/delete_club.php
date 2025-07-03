<?php

include_once __DIR__ . "/../config/database.php";

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "Invalid club ID.";
    header("Location: school_clubs.php");
    exit();
}

$club_id = $_GET['id'];

// Delete the club
$stmt = $pdo->prepare("DELETE FROM clubs WHERE club_id = :club_id");
$stmt->bindParam(':club_id', $club_id, PDO::PARAM_INT);

if ($stmt->execute()) {
    $_SESSION['success'] = "Club deleted successfully!";
} else {
    $_SESSION['error'] = "Failed to delete club.";
}

header("Location: school_clubs.php");
exit();
?>
