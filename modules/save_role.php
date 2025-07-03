<?php
include_once __DIR__ . "/../config/database.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);

    if (!empty($name)) {
        $stmt = $pdo->prepare("INSERT INTO roles (name, description) VALUES (?, ?)");
        $stmt->execute([$name, $description]);

        $_SESSION['message'] = "Role added successfully!";
    }
}

header("Location: roles.php");
exit();
?>
