<?php
session_start();
include_once "../config/db.php"; // adjust this path
include_once "../functions/helper_functions.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['type'] !== 'student') {
    header("Location: ../login.php");
    exit;
}

$student_id = $_SESSION['user']['id'];
$message = "";
$results = [];
$breakdown = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $card_code = trim($_POST['scratch_code'] ?? '');

    // Validate scratch card
    $card = fetch_one("SELECT * FROM scratch_cards WHERE code = ? AND used_by = ? AND is_used = 1 AND expiry_date >= CURDATE()", [$card_code, $student_id]);

    if ($card) {
        // Get results
        $results = fetch_all("SELECT sea.*, e.title, e.total_marks, e.pass_marks 
            FROM student_exam_attempt sea
            JOIN exam e ON sea.exam_id = e.exam_id
            WHERE sea.student_id = ? AND sea.status = 'completed'", [$student_id]);

        if (isset($_POST['exam_id'])) {
            $exam_id = $_POST['exam_id'];
            $attempt = fetch_one("SELECT * FROM student_exam_attempt WHERE student_id = ? AND exam_id = ?", [$student_id, $exam_id]);

            if ($attempt) {
                $breakdown = fetch_all("
                    SELECT q.question, q.marks, q.question_type,
                           o.option_letter AS selected_letter, o.option_text AS selected_text,
                           correct.option_letter AS correct_letter, correct.option_text AS correct_text
                    FROM student_answers sa
                    LEFT JOIN question q ON sa.question_id = q.question_id
                    LEFT JOIN option o ON sa.selected_option_id = o.option_id
                    LEFT JOIN option correct ON correct.question_id = q.question_id AND correct.is_correct = 1
                    WHERE sa.attempt_id = ?", [$attempt['attempt_id']]);
            }
        }
    } else {
        $message = "Invalid or expired scratch card.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Result</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
</head>
<body class="container mt-5">
    <h2 class="mb-4">View Your Exam Result</h2>

    <?php if ($message): ?>
        <div class="alert alert-danger"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST" class="mb-4">
        <div class="mb-3">
            <label for="scratch_code" class="form-label">Enter Scratch Card</label>
            <input type="text" class="form-control" id="scratch_code" name="scratch_code" required>
        </div>
        <button type="submit" class="btn btn-primary">Check Result</button>
    </form>

    <?php if (!empty($results)): ?>
        <form method="POST" class="mb-4">
            <input type="hidden" name="scratch_code" value="<?= htmlspecialchars($card_code) ?>">
            <div class="mb-3">
                <label for="exam_id" class="form-label">Select Exam</label>
                <select name="exam_id" class="form-select" required>
                    <option value="">--Select--</option>
                    <?php foreach ($results as $res): ?>
                        <option value="<?= $res['exam_id'] ?>" <?= (isset($_POST['exam_id']) && $_POST['exam_id'] == $res['exam_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($res['title']) ?> (Score: <?= $res['total_score'] ?>/<?= $res['total_marks'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-success">View Breakdown</button>
        </form>
    <?php endif; ?>

    <?php if (!empty($breakdown)): ?>
        <h4>Exam Breakdown</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Question</th>
                    <th>Type</th>
                    <th>Marks</th>
                    <th>Your Answer</th>
                    <th>Correct Answer</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($breakdown as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['question']) ?></td>
                        <td><?= htmlspecialchars($row['question_type']) ?></td>
                        <td><?= $row['marks'] ?></td>
                        <td><?= $row['selected_letter'] ?>: <?= htmlspecialchars($row['selected_text']) ?></td>
                        <td><?= $row['correct_letter'] ?>: <?= htmlspecialchars($row['correct_text']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    <form method="GET" action="download_result_pdf.php" target="_blank">
    <input type="hidden" name="exam_id" value="<?= $exam_id ?>">
    <input type="hidden" name="card_code" value="<?= htmlspecialchars($card_code) ?>">
    <button type="submit" class="btn btn-outline-danger mt-3">Download as PDF</button>
    </form>


</body>
</html>
