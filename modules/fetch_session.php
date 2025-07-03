<?php
include_once __DIR__ . "/../config/database.php";

try {
    $stmt = $pdo->query("SELECT session_id, session_year FROM sessions ORDER BY session_year DESC");
    $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($sessions) > 0) {
        foreach ($sessions as $index => $session) {
            echo "<tr>
                    <td>" . ($index + 1) . "</td>
                    <td>" . htmlspecialchars($session['session_year']) . "</td>
                    <td>
                        <a href='edit_session.php?id=" . $session['session_id'] . "' class='btn btn-primary btn-sm'>Edit</a>
                        <a href='delete_session.php?id=" . $session['session_id'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Delete this session?\");'>Delete</a>
                    </td>
                </tr>";
        }
    } else {
        echo "<tr><td colspan='3' class='text-center'>No sessions found.</td></tr>";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
