<?php
session_start();
$pageTitle = "Manage Subjects";
include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../includes/admin_header.php";

// Fetch classes
$classQuery = "SELECT class_id, class_name FROM classes ORDER BY class_name ASC";
$classes = $pdo->query($classQuery)->fetchAll(PDO::FETCH_ASSOC);

// Fetch teachers
$teacherQuery = "SELECT id, firstname, lastname FROM staff WHERE role_id = 2 ORDER BY firstname ASC";
$teachers = $pdo->query($teacherQuery)->fetchAll(PDO::FETCH_ASSOC);

// Handle adding a subject
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_subject'])) {
    $subjectName = trim($_POST['subject_name']);
    $classId = $_POST['class_id'];
    $teacherId = $_POST['teacher_id'];

    $insertQuery = "INSERT INTO subjects (name, class_id, staff_id) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($insertQuery);
    $stmt->execute([$subjectName, $classId, $teacherId]);

    echo "<script>alert('Subject Added Successfully!'); window.location.href='subjects.php';</script>";
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
                        src="../assets/images/logo_light.svg"
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

        <div class="container mt-4">
            <div class="row">
                <!-- LEFT CARD - ADD SUBJECT -->
                <div class="col-md-5">
                    <div class="card shadow">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0">Add Subject</h5>
                        </div>
                        <div class="card-body">
                            <form action="" method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Subject Name</label>
                                    <input type="text" class="form-control" name="subject_name" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Select Class</label>
                                    <select name="class_id" class="form-control" required>
                                        <option value="">Select Class</option>
                                        <?php foreach ($classes as $class) { ?>
                                            <option value="<?= $class['class_id'] ?>"><?= $class['class_name'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Select Teacher</label>
                                    <select name="teacher_id" class="form-control" required>
                                        <option value="">Select Teacher</option>
                                        <?php foreach ($teachers as $teacher) { ?>
                                            <option value="<?= $teacher['id'] ?>">
                                                <?= $teacher['firstname'] . " " . $teacher['lastname'] ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <button type="submit" name="add_subject" class="btn btn-success">Add Subject</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- RIGHT CARD - GET SUBJECT LIST -->
                <div class="col-md-7">
                    <div class="card shadow">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0">List Subjects</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Select Class</label>
                                <select id="filter_class" class="form-control">
                                    <option value="">Select Class</option>
                                    <?php foreach ($classes as $class) { ?>
                                        <option value="<?= $class['class_id'] ?>"><?= $class['class_name'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            <button id="get_subjects" class="btn btn-primary">Get Subjects</button>

                            <hr>
                            <div id="subject_list">
                                <table class="table table-bordered mt-3">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Class Name</th>
                                            <th>Subject Name</th>
                                            <th>Teacher</th>
                                            <th>Options</th>
                                        </tr>
                                    </thead>
                                    <tbody id="subject_table">
                                        <tr><td colspan="5" class="text-center">Select a class and click "Get Subjects"</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>  
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById("get_subjects").addEventListener("click", function () {
        let classId = document.getElementById("filter_class").value;
        let subjectTable = document.getElementById("subject_table");

        if (classId === "") {
            alert("Please select a class first.");
            return;
        }

        // Fetch Subjects via AJAX
        fetch("fetch_subjects.php?class_id=" + classId)
        .then(response => response.json())
        .then(data => {
            subjectTable.innerHTML = "";
            if (data.length === 0) {
                subjectTable.innerHTML = `<tr><td colspan="5" class="text-center">No subjects found.</td></tr>`;
                return;
            }

            data.forEach((subject, index) => {
                subjectTable.innerHTML += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${subject.class_name}</td>
                        <td>${subject.name}</td>
                        <td>${subject.teacher}</td>
                        <td>
                            <a href="edit_subject.php?id=${subject.subject_id}" class="btn btn-warning btn-sm rounded-circle d-flex justify-content-center align-items-center" style="width: 26px; height: 26px;" title="Edit">
                                <i class="bi bi-pencil" style="font-size: 10px; color: white;"></i></a>
                            <a href="delete_subject.php?id=${subject.subject_id}" class="btn btn-danger btn-sm rounded-circle d-flex justify-content-center align-items-center" style="width: 26px; height: 26px;" title="Delete" onclick="return confirm('Are you sure you want to delete this Subject?');">
                                <i class="bi bi-trash" style="font-size: 10px;"></i></a>
                        </td>
                    </tr>
                `;
            });
        })
        .catch(error => console.error("Error fetching subjects:", error));

    });
</script>
<!--   Core JS Files   -->
    <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
   
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
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
