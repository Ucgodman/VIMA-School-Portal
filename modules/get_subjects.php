<?php
include_once __DIR__ . "/../config/database.php";

if (isset($_GET['id'])) {
    $class_id = intval($_GET['id']);
    $subjects = [];

    $sql = "SELECT subject_id AS id, name FROM subjects WHERE class_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":id", $class_id, PDO::PARAM_INT);
    $stmt->execute();
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($subjects);
}
?>
