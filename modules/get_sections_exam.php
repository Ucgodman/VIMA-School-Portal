<?php
// Include database connection
include_once __DIR__ . '/../config/database.php';

// Check if class_id is set
if (isset($_POST['class_id'])) {
    $class_id = $_POST['class_id'];

    $sql = "SELECT section_id, name FROM sections WHERE class_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$class_id]);

    echo '<option value="">Select Section</option>';
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo '<option value="' . $row['section_id'] . '">' . $row['name'] . '</option>';
    }
}
?>
