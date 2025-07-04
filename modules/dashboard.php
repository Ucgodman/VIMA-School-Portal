<?php
session_start(); 
$pageTitle = "Dashboard";

include_once __DIR__ . "/../functions/helper_functions.php"; 
include_once __DIR__ . "/../includes/admin_header.php";
include_once __DIR__ . "/../includes/auth_check.php";
include_once __DIR__ . "/../functions/permission.php"; 


if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['type'], ['admin', 'student'])) {
    // If not logged in or not admin/student, redirect
    header("Location: ../login.php");
    exit;
}

// Proceed only if user is student, since the data below is student-specific
if ($_SESSION['user']['type'] === 'student') {
    $student_id = $_SESSION['user']['id'];
}



// Fetch student details including class, section, session
$student = fetch_one("SELECT s.*, c.class_name, sec.name, ses.session_year 
                      FROM students s
                      LEFT JOIN classes c ON s.class_id = c.class_id
                      LEFT JOIN sections sec ON s.section_id = sec.section_id
                      LEFT JOIN sessions ses ON s.session_id = ses.session_id
                      WHERE s.student_id = ?", [$student_id]);

// Fetch attendance count for this student (assuming attendance table has student_id)
$attendance_count = fetch_one("SELECT COUNT(*) as count FROM attendance WHERE student_id = ? AND LOWER(status) = 'present'", [$student_id])['count'] ?? 0;

// Fetch assignment count for this student (assuming assignment submissions table)
$assignment_count = fetch_one("SELECT COUNT(*) as count FROM assignments WHERE class_id = ? AND section_id = ?", [
    $student['class_id'], $student['section_id']
])['count'] ?? 0;


// Fetch expenses count for this student (assuming expenses linked by student_id)
$expenses_count = fetch_one("SELECT COUNT(*) as count FROM expenses WHERE student_id = ?", [$student_id])['count'] ?? 0;

// Total staff count
$staff_count = fetch_one("SELECT COUNT(*) as count FROM staff")['count'] ?? 0;

// Total teacher count (role_id = 2)
$teacher_count = fetch_one("SELECT COUNT(*) as count FROM staff WHERE role_id = 2")['count'] ?? 0;

// Today's timetable for the student (class_id, section_id, day = current day)
$today = date('l'); // Full weekday name, e.g. Monday
$timetable = fetch_all("SELECT tt.start_time, tt.end_time, sub.name, st.firstname AS teacher_fname, st.lastname AS teacher_lname
                       FROM timetable tt
                       LEFT JOIN subjects sub ON tt.subject_id = sub.subject_id
                       LEFT JOIN staff st ON tt.teacher_id = st.id
                       WHERE tt.class_id = ? AND tt.section_id = ? AND tt.day = ?
                       ORDER BY tt.start_time ASC", 
                       [$student['class_id'], $student['section_id'], $today]);

// Fetch notices/announcements (latest 5)
$notices = fetch_all("SELECT title, content, created_at FROM noticeboard ORDER BY created_at DESC LIMIT 5");
?>

<div class="wrapper">
    <!-- Sidebar -->
    <?php include_once __DIR__ . "/../includes/sidebar.php";?>
    <!-- End Sidebar -->

    <div class="main-panel">
        <div class="main-header">
            <div class="main-header-logo">
                <div class="logo-header" data-background-color="dark">
                    <a href="index.html" class="logo">
                        <img src="../assets/img/kaiadmin/logo_light.svg" alt="navbar brand" class="navbar-brand" height="20" />
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
            <div class="page-inner">
                <div class="row">

                    <?php
                        $stats = [
                            ["Class", $student['class_name'], "fas fa-school", "icon-primary"],
                            ["Section", $student['name'], "fas fa-layer-group", "icon-success"],
                            ["Session", $student['session_year'], "fas fa-calendar-alt", "icon-warning"],
                            ["Teachers", $teacher_count, "fas fa-chalkboard-teacher", "icon-info"],
                            ["Staff", $staff_count, "fas fa-user-shield", "icon-secondary"],
                            ["Attendance", $attendance_count, "fas fa-calendar-check", "icon-warning"],
                            ["Assignments", $assignment_count, "fas fa-tasks", "icon-danger"],
                            ["Expenses", "₦" . number_format($expenses_count, 2), "fas fa-money-bill-wave", "icon-danger"],
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

                <!-- Today's Timetable -->
                <div class="card card-round mb-3">
                    <div class="card-header">
                        <h4 class="card-title">Today's Timetable (<?= $today ?>)</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($timetable): ?>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Start Time</th>
                                        <th>End Time</th>
                                        <th>Subject</th>
                                        <th>Teacher</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($timetable as $period): ?>
                                        <tr>
                                            <td><?= htmlspecialchars(date("h:i A", strtotime($period['start_time']))) ?></td>
                                            <td><?= htmlspecialchars(date("h:i A", strtotime($period['end_time']))) ?></td>
                                            <td><?= htmlspecialchars($period['name']) ?></td>
                                            <td><?= htmlspecialchars($period['teacher_fname'] . ' ' . $period['teacher_lname']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p>No timetable scheduled for today.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Notices / Announcements -->
                <div class="card card-round">
                    <div class="card-header">
                        <h4 class="card-title">Notices & Announcements</h4>
                    </div>
                    <div class="card-body">
                        <?php if ($notices): ?>
                            <ul class="list-group">
                                <?php foreach ($notices as $notice): ?>
                                    <li class="list-group-item">
                                        <h5><?= htmlspecialchars($notice['title']) ?></h5>
                                        <p><?= nl2br(htmlspecialchars($notice['content'])) ?></p>
                                        <small><em><?= date("d M, Y", strtotime($notice['created_at'])) ?></em></small>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p>No announcements available.</p>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- JS Includes (keep as in your template) -->
<script src="../assets/js/core/bootstrap.bundle.min.js"></script>
<script src="../assets/js/core/jquery-3.7.1.min.js"></script>


</body>
</html>
