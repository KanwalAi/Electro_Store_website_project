<?php
session_start();
include('../../backend/includes/db.php');

if(!isset($_SESSION['user_id'])) {
    header("location:../../backend/pages/login.php");
    exit;
}

$page_title = 'My Profile';
include('../../backend/includes/header.php');

$user_id = $_SESSION['user_id'];
$user = mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'");
$user_data = mysqli_fetch_assoc($user);

$error = '';
$success = '';

if(isset($_POST['update_profile'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $state = mysqli_real_escape_string($conn, $_POST['state']);
    $zipcode = mysqli_real_escape_string($conn, $_POST['zipcode']);
    $country = mysqli_real_escape_string($conn, $_POST['country']);

    $update = mysqli_query($conn, "UPDATE users SET name='$name', phone='$phone', address='$address', city='$city', state='$state', zipcode='$zipcode', country='$country' WHERE id='$user_id'");
    
    if($update) {
        $_SESSION['name'] = $name;
        $success = 'Profile updated successfully!';
        $user_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'"));
    } else {
        $error = 'Failed to update profile';
    }
}
?>

<div class="container my-5">
    <div class="section-header">
        <h2><i class="fas fa-user-circle"></i> My Profile</h2>
    </div>

    <div class="row">
        <div class="col-lg-3">
            <div class="card shadow-lg border-0">
                <div class="card-body text-center">
                    <i class="fas fa-user-circle" style="font-size: 80px; color: #007bff;"></i>
                    <h5 class="mt-3"><?php echo htmlspecialchars($user_data['name']); ?></h5>
                    <p class="text-muted"><?php echo htmlspecialchars($user_data['email']); ?></p>
                    <div class="badge bg-primary"><?php echo ucfirst($user_data['role']); ?></div>
                </div>
            </div>

            <div class="card shadow-lg border-0 mt-3">
                <div class="card-body">
                    <h6 class="card-title"><i class="fas fa-link"></i> Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="my_orders.php" class="text-decoration-none"><i class="fas fa-box"></i> My
                                Orders</a></li>
                        <li class="mt-2"><a href="#" class="text-decoration-none"><i class="fas fa-heart"></i>
                                Wishlist</a></li>
                        <li class="mt-2"><a href="#" class="text-decoration-none"><i class="fas fa-cog"></i>
                                Settings</a></li>
                        <li class="mt-2"><a href="../../backend/pages/logout.php"
                                class="text-decoration-none text-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <h4 class="mb-4"><i class="fas fa-edit"></i> Edit Profile</h4>

                    <?php if(!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <?php if(!empty($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="name" class="form-control"
                                    value="<?php echo htmlspecialchars($user_data['name']); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email (Cannot be changed)</label>
                                <input type="email" class="form-control"
                                    value="<?php echo htmlspecialchars($user_data['email']); ?>" disabled>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="tel" name="phone" class="form-control"
                                    value="<?php echo htmlspecialchars($user_data['phone'] ?? ''); ?>"
                                    placeholder="+1 (555) 123-4567">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Country</label>
                                <input type="text" name="country" class="form-control"
                                    value="<?php echo htmlspecialchars($user_data['country'] ?? ''); ?>"
                                    placeholder="United States">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <input type="text" name="address" class="form-control"
                                value="<?php echo htmlspecialchars($user_data['address'] ?? ''); ?>"
                                placeholder="123 Main Street">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">City</label>
                                <input type="text" name="city" class="form-control"
                                    value="<?php echo htmlspecialchars($user_data['city'] ?? ''); ?>"
                                    placeholder="San Francisco">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">State</label>
                                <input type="text" name="state" class="form-control"
                                    value="<?php echo htmlspecialchars($user_data['state'] ?? ''); ?>" placeholder="CA">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Zip Code</label>
                                <input type="text" name="zipcode" class="form-control"
                                    value="<?php echo htmlspecialchars($user_data['zipcode'] ?? ''); ?>"
                                    placeholder="94025">
                            </div>
                        </div>

                        <button type="submit" name="update_profile" class="btn btn-primary w-100">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../../backend/includes/footer.php'); ?>