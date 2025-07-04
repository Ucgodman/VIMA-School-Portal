<?php

session_start(); // Ensure session is started if not already
$pageTitle = "Staff Dashboard"; // Change this variable on each page as needed

include_once __DIR__ . "/../functions/helper_functions.php"; 
include_once __DIR__ . "/../includes/admin_header.php";
include_once __DIR__ . "/../includes/auth_check.php";
include_once __DIR__ . "/../functions/permission.php"; 


if (isset($_SESSION['success_message'])) {
    echo "<div style='padding: 10px; background: green; color: white; text-align: center;'>" . $_SESSION['success_message'] . "</div>";
    unset($_SESSION['success_message']); // Remove message after displaying
}

if (isset($_SESSION['error_message'])) {
    echo "<div style='padding: 10px; background: red; color: white; text-align: center;'>" . $_SESSION['error_message'] . "</div>";
    unset($_SESSION['error_message']); // Remove message after displaying
}


// Fetch user role counts
$student_count = fetch_one("SELECT COUNT(*) as count FROM students")['count'];
$teacher_count = fetch_one("SELECT COUNT(*) as count FROM staff WHERE role_id = 2")['count'];
$parent_count = fetch_one("SELECT COUNT(*) as count FROM users WHERE role_id = 4")['count'];
$staff_count = fetch_one("SELECT COUNT(*) as count FROM staff ")['count'];
$attendance_count = fetch_one("SELECT COUNT(*) as count FROM attendance WHERE LOWER(status) = 'present'")['count'];
$assignment_count = fetch_one("SELECT COUNT(*) as count FROM assignments")['count'];
$income_total = fetch_one("SELECT SUM(amount) as total FROM income")['total'] ?? 0;
$expense_total = fetch_one("SELECT SUM(amount) as total FROM expenses")['total'] ?? 0;
$daily_fee_payment = fetch_one("SELECT SUM(amount) as total FROM income WHERE DATE(date_received) = CURDATE()")['total'] ?? 0;

// Fetch recently added teachers and students
$recent_teachers = fetch_all("SELECT firstname, middlename,lastname, phone, created_at FROM staff WHERE role_id = 2 ORDER BY created_at DESC LIMIT 5");
$recent_students = fetch_all("SELECT firstname, middlename,lastname, phone, created_at FROM students ORDER BY created_at DESC LIMIT 5");
?>

    <div class="wrapper">
      <!-- Sidebar -->
     <?php include_once __DIR__ . "/../includes/sidebar.php";?>
      <!-- End Sidebar -->

      <div class="main-panel">
            <div class="main-header">
                <div class="main-header-logo">
                    <!-- Logo Header -->
                    <div class="logo-header" data-background-color="dark">
                    <a href="index.html" class="logo">
                        <img
                        src="../assets/img/kaiadmin/logo_light.svg"
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
            <?php include_once __DIR__ . "/../includes/navbar.php"; ?>
            <!-- End Navbar -->
            </div>

            <div class="container">
            <div class="page-inner">
            
                <!-- Statistics Section -->
                <div class="row">

                    <?php
                        $stats = [
                            ["Students", $student_count, "fas fa-user-graduate", "icon-primary"],
                            ["Teachers", $teacher_count, "fas fa-chalkboard-teacher", "icon-info"],
                            ["Parents", $parent_count, "fas fa-users", "icon-success"],
                            ["Staff", $staff_count, "fas fa-user-shield", "icon-secondary"],
                            ["Attendance", $attendance_count, "fas fa-calendar-check", "icon-warning"],
                            ["Assignments", $assignment_count, "fas fa-tasks", "icon-danger"],
                            ["Income", "₦" . number_format($income_total, 2), "fas fa-dollar-sign", "icon-success"],
                            ["Expenses", "₦" . number_format($expense_total, 2), "fas fa-money-bill-wave", "icon-danger"],
                        ];

                        foreach ($stats as $stat) { ?>
                            <div class="col-sm-6 col-md-3">
                                <div class="card card-stats card-round">
                                    <div class="card-body">
                                        <div class="row align-items-center">
                                            <div class="col-icon">
                                                <div class="icon-big text-center <?= $stat[3] ?> bubble-shadow-small">
                                                    <i class="<?= $stat[2] ?>"></i>
                                                </div>
                                            </div>
                                            <div class="col col-stats ms-3 ms-sm-0">
                                                <div class="numbers">
                                                    <p class="card-category"><?= $stat[0] ?></p>
                                                    <h4 class="card-title"><?= $stat[1] ?></h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                </div> <!-- Properly closing the statistics row -->
            
                
                    <!-- Charts Section -->            
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card card-round h-30">
                                <div class="card-header">
                                    <h4 class="card-title">User Distribution Chart</h4>
                                </div>
                                <div class="card-body d-flex align-items-center">
                                    <canvas id="userChart"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card card-round h-30">
                                <div class="card-header">
                                    <h4 class="card-title">Daily Fee Payment</h4>
                                </div>
                                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                                    <p class="card-category">Total Fees Collected Today</p>
                                    <h4 class="card-title">₦<?= number_format($daily_fee_payment, 2) ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                
                <!-- Recently Added Users -->
                <div class="row">
                    <?php 
                    $recent_data = [
                        ["Recently Added Teachers", $recent_teachers],
                        ["Recently Added Students", $recent_students]
                    ];
                    foreach ($recent_data as $section) { ?>
                        <div class="col-md-6">
                            <div class="card card-round">
                                <div class="card-header">
                                    <h4 class="card-title"><?= $section[0] ?></h4>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Phone</th>
                                                    <th>Added On</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($section[1] as $user): ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($user['firstname']); ?></td>
                                                        <td><?= htmlspecialchars($user['phone']); ?></td>
                                                        <td><?= date("d M, Y", strtotime($user['created_at'])); ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div> <!-- Closing the recently added users row -->

                </div>
            </div>
            </div>

                <!-- Chart.js for User Distribution -->
                 <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    var ctx = document.getElementById('userChart').getContext('2d');
                    var userChart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: ["Students", "Teachers", "Parents", "Staff"],
                            datasets: [{
                                label: "User Count",
                                data: [<?= $student_count ?>, <?= $teacher_count ?>, <?= $parent_count ?>, <?= $staff_count ?>],
                                backgroundColor: ['#4CAF50', '#2196F3', '#FF9800', '#9C27B0']
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false
                        }
                    });
                });
            </script>
      </div>
    </div>
 
    <!--   Core JS Files   -->
    <script src="../assets/js/core/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   

  
</body>
</html>