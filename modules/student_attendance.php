<?php
session_start ();
$pageTitle = "Manage Attendance";

// Include necessary files
include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../includes/admin_header.php";

// Fetch classes for dropdown
$classes = $pdo->query("SELECT * FROM classes ORDER BY class_name ASC")->fetchAll();

// Fetch sessions for dropdown
$sessions = $pdo->query("SELECT * FROM sessions ORDER BY session_year ASC")->fetchAll();
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
            <h3 class="mb-3">Manage Attendance</h3>

            <div class="row">
                <!-- First Card: Filters -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5>Select Class, Section & Session</h5>
                            <div class="form-group">
                                <label for="class_id">Class</label>
                                <select id="class_id" class="form-control">
                                    <option value="">Select Class</option>
                                    <?php foreach ($classes as $class): ?>
                                        <option value="<?= $class['class_id'] ?>"><?= $class['class_name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group mt-2">
                                <label for="section_id">Section</label>
                                <select id="section_id" class="form-control">
                                    <option value="">Select Section</option>
                                </select>
                            </div>
                            <div class="form-group mt-2">
                                <label for="session_id">Session</label>
                                <select id="session_id" class="form-control">
                                    <option value="">Select Session</option>
                                    <?php foreach ($sessions as $session): ?>
                                        <option value="<?= $session['session_id'] ?>"><?= $session['session_year'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group mt-3">
                                <label for="attendance_date">Attendance Date</label>
                                <input type="date" id="attendance_date" class="form-control" value="<?= date('Y-m-d') ?>">
                            </div>
                            <button class="btn btn-primary w-100 mt-3" id="getStudents">Get Students</button>
                        </div>
                    </div>
                </div>

                <!-- Second Card: Student List -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <h5>Attendance Summary</h5>
                            <p id="summary">Please select a class, section, session, and date.</p>

                            <div class="table-responsive">
                                <input type="text" id="search" class="form-control mb-2" placeholder="Search student...">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Admission No</th>
                                            <th>Name</th>
                                            <th>Gender</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="studentList">
                                        <tr><td colspan="5" class="text-center">No students found.</td></tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <button class="btn btn-success w-100 mt-3" id="saveAttendance">Save Attendance</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script src="../assets/js/core/jquery-3.7.1.min.js"></script>

<script>
$(document).ready(function() {
    // Load sections based on class
    $('#class_id').change(function() {
        let classId = $(this).val();
        $('#section_id').html('<option value="">Loading...</option>');

        $.post('backend_attendance.php', { action: 'get_sections', class_id: classId }, function(response) {
            let data = JSON.parse(response);
            let options = '<option value="">Select Section</option>';
            if(data.length > 0) {
                data.forEach(section => {
                    options += `<option value="${section.section_id}">${section.name}</option>`;
                });
            }
            $('#section_id').html(options);
        });
    });

    // Fetch students
    $('#getStudents').click(function() {
        let classId = $('#class_id').val();
        let sectionId = $('#section_id').val();
        let sessionId = $('#session_id').val();
        let date = $('#attendance_date').val();

        if (!classId || !sectionId || !sessionId || !date) {
            alert('Please select class, section, session, and date.');
            return;
        }

        $.post('backend_attendance.php', 
            { action: 'get_students', class_id: classId, section_id: sectionId, session_id: sessionId, date: date }, 
            function(response) {
                let data = JSON.parse(response);
                $('#studentList').empty();
                if (data.length > 0) {
                    $.each(data, function(index, student) {
                        $('#studentList').append(`
                            <tr>
                                <td>${index + 1}</td>
                                <td>${student.admission_no}</td>
                                <td>${student.firstname} ${student.middlename} ${student.lastname}</td>
                                <td>${student.gender}</td>
                                <td>
                                     <select class="status" data-student_id="${student.student_id}">
                                        <option value="Undefined">Undefined</option>
                                        <option value="Present">Present</option>
                                        <option value="Absent">Absent</option>
                                        <option value="Holiday">Holiday</option>
                                        <option value="Half Day">Half Day</option>
                                        <option value="Late">Late</option>
                                    </select>
                                </td>
                            </tr>
                        `);
                    });
                } else {
                    $('#studentList').html('<tr><td colspan="5" class="text-center">No students found.</td></tr>');
                }
            });
    });

    // Save attendance
    $('#saveAttendance').click(function() {
        let attendanceData = [];
        $('.status').each(function() {
            let studentId = $(this).data('student_id');
            let status = $(this).val();
            attendanceData.push({ student_id: studentId, status: status });
        });

        $.post('backend_attendance.php', 
            { 
                action: 'save_attendance', 
                attendance: JSON.stringify(attendanceData),
                class_id: $('#class_id').val(), 
                section_id: $('#section_id').val(), 
                session_id: $('#session_id').val(),
                date: $('#attendance_date').val()
            }, 
            function(response) {
                if (response.success) {
                    alert('Attendance saved successfully!');
                } else {
                    alert('Failed to save attendance: ' + response.message);
                }
            }, 'json');
    });
});
</script>

<!-- Core JS Files -->
<script src="../assets/js/core/bootstrap.bundle.min.js"></script>
<script src="../assets/js/core/jquery-3.7.1.min.js"></script>

</body>
</html>
