<?php
session_start();
include('../../backend/includes/db.php');

$statusMsg = '';
// Handle review submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    // Require user to be logged in to submit review
    if (!isset($_SESSION['user_id'])) {
        header('location:../../backend/pages/login.php');
        exit;
    }
    
    $pid = intval($_POST['product_id']);

    // create reviews table if it doesn't exist
    $createSql = "CREATE TABLE IF NOT EXISTS reviews (
        id INT AUTO_INCREMENT PRIMARY KEY,
        product_id INT NOT NULL,
        user_id INT DEFAULT NULL,
        name VARCHAR(150) DEFAULT NULL,
        rating TINYINT NOT NULL,
        comment TEXT,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $resCreate = mysqli_query($conn, $createSql);
    if ($resCreate === false) {
        $statusMsg = 'DB error creating reviews table: ' . mysqli_error($conn);
    }
    // ensure `name` column exists (older installs may have different schema)
    $colCheck = mysqli_query($conn, "SHOW COLUMNS FROM reviews LIKE 'name'");
    if ($colCheck && mysqli_num_rows($colCheck) === 0) {
        $alter = mysqli_query($conn, "ALTER TABLE reviews ADD COLUMN `name` VARCHAR(150) DEFAULT NULL");
        if ($alter === false) {
            $statusMsg = 'DB error adding name column to reviews: ' . mysqli_error($conn);
        }
    }

    $user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : null;
    $name = '';
    if ($user_id) {
        $uRes = mysqli_query($conn, "SELECT name FROM users WHERE id=$user_id LIMIT 1");
        if ($uRes && mysqli_num_rows($uRes)) {
            $uRow = mysqli_fetch_assoc($uRes);
            $name = mysqli_real_escape_string($conn, $uRow['name']);
        }
    } else {
        $name = mysqli_real_escape_string($conn, $_POST['name'] ?? 'Guest');
    }

    $rating = max(1, min(5, intval($_POST['rating'] ?? 5)));
    $comment = mysqli_real_escape_string($conn, $_POST['comment'] ?? '');

    $insertSql = "INSERT INTO reviews (`product_id`, `user_id`, `name`, `rating`, `comment`) VALUES ($pid, " . ($user_id ? $user_id : 'NULL') . ", '$name', $rating, '$comment')";
    $resInsert = mysqli_query($conn, $insertSql);
    if ($resInsert === false) {
        $statusMsg = 'DB error inserting review: ' . mysqli_error($conn);
        // do not redirect, show error below
    } else {
        // Recalculate product aggregate rating/count
        $agg = mysqli_query($conn, "SELECT COUNT(*) AS cnt, AVG(rating) AS avg_rating FROM reviews WHERE product_id=$pid");
        if ($agg && mysqli_num_rows($agg)) {
            $a = mysqli_fetch_assoc($agg);
            $cnt = intval($a['cnt']);
            $avg = $a['avg_rating'] ? floatval($a['avg_rating']) : 0;
            $resUp = mysqli_query($conn, "UPDATE products SET reviews=$cnt, rating=$avg WHERE id=$pid");
            if ($resUp === false) {
                $statusMsg = 'DB error updating product aggregates: ' . mysqli_error($conn);
                // do not redirect
            }
        }

        if (empty($statusMsg)) {
            header('Location: product-details.php?id=' . $pid);
            exit;
        }
        // else fall through and show $statusMsg
    }
}

$page_title = 'Product Details';
include('../../backend/includes/header.php');

