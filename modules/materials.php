<?php
session_start();
$pageTitle = "Manage Materials";
include_once __DIR__ . '/../config/database.php';
include_once __DIR__ . '/../includes/admin_header.php';

// Fetch classes
$classes = $pdo->query("SELECT * FROM classes ORDER BY class_name ASC")->fetchAll();

// Fetch teachers
$teachers = $pdo->query("SELECT * FROM staff WHERE role_id = 2 ORDER BY lastname ASC")->fetchAll();

// Fetch assignments
$materials = $pdo->query("SELECT a.*, c.class_name, s.name AS subject_name, t.lastname, t.firstname 
                            FROM assignments a 
                            JOIN classes c ON a.class_id = c.class_id 
                            JOIN subjects s ON a.subject_id = s.subject_id 
                            JOIN staff t ON a.staff_id = t.id 
                            ORDER BY a.created_at DESC")->fetchAll();
?>

<div class="wrapper">
    <?php include_once __DIR__ . "/../includes/sidebar.php"; ?>
    <div class="main-panel">
        <div class="main-header">
            <div class="main-header-logo">
                <div class="logo-header" data-background-color="dark">
                    <a href="index.html" class="logo">
                        <img src="/../assets/images/ogo_light.svg" alt="navbar brand" class="navbar-brand" height="20" />
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
                <div class="col-md-12">
                    <div class="card shadow">
                        <div class="card-header bg-secondary text-white">Add Material</div>
                        <div class="card-body">
                            <form id="materialForm" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title</label>
                                    <input type="text" class="form-control" id="title" name="title" required>
                                </div>
                                <div class="mb-3">
                                    <label for="class_id" class="form-label">Select Class</label>
                                    <select class="form-control" id="class_id" name="class_id" onchange="fetchSubjects(this.value)" required>
                                        <option value="">Select</option>
                                        <?php foreach ($classes as $class): ?>
                                            <option value="<?php echo $class['class_id']; ?>"><?php echo $class['class_name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="subject_id" class="form-label">Select Subject</label>
                                    <select class="form-control" id="subject_id" name="subject_id" required>
                                        <option value="">Select Class First</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="staff_id" class="form-label">Select Teacher</label>
                                    <select class="form-control" id="staff_id" name="staff_id" required>
                                        <option value="">Select</option>
                                        <?php foreach ($teachers as $teacher): ?>
                                            <option value="<?php echo $teacher['id']; ?>">
                                                <?php echo $teacher['lastname'] . ' ' . $teacher['firstname']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="due_date" class="form-label">Due Date</label>
                                    <input type="date" class="form-control" id="due_date" name="due_date" required>
                                </div>
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Upload Document</label>
                                    <div class="drop-zone" id="drop-zone">
                                        <input type="file" class="drop-zone__input form-control" id="file" name="file" required>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success">Add Material</button>
                            </form>
                        </div>
                    </div>

                    <!-- Material List -->
                    <div class="card mt-4 shadow">
                        <div class="card-header bg-secondary text-white">Material List</div>
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
                                    <?php foreach ($materials as $index => $material): ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo htmlspecialchars($material['title']); ?></td>
                                            <td><?php echo htmlspecialchars($material['class_name']); ?></td>
                                            <td><?php echo htmlspecialchars($material['subject_name']); ?></td>
                                            <td><?php echo htmlspecialchars($material['lastname'] . ' ' . $material['firstname']); ?></td>
                                            <td>
                                                <a href="delete_material.php?id=<?php echo $material['assignment_id']; ?>" class="btn btn-danger btn-sm">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <nav>
                                <ul class="pagination">
                                    <li class="page-item"><a class="page-link" href="#">Previous</a></li>
                                    <li class="page-item"><a class="page-link" href="#">1</a></li>
                                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                                    <li class="page-item"><a class="page-link" href="#">Next</a></li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div> <!-- End col-md-12 -->
            </div>
        </div>
    </div>
</div>

<script>
function fetchSubjects(classId) {
    if (classId) {
        $.ajax({
            url: 'fetch_sections.php',
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

$('#materialForm').submit(function(e) {
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
        url: 'submit_material.php',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            alert('Material added successfully!');
            location.reload();
        }
    });
});

// Drag & Drop File Upload
const dropZone = document.getElementById("drop-zone");
const fileInput = document.getElementById("file");

dropZone.addEventListener("click", () => fileInput.click());

dropZone.addEventListener("dragover", (e) => {
    e.preventDefault();
    dropZone.classList.add("drag-over");
});

dropZone.addEventListener("dragleave", () => dropZone.classList.remove("drag-over"));

dropZone.addEventListener("drop", (e) => {
    e.preventDefault();
    dropZone.classList.remove("drag-over");
    const files = e.dataTransfer.files;
    fileInput.files = files;
});
</script>

<!-- Core JS Files -->
<script src="../assets/js/core/jquery-3.7.1.min.js"></script>
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

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
