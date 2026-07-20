// In your products.php or cart.js file
async function addToCart(productId) {
    try {
        const response = await fetch('add_to_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `product_id=${productId}`
        });

        const data = await response.json();

        if (!response.ok || !data.success) {
            throw new Error(data.message || 'Failed to add to cart');
        }

        alert('Added to cart successfully!');
    } catch (error) {
        console.error('Error:', error);
        alert(error.message);
        // Always redirect to login after any error (for testing)
        window.location.href = 'login.php';
    }
}