<?php
include_once __DIR__ . "/../config/database.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sessionYear = trim($_POST['session_year']);

    if (!empty($sessionYear)) {
        $stmt = $pdo->prepare("INSERT INTO sessions (session_year) VALUES (?)");
        $stmt->execute([$sessionYear]);
        echo "Session added successfully!";
    } else {
        echo "Session year cannot be empty.";
    }
}
?>
