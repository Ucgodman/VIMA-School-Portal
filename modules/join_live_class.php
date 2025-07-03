<?php
// joining live class as student
session_start();
include_once "../config/database.php";
$student_id = $_SESSION['student_id'] ?? 0;
$class_id = $_GET['class_id'] ?? 0;

// Log attendance
$stmt = $pdo->prepare("INSERT INTO class_attendance (student_id, class_id) VALUES (?, ?)");
$stmt->execute([$student_id, $class_id]);

// Redirect to Jitsi or live video
header("Location: https://meet.jit.si/yourroom-$class_id");
exit;
?>
