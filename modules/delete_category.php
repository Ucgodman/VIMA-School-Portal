<?php

include_once __DIR__ . "/../config/database.php";

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "Invalid category ID!";
    header("Location: enquiry_category.php");
    exit;
}

$id = $_GET['id'];

$stmt = $pdo->prepare("DELETE FROM enquiry_category WHERE enquiry_id = ?");
if ($stmt->execute([$id])) {
    $_SESSION['success'] = "Category deleted successfully!";
} else {
    $_SESSION['error'] = "Failed to delete category!";
}

header("Location: enquiry_category.php");
exit;
?>
