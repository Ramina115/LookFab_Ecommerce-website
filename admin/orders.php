<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}
require_once '../config/database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Orders - LookFab Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .admin-section-card {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(26,34,56,0.10);
            padding: 36px 32px 28px 32px;
            margin: 40px auto;
            max-width: 1200px;
        }
        .admin-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(26,34,56,0.10);
            overflow: hidden;
        }
        .admin-table th, .admin-table td {
            padding: 14px 10px;
            text-align: center;
        }
        .admin-table th {
            background: var(--primary);
            color: #fff;
            font-weight: 700;
            font-size: 1.08em;
        }
        .admin-table tr {
            background: #f9f9fb;
        }
        .admin-table tr:not(:last-child) {
            border-bottom: 1px solid #eee;
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
        .admin-action-btn.view {
            background: #28a745;
            color: #fff;
        }
        .admin-action-btn:hover {
            opacity: 0.85;
        }
        .modal {
            display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.3); align-items: center; justify-content: center;
        }
        .modal-content {
            background: #fff; padding: 32px; border-radius: 16px; min-width: 320px; max-width: 90vw; box-shadow: 0 4px 24px rgba(26,34,56,0.10);
        }
        .modal-close { float: right; cursor: pointer; font-size: 1.3em; color: #e74c3c; }
        .modal-actions { margin-top: 18px; text-align: right; }
        .order-status {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: 600;
        }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-processing { background: #cce5ff; color: #004085; }
        .status-shipped { background: #d4edda; color: #155724; }
        .status-delivered { background: #d1ecf1; color: #0c5460; }
        .status-cancelled { background: #f8d7da; color: #721c24; }
        .order-details-modal {
            max-width: 600px;
            max-height: 80vh;
            overflow-y: auto;
        }
        .order-items {
            margin-top: 20px;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        .order-item {
            display: flex;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        .order-item img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
            margin-right: 15px;
        }
        .order-item-details {
            flex: 1;
        }
        .order-item-name {
            font-weight: 600;
            margin-bottom: 4px;
        }
        .order-item-meta {
            font-size: 0.9em;
            color: #666;
        }
        @media (max-width: 900px) {
            .admin-section-card { padding: 18px 4px; }
            .admin-table th, .admin-table td { padding: 8px 2px; font-size: 0.98em; }
        }
        .admin-header {
            position: sticky;
            top: 0;
            z-index: 1001;
            background: var(--primary);
            color: #fff;
            padding: 24px 0 12px 0;
            text-align: center;
            font-family: 'Playfair Display', serif;
            font-size: 2em;
            letter-spacing: 2px;
        }
        .admin-nav {
            position: sticky;
            top: 64px; /* height of header */
            z-index: 1000;
            background: var(--primary);
            display: flex;
            justify-content: center;
            gap: 32px;
            padding: 12px 0;
        }
    </style>
</head>
<body>
    <div class="admin-header">LookFab Admin</div>
    <nav class="admin-nav">
        <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="manage_products.php"><i class="fas fa-box"></i> Products</a>
        <a href="orders.php" class="active"><i class="fas fa-shopping-bag"></i> Orders</a>
        <a href="users.php"><i class="fas fa-users"></i> Users</a>
        <a href="admin_logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </nav>
    <section class="admin-section-card">
        <h2 style="margin-bottom: 24px; color: var(--primary); font-family: 'Playfair Display', serif;">Manage Orders</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Address</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $result = mysqli_query($conn, "SELECT o.*, u.username FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.id DESC");
                while ($order = mysqli_fetch_assoc($result)): ?>
                    <tr data-order-id="<?= $order['id'] ?>">
                        <td>#<?= $order['id'] ?></td>
                        <td>
                            <div><?= htmlspecialchars($order['full_name']) ?></div>
                            <small style="color: #666;"><?= htmlspecialchars($order['username'] ?? 'Guest') ?></small>
                        </td>
                        <td>
                            <div><?= htmlspecialchars($order['address']) ?></div>
                            <small style="color: #666;"><?= htmlspecialchars($order['city']) ?></small>
                        </td>
                        <td><?= htmlspecialchars($order['payment_method']) ?></td>
                        <td>
                            <span class="order-status status-<?= strtolower($order['status'] ?? 'pending') ?>">
                                <?= ucfirst($order['status'] ?? 'pending') ?>
                            </span>
                        </td>
                        <td>₹<?= number_format($order['total_amount'], 2) ?></td>
                        <td><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                        <td>
                            <button class="admin-action-btn view" onclick="viewOrderDetails(<?= $order['id'] ?>)"><i class="fas fa-eye"></i> View</button>
                            <button class="admin-action-btn edit" onclick="openEditOrderModal(<?= $order['id'] ?>, '<?= htmlspecialchars($order['full_name']) ?>', '<?= htmlspecialchars($order['address']) ?>', '<?= htmlspecialchars($order['city']) ?>', '<?= htmlspecialchars($order['payment_method']) ?>', '<?= htmlspecialchars($order['status'] ?? 'pending') ?>')"><i class="fas fa-edit"></i> Edit</button>
                            <button class="admin-action-btn delete" onclick="deleteOrder(<?= $order['id'] ?>)"><i class="fas fa-trash"></i> Delete</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </section>

    <!-- Edit Order Modal -->
    <div class="modal" id="editOrderModal">
        <div class="modal-content">
            <span class="modal-close" onclick="closeEditOrderModal()">&times;</span>
            <h3>Edit Order</h3>
            <form id="editOrderForm">
                <input type="hidden" name="order_id" id="modalOrderId">
                <label for="modalFullName">Full Name:</label>
                <input type="text" name="full_name" id="modalFullName" required>
                <label for="modalAddress">Address:</label>
                <textarea name="address" id="modalAddress" required></textarea>
                <label for="modalCity">City:</label>
                <input type="text" name="city" id="modalCity" required>
                <label for="modalPaymentMethod">Payment Method:</label>
                <select name="payment_method" id="modalPaymentMethod" required>
                    <option value="cod">Cash on Delivery</option>
                    <option value="card">Credit/Debit Card</option>
                    <option value="upi">UPI</option>
                    <option value="netbanking">Net Banking</option>
                </select>
                <label for="modalStatus">Status:</label>
                <select name="status" id="modalStatus" required>
                    <option value="pending">Pending</option>
                    <option value="processing">Processing</option>
                    <option value="shipped">Shipped</option>
                    <option value="delivered">Delivered</option>
                    <option value="cancelled">Cancelled</option>
                </select>
                <div class="modal-actions">
                    <button type="submit" class="admin-action-btn edit">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- View Order Details Modal -->
    <div class="modal" id="viewOrderModal">
        <div class="modal-content order-details-modal">
            <span class="modal-close" onclick="closeViewOrderModal()">&times;</span>
            <div id="orderDetailsContent">
                <!-- Order details will be loaded here -->
            </div>
        </div>
    </div>

    <script>
    function openEditOrderModal(orderId, fullName, address, city, paymentMethod, status) {
        document.getElementById('editOrderModal').style.display = 'flex';
        document.getElementById('modalOrderId').value = orderId;
        document.getElementById('modalFullName').value = fullName;
        document.getElementById('modalAddress').value = address;
        document.getElementById('modalCity').value = city;
        document.getElementById('modalPaymentMethod').value = paymentMethod;
        document.getElementById('modalStatus').value = status;
    }

    function closeEditOrderModal() {
        document.getElementById('editOrderModal').style.display = 'none';
    }

    function closeViewOrderModal() {
        document.getElementById('viewOrderModal').style.display = 'none';
    }

    document.getElementById('editOrderForm').onsubmit = function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'update_order.php');
        xhr.onload = function() {
            if (xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                if (response.success) {
                    location.reload(); // Reload to show updated data
                } else {
                    alert('Failed to update order: ' + response.message);
                }
            } else {
                alert('Failed to update order.');
            }
        };
        xhr.send(formData);
    };

    function deleteOrder(orderId) {
        if (!confirm('Are you sure you want to delete this order? This action cannot be undone.')) return;
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'delete_order.php');
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function() {
            if (xhr.status === 200) {
                var response = JSON.parse(xhr.responseText);
                if (response.success) {
                    document.querySelector('tr[data-order-id="'+orderId+'"]')?.remove();
                } else {
                    alert('Failed to delete order: ' + response.message);
                }
            } else {
                alert('Failed to delete order.');
            }
        };
        xhr.send('order_id='+orderId);
    }

    function viewOrderDetails(orderId) {
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'get_order_details.php?order_id=' + orderId);
        xhr.onload = function() {
            if (xhr.status === 200) {
                document.getElementById('orderDetailsContent').innerHTML = xhr.responseText;
                document.getElementById('viewOrderModal').style.display = 'flex';
            } else {
                alert('Failed to load order details.');
            }
        };
        xhr.send();
    }
    </script>
</body>
</html> 