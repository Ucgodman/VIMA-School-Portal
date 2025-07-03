<?php
include_once __DIR__ . "/../config/database.php";

if (!isset($_GET['id'])) {
    die("Student ID is required.");
}

$student_id = $_GET['id'];

// Update login status to inactive
$stmt = $pdo->prepare("UPDATE students SET login_status = 'inactive' WHERE student_id = ?");
$stmt->execute([$student_id]);

echo "<script>alert('Student marked as inactive!'); window.location.href='student_information.php';</script>";
?>
