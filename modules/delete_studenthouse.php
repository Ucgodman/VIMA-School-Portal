<?php

include_once __DIR__ . "/../config/database.php";

if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error'] = "Invalid request!";
    header("Location: studenthouse.php");
    exit();
}

$house_id = $_GET['id'];

// Check if the house exists
$stmt = $pdo->prepare("SELECT * FROM student_house WHERE house_id = :house_id");
$stmt->bindParam(':house_id', $house_id, PDO::PARAM_INT);
$stmt->execute();
$house = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$house) {
    $_SESSION['error'] = "Student House not found!";
    header("Location: studenthouse.php");
    exit();
}

// Delete the house
$stmt = $pdo->prepare("DELETE FROM student_house WHERE house_id = :house_id");
$stmt->bindParam(':house_id', $house_id, PDO::PARAM_INT);

if ($stmt->execute()) {
    $_SESSION['success'] = "Student House deleted successfully!";
} else {
    $_SESSION['error'] = "Failed to delete student house.";
}

header("Location: studenthouse.php");
exit();
