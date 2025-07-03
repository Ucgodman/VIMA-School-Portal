<?php

include_once __DIR__ . "/../config/database.php";

// Get subject ID from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('Invalid subject ID.'); window.location.href='subjects.php';</script>";
    exit;
}

$subjectId = $_GET['id'];

// Delete the subject
$deleteQuery = "DELETE FROM subjects WHERE subject_id = ?";
$stmt = $pdo->prepare($deleteQuery);
$stmt->execute([$subjectId]);

echo "<script>alert('Subject Deleted Successfully!'); window.location.href='subjects.php';</script>";
?>
