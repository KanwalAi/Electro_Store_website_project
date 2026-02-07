<?php
session_start();
include('../../backend/includes/db.php');

if(!isset($_SESSION['user_id'])) {
    header("location:../../backend/pages/login.php");
    exit;
}

$page_title = 'Checkout';
include('../../backend/includes/header.php');

if(empty($_SESSION['cart'])) {
    header("location:cart.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user = mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'");
$user_data = mysqli_fetch_assoc($user);

$total_amount = 0;
foreach($_SESSION['cart'] as $id => $qty) {
    $product = mysqli_query($conn, "SELECT price FROM products WHERE id='$id'");
    if($product) {
        $prod = mysqli_fetch_assoc($product);
        $total_amount += $prod['price'] * $qty;
    }
}
$tax = $total_amount * 0.08;
$grand_total = $total_amount + $tax;

$error = '';
$success = '';

if(isset($_POST['place_order'])) {
    // Insert order
    $shipping_address = mysqli_real_escape_string($conn, $_POST['shipping_address']);
    $payment_method = mysqli_real_escape_string($conn, $_POST['payment_method']);
    
    $order_query = mysqli_query($conn, "INSERT INTO orders (user_id, total_amount, status, payment_method, shipping_address) 
                                       VALUES ('$user_id', '$grand_total', 'pending', '$payment_method', '$shipping_address')");
    
    if($order_query) {
        $order_id = mysqli_insert_id($conn);
        
        // Insert order items
        foreach($_SESSION['cart'] as $id => $qty) {
            $product = mysqli_query($conn, "SELECT price FROM products WHERE id='$id'");
            $prod = mysqli_fetch_assoc($product);
            mysqli_query($conn, "INSERT INTO order_items (order_id, product_id, quantity, price) 
                               VALUES ('$order_id', '$id', '$qty', '{$prod['price']}')");
            
            // Update stock
            mysqli_query($conn, "UPDATE products SET stock = stock - $qty WHERE id='$id'");
        }
        
        // Clear cart
        $_SESSION['cart'] = [];
        $success = 'Order placed successfully! Order ID: #' . $order_id;
        header("refresh:3;url=my_orders.php");
    } else {
        $error = 'Failed to place order. Please try again.';
    }
}
?>

<div class="container my-5">
    <div class="section-header">
        <h2><i class="fas fa-credit-card"></i> Checkout</h2>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0 mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-map-marker-alt"></i> Shipping Address</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted">Current Address on File:</p>
                    <p>
                        <?php 
                        $address = $user_data['address'] . ' ' . $user_data['city'] . ', ' . $user_data['state'] . ' ' . $user_data['zipcode'];
                        echo htmlspecialchars($address);
                        ?>
                    </p>
                </div>
            </div>

            <div class="card shadow-lg border-0 mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-credit-card"></i> Payment Method</h5>
                </div>
                <div class="card-body">
                    <form method="POST" id="checkoutForm">
                        <div class="mb-3">
                            <label class="form-label">Shipping Address (if different)</label>
                            <textarea name="shipping_address" class="form-control" rows="3" placeholder="Leave blank to use your current address" 
                                      value="<?php echo isset($_POST['shipping_address']) ? htmlspecialchars($_POST['shipping_address']) : ''; ?>"></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Select Payment Method</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="credit_card" value="Credit Card" checked>
                                <label class="form-check-label" for="credit_card">
                                    <i class="fas fa-credit-card"></i> Credit/Debit Card
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="paypal" value="PayPal">
                                <label class="form-check-label" for="paypal">
                                    <i class="fab fa-paypal"></i> PayPal
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="bank_transfer" value="Bank Transfer">
                                <label class="form-check-label" for="bank_transfer">
                                    <i class="fas fa-university"></i> Bank Transfer
                                </label>
                            </div>
                        </div>

                        <?php if(!empty($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <?php if(!empty($success)): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                            </div>
                        <?php endif; ?>

                        <button type="submit" name="place_order" class="btn btn-success btn-lg w-100">
                            <i class="fas fa-check"></i> Place Order
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="cart-summary">
                <h3><i class="fas fa-receipt"></i> Order Summary</h3>
                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span>$<?php echo number_format($total_amount, 2); ?></span>
                </div>
                <div class="summary-row">
                    <span>Shipping:</span>
                    <span>FREE</span>
                </div>
                <div class="summary-row">
                    <span>Tax (8%):</span>
                    <span>$<?php echo number_format($tax, 2); ?></span>
                </div>
                <div class="summary-row total">
                    <span>Total:</span>
                    <span>$<?php echo number_format($grand_total, 2); ?></span>
                </div>

                <div class="alert alert-info mt-3">
                    <strong><i class="fas fa-info-circle"></i> Note:</strong> This is a demo store. Payment will be simulated.
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../../backend/includes/footer.php'); ?>