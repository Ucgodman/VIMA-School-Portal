<?php

include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../functions/helper_functions.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        // Sanitize inputs
        $name = isset($_POST['name']) ? trim($_POST['name']) : "";
        $description = isset($_POST['description']) ? trim($_POST['description']) : "";

        // Validate required field
        if (empty($name)) {
            $_SESSION['message'] = "Department name is required!";
            header("Location: department.php");
            exit();
        }

        // Insert department into database
        $sql = "INSERT INTO departments (name, description, created_at) VALUES (:name, :description, NOW())";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':name' => $name,
            ':description' => $description
        ]);

        $_SESSION['message'] = "Department added successfully!";
        header("Location: departments.php");
        exit();
    } catch (PDOException $e) {
        $_SESSION['message'] = "Database error: " . $e->getMessage();
        header("Location: departments.php");
        exit();
    }
} else {
    $_SESSION['message'] = "Invalid request!";
    header("Location: departments.php");
    exit();
}
?>
