<?php
$host = 'localhost';
$dbname = 'ok_market_db';
$username = 'okmarket';
$password = 'Qazwsx1234$#';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}
?>
