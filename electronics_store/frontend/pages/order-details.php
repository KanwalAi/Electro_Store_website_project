<?php
session_start();
include('../../backend/includes/db.php');

if(!isset($_SESSION['user_id'])) {
    header("location:../../backend/pages/login.php");
    exit;
}

$page_title = 'Order Details';
include('../../backend/includes/header.php');

if(isset($_GET['id'])) {
    $order_id = intval($_GET['id']);
    $order = mysqli_query($conn, "SELECT o.*, u.name, u.email, u.phone, u.address FROM orders o JOIN users u ON o.user_id = u.id WHERE o.id=$order_id");
    
    if($order && mysqli_num_rows($order) > 0) {
        $order_data = mysqli_fetch_assoc($order);
        $order_items = mysqli_query($conn, "SELECT oi.*, p.name, p.image FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id=$order_id");

        // Determine appropriate back URL: prefer explicit `from=admin`, then HTTP_REFERER; default to my_orders.php
        $back_url = 'my_orders.php';
        if (isset($_GET['from']) && $_GET['from'] === 'admin') {
            $back_url = '../admin/manage_orders.php';
        } elseif (!empty($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'manage_orders.php') !== false) {
            $back_url = '../admin/manage_orders.php';
        }
?>

<div class="container my-5">


    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0 mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Order #<?php echo $order_data['id']; ?> Details</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Order Information</h6>
                            <p>
                                <strong>Status:</strong> <span
                                    class="status-badge status-<?php echo $order_data['status']; ?>"><?php echo ucfirst($order_data['status']); ?></span><br>
                                <strong>Date:</strong>
                                <?php echo date('M d, Y H:i', strtotime($order_data['created_at'])); ?><br>
                                <strong>Payment Method:</strong>
                                <?php echo htmlspecialchars($order_data['payment_method']); ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6>Customer Information</h6>
                            <p>
                                <strong>Name:</strong> <?php echo htmlspecialchars($order_data['name']); ?><br>
                                <strong>Email:</strong> <?php echo htmlspecialchars($order_data['email']); ?><br>
                                <strong>Phone:</strong> <?php echo htmlspecialchars($order_data['phone']); ?>
                            </p>
                        </div>
                    </div>

                    <h6>Shipping Address</h6>
                    <p>
                        <?php 
                        $shipping = $order_data['shipping_address'] ?: $order_data['address'];
                        echo htmlspecialchars($shipping);
                        ?>
                    </p>
                </div>
            </div>

            <div class="card shadow-lg border-0">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Order Items</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($item = mysqli_fetch_assoc($order_items)): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="../assets/images/<?php echo $item['image']; ?>" alt=""
                                            style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;"
                                            onerror="this.src='https://via.placeholder.com/50?text=No+Image'">
                                        <?php echo htmlspecialchars($item['name']); ?>
                                    </div>
                                </td>
                                <td>$<?php echo $item['price']; ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td><strong>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></strong>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="cart-summary">
                <h3><i class="fas fa-receipt"></i> Order Summary</h3>
                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span>$<?php echo number_format($order_data['total_amount'] / 1.08, 2); ?></span>
                </div>
                <div class="summary-row">
                    <span>Tax:</span>
                    <span>$<?php echo number_format($order_data['total_amount'] * 0.08 / 1.08, 2); ?></span>
                </div>
                <div class="summary-row total">
                    <span>Total:</span>
                    <span>$<?php echo number_format($order_data['total_amount'], 2); ?></span>
                </div>

                <a href="<?php echo htmlspecialchars($back_url); ?>" class="btn btn-secondary w-100 mt-3">
                    <i class="fas fa-arrow-left"></i> Back to Orders
                </a>
            </div>
        </div>
    </div>
</div>

<?php 
    } else {
        echo '<div class="container my-5"><div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> Order not found</div></div>';
    }
}

include('../../backend/includes/footer.php');
?>