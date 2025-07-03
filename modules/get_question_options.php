<?php
include_once __DIR__ . '/../config/database.php';

$questionId = $_GET['question_id'] ?? 0;
$response = ['options' => [], 'correct_answer' => ''];

if ($questionId) {
    $stmt = $pdo->prepare("SELECT option_letter, option_text, is_correct FROM options WHERE question_id = ?");
    $stmt->execute([$questionId]);
    $options = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($options as $opt) {
        $response['options'][] = $opt['option_text'];
        if ($opt['is_correct']) {
            $response['correct_answer'] = $opt['option_text']; // or $opt['option_letter'] if preferred
        }
    }
}

echo json_encode($response);
