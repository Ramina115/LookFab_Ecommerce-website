<?php
session_start();
require_once 'config/database.php';

// Redirect if no order ID
if (empty($_GET['order_id'])) {
    header("Location: cart.php");
    exit;
}

$order_id = (int)$_GET['order_id'];
$user_id = $_SESSION['user_id'];

// Fetch order details
$order_query = "SELECT * FROM orders WHERE id = ? AND user_id = ?";
$stmt = mysqli_prepare($conn, $order_query);
mysqli_stmt_bind_param($stmt, 'ii', $order_id, $user_id);
mysqli_stmt_execute($stmt);
$order = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

// Fetch order items
$items_query = "SELECT oi.*, p.name, p.image 
               FROM order_items oi
               JOIN products p ON oi.product_id = p.id
               WHERE oi.order_id = ?";
$stmt = mysqli_prepare($conn, $items_query);
mysqli_stmt_bind_param($stmt, 'i', $order_id);
mysqli_stmt_execute($stmt);
$items = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
?>

<?php include 'includes/header.php'; ?>

<section class="order-confirmation-section">
    <div class="container">
        <div class="order-confirm-card">
            <div class="order-confirm-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h2 class="order-confirm-title">Thank You for Your Order!</h2>
            <div class="order-confirm-details">
                <div class="order-confirm-row">
                    <span>Order #</span>
                    <span><?= $order_id ?></span>
                </div>
                <div class="order-confirm-row">
                    <span>Status</span>
                    <span><?= ucfirst($order['status']) ?></span>
                </div>
                <div class="order-confirm-row">
                    <span>Total</span>
                    <span class="order-confirm-total">₹<?= number_format($order['total_amount'], 2) ?></span>
                </div>
                <div class="order-confirm-row">
                    <span>Shipped to</span>
                    <span><?= htmlspecialchars($order['full_name']) ?>, <?= htmlspecialchars($order['city']) ?></span>
                </div>
            </div>
            <div class="order-confirm-items">
                <h3>Your Items</h3>
                <?php foreach ($items as $item): ?>
                    <div class="order-confirm-item">
                        <img src="assets/images/products/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                        <div>
                            <div class="order-confirm-item-name"><?= htmlspecialchars($item['name']) ?></div>
                            <div class="order-confirm-item-meta">
                                <span>Qty: <?= $item['quantity'] ?></span>
                                <span>Price: ₹<?= number_format($item['price'], 2) ?></span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <a href="products.php" class="btn" style="margin-top: 24px;">Continue Shopping</a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>