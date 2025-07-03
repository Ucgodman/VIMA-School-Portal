<?php
session_start();
$pageTitle = "Manage Assignments";
include_once __DIR__ . '/../config/database.php';
include_once __DIR__ . '/../includes/admin_header.php';

// Fetch classes
$classes = $pdo->query("SELECT * FROM classes ORDER BY class_name ASC")->fetchAll();

// Fetch teachers
$teachers = $pdo->query("SELECT staff_id, firstname, middlename, lastname FROM staff WHERE role_id = 2 ORDER BY lastname ASC")->fetchAll();

// Pagination
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;
$totalAssignments = $pdo->query("SELECT COUNT(*) as total FROM assignments")->fetch()['total'];
$totalPages = ceil($totalAssignments / $limit);

// Fetch assignments with pagination
$assignments = $pdo->prepare("SELECT a.*, c.class_name, s.name AS subject_name, t.lastname, t.firstname 
                            FROM assignments a 
                            JOIN classes c ON a.class_id = c.class_id 
                            JOIN subjects s ON a.subject_id = s.subject_id 
                            JOIN staff t ON a.staff_id = t.id 
                            ORDER BY a.created_at DESC 
                            LIMIT :start, :limit");
$assignments->bindValue(':start', $start, PDO::PARAM_INT);
$assignments->bindValue(':limit', $limit, PDO::PARAM_INT);
$assignments->execute();
$assignments = $assignments->fetchAll();
?>

<div class="wrapper">
    <?php include_once __DIR__ . "/../includes/sidebar.php"; ?>
    <div class="main-panel">
        <?php include_once __DIR__ . "/../includes/navbar.php"; ?>

        <div class="container">
            <div class="row">
                <div class="col-md-12">

                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
                    <?php endif; ?>

                    <div class="card shadow">
                        <div class="card-header bg-secondary text-white">
                            <a class="text-white text-decoration-none" data-bs-toggle="collapse" href="#collapseAssignment">Add Assignment</a>
                        </div>
                        <div id="collapseAssignment" class="collapse show">
                            <div class="card-body">
                                <form id="assignmentForm" enctype="multipart/form-data">
                                    <div class="mb-3">
                                        <label class="form-label">Title</label>
                                        <input type="text" class="form-control" name="title" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Select Class</label>
                                        <select class="form-control" name="class_id" onchange="fetchSections(this.value)" required>
                                            <option value="">Select</option>
                                            <?php foreach ($classes as $class): ?>
                                                <option value="<?= $class['class_id']; ?>"><?= $class['class_name']; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Select Section</label>
                                        <select class="form-control" id="section_id" name="section_id" required>
                                            <option value="">Select Class First</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Select Subject</label>
                                        <select class="form-control" id="subject_id" name="subject_id" required>
                                            <option value="">Select Class First</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Select Teacher</label>
                                        <select class="form-control" name="staff_id" required>
                                            <option value="">Select</option>
                                            <?php foreach ($teachers as $teacher): ?>
                                                <option value="<?= $teacher['staff_id']; ?>">
                                                    <?= $teacher['lastname'] . ' ' . $teacher['firstname']; ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Due Date</label>
                                        <input type="date" class="form-control" name="due_date" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Description</label>
                                        <textarea class="form-control" name="description" rows="3"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Upload Document</label>
                                        <div class="drop-zone" id="drop-zone">
                                            <span class="drop-zone__prompt">Drag & Drop file here or click to upload</span>
                                            <input type="file" class="drop-zone__input form-control" id="file" name="file" required>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-success">Add Assignment</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Assignment List -->
                    <div class="card mt-4 shadow">
                        <div class="card-header bg-secondary text-white">Assignment List</div>
                        <div class="card-body">
                            <table class="table table-bordered table-responsive">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Title</th>
                                        <th>Class</th>
                                        <th>Subject</th>
                                        <th>Teacher</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($assignments as $index => $assignment): ?>
                                        <tr>
                                            <td><?= $index + 1; ?></td>
                                            <td><?= htmlspecialchars($assignment['title']); ?></td>
                                            <td><?= htmlspecialchars($assignment['class_name']); ?></td>
                                            <td><?= htmlspecialchars($assignment['subject_name']); ?></td>
                                            <td><?= htmlspecialchars($assignment['lastname'] . ' ' . $assignment['firstname']); ?></td>
                                            <td>
                                                <a href="edit_assignment.php?id=<?= $assignment['assignment_id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                                                <a href="delete_assignment.php?id=<?= $assignment['assignment_id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                            <!-- Pagination -->
                            <nav>
                                <ul class="pagination justify-content-center">
                                    <li class="page-item <?= ($page == 1) ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?page=1">First</a>
                                    </li>
                                    <li class="page-item <?= ($page <= 1) ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?page=<?= $page - 1; ?>">Previous</a>
                                    </li>
                                    <?php for ($i = max(1, $page - 1); $i <= min($page + 1, $totalPages); $i++): ?>
                                        <li class="page-item <?= ($i == $page) ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?= $i; ?>"><?= $i; ?></a>
                                        </li>
                                    <?php endfor; ?>
                                    <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?page=<?= $page + 1; ?>">Next</a>
                                    </li>
                                    <li class="page-item <?= ($page == $totalPages) ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?page=<?= $totalPages; ?>">Last</a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Core JS Libraries (jQuery MUST load before your script) -->
<script src="../assets/js/core/jquery-3.7.1.min.js"></script>
<script src="../assets/js/core/popper.min.js"></script>
<script src="../assets/js/core/bootstrap.min.js"></script>
<script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
<script src="../assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js"></script>
<script src="../assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js"></script>
<script src="../assets/js/plugin/sweetalert/sweetalert.min.js"></script>
<script src="../assets/js/kaiadmin.min.js"></script>

<!-- Custom Script -->
<script>
$(document).ready(function () {
    function fetchSections(classId) {
        if (classId) {
            $.post('fetch_sections.php', { class_id: classId }, function (response) {
                $('#section_id').html(response);
                $('#section_id').change(function () {
                    fetchSubjects(classId);
                });
            });
        }
    }

    function fetchSubjects(classId) {
        if (classId) {
            $.post('fetch_subjects.php', { class_id: classId }, function (response) {
                $('#subject_id').html(response);
            });
        }
    }

    // Expose function globally
    window.fetchSections = fetchSections;

    // Handle form submission
    $('#assignmentForm').submit(function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            url: 'submit_assignments.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.trim() === "success") {
                    alert('Assignment added successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + response);
                }
            }
        });
    });

    // Drag and drop file upload
    const dropZone = document.getElementById("drop-zone");
    const fileInput = document.getElementById("file");

    dropZone.addEventListener("click", () => fileInput.click());
    dropZone.addEventListener("dragover", (e) => {
        e.preventDefault();
        dropZone.classList.add("drag-over");
    });
    dropZone.addEventListener("dragleave", () => {
        dropZone.classList.remove("drag-over");
    });
    dropZone.addEventListener("drop", (e) => {
        e.preventDefault();
        dropZone.classList.remove("drag-over");
        fileInput.files = e.dataTransfer.files;
    });
});
</script>
