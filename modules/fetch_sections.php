<?php
include_once __DIR__ . "/../config/database.php";

if (isset($_POST['class_id'])) {
    $class_id = $_POST['class_id'];

    // Fetch sections based on class_id
    $stmt = $pdo->prepare("SELECT section_id, name FROM sections WHERE class_id = ?");
    $stmt->execute([$class_id]);
    $sections = $stmt->fetchAll();

    echo '<option value="">Select Section</option>';
    foreach ($sections as $section) {
        echo '<option value="' . $section['section_id'] . '">' . $section['name'] . '</option>';
    }
}
?>
