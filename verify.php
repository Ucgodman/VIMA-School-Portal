<?php

include 'config/database.php'; // Include database connection
include 'functions/helper_functions.php'; // Include helper functions
// Assuming you have already established a connection to the database

// Example email to fetch the user
$email = 'admin@example.com';  // The email of the user you want to verify

// Fetch user data (including password hash) from the database
$query = "SELECT password FROM users WHERE email = :email";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':email', $email);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if the user exists and verify the password
if ($user) {
    $storedHash = $user['password']; // The hash from your database
    $passwordToVerify = 'admin123'; // The plain password to verify

    if (password_verify($passwordToVerify, $storedHash)) {
        echo "Password is correct!";
    } else {
        echo "Password is incorrect!";
    }
} else {
    echo "User not found!";
}
?>
