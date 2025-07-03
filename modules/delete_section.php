<?php

include_once __DIR__ . "/../config/database.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $sectionId = intval($_POST["section_id"]);

    if (empty($sectionId)) {
        echo "Invalid section ID.";
        exit;
    }

    try {
        // Check if the section exists
        $checkQuery = "SELECT COUNT(*) FROM sections WHERE section_id = ?";
        $stmt = $pdo->prepare($checkQuery);
        $stmt->execute([$sectionId]);
        $count = $stmt->fetchColumn();

        if ($count == 0) {
            echo "Section not found.";
            exit;
        }

        // Check if the section is referenced elsewhere
        $referenceQuery = "SELECT COUNT(*) FROM students WHERE section_id = ?";
        $stmt = $pdo->prepare($referenceQuery);
        $stmt->execute([$sectionId]);
        $referenceCount = $stmt->fetchColumn();

        if ($referenceCount > 0) {
            echo "Cannot delete section. It is referenced by students.";
            exit;
        }

        // Soft delete: Mark section as inactive instead of deleting
        $deleteQuery = "UPDATE sections SET is_active = 0 WHERE section_id = ?";
        $stmt = $pdo->prepare($deleteQuery);
        $stmt->execute([$sectionId]);

        echo "Section deactivated successfully.";
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
    }
} else {
    echo "Invalid request.";
}
?>
