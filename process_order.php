<?php
session_start();
require_once 'config/database.php';

// Redirect if not POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: cart.php");
    exit;
}

// Validate required fields
$required = ['full_name', 'address', 'city', 'payment_method'];
foreach ($required as $field) {
    if (empty($_POST[$field])) {
        die("Error: Missing required field '$field'");
    }
}

// 1. Calculate Order Total
$user_id = $_SESSION['user_id'];
$total_query = "SELECT SUM(p.price * c.quantity) as total 
               FROM cart c 
               JOIN products p ON c.product_id = p.id 
               WHERE c.user_id = ?";
$stmt = mysqli_prepare($conn, $total_query);
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$total_result = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
$total = $total_result['total'] ?? 0;

// 2. Save Order
$order_query = "INSERT INTO orders (user_id, full_name, address, city, payment_method, total_amount) 
                VALUES (?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($conn, $order_query);
mysqli_stmt_bind_param($stmt, 'issssd', 
    $user_id,
    $_POST['full_name'],
    $_POST['address'],
    $_POST['city'],
    $_POST['payment_method'],
    $total
);
mysqli_stmt_execute($stmt);
$order_id = mysqli_insert_id($conn);

// 3. Save Order Items
$items_query = "INSERT INTO order_items (order_id, product_id, quantity, price)
                SELECT ?, c.product_id, c.quantity, p.price
                FROM cart c
                JOIN products p ON c.product_id = p.id
                WHERE c.user_id = ?";
$stmt = mysqli_prepare($conn, $items_query);
mysqli_stmt_bind_param($stmt, 'ii', $order_id, $user_id);
mysqli_stmt_execute($stmt);

// 4. Clear Cart
mysqli_query($conn, "DELETE FROM cart WHERE user_id = $user_id");

// 5. Redirect to Confirmation
header("Location: order_confirmation.php?order_id=$order_id");
exit;
?>