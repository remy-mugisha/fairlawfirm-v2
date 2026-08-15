<?php
$host = "localhost";
$dbname = "helloshi_faird";
$charset = "utf8";
$username = "helloshi_fairUser"; 
$password = "Allin@12345"; 

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
