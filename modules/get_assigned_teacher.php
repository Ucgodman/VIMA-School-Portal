<?php
include_once __DIR__ . "/../config/database.php";

if (isset($_GET['class_id'])) {
    $class_id = $_GET['class_id'];

    $stmt = $pdo->prepare("SELECT c.staff_id, s.firstname, s.lastname 
                           FROM classes c 
                           LEFT JOIN staff s ON c.staff_id = s.id 
                           WHERE c.class_id = ?");
    $stmt->execute([$class_id]);
    $teacher = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($teacher) {
        echo json_encode([
            "success" => true,
            "staff_id" => $teacher['staff_id'],
            "teacher_name" => $teacher['firstname'] . " " . $teacher['lastname']
        ]);
    } else {
        echo json_encode(["success" => false]);
    }
} else {
    echo json_encode(["success" => false]);
}
?>
