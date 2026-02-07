<?php
session_start();
include('../includes/db.php');

$error = '';
$success = '';

if(isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = $_POST['password'];

    if(empty($email) || empty($pass)) {
        $error = 'Email and password are required';
    } else {
        $result = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
        if($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            if(password_verify($pass, $row['password'])) {
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['role'] = $row['role'];
                $_SESSION['name'] = $row['name'];

                if($row['role'] == 'admin') {
                    header("location:../../frontend/admin/admin_dashboard.php");
                } else {
                    header("location:../../frontend/pages/index.php");
                }
                exit;
            } else { 
                $error = 'Invalid password'; 
            }
        } else { 
            $error = 'User not found'; 
        }
    }
}
$page_title = 'Login';
include('../includes/header.php');
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <h2 class="card-title text-center mb-4">
                        <i class="fas fa-sign-in-alt text-primary"></i> Login
                    </h2>

                    <?php if(!empty($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php endif; ?>

                    <form method="POST" id="loginForm">
                        <div class="form-group mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="Enter your email" required>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Enter your password" required>
                            </div>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="remember" name="remember">
                            <label class="form-check-label" for="remember">
                                Remember me
                            </label>
                        </div>

                        <button type="submit" name="login" class="btn btn-primary btn-block w-100 mb-3">
                            <i class="fas fa-sign-in-alt"></i> Login
                        </button>
                    </form>

                    <hr>

                    <p class="text-center mb-0">
                        Don't have an account?
                        <a href="register.php" class="text-primary font-weight-bold">Register here</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../../backend/includes/footer.php'); ?>