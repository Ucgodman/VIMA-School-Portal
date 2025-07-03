<?php
include_once __DIR__ . "/../config/database.php";

if (isset($_POST['class_id'], $_POST['section_id'], $_POST['date'])) {
    $class_id = $_POST['class_id'];
    $section_id = $_POST['section_id'];
    $date = $_POST['date']; // Format: YYYY-MM-DD

    // Fetch student attendance details
    $stmt = $pdo->prepare("
        SELECT s.student_id, s.firstname, s.middlename, s.lastname, a.status, a.created_at
        FROM students s
        LEFT JOIN attendance a 
        ON s.student_id = a.student_id 
        AND DATE(a.created_at) = ?
        WHERE s.class_id = ? AND s.section_id = ?
        ORDER BY s.lastname, s.firstname");
    $stmt->execute([$date, $class_id, $section_id]);
    $students = $stmt->fetchAll();

    if ($students) {
        echo '<div class="table-responsive">
                <table class="table table-striped table-hover table-bordered">
                    <thead class="table-secondary">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>';
        $i = 1;

        foreach ($students as $student) {
            $status = trim($student['status'] ?? 'Undefined'); // Ensure no leading/trailing spaces

            // ✅ Check if status is stored as text instead of numbers
            switch (strtolower($status)) {
                case 'present':
                case '1':
                    $status_badge = '<span class="badge bg-success">Present</span>';
                    break;
                case 'absent':
                case '2':
                    $status_badge = '<span class="badge bg-danger">Absent</span>';
                    break;
                case 'holiday':
                case '3':
                    $status_badge = '<span class="badge bg-info">Holiday</span>';
                    break;
                case 'half day':
                case '4':
                    $status_badge = '<span class="badge bg-primary">Half Day</span>';
                    break;
                case 'late':
                case '5':
                    $status_badge = '<span class="badge bg-warning text-dark">Late</span>';
                    break;
                default:
                    $status_badge = '<span class="badge bg-secondary">Undefined</span>';
                    break;
            }

            echo '<tr>
                    <td>' . $i++ . '</td>
                    <td>' . htmlspecialchars($student['lastname'] . ' ' . $student['firstname'] . ' ' . $student['middlename']) . '</td>
                    <td>' . $status_badge . '</td>
                  </tr>';
        }
        echo '</tbody></table></div>';
    } else {
        echo '<div class="alert alert-warning text-center">No records found for the selected criteria.</div>';
    }
}
?>
