<?php
/**
 * Khalti Payment Gateway Configuration
 * 
 * Replace the secret key with your actual Khalti secret key
 * You can get your keys from: https://khalti.com/merchant/account/apikey/
 */

// Khalti API Configuration
define('KHALTI_SECRET_KEY', 'live_secret_key_68791341fdd94846a146f0457ff7b455');
define('KHALTI_API_URL', 'https://a.khalti.com/api/v2/epayment/');

// Payment URLs
function getKhaltiReturnUrl($order_id = null) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    // Get the script directory (root of the project)
    $script_dir = dirname($_SERVER['SCRIPT_NAME']);
    $base_url = $protocol . '://' . $host . ($script_dir !== '/' ? $script_dir : '');
    $base_url = rtrim($base_url, '/');
    
    if ($order_id) {
        return $base_url . '/verify_khalti.php?order_id=' . $order_id;
    }
    return $base_url . '/payment_response.php';
}

function getKhaltiWebsiteUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    // Get the script directory (root of the project)
    $script_dir = dirname($_SERVER['SCRIPT_NAME']);
    $base_url = $protocol . '://' . $host . ($script_dir !== '/' ? $script_dir : '');
    return rtrim($base_url, '/') . '/';
}

/**
 * Initiate Khalti Payment
 * 
 * @param array $paymentData Payment data array
 * @return array Response array with payment_url or error
 */
function initiateKhaltiPayment($paymentData) {
    $url = KHALTI_API_URL . 'initiate/';
    
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($paymentData),
        CURLOPT_HTTPHEADER => array(
            'Authorization: Key ' . KHALTI_SECRET_KEY,
            'Content-Type: application/json',
        ),
    ));
    
    $response = curl_exec($curl);
    $curl_error = curl_error($curl);
    curl_close($curl);
    
    if ($curl_error) {
        return ['error' => $curl_error];
    }
    
    $responseArray = json_decode($response, true);
    return $responseArray ?: ['error' => 'Invalid response'];
}

/**
 * Verify Khalti Payment
 * 
 * @param string $pidx Payment ID from Khalti
 * @return array Response array with payment status
 */
function verifyKhaltiPayment($pidx) {
    $url = KHALTI_API_URL . 'lookup/';
    
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode(['pidx' => $pidx]),
        CURLOPT_HTTPHEADER => array(
            'Authorization: Key ' . KHALTI_SECRET_KEY,
            'Content-Type: application/json',
        ),
    ));
    
    $response = curl_exec($curl);
    $curl_error = curl_error($curl);
    curl_close($curl);
    
    if ($curl_error) {
        return ['error' => $curl_error];
    }
    
    $responseArray = json_decode($response, true);
    return $responseArray ?: ['error' => 'Invalid response'];
}
?>

