// Global JavaScript for ElectroStore
console.log('app.js loaded');

// Add to cart function (reads quantity from page when available)
function addToCart(productId, quantity) {
    // If caller didn't pass quantity, try to read `#quantity` input on the page
    if (typeof quantity === 'undefined') {
        const qEl = document.getElementById('quantity');
        quantity = qEl ? parseInt(qEl.value, 10) || 1 : 1;
    }

    console.log('addToCart called - productId:', productId, 'quantity:', quantity);
    const body = new URLSearchParams();
    body.append('product_id', productId);
    body.append('quantity', quantity);
    console.log('Sending to backend:', body.toString());

    fetch('../../backend/api/api_add_to_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: body.toString()
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            showAlert('Product added to cart!', 'success');
            updateCartCount();
        } else {
            showAlert(data.message || 'Error adding to cart', 'danger');
        }
    })
    .catch(error => console.error('Error:', error));
}

// Wrapper function for product-details page (reads quantity and stays on page)
function addToCartWithQty(productId) {
    const qEl = document.getElementById('quantity');
    const qty = qEl ? parseInt(qEl.value, 10) || 1 : 1;
    console.log('addToCartWithQty - productId:', productId, 'quantity from input:', qty);
    addToCart(productId, qty);
}

// Add to wishlist function
function addToWishlist(elOrId, maybeId) {
    // Support two call styles: addToWishlist(productId) or addToWishlist(buttonElem, productId)
    var btn = null;
    var productId = null;
    if (typeof maybeId === 'undefined') {
        productId = elOrId;
    } else {
        btn = elOrId;
        productId = maybeId;
    }

    console.log('addToWishlist called for', productId);
    const formData = new FormData();
    formData.append('action', 'add');
    formData.append('product_id', productId);

    if (btn) {
        btn.disabled = true;
        var oldHtml = btn.innerHTML;
        btn.dataset._oldhtml = oldHtml;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
    }

    fetch('../../backend/api/wishlist.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (response.status === 401) {
            showAlert('Please log in to use wishlist', 'warning');
            // optionally redirect to login
            // window.location.href = '../../backend/pages/login.php';
            return Promise.reject(new Error('unauth'));
        }
        if (!response.ok) {
            return response.text().then(t => Promise.reject(new Error(t || ('HTTP ' + response.status))));
        }
        return response.json();
    })
    .then(data => {
        if(data.status === 'success') {
            showAlert('Added to wishlist!', 'success');
            if (btn) {
                btn.innerHTML = '<i class="fas fa-heart"></i> Added';
                btn.classList.remove('btn-warning');
                btn.classList.add('btn-success');
                btn.disabled = true;
            }
        } else {
            showAlert(data.message || 'Error adding to wishlist', 'danger');
            if (btn) {
                btn.disabled = false;
                if (btn.dataset._oldhtml) btn.innerHTML = btn.dataset._oldhtml;
            }
        }
    })
    .catch(error => {
        if (error.message === 'unauth') return;
        console.error('Wishlist Error:', error);
        showAlert('Error adding to wishlist: ' + error.message, 'danger');
        if (btn) {
            btn.disabled = false;
            if (btn.dataset._oldhtml) btn.innerHTML = btn.dataset._oldhtml;
        }
    });
}

// Remove from cart function
function removeFromCart(productId) {
    if(confirm('Remove this item from cart?')) {
        fetch('../../backend/api/api_remove_from_cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'product_id=' + productId
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                location.reload();
            }
        })
        .catch(error => console.error('Error:', error));
    }
}

// Update cart count in navbar
function updateCartCount() {
    fetch('../../backend/api/api_get_cart_count.php')
    .then(response => response.json())
    .then(data => {
        const cartBadge = document.querySelector('.navbar .badge');
        if(cartBadge) {
            if(data.count > 0) {
                cartBadge.textContent = data.count;
            } else {
                cartBadge.remove();
            }
        }
    });
}

// Show alert function
function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3`;
    alertDiv.style.zIndex = '9999';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(alertDiv);
    
    setTimeout(() => alertDiv.remove(), 4000);
}

// Form validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    if(!form.checkValidity() === false) {
        event.preventDefault();
        event.stopPropagation();
    }
    form.classList.add('was-validated');
}

// Format currency
function formatCurrency(value) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD'
    }).format(value);
}

// Document ready
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
