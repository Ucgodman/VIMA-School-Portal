<?php

$pageTitle = "Edit Assignment";
include_once __DIR__ . '/../config/database.php';
include_once __DIR__ . '/../includes/admin_header.php';

// Check if assignment ID is set
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: manage_assignments.php");
    exit;
}

$assignment_id = $_GET['id'];

// Fetch assignment details
$stmt = $pdo->prepare("SELECT * FROM assignments WHERE assignment_id = ?");
$stmt->execute([$assignment_id]);
$assignment = $stmt->fetch();

if (!$assignment) {
    echo "<script>alert('Assignment not found!'); window.location='manage_assignments.php';</script>";
    exit;
}

// Fetch classes
$classes = $pdo->query("SELECT * FROM classes ORDER BY class_name ASC")->fetchAll();

// Fetch teachers
$teachers = $pdo->query("SELECT staff_id, firstname, middlename, lastname FROM staff WHERE role_id = 2 ORDER BY lastname ASC")->fetchAll();

// Fetch subjects
$subjects = $pdo->query("SELECT * FROM subjects ORDER BY name ASC")->fetchAll();

// Fetch sections
$stmt = $pdo->prepare("SELECT * FROM sections WHERE class_id = ? ORDER BY name ASC");
$stmt->execute([$assignment['class_id']]);
$sections = $stmt->fetchAll();
?>

<div class="wrapper">
    <?php include_once __DIR__ . "/../includes/admin_sidebar.php"; ?>
    <div class="main-panel">
        <div class="main-header">
            <?php include_once __DIR__ . "/../includes/navbar.php"; ?>
        </div>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="card shadow">
                        <div class="card-header bg-secondary text-white">Edit Assignment</div>
                        <div class="card-body">
                            <form action="update_assignment.php" method="POST" enctype="multipart/form-data">
                                <input type="hidden" name="id" value="<?php echo $assignment['assignment_id']; ?>">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title</label>
                                    <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($assignment['title']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="class_id" class="form-label">Select Class</label>
                                    <select class="form-control" id="class_id" name="class_id" onchange="fetchSections(this.value)" required>
                                        <?php foreach ($classes as $class): ?>
                                            <option value="<?php echo $class['class_id']; ?>" <?php echo ($assignment['class_id'] == $class['class_id']) ? 'selected' : ''; ?>><?php echo $class['class_name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="section_id" class="form-label">Select Section</label>
                                    <select class="form-control" id="section_id" name="section_id" required>
                                        <?php foreach ($sections as $section): ?>
                                            <option value="<?php echo $section['section_id']; ?>" <?php echo ($assignment['section_id'] == $section['section_id']) ? 'selected' : ''; ?>><?php echo $section['name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="subject_id" class="form-label">Select Subject</label>
                                    <select class="form-control" id="subject_id" name="subject_id" required>
                                        <?php foreach ($subjects as $subject): ?>
                                            <option value="<?php echo $subject['subject_id']; ?>" <?php echo ($assignment['subject_id'] == $subject['subject_id']) ? 'selected' : ''; ?>><?php echo $subject['name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="staff_id" class="form-label">Select Teacher</label>
                                    <select class="form-control" id="staff_id" name="staff_id" required>
                                        <?php foreach ($teachers as $teacher): ?>
                                            <option value="<?php echo $teacher['staff_id']; ?>" <?php echo ($assignment['staff_id'] == $teacher['staff_id']) ? 'selected' : ''; ?>>
                                                <?php echo $teacher['lastname'] . ' ' . $teacher['firstname']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="due_date" class="form-label">Due Date</label>
                                    <input type="date" class="form-control" id="due_date" name="due_date" value="<?php echo $assignment['due_date']; ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($assignment['description']); ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Upload New Document (Optional)</label>
                                    <input type="file" class="form-control" id="file" name="file">
                                    <p>Current File: <a href="../uploads/assignments/<?php echo $assignment['file_path']; ?>" target="_blank" download>Download File</a></p>
                                </div>
                                <button type="submit" class="btn btn-primary">Update Assignment</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
function fetchSections(classId) {
    if (classId) {
        $.ajax({
            url: 'fetch_sections.php',
            type: 'POST',
            data: { class_id: classId },
            success: function(response) {
                $('#section_id').html(response);
                $('#section_id').change(function() {
                    fetchSubjects(classId);
                });
            }
        });
    } else {
        $('#section_id').html('<option value="">Select Class First</option>');
    }
}

function fetchSubjects(classId) {
    if (classId) {
        $.ajax({
            url: 'fetch_subjects.php',
            type: 'POST',
            data: { class_id: classId },
            success: function(response) {
                $('#subject_id').html(response);
            }
        });
    } else {
        $('#subject_id').html('<option value="">Select Class First</option>');
    }
}
</script>

</body>
</html>