<?php
include('../includes/db.php');

$error = '';
$success = '';

if(isset($_POST['register'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone'] ?? '');
    $country = mysqli_real_escape_string($conn, $_POST['country'] ?? '');
    $address = mysqli_real_escape_string($conn, $_POST['address'] ?? '');
    $city = mysqli_real_escape_string($conn, $_POST['city'] ?? '');
    $state = mysqli_real_escape_string($conn, $_POST['state'] ?? '');
    $zip = mysqli_real_escape_string($conn, $_POST['zip'] ?? '');
    $pass = $_POST['password'];
    $conf_pass = $_POST['confirm_password'];

    if(empty($name) || empty($email) || empty($pass) || empty($conf_pass)) {
        $error = 'Name, Email, and Password are required';
    } elseif(strlen($pass) < 6) {
        $error = 'Password must be at least 6 characters';
    } elseif($pass !== $conf_pass) {
        $error = 'Passwords do not match';
    } else {
        $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
        if(mysqli_num_rows($check) > 0) {
            $error = 'Email already registered';
        } else {
            // Ensure users table has the profile columns (add if missing)
            $cols = [
                'phone' => "VARCHAR(50) DEFAULT NULL",
                'country' => "VARCHAR(100) DEFAULT NULL",
                'address' => "TEXT DEFAULT NULL",
                'city' => "VARCHAR(100) DEFAULT NULL",
                'state' => "VARCHAR(100) DEFAULT NULL",
                'zip' => "VARCHAR(20) DEFAULT NULL"
            ];
            foreach($cols as $col => $def) {
                $res = mysqli_query($conn, "SELECT COUNT(*) AS cnt FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='users' AND COLUMN_NAME='".mysqli_real_escape_string($conn, $col)."'");
                $r = $res ? mysqli_fetch_assoc($res) : null;
                if(!$r || intval($r['cnt']) === 0) {
                    @mysqli_query($conn, "ALTER TABLE users ADD COLUMN `".mysqli_real_escape_string($conn, $col)."` " . $def);
                }
            }

            $pass_hash = password_hash($pass, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (name, email, password, phone, country, address, city, state, zip, role) ";
            $sql .= "VALUES ('".mysqli_real_escape_string($conn,$name)."', '".mysqli_real_escape_string($conn,$email)."', '".$pass_hash."', '".mysqli_real_escape_string($conn,$phone)."', '".mysqli_real_escape_string($conn,$country)."', '".mysqli_real_escape_string($conn,$address)."', '".mysqli_real_escape_string($conn,$city)."', '".mysqli_real_escape_string($conn,$state)."', '".mysqli_real_escape_string($conn,$zip)."', 'customer')";
            if(mysqli_query($conn, $sql)) {
                $success = 'Registration successful! Redirecting to login...';
                header("refresh:2;url=login.php");
            } else {
                $error = 'Registration failed. Please try again. Error: ' . mysqli_error($conn);
            }
        }
    }
}
$page_title = 'Register';
include('../includes/header.php');
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <h2 class="card-title text-center mb-4">
                        <i class="fas fa-user-plus text-primary"></i> Create Account
                    </h2>

                    <?php if(!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <?php if(!empty($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
                    </div>
                    <?php endif; ?>

                    <form method="POST" id="registerForm">
                        <div class="form-group mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" id="name" name="name"
                                    placeholder="Enter your full name" required>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="Enter your email" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Phone Number</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                    <input type="tel" class="form-control" id="phone" name="phone"
                                        placeholder="e.g. +923...">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="country" class="form-label">Country</label>
                                <input type="text" class="form-control" id="country" name="country"
                                    placeholder="Country">
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="2"
                                placeholder="Street address"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="city" class="form-label">City</label>
                                <input type="text" class="form-control" id="city" name="city" placeholder="City">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="state" class="form-label">State</label>
                                <input type="text" class="form-control" id="state" name="state" placeholder="State">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="zip" class="form-label">Zip Code</label>
                                <input type="text" class="form-control" id="zip" name="zip" placeholder="Zip Code">
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Minimum 6 characters" required>
                            </div>
                            <small class="form-text text-muted">Password must be at least 6 characters long</small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="confirm_password"
                                    name="confirm_password" placeholder="Re-enter your password" required>
                            </div>
                        </div>

                        <button type="submit" name="register" class="btn btn-primary w-100 mb-3">
                            <i class="fas fa-user-plus"></i> Register
                        </button>
                    </form>

                    <hr>

                    <p class="text-center mb-0">
                        Already have an account?
                        <a href="login.php" class="text-primary font-weight-bold">Login here</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../../backend/includes/footer.php'); ?>