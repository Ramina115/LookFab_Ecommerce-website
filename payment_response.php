<?php
session_start();
require_once 'config/khalti.php';

// Get the pidx from the URL
$pidx = $_GET['pidx'] ?? null;

if ($pidx) {
    $responseArray = verifyKhaltiPayment($pidx);

    if ($responseArray && !isset($responseArray['error'])) {
        switch ($responseArray['status']) {
            case 'Completed':
                //here you can write your logic to update the database
                $_SESSION['transaction_msg'] = '<script>
                        Swal.fire({
                            icon: "success",
                            title: "Transaction successful.",
                            showConfirmButton: false,
                            timer: 1500
                        });
                    </script>';
                // Extract order_id from purchase_order_id if available
                $purchase_order_id = $responseArray['purchase_order_id'] ?? '';
                if (preg_match('/ORDER_(\d+)/', $purchase_order_id, $matches)) {
                    header("Location: order_confirmation.php?order_id=" . $matches[1]);
                } else {
                    header("Location: order_confirmation.php");
                }
                exit();
                
            case 'Expired':
            case 'User canceled':
                //here you can write your logic to update the database
                $_SESSION['transaction_msg'] = '<script>
                        Swal.fire({
                            icon: "error",
                            title: "Transaction failed.",
                            showConfirmButton: false,
                            timer: 1500
                        });
                    </script>';
                header("Location: checkout.php");
                exit();
                
            default:
                //here you can write your logic to update the database
                $_SESSION['transaction_msg'] = '<script>
                        Swal.fire({
                            icon: "error",
                            title: "Transaction failed.",
                            showConfirmButton: false,
                            timer: 1500
                        });
                    </script>';
                header("Location: checkout.php");
                exit();
        }
    } else {
        // Handle error case
        $error_msg = isset($responseArray['error']) ? $responseArray['error'] : 'Payment verification failed.';
        $_SESSION['transaction_msg'] = '<script>
                Swal.fire({
                    icon: "error",
                    title: "Payment Error",
                    text: "' . htmlspecialchars($error_msg) . '",
                    showConfirmButton: false,
                    timer: 2000
                });
            </script>';
        header("Location: checkout.php");
        exit();
    }
} else {
    // No pidx provided
    header("Location: checkout.php");
    exit();
}
?>