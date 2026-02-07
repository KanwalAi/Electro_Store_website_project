<?php 
if(session_status() == PHP_SESSION_NONE) {
    session_start();
}
include(__DIR__ . '/db.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' | ElectroStore' : 'ElectroStore - Electronics Store'; ?>
    </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../frontend/assets/style.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="../../frontend/pages/index.php">
                <i class="fas fa-bolt"></i> ElectroStore
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../../frontend/pages/index.php"><i class="fas fa-home"></i> Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../../frontend/pages/shop.php"><i class="fas fa-shopping-cart"></i>
                            Shop</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../../frontend/pages/contact.php"><i class="fas fa-envelope"></i>
                            Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../../frontend/pages/cart.php"><i class="fas fa-cart-shopping"></i>
                            Cart
                            <?php if(!empty($_SESSION['cart'])) echo '<span class="badge bg-danger">' . count($_SESSION['cart']) . '</span>'; ?>
                        </a>
                    </li>
                    <?php if(isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="../../frontend/pages/wishlist.php"><i class="fas fa-heart"></i>
                            Wishlist</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i>
                            <?php echo isset($_SESSION['name']) ? substr($_SESSION['name'], 0, 10) : 'User'; ?>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="../../frontend/pages/profile.php"><i
                                        class="fas fa-user-circle"></i> My Profile</a></li>
                            <li><a class="dropdown-item" href="../../frontend/pages/my_orders.php"><i
                                        class="fas fa-box"></i> My Orders</a></li>
                            <?php if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin'): ?>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="../../frontend/admin/admin_dashboard.php"><i
                                        class="fas fa-tachometer-alt"></i> Admin Panel</a></li>
                            <?php endif; ?>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="../../backend/pages/logout.php"><i
                                        class="fas fa-sign-out-alt"></i>
                                    Logout</a></li>
                        </ul>
                    </li>
                    <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="../../backend/pages/login.php"><i class="fas fa-sign-in-alt"></i>
                            Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../../backend/pages/register.php"><i class="fas fa-user-plus"></i>
                            Register</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>