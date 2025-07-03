<?php
session_start();
require '../vendor/autoload.php'; // Dompdf
use Dompdf\Dompdf;
use Dompdf\Options;

include_once "../config/db.php";
include_once "../functions/helper_functions.php";

if (!isset($_SESSION['user']) || $_SESSION['user']['type'] !== 'student') {
    die("Unauthorized");
}

$student_id = $_SESSION['user']['id'];
$exam_id = $_GET['exam_id'] ?? null;
$card_code = $_GET['card_code'] ?? null;

if (!$exam_id || !$card_code) {
    die("Missing parameters.");
}

// Validate scratch card
$card = fetch_one("SELECT * FROM scratch_cards WHERE code = ? AND used_by = ? AND is_used = 1 AND expiry_date >= CURDATE()", [$card_code, $student_id]);

if (!$card) {
    die("Invalid or expired scratch card.");
}

// Fetch exam, attempt, student, and related info
$attempt = fetch_one("SELECT * FROM student_exam_attempt WHERE student_id = ? AND exam_id = ?", [$student_id, $exam_id]);
$exam = fetch_one("SELECT * FROM exam WHERE exam_id = ?", [$exam_id]);
$student = fetch_one("SELECT firstname, lastname, class_id, section_id, session_id FROM student WHERE student_id = ?", [$student_id]);

// Fetch class, section, session, subject names
$class = fetch_one("SELECT class_name FROM classes WHERE class_id = ?", [$student['class_id']])['class_name'] ?? '';
$section = fetch_one("SELECT name FROM sections WHERE section_id = ?", [$student['section_id']])['name'] ?? '';
$session = fetch_one("SELECT session_year FROM sessions WHERE session_id = ?", [$student['session_id']])['session_year'] ?? '';
$subject = fetch_one("SELECT name FROM subjects WHERE subject_id = ?", [$exam['subject_id']])['name'] ?? '';

// Calculate pass/fail
$passFail = ($attempt['total_score'] >= $exam['pass_marks']) ? "<span style='color:green; font-weight:bold;'>Passed</span>" : "<span style='color:red; font-weight:bold;'>Failed</span>";

// Fetch breakdown of questions, selected and correct answers
$breakdown = fetch_all("
    SELECT q.question, q.marks, q.question_type,
           o.option_letter AS selected_letter, o.option_text AS selected_text,
           correct.option_letter AS correct_letter, correct.option_text AS correct_text
    FROM student_answers sa
    LEFT JOIN question q ON sa.question_id = q.question_id
    LEFT JOIN option o ON sa.selected_option_id = o.option_id
    LEFT JOIN option correct ON correct.question_id = q.question_id AND correct.is_correct = 1
    WHERE sa.attempt_id = ?", [$attempt['attempt_id']]);

$today = date("d M Y");

$logoPath = "../assets/images/logo.png"; // update path if needed
$logoImg = file_exists($logoPath) ? '<img src="' . $logoPath . '" height="60"/>' : '';

$html = '
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
    .header { text-align: center; margin-bottom: 20px; }
    table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    th, td { border: 1px solid #333; padding: 8px; text-align: left; vertical-align: top; }
    th { background-color: #f2f2f2; }
    .info { margin-bottom: 10px; }
    .footer { text-align: center; font-size: 10px; margin-top: 20px; color: #777; }
    .passfail { font-size: 16px; margin-top: 10px; }
</style>

<div class="header">
    ' . $logoImg . '
    <h2>Exam Result Breakdown</h2>
</div>

<div class="info">
    <strong>Student:</strong> ' . htmlspecialchars($student['firstname'] . ' ' . $student['lastname']) . '<br>
    <strong>Class:</strong> ' . htmlspecialchars($class) . '<br>
    <strong>Section:</strong> ' . htmlspecialchars($section) . '<br>
    <strong>Session:</strong> ' . htmlspecialchars($session) . '<br>
    <strong>Subject:</strong> ' . htmlspecialchars($subject) . '<br>
    <strong>Exam:</strong> ' . htmlspecialchars($exam['title']) . '<br>
    <strong>Total Score:</strong> ' . htmlspecialchars($attempt['total_score']) . ' / ' . htmlspecialchars($exam['total_marks']) . '<br>
    <strong>Pass Marks:</strong> ' . htmlspecialchars($exam['pass_marks']) . '<br>
    <div class="passfail">Result Status: ' . $passFail . '</div>
    <strong>Date:</strong> ' . $today . '
</div>

<table>
    <thead>
        <tr>
            <th>Question</th>
            <th>Type</th>
            <th>Marks</th>
            <th>Your Answer</th>
            <th>Correct Answer</th>
        </tr>
    </thead>
    <tbody>';

foreach ($breakdown as $row) {
    $html .= '
        <tr>
            <td>' . htmlspecialchars($row['question']) . '</td>
            <td>' . htmlspecialchars($row['question_type']) . '</td>
            <td>' . htmlspecialchars($row['marks']) . '</td>
            <td>' . htmlspecialchars($row['selected_letter']) . ': ' . htmlspecialchars($row['selected_text']) . '</td>
            <td>' . htmlspecialchars($row['correct_letter']) . ': ' . htmlspecialchars($row['correct_text']) . '</td>
        </tr>';
}

$html .= '
    </tbody>
</table>

<div class="footer">
    Generated on ' . $today . ' — VIMA CBT Portal
</div>';

// Generate PDF
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('defaultFont', 'DejaVu Sans');
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("exam_result_{$exam_id}.pdf", ["Attachment" => 1]);
exit;
