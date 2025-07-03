<?php

function fetch_one($query, $params = []) {
    global $pdo;

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function fetch_all($query, $params = []) {
    global $pdo;

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);  // ✅ Corrected to fetch all rows
}

function execute_query($query, $params = []) {
    global $pdo;

    $stmt = $pdo->prepare($query);
    return $stmt->execute($params);  // ✅ Now correctly executes INSERT, UPDATE, DELETE
}

?>
