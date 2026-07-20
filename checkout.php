<?php
session_start();
require_once 'config/database.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Calculate cart total from database
$total_query = "SELECT SUM(p.price * c.quantity) as total 
               FROM cart c 
               JOIN products p ON c.product_id = p.id 
               WHERE c.user_id = ?";
$stmt = mysqli_prepare($conn, $total_query);
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$total_result = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
$total = $total_result['total'] ?? 0;

// Redirect if cart is empty
if ($total <= 0) {
    header("Location: cart.php");
    exit;
}

// Get cart items for display
$cart_query = "SELECT c.id as cart_id, p.id as product_id, p.name, p.price, p.image, c.quantity 
               FROM cart c 
               JOIN products p ON c.product_id = p.id 
               WHERE c.user_id = ?";
$stmt = mysqli_prepare($conn, $cart_query);
mysqli_stmt_bind_param($stmt, 'i', $user_id);
mysqli_stmt_execute($stmt);
$cart_items = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);

// Process checkout
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $state = mysqli_real_escape_string($conn, $_POST['state']);
    $zip = mysqli_real_escape_string($conn, $_POST['zip']);
    
    // Insert order
    $order_query = "INSERT INTO orders (user_id, full_name, email, phone, address, city, state, zip_code, total_amount) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $order_query);
    mysqli_stmt_bind_param($stmt, 'isssssssd', $user_id, $full_name, $email, $phone, $address, $city, $state, $zip, $total);
    
    if (mysqli_stmt_execute($stmt)) {
        $order_id = mysqli_insert_id($conn);
        
        // Insert order items from database cart
        $items_query = "INSERT INTO order_items (order_id, product_id, quantity, price)
                        SELECT ?, c.product_id, c.quantity, p.price
                        FROM cart c
                        JOIN products p ON c.product_id = p.id
                        WHERE c.user_id = ?";
        $stmt_items = mysqli_prepare($conn, $items_query);
        mysqli_stmt_bind_param($stmt_items, 'ii', $order_id, $user_id);
        mysqli_stmt_execute($stmt_items);
        
        // Clear cart from database
        $delete_cart_query = "DELETE FROM cart WHERE user_id = ?";
        $stmt_delete = mysqli_prepare($conn, $delete_cart_query);
        mysqli_stmt_bind_param($stmt_delete, 'i', $user_id);
        mysqli_stmt_execute($stmt_delete);
        
        // Initiate Khalti payment
        require_once 'config/khalti.php';
        
        $return_url = getKhaltiReturnUrl($order_id);
        $website_url = getKhaltiWebsiteUrl();
        
        $payload = [
            'return_url' => $return_url,
            'website_url' => $website_url,
            'amount' => $total * 100, // Convert to paisa
            'purchase_order_id' => 'ORDER_' . $order_id,
            'purchase_order_name' => 'Order #' . $order_id,
            'customer_info' => [
                'name' => $full_name,
                'email' => $email,
                'phone' => $phone
            ]
        ];
        
        $responseArray = initiateKhaltiPayment($payload);
        
        if (isset($responseArray['payment_url'])) {
            // Redirect to Khalti payment page
            header("Location: " . $responseArray['payment_url']);
            exit();
        } else {
            // If payment initiation fails, show order confirmation with error message
            $error_msg = isset($responseArray['error']) ? $responseArray['error'] : 'Payment initiation failed. Please contact support.';
            $_SESSION['payment_message'] = [
                'type' => 'error',
                'text' => $error_msg
            ];
            header("Location: order_confirmation.php?order_id=" . $order_id);
            exit();
        }
        
    } else {
        $error = "Failed to place order. Please try again.";
    }
}
?>

<?php include 'includes/header.php'; ?>

