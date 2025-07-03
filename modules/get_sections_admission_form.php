<?php
include_once __DIR__ . "/../config/database.php";

if (isset($_POST['id'])) {
    $class_id = $_POST['id'];
    
    $stmt = $pdo->prepare("SELECT * FROM sections WHERE class_id = ?");
    $stmt->execute([$class_id]);
    $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($sections);
}
?>
