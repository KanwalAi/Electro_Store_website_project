<?php 
$page_title = 'Home';
include('../../backend/includes/header.php');
?>

<div class="hero-section">
    <div class="container">
        <h1>High-Quality Electronic Components</h1>
        <p>From Microchips to Resistors, we power your innovation.</p>
        <a href="shop.php" class="btn btn-primary">Browse Our Catalog</a>
    </div>
</div>

<div class="container section">
    <section class="featured">
        <h2>Featured Products</h2>
        <div class="product-grid">
            <?php
            $featured = mysqli_query($conn, "SELECT * FROM products ORDER BY created_at DESC LIMIT 12");

            if ($featured && mysqli_num_rows($featured) > 0) {
                while($row = mysqli_fetch_assoc($featured)) {
                    // compute fresh aggregate from reviews table
                    $pid = intval($row['id']);
                    $aggRes = mysqli_query($conn, "SELECT COUNT(*) AS cnt, AVG(rating) AS avg_rating FROM reviews WHERE product_id={$pid}");
                    $reviews_count = 0;
                    $rating_val = 0;
                    if ($aggRes && mysqli_num_rows($aggRes) > 0) {
                        $aggRow = mysqli_fetch_assoc($aggRes);
                        $reviews_count = intval($aggRow['cnt']);
                        $rating_val = $aggRow['avg_rating'] !== null ? floatval($aggRow['avg_rating']) : 0;
                    }
                    if ($reviews_count > 0) {
                        $rounded = round($rating_val);
                        $stars = '';
                        for ($i = 1; $i <= 5; $i++) {
                            $stars .= ($i <= $rounded) ? "<i class='fas fa-star text-warning'></i>" : "<i class='far fa-star text-muted'></i>";
                        }
                        $rating_display = $stars . ' <span class="ms-1">' . round($rating_val, 1) . ' (' . $reviews_count . ')</span>';
                    } else {
                        $rating_display = "<span class='text-muted'>No ratings</span>";
                    }
                    $actionBtn = '';
                    if ($row['stock'] > 0) {
                        $actionBtn = "<button class='btn btn-primary btn-small flex-grow-1' onclick='addToCart({$row['id']})'>\n                                        <i class='fas fa-cart-plus'></i> Add to Cart\n                                    </button>";
                    } else {
                            $actionBtn = "<button class='btn btn-warning btn-small flex-grow-1' onclick=\"addToWishlist(this, {$row['id']})\">\n                                        <i class='fas fa-heart'></i> Add To Wishlist\n                                    </button>";
                    }

                    echo "<div class='product-card'>
                            <img src='../assets/images/{$row['image']}' alt='{$row['name']}' onerror=\"this.src='https://via.placeholder.com/200?text=No+Image'\">
                            <div class='card-body'>
                                <h3>{$row['name']}</h3>
                                <p class='brand'><strong>{$row['brand']}</strong></p>
                                <p class='price'>\${$row['price']}</p>
                                <p class='rating'>{$rating_display}</p>
                                <p class='stock-info " . ($row['stock'] > 0 ? 'in-stock' : 'out-of-stock') . "'>" . ($row['stock'] > 0 ? 'In Stock (' . $row['stock'] . ')' : 'Out of Stock') . "</p>
                                <div class='card-actions'>
                                    " . $actionBtn . "
                                    <a href='product-details.php?id={$row['id']}' class='btn btn-secondary btn-small'>View</a>
                                </div>
                            </div>
                          </div>";
                }
            } else {
                echo "<p class='alert alert-info'>No products available yet.</p>";
            }
            ?>
        </div>
    </section>
</div>

<div class="bg-light py-5">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-4 mb-4">
                <h4><i class="fas fa-shipping-fast text-primary"></i> Fast Shipping</h4>
                <p>Get your orders delivered quickly and safely</p>
            </div>
            <div class="col-md-4 mb-4">
                <h4><i class="fas fa-lock text-primary"></i> Secure Payment</h4>
                <p>Your payment information is always secure</p>
            </div>
            <div class="col-md-4 mb-4">
                <h4><i class="fas fa-headset text-primary"></i> 24/7 Support</h4>
                <p>We're here to help you anytime, anywhere</p>
            </div>
        </div>
    </div>
</div>

<?php include('../../backend/includes/footer.php'); ?>