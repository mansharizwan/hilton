<?php
// Database connection parameters
$host = 'localhost';
$dbname = 'dbie2tqurypex3';
$username = 'ufmo7njmacww5';
$password = '11sfr0qvmbjh';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
