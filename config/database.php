<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "lookfab_db";

// Create connection with error handling
$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) {
    error_log("Database connection error: " . mysqli_connect_error());
    die("We're experiencing technical difficulties. Please try again later.");
}

// Set charset to utf8mb4
if (!mysqli_set_charset($conn, "utf8mb4")) {
    error_log("Error setting charset: " . mysqli_error($conn));
}
?>