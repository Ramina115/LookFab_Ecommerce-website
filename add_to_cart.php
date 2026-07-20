<?php
// Start output buffering
ob_start();

// Strict error handling
error_reporting(0);
ini_set('display_errors', 0);

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set JSON header immediately
header('Content-Type: application/json');

// Check request method FIRST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode([
        'success' => false,
        'message' => 'Only POST requests are allowed',
        'received_method' => $_SERVER['REQUEST_METHOD'],
        'expected_method' => 'POST'
    ]));
}

// Rest of your cart processing code...
// [Keep the existing code you have for handling the cart]
// Start output buffering to catch any accidental output
ob_start();

// Strict error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0); // We'll handle errors manually

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set JSON header immediately
header('Content-Type: application/json');

try {
    // Validate session
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Please login first', 401);
    }

    // Only accept POST requests
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method', 405);
    }

    // Validate input
    if (!isset($_POST['product_id'])) {
        throw new Exception('Product ID is required', 400);
    }

    // Sanitize input
    $product_id = filter_var($_POST['product_id'], FILTER_VALIDATE_INT);
    $quantity = isset($_POST['quantity']) ? filter_var($_POST['quantity'], FILTER_VALIDATE_INT) : 1;

    if (!$product_id || $product_id <= 0) {
        throw new Exception('Invalid product ID', 400);
    }

    if (!$quantity || $quantity <= 0) {
        throw new Exception('Quantity must be at least 1', 400);
    }

    // Include database config
    require __DIR__.'/config/database.php';

    // Check database connection
    if (!$conn) {
        throw new Exception('Database connection failed', 500);
    }

    // Verify product exists
    $stmt = $conn->prepare("SELECT id FROM products WHERE id = ?");
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    
    if (!$stmt->get_result()->num_rows) {
        throw new Exception('Product does not exist', 404);
    }

    // Check if already in cart
    $stmt = $conn->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param('ii', $_SESSION['user_id'], $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update existing item
        $row = $result->fetch_assoc();
        $new_qty = $row['quantity'] + $quantity;
        $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
        $stmt->bind_param('ii', $new_qty, $row['id']);
    } else {
        // Insert new item
        $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->bind_param('iii', $_SESSION['user_id'], $product_id, $quantity);
    }

    if (!$stmt->execute()) {
        throw new Exception('Failed to update cart: '.$conn->error, 500);
    }

    // Successful response
    $response = [
        'success' => true,
        'message' => 'Product added to cart',
        'data' => [
            'product_id' => $product_id,
            'quantity' => $quantity
        ]
    ];

} catch (Exception $e) {
    // Error response
    $response = [
        'success' => false,
        'message' => $e->getMessage(),
        'code' => $e->getCode()
    ];
} finally {
    // Clean any output buffers
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    
    // Send JSON response
    echo json_encode($response);
    exit;
}

