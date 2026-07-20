document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const navbarToggler = document.querySelector('.navbar-toggler');
    const navbarNav = document.querySelector('#navbarNav');
    
    if (navbarToggler && navbarNav) {
        navbarToggler.addEventListener('click', function() {
            navbarNav.classList.toggle('show');
        });
    }
    
    // Add to cart functionality
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.id;
            fetch('/add-to-cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${productId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update cart count
                    document.querySelectorAll('.cart-count').forEach(el => {
                        el.textContent = data.cart_count;
                    });
                    
                    // Show toast notification
                    const toast = document.createElement('div');
                    toast.className = 'position-fixed bottom-0 end-0 p-3';
                    toast.innerHTML = `
                        <div class="toast show" role="alert">
                            <div class="toast-body">
                                Product added to cart!
                            </div>
                        </div>
                    `;
                    document.body.appendChild(toast);
                    
                    setTimeout(() => toast.remove(), 3000);
                }
            });
        });
    });
    
    // Initialize dropdowns
    const dropdowns = document.querySelectorAll('.dropdown-toggle');
    dropdowns.forEach(dropdown => {
        dropdown.addEventListener('click', function(e) {
            e.preventDefault();
            const menu = this.nextElementSibling;
            menu.classList.toggle('show');
        });
    });
});