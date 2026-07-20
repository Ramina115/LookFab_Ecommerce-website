<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    exit('Unauthorized');
}

require_once '../config/database.php';

if (empty($_GET['order_id'])) {
    exit('Order ID is required');
}

$order_id = (int)$_GET['order_id'];

// Fetch order details
$order_query = "SELECT o.*, u.username FROM orders o LEFT JOIN users u ON o.user_id = u.id WHERE o.id = ?";
$stmt = mysqli_prepare($conn, $order_query);
mysqli_stmt_bind_param($stmt, 'i', $order_id);
mysqli_stmt_execute($stmt);
$order = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$order) {
    exit('Order not found');
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

<div style="max-width: 600px;">
    <h3 style="color: var(--primary); margin-bottom: 20px;">Order #<?= $order_id ?> Details</h3>
    
    <div style="background: #f8f9fa; padding: 20px; border-radius: 12px; margin-bottom: 20px;">
        <h4 style="margin-bottom: 15px; color: var(--primary);">Customer Information</h4>
        <div style="margin-bottom: 10px;">
            <strong>Name:</strong> <?= htmlspecialchars($order['full_name']) ?>
        </div>
        <div style="margin-bottom: 10px;">
            <strong>Username:</strong> <?= htmlspecialchars($order['username'] ?? 'Guest') ?>
        </div>
        <div style="margin-bottom: 10px;">
            <strong>Address:</strong> <?= htmlspecialchars($order['address']) ?>
        </div>
        <div style="margin-bottom: 10px;">
            <strong>City:</strong> <?= htmlspecialchars($order['city']) ?>
        </div>
        <div style="margin-bottom: 10px;">
            <strong>Payment Method:</strong> <?= htmlspecialchars($order['payment_method']) ?>
        </div>
        <div style="margin-bottom: 10px;">
            <strong>Status:</strong> 
            <span style="padding: 4px 12px; border-radius: 20px; font-size: 0.9em; font-weight: 600; background: #d4edda; color: #155724;">
                <?= ucfirst($order['status'] ?? 'pending') ?>
            </span>
        </div>
        <div style="margin-bottom: 10px;">
            <strong>Total Amount:</strong> ₹<?= number_format($order['total_amount'], 2) ?>
        </div>
        <div>
            <strong>Order Date:</strong> <?= date('F d, Y \a\t g:i A', strtotime($order['created_at'])) ?>
        </div>
    </div>

    <div style="background: #f8f9fa; padding: 20px; border-radius: 12px;">
        <h4 style="margin-bottom: 15px; color: var(--primary);">Order Items</h4>
        <?php if (empty($items)): ?>
            <p style="color: #666; font-style: italic;">No items found for this order.</p>
        <?php else: ?>
            <?php foreach ($items as $item): ?>
                <div style="display: flex; align-items: center; padding: 15px 0; border-bottom: 1px solid #e9ecef;">
                    <img src="../assets/images/products/<?= htmlspecialchars($item['image']) ?>" 
                         alt="<?= htmlspecialchars($item['name']) ?>" 
                         style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; margin-right: 15px;">
                    <div style="flex: 1;">
                        <div style="font-weight: 600; margin-bottom: 5px;"><?= htmlspecialchars($item['name']) ?></div>
                        <div style="color: #666; font-size: 0.9em;">
                            Quantity: <?= $item['quantity'] ?> | 
                            Price: ₹<?= number_format($item['price'], 2) ?> | 
                            Total: ₹<?= number_format($item['price'] * $item['quantity'], 2) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div> 