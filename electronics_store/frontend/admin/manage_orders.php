<?php
session_start();
include('../../backend/includes/db.php');

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') { 
    header("location:../../backend/pages/login.php");
    exit;
}

$page_title = 'Manage Orders';
include('../../backend/includes/header.php');

// Update Order Status
if(isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    mysqli_query($conn, "UPDATE orders SET status='$status' WHERE id=$order_id");
}

// Get filter from URL/GET
$filter_status = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';

// Build query with optional status filter
$query = "SELECT o.*, u.name, u.email FROM orders o JOIN users u ON o.user_id = u.id";
if(!empty($filter_status)) {
    $query .= " WHERE o.status='$filter_status'";
}
$query .= " ORDER BY o.created_at DESC";

$orders = mysqli_query($conn, $query);
$statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
?>

<div class="container my-5">
    <div class="section-header d-flex justify-content-between align-items-center">
        <h2><i class="fas fa-shopping-bag"></i> Manage Orders</h2>
        <a href="admin_dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Admin Dashboard</a>
    </div>

    <!-- Filter Buttons -->
    <div class="mb-4 d-flex gap-2 flex-wrap">
        <a href="manage_orders.php"
            class="btn <?php echo empty($filter_status) ? 'btn-primary' : 'btn-outline-primary'; ?>">
            <i class="fas fa-list"></i> All Orders
        </a>
        <?php foreach($statuses as $status): ?>
        <a href="manage_orders.php?status=<?php echo $status; ?>"
            class="btn <?php echo $filter_status == $status ? 'btn-primary' : 'btn-outline-primary'; ?>">
            <i class="fas fa-circle"></i> <?php echo ucfirst($status); ?>
        </a>
        <?php endforeach; ?>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Email</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($order = mysqli_fetch_assoc($orders)): ?>
                <tr>
                    <td><strong>#<?php echo $order['id']; ?></strong></td>
                    <td><?php echo htmlspecialchars($order['name']); ?></td>
                    <td><?php echo htmlspecialchars($order['email']); ?></td>
                    <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                    <td>
                        <form method="POST" style="display: inline-flex; gap: 5px;">
                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                            <select name="status" class="form-select form-select-sm" style="width: auto;">
                                <option value="pending" <?php echo $order['status'] == 'pending' ? 'selected' : ''; ?>>
                                    Pending</option>
                                <option value="processing"
                                    <?php echo $order['status'] == 'processing' ? 'selected' : ''; ?>>Processing
                                </option>
                                <option value="shipped" <?php echo $order['status'] == 'shipped' ? 'selected' : ''; ?>>
                                    Shipped</option>
                                <option value="delivered"
                                    <?php echo $order['status'] == 'delivered' ? 'selected' : ''; ?>>Delivered</option>
                                <option value="cancelled"
                                    <?php echo $order['status'] == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                            </select>
                            <button type="submit" name="update_status" class="btn btn-sm btn-primary">Update</button>
                        </form>
                    </td>
                    <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                    <td>
                        <a href="../pages/order-details.php?id=<?php echo $order['id']; ?>"
                            class="btn btn-sm btn-info">View</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include('../../backend/includes/footer.php'); ?>