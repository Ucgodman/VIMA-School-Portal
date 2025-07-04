<?php
include 'config/database.php';
include 'functions/helper_functions.php'; // Include helper functions

$email = 'michael.brown@example.com';  // The email of the user whose password you want to update
$new_password = 'techer123';  // The new password you want to set

// Hash the new password
$hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);

// Prepare SQL statement to update the password
$query = "UPDATE staff SET password = :password, updated_at = NOW() WHERE email = :email";

// Prepare the statement
$stmt = $pdo->prepare($query);

// Bind parameters and execute the update
$stmt->bindParam(':email', $email);
$stmt->bindParam(':password', $hashedPassword);
$stmt->execute();

echo "Password updated successfully!";
?>
