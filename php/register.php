<?php
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    // Validate inputs
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        header('Location: ../register.php?error=empty');
        exit;
    }
    
    // Additional validation
    if (strlen($password) < 4) {
        header('Location: ../register.php?error=pwlength');
        exit;
    }
    if (!preg_match('/[^A-Za-z0-9]/', $password)) {
        header('Location: ../register.php?error=pwspecial');
        exit;
    }
    if (empty(trim($username))) {
        header('Location: ../register.php?error=emptyusername');
        exit;
    }
    
    if ($password !== $confirm_password) {
        header('Location: ../register.php?error=password');
        exit;
    }
    
    // Check if username exists
    $query = "SELECT id FROM users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    
    if (mysqli_stmt_num_rows($stmt) > 0) {
        header('Location: ../register.php?error=username');
        exit;
    }
    
    // Check if email exists
    $query = "SELECT id FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    
    if (mysqli_stmt_num_rows($stmt) > 0) {
        header('Location: ../register.php?error=email');
        exit;
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new user
    $query = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'sss', $username, $email, $hashed_password);
    
    if (mysqli_stmt_execute($stmt)) {
        header('Location: ../login.php?success=registered');
    } else {
        header('Location: ../register.php?error=database');
    }
    exit;
}

header('Location: ../register.php');
exit;
?>