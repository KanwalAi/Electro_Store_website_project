<?php
session_start();
include('../../backend/includes/db.php');

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') { 
    header("location:../../backend/pages/login.php");
    exit;
}

$page_title = 'Admin Dashboard';
include('../../backend/includes/header.php');
?>

<div class="container my-5">
    <div class="section-header">
        <h2><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h2>
    </div>

    <!-- Statistics -->
    <div class="stats-grid">
        <?php
        // Total Products
        $products_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM products");
        $prod_data = mysqli_fetch_assoc($products_result);
        
        // Total Orders
        $orders_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM orders");
        $order_data = mysqli_fetch_assoc($orders_result);
        
        // Total Revenue
        $revenue_result = mysqli_query($conn, "SELECT SUM(total_amount) as total FROM orders WHERE status='delivered'");
        $revenue_data = mysqli_fetch_assoc($revenue_result);
        
        // Total Users
        $users_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='customer'");
        $user_data = mysqli_fetch_assoc($users_result);
        
        // Unread Messages
        $messages_result = mysqli_query($conn, "SELECT COUNT(*) as total FROM messages WHERE status='unread'");
        $msg_data = mysqli_fetch_assoc($messages_result);
        ?>
        
        <div class="stat-card">
            <h3><?php echo $prod_data['total']; ?></h3>
            <p><i class="fas fa-boxes"></i> Total Products</p>
        </div>
        <div class="stat-card">
            <h3><?php echo $order_data['total']; ?></h3>
            <p><i class="fas fa-shopping-bag"></i> Total Orders</p>
        </div>
        <div class="stat-card">
            <h3>$<?php echo number_format($revenue_data['total'] ?? 0, 2); ?></h3>
            <p><i class="fas fa-dollar-sign"></i> Total Revenue</p>
        </div>
        <div class="stat-card">
            <h3><?php echo $user_data['total']; ?></h3>
            <p><i class="fas fa-users"></i> Total Customers</p>
        </div>
    </div>

    <!-- Admin Menu -->
    <div class="row my-5">
        <div class="col-md-6 mb-4">
            <div class="card shadow-lg border-0">
                <div class="card-body text-center p-4">
                    <i class="fas fa-box" style="font-size: 40px; color: #007bff;"></i>
                    <h5 class="mt-3">Manage Products</h5>
                    <p class="text-muted">Add, edit, or remove products from inventory</p>
                    <a href="manage_products.php" class="btn btn-primary">Manage</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow-lg border-0">
                <div class="card-body text-center p-4">
                    <i class="fas fa-shopping-bag" style="font-size: 40px; color: #28a745;"></i>
                    <h5 class="mt-3">Manage Orders</h5>
                    <p class="text-muted">View and update order status</p>
                    <a href="manage_orders.php" class="btn btn-success">Manage</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow-lg border-0">
                <div class="card-body text-center p-4">
                    <i class="fas fa-envelope" style="font-size: 40px; color: #ffc107;"></i>
                    <h5 class="mt-3">Messages <span class="badge bg-danger"><?php echo $msg_data['total']; ?></span></h5>
                    <p class="text-muted">View customer messages and inquiries</p>
                    <a href="admin_inbox.php" class="btn btn-warning">View</a>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow-lg border-0">
                <div class="card-body text-center p-4">
                    <i class="fas fa-users" style="font-size: 40px; color: #17a2b8;"></i>
                    <h5 class="mt-3">Users</h5>
                    <p class="text-muted">View all registered customers</p>
                    <a href="manage_users.php" class="btn btn-info">View</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="card shadow-lg border-0 mt-5">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0"><i class="fas fa-recent"></i> Recent Orders</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $recent_orders = mysqli_query($conn, "SELECT o.*, u.name, u.email FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 10");
                        if($recent_orders && mysqli_num_rows($recent_orders) > 0) {
                            while($order = mysqli_fetch_assoc($recent_orders)) {
                                echo "
                                <tr>
                                    <td><strong>#" . $order['id'] . "</strong></td>
                                    <td>{$order['name']}</td>
                                    <td>\${$order['total_amount']}</td>
                                    <td><span class='status-badge status-" . $order['status'] . "'>" . ucfirst($order['status']) . "</span></td>
                                    <td>" . date('M d, Y', strtotime($order['created_at'])) . "</td>
                                    <td><a href='manage_orders.php?id=" . $order['id'] . "' class='btn btn-sm btn-primary'>View</a></td>
                                </tr>
                                ";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include('../../backend/includes/footer.php'); ?>