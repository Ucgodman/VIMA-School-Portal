<?php
session_start();
$pageTitle = "Staff Management";

// Include database connection
include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../functions/helper_functions.php";
include_once __DIR__ . "/../includes/admin_header.php";

// Function to generate a unique six-digit Staff ID
function generateUniqueStaffID($pdo) {
    do {
        $staffID = str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT);
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM staff WHERE staff_id = ?");
        $stmt->execute([$staffID]);
        $count = $stmt->fetchColumn();
    } while ($count > 0); // Ensure the ID is unique

    return $staffID;
}

try {
    // Fetch Roles
    $roleQuery = "SELECT id, name FROM roles";
    $roleResult = $pdo->query($roleQuery)->fetchAll(PDO::FETCH_ASSOC);

    // Fetch Departments
    $deptQuery = "SELECT department_id, name FROM departments";
    $deptResult = $pdo->query($deptQuery)->fetchAll(PDO::FETCH_ASSOC);

    // Fetch Staff List
    $staffQuery = "SELECT s.*, r.name AS role_name, d.name AS department_name
                   FROM staff s
                   JOIN roles r ON s.role_id = r.id
                   JOIN departments d ON s.department_id = d.department_id";
    $staffResult = $pdo->query($staffQuery)->fetchAll(PDO::FETCH_ASSOC);

    // Generate unique staff ID
    $uniqueStaffID = generateUniqueStaffID($pdo);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<div class="wrapper">
    <?php include_once __DIR__ . "/../includes/sidebar.php"; ?>

    <div class="main-panel">
        <div class="main-header">
            <div class="main-header-logo">
                <!-- Logo Header -->
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
                <!-- End Logo Header -->
            </div>
            <!-- Navbar Header -->
            <?php include_once __DIR__ . "/../includes/navbar.php"; ?>
            <!-- End Navbar -->
        </div>

        <div class="container mt-7">
            <div class="row">
                <!-- Add Staff Form -->
                <div class="col-md-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Add Staff</h5>
                            <button class="btn btn-light btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#addStaffForm">
                                Toggle Form
                            </button>
                        </div>
                        <div class="collapse show" id="addStaffForm">
                            <div class="card-body">
                                <form action="save_staff.php" method="POST" enctype="multipart/form-data">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Staff ID (Auto-generated)</label>
                                            <input type="text" class="form-control" name="staff_id" value="<?= $uniqueStaffID ?>" readonly>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">First Name</label>
                                            <input type="text" class="form-control" name="firstname" required>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Middle Name</label>
                                            <input type="text" class="form-control" name="middlename" required>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Last Name</label>
                                            <input type="text" class="form-control" name="lastname" required>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Birthday</label>
                                            <input type="date" class="form-control" name="birthday" required>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Gender</label>
                                            <select class="form-control" name="gender" required>
                                                <option value="Male">Male</option>
                                                <option value="Female">Female</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Phone</label>
                                            <input type="text" class="form-control" name="phone" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label>Marital Status:</label>
                                            <select name="marital_status" class="form-control" required>
                                                <option value="Single">Single</option>
                                                <option value="Married">Married</option>
                                                <option value="Divorced">Divorced</option>
                                            </select>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Email</label>
                                            <input type="email" class="form-control" name="email" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label>Qualification:</label>
                                            <input type="text" name="qualification" class="form-control" required>
                                        </div>


                                        <div class="col-md-12 mb-3">
                                            <label class="form-label">Address</label>
                                            <textarea class="form-control" name="address" rows="2" required></textarea>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Select Role</label>
                                            <select name="role_id" class="form-control" required>
                                                <option value="">Select Role</option>
                                                <?php foreach ($roleResult as $role) { ?>
                                                    <option value="<?= $role['id'] ?>"><?= $role['name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Select Department</label>
                                            <select name="department_id" class="form-control" required>
                                                <option value="">Select Department</option>
                                                <?php foreach ($deptResult as $dept) { ?>
                                                    <option value="<?= $dept['id'] ?>"><?= $dept['name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Salary</label>
                                            <input type="number" class="form-control" name="salary" required>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Hire Date</label>
                                            <input type="date" class="form-control" name="hire_date" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label>Account Holder Name:</label>
                                            <input type="text" name="account_holder_name" class="form-control" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label>Account Number:</label>
                                            <input type="text" name="account_number" class="form-control" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label>Bank Name:</label>
                                            <input type="text" name="bank_name" class="form-control" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label>Passport Photo:</label>
                                            <input type="file" name="passport" class="form-control" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label>Upload Document:</label>
                                            <input type="file" name="document" class="form-control" required>
                                        </div>


                                        <div class="col-md-6 mb-3 mt-4">
                                            <label class="form-label">Status</label>
                                            <input type="checkbox" name="status" value="1"> Active
                                        </div>

                                        <div class="col-md-12">
                                            <button type="submit" class="btn btn-success mt-3">Save Staff</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Staff List -->
                <div class="col-md-12 mt-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0">Staff List</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="bg-secondary text-white">
                                        <tr>
                                            <th>#</th>
                                            <th>First Name</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Salary</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($staffResult as $index => $staff) { ?>
                                            <tr>
                                                <td><?= $index + 1 ?></td>
                                                <td><?= htmlspecialchars($staff['firstname']) ?></td>
                                                <td><?= htmlspecialchars($staff['email']) ?></td>
                                                <td><?= htmlspecialchars($staff['role_name']) ?></td>
                                                <td>₦<?= number_format($staff['salary'], 2) ?></td>
                                                <td>
                                                    <!-- Edit Button -->
                                                    <a href="edit_staff.php?id=<?= $staff['staff_id'] ?>" 
                                                       class="btn btn-warning btn-sm rounded-circle" 
                                                       title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>

                                                    <!-- Delete Button -->
                                                    <a href="delete_staff.php?id=<?= $staff['staff_id'] ?>" 
                                                       class="btn btn-danger btn-sm rounded-circle" 
                                                       title="Delete"
                                                       onclick="return confirm('Are you sure you want to delete this staff?');">
                                                        <i class="fas fa-trash"></i>
                                                    </a>

                                                    <!-- Download Button -->
                                                    <a href="download_staff.php?id=<?= $staff['staff_id'] ?>" 
                                                       class="btn btn-primary btn-sm rounded-circle" 
                                                       title="Download">
                                                        <i class="fas fa-download"></i>
                                                    </a>
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
    </div>  
</div>

  
        
        

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