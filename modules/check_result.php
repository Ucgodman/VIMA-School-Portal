<?php
session_start();
include_once "../config/db.php";
include_once "../functions/helper_functions.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $card_code = trim($_POST['card_code']);
    $student_id = $_SESSION['user']['id'];

    $card = fetch_one("SELECT * FROM scratch_cards WHERE card_code = ? AND is_used = 0", [$card_code]);

    if ($card) {
        // Mark as used
        exec_query("UPDATE scratch_cards SET is_used = 1, student_id = ?, used_at = NOW() WHERE card_code = ?", [$student_id, $card_code]);

        // Redirect to result
        header("Location: view_result.php");
        exit;
    } else {
        $error = "Invalid or already used scratch card.";
    }
}
?>

<form method="post">
    <label>Enter Scratch Card Code</label>
    <input type="text" name="card_code" required class="form-control" />
    <button type="submit" class="btn btn-primary mt-2">View Result</button>
    <?php if (!empty($error)) echo "<p class='text-danger'>$error</p>"; ?>
</form>
