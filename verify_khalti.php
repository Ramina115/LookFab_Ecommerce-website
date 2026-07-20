<?php
session_start();
require_once 'config/database.php';
require_once 'config/khalti.php';

// Get the pidx from the URL
$pidx = $_GET['pidx'] ?? null;
$order_id = $_GET['order_id'] ?? null;

if ($pidx && $order_id) {
    // Verify payment using config function
    $responseArray = verifyKhaltiPayment($pidx);
 
    if (isset($responseArray['error'])) {
        $_SESSION['payment_message'] = [
            'type' => 'error',
            'text' => 'Payment verification error: ' . $responseArray['error']
        ];
        header("Location: order_confirmation.php?order_id=" . $order_id);
        exit();
    }

    if ($responseArray) {
        if (!$responseArray || !isset($responseArray['status'])) {
            $_SESSION['payment_message'] = [
                'type' => 'error',
                'text' => 'Invalid response from payment gateway.'
            ];
            header("Location: order_confirmation.php?order_id=" . $order_id);
            exit();
        }
        
        // Update order status based on payment status
        $status = 'pending';
        $message = '';
        
        switch ($responseArray['status']) {
            case 'Completed':
                $status = 'processing'; // Changed to 'processing' as it's a valid status and order is paid
                $message = 'Payment successful! Your order is being processed.';
                break;
            case 'Expired':
                $status = 'cancelled';
                $message = 'Payment expired.';
                break;
            case 'User canceled':
                $status = 'cancelled';
                $message = 'Payment cancelled by user.';
                break;
            default:
                $status = 'pending';
                $message = 'Payment status: ' . ($responseArray['status'] ?? 'Unknown');
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
        
        // Set session message
        $_SESSION['payment_message'] = [
            'type' => $status === 'processing' ? 'success' : 'error',
            'text' => $message
        ];
        
        // Redirect to Khalti confirmation page
        header("Location: khalti_confirmation.php?pidx=" . urlencode($pidx) . "&order_id=" . $order_id);
        exit();
    } else {
        $_SESSION['payment_message'] = [
            'type' => 'error',
            'text' => 'No response from payment gateway.'
        ];
        header("Location: order_confirmation.php?order_id=" . $order_id);
        exit();
    }
} else {
    // If no pidx, redirect to home
    header("Location: index.php");
    exit();
}
?>