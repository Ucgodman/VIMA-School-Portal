<?php
// live_class_list.php
session_start();
include_once "../config/database.php";

$limit = 10;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$class_id = $_GET['class_id'] ?? '';
$section_id = $_GET['section_id'] ?? '';
$subject = $_GET['subject'] ?? '';

$where = "WHERE 1";
$params = [];

if ($class_id) {
    $where .= " AND lc.class_id = ?";
    $params[] = $class_id;
}
if ($section_id) {
    $where .= " AND lc.section_id = ?";
    $params[] = $section_id;
}
if ($subject) {
    $where .= " AND lc.subject LIKE ?";
    $params[] = "%$subject%";
}

$count_sql = "SELECT COUNT(*) FROM live_classes lc $where";
$count_stmt = $pdo->prepare($count_sql);
$count_stmt->execute($params);
$total_records = $count_stmt->fetchColumn();

$sql = "SELECT lc.*, c.class_name, s.section_name
        FROM live_classes lc
        JOIN classes c ON c.id = lc.class_id
        JOIN sections s ON s.id = lc.section_id
        $where
        ORDER BY lc.start_time DESC
        LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_pages = ceil($total_records / $limit);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Live Class List</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="p-4">
    <h3>Live Class List</h3>

    <form method="get" class="row g-3 mb-4">
        <div class="col-md-3">
            <input type="text" name="class_id" class="form-control" placeholder="Class ID" value="<?= htmlspecialchars($class_id) ?>">
        </div>
        <div class="col-md-3">
            <input type="text" name="section_id" class="form-control" placeholder="Section ID" value="<?= htmlspecialchars($section_id) ?>">
        </div>
        <div class="col-md-3">
            <input type="text" name="subject" class="form-control" placeholder="Subject" value="<?= htmlspecialchars($subject) ?>">
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary">Filter</button>
        </div>
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Title</th>
                <th>Subject</th>
                <th>Class</th>
                <th>Section</th>
                <th>Start Time</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($classes): foreach ($classes as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td><?= htmlspecialchars($row['subject']) ?></td>
                    <td><?= htmlspecialchars($row['class_name']) ?></td>
                    <td><?= htmlspecialchars($row['section_name']) ?></td>
                    <td><?= date("d M Y H:i", strtotime($row['start_time'])) ?></td>
                    <td>
                        <a href="join_live_class.php?class_id=<?= $row['id'] ?>" class="btn btn-sm btn-success">Join</a>
                    </td>
                </tr>
            <?php endforeach; else: ?>
                <tr><td colspan="6">No classes found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <nav>
        <ul class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>&class_id=<?= $class_id ?>&section_id=<?= $section_id ?>&subject=<?= urlencode($subject) ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>
</body>
</html>