// Handle Add to Cart with validation
$addError = '';
$addSuccess = '';
if(isset($_POST['add_to_cart_qty'])) {
    if(!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
    
    $pid = intval($_GET['id']);
    foreach($_POST['quantity'] as $product_id => $qty) {
        $product_id = intval($product_id);
        $qty = max(1, intval($qty));
        
        // Fetch product stock
        $pRes = mysqli_query($conn, "SELECT id, stock FROM products WHERE id = $product_id LIMIT 1");
        if($pRes && mysqli_num_rows($pRes) > 0) {
            $pRow = mysqli_fetch_assoc($pRes);
            $stock = intval($pRow['stock']);
            
            $current = isset($_SESSION['cart'][$product_id]) ? intval($_SESSION['cart'][$product_id]) : 0;
            $requested_total = $current + $qty;
            
            if ($requested_total > $stock) {
                $addError = "Only " . ($stock - $current) . " item(s) available (you already have " . $current . " in cart)";
            } else {
                $_SESSION['cart'][$product_id] = $requested_total;
                $addSuccess = 'Added ' . $qty . ' item(s) to cart!';
            }
        }
    }
    
    // Store messages in session and redirect to clear form state (like cart.php)
    $_SESSION['add_error_msg'] = $addError;
    $_SESSION['add_success_msg'] = $addSuccess;
    header("Location: product-details.php?id=" . $pid);
    exit;
}

if(isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $product = mysqli_query($conn, "SELECT * FROM products WHERE id='$id'");
    
    if($product && mysqli_num_rows($product) > 0) {
        $row = mysqli_fetch_assoc($product);
        
        // Get reviews (include guest reviews via LEFT JOIN)
        $reviews = mysqli_query($conn, "SELECT r.*, COALESCE(u.name, r.name) AS reviewer_name FROM reviews r LEFT JOIN users u ON r.user_id = u.id WHERE r.product_id='$id' ORDER BY r.created_at DESC");

        // compute up-to-date aggregate rating/count from reviews table
        $agg = mysqli_query($conn, "SELECT COUNT(*) AS cnt, AVG(rating) AS avg_rating FROM reviews WHERE product_id={$id}");
        $reviews_count = 0;
        $rating_val = 0;
        if ($agg && mysqli_num_rows($agg) > 0) {
            $ar = mysqli_fetch_assoc($agg);
            $reviews_count = intval($ar['cnt']);
            $rating_val = $ar['avg_rating'] !== null ? floatval($ar['avg_rating']) : 0;
        }
        // show status message for debugging/feedback
        if (!empty($statusMsg)) {
            echo '<div class="container my-3"><div class="alert alert-warning">' . htmlspecialchars($statusMsg) . '</div></div>';
        }
        
        // Check session messages from POST redirect
        $addError = isset($_SESSION['add_error_msg']) ? $_SESSION['add_error_msg'] : '';
        $addSuccess = isset($_SESSION['add_success_msg']) ? $_SESSION['add_success_msg'] : '';
        
        if (!empty($addError)) {
            echo '<div class="container my-3"><div class="alert alert-danger alert-dismissible fade show"><i class="fas fa-exclamation-circle"></i> ' . htmlspecialchars($addError) . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div></div>';
            $_SESSION['add_error_msg'] = '';
        }
        
        if (!empty($addSuccess)) {
            echo '<div class="container my-3"><div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check-circle"></i> ' . htmlspecialchars($addSuccess) . '<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div></div>';
            $_SESSION['add_success_msg'] = '';
        }
?>

<div class="container my-5">

    <div class="row">
        <div class="col-lg-5">
            <div class="card shadow-lg border-0">
                <img src="../assets/images/<?php echo $row['image']; ?>"
                    alt="<?php echo htmlspecialchars($row['name']); ?>" class="card-img-top"
                    style="height: 400px; object-fit: cover;"
                    onerror="this.src='https://via.placeholder.com/400?text=No+Image'">
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <h2><?php echo htmlspecialchars($row['name']); ?></h2>

                    <div class="my-3">
                        <span class="badge bg-info"><?php echo $row['category']; ?></span>
                        <span class="badge bg-secondary"><?php echo $row['brand']; ?></span>
                    </div>

                    <div class="mb-3">
                        <h3 class="text-primary">$<?php echo $row['price']; ?></h3>
                        <p class="text-muted">
                            <?php
                            // Prefer freshly computed aggregate if available
                            if (isset($reviews_count) && $reviews_count > 0) {
                                $rounded = round($rating_val);
                                for ($i = 1; $i <= 5; $i++) {
                                    echo ($i <= $rounded) ? '<i class="fas fa-star text-warning"></i>' : '<i class="far fa-star text-muted"></i>';
                                }
                                echo ' <span class="ms-1">' . round($rating_val, 1) . ' (' . $reviews_count . ')</span>';
                            } else {
                                echo '<span class="text-muted">No ratings yet</span>';
                            }
                            ?>
                        </p>
                    </div>

                    <p class="lead"><?php echo htmlspecialchars($row['description'] ?? 'No description available'); ?>
                    </p>

                    <div class="alert alert-info">
                        <strong>Stock Status:</strong>
                        <span class="<?php echo $row['stock'] > 0 ? 'text-success' : 'text-danger'; ?>">
                            <?php echo $row['stock'] > 0 ? 'In Stock (' . $row['stock'] . ' available)' : 'Out of Stock'; ?>
                        </span>
                    </div>

                    <?php $inStock = ($row['stock'] > 0); ?>

                    <?php if ($inStock): ?>
                    <form method="POST" action="product-details.php?id=<?php echo $row['id']; ?>" style="display: flex; gap: 10px; align-items: flex-end;" onsubmit="return event.submitter && event.submitter.name === 'add_to_cart_qty';">
                        <div class="mb-0">
                            <label for="add_quantity" class="form-label">Quantity:</label>
                            <input type="number" id="add_quantity" name="quantity[<?php echo $row['id']; ?>]" min="1"
                                max="<?php echo $row['stock']; ?>" value="1" class="form-control"
                                style="max-width: 100px;" onkeypress="if(event.key==='Enter') event.preventDefault();">
                        </div>
                        <button type="submit" name="add_to_cart_qty" class="btn btn-primary btn-lg flex-grow-1">
                            <i class="fas fa-cart-plus"></i> Add to Cart
                        </button>
                    </form>
                    <?php else: ?>
                    <button class="btn btn-warning btn-lg w-100"
                        onclick="addToWishlist(this, <?php echo $row['id']; ?>)">
                        <i class="fas fa-heart"></i> Add To Wishlist
                    </button>
                    <?php endif; ?>

                    <a href="shop.php" class="btn btn-secondary btn-lg w-100 mt-3">
                        <i class="fas fa-arrow-left"></i> Back to Shop
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Reviews Section -->
    <div class="row mt-5">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-comments"></i> Customer Reviews
                        (<?php echo $reviews ? mysqli_num_rows($reviews) : 0; ?>)</h5>
                </div>
                <div class="card-body">
                    <div class="mt-4 mb-4">
                        <h6>Write a review</h6>
                        <form method="post" action="product-details.php?id=<?php echo $id; ?>">
                            <?php if(!isset($_SESSION['user_id'])): ?>
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <?php endif; ?>
                            <input type="hidden" name="product_id" value="<?php echo $id; ?>">
                            <div class="mb-3">
                                <label class="form-label">Rating</label>
                                <select name="rating" class="form-select" required>
                                    <option value="5">5 - Excellent</option>
                                    <option value="4">4 - Very Good</option>
                                    <option value="3">3 - Good</option>
                                    <option value="2">2 - Fair</option>
                                    <option value="1">1 - Poor</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Review</label>
                                <textarea name="comment" rows="4" class="form-control" required></textarea>
                            </div>
                            <button type="submit" name="submit_review" class="btn btn-primary">Submit Review</button>
                        </form>
                    </div>
                    <hr>
                    <?php if($reviews && mysqli_num_rows($reviews) > 0): ?>
                    <?php while($review = mysqli_fetch_assoc($reviews)): ?>
                    <div class="mb-4 pb-4 border-bottom">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1"><?php echo htmlspecialchars($review['reviewer_name'] ?? 'Guest'); ?>
                                </h6>
                                <small
                                    class="text-muted"><?php echo date('M d, Y', strtotime($review['created_at'])); ?></small>
                            </div>
                            <span class="badge bg-warning"><i class="fas fa-star"></i>
                                <?php echo $review['rating']; ?>/5</span>
                        </div>
                        <p class="mt-2"><?php echo htmlspecialchars($review['comment']); ?></p>
                    </div>
                    <?php endwhile; ?>
                    <?php else: ?>
                    <p class="text-muted text-center py-4">No reviews yet. Be the first to review!</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
    } else {
        echo '<div class="container my-5"><div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Product not found</div></div>';
    }
}

include('../../backend/includes/footer.php');
?>