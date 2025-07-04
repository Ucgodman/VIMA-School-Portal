<?php
session_start();
$pageTitle = "Student Information";

// Include necessary files
include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../includes/admin_header.php";

// Fetch all classes for the select box
$stmt = $pdo->query("SELECT class_id, class_name FROM classes ORDER BY class_name ASC");
$classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="wrapper">
    <?php include_once __DIR__ . "/../includes/sidebar.php"; ?>
    <div class="main-panel">
            <div class="main-header">
                    <div class="main-header-logo">
                        <!-- Logo Header -->
                        <div class="logo-header" data-background-color="dark">
                        <a href="index.html" class="logo">
                            <img
                            src="/../assets/images/logo_light.svg"
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
                <div class="card shadow">
                    <div class="card-header bg-secondary text-white">
                        <h4 class="mb-0">List Students</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="classSelect">Select Class</label>
                                <select class="form-control" id="classSelect">
                                    <option value="">-- Select Class --</option>
                                    <?php foreach ($classes as $class): ?>
                                        <option value="<?= $class['class_id']; ?>"><?= htmlspecialchars($class['class_name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button id="getStudentsBtn" class="btn btn-success">Get Students</button>
                            </div>
                        </div>

                        <!-- Table for displaying students -->
                        <div class="table-responsive mt-4">
                            <table class="table table-bordered">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Passport</th>
                                        <th>Admission No</th>
                                        <th>First Name</th>
                                        <th>Class</th>
                                        <th>Email</th>
                                        <th>Parent</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="studentTableBody">
                                    <tr>
                                        <td colspan="7" class="text-center">Select a class and click "Get Students" to load data.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</div>

<script>
document.getElementById("getStudentsBtn").addEventListener("click", function () {
    let classId = document.getElementById("classSelect").value;
    if (classId === "") {
        alert("Please select a class.");
        return;
    }

    fetch("fetch_student.php?id=" + classId)
        .then(response => response.json())
        .then(data => {
            let studentTable = document.getElementById("studentTableBody");
            studentTable.innerHTML = "";

            if (data.error) {
                console.error("Error:", data.error);
                studentTable.innerHTML = `<tr><td colspan="7" class="text-center text-danger">${data.error}</td></tr>`;
                return;
            }

            if (data.length === 0) {
                studentTable.innerHTML = '<tr><td colspan="7" class="text-center">No students found.</td></tr>';
                return;
            }

            data.forEach(student => {
                let row = `<tr>
                    <td><img src="${student.passport}" alt="Passport" width="50" height="50"></td>
                    <td>${student.admission_no}</td>
                    <td>${student.first_name}</td>
                    <td>${student.class_name}</td>
                    <td>${student.email}</td>
                    <td>${student.parent_name}</td>
                    <td>
                        <a href="edit_student.php?id=${student.student_id}" class="btn btn-primary btn-sm">Edit</a>
                        <a href="delete_student.php?id=${student.student_id}" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?');">Delete</a>
                        <a href="change_password.php?id=${student.student_id}" class="btn btn-warning btn-sm">Change Password</a>
                    </td>
                </tr>`;
                studentTable.innerHTML += row;
            });
        })
        .catch(error => {
            console.error("Fetch error:", error);
            document.getElementById("studentTableBody").innerHTML = `<tr><td colspan="7" class="text-center text-danger">Error fetching students.</td></tr>`;
        });
});


</script>


</script>
    <!--   Core JS Files   -->
<script src="../assets/js/core/bootstrap.bundle.min.js"></script>
<script src="../assets/js/core/jquery-3.7.1.min.js"></script>


</body>
</html>
