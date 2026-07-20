<?php
// includes/session_manager.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    
    // Optional security enhancements
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}
?>