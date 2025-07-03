<?php
session_start();

// Only admins allowed
if (!isset($_SESSION['user']) || $_SESSION['user']['type'] !== 'staff' || $_SESSION['user']['role_id'] != 1) {
    header("Location: ../login.php");
    exit;
}

include_once __DIR__ . '/../config/db.php';
include_once __DIR__ . '/../functions/helper_functions.php';

// Handle card generation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $num_cards = (int) $_POST['num_cards'];
    $expiry = $_POST['expiry_date'];
    $max_uses = (int) $_POST['max_uses'];
    $exam_ids = $_POST['exam_ids'] ?? [];

    if ($num_cards > 0 && $max_uses > 0 && !empty($exam_ids)) {
        $pdo->beginTransaction();
        try {
            $exam_ids_json = json_encode($exam_ids);

            for ($i = 0; $i < $num_cards; $i++) {
                $code = strtoupper(bin2hex(random_bytes(4)));
                $stmt = $pdo->prepare("INSERT INTO scratch_cards (code, expiry_date, max_uses, used_count, assigned_exam_ids) VALUES (?, ?, ?, 0, ?)");
                $stmt->execute([$code, $expiry, $max_uses, $exam_ids_json]);
            }
            $pdo->commit();
            $success = "Successfully generated $num_cards scratch cards.";
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Error: " . $e->getMessage();
        }
    } else {
        $error = "Please fill all required fields.";
    }
}

// Fetch exams
$exams = fetch_all("SELECT exam_id, title FROM exam ORDER BY created_at DESC");
?>

<?php include_once __DIR__ . '/../includes/admin_header.php'; ?>
<div class="container mt-5">
    <h2>Generate Scratch Cards</h2>
    <?php if (!empty($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
    <?php if (!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="num_cards" class="form-label">Number of Cards</label>
            <input type="number" name="num_cards" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="expiry_date" class="form-label">Expiry Date</label>
            <input type="date" name="expiry_date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="max_uses" class="form-label">Max Uses Per Card</label>
            <input type="number" name="max_uses" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="exam_ids" class="form-label">Assign to Exams</label>
            <select name="exam_ids[]" class="form-control" multiple required>
                <?php foreach ($exams as $exam): ?>
                    <option value="<?= $exam['exam_id'] ?>"><?= htmlspecialchars($exam['title']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Generate</button>
    </form>
</div>

<?php include_once __DIR__ . '/../includes/admin_footer.php'; ?>
