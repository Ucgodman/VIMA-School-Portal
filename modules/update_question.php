<?php
include_once __DIR__ . "/../config/database.php";

$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? null;
    if (!$id) {
        $response['message'] = "Missing question id.";
        echo json_encode($response);
        exit;
    }

    $question = $_POST['question'] ?? '';
    $question_type = $_POST['question_type'] ?? '';
    $marks = $_POST['marks'] ?? 0;
    $correct_answer = $_POST['correct_answer'] ?? '';
    $options = $_POST['options'] ?? null;  // Single input for options

    // Handle file upload if provided
    $question_image = null;
    if (isset($_FILES['question_image']) && $_FILES['question_image']['error'] == 0) {
        $uploadDir = __DIR__ . "/../uploads/questions/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $filename = time() . "_" . basename($_FILES['question_image']['name']);
        $targetFile = $uploadDir . $filename;

        if (move_uploaded_file($_FILES['question_image']['tmp_name'], $targetFile)) {
            $question_image = "uploads/questions/" . $filename;
        }
    }

    try {
        $sql = "UPDATE questions 
                SET question = :question, 
                    question_type = :question_type, 
                    marks = :marks, 
                    correct_answer = :correct_answer, 
                    options = :options";
        
        if ($question_image !== null) {
            $sql .= ", image_path = :image_path";
        }
        
        $sql .= " WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':question', $question);
        $stmt->bindParam(':question_type', $question_type);
        $stmt->bindParam(':marks', $marks, PDO::PARAM_INT);
        $stmt->bindParam(':correct_answer', $correct_answer);
        $stmt->bindParam(':options', $options, PDO::PARAM_STR);
        
        if ($question_image !== null) {
            $stmt->bindParam(':image_path', $question_image);
        }

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $response['success'] = true;
            $response['message'] = "Question updated successfully.";
        } else {
            $response['message'] = "Failed to update question.";
        }
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>