<?php

include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../functions/helper_functions.php";

// Check if department ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = "Invalid department ID!";
    header("Location: department.php");
    exit();
}

$department_id = $_GET['id'];

// Check if department exists
$stmt = $pdo->prepare("SELECT department_id FROM departments WHERE department_id = :id");
$stmt->execute([':id' => $department_id]);
$department = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$department) {
    $_SESSION['message'] = "Department not found!";
    header("Location: department.php");
    exit();
}

// Delete department
$delete_sql = "DELETE FROM departments WHERE department_id = :id";
$stmt = $pdo->prepare($delete_sql);
$stmt->execute([':id' => $department_id]);

$_SESSION['message'] = "Department deleted successfully!";
header("Location: department.php");
exit();
?>
