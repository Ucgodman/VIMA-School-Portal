<?php
$pageTitle = "Manage Questions";
include_once __DIR__ . "/../config/database.php";
include_once __DIR__ . "/../includes/admin_header.php";

// Pagination setup
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Total questions count
$countStmt = $pdo->query("SELECT COUNT(*) FROM questions WHERE is_deleted = 0");
$totalQuestions = $countStmt->fetchColumn();
$totalPages = ceil($totalQuestions / $limit);

// Get active session
$stmt = $pdo->query("SELECT session_id, session_year FROM sessions WHERE is_active = 1 LIMIT 1");
$activeSession = $stmt->fetch(PDO::FETCH_ASSOC);

// Fetch questions
$query = "SELECT q.*, ex.title AS exam_title, c.class_name, s.name, se.name AS section_name, sess.session_year, 
                 st.firstname AS staff_firstname, st.lastname AS staff_lastname, ex.class_id, ex.subject_id, 
                 ex.section_id, ex.session_id, ex.staff_id
                  FROM questions q
                  JOIN exams ex ON q.exam_id = ex.exam_id
                  JOIN classes c ON ex.class_id = c.class_id
                  JOIN subjects s ON ex.subject_id = s.subject_id
                  LEFT JOIN sections se ON ex.section_id = se.section_id
                  LEFT JOIN sessions sess ON ex.session_id = sess.session_id
                  LEFT JOIN staff st ON ex.staff_id = st.id
                  WHERE q.is_deleted = 0
                  ORDER BY q.question_id ASC
                  LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($query);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Include sidebar -->
