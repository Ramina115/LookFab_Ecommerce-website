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

if (empty($_POST['order_id'])) {
    echo json_encode(['success' => false, 'message' => 'Order ID is required']);
    exit;
}

$order_id = (int)$_POST['order_id'];

// Start transaction
mysqli_begin_transaction($conn);

try {
    // Delete order items first (due to foreign key constraint)
    $delete_items_query = "DELETE FROM order_items WHERE order_id = ?";
    $stmt = mysqli_prepare($conn, $delete_items_query);
    mysqli_stmt_bind_param($stmt, 'i', $order_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Delete the order
    $delete_order_query = "DELETE FROM orders WHERE id = ?";
    $stmt = mysqli_prepare($conn, $delete_order_query);
    mysqli_stmt_bind_param($stmt, 'i', $order_id);
    
    if (mysqli_stmt_execute($stmt)) {
        mysqli_commit($conn);
        echo json_encode(['success' => true, 'message' => 'Order deleted successfully']);
    } else {
        throw new Exception('Failed to delete order');
    }
    
    mysqli_stmt_close($stmt);
    
} catch (Exception $e) {
    mysqli_rollback($conn);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

mysqli_close($conn);
?> 