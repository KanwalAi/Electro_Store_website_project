<footer class="bg-dark text-light py-5 mt-5">
    <div class="container">
        <div class="row">
            <div class="col-md-4">
                <h5><i class="fas fa-bolt text-primary"></i> ElectroStore</h5>
                <p>Your trusted partner for quality electronic components and parts.</p>
                <div class="social-links">
                    <a href="#" class="text-light me-2"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="text-light me-2"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="text-light me-2"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-light"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
            <div class="col-md-4">
                <h5>Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="../../frontend/pages/shop.php" class="text-light text-decoration-none"><i
                                class="fas fa-arrow-right"></i> Shop</a></li>
                    <li><a href="../../frontend/pages/contact.php" class="text-light text-decoration-none"><i
                                class="fas fa-arrow-right"></i> Contact Us</a></li>
                    <li><a href="#" class="text-light text-decoration-none"><i class="fas fa-arrow-right"></i> About
                            Us</a></li>
                    <li><a href="#" class="text-light text-decoration-none"><i class="fas fa-arrow-right"></i> Terms &
                            Conditions</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h5>Contact Info</h5>
                <p>
                    <i class="fas fa-phone text-primary"></i> +1 (555) 123-4567<br>
                    <i class="fas fa-envelope text-primary"></i> admin123@gmail.com<br>
                    <i class="fas fa-map-marker-alt text-primary"></i> 123 Tech Street, Silicon Valley, CA
                </p>
            </div>
        </div>
        <hr class="bg-secondary">
        <div class="row">
            <div class="col-md-6">
                <p>&copy; 2026 ElectroStore. All rights reserved.</p>
            </div>
            <div class="col-md-6 text-md-end">
                <p>Secure Payment | Fast Shipping | Quality Guaranteed</p>
            </div>
        </div>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="/electronics_store/frontend/assets/app.js"></script>
<script>
// Fallback: ensure addToWishlist exists even if app.js failed to load
if (typeof window.addToWishlist === 'undefined') {
    window.addToWishlist = function(elOrId, maybeId) {
        var btn = null;
        var productId = null;
        if (typeof maybeId === 'undefined') {
            productId = elOrId;
        } else {
            btn = elOrId;
            productId = maybeId;
        }

        var url = '/electronics_store/backend/api/wishlist.php';
        var body = 'action=add&product_id=' + encodeURIComponent(productId);

        if (btn) {
            btn.disabled = true;
            btn.dataset._oldhtml = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
        }

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: body
        }).then(function(res) {
            if (res.status === 401) {
                alert('Please log in to use wishlist');
                throw new Error('unauth');
            }
            if (!res.ok) return res.text().then(function(t) {
                throw new Error(t || ('HTTP ' + res.status));
            });
            return res.json();
        }).then(function(data) {
            if (data.status === 'success') {
                if (btn) {
                    btn.innerHTML = '<i class="fas fa-heart"></i> Added';
                    btn.classList.remove('btn-warning');
                    btn.classList.add('btn-success');
                    btn.disabled = true;
                } else {
                    alert('Added to wishlist!');
                }
            } else {
                alert(data.message || 'Error adding to wishlist');
                if (btn) {
                    btn.disabled = false;
                    if (btn.dataset._oldhtml) btn.innerHTML = btn.dataset._oldhtml;
                }
            }
        }).catch(function(err) {
            if (err.message === 'unauth') return;
            alert('Error adding to wishlist: ' + err.message);
            if (btn) {
                btn.disabled = false;
                if (btn.dataset._oldhtml) btn.innerHTML = btn.dataset._oldhtml;
            }
        });
    };
}
</script>