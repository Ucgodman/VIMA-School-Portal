<?php
include_once __DIR__ . "/../config/database.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Delete the question from the database
    $query = "DELETE FROM questions WHERE id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$id]);

    header("Location: manage_questions.php");
    exit();
}
?>
