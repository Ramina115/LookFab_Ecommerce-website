// AJAX functions for the website

// Add to cart function
function addToCart(productId, quantity = 1) {
    fetch('php/cart_functions.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=add&product_id=${productId}&quantity=${quantity}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Product added to cart!');
            updateCartCount();
        } else {
            alert(data.message || 'Error adding to cart');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
    });
}

// Remove from cart function
function removeFromCart(cartId) {
    if (confirm('Are you sure you want to remove this item from your cart?')) {
        fetch('php/cart_functions.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=remove&cart_id=${cartId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Refresh the cart or remove the item from the DOM
                location.reload();
            } else {
                alert(data.message || 'Error removing item');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred');
        });
    }
}

// Update cart quantity
function updateCartQuantity(cartId, quantity) {
    if (quantity < 1) {
        removeFromCart(cartId);
        return;
    }

    fetch('php/cart_functions.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=update&cart_id=${cartId}&quantity=${quantity}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the total price if needed
            if (data.newTotal) {
                document.querySelector(`.cart-item-total[data-id="${cartId}"]`).textContent = data.newTotal;
                document.querySelector('.cart-summary-total').textContent = data.grandTotal;
            }
        } else {
            alert(data.message || 'Error updating quantity');
            location.reload(); // Refresh to get correct values
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred');
    });
}

// Search products
function searchProducts(query) {
    if (query.length < 2) return; // Don't search for very short queries
    
    fetch(`php/product_functions.php?action=search&query=${encodeURIComponent(query)}`)
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displaySearchResults(data.results);
        }
    })
    .catch(error => console.error('Error:', error));
}

function displaySearchResults(results) {
    const searchResults = document.getElementById('search-results');
    if (!searchResults) return;
    
    searchResults.innerHTML = '';
    
    if (results.length === 0) {
        searchResults.innerHTML = '<div class="no-results">No products found</div>';
        return;
    }
    
    results.forEach(product => {
        const item = document.createElement('div');
        item.className = 'search-result-item';
        item.innerHTML = `
            <a href="product.php?id=${product.id}">
                <img src="assets/images/products/${product.image}" alt="${product.name}">
                <div>
                    <h4>${product.name}</h4>
                    <p>$${product.price}</p>
                </div>
            </a>
        `;
        searchResults.appendChild(item);
    });
    
    searchResults.style.display = 'block';
}

// Close search results when clicking outside
document.addEventListener('click', function(e) {
    const searchResults = document.getElementById('search-results');
    const searchInput = document.querySelector('.search-bar input');
    
    if (searchResults && searchInput && 
        !searchResults.contains(e.target) && 
        !searchInput.contains(e.target)) {
        searchResults.style.display = 'none';
    }
});