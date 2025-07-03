<?php
session_start();
$pageTitle = "Edit Subject";
include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../includes/admin_header.php";

// Get subject ID from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('Invalid subject ID.'); window.location.href='manage_subjects.php';</script>";
    exit;
}

$subjectId = $_GET['id'];

// Fetch subject details
$query = "SELECT * FROM subjects WHERE subject_id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$subjectId]);
$subject = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$subject) {
    echo "<script>alert('Subject not found.'); window.location.href='manage_subjects.php';</script>";
    exit;
}

// Fetch classes
$classQuery = "SELECT class_id, class_name FROM classes ORDER BY class_name ASC";
$classes = $pdo->query($classQuery)->fetchAll(PDO::FETCH_ASSOC);

// Fetch teachers
$teacherQuery = "SELECT id, firstname, lastname FROM staff WHERE role_id = 2 ORDER BY firstname ASC";
$teachers = $pdo->query($teacherQuery)->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_subject'])) {
    $subjectName = trim($_POST['subject_name']);
    $classId = $_POST['class_id'];
    $teacherId = $_POST['teacher_id'];

    $updateQuery = "UPDATE subjects SET name = ?, class_id = ?, staff_id = ? WHERE subject_id = ?";
    $stmt = $pdo->prepare($updateQuery);
    $stmt->execute([$subjectName, $classId, $teacherId, $subjectId]);

    echo "<script>alert('Subject Updated Successfully!'); window.location.href='subjects.php';</script>";
}
?>

<div class="wrapper">
    <?php include_once __DIR__ . "/../includes/sidebar.php"; ?>
    <div class="main-panel">
        <div class="container mt-4">
            <div class="row">
                <div class="col-md-6 offset-md-3">
                    <div class="card shadow">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Edit Subject</h5>
                        </div>
                        <div class="card-body">
                            <form action="" method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Subject Name</label>
                                    <input type="text" class="form-control" name="subject_name" value="<?= htmlspecialchars($subject['name']) ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Select Class</label>
                                    <select name="class_id" class="form-control" required>
                                        <option value="">Select Class</option>
                                        <?php foreach ($classes as $class) { ?>
                                            <option value="<?= $class['class_id'] ?>" <?= ($class['class_id'] == $subject['class_id']) ? "selected" : "" ?>>
                                                <?= $class['class_name'] ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Select Teacher</label>
                                    <select name="teacher_id" class="form-control" required>
                                        <option value="">Select Teacher</option>
                                        <?php foreach ($teachers as $teacher) { ?>
                                            <option value="<?= $teacher['id'] ?>" <?= ($teacher['id'] == $subject['staff_id']) ? "selected" : "" ?>>
                                                <?= $teacher['firstname'] . " " . $teacher['lastname'] ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <button type="submit" name="update_subject" class="btn btn-success">Update Subject</button>
                                <a href="manage_subjects.php" class="btn btn-secondary">Cancel</a>
                            </form>
                        </div>
                    </div>
                </div>  
            </div>
        </div>
    </div>
</div>
