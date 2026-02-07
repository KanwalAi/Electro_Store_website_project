<?php
session_start();
include('../../backend/includes/db.php');

$page_title = 'Shopping Cart';
include('../../backend/includes/header.php');

if(isset($_GET['add'])) {
    $id = intval($_GET['add']);
    $qty = isset($_POST['quantity']) ? max(1, intval($_POST['quantity'])) : 1;
    
    // Fetch product stock from DB
    $pRes = mysqli_query($conn, "SELECT id, stock FROM products WHERE id = $id LIMIT 1");
    if($pRes && mysqli_num_rows($pRes) > 0) {
        $pRow = mysqli_fetch_assoc($pRes);
        $stock = intval($pRow['stock']);
        
        if(!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
        
        $current = isset($_SESSION['cart'][$id]) ? intval($_SESSION['cart'][$id]) : 0;
        $requested_total = $current + $qty;
        
        if ($requested_total > $stock) {
            $_SESSION['add_error'] = "Only " . ($stock - $current) . " item(s) available (you already have " . $current . " in cart)";
        } else {
            $_SESSION['cart'][$id] = $requested_total;
            $_SESSION['add_error'] = '';
        }
    }
    header("location:cart.php");
}

if(isset($_GET['remove'])) {
    $id = intval($_GET['remove']);
    if(isset($_SESSION['cart'][$id])) {
        unset($_SESSION['cart'][$id]);
    }
    header("location:cart.php");
}

if(isset($_POST['update_cart'])) {
    foreach($_POST['quantity'] as $id => $qty) {
        $qty = intval($qty);
        if($qty <= 0) {
            unset($_SESSION['cart'][$id]);
        } else {
            $_SESSION['cart'][$id] = $qty;
        }
    }
    header("location:cart.php");
}

if(isset($_POST['clear_cart'])) {
    $_SESSION['cart'] = [];
    header("location:cart.php");
}
?>

<div class="container my-5">
    <div class="section-header">
        <h2><i class="fas fa-shopping-cart"></i> Shopping Cart</h2>
    </div>

    <?php if(!empty($_SESSION['add_error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['add_error']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php $_SESSION['add_error'] = ''; endif; ?>

    <?php if(empty($_SESSION['cart'])): ?>
    <div class="alert alert-info text-center py-5">
        <h4><i class="fas fa-inbox"></i> Your cart is empty</h4>
        <p>Start shopping to add items to your cart</p>
        <a href="shop.php" class="btn btn-primary"><i class="fas fa-shopping-bag"></i> Continue Shopping</a>
    </div>
    <?php else: ?>
    <div class="row">
        <div class="col-lg-8">
            <div class="cart-table">
                <form method="POST">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $grand_total = 0;
                                foreach($_SESSION['cart'] as $id => $qty) {
                                    $product = mysqli_query($conn, "SELECT * FROM products WHERE id='$id'");
                                    if($product && mysqli_num_rows($product) > 0) {
                                        $row = mysqli_fetch_assoc($product);
                                        $total = $row['price'] * $qty;
                                        $grand_total += $total;
                                        echo "
                                        <tr>
                                            <td>
                                                <div class='d-flex align-items-center gap-3'>
                                                    <img src='../assets/images/{$row['image']}' alt='{$row['name']}' class='rounded' style='width: 60px; height: 60px; object-fit: cover;' onerror=\"this.src='https://via.placeholder.com/60?text=No+Image'\">
                                                    <div>
                                                        <h6 class='mb-1'>{$row['name']}</h6>
                                                        <small class='text-muted'>{$row['brand']}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>\${$row['price']}</td>
                                            <td>
                                                <input type='number' name='quantity[$id]' value='$qty' min='1' max='{$row['stock']}' class='form-control' style='width: 80px;'>
                                            </td>
                                            <td>\$$total</td>
                                            <td>
                                                <a href='cart.php?remove=$id' class='btn btn-danger btn-sm' onclick=\"return confirm('Remove item?')\">
                                                    <i class='fas fa-trash'></i>
                                                </a>
                                            </td>
                                        </tr>
                                        ";
                                    }
                                }
                                ?>
                        </tbody>
                    </table>
                    <div class="p-3 border-top d-flex gap-2">
                        <button type="submit" name="update_cart" class="btn btn-primary">
                            <i class="fas fa-sync"></i> Update Cart
                        </button>
                        <button type="submit" name="clear_cart" class="btn btn-danger"
                            onclick="return confirm('Are you sure you want to clear your entire cart?')">
                            <i class="fas fa-trash-alt"></i> Clear Cart
                        </button>
                        <a href="shop.php" class="btn btn-secondary">
                            <i class="fas fa-shopping-bag"></i> Continue Shopping
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="cart-summary">
                <h3><i class="fas fa-calculator"></i> Order Summary</h3>
                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span>$<?php echo number_format($grand_total, 2); ?></span>
                </div>
                <div class="summary-row">
                    <span>Shipping:</span>
                    <span>$0.00 <small class="text-success">(FREE)</small></span>
                </div>
                <div class="summary-row">
                    <span>Tax:</span>
                    <span>$<?php echo number_format($grand_total * 0.08, 2); ?></span>
                </div>
                <div class="summary-row total">
                    <span>Total:</span>
                    <span>$<?php echo number_format($grand_total + ($grand_total * 0.08), 2); ?></span>
                </div>

                <?php if(isset($_SESSION['user_id'])): ?>
                <a href="checkout.php" class="btn btn-success w-100 mt-3">
                    <i class="fas fa-lock"></i> Proceed to Checkout
                </a>
                <?php else: ?>
                <div class="alert alert-warning mt-3 mb-0">
                    <small><i class="fas fa-info-circle"></i> Please <a href="../../backend/pages/login.php"
                            class="alert-link">login</a>
                        to
                        checkout</small>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include('../../backend/includes/footer.php'); ?>