<section class="checkout-section">
    <div class="container">
        <div class="checkout-header">
            <h2 class="checkout-title">
                <i class="fas fa-shopping-bag"></i> Checkout
            </h2>
            <p class="checkout-subtitle">Complete your order and proceed to payment</p>
        </div>
        
        <div class="checkout-container">
            <div class="checkout-left">
                <div class="checkout-form-card">
                    <div class="form-header">
                        <h3><i class="fas fa-shipping-fast"></i> Shipping Information</h3>
                        <p class="form-subtitle">Please provide your delivery details</p>
                    </div>
                    
                    <?php if (isset($error)): ?>
                        <div class="alert alert-error">
                            <i class="fas fa-exclamation-circle"></i> <?= $error ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="" id="checkoutForm" class="checkout-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="full_name">
                                    <i class="fas fa-user"></i> Full Name *
                                </label>
                                <input type="text" id="full_name" name="full_name" placeholder="Enter your full name" required>
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="email">
                                    <i class="fas fa-envelope"></i> Email Address *
                                </label>
                                <input type="email" id="email" name="email" placeholder="your.email@example.com" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">
                                    <i class="fas fa-phone"></i> Phone Number *
                                </label>
                                <input type="tel" id="phone" name="phone" placeholder="98XXXXXXXX" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="address">
                                <i class="fas fa-map-marker-alt"></i> Delivery Address *
                            </label>
                            <textarea id="address" name="address" rows="3" placeholder="Enter your complete address" required></textarea>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="city">
                                    <i class="fas fa-city"></i> City *
                                </label>
                                <input type="text" id="city" name="city" placeholder="Kathmandu" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="state">
                                    <i class="fas fa-map"></i> State/Province *
                                </label>
                                <input type="text" id="state" name="state" placeholder="Bagmati" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="zip">
                                <i class="fas fa-mail-bulk"></i> ZIP/Postal Code *
                            </label>
                            <input type="text" id="zip" name="zip" placeholder="44600" required>
                        </div>
                        
                        <div class="payment-method-info">
                            <div class="payment-method-card">
                                <div class="payment-method-header">
                                    <i class="fab fa-cc-visa"></i>
                                    <span>Secure Payment via Khalti</span>
                                </div>
                                <p class="payment-method-desc">
                                    <i class="fas fa-shield-alt"></i> Your payment information is secure and encrypted
                                </p>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn-checkout-submit" id="submitBtn">
                            <span class="btn-content">
                                <i class="fas fa-lock"></i>
                                <span>Proceed to Khalti Payment</span>
                            </span>
                            <span class="btn-loader" style="display: none;">
                                <i class="fas fa-spinner fa-spin"></i> Processing...
                            </span>
                        </button>
                        
                        <p class="payment-note">
                            <i class="fas fa-info-circle"></i> You will be redirected to Khalti's secure payment gateway to complete your purchase.
                        </p>
                    </form>
                </div>
            </div>
            
            <div class="checkout-right">
                <div class="checkout-summary-card">
                    <div class="summary-header">
                        <h3><i class="fas fa-receipt"></i> Order Summary</h3>
                    </div>
                    
                    <div class="summary-items">
                        <?php if (!empty($cart_items)): ?>
                            <?php foreach ($cart_items as $item): ?>
                                <div class="summary-item">
                                    <div class="summary-item-image">
                                        <img src="assets/images/products/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                                    </div>
                                    <div class="summary-item-details">
                                        <h4><?= htmlspecialchars($item['name']) ?></h4>
                                        <p>Quantity: <?= $item['quantity'] ?> × ₹<?= number_format($item['price'], 2) ?></p>
                                    </div>
                                    <div class="summary-item-price">
                                        ₹<?= number_format($item['price'] * $item['quantity'], 2) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    
                    <div class="summary-divider"></div>
                    
                    <div class="summary-totals">
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span>₹<?= number_format($total, 2) ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Shipping</span>
                            <span class="free-shipping">FREE</span>
                        </div>
                        <div class="summary-total">
                            <span>Total Amount</span>
                            <span class="total-amount">₹<?= number_format($total, 2) ?></span>
                        </div>
                    </div>
                    
                    <div class="summary-footer">
                        <div class="trust-badges">
                            <div class="trust-badge">
                                <i class="fas fa-shield-alt"></i>
                                <span>Secure Payment</span>
                            </div>
                            <div class="trust-badge">
                                <i class="fas fa-truck"></i>
                                <span>Free Shipping</span>
                            </div>
                            <div class="trust-badge">
                                <i class="fas fa-undo"></i>
                                <span>Easy Returns</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submitBtn');
    const btnContent = submitBtn.querySelector('.btn-content');
    const btnLoader = submitBtn.querySelector('.btn-loader');
    
    btnContent.style.display = 'none';
    btnLoader.style.display = 'inline-flex';
    submitBtn.disabled = true;
});
</script>

<?php include 'includes/footer.php'; ?>