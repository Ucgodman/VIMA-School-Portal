<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();

}

if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id'])) {
    echo '<pre>'; var_dump($_SESSION); echo '</pre>'; // Debugging
    header("Location: ../login.php");
    exit;
}
