<?php
// Run this ONCE to create an admin user
require_once '../config/database.php';
$username = 'admin';
$password = 'admin123'; // change this!
$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = mysqli_prepare($conn, "INSERT INTO admins (username, password) VALUES (?, ?)");
mysqli_stmt_bind_param($stmt, 'ss', $username, $hash);
mysqli_stmt_execute($stmt);
echo "Admin created!";
?>