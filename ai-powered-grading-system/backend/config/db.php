<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "plmun_portal_system";
$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

try {
    $conn = new PDO($dsn, $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo = $conn; // Make $pdo available for consistency with other files
    $GLOBALS['pdo'] = $conn; // Also make it globally available
    // echo "Connected successfully"; // optional debug
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    die("Sorry, could not connect to the database right now.");
}
