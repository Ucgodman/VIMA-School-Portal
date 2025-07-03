<?php
session_start();
$pageTitle = "Add Question";

// Include necessary files
include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../includes/admin_header.php";

// Fetch all exams with their related class, section, and subject
$stmt = $pdo->query("SELECT exams.exam_id, exams.title, classes.class_name, sections.name AS section_name, subjects.name AS subject_name
                     FROM exams
                     JOIN classes ON exams.class_id = classes.class_id
                     JOIN sections ON exams.section_id = sections.section_id
                     JOIN subjects ON exams.subject_id = subjects.subject_id
                     ORDER BY exams.created_at DESC");
$exams = $stmt->fetchAll(PDO::FETCH_ASSOC);

// You may want to handle session messages
$success = $_GET['success'] ?? '';
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

        <div class="container mt-4">
          <div class="row">

            <?php if ($success == 'single'): ?>
              <div class="alert alert-success">Single question added successfully!</div>
            <?php elseif ($success == 'batch'): ?>
              <div class="alert alert-success">Batch questions uploaded successfully!</div>
            <?php endif; ?>

            <!-- ========== Batch Upload Card ========== -->
            <div class="col-md-6">
              <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">Batch Upload (Word or PDF)</div>
                <div class="card-body">
                  <form action="question_backend.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="upload_batch" value="1">

                    <!-- Exam Dropdown -->
                    <div class="mb-3">
                      <label for="exam_id" class="form-label">Select Exam</label>
                      <select name="exam_id" class="form-control" required>
                        <option value="">-- Choose Exam --</option>
                        <?php foreach ($exams as $exam): ?>
                          <option value="<?= $exam['exam_id'] ?>">
                            <?= $exam['title'] ?> (<?= $exam['class_name'] ?> - <?= $exam['section_name'] ?> - <?= $exam['subject_name'] ?>)
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>

                    <div class="mb-3">
                      <label for="question_file" class="form-label">Upload File (PDF/DOCX)</label>
                      <input type="file" name="question_file" class="form-control" accept=".pdf,.doc,.docx" required>
                    </div>

                    <button type="submit" class="btn btn-dark w-100">Upload Questions</button>
                  </form>
                </div>
              </div>
            </div>

            <!-- ========== Single Question Entry Card ========== -->
            <div class="col-md-6">
              <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">Single Question Entry</div>
                <div class="card-body">
                  <form action="question_backend.php" method="POST">
                    <input type="hidden" name="save_single" value="1">

                    <!-- Exam Dropdown -->
                    <div class="mb-3">
                      <label for="exam_id" class="form-label">Select Exam</label>
                      <select name="exam_id" id="exam_id" class="form-control" required>
                        <option value="">-- Choose Exam --</option>
                        <?php foreach ($exams as $exam): ?>
                          <option value="<?= $exam['exam_id'] ?>">
                            <?= $exam['title'] ?> (<?= $exam['class_name'] ?> - <?= $exam['section_name'] ?> - <?= $exam['subject_name'] ?>)
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>

                    <div class="mb-3">
                      <label for="question" class="form-label">Question</label>
                      <textarea name="question" class="form-control" rows="4" required></textarea>
                    </div>

                    <div class="mb-3">
                      <label for="question_type" class="form-label">Question Type</label>
                      <select name="question_type" class="form-control" required>
                        <option value="objective">Objective</option>
                        <option value="theory">Theory</option>
                        <option value="true_false">True/False</option>
                      </select>
                    </div>

                    <!-- Dynamic Options -->
                    <div id="option-container">
                      <label class="form-label">Options</label>
                      <div class="option-group mb-2 d-flex gap-2">
                        <input type="text" name="option_letters[]" class="form-control w-25" value="A" readonly>
                        <input type="text" name="option_texts[]" class="form-control w-75" placeholder="Option text" required>
                        <input type="radio" name="is_correct" value="A" required>
                      </div>

                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary mb-3" onclick="addOption()">Add Option</button>

                    <div class="mb-3">
                      <label for="marks" class="form-label">Marks</label>
                      <input type="number" name="marks" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-success w-100">Save Question</button>
                  </form>
                </div>
              </div>
            </div>


          </div>
        </div>
    </div>
</div>

<script src="../assets/js/core/jquery-3.7.1.min.js"></script> 
<script src="../assets/js/core/bootstrap.bundle.min.js"></script>
<script src="../summernote/summernote-bs5.min.js"></script>
<script src="../assets/js/core/popper.min.js"></script>
<script src="../assets/js/core/bootstrap.min.js"></script>
<script src="../assets/js/kaiadmin.min.js"></script>
<script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
<script src="../assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js"></script>
<script src="../assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js"></script>
<script src="../assets/js/plugin/sweetalert/sweetalert.min.js"></script>

<script>
  $(document).ready(function () {
    $('#summernote').summernote({ height: 150 });
  });

  function loadSectionsAndSubjects(classId, sectionId, subjectId) {
    $.post('fetch_sections.php', { class_id: classId }, function(data) {
      $(sectionId).html(data);
    });
    $.post('fetch_subjects.php', { class_id: classId }, function(data) {
      $(subjectId).html(data);
    });
  }

  $('#batch_class').change(function() {
    loadSectionsAndSubjects($(this).val(), '#batch_section', '#batch_subject');
  });

  $('#single_class').change(function() {
    loadSectionsAndSubjects($(this).val(), '#single_section', '#single_subject');
  });

  function addOption() {
  const optionCount = document.querySelectorAll('[name="option_letters[]"]').length;
  const nextLetter = String.fromCharCode(65 + optionCount); // 65 = 'A'

  const optionGroup = `
    <div class="option-group mb-2 d-flex gap-2">
      <input type="text" name="option_letters[]" class="form-control w-25" value="${nextLetter}" readonly>
      <input type="text" name="option_texts[]" class="form-control w-75" placeholder="Option text" required>
      <input type="radio" name="is_correct" value="${nextLetter}" required>
    </div>`;
  $('#option-container').append(optionGroup);
}

</script>

<?php if (isset($_GET['success'])): ?>
    <script>
        const successType = "<?php echo $_GET['success']; ?>";
        let message = "";

        if (successType === "single") {
            message = "Single question added successfully!";
        } else if (successType === "batch") {
            message = "Batch upload successful!";
        }

        if (message) {
            alert(message);
        }
    </script>
<?php endif; ?>

</body>
</html>
