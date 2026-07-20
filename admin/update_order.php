<?php
session_start();
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Validate required fields
$required_fields = ['order_id', 'full_name', 'address', 'city', 'payment_method', 'status'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        echo json_encode(['success' => false, 'message' => "Missing required field: $field"]);
        exit;
    }
}

$order_id = (int)$_POST['order_id'];
$full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
$address = mysqli_real_escape_string($conn, $_POST['address']);
$city = mysqli_real_escape_string($conn, $_POST['city']);
$payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
$status = mysqli_real_escape_string($conn, $_POST['status']);

// Validate status
$valid_statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
if (!in_array($status, $valid_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit;
}

// Update order
$query = "UPDATE orders SET full_name = ?, address = ?, city = ?, payment_method = ?, status = ? WHERE id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, 'sssssi', $full_name, $address, $city, $payment_method, $status, $order_id);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode(['success' => true, 'message' => 'Order updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($conn)]);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?> 