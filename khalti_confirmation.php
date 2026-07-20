<?php
session_start();
require_once 'config/database.php';
require_once 'config/khalti.php';

// Get the pidx from Khalti return URL
$pidx = $_GET['pidx'] ?? null;
$order_id = $_GET['order_id'] ?? null;

$payment_status = 'verifying';
$payment_message = 'Verifying your payment...';
$khalti_response = null;
$order = null;
$items = [];

// Verify payment if pidx is provided
if ($pidx && $order_id) {
    // Verify payment using config function
    $khalti_response = verifyKhaltiPayment($pidx);
    
    if (isset($khalti_response['error'])) {
        $payment_status = 'error';
        $payment_message = 'Payment verification error: ' . $khalti_response['error'];
    } else if ($khalti_response && isset($khalti_response['status'])) {
        // Update order status based on payment status
        $status = 'pending';
        $message = '';
        
        switch ($khalti_response['status']) {
            case 'Completed':
                $status = 'processing';
                $message = 'Payment successful! Your order has been confirmed.';
                $payment_status = 'success';
                break;
            case 'Expired':
                $status = 'cancelled';
                $message = 'Payment session expired. Please try again.';
                $payment_status = 'expired';
                break;
            case 'User canceled':
                $status = 'cancelled';
                $message = 'Payment was cancelled. You can try again anytime.';
                $payment_status = 'cancelled';
                break;
            default:
                $status = 'pending';
                $message = 'Payment status: ' . ($khalti_response['status'] ?? 'Unknown');
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

<section class="khalti-confirmation-section">
    <div class="container">
        <?php if ($payment_status === 'success'): ?>
            <!-- Success State -->
            <div class="khalti-confirmation-card success">
                <div class="khalti-header">
                    <div class="khalti-logo">
                        <svg width="120" height="120" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="60" cy="60" r="60" fill="#5C2D91"/>
                            <path d="M60 30L75 45H65V55H75V65H65V75H75L60 90L45 75H55V65H45V55H55V45H45L60 30Z" fill="white"/>
                        </svg>
                    </div>
                    <h1 class="khalti-title">Payment Confirmed!</h1>
                    <p class="khalti-subtitle">Your payment has been successfully processed through Khalti</p>
                </div>
                
                <div class="success-animation-wrapper">
                    <div class="success-checkmark">
                        <div class="check-icon-circle">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>
                </div>
                
                <div class="confirmation-details">
                    <?php if ($khalti_response): ?>
                        <div class="detail-card">
                            <div class="detail-header">
                                <i class="fas fa-receipt"></i>
                                <h3>Transaction Details</h3>
                            </div>
                            <div class="detail-body">
                                <div class="detail-row">
                                    <span class="detail-label">Transaction ID</span>
                                    <span class="detail-value"><?= htmlspecialchars($pidx) ?></span>
                                </div>
                                <?php if (isset($khalti_response['transaction_id'])): ?>
                                <div class="detail-row">
                                    <span class="detail-label">Khalti Transaction</span>
                                    <span class="detail-value"><?= htmlspecialchars($khalti_response['transaction_id']) ?></span>
                                </div>
                                <?php endif; ?>
                                <div class="detail-row">
                                    <span class="detail-label">Payment Method</span>
                                    <span class="detail-value">
                                        <i class="fab fa-cc-visa"></i> Khalti Digital Wallet
                                    </span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Amount Paid</span>
                                    <span class="detail-value amount">₹<?= number_format(($khalti_response['total_amount'] ?? $order['total_amount'] ?? 0) / 100, 2) ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($order): ?>
                        <div class="detail-card">
                            <div class="detail-header">
                                <i class="fas fa-shopping-bag"></i>
                                <h3>Order Information</h3>
                            </div>
                            <div class="detail-body">
                                <div class="detail-row">
                                    <span class="detail-label">Order Number</span>
                                    <span class="detail-value">#<?= $order_id ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Order Status</span>
                                    <span class="detail-value status-badge processing">Processing</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Delivery Address</span>
                                    <span class="detail-value"><?= htmlspecialchars($order['address']) ?>, <?= htmlspecialchars($order['city']) ?></span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Contact</span>
                                    <span class="detail-value"><?= htmlspecialchars($order['phone']) ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <?php if (!empty($items)): ?>
                            <div class="detail-card">
                                <div class="detail-header">
                                    <i class="fas fa-box"></i>
                                    <h3>Ordered Items</h3>
                                </div>
                                <div class="items-list">
                                    <?php foreach ($items as $item): ?>
                                        <div class="item-card">
                                            <img src="assets/images/products/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                                            <div class="item-info">
                                                <h4><?= htmlspecialchars($item['name']) ?></h4>
                                                <p>Quantity: <?= $item['quantity'] ?> × ₹<?= number_format($item['price'], 2) ?></p>
                                            </div>
                                            <div class="item-price">₹<?= number_format($item['price'] * $item['quantity'], 2) ?></div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="order-total-row">
                                    <span>Total Amount</span>
                                    <span class="total-amount">₹<?= number_format($order['total_amount'], 2) ?></span>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                
                <div class="confirmation-actions">
                    <a href="order_confirmation.php?order_id=<?= $order_id ?>" class="btn-khalti btn-primary">
                        <i class="fas fa-eye"></i> View Full Order Details
                    </a>
                    <a href="products.php" class="btn-khalti btn-secondary">
                        <i class="fas fa-shopping-bag"></i> Continue Shopping
                    </a>
                </div>
                
                <div class="khalti-footer-note">
                    <i class="fas fa-shield-alt"></i>
                    <p>Your payment is secured by Khalti. You will receive an email confirmation shortly.</p>
                </div>
            </div>
            
        <?php elseif ($payment_status === 'cancelled' || $payment_status === 'expired'): ?>
            <!-- Cancelled/Expired State -->
            <div class="khalti-confirmation-card cancelled">
                <div class="khalti-header">
                    <div class="khalti-logo">
                        <svg width="120" height="120" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="60" cy="60" r="60" fill="#e74c3c"/>
                            <path d="M60 30L75 45H65V55H75V65H65V75H75L60 90L45 75H55V65H45V55H55V45H45L60 30Z" fill="white"/>
                        </svg>
                    </div>
                    <h1 class="khalti-title">Payment <?= $payment_status === 'expired' ? 'Expired' : 'Cancelled' ?></h1>
                    <p class="khalti-subtitle"><?= htmlspecialchars($payment_message) ?></p>
                </div>
                
                <div class="cancelled-icon-wrapper">
                    <i class="fas fa-times-circle"></i>
                </div>
                
                <div class="confirmation-actions">
                    <a href="checkout.php" class="btn-khalti btn-primary">
                        <i class="fas fa-redo"></i> Try Payment Again
                    </a>
                    <a href="cart.php" class="btn-khalti btn-secondary">
                        <i class="fas fa-shopping-cart"></i> Back to Cart
                    </a>
                </div>
            </div>
            
        <?php else: ?>
            <!-- Error/Pending State -->
            <div class="khalti-confirmation-card error">
                <div class="khalti-header">
                    <div class="khalti-logo">
                        <svg width="120" height="120" viewBox="0 0 120 120" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="60" cy="60" r="60" fill="#f39c12"/>
                            <path d="M60 30L75 45H65V55H75V65H65V75H75L60 90L45 75H55V65H45V55H55V45H45L60 30Z" fill="white"/>
                        </svg>
                    </div>
                    <h1 class="khalti-title">Payment <?= $payment_status === 'failed' ? 'Failed' : 'Pending' ?></h1>
                    <p class="khalti-subtitle"><?= htmlspecialchars($payment_message ?: 'Unable to verify payment status. Please contact support.') ?></p>
                </div>
                
                <div class="error-icon-wrapper">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                
                <div class="confirmation-actions">
                    <?php if ($order_id): ?>
                        <a href="order_confirmation.php?order_id=<?= $order_id ?>" class="btn-khalti btn-primary">
                            <i class="fas fa-eye"></i> View Order Status
                        </a>
                    <?php endif; ?>
                    <a href="checkout.php" class="btn-khalti btn-secondary">
                        <i class="fas fa-redo"></i> Try Again
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

