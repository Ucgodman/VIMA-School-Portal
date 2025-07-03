<?php
include_once __DIR__ . "/../config/database.php";

if (isset($_POST['class_id'])) {
    $class_id = $_POST['class_id'];

    // Fetch subjects based on class_id
    $stmt = $pdo->prepare("SELECT subject_id, name FROM subjects WHERE class_id = ?");
    $stmt->execute([$class_id]);
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo '<option value="">Select Subject</option>';
    foreach ($subjects as $subject) {
        echo '<option value="' . $subject['subject_id'] . '">' . $subject['name'] . '</option>';
    }
}
?>

