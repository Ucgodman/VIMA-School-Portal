<?php
session_start();
$pageTitle = "Add Question";

// Include necessary files
include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../includes/admin_header.php";

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

        <!-- set_exam.php -->
        <div class="container mt-4">
          <!-- Set Exam Form -->
          <div class="card shadow mb-4">
            <div class="card-header bg-primary text-white">
              <h5 class="mb-0">Set New Exam</h5>
            </div>
            <div class="card-body">
              <form action="save_exam.php" method="POST" id="examForm">
                <div class="row mb-3">
                  <div class="col-md-4">
                    <label for="class_id" class="form-label">Class</label>
                    <select name="class_id" id="class_id" class="form-select" required>
                      <option value="">Select Class</option>
                         <?php
                          $classes = $pdo->query("SELECT class_id, class_name FROM classes");
                            foreach ($classes as $row) {
                             echo "<option value='{$row['class_id']}'>{$row['class_name']}</option>";
                          }
                      ?>
                    </select>
                  </div>
                  <div class="col-md-4">
                    <label for="section_id" class="form-label">Section</label>
                    <select name="section_id" id="section_id" class="form-select" required>
                      <option value="">Select Section</option>
                    </select>
                  </div>
                  <div class="col-md-4">
                    <label for="session_id" class="form-label">Session</label>
                    <select name="session_id" id="session_id" class="form-select" required>
                      <option value="">Select Session</option>
                         <?php
                        $sessions = $pdo->query("SELECT session_id, session_year FROM sessions WHERE is_active = 1");
                        foreach ($sessions as $row) {
                          echo "<option value='{$row['session_id']}'>{$row['session_year']}</option>";
                        }
                      ?>
                    </select>
                  </div>
                </div>

                <div class="row mb-3">
                  <div class="col-md-6">
                    <label for="subject_id" class="form-label">Subject</label>
                    <select name="subject_id" id="subject_id" class="form-select" required>
                      <option value="">Select Subject</option>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label for="staff_id" class="form-label">Teacher</label>
                    <select name="staff_id" id="staff_id" class="form-select" required>
                      <option value="">Select Teacher</option>
                    </select>
                  </div>
                </div>

                <div class="mb-3">
                  <label for="title" class="form-label">Exam Title</label>
                  <input type="text" name="title" id="title" class="form-control" required>
                </div>

                <div class="row mb-3">
                  <div class="col-md-4">
                    <label for="total_marks" class="form-label">Total Marks</label>
                    <input type="number" name="total_marks" id="total_marks" class="form-control" required>
                  </div>
                  <div class="col-md-4">
                    <label for="pass_marks" class="form-label">Pass Marks</label>
                    <input type="number" name="pass_marks" id="pass_marks" class="form-control" required>
                  </div>
                  <div class="col-md-4">
                    <label for="duration_minutes" class="form-label">Duration (Minutes)</label>
                    <input type="number" name="duration_minutes" id="duration_minutes" class="form-control" required>
                  </div>
                </div>

                <div class="row mb-4">
                  <div class="col-md-6">
                    <label for="start_time" class="form-label">Start Time</label>
                    <input type="datetime-local" name="start_time" id="start_time" class="form-control" required>
                  </div>
                  <div class="col-md-6">
                    <label for="end_time" class="form-label">End Time</label>
                    <input type="datetime-local" name="end_time" id="end_time" class="form-control" required>
                  </div>
                </div>

                <div class="d-grid">
                  <button type="submit" class="btn btn-success">Save Exam</button>
                </div>
              </form>
            </div>
          </div>

          <!-- Exam List Table -->
          <div class="card shadow">
            <div class="card-header bg-dark text-white">
              <h5 class="mb-0">List of Exams</h5>
            </div>
            <div class="card-body">
              <table class="table table-bordered table-striped">
                <thead class="table-dark">
                  <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Class</th>
                    <th>Section</th>
                    <th>Subject</th>
                    <th>Teacher</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                 $stmt = $pdo->query("SELECT e.*, 
                            c.class_name, 
                            s.name AS section_name, 
                            sb.name AS subject_name,
                            st.firstname, 
                            st.lastname 
                     FROM exams e
                     JOIN classes c ON c.class_id = e.class_id
                     JOIN sections s ON s.section_id = e.section_id
                     JOIN subjects sb ON sb.subject_id = e.subject_id
                     JOIN staff st ON st.id = e.staff_id");

                  $count = 1;
                  foreach ($stmt as $exam) {
                    echo "<tr>
                      <td>{$count}</td>
                      <td>{$exam['title']}</td>
                      <td>{$exam['class_name']}</td>
                      <td>{$exam['section_name']}</td>
                      <td>{$exam['subject_name']}</td>
                      <td>{$exam['firstname']} {$exam['lastname']}</td>
                      <td>{$exam['start_time']}</td>
                      <td>{$exam['end_time']}</td>
                      <td>
                        <a href='edit_exam.php?id={$exam['exam_id']}' class='btn btn-sm btn-warning'>Edit</a>
                        <a href='delete_exam.php?id={$exam['exam_id']}' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure?\")'>Delete</a>
                      </td>
                    </tr>";
                    $count++;
                  }
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

    </div>
</div>

        <!-- AJAX Script -->
        <script src="../assets/js/core/jquery-3.7.1.min.js"></script> 
        <script>
          $('#class_id').change(function() {
              let classId = $(this).val();
              if (classId) {
                  // Load Sections
                  $.post('get_sections_exam.php', { class_id: classId }, function(data) {
                      $('#section_id').html(data);
                  });

                  // Load Subjects
                  $.post('get_subjects_exam.php', { class_id: classId }, function(data) {
                      $('#subject_id').html(data);
                  });

                    // Load Staff
                  $.post('get_staff_exam.php', { class_id: classId }, function(data) {
                      $('#staff_id').html(data);
                  });

                                  
              } else {
                  // Reset dropdowns if no class is selected
                  $('#section_id').html('<option value="">Select Section</option>');
                  $('#subject_id').html('<option value="">Select Subject</option>');
                  $('#staff_id').html('<option value="">Select Teacher</option>');
              }
          });
            
        </script>

<script src="../assets/js/core/bootstrap.bundle.min.js"></script>

<script src="../assets/js/core/popper.min.js"></script>
<script src="../assets/js/core/bootstrap.min.js"></script>

<!-- Kaiadmin JS -->
<script src="../assets/js/kaiadmin.min.js"></script>
<!-- jQuery Scrollbar -->
<script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
<script src="../assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js"></script>

<!-- Bootstrap Notify -->
<script src="../assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js"></script>

<!-- Sweet Alert -->
<script src="../assets/js/plugin/sweetalert/sweetalert.min.js"></script>

              
    </body>
</html>