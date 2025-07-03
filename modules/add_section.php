<?php

include_once __DIR__ . "/../config/database.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $sectionName = trim($_POST["name"]);
    $classId = intval($_POST["class_id"]); // Ensure correct field name
    $sessionId = intval($_POST["session_id"]); // Retrieve session ID from form
    $staffId = !empty($_POST["staff_id"]) ? intval($_POST["staff_id"]) : NULL; // Get staff ID (nullable)

    if (empty($sectionName) || empty($classId) || empty($sessionId)) {
        echo "All fields are required.";
        exit;
    }

    try {
        // Check if the section already exists for the selected class and session
        $checkQuery = "SELECT COUNT(*) FROM sections WHERE name = ? AND class_id = ? AND session_id = ?";
        $stmt = $pdo->prepare($checkQuery);
        $stmt->execute([$sectionName, $classId, $sessionId]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            echo "This section already exists for the selected class in the active session.";
            exit;
        }

        // Insert the new section with the assigned teacher
        $insertQuery = "INSERT INTO sections (name, class_id, session_id, staff_id, created_at, updated_at) 
                        VALUES (?, ?, ?, ?, NOW(), NOW())";
        $stmt = $pdo->prepare($insertQuery);
        $stmt->execute([$sectionName, $classId, $sessionId, $staffId]);

        echo "Section added successfully.";
    } catch (PDOException $e) {
        echo "Database error: " . $e->getMessage();
    }
} else {
    echo "Invalid request.";
}
