<?php

$pageTitle = "Manage Assignments";
include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../includes/admin_header.php";

// Fetch classes, subjects, and teachers
$classQuery = "SELECT class_id, numeric_name FROM classes ORDER BY numeric_name ASC";
$subjectQuery = "SELECT subject_id, name FROM subjects ORDER BY name ASC";
$teacherQuery = "SELECT staff_id, firstname, lastname FROM staff WHERE role_id = 2 ORDER BY firstname ASC";

$classes = $pdo->query($classQuery)->fetchAll(PDO::FETCH_ASSOC);
$subjects = $pdo->query($subjectQuery)->fetchAll(PDO::FETCH_ASSOC);
$teachers = $pdo->query($teacherQuery)->fetchAll(PDO::FETCH_ASSOC);

// Fetch assignments list
$assignmentsQuery = "SELECT a.*, c.numeric_name as class_name, s.name as subject_name, 
                     t.firstname, t.lastname FROM assignments a
                     JOIN classes c ON a.class_id = c.class_id
                     JOIN subjects s ON a.subject_id = s.subject_id
                     JOIN staff t ON a.staff_id = t.staff_id
                     ORDER BY a.created_at DESC";
$assignments = $pdo->query($assignmentsQuery)->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST['title']);
    $classId = $_POST['class_id'];
    $subjectId = $_POST['subject_id'];
    $teacherId = $_POST['teacher_id'];
    $dueDate = $_POST['due_date'];
    $description = trim($_POST['description']);
    $filePath = "";

    // Handle File Upload
    if (!empty($_FILES['file']['name'])) {
        $targetDir = "uploads/assignments/";
        $fileName = time() . "_" . basename($_FILES['file']['name']);
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
            $filePath = $targetFile;
        } else {
            echo "<script>alert('File upload failed!');</script>";
        }
    }

    // Insert Assignment into Database
    $insertQuery = "INSERT INTO assignments (title, class_id, subject_id, staff_id, description, file_path, due_date) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($insertQuery);
        $stmt->execute([$title, $classId, $subjectId, $teacherId, $description, $filePath, $dueDate]);


    echo "<script>alert('Assignment Added Successfully!'); window.location.href='manage_assignments.php';</script>";
}
?>

<div class="wrapper">
    <?php include_once __DIR__ . "/../includes/admin_sidebar.php"; ?>
    <div class="main-panel">

        <div class="main-header">
                    <div class="main-header-logo">
                        <!-- Logo Header -->
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
                        <!-- End Logo Header -->
                    </div>
                    <!-- Navbar Header -->
                <?php include_once __DIR__ . "/../includes/navbar.php";?>
                <!-- End Navbar -->
        </div>
        

        <div class="container">

            <!-- ADD NEW ASSIGNMENT CARD -->
            <div class="card shadow">
               <div class="card-header bg-secondary text-white" data-bs-toggle="collapse" data-bs-target="#addAssignment">
                    <h5 class="mb-0">Add New Assignment</h5>
                </div>

                <div id="addAssignment" class="collapse show">
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label">Title</label>
                                <input type="text" class="form-control" name="title" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Class</label>
                                <select name="class_id" class="form-control" required>
                                    <option value="">Select Class</option>
                                    <?php foreach ($classes as $class) { ?>
                                        <option value="<?= $class['class_id'] ?>"><?= $class['numeric_name'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Subject</label>
                                <select name="subject_id" class="form-control" required>
                                    <option value="">Select Subject</option>
                                    <?php foreach ($subjects as $subject) { ?>
                                        <option value="<?= $subject['subject_id'] ?>"><?= $subject['name'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Teacher</label>
                                <select name="teacher_id" class="form-control" required>
                                    <option value="">Select Teacher</option>
                                    <?php foreach ($teachers as $teacher) { ?>
                                        <option value="<?= $teacher['staff_id'] ?>">
                                            <?= $teacher['firstname'] . " " . $teacher['lastname'] ?>
                                        </option>
                                    <?php } ?>
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
                                <label class="form-label">Upload File</label>
                                <input type="file" class="form-control" name="file">
                            </div>

                            <button type="submit" class="btn btn-success">Add Assignment</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- LIST OF ASSIGNMENTS -->
            <div class="card shadow mt-4">
                <div class="card-header bg-secondary text-white" data-bs-toggle="collapse" data-bs-target="#assignmentList">
                    <h5 class="mb-0">Assignment List</h5>
                </div>

                <div id="assignmentList" class="collapse show">
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Class</th>
                                    <th>Subject</th>
                                    <th>Teacher</th>
                                    <th>Description</th>
                                    <th>File</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($assignments as $assignment) { ?>
                                    <tr>
                                        <td><?= htmlspecialchars($assignment['title']) ?></td>
                                        <td><?= htmlspecialchars($assignment['class_name']) ?></td>
                                        <td><?= htmlspecialchars($assignment['subject_name']) ?></td>
                                        <td><?= htmlspecialchars($assignment['firstname'] . " " . $assignment['lastname']) ?></td>
                                        <td><?= htmlspecialchars($assignment['description']) ?></td>
                                        <td>
                                            <?php if ($assignment['file_path']) { ?>
                                                <a href="<?= $assignment['file_path'] ?>" download>Download</a>
                                            <?php } else {
                                                echo "No File";
                                            } ?>
                                        </td>
                                        <td>
                                            <a href="edit_assignment.php?id=<?= $assignment['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                            <a href="delete_assignment.php?id=<?= $assignment['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this assignment?')">Delete</a>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
  <!--   Core JS Files   -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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