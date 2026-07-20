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

if (!$order) {
    header("Location: cart.php");
    exit;
}

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

<?php if (isset($_SESSION['payment_message'])): ?>
    <div class="payment-alert payment-alert-<?= $_SESSION['payment_message']['type'] ?>">
        <div class="container">
            <p><?= htmlspecialchars($_SESSION['payment_message']['text']) ?></p>
        </div>
    </div>
    <?php unset($_SESSION['payment_message']); ?>
<?php endif; ?>

<section class="order-confirmation-section">
    <div class="container">
        <div class="order-confirm-card">
            <div class="order-confirm-icon <?= in_array($order['status'], ['processing', 'shipped', 'delivered']) ? 'success' : 'pending' ?>">
                <i class="fas fa-<?= in_array($order['status'], ['processing', 'shipped', 'delivered']) ? 'check-circle' : 'clock' ?>"></i>
            </div>
            <h2 class="order-confirm-title">
                <?php if (in_array($order['status'], ['processing', 'shipped', 'delivered'])): ?>
                    Payment Successful!
                <?php else: ?>
                    Order Received
                <?php endif; ?>
            </h2>
            <p class="order-confirm-subtitle">
                <?php if (in_array($order['status'], ['processing', 'shipped', 'delivered'])): ?>
                    Thank you for your purchase. Your order is being processed.
                <?php else: ?>
                    Please complete the payment to process your order.
                <?php endif; ?>
            </p>
            
            <div class="order-confirm-details">
                <div class="order-confirm-row">
                    <span>Order #</span>
                    <span><?= $order_id ?></span>
                </div>
                <div class="order-confirm-row">
                    <span>Status</span>
                    <span class="status-<?= $order['status'] ?>"><?= ucfirst($order['status']) ?></span>
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
            
            <div class="order-confirm-actions">
                <a href="products.php" class="btn btn-outline">
                    <i class="fas fa-shopping-bag"></i> Continue Shopping
                </a>
                <?php if (!in_array($order['status'], ['processing', 'shipped', 'delivered'])): ?>
                    <a href="checkout.php?order_id=<?= $order_id ?>" class="btn btn-primary">
                        <i class="fas fa-credit-card"></i> Complete Payment
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>