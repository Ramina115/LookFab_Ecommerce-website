<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}
require_once '../config/database.php';

// Your existing logic for adding, editing, deleting products goes here...

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Products - LookFab Admin</title>
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
        .admin-products-section {
            max-width: 1100px;
            margin: 40px auto;
            padding: 0 18px;
        }
        .admin-products-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        .admin-products-header h2 {
            margin: 0;
            color: var(--primary);
            font-family: 'Playfair Display', serif;
        }
        .admin-products-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(26,34,56,0.10);
            overflow: hidden;
        }
        .admin-products-table th, .admin-products-table td {
            padding: 14px 10px;
            text-align: center;
        }
        .admin-products-table th {
            background: var(--primary);
            color: #fff;
            font-weight: 700;
            font-size: 1.08em;
        }
        .admin-products-table tr {
            background: #f9f9fb;
        }
        .admin-products-table tr:not(:last-child) {
            border-bottom: 1px solid #eee;
        }
        .admin-products-table img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 10px;
            border: 2px solid var(--secondary);
            background: #fff;
        }
        .admin-action-btn {
            background: var(--secondary);
            color: var(--primary);
            border: none;
            border-radius: 8px;
            padding: 6px 16px;
            font-size: 1em;
            cursor: pointer;
            margin: 0 4px;
            transition: background 0.2s, color 0.2s;
        }
        .admin-action-btn.edit {
            background: #3a86ff;
            color: #fff;
        }
        .admin-action-btn.delete {
            background: #e74c3c;
            color: #fff;
        }
        .admin-action-btn:hover {
            opacity: 0.85;
        }
        .add-product-btn {
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 8px 22px;
            font-size: 1.08em;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.2s, color 0.2s;
        }
        .add-product-btn:hover {
            background: var(--secondary);
            color: var(--primary);
        }
        @media (max-width: 900px) {
            .admin-products-section {
                padding: 0 2px;
            }
            .admin-products-header {
                flex-direction: column;
                gap: 12px;
                align-items: flex-start;
            }
            .admin-products-table th, .admin-products-table td {
                padding: 8px 2px;
                font-size: 0.98em;
            }
        }
    </style>
</head>
<body>
    <div class="admin-header">LookFab Admin</div>
    <nav class="admin-nav">
        <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="manage_products.php" class="active"><i class="fas fa-box"></i> Products</a>
        <a href="orders.php"><i class="fas fa-shopping-bag"></i> Orders</a>
        <a href="users.php"><i class="fas fa-users"></i> Users</a>
        <a href="admin_logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
    <section class="admin-products-section">
        <div class="admin-products-header">
            <h2>Manage Products</h2>
            <a href="add_product.php" class="add-product-btn"><i class="fas fa-plus"></i> Add Product</a>
        </div>
        <table class="admin-products-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Example: Fetch all products
                $result = mysqli_query($conn, "SELECT * FROM products");
                while ($product = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= $product['id'] ?></td>
                        <td><img src="../assets/images/products/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>"></td>
                        <td><?= htmlspecialchars($product['name']) ?></td>
                        <td><?= htmlspecialchars($product['category']) ?></td>
                        <td>₹<?= htmlspecialchars($product['price']) ?></td>
                        <td><?= htmlspecialchars($product['stock'] ?? '-') ?></td>
                        <td>
                            <a href="edit_product.php?id=<?= $product['id'] ?>" class="admin-action-btn edit"><i class="fas fa-edit"></i> Edit</a>
                            <a href="delete_product.php?id=<?= $product['id'] ?>" class="admin-action-btn delete" onclick="return confirm('Delete this product?');"><i class="fas fa-trash"></i> Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>
</body>
</html>