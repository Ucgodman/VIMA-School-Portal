<?php
session_start();
$pageTitle = "Manage Classes";

// Include necessary files
include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../includes/admin_header.php";

try {
    // Fetch teachers for dropdown selection
    $stmtTeachers = $pdo->query("SELECT id, firstname, lastname FROM staff WHERE role_id = 2 ORDER BY firstname ASC");
    $teachers = $stmtTeachers->fetchAll(PDO::FETCH_ASSOC);

    // Fetch all classes
    $stmtClasses = $pdo->query("SELECT c.class_id, c.class_name, s.firstname, s.lastname 
                            FROM classes c 
                            LEFT JOIN staff s ON c.staff_id = s.id 
                            ORDER BY c.class_name ASC");

    $classes = $stmtClasses->fetchAll(PDO::FETCH_ASSOC) ?: []; // Ensure $classes is an array

} catch (PDOException $e) {
    die("Query Error: " . $e->getMessage());
}
?>

<div class="wrapper">
    <?php include_once __DIR__ . "/../includes/sidebar.php"; ?>
    <div class="main-panel">
        <div class="main-header">
            <div class="main-header-logo">
                <div class="logo-header" data-background-color="dark">
                    <a href="index.html" class="logo">
                        <img src="/../assets/images/logo_light.svg" alt="navbar brand" class="navbar-brand" height="20" />
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
            <div class="row">
                <!-- Add Class Form -->
                <div class="col-md-4">
                    <div class="card shadow">
                        <div class="card-header bg-secondary text-white">
                            <h4 class="mb-0">Add Class</h4>
                        </div>
                        <div class="card-body">
                            <form id="addClassForm">
                                <div class="form-group">
                                    <label for="className">Class Name</label>
                                    <input type="text" id="className" name="class_name" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label for="teacherSelect">Select Teacher</label>
                                    <select id="teacherSelect" name="staff_id" class="form-control" required>
                                        <option value="">-- Select Teacher --</option>
                                        <?php foreach ($teachers as $teacher): ?>
                                            <option value="<?= $teacher['id']; ?>">
                                                <?= htmlspecialchars($teacher['firstname'] . " " . $teacher['lastname']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-success">Add Class</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- List Classes -->
                <div class="col-md-8">
                    <div class="card shadow">
                        <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">List Classes</h4>
                            <input type="text" id="searchInput" class="form-control w-50" placeholder="Search class...">
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>Class Name</th>
                                            <th>Teacher</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="classTableBody">
                                        <?php foreach ($classes as $class): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($class['class_name']); ?></td>
                                                <td><?= !empty($class['firstname']) ? htmlspecialchars($class['firstname'] . " " . $class['lastname']) : "No Teacher Assigned"; ?></td>
                                                <td>
                                                    <a href="edit_class.php?id=<?= $class['class_id']; ?>" class="btn btn-warning btn-xs rounded-circle d-flex justify-content-center align-items-center bi bi-pencil" style="width: 26px; height: 26px; color: white;" title="Edit"></a>
                                                    <a href="delete_class.php?id=<?= $class['class_id']; ?>" class="btn btn-danger btn-xs rounded-circle d-flex justify-content-center align-items-center bi bi-trash" style="width: 26px; height: 26px; color: white; " title="Delete"
                                                            onclick="return confirm('Are you sure you want to delete this class?');"></a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="d-flex justify-content-between">
                                <p id="entryInfo">Showing 1 to <?= count($classes) ?> of <?= count($classes) ?> entries</p>
                                <nav>
                                    <ul class="pagination">
                                        <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
                                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                        <li class="page-item"><a class="page-link" href="#">Next</a></li>
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div> 
            </div>
        </div>
    </div>
</div>

<script>
// Search Functionality
document.getElementById("searchInput").addEventListener("keyup", function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll("#classTableBody tr");

    rows.forEach(row => {
        let className = row.cells[0].textContent.toLowerCase();
        row.style.display = className.includes(filter) ? "" : "none";
    });
});

// Handle Add Class Form Submission
document.getElementById("addClassForm").addEventListener("submit", function(e) {
    e.preventDefault();
    let formData = new FormData(this);

    fetch("add_class.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        alert(data);
        location.reload();
    })
    .catch(error => console.error("Error adding class:", error));
});
</script>

<!-- Core JS Files -->
<script src="../assets/js/core/bootstrap.bundle.min.js"></script>
<script src="../assets/js/core/popper.min.js"></script>
<script src="../assets/js/core/bootstrap.min.js"></script>

<!-- jQuery Scrollbar -->
<script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
<script src="../assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js"></script>

<!-- Bootstrap Notify -->
<script src="../assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js"></script>

<!-- Sweet Alert -->
<script src="../assets/js/plugin/sweetalert/sweetalert.min.js"></script>

<!-- Kaiadmin JS -->
<script src="../assets/js/kaiadmin.min.js"></script>

</body>
</html>
