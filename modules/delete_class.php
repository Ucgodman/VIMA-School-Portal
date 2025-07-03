<?php
include_once __DIR__ . "/../config/database.php";

if (isset($_GET['id'])) {
    $classId = $_GET['id'];

    $stmt = $pdo->prepare("DELETE FROM classes WHERE class_id = ?");
    $stmt->execute([$classId]);

    header("Location: classes.php");
    exit();
}
?>
