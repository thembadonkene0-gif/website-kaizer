<?php
require __DIR__ . '/../db.php';
$result = $conn->query('SELECT id, email, password FROM admins');
if (!$result) {
    echo 'query failed: ' . $conn->error;
    exit(1);
}
while ($row = $result->fetch_assoc()) {
    echo 'id=' . $row['id'] . ' email=' . $row['email'] . ' password=' . $row['password'] . "\n";
}
?>