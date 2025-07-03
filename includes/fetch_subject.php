<?php
include_once __DIR__ . "/../config/database.php";

if (isset($_GET['class_id'])) {
    $classId = $_GET['class_id'];

    $query = "SELECT s.subject_id, s.name, c.numeric_name AS class_name, 
                     t.firstname, t.lastname 
              FROM subjects s
              JOIN classes c ON s.class_id = c.class_id
              JOIN staff t ON s.staff_id = t.staff_id
              WHERE s.class_id = ?";
              
    $stmt = $pdo->prepare($query);
    $stmt->execute([$classId]);
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $formattedSubjects = array_map(function ($subject) {
        return [
            'subject_id' => $subject['subject_id'],
            'name' => $subject['name'],
            'class_name' => $subject['class_name'],
            'teacher' => $subject['firstname'] . " " . $subject['lastname']
        ];
    }, $subjects);

    echo json_encode($formattedSubjects);
}
?>
