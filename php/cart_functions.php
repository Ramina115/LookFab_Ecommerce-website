<?php
session_start();
include '../config/database.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'add':
        $product_id = intval($_POST['product_id']);
        $quantity = intval($_POST['quantity'] ?? 1);
        
        // Check if product exists and is in stock
        $query = "SELECT id, stock FROM products WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'i', $product_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) === 0) {
            echo json_encode(['success' => false, 'message' => 'Product not found']);
            exit;
        }
        
        $product = mysqli_fetch_assoc($result);
        if ($product['stock'] < $quantity) {
            echo json_encode(['success' => false, 'message' => 'Not enough stock']);
            exit;
        }
        
        // Check if product already in cart
        $query = "SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'ii', $user_id, $product_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            // Update quantity
            $cart_item = mysqli_fetch_assoc($result);
            $new_quantity = $cart_item['quantity'] + $quantity;
            
            $query = "UPDATE cart SET quantity = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'ii', $new_quantity, $cart_item['id']);
        } else {
            // Add new item
            $query = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'iii', $user_id, $product_id, $quantity);
        }
        
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
        break;
        
    case 'remove':
        $cart_id = intval($_POST['cart_id']);
        
        $query = "DELETE FROM cart WHERE id = ? AND user_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'ii', $cart_id, $user_id);
        
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
        break;
        
    case 'update':
        $cart_id = intval($_POST['cart_id']);
        $quantity = intval($_POST['quantity']);
        
        if ($quantity < 1) {
            echo json_encode(['success' => false, 'message' => 'Invalid quantity']);
            exit;
        }
        
        // Get product price
        $query = "SELECT p.price 
                  FROM cart c 
                  JOIN products p ON c.product_id = p.id 
                  WHERE c.id = ? AND c.user_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'ii', $cart_id, $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) === 0) {
            echo json_encode(['success' => false, 'message' => 'Item not found']);
            exit;
        }
        
        $product = mysqli_fetch_assoc($result);
        
        // Update quantity
        $query = "UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'iii', $quantity, $cart_id, $user_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $newTotal = number_format($product['price'] * $quantity, 2);
            
            // Calculate grand total
            $query = "SELECT SUM(p.price * c.quantity) as grand_total 
                      FROM cart c 
                      JOIN products p ON c.product_id = p.id 
                      WHERE c.user_id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, 'i', $user_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $grandTotal = mysqli_fetch_assoc($result)['grand_total'] ?? 0;
            $grandTotal = number_format($grandTotal + 5, 2); // Adding shipping
            
            echo json_encode([
                'success' => true,
                'newTotal' => $newTotal,
                'grandTotal' => $grandTotal
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error']);
        }
        break;
        
    case 'get_count':
        $query = "SELECT COUNT(*) as count FROM cart WHERE user_id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, 'i', $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $count = mysqli_fetch_assoc($result)['count'] ?? 0;
        
        echo json_encode(['success' => true, 'count' => $count]);
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
?>