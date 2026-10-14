<?php

$host = "db01.dbhost.dev";
$port = "5051";
$dbname = "db_454yt3gaw";
$username = "user_454yt3gaw";
$password = "p454yt3gaw";

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    $pdo->setAttribute(
        PDO::ATTR_DEFAULT_FETCH_MODE,
        PDO::FETCH_ASSOC
    );

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}


?>