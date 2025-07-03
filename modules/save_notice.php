<?php

include_once __DIR__ . "/../config/database.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST["title"]);
    $location = trim($_POST["location"]);
    $content = trim($_POST["content"]);
    $noticeboard_date = $_POST["noticeboard_date"];
    $send_to_mail = isset($_POST["send_to_mail"]) ? 1 : 0;

    try {
        // Insert notice into the database
        $query = "INSERT INTO noticeboard (title, location, content, noticeboard_date, send_to_mail) 
                  VALUES (:title, :location, :content, :noticeboard_date, :send_to_mail)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            ":title" => $title,
            ":location" => $location,
            ":content" => $content,
            ":noticeboard_date" => $noticeboard_date,
            ":send_to_mail" => $send_to_mail
        ]);

        // Send email if required
        if ($send_to_mail == 1) {
            include_once __DIR__ . "/../functions/mailer.php"; // Assuming mailer function exists
            $subject = "New Notice: " . $title;
            $message = "Location: $location\nDate: $noticeboard_date\n\n$content";
            sendMailToAllUsers($subject, $message); // Assuming this function sends email to all users
        }

        $_SESSION["success"] = "Notice added successfully.";
    } catch (PDOException $e) {
        $_SESSION["error"] = "Error: " . $e->getMessage();
    }
}

header("Location: noticeboard.php");
exit;
?>
