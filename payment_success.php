<?php
session_start();
require_once 'config/database.php';
require_once 'config/khalti.php';

// Get the pidx from the URL
$pidx = $_GET['pidx'] ?? null;
$order_id = $_GET['order_id'] ?? null;

$payment_status = 'pending';
$payment_message = '';
$order = null;
$items = [];

if ($pidx && $order_id) {
    // Verify payment using config function
    $responseArray = verifyKhaltiPayment($pidx);
    
    if (isset($responseArray['error'])) {
        $payment_status = 'error';
        $payment_message = 'Payment verification error: ' . $responseArray['error'];
    } else if ($responseArray) {
        // Update order status based on payment status
        $status = 'pending';
        $message = '';
        
        switch ($responseArray['status']) {
            case 'Completed':
                $status = 'processing';
                $message = 'Payment successful! Your order is being processed.';
                $payment_status = 'success';
                break;
            case 'Expired':
                $status = 'cancelled';
                $message = 'Payment expired.';
                $payment_status = 'expired';
                break;
            case 'User canceled':
                $status = 'cancelled';
                $message = 'Payment cancelled by user.';
                $payment_status = 'cancelled';
                break;
            default:
                $status = 'pending';
                $message = 'Payment status: ' . ($responseArray['status'] ?? 'Unknown');
                $payment_status = 'failed';
                break;
        }
        
        // Update order status in database
        $update_query = "UPDATE orders SET status = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $update_query);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'si', $status, $order_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
        
        $payment_message = $message;
    }
}

// Fetch order details
if ($order_id) {
    $user_id = $_SESSION['user_id'] ?? null;
    $order_query = "SELECT * FROM orders WHERE id = ?" . ($user_id ? " AND user_id = ?" : "");
    $stmt = mysqli_prepare($conn, $order_query);
    if ($user_id) {
        mysqli_stmt_bind_param($stmt, 'ii', $order_id, $user_id);
    } else {
        mysqli_stmt_bind_param($stmt, 'i', $order_id);
    }
    mysqli_stmt_execute($stmt);
    $order = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    
    if ($order) {
        // Fetch order items
        $items_query = "SELECT oi.*, p.name, p.image 
                       FROM order_items oi
                       JOIN products p ON oi.product_id = p.id
                       WHERE oi.order_id = ?";
        $stmt = mysqli_prepare($conn, $items_query);
        mysqli_stmt_bind_param($stmt, 'i', $order_id);
        mysqli_stmt_execute($stmt);
        $items = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    }
}
?>

<?php include 'includes/header.php'; ?>

<section class="payment-success-section">
    <div class="container">
        <?php if ($payment_status === 'success'): ?>
            <!-- Success State -->
            <div class="payment-success-card success">
                <div class="success-animation">
                    <div class="success-icon-wrapper">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="success-checkmark">
                        <div class="check-icon"></div>
                    </div>
                </div>
                
                <h1 class="success-title">Payment Successful!</h1>
                <p class="success-message"><?= htmlspecialchars($payment_message) ?></p>
                
                <?php if ($order): ?>
                    <div class="order-info-card">
                        <div class="order-info-header">
                            <h3><i class="fas fa-receipt"></i> Order Details</h3>
                        </div>
                        <div class="order-info-body">
                            <div class="info-row">
                                <span class="info-label">Order Number</span>
                                <span class="info-value">#<?= $order_id ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Payment Method</span>
                                <span class="info-value">
                                    <i class="fab fa-cc-visa"></i> Khalti
                                </span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Total Amount</span>
                                <span class="info-value amount">₹<?= number_format($order['total_amount'], 2) ?></span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Delivery Address</span>
                                <span class="info-value"><?= htmlspecialchars($order['address']) ?>, <?= htmlspecialchars($order['city']) ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (!empty($items)): ?>
                        <div class="order-items-card">
                            <h3><i class="fas fa-shopping-bag"></i> Items Ordered</h3>
                            <div class="items-list">
                                <?php foreach ($items as $item): ?>
                                    <div class="item-row">
                                        <img src="assets/images/products/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                                        <div class="item-details">
                                            <h4><?= htmlspecialchars($item['name']) ?></h4>
                                            <p>Quantity: <?= $item['quantity'] ?> × ₹<?= number_format($item['price'], 2) ?></p>
                                        </div>
                                        <div class="item-total">₹<?= number_format($item['price'] * $item['quantity'], 2) ?></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                
                <div class="success-actions">
                    <a href="order_confirmation.php?order_id=<?= $order_id ?>" class="btn btn-view-order">
                        <i class="fas fa-eye"></i> View Order Details
                    </a>
                    <a href="products.php" class="btn btn-continue-shopping">
                        <i class="fas fa-shopping-bag"></i> Continue Shopping
                    </a>
                </div>
            </div>
            
        <?php elseif ($payment_status === 'cancelled' || $payment_status === 'expired'): ?>
            <!-- Cancelled/Expired State -->
            <div class="payment-success-card cancelled">
                <div class="cancelled-icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <h1 class="cancelled-title">Payment <?= $payment_status === 'expired' ? 'Expired' : 'Cancelled' ?></h1>
                <p class="cancelled-message"><?= htmlspecialchars($payment_message) ?></p>
                <div class="cancelled-actions">
                    <a href="checkout.php" class="btn btn-retry">
                        <i class="fas fa-redo"></i> Try Again
                    </a>
                    <a href="cart.php" class="btn btn-back-cart">
                        <i class="fas fa-shopping-cart"></i> Back to Cart
                    </a>
                </div>
            </div>
            
        <?php else: ?>
            <!-- Error/Pending State -->
            <div class="payment-success-card error">
                <div class="error-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h1 class="error-title">Payment <?= $payment_status === 'failed' ? 'Failed' : 'Pending' ?></h1>
                <p class="error-message"><?= htmlspecialchars($payment_message ?: 'Unable to verify payment status. Please contact support.') ?></p>
                <div class="error-actions">
                    <?php if ($order_id): ?>
                        <a href="order_confirmation.php?order_id=<?= $order_id ?>" class="btn btn-view-order">
                            <i class="fas fa-eye"></i> View Order
                        </a>
                    <?php endif; ?>
                    <a href="checkout.php" class="btn btn-retry">
                        <i class="fas fa-redo"></i> Try Again
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>


