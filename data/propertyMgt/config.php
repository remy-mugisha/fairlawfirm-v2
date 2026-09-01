<?php
require_once dirname(__DIR__, 2) . '/env.php';
loadEnv(dirname(__DIR__, 2) . '/.env');

$host = env('DB_HOST', 'localhost');
$dbname = env('DB_NAME', 'helloshi_fairdb');
$charset = "utf8";
$username = env('DB_USER', 'root');
$password = env('DB_PASS', '');

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log("Database connection failed: " . $e->getMessage());
    die("A database error occurred. Please try again later.");
}
?>
