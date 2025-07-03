<?php
session_start();
$pageTitle = "Syllabus";

// Include database connection
include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../functions/helper_functions.php";
include_once __DIR__ . "/../includes/admin_header.php";

try {
    // Fetch Classes
    $classQuery = "SELECT class_id, class_name FROM classes";
    $classResult = $pdo->query($classQuery)->fetchAll(PDO::FETCH_ASSOC);

    // Pagination settings
    $limit = 5; // Number of records per page
    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    $start = ($page - 1) * $limit;

    // Get total number of syllabus records
    $totalQuery = "SELECT COUNT(*) as total FROM syllabus";
    $totalResult = $pdo->query($totalQuery)->fetch(PDO::FETCH_ASSOC);
    $totalRecords = $totalResult['total'];
    $totalPages = ceil($totalRecords / $limit);

    // Fetch Syllabus Records with Pagination
    $syllabusQuery = "SELECT s.*, u.email AS uploader_name 
                      FROM syllabus s
                      JOIN users u ON s.uploader_id = u.id
                      LIMIT $start, $limit";
    $syllabusResult = $pdo->query($syllabusQuery)->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<div class="wrapper">
    <?php include_once __DIR__ . "/../includes/sidebar.php"; ?>
    <div class="main-panel">
        <div class="main-header">
             <div class="main-header-logo">
                    <div class="logo-header" data-background-color="dark">
                    <a href="index.html" class="logo">
                        <img
                        src="/../assets/images/ogo_light.svg"
                        alt="navbar brand"
                        class="navbar-brand"
                        height="20"
                        />
                    </a>
                    <div class="nav-toggle">
                        <button class="btn btn-toggle toggle-sidebar">
                        <i class="gg-menu-right"></i>
                        </button>
                        <button class="btn btn-toggle sidenav-toggler">
                        <i class="gg-menu-left"></i>
                        </button>
                    </div>
                    <button class="topbar-toggler more">
                        <i class="gg-more-vertical-alt"></i>
                    </button>
                    </div>
                </div>
            <?php include_once __DIR__ . "/../includes/navbar.php"; ?>
        </div>
        
        <div class="container">
            <div class="row">
                <div class="col-md-5">
                    <div class="card shadow-sm">
                        <div class="card-header bg-secondary text-white">Add Syllabus</div>
                        <div class="card-body">
                            <form action="upload_syllabus.php" method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label class="form-label">Title</label>
                                    <input type="text" class="form-control" name="title" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea class="form-control" name="description" rows="3"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Class</label>
                                    <select name="class_id" class="form-control" onchange="fetchSubjects(this.value)" required>
                                        <option value="">Select Class</option>
                                        <?php foreach ($classResult as $class) { ?>
                                            <option value="<?= $class['class_id'] ?>"><?= $class['class_name'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Subject</label>
                                    <select name="subject_id" id="subject_selector" class="form-control" required>
                                        <option value="">Select Subject</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Browse File</label>
                                    <input type="file" name="file" class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-success">Add Syllabus</button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-7">
                    <div class="card shadow-sm">
                        <div class="card-header bg-secondary text-white">List Syllabus</div>
                        <div class="card-body">
                            <div class="mb-3">
                                <?php foreach ($classResult as $class) { ?>
                                    <button class="btn btn-info btn-sm" onclick="filterSyllabus(<?= $class['class_id'] ?>)">
                                        <?= $class['class_name'] ?>
                                    </button>
                                <?php } ?>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="bg-secondary text-white">
                                        <tr>
                                            <th>#</th>
                                            <th>Title</th>
                                            <th>Description</th>
                                            <th>Uploader</th>
                                            <th>Timestamp</th>
                                            <th>File Name</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="syllabus_list">
                                        <?php foreach ($syllabusResult as $row) { ?>
                                            <tr>
                                                <td><?= $row['syllabus_id'] ?></td>
                                                <td><?= htmlspecialchars($row['title']) ?></td>
                                                <td><?= htmlspecialchars($row['description']) ?></td>
                                                <td><?= htmlspecialchars($row['uploader_name']) ?></td>
                                                <td><?= $row['timestamp'] ?></td>
                                                <td><a href="uploads/<?= htmlspecialchars($row['file_name']) ?>" target="_blank">Download</a></td>
                                                <td>
                                                    <a href="uploads/<?= htmlspecialchars($row['file_name']) ?>" class="btn btn-success btn-sm" download>Download</a>
                                                    <button class="btn btn-danger btn-sm" onclick="deleteSyllabus(<?= $row['syllabus_id'] ?>)">Delete</button>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                             <!-- Pagination -->
                            <nav>
                                <ul class="pagination justify-content-center">
                                    <!-- First Page -->
                                    <li class="page-item <?= ($page == 1) ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?page=1">First</a>
                                    </li>

                                    <!-- Previous Page -->
                                    <li class="page-item <?= ($page == 1) ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
                                    </li>

                                    <!-- Page Numbers -->
                                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++) { ?>
                                        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                            <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                                        </li>
                                    <?php } ?>

                                    <!-- Next Page -->
                                    <li class="page-item <?= ($page == $totalPages) ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
                                    </li>

                                    <!-- Last Page -->
                                    <li class="page-item <?= ($page == $totalPages) ? 'disabled' : '' ?>">
                                        <a class="page-link" href="?page=<?= $totalPages ?>">Last</a>
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

<script>
function fetchSubjects(classId) {
    fetch('fetch_subjects.php?id=' + classId)
        .then(response => response.json())
        .then(data => {
            let subjectDropdown = document.getElementById('subject_selector');
            subjectDropdown.innerHTML = '<option value="">Select Subject</option>';
            data.forEach(subject => {
                subjectDropdown.innerHTML += `<option value="${subject.subject_id}">${subject.name}</option>`;
            });
        })
        .catch(error => console.error('Error fetching subjects:', error));
}


function filterSyllabus(classId, page = 1) {
    fetch('get_syllabus.php?id=' + classId + '&page=' + page)
        .then(response => response.text())
        .then(data => {
            document.getElementById('syllabus_list').innerHTML = data;
        });
}


function deleteSyllabus(id) {
    if (confirm("Are you sure you want to delete this syllabus?")) {
        fetch('delete_syllabus.php?id=' + id, { method: 'GET' })
            .then(response => response.text())
            .then(data => {
                alert(data);
                location.reload();
            });
    }
}
</script>
  <!--   Core JS Files   -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
    <script src="../assets/js/core/popper.min.js"></script>
    <script src="../assets/js/core/bootstrap.min.js"></script>

    <!-- jQuery Scrollbar -->
    <script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
    <!-- jQuery Sparkline -->
    <script src="../assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js"></script>

    <!-- Bootstrap Notify -->
    <script src="../assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js"></script>

    <!-- Sweet Alert -->
    <script src="../assets/js/plugin/sweetalert/sweetalert.min.js"></script>

    <!-- Kaiadmin JS -->
    <script src="../assets/js/kaiadmin.min.js"></script>

</body>
</html>
