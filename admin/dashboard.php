<?php
session_start();
require_once '../config/database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - LookFab</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css?family=Playfair+Display:700,900|Lato:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .admin-header {
            background: var(--primary);
            color: #fff;
            padding: 24px 0 12px 0;
            text-align: center;
            font-family: 'Playfair Display', serif;
            font-size: 2em;
            letter-spacing: 2px;
        }
        .admin-nav {
            background: var(--primary);
            display: flex;
            justify-content: center;
            gap: 32px;
            padding: 12px 0;
        }
        .admin-nav a {
            color: #fff;
            font-size: 1.08em;
            padding: 8px 18px;
            border-radius: 12px;
            text-decoration: none;
            transition: background 0.2s, color 0.2s;
        }
        .admin-nav a:hover, .admin-nav .active {
            background: var(--secondary);
            color: var(--primary);
        }
        .admin-dashboard-section {
            max-width: 1100px;
            margin: 40px auto;
            padding: 0 18px;
        }
        .admin-dashboard-cards {
            display: flex;
            flex-wrap: wrap;
            gap: 32px;
            justify-content: center;
            margin-top: 40px;
        }
        .admin-dashboard-card {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(26,34,56,0.10);
            padding: 36px 32px 28px 32px;
            min-width: 240px;
            max-width: 320px;
            flex: 1 1 260px;
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: box-shadow 0.2s, transform 0.2s;
            position: relative;
        }
        .admin-dashboard-card i {
            font-size: 2.6em;
            color: var(--secondary);
            margin-bottom: 18px;
        }
        .admin-dashboard-card .card-value {
            font-size: 2.4em;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 8px;
            font-family: 'Playfair Display', serif;
        }
        .admin-dashboard-card h3 {
            margin: 0 0 10px 0;
            color: var(--text);
            font-size: 1.18em;
            font-family: 'Lato', 'Open Sans', Arial, sans-serif;
        }
        .admin-dashboard-card a {
            margin-top: 10px;
            color: var(--secondary);
            font-weight: 600;
            text-decoration: none;
            border-radius: 8px;
            padding: 6px 18px;
            background: #f5f6fa;
            transition: background 0.2s, color 0.2s;
            font-size: 1em;
        }
        .admin-dashboard-card a:hover {
            background: var(--secondary);
            color: var(--primary);
        }
        .admin-dashboard-card:hover {
            box-shadow: 0 8px 32px rgba(26,34,56,0.16);
            transform: translateY(-4px) scale(1.03);
        }
        @media (max-width: 900px) {
            .admin-dashboard-cards {
                flex-direction: column;
                gap: 24px;
            }
            .admin-dashboard-card {
                min-width: 0;
                width: 100%;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="admin-header">LookFab Admin Dashboard</div>
    <nav class="admin-nav">
        <a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="manage_products.php"><i class="fas fa-box"></i> Products</a>
        <a href="orders.php"><i class="fas fa-shopping-bag"></i> Orders</a>
        <a href="users.php"><i class="fas fa-users"></i> Users</a>
        <a href="admin_logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
    <section class="admin-dashboard-section">
        <div class="admin-dashboard-cards">
            <!-- Products Card -->
            <div class="admin-dashboard-card">
                <i class="fas fa-box"></i>
                <div class="card-value">
                    <?php
                    $result = mysqli_query($conn, "SELECT COUNT(*) as count FROM products");
                    $row = mysqli_fetch_assoc($result);
                    echo $row['count'];
                    ?>
                </div>
                <h3>Products</h3>
                <a href="manage_products.php">Manage Products</a>
            </div>
            <!-- Orders Card -->
            <div class="admin-dashboard-card">
                <i class="fas fa-shopping-bag"></i>
                <div class="card-value">
                    <?php
                    $result = mysqli_query($conn, "SELECT COUNT(*) as count FROM orders");
                    $row = mysqli_fetch_assoc($result);
                    echo $row['count'];
                    ?>
                </div>
                <h3>Orders</h3>
                <a href="orders.php">View Orders</a>
            </div>
            <!-- Users Card -->
            <div class="admin-dashboard-card">
                <i class="fas fa-users"></i>
                <div class="card-value">
                    <?php
                    $result = mysqli_query($conn, "SELECT COUNT(*) as count FROM users");
                    $row = mysqli_fetch_assoc($result);
                    echo $row['count'];
                    ?>
                </div>
                <h3>Users</h3>
                <a href="users.php">View Users</a>
            </div>
        </div>
    </section>
</body>
</html>