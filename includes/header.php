<?php
require_once 'session_manager.php';
$base_url = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LookFab - Boutique Fashion</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css?family=Playfair+Display:700,900|Lato:400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?= $base_url ?>/assets/css/responsive.css">
</head>
<body>
    <header class="header">
        <div class="header-flex">
            <div class="logo">
                <a href="index.php">
                    <i class="fas fa-gem logo-icon"></i>
                    <span class="logo-text">LookFab</span>
                </a>
            </div>
            <nav class="navbar" id="main-navbar">
                <a href="index.php"><i class="fas fa-home"></i> Home</a>
                <a href="shop.php"><i class="fas fa-shopping-bag"></i> Shop</a>
                <a href="products.php"><i class="fas fa-tshirt"></i> Products</a>
                <a href="about.php"><i class="fas fa-info-circle"></i> About</a>
                <a href="contact.php"><i class="fas fa-envelope"></i> Contact</a>
            </nav>
            <div class="user-actions">
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="account.php"><i class="fas fa-user"></i> Account</a>
                    <a href="cart.php"><i class="fas fa-shopping-cart"></i></a>
                    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                <?php else: ?>
                    <a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
                    <a href="register.php"><i class="fas fa-user-plus"></i> Register</a>
                <?php endif; ?>
            </div>
            <div class="mobile-menu-btn" id="mobile-menu-btn">
                <i class="fas fa-bars"></i>
            </div>
        </div>
        <div class="header-search">
            <form action="products.php" method="GET" class="search-form">
                <input type="text" name="search" placeholder="Search products..." class="search-input">
                <button type="submit"><i class="fas fa-search"></i></button>
            </form>
        </div>
    </header>
    <main>
<script>
// Responsive mobile menu toggle
const menuBtn = document.getElementById('mobile-menu-btn');
const navbar = document.getElementById('main-navbar');
menuBtn.addEventListener('click', function() {
    navbar.classList.toggle('active');
});
</script>