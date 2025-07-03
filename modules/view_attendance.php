<?php
session_start ();
$pageTitle = "Daily Attendance";
include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../includes/admin_header.php";

// Fetch classes for dropdown
$classes = $pdo->query("SELECT * FROM classes ORDER BY class_name ASC")->fetchAll();
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
            

            <div class="row">
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-header bg-secondary text-white">Manage Attendance</div>
                        <div class="card-body">
                            <div class="form-group">
                                <label>Select Class</label>
                                <select class="form-control" id="class_id" onchange="getSections(this.value)">
                                    <option value="">Select</option>
                                    <?php foreach ($classes as $class): ?>
                                        <option value="<?php echo $class['class_id']; ?>"><?php echo $class['class_name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Select Section</label>
                                <select class="form-control" id="section_id">
                                    <option value="">Select Class First</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Date</label>
                                <input type="date" id="date" class="form-control">
                            </div>

                            <button type="button" class="btn btn-primary btn-block" id="getAttendance">Get Attendance</button>
                        </div>
                    </div>
                </div>
            </div>

            <br>
            <div id="attendanceData"></div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#getAttendance').on('click', function() {
        var class_id = $('#class_id').val();
        var section_id = $('#section_id').val();
        var date = $('#date').val();

        if (class_id == "" || section_id == "" || date == "") {
            alert('Please select class, section, and date');
            return;
        }

        $.ajax({
            url: 'fetch_attendance.php',
            type: 'POST',
            data: { class_id: class_id, section_id: section_id, date: date },
            success: function(response) {
                $('#attendanceData').html(response);
            }
        });
    });
});

function getSections(class_id) {
    $.ajax({
        url: 'fetch_sections.php',
        type: 'POST',
        data: { class_id: class_id },
        success: function(response) {
            $('#section_id').html(response);
        }
    });
}
</script>

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

