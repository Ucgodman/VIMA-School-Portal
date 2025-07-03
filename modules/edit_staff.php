<?php
session_start();
$pageTitle = "Edit Staff";

// Include database connection
include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../functions/helper_functions.php";
include_once __DIR__ . "/../includes/admin_header.php";

// Check if staff_id is set
if (!isset($_GET['id'])) {
    die("Invalid request.");
}

$staff_id = $_GET['id'];

try {
    // Fetch staff details
    $stmt = $pdo->prepare("SELECT * FROM staff WHERE staff_id = ?");
    $stmt->execute([$staff_id]);
    $staff = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$staff) {
        die("Staff not found.");
    }

    // Fetch roles
    $roleQuery = "SELECT id, name FROM roles";
    $roleResult = $pdo->query($roleQuery)->fetchAll(PDO::FETCH_ASSOC);

    // Fetch departments
    $deptQuery = "SELECT department_id, name FROM departments";
    $deptResult = $pdo->query($deptQuery)->fetchAll(PDO::FETCH_ASSOC);
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
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h2 class="mb-0">Edit Staff</h2>
                </div>
                <div class="card-body">
                    <form action="update_staff.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="staff_id" value="<?= htmlspecialchars($staff['staff_id']) ?>">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">First Name</label>
                                <input type="text" class="form-control" name="firstname" value="<?= htmlspecialchars($staff['firstname']) ?>">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Middle Name</label>
                                <input type="text" class="form-control" name="middlename" value="<?= htmlspecialchars($staff['middlename']) ?>">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Last Name</label>
                                <input type="text" class="form-control" name="lastname" value="<?= htmlspecialchars($staff['lastname']) ?>">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Birthday</label>
                                <input type="date" class="form-control" name="birthday" value="<?= htmlspecialchars($staff['birthday']) ?>">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Gender</label>
                                <select class="form-control" name="gender">
                                    <option value="Male" <?= $staff['gender'] == "Male" ? "selected" : "" ?>>Male</option>
                                    <option value="Female" <?= $staff['gender'] == "Female" ? "selected" : "" ?>>Female</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Marital Status</label>
                                <select class="form-control" name="marital_status">
                                    <option value="Single" <?= isset($staff['marital_status']) && $staff['marital_status'] == "Single" ? "selected" : "" ?>>Single</option>
                                    <option value="Married" <?= isset($staff['marital_status']) && $staff['marital_status'] == "Married" ? "selected" : "" ?>>Married</option>
                                    <option value="Divorced" <?= isset($staff['marital_status']) && $staff['marital_status'] == "Divorced" ? "selected" : "" ?>>Divorced</option>
                                    <option value="Widowed" <?= isset($staff['marital_status']) && $staff['marital_status'] == "Widowed" ? "selected" : "" ?>>Widowed</option>
                                </select>

                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" class="form-control" name="phone" value="<?= htmlspecialchars($staff['phone']) ?>">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($staff['email']) ?>">
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Address</label>
                                <textarea class="form-control" name="address" rows="2" required><?= htmlspecialchars($staff['address']) ?></textarea>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Qualification</label>
                                <input type="text" class="form-control" name="qualification" value="<?= htmlspecialchars($staff['qualification']) ?>" >
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Bank Name</label>
                                <input type="text" class="form-control" name="bank_name" value="<?= htmlspecialchars($staff['bank_name']) ?>">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Account Holder Name</label>
                                <input type="text" class="form-control" name="account_holder" value="<?= htmlspecialchars($staff['account_holder_name']) ?>" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Account Number</label>
                                <input type="text" class="form-control" name="account_number" value="<?= htmlspecialchars($staff['account_number']) ?>" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Role</label>
                                <select name="role_id" class="form-control" required>
                                    <?php foreach ($roleResult as $role) { ?>
                                        <option value="<?= $role['id'] ?>" <?= ($staff['role_id'] == $role['id']) ? 'selected' : '' ?>><?= $role['name'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Department</label>
                                <select name="department_id" class="form-control" required>
                                    <?php foreach ($deptResult as $dept) { ?>
                                        <option value="<?= $dept['department_id'] ?>" <?= ($staff['department_id'] == $dept['department_id']) ? 'selected' : '' ?>><?= $dept['name'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Salary</label>
                                <input type="number" class="form-control" name="salary" value="<?= htmlspecialchars($staff['salary']) ?>" >
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Hire Date</label>
                                <input type="date" class="form-control" name="hire_date" value="<?= htmlspecialchars($staff['hire_date']) ?>" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Upload Passport</label>
                                <input type="file" class="form-control" name="passport">
                                <small>Current: <?= htmlspecialchars($staff['passport']) ?></small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Upload Document</label>
                                <input type="file" class="form-control" name="document">
                                <small>Current: <?= htmlspecialchars($staff['document']) ?></small>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Status</label>
                                <input type="checkbox" name="status" value="1" <?= $staff['status'] == 1 ? "checked" : "" ?>> Active
                            </div>

                            <div class="col-md-12">
                                <button type="submit" class="btn btn-success mt-3">Update Staff</button>
                                <a href="staff.php" class="btn btn-secondary mt-3">Cancel</a>
                            </div>
                        </div>
                    </form>
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
