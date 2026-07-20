<?php
session_start();
require_once '../config/database.php';

if (isset($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['admin_username']);
    $password = $_POST['admin_password'];

    $sql = "SELECT id, password FROM admins WHERE username = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if (!$stmt) {
        die("Database error: " . mysqli_error($conn) . "<br>SQL: $sql");
    }

    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $admin = mysqli_fetch_assoc($result);

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id'];
        header('Location: dashboard.php');
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Login - LookFab</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css?family=Playfair+Display:700,900|Lato:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .admin-auth-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--background);
        }
        .admin-auth-card {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(26,34,56,0.10);
            max-width: 400px;
            width: 100%;
            padding: 36px 28px 28px 28px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .admin-auth-card h2 {
            font-family: 'Playfair Display', serif;
            color: var(--primary);
            margin-bottom: 18px;
        }
        .admin-auth-form .form-group {
            margin-bottom: 18px;
            width: 100%;
        }
        .admin-auth-form label {
            display: block;
            margin-bottom: 6px;
            color: var(--primary);
            font-weight: 600;
        }
        .admin-auth-form input[type="text"],
        .admin-auth-form input[type="password"] {
            width: 100%;
            padding: 10px 12px;
            border-radius: var(--radius);
            border: 1.5px solid #ddd;
            font-size: 1em;
            outline: none;
            background: #f5f6fa;
            margin-bottom: 2px;
        }
        .admin-auth-form input:focus {
            border: 1.5px solid var(--secondary);
        }
        .admin-auth-form .btn {
            width: 100%;
            margin-top: 12px;
        }
        .admin-auth-card .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 18px;
            font-size: 1em;
            background: #ffeaea;
            color: #b71c1c;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <section class="admin-auth-section">
        <div class="admin-auth-card">
            <h2>Admin Login</h2>
            <?php if (!empty($error)): ?>
                <div class="alert"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form action="admin_login.php" method="POST" class="admin-auth-form">
                <div class="form-group">
                    <label for="admin_username">Username</label>
                    <input type="text" id="admin_username" name="admin_username" required>
                </div>
                <div class="form-group">
                    <label for="admin_password">Password</label>
                    <input type="password" id="admin_password" name="admin_password" required>
                </div>
                <button type="submit" class="btn">Login</button>
            </form>
        </div>
    </section>
</body>
</html>