<div class="wrapper">
<?php include_once __DIR__ . "/../includes/admin_sidebar.php"; ?>
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
      <?php if (isset($_GET['updated']) && $_GET['updated'] == 1): ?>
        <div class="alert alert-success">Question updated successfully!</div>
      <?php endif; ?>
        <div class="card shadow p-3">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                    <h2 class="text-primary mb-2 mb-md-0">Manage Questions</h2>
                <div class="d-flex align-items-center mb-2 mb-md-0">
                    <input type="text" id="searchBox" class="form-control me-2" placeholder="Search class..." onkeyup="searchQuestions()" style="min-width: 250px;">
                        <button class="btn btn-success" onclick="fetchQuestions()">Refresh</button>
                </div>
            </div>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="table-dark">
                          <tr>
                            <th>#</th>
                            <th>Class</th>
                            <th>Question</th>
                            <th>Question Type</th>
                            <th>Marks</th>
                            <th>Actions</th>
                          </tr>
                        </thead>
                        <tbody>
                         <?php foreach($questions as $index => $q): 
                            // Fetch options for this question
                            $optionStmt = $pdo->prepare("SELECT * FROM options WHERE question_id = ?");
                            $optionStmt->execute([$q['question_id']]);
                            $options = $optionStmt->fetchAll(PDO::FETCH_ASSOC);

                            // Fetch all exams with joined data
                            $examStmt = $pdo->query("
                              SELECT ex.exam_id, ex.title, c.class_name, s.name AS subject_name, se.name AS section_name
                              FROM exams ex
                              JOIN classes c ON ex.class_id = c.class_id
                              JOIN subjects s ON ex.subject_id = s.subject_id
                              LEFT JOIN sections se ON ex.section_id = se.section_id
                            ");
                            $exams = $examStmt->fetchAll(PDO::FETCH_ASSOC);


                            // Prepare the question data array
                            $questionData = [
                              'question_id' => $q['question_id'],
                              'exam_id' => $q['exam_id'],
                              'question' => $q['question'],
                              'question_type' => $q['question_type'],
                              'marks' => $q['marks'],
                              'options' => $options
                            ];
                          ?>
                            <tr>
                              <td><?= $offset + $index + 1 ?></td>
                              <td><?= htmlspecialchars($q['class_name']) ?></td>
                              <td><?= htmlspecialchars($q['question']) ?></td>
                              <td><?= htmlspecialchars($q['question_type']) ?></td>
                              <td><?= htmlspecialchars($q['marks']) ?></td>
                              <td>
                                <button 
                                  class="btn btn-sm btn-warning open-edit-btn"
                                  data-question='<?= htmlspecialchars(json_encode($questionData), ENT_QUOTES, "UTF-8") ?>'
                                >
                                  Edit
                                </button>
                                <button class="btn btn-danger btn-sm" onclick="confirmDelete(<?= $q['question_id'] ?>)">Delete</button>

                              </td>
                            </tr>
                          <?php endforeach; ?>

                                
                           
                        </tbody>
                    </table>

                      <!-- Pagination -->
                      <?php if ($totalPages > 1): ?>
                      <nav class="mt-3">
                          <ul class="pagination justify-content-center">
                            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                               <a class="page-link" href="?page=1">First</a>
                            </li>
                            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
                            </li>
                            <?php for ($i = max(1, $page - 1); $i <= min($totalPages, $page + 1); $i++): ?>
                            <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                                <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                            </li>
                            <?php endfor; ?>
                            <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
                            </li>
                            <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                                <a class="page-link" href="?page=<?= $totalPages ?>">Last</a>
                            </li>
                          </ul>
                      </nav>
                      <?php endif; ?>
                </div>
          </div>
    </div>
       <?php include_once __DIR__ . "/../includes/header_footer.php"; ?>
  </div>

</div>

  <!-- Edit Question Modal -->
 <!-- Edit Question Modal -->
<div class="modal fade" id="editQuestionModal" tabindex="-1" aria-labelledby="editQuestionModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <form id="editQuestionForm" action="question_backend.php" method="POST">
      <input type="hidden" name="edit_single" value="1">
      <input type="hidden" name="action" value="update_question">
      <input type="hidden" name="question_id" id="edit_question_id">

      <div class="modal-content">
        <div class="modal-header bg-warning">
          <h5 class="modal-title text-white" id="editQuestionModalLabel">Edit Question</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          
          <!-- Exam Dropdown -->
          <div class="mb-3">
            <label for="edit_exam_id" class="form-label">Select Exam</label>
            <select name="exam_id" id="edit_exam_id" class="form-control" required>
              <option value="">-- Choose Exam --</option>
              <?php foreach ($exams as $exam): ?>
                <option value="<?= $exam['exam_id'] ?>">
                  <?= $exam['title'] ?> (<?= $exam['class_name'] ?> - <?= $exam['section_name'] ?> - <?= $exam['subject_name'] ?>)
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-3">
            <label for="edit_question" class="form-label">Question</label>
            <textarea name="question" id="edit_question" class="form-control" rows="4" required></textarea>
          </div>

          <div class="mb-3">
            <label for="edit_question_type" class="form-label">Question Type</label>
            <select name="question_type" id="edit_question_type" class="form-control" required>
              <option value="objective">Objective</option>
              <option value="theory">Theory</option>
              <option value="true_false">True/False</option>
            </select>
          </div>

          <div id="edit-option-container">
            <!-- Dynamic option fields get injected here -->
          </div>
          <button type="button" class="btn btn-sm btn-outline-warning mb-3" onclick="addEditOption()">Add Option</button>

          <div class="mb-3">
            <label for="edit_marks" class="form-label">Marks</label>
            <input type="number" name="marks" id="edit_marks" class="form-control" required>
          </div>

        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success w-100">Update Question</button>
        </div>
      </div>
    </form>
  </div>
</div>

  <script src="../assets/js/core/jquery-3.7.1.min.js"></script>
  <!-- Core JS Files -->
<script src="../assets/js/core/bootstrap.bundle.min.js"></script>
<script src="../assets/js/core/popper.min.js"></script>
<script src="../assets/js/core/bootstrap.min.js"></script>

<!-- jQuery Scrollbar -->
<script src="../assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js"></script>
<script src="../assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js"></script>

<!-- Bootstrap Notify -->
<script src="../assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js"></script>

<!-- Sweet Alert -->
<script src="../assets/js/plugin/sweetalert/sweetalert.min.js"></script>

<!-- Kaiadmin JS -->
<script src="../assets/js/kaiadmin.min.js"></script>

<script>
// Function to load sections and subjects via AJAX
function loadSectionsAndSubjects(classId, sectionSelector, subjectSelector, selectedSectionId = null, selectedSubjectId = null) {
  if (!classId) {
    $(sectionSelector).html('<option value="">Select Section</option>');
    $(subjectSelector).html('<option value="">Select Subject</option>');
    return;
  }

  $.ajax({
    url: 'loadsectionsandsubjects.php',
    type: 'GET',
    data: { class_id: classId },
    dataType: 'json',
    success: function(response) {
      // Sections
      let sectionOptions = '<option value="">Select Section</option>';
      response.sections.forEach(function(section) {
        const selected = section.section_id == selectedSectionId ? 'selected' : '';
        sectionOptions += `<option value="${section.section_id}" ${selected}>${section.name}</option>`;
      });
      $(sectionSelector).html(sectionOptions);

      // Subjects
      let subjectOptions = '<option value="">Select Subject</option>';
      response.subjects.forEach(function(subject) {
        const selected = subject.subject_id == selectedSubjectId ? 'selected' : '';
        subjectOptions += `<option value="${subject.subject_id}" ${selected}>${subject.name}</option>`;
      });
      $(subjectSelector).html(subjectOptions);
    },
    error: function(xhr, status, error) {
      alert("Error loading sections and subjects: " + error);
    }
  });
}

$('.open-edit-btn').on('click', function() {
  const questionData = JSON.parse($(this).attr('data-question'));
  $('#edit_question_id').val(questionData.question_id);
  $('#edit_exam_id').val(questionData.exam_id);
  $('#edit_question').val(questionData.question);
  $('#edit_question_type').val(questionData.question_type);
  $('#edit_marks').val(questionData.marks);
  $('#edit-option-container').empty();
  questionData.options.forEach(opt => {
    const checked = opt.is_correct ? 'checked' : '';
    $('#edit-option-container').append(`
      <div class="option-group mb-2 d-flex gap-2">
        <input type="text" name="option_letters[]" class="form-control w-25" value="${opt.option_letter}" readonly>
        <input type="text" name="option_texts[]" class="form-control w-75" value="${opt.option_text}" required>
        <input type="radio" name="is_correct" value="${opt.option_letter}" ${checked} required>
      </div>`);
  });
  $('#editQuestionModal').modal('show');
});

function addEditOption() {
  const count = document.querySelectorAll('#edit-option-container [name="option_letters[]"]').length;
  const nextLetter = String.fromCharCode(65 + count);
  $('#edit-option-container').append(`
    <div class="option-group mb-2 d-flex gap-2">
      <input type="text" name="option_letters[]" class="form-control w-25" value="${nextLetter}" readonly>
      <input type="text" name="option_texts[]" class="form-control w-75" placeholder="Option text" required>
      <input type="radio" name="is_correct" value="${nextLetter}" required>
    </div>`);
}


function confirmDelete(questionId) {
  if (!confirm("Are you sure you want to delete this question?")) return;

  $.ajax({
    url: 'question_backend.php',
    type: 'POST',
    data: {
      delete_question: 1,
      question_id: questionId
    },
    success: function(response) {
      response = response.trim();
      if (response === 'success') {
        alert('Question deleted successfully!');
        location.reload(); // Auto refresh
      } else {
        alert('Failed to delete question: ' + response);
      }
    },
    error: function(xhr, status, error) {
      alert('AJAX Error: ' + error);
    }
  });
}


</script>




</body>
</html>