<?php
// Include database connection
include_once __DIR__ . '/../config/database.php';

// Check if class_id is set
if (isset($_POST['class_id'])) {
    $class_id = $_POST['class_id'];

    // Prepare query to get distinct teachers for subjects in that class
    $stmt = $pdo->prepare("
        SELECT DISTINCT st.id, st.firstname, st.lastname
        FROM subjects sub
        JOIN staff st ON sub.staff_id = st.id
        WHERE sub.class_id = ?
          AND st.role_id = 2
          AND st.status = 1
        ORDER BY st.firstname ASC
    ");
    $stmt->execute([$class_id]);

    $options = '<option value="">Select Teacher</option>';

    if ($stmt->rowCount() > 0) {
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $fullName = htmlspecialchars($row['firstname'] . ' ' . $row['lastname']);
            $options .= "<option value='{$row['id']}'>{$fullName}</option>";
        }
    } else {
        $options .= '<option value="">No teachers found</option>';
    }

    echo $options;
} else {
    echo '<option value="">No class selected</option>';
}
