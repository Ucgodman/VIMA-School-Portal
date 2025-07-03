<?php
session_start();
$pageTitle = "Teacher Schedule Class";
include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../functions/helper_functions.php";
include_once __DIR__ . "/../includes/admin_header.php";

// Only allow admin and teachers
if (
    !isset($_SESSION['user']) ||
    (
        $_SESSION['user']['type'] !== 'admin' &&
        !(
            $_SESSION['user']['type'] === 'staff' &&
            isset($_SESSION['user']['role_id']) &&
            $_SESSION['user']['role_id'] == 3
        )
    )
) {
    die("Access denied");
}

// Fetch all classes for dropdown
$classes = $pdo->query("SELECT * FROM classes ORDER BY class_name")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_id = $_POST['class_id'];
    $section_id = $_POST['section_id'];
    $subject_id = $_POST['subject_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $start_time = $_POST['start_time'];
    $duration = $_POST['duration_minutes'];
    $room = "class_" . uniqid();

    // Get staff_id from session (ensure it's set)
    $staff_id = $_SESSION['user']['staff_id'] ?? null;

    if (!$staff_id) {
        die("Staff ID missing in session.");
    }

    $sql = "INSERT INTO live_classes (class_id, section_id, subject_id, staff_id, title, description, start_time, duration_minutes, jitsi_room)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$class_id, $section_id, $subject_id, $staff_id, $title, $description, $start_time, $duration, $room]);

    $_SESSION['success_message'] = "Live class scheduled.";
    header("Location: teacher_schedule_class.php");
    exit;
}

    // Pagination setup
    $perPage = 10;
    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    $offset = ($page - 1) * $perPage;

    $staff_id = $_SESSION['user']['staff_id'] ?? ($_SESSION['user']['id'] ?? null);
    $schedules = [];
    $totalSchedules = 0;

    if ($staff_id) {
        // Count total
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM live_classes WHERE staff_id = ?");
        $stmt->execute([$staff_id]);
        $totalSchedules = $stmt->fetchColumn();

        // Fetch paginated
        $stmt = $pdo->prepare("SELECT lc.*, c.class_name, s.name AS section_name, sb.name AS subject_name
            FROM live_classes lc
            JOIN classes c ON lc.class_id = c.class_id
            JOIN sections s ON lc.section_id = s.section_id
            JOIN subjects sb ON lc.subject_id = sb.subject_id
            WHERE lc.staff_id = ?
            ORDER BY lc.start_time DESC
            LIMIT $offset, $perPage");
        $stmt->execute([$staff_id]);
        $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
?>

<div class="wrapper">
    <?php include_once __DIR__ . "/../includes/sidebar.php"; ?>
    <div class="main-panel">
        <div class="main-header">
            <div class="main-header-logo">
                <div class="logo-header" data-background-color="dark">
                    <a href="index.html" class="logo">
                        <img src="../assets/images/logo_light.svg" alt="navbar brand" class="navbar-brand" height="20" />
                    </a>
                    <div class="nav-toggle">
                        <button class="btn btn-toggle toggle-sidebar"><i class="gg-menu-right"></i></button>
                        <button class="btn btn-toggle sidenav-toggler"><i class="gg-menu-left"></i></button>
                    </div>
                    <button class="topbar-toggler more"><i class="gg-more-vertical-alt"></i></button>
                </div>
            </div>
            <?php include_once __DIR__ . "/../includes/navbar.php"; ?>
        </div>

        <div class="container">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Schedule a Live Class</h4>
                </div>

                <?php if (isset($_SESSION['success_message'])): ?>
                    <div class="alert alert-success"><?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
                <?php endif; ?>

                <div class="card-body">
                    <form method="post">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Class</label>
                                <select class="form-control" name="class_id" id="classSelect" required>
                                    <option value="">Select Class</option>
                                    <?php foreach ($classes as $class) : ?>
                                        <option value="<?= $class['class_id'] ?>"><?= htmlspecialchars($class['class_name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Section</label>
                                <select class="form-control" name="section_id" id="sectionSelect" required>
                                    <option value="">Select Section</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Subject</label>
                                <select class="form-control" name="subject_id" id="subjectSelect" required>
                                    <option value="">Select Subject</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Title</label>
                                <input name="title" class="form-control" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label>Description</label>
                                <textarea name="description" class="form-control" rows="3"></textarea>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Start Time</label>
                                <input type="datetime-local" name="start_time" class="form-control" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label>Duration (minutes)</label>
                                <input type="number" name="duration_minutes" class="form-control" required>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Schedule</button>
                        </div>
                    </form>
                    <div class="card mt-4 shadow">
                        <div class="card-header bg-info text-white">
                            <h4 class="mb-0">Your Scheduled Live Classes</h4>
                        </div>
                        <div class="card-body">
                            <?php if (empty($schedules)): ?>
                                <div class="alert alert-warning">No classes scheduled yet.</div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Class</th>
                                                <th>Section</th>
                                                <th>Subject</th>
                                                <th>Title</th>
                                                <th>Start Time</th>
                                                <th>Duration</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($schedules as $index => $class): ?>
                                                <tr>
                                                    <td><?= $offset + $index + 1 ?></td>
                                                    <td><?= htmlspecialchars($class['class_name']) ?></td>
                                                    <td><?= htmlspecialchars($class['section_name']) ?></td>
                                                    <td><?= htmlspecialchars($class['subject_name']) ?></td>
                                                    <td><?= htmlspecialchars($class['title']) ?></td>
                                                    <td><?= date("M d, Y H:i", strtotime($class['start_time'])) ?></td>
                                                    <td><?= $class['duration_minutes'] ?> min</td>
                                                    <td>
                                                        <a href="edit_schedule.php?id=<?= $class['id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                                                        <a href="delete_schedule.php?id=<?= $class['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this class?')">Delete</a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination -->
                                <?php
                                $totalPages = ceil($totalSchedules / $perPage);
                                if ($totalPages > 1):
                                ?>
                                    <nav>
                                        <ul class="pagination justify-content-center">
                                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                                </li>
                                            <?php endfor; ?>
                                        </ul>
                                    </nav>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- JS and jQuery -->
<script src="../assets/js/core/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Dynamic AJAX for Sections and Subjects -->
<script>
$(document).ready(function () {
    $('#classSelect').on('change', function () {
        var classId = $(this).val();

        if (classId) {
            $.ajax({
                url: 'fetch_sections_subjects_schedule.php',
                method: 'POST',
                data: { class_id: classId },
                dataType: 'json',
                success: function (data) {
                    // Populate sections
                    $('#sectionSelect').empty().append('<option value="">Select Section</option>');
                    $.each(data.sections, function (index, section) {
                        $('#sectionSelect').append('<option value="' + section.section_id + '">' + section.name + '</option>');
                    });

                    // Populate subjects
                    $('#subjectSelect').empty().append('<option value="">Select Subject</option>');
                    $.each(data.subjects, function (index, subject) {
                        $('#subjectSelect').append('<option value="' + subject.subject_id + '">' + subject.name + '</option>');
                    });
                }
            });
        } else {
            $('#sectionSelect').html('<option value="">Select Section</option>');
            $('#subjectSelect').html('<option value="">Select Subject</option>');
        }
    });
});
</script>

<!-- JS Includes (keep as in your template) -->
<script src="../assets/js/core/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</body>
</html>
