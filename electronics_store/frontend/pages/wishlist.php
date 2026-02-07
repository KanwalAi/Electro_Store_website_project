<?php
session_start();
include('../../backend/includes/db.php');

if(!isset($_SESSION['user_id'])) {
    header('location:../../backend/pages/login.php');
    exit;
}

$page_title = 'My Wishlist';
include('../../backend/includes/header.php');

$user_id = intval($_SESSION['user_id']);

// Create wishlist table if it doesn't exist
$createTableSQL = "CREATE TABLE IF NOT EXISTS wishlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    added_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_product (user_id, product_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
mysqli_query($conn, $createTableSQL);

// Get wishlist items
$wishlist = mysqli_query($conn, "
    SELECT p.*, w.added_at 
    FROM wishlist w 
    JOIN products p ON w.product_id = p.id 
    WHERE w.user_id=$user_id 
    ORDER BY w.added_at DESC
");

if (!$wishlist) {
    $wishlist = mysqli_query($conn, "SELECT p.*, NULL as added_at FROM products p LIMIT 0");
}

$wishlist_count = $wishlist ? mysqli_num_rows($wishlist) : 0;
?>

<div class="container my-5">
    <div class="section-header">
        <h2><i class="fas fa-heart"></i> My Wishlist</h2>
    </div>

    <?php if($wishlist_count > 0): ?>
    <p class="text-muted mb-4"><?php echo $wishlist_count; ?> item(s) in your wishlist</p>

    <div class="row">
        <?php while($item = mysqli_fetch_assoc($wishlist)): ?>
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card shadow-lg border-0 h-100">
                <img src="../assets/images/<?php echo htmlspecialchars($item['image']); ?>"
                    alt="<?php echo htmlspecialchars($item['name']); ?>" class="card-img-top"
                    style="height: 200px; object-fit: cover;"
                    onerror="this.src='https://via.placeholder.com/200?text=No+Image'">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($item['name']); ?></h5>
                    <p class="text-muted mb-2"><?php echo htmlspecialchars($item['brand']); ?></p>
                    <p class="price mb-3"><strong>$<?php echo number_format($item['price'], 2); ?></strong></p>
                    <p class="stock-info <?php echo ($item['stock'] > 0 ? 'in-stock' : 'out-of-stock'); ?>">
                        <?php echo ($item['stock'] > 0 ? 'In Stock (' . $item['stock'] . ')' : 'Out of Stock'); ?>
                    </p>
                    <small class="text-muted">Added: <?php echo date('M d, Y', strtotime($item['added_at'])); ?></small>
                    <div class="card-actions mt-3">
                        <?php if($item['stock'] > 0): ?>
                        <button class="btn btn-primary btn-sm flex-grow-1"
                            onclick="addToCart(<?php echo $item['id']; ?>)">
                            <i class="fas fa-cart-plus"></i> Add to Cart
                        </button>
                        <?php endif; ?>
                        <button class="btn btn-danger btn-sm" onclick="removeFromWishlist(<?php echo $item['id']; ?>)">
                            <i class="fas fa-trash"></i> Remove
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
    <?php else: ?>
    <div class="alert alert-info text-center py-5">
        <h4><i class="fas fa-heart-broken"></i> Your wishlist is empty</h4>
        <p class="mb-0">Add out-of-stock items to your wishlist to keep track of products you're interested in.</p>
        <a href="shop.php" class="btn btn-primary mt-3"><i class="fas fa-shopping-bag"></i> Continue Shopping</a>
    </div>
    <?php endif; ?>
</div>

<script>
function addToWishlist(productId) {
    fetch('../../backend/api/wishlist.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'action=add&product_id=' + productId
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert('Added to wishlist!');
                location.reload();
            } else {
                alert(data.message || 'Error adding to wishlist');
            }
        })
        .catch(error => alert('Error: ' + error));
}

function removeFromWishlist(productId) {
    if (confirm('Remove from wishlist?')) {
        fetch('../../backend/api/wishlist.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'action=remove&product_id=' + productId
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    location.reload();
                } else {
                    alert(data.message || 'Error removing from wishlist');
                }
            })
            .catch(error => alert('Error: ' + error));
    }
}
</script>

<?php include('../../backend/includes/footer.php'); ?>