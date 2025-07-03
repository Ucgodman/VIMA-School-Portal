<?php
include_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpWord\IOFactory;
use Smalot\PdfParser\Parser;

date_default_timezone_set("Africa/Lagos");
$now = date('Y-m-d H:i:s');

// === SINGLE QUESTION ENTRY ===
if (isset($_POST['save_single'])) {
    $exam_id = $_POST['exam_id'];
    $question = $_POST['question'];
    $question_type = $_POST['question_type'];
    $marks = $_POST['marks'];

    // REMOVE HTML TAG AND HTML ENTITY
    $clean_question = trim(strip_tags(html_entity_decode($question)));

    // Insert question
    $stmt = $pdo->prepare("INSERT INTO questions (exam_id, question, question_type, marks, is_deleted, created_at, updated_at) VALUES (?, ?, ?, ?, 0, ?, ?)");
    $stmt->execute([$exam_id, $clean_question, $question_type, $marks, $now, $now]);

    $question_id = $pdo->lastInsertId();

    // Insert options (only for objective and true_false types)
    if (in_array($question_type, ['objective', 'true_false']) && isset($_POST['option_letters'], $_POST['option_texts'])) {
        $option_letters = $_POST['option_letters'];
        $option_texts = $_POST['option_texts'];
        $correct = $_POST['is_correct'] ?? '';

        foreach ($option_letters as $idx => $letter) {
            $is_correct = (strtoupper(trim($correct)) == strtoupper(trim($letter))) ? 1 : 0;
            $opt_letter = strtoupper(trim($letter));
            $opt_text = trim($option_texts[$idx]);

            $stmt = $pdo->prepare("INSERT INTO options (question_id, option_letter, option_text, is_correct, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)");
            error_log("Option: $opt_letter | Correct: $correct | is_correct: $is_correct");

            $stmt->execute([$question_id, $opt_letter, $opt_text, $is_correct, $now, $now]);
        }
    }

    header("Location: add_question.php?success=single");
    exit;
}


// === BATCH UPLOAD (WORD/PDF) ===
if (isset($_POST['upload_batch']) && isset($_FILES['question_file'])) {
    $exam_id = $_POST['exam_id'];

    $file = $_FILES['question_file'];
    $fileType = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $tmpPath = $file['tmp_name'];

    $text = '';

    if ($fileType === 'pdf') {
        $parser = new Parser();
        $pdf = $parser->parseFile($tmpPath);
        $text = $pdf->getText();
    } elseif (in_array($fileType, ['doc', 'docx'])) {
        $phpWord = IOFactory::load($tmpPath);
        foreach ($phpWord->getSections() as $section) {
            $elements = $section->getElements();
            foreach ($elements as $element) {
                if (method_exists($element, 'getText')) {
                    $text .= $element->getText() . "\n";
                }
            }
        }
    } else {
        die("Unsupported file type.");
    }

    $lines = explode("\n", $text);
    $questionBlock = [];

    foreach ($lines as $line) {
        $trimmed = trim($line);
        if ($trimmed === '') continue;

        // Detect new question block
        if (preg_match('/^\d+\.\s/', $trimmed) && !empty($questionBlock)) {
            processQuestionBlock($questionBlock, $pdo, $exam_id, $now);
            $questionBlock = [];
        }

        $questionBlock[] = $trimmed;
    }

    // Process the last block
    if (!empty($questionBlock)) {
        processQuestionBlock($questionBlock, $pdo, $exam_id, $now);
    }

    header("Location: add_question.php?success=batch");
    exit;
}

// === Function to Process Each Question Block ===
function processQuestionBlock($block, $pdo, $exam_id, $now) {
    $question = '';
    $type = 'objective';
    $marks = 1;
    $options = [];
    $correct = '';

    foreach ($block as $line) {
        if (preg_match('/^\d+\.\s*(.*)/', $line, $m)) {
            $question = trim($m[1]);
        } elseif (stripos($line, 'Type:') === 0) {
            $type = strtolower(trim(str_ireplace('Type:', '', $line)));
        } elseif (stripos($line, 'Marks:') === 0) {
            $marks = intval(trim(str_ireplace('Marks:', '', $line)));
        } elseif (preg_match('/^([A-Z])\.\s*(.*)/', $line, $m)) {
            $options[$m[1]] = $m[2];
        } elseif (stripos($line, 'Answer:') === 0) {
            $correct = strtoupper(trim(str_ireplace('Answer:', '', $line)));
        }
    }

    if (empty($question) || empty($options) || empty($correct)) return;

    // Insert question
    $stmt = $pdo->prepare("INSERT INTO questions (exam_id, question, question_type, marks, is_deleted, created_at, updated_at)
                           VALUES (?, ?, ?, ?, 0, ?, ?)");
    $stmt->execute([$exam_id, $question, $type, $marks, $now, $now]);

    $question_id = $pdo->lastInsertId();

    foreach ($options as $letter => $text) {
        $stmt = $pdo->prepare("INSERT INTO options (question_id, option_letter, option_text, is_correct, created_at, updated_at)
                               VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $question_id,
            strtoupper($letter),
            $text,
            (strtoupper($letter) == $correct ? 1 : 0),
            $now,
            $now
        ]);
    }
}


// === EDIT QUESTION ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'update_question') {
        $question_id = $_POST['question_id'];
        $question_type = $_POST['question_type'];
        $question = $_POST['question'];
        $marks = $_POST['marks'];
        $exam_id = $_POST['exam_id'] ?? null;

        $clean_question = trim(strip_tags(html_entity_decode($question)));

        if (!$exam_id) {
            echo json_encode(['status' => 'error', 'message' => 'Exam ID is missing.']);
            exit;
        }

        try {
            $stmt = $pdo->prepare("UPDATE questions SET exam_id = ?, question_type = ?, question = ?, marks = ?, updated_at = NOW() WHERE question_id = ?");
            $stmt->execute([$exam_id, $question_type, $clean_question, $marks, $question_id]);

            // Optional: delete and re-insert options (or handle update logic separately)

            header("Location: manage_questions.php?updated=1");
            exit;
        } catch (PDOException $e) {
            echo "Failed to update question: " . $e->getMessage();
        }
    } else {
        echo "No action specified";
    }
} else {
    echo "Invalid request method";
}

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_question'], $_POST['question_id'])) {
        $questionId = (int)$_POST['question_id'];

        $stmt = $pdo->prepare("UPDATE questions SET is_deleted = 1 WHERE question_id = ?");
        if ($stmt->execute([$questionId])) {
            echo 'success';
        } else {
            echo 'fail';
        }
        exit;
    }
}

// If nothing matched
echo 'no_action';
exit;
