<?php
session_start();
require_once 'config/khalti.php';

if (isset($_POST['submit'])) {
    $amount = $_POST['inputAmount4']*100; // convert the amount to paisa as the amount should be in paisa for khalti
    $purchase_order_id = $_POST['inputPurchasedOrderId4'];
    $purchase_order_name = $_POST['inputPurchasedOrderName'];
    $name = $_POST['inputName'];
    $email = $_POST['inputEmail'];
    $phone = $_POST['inputPhone'];

    //here validate the data
    if(empty($amount) || empty($purchase_order_id) || empty($purchase_order_name) || empty($name) || empty($email) || empty($phone)){
        $_SESSION["validate_msg"] = '<script>
        Swal.fire({
            icon: "error",
            title: "All fields are required",
            showConfirmButton: false,
            timer: 1500
        });
    </script>';
        header("Location: checkout.php");
        exit();
    }
    //check if the amount is a number
    if(!is_numeric($amount)){
        $_SESSION["validate_msg"] = '<script>
        Swal.fire({
            icon: "error",
            title: "Amount must be a number",
            showConfirmButton: false,
            timer: 1500
        });
    </script>';
        header("Location: checkout.php");
        exit();
    }

    //check if the phone number is a number
    if(!is_numeric($phone)){
        $_SESSION["validate_msg"] = '<script>
        Swal.fire({
            icon: "error",
            title: "Phone must be a number",
            showConfirmButton: false,
            timer: 1500
        });
    </script>';
        header("Location: checkout.php");
        exit();
    }

    //check if the email is valid
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $_SESSION["validate_msg"] = '<script>
        Swal.fire({
            icon: "error",
            title: "Email is not valid",
            showConfirmButton: false,
            timer: 1500
        });
    </script>';
        header("Location: checkout.php");
        exit();
    }

    $postFields = array(
        "return_url" => getKhaltiReturnUrl(),
        "website_url" => getKhaltiWebsiteUrl(),
        "amount" => $amount,
        "purchase_order_id" => $purchase_order_id,
        "purchase_order_name" => $purchase_order_name,
        "customer_info" => array(
            "name" => $name,
            "email" => $email,
            "phone" => $phone
        )
    );

    $responseArray = initiateKhaltiPayment($postFields);

    if (isset($responseArray['error'])) {
        $_SESSION["validate_msg"] = '<script>
            Swal.fire({
                icon: "error",
                title: "Payment Error",
                text: "' . htmlspecialchars($responseArray['error']) . '",
                showConfirmButton: false,
                timer: 2000
            });
        </script>';
        header("Location: checkout.php");
        exit();
    } elseif (isset($responseArray['payment_url'])) {
        // Redirect the user to the payment page
        header('Location: ' . $responseArray['payment_url']);
        exit();
    } else {
        $_SESSION["validate_msg"] = '<script>
            Swal.fire({
                icon: "error",
                title: "Payment Error",
                text: "Unexpected response from payment gateway",
                showConfirmButton: false,
                timer: 2000
            });
        </script>';
        header("Location: checkout.php");
        exit();
    }
} else {
    // If form not submitted, redirect to checkout
    header("Location: checkout.php");
    exit();
}