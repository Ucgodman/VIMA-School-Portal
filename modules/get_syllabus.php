<?php
include_once __DIR__ . "/../config/database.php";

$limit = 5; // Number of records per page
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$start = ($page - 1) * $limit;

$class_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$whereClause = $class_id ? "WHERE s.class_id = ?" : "";

// Count total records for pagination
$countQuery = "SELECT COUNT(*) as total FROM syllabus s $whereClause";
$countStmt = $pdo->prepare($countQuery);
$class_id ? $countStmt->execute([$class_id]) : $countStmt->execute();
$totalRecords = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
$totalPages = ceil($totalRecords / $limit);

// Fetch syllabus with pagination
$query = "SELECT s.syllabus_id, s.title, s.description, sub.name AS subject, s.file_name, s.timestamp 
          FROM syllabus s
          JOIN subjects sub ON s.subject_id = sub.subject_id
          $whereClause
          ORDER BY s.timestamp DESC 
          LIMIT $start, $limit";
$stmt = $pdo->prepare($query);
$class_id ? $stmt->execute([$class_id]) : $stmt->execute();

if ($stmt->rowCount() > 0) {
    echo "<table class='table table-bordered'>";
    echo "<thead>
            <tr>
                <th>#</th>
                <th>Title</th>
                <th>Description</th>
                <th>Subject</th>
                <th>File</th>
                <th>Uploaded On</th>
            </tr>
          </thead><tbody>";

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<tr>
                <td>{$row['syllabus_id']}</td>
                <td>{$row['title']}</td>
                <td>{$row['description']}</td>
                <td>{$row['subject']}</td>
                <td><a href='uploads/syllabus/{$row['file_name']}' target='_blank'>Download</a></td>
                <td>{$row['timestamp']}</td>
              </tr>";
    }
    echo "</tbody></table>";

    // Pagination Links
    echo "<nav><ul class='pagination'>";
    for ($i = 1; $i <= $totalPages; $i++) {
        $active = ($i == $page) ? "active" : "";
        echo "<li class='page-item $active'><a class='page-link' href='#' onclick='filterSyllabus($class_id, $i)'>$i</a></li>";
    }
    echo "</ul></nav>";
} else {
    echo "<p>No syllabus found for this class.</p>";
}
?>
