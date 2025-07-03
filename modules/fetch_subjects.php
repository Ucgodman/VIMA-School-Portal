<?php
include_once __DIR__ . "/../config/database.php";

if (isset($_GET['class_id'])) {
    $classId = $_GET['class_id'];

    $query = "SELECT s.subject_id, s.name, c.class_name AS class_name, 
                     t.firstname, t.lastname 
              FROM subjects s
              JOIN classes c ON s.class_id = c.class_id
              JOIN staff t ON s.staff_id = t.id AND t.role_id = 2
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
