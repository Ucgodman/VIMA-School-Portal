<?php
include_once __DIR__ . "/../config/database.php";

$classId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($classId > 0) {
    // Fetch sections with class and teacher details
    $stmt = $pdo->prepare("SELECT s.section_id, s.name AS section_name, c.class_name AS class_name, 
                                    st.firstname, st.lastname 
                           FROM sections s
                           JOIN classes c ON s.class_id = c.class_id
                           LEFT JOIN staff st ON c.staff_id = st.id
                           WHERE s.class_id = ?
                           ORDER BY c.class_name, s.name ASC");
    $stmt->execute([$classId]);
    $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($sections) {
        foreach ($sections as $section) {
            echo "<tr>
                    <td>" . htmlspecialchars($section['section_name']) . "</td>
                    <td>" . htmlspecialchars($section['class_name']) . "</td>
                    <td>" . (!empty($section['firstname']) ? htmlspecialchars($section['firstname'] . " " . $section['lastname']) : "No Teacher Assigned") . "</td>
                    <td>
                        <a href='edit_section.php?id=" . $section['section_id'] . "' class='btn btn-warning btn-xs rounded-circle d-flex justify-content-center align-items-center bi bi-pencil' style='width: 26px; height: 26px; color: white;' title='Edit'></a>
                        <a href='delete_section.php?id=" . $section['section_id'] . "' class='btn btn-danger btn-xs rounded-circle d-flex justify-content-center align-items-center bi bi-trash' style='width: 26px; height: 26px; color: white;' title='Delete'
                        onclick='return confirm(\"Are you sure you want to delete this section?\");'></a>
                    </td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='4'>No sections found for this class.</td></tr>";
    }
} else {
    echo "<tr><td colspan='4'>Invalid class ID.</td></tr>";
}
?>
