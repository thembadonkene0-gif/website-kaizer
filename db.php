<?php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'kaizerbnb';

$conn = new mysqli($host, $user, $password);

if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

require 'db_setup.php';
ensure_booking_database($conn);
$conn->set_charset('utf8');
?>
