<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once 'config/database.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=cart');
    exit;
}

// Verify database connection
if (!$conn) {
    die("Database connection failed. Please try again later.");
}

// Function to create cart table
function create_cart_table($conn) {
    $sql = "CREATE TABLE IF NOT EXISTS cart (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id INT(6) UNSIGNED NOT NULL,
        product_id INT(6) UNSIGNED NOT NULL,
        quantity INT(6) NOT NULL DEFAULT 1,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    ) ENGINE=InnoDB";
    
    return mysqli_query($conn, $sql);
}

// Check if cart table exists
$table_check = mysqli_query($conn, "SHOW TABLES LIKE 'cart'");
if (mysqli_num_rows($table_check) == 0) {
    if (!create_cart_table($conn)) {
        die("Could not initialize shopping cart. Error: " . mysqli_error($conn));
    }
}

// Get cart items with error handling
$user_id = $_SESSION['user_id'];
$query = "SELECT c.id as cart_id, p.id as product_id, p.name, p.price, p.image, c.quantity 
          FROM cart c 
          JOIN products p ON c.product_id = p.id 
          WHERE c.user_id = ?";

$cart_items = [];
$stmt = mysqli_prepare($conn, $query);

if (!$stmt) {
    die("Prepare failed: " . mysqli_error($conn));
}

if (!mysqli_stmt_bind_param($stmt, 'i', $user_id)) {
    die("Binding parameters failed: " . mysqli_error($conn));
}

if (!mysqli_stmt_execute($stmt)) {
    die("Execute failed: " . mysqli_error($conn));
}

$result = mysqli_stmt_get_result($stmt);
if ($result) {
    $cart_items = mysqli_fetch_all($result, MYSQLI_ASSOC);
} else {
    die("Get result failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Shopping Cart - LookFab</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }
        
        .cart-container {
            max-width: 1000px;
            margin: 30px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        
        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .cart-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .cart-table th {
            background-color: #2c3e50;
            color: white;
            padding: 15px;
            text-align: left;
        }
        
        .cart-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            vertical-align: middle;
        }
        
        .cart-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .product-info {
            display: flex;
            align-items: center;
        }
        
        .product-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            margin-right: 20px;
            border-radius: 4px;
        }
        
        .quantity-control {
            display: flex;
            align-items: center;
        }
        
        .quantity-btn {
            width: 30px;
            height: 30px;
            background: #e0e0e0;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        
        .quantity-input {
            width: 50px;
            text-align: center;
            margin: 0 10px;
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .remove-btn {
            color: #e74c3c;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 18px;
            padding: 5px;
        }
        
        .cart-total {
            text-align: right;
            font-size: 1.3em;
            margin: 30px 0;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        
        .total-amount {
            font-weight: bold;
            color: #e74c3c;
            font-size: 1.4em;
        }
        
        .action-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }
        
        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1em;
            font-weight: bold;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        
        .btn-checkout {
            background-color: #27ae60;
            color: white;
        }
        
        .btn-checkout:hover {
            background-color: #2ecc71;
        }
        
        .btn-continue {
            background-color: #3498db;
            color: white;
        }
        
        .btn-continue:hover {
            background-color: #2980b9;
        }
        
        .empty-cart {
            text-align: center;
            padding: 50px;
        }
        
        .empty-cart i {
            font-size: 60px;
            color: #bdc3c7;
            margin-bottom: 20px;
        }
        
        .empty-cart p {
            font-size: 18px;
            color: #7f8c8d;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="cart-container">
        <h1>Your Shopping Cart</h1>
        
        <?php if (empty($cart_items)): ?>
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <p>Your cart is empty</p>
                <a href="products.php" class="btn btn-continue">Browse Products</a>
            </div>
        <?php else: ?>
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total = 0;
                    foreach ($cart_items as $item): 
                        $subtotal = $item['price'] * $item['quantity'];
                        $total += $subtotal;
                    ?>
                        <tr>
                            <td>
                                <div class="product-info">
                                    <img src="assets/images/products/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="product-image">
                                    <div>
                                        <h3><?= htmlspecialchars($item['name']) ?></h3>
                                    </div>
                                </div>
                            </td>
                            <td>$<?= number_format($item['price'], 2) ?></td>
                            <td>
                                <div class="quantity-control">
                                    <button class="quantity-btn">-</button>
                                    <input type="text" class="quantity-input" value="<?= $item['quantity'] ?>">
                                    <button class="quantity-btn">+</button>
                                </div>
                            </td>
                            <td>$<?= number_format($subtotal, 2) ?></td>
                            <td>
                                <button class="remove-btn" data-cart-id="<?= $item['cart_id'] ?>">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <div class="cart-total">
                <span>Total: </span>
                <span class="total-amount">$<?= number_format($total, 2) ?></span>
            </div>
            
            <div class="action-buttons">
                <a href="products.php" class="btn btn-continue">Continue Shopping</a>
                <a href="checkout.php" class="btn btn-checkout">Proceed to Checkout</a>
            </div>
        <?php endif; ?>
    </div>

    <script src="assets/js/ajax.js"></script>
    <script>
        // Quantity adjustment functionality
        document.querySelectorAll('.quantity-btn').forEach(button => {
            button.addEventListener('click', function() {
                const input = this.parentElement.querySelector('.quantity-input');
                let quantity = parseInt(input.value);
                if (this.textContent === '-' && quantity > 1) {
                    input.value = quantity - 1;
                } else if (this.textContent === '+' && quantity < 99) {
                    input.value = quantity + 1;
                }
                // TODO: Add AJAX call to update quantity in database
                const cartId = this.closest('tr').querySelector('.remove-btn').dataset.cartId;
                console.log('Update quantity for cart item:', cartId, 'to', input.value);
            });
        });
        // Remove item functionality (AJAX)
        document.querySelectorAll('.remove-btn').forEach(button => {
            button.addEventListener('click', function() {
                const cartId = this.dataset.cartId;
                removeFromCart(cartId);
            });
        });
    </script>
</body>
</html>