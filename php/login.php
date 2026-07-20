<?php
session_start();
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    if (empty($username) || empty($password)) {
        header('Location: ../login.php?error=empty');
        exit;
    }
    
    // Check if username is email
    if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
        $query = "SELECT * FROM users WHERE email = ?";
    } else {
        $query = "SELECT * FROM users WHERE username = ?";
    }
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_type'] = $user['user_type'];
            
            // Redirect to admin dashboard if admin
            if ($user['user_type'] === 'admin') {
                header('Location: ../admin/dashboard.php');
            } else {
                // Redirect to previous page or home
                $redirect = isset($_SESSION['redirect']) ? $_SESSION['redirect'] : '../index.php';
                unset($_SESSION['redirect']);
                header('Location: ' . $redirect);
            }
            exit;
        }
    }
    
    // If we get here, login failed
    header('Location: ../login.php?error=invalid');
    exit;
}

header('Location: ../login.php');
exit;
?>