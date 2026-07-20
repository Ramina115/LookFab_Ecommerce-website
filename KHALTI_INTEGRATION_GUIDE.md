# Khalti Payment Gateway Integration Guide

## Overview
This document outlines the Khalti payment gateway integration that has been applied to your LookFab e-commerce system.

## What Was Fixed

### 1. **Payment Request File (`payment_request.php`)**
   - Fixed undefined variable error by moving `json_encode()` inside the if block
   - Replaced hardcoded URLs with dynamic URL generation
   - Fixed authorization header format (`key` → `Key`)
   - Improved error handling with proper redirects

### 2. **Payment Response File (`payment_response.php`)**
   - Fixed authorization header format
   - Added comprehensive error handling
   - Updated redirect paths to use correct filenames
   - Integrated with order system using purchase_order_id

### 3. **Payment Verification File (`verify_khalti.php`)**
   - Fixed redirect path (`order-confirmation.php` → `order_confirmation.php`)
   - Improved error handling with curl error checking
   - Updated order status to use `processing` for successful payments
   - Added proper error messages and redirects

### 4. **Checkout File (`checkout.php`)**
   - **MAJOR UPDATE**: Changed from session-based cart to database cart
   - Fixed hardcoded URLs to use dynamic URL generation
   - Added user authentication check
   - Improved error handling for payment initiation failures
   - Fixed redirect path typo
   - Added order summary display

### 5. **Order Confirmation File (`order_confirmation.php`)**
   - Updated status checks to handle `processing`, `shipped`, and `delivered` statuses
   - Fixed status display logic to work with updated payment flow

## New Files Created

### 1. **`config/khalti.php`**
   Centralized configuration file for Khalti payment gateway containing:
   - API credentials (secret key)
   - Helper functions for URL generation
   - Payment initiation function
   - Payment verification function

### 2. **`setup_orders.php`**
   Database setup script to create/update orders and order_items tables with all required fields.

## Database Changes

The system now uses a **database-based cart** instead of session-based cart. The checkout process:
1. Reads cart items from the `cart` table
2. Creates order in `orders` table
3. Creates order items in `order_items` table
4. Clears cart from database
5. Initiates Khalti payment

### Required Database Tables

**Orders Table:**
- `id` (Primary Key)
- `user_id` (Foreign Key)
- `full_name`, `email`, `phone`
- `address`, `city`, `state`, `zip_code`
- `payment_method` (default: 'khalti')
- `status` (default: 'pending', values: pending, processing, shipped, delivered, cancelled)
- `total_amount`
- `created_at`, `updated_at`

**Order Items Table:**
- `id` (Primary Key)
- `order_id` (Foreign Key)
- `product_id` (Foreign Key)
- `quantity`
- `price`
- `created_at`

## Setup Instructions

### Step 1: Run Database Setup
```bash
# Visit in browser or run via command line:
http://localhost/lookfab/setup.php
http://localhost/lookfab/setup_orders.php
```

### Step 2: Configure Khalti Credentials
Edit `config/khalti.php` and update:
```php
define('KHALTI_SECRET_KEY', 'your_actual_secret_key_here');
```

Get your keys from: https://khalti.com/merchant/account/apikey/

### Step 3: Test the Integration

1. **Add products to cart** (via database cart)
2. **Go to checkout** (`checkout.php`)
3. **Fill shipping information**
4. **Click "Pay with Khalti"**
5. **Complete payment on Khalti gateway**
6. **Verify order status** on order confirmation page

## Payment Flow

```
User adds items to cart (database)
    ↓
User clicks checkout
    ↓
User fills shipping form
    ↓
Order created in database (status: pending)
    ↓
Order items saved
    ↓
Cart cleared from database
    ↓
Khalti payment initiated
    ↓
User redirected to Khalti payment page
    ↓
User completes payment
    ↓
Khalti redirects to verify_khalti.php
    ↓
Payment verified via Khalti API
    ↓
Order status updated (pending → processing)
    ↓
User redirected to order_confirmation.php
```

## Important Notes

1. **API Key**: Always update `KHALTI_SECRET_KEY` in `config/khalti.php` with your actual key
2. **Test Mode**: Use Khalti test credentials during development
3. **Database Cart**: The system now uses database cart, not session cart
4. **Order Status**: Successful payments set status to `processing`, not `completed`
5. **Error Handling**: All payment errors are properly handled and displayed to users

## Troubleshooting

### Payment Not Initiating
- Check Khalti API key in `config/khalti.php`
- Verify database connection
- Check cart has items before checkout

### Order Status Not Updating
- Verify `verify_khalti.php` is accessible
- Check database `orders` table has `status` column
- Check Khalti API response

### Cart Empty Error
- Ensure user is logged in
- Verify cart table has items for the user
- Check database connection

## Support

For Khalti API documentation: https://docs.khalti.com/
For issues with this integration, check:
- PHP error logs
- Browser console for JavaScript errors
- Database connection status


