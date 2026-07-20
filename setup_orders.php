<?php
/**
 * Database Setup Script for Orders and Order Items Tables
 * Run this script once to create the necessary tables for the order system
 */

require_once 'config/database.php';

// Select database
mysqli_select_db($conn, "lookfab_db");

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
    echo "Orders table created/verified successfully<br>";
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
    echo "Order items table created/verified successfully<br>";
} else {
    echo "Error creating order_items table: " . mysqli_error($conn) . "<br>";
}

// Add status column if it doesn't exist (for existing orders table)
$check_status = mysqli_query($conn, "SHOW COLUMNS FROM orders LIKE 'status'");
if (mysqli_num_rows($check_status) == 0) {
    $sql = "ALTER TABLE orders ADD COLUMN status VARCHAR(20) DEFAULT 'pending' AFTER payment_method";
    if (mysqli_query($conn, $sql)) {
        echo "Status column added to orders table<br>";
    } else {
        echo "Error adding status column: " . mysqli_error($conn) . "<br>";
    }
}

// Add email, phone, state, zip_code columns if they don't exist
$columns_to_add = [
    'email' => "ALTER TABLE orders ADD COLUMN email VARCHAR(100) AFTER full_name",
    'phone' => "ALTER TABLE orders ADD COLUMN phone VARCHAR(20) AFTER email",
    'state' => "ALTER TABLE orders ADD COLUMN state VARCHAR(50) AFTER city",
    'zip_code' => "ALTER TABLE orders ADD COLUMN zip_code VARCHAR(20) AFTER state"
];

foreach ($columns_to_add as $column => $alter_sql) {
    $check_column = mysqli_query($conn, "SHOW COLUMNS FROM orders LIKE '$column'");
    if (mysqli_num_rows($check_column) == 0) {
        if (mysqli_query($conn, $alter_sql)) {
            echo ucfirst($column) . " column added to orders table<br>";
        } else {
            echo "Error adding $column column: " . mysqli_error($conn) . "<br>";
        }
    }
}

mysqli_close($conn);
echo "<br>Orders setup completed!";
?>


