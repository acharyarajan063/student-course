<?php
// run this script once (via browser or CLI) to insert a known admin user
require_once __DIR__ . '/config/db.php';

$email = 'acharyarajan063@gmail.com';
$password = '123';

$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare('INSERT INTO admins (email, password) VALUES (?, ?)');
$stmt->bind_param('ss', $email, $hash);
if ($stmt->execute()) {
    echo "Admin user inserted: $email\n";
} else {
    echo "Error inserting admin: " . $conn->error;
}
?>