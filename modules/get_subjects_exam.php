<?php
// Include database connection
include_once __DIR__ . '/../config/database.php';

// Check if class_id is set
if (isset($_POST['class_id'])) {
    $class_id = $_POST['class_id'];

    // Prepare and execute the query
    $stmt = $pdo->prepare("SELECT subject_id, name FROM subjects WHERE class_id = ?");
    $stmt->execute([$class_id]);

    // Initialize options string
    $options = '<option value="">Select Subject</option>';

    // Fetch and append each subject as an option
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $options .= "<option value='{$row['subject_id']}'>{$row['name']}</option>";
    }

    // Output the options
    echo $options;
}
?>
