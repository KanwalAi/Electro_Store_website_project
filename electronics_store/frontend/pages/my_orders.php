<?php
session_start();
include('../../backend/includes/db.php');

if(!isset($_SESSION['user_id'])) {
    header("location:../../backend/pages/login.php");
    exit;
}

$page_title = 'My Orders';
include('../../backend/includes/header.php');

$user_id = $_SESSION['user_id'];
$orders = mysqli_query($conn, "SELECT * FROM orders WHERE user_id='$user_id' ORDER BY created_at DESC");
?>

<div class="container my-5">
    <div class="section-header">
        <h2><i class="fas fa-box"></i> My Orders</h2>
    </div>

    <?php if(mysqli_num_rows($orders) > 0): ?>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($order = mysqli_fetch_assoc($orders)): ?>
                        <tr>
                            <td><strong>#<?php echo $order['id']; ?></strong></td>
                            <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                            <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $order['status']; ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="order-details.php?id=<?php echo $order['id']; ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center py-5">
            <h4><i class="fas fa-inbox"></i> No orders yet</h4>
            <p>Start shopping to place your first order</p>
            <a href="shop.php" class="btn btn-primary"><i class="fas fa-shopping-bag"></i> Start Shopping</a>
        </div>
    <?php endif; ?>
</div>

<?php include('../../backend/includes/footer.php'); ?>