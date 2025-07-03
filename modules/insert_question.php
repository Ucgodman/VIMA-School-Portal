<?php
// Include database connection
include '../config/database.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['batch_upload'])) {
        // Handle batch upload
        if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['file']['tmp_name'];
            $fileName = $_FILES['file']['name'];
            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

            if (in_array($fileExtension, ['pdf', 'doc', 'docx'])) {
                // Process the uploaded file (e.g., save it to a directory or parse its content)
                $uploadDir = '../uploads/';
                $destPath = $uploadDir . $fileName;

                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    echo json_encode(['status' => 'success', 'message' => 'File uploaded successfully.']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to move uploaded file.']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid file type. Only PDF, DOC, and DOCX are allowed.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No file uploaded or an error occurred.']);
        }
    } elseif (isset($_POST['single_question'])) {
        // Handle single question entry
        $classId = $_POST['class_id'];
        $sectionId = $_POST['section_id'];
        $subjectId = $_POST['subject_id'];
        $session = $_POST['session'];
        $questionType = $_POST['question_type'];
        $question = $_POST['question'];
        $options = $_POST['options'] ?? null;
        $correctAnswer = $_POST['correct_answer'];
        $marks = $_POST['marks'];

        // Handle image upload if provided
        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imageTmpPath = $_FILES['image']['tmp_name'];
            $imageName = $_FILES['image']['name'];
            $uploadDir = '../uploads/images/';
            $imagePath = $uploadDir . $imageName;

            if (!move_uploaded_file($imageTmpPath, $imagePath)) {
                echo json_encode(['status' => 'error', 'message' => 'Failed to upload image.']);
                exit;
            }
        }

        // Insert question into the database
        $query = "INSERT INTO questions (class_id, section_id, subject_id, session, question_type, question, options, correct_answer, marks, image_path) 
                  VALUES (:class_id, :section_id, :subject_id, :session, :question_type, :question, :options, :correct_answer, :marks, :image_path)";
        $stmt = $pdo->prepare($query);

        $stmt->bindParam(':class_id', $classId);
        $stmt->bindParam(':section_id', $sectionId);
        $stmt->bindParam(':subject_id', $subjectId);
        $stmt->bindParam(':session', $session);
        $stmt->bindParam(':question_type', $questionType);
        $stmt->bindParam(':question', $question);
        $stmt->bindParam(':options', $options);
        $stmt->bindParam(':correct_answer', $correctAnswer);
        $stmt->bindParam(':marks', $marks);
        $stmt->bindParam(':image_path', $imagePath);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Question added successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to add question.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}