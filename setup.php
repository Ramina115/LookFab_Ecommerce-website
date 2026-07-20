<?php
include 'config/database.php';

// Create database
$sql = "CREATE DATABASE IF NOT EXISTS lookfab_db";
if (mysqli_query($conn, $sql)) {
    echo "Database created successfully<br>";
} else {
    echo "Error creating database: " . mysqli_error($conn) . "<br>";
}

// Select database
mysqli_select_db($conn, "lookfab_db");

// Users table
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(30) NOT NULL,
    email VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    user_type ENUM('admin','customer') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if (mysqli_query($conn, $sql)) {
    echo "Users table created successfully<br>";
} else {
    echo "Error creating users table: " . mysqli_error($conn) . "<br>";
}

// Products table
$sql = "CREATE TABLE IF NOT EXISTS products (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    category ENUM('men','women','children') NOT NULL,
    image VARCHAR(255),
    stock INT(6) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
if (mysqli_query($conn, $sql)) {
    echo "Products table created successfully<br>";
} else {
    echo "Error creating products table: " . mysqli_error($conn) . "<br>";
}

// Cart table
// In your setup.php file, modify the cart table creation code like this:

// First create the users and products tables
$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(30) NOT NULL,
    email VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    user_type ENUM('admin','customer') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
mysqli_query($conn, $sql);

$sql = "CREATE TABLE IF NOT EXISTS products (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    category ENUM('men','women','children') NOT NULL,
    image VARCHAR(255),
    stock INT(6) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
mysqli_query($conn, $sql);

// Then create the cart table
$sql = "CREATE TABLE IF NOT EXISTS cart (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT(6) UNSIGNED NOT NULL,
    product_id INT(6) UNSIGNED NOT NULL,
    quantity INT(6) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB";
mysqli_query($conn, $sql);

// Orders table
$sql = "CREATE TABLE IF NOT EXISTS orders (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT(6) UNSIGNED NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    address TEXT NOT NULL,
    city VARCHAR(50) NOT NULL,
    state VARCHAR(50),
    zip_code VARCHAR(20),
    payment_method VARCHAR(50) DEFAULT 'khalti',
    status VARCHAR(20) DEFAULT 'pending',
    total_amount DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
if (mysqli_query($conn, $sql)) {
    echo "Orders table created successfully<br>";
} else {
    echo "Error creating orders table: " . mysqli_error($conn) . "<br>";
}

// Order Items table
$sql = "CREATE TABLE IF NOT EXISTS order_items (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT(6) UNSIGNED NOT NULL,
    product_id INT(6) UNSIGNED NOT NULL,
    quantity INT(6) NOT NULL DEFAULT 1,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
if (mysqli_query($conn, $sql)) {
    echo "Order items table created successfully<br>";
} else {
    echo "Error creating order_items table: " . mysqli_error($conn) . "<br>";
}

// Add admin user
$password = password_hash('admin123', PASSWORD_DEFAULT);
$sql = "INSERT INTO users (username, email, password, user_type) 
        VALUES ('admin', 'admin@lookfab.com', '$password', 'admin') 
        ON DUPLICATE KEY UPDATE password='$password', user_type='admin'";
if (mysqli_query($conn, $sql)) {
    echo "Admin user created/updated successfully<br>";
} else {
    echo "Error creating admin user: " . mysqli_error($conn) . "<br>";
}

mysqli_close($conn);
echo "Setup completed!";
?>