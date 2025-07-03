<?php
$host = 'localhost';
$dbname = 'schportal';
$username = 'root';
$password = '';

try {
    // Create a PDO instance and establish a connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // If there is an error, display the message
    echo "Connection failed: " . $e->getMessage();
    exit;
}
?>
