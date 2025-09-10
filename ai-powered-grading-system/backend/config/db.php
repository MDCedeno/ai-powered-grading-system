<?php
// Database connection configuration
$host = 'localhost';
$dbname = 'plmun_portal';
$username = 'root';
$password = ''; // Adjust if you have a password set for your MySQL user

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    // Set error mode to exception for better error handling
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
