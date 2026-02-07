<?php
/**
 * Admin Setup Utility
 * Use this page to create/update admin credentials
 * 
 * IMPORTANT: Delete this file after creating admin account for security!
 * Location: /backend/pages/setup_admin.php
 */

include('../includes/db.php');

$success = '';
$error = '';
$admin_created = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');

    // Validation
    if (empty($username)) {
        $error = 'Username is required';
    } elseif (empty($password)) {
        $error = 'Password is required';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } else {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Check if admin exists
        $check = mysqli_query($conn, "SELECT id FROM users WHERE role='admin' LIMIT 1");
        
        if ($check && mysqli_num_rows($check) > 0) {
            // Update existing admin
            $update = "UPDATE users SET email='$username', password='$hashed_password' WHERE role='admin' LIMIT 1";
            if (mysqli_query($conn, $update)) {
                $success = 'Admin credentials updated successfully!';
                $admin_created = true;
            } else {
                $error = 'Error updating admin: ' . mysqli_error($conn);
            }
        } else {
            // Create new admin
            $insert = "INSERT INTO users (name, email, password, role) VALUES ('Administrator', '$username', '$hashed_password', 'admin')";
            if (mysqli_query($conn, $insert)) {
                $success = 'Admin account created successfully!';
                $admin_created = true;
            } else {
                $error = 'Error creating admin: ' . mysqli_error($conn);
            }
        }
    }
}

// Check if admin already exists
$admin_check = mysqli_query($conn, "SELECT email FROM users WHERE role='admin' LIMIT 1");
$admin_exists = $admin_check && mysqli_num_rows($admin_check) > 0;
$current_admin = $admin_exists ? mysqli_fetch_assoc($admin_check) : null;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Setup - ElectroStore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
    body {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .setup-container {
        max-width: 500px;
        width: 100%;
    }

    .card {
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        border: none;
        border-radius: 15px;
    }

    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px 15px 0 0 !important;
        padding: 30px;
        text-align: center;
    }

    .card-header h2 {
        margin: 0;
        font-size: 1.8rem;
        font-weight: 700;
    }

    .card-header p {
        margin: 10px 0 0 0;
        opacity: 0.9;
        font-size: 0.95rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        font-weight: 600;
        color: #333;
        margin-bottom: 0.5rem;
    }

    .form-control {
        border: 2px solid #e0e0e0;
        padding: 12px 15px;
        border-radius: 8px;
        transition: all 0.3s;
    }

    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .btn-submit {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        padding: 12px 30px;
        font-weight: 600;
        border-radius: 8px;
        width: 100%;
        margin-top: 10px;
        transition: all 0.3s;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
    }

    .alert {
        border: none;
        border-left: 4px solid;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .alert-success {
        background-color: #d4edda;
        border-color: #28a745;
        color: #155724;
    }

    .alert-danger {
        background-color: #f8d7da;
        border-color: #dc3545;
        color: #721c24;
    }

    .alert-info {
        background-color: #d1ecf1;
        border-color: #17a2b8;
        color: #0c5460;
    }

    .current-admin {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        border-left: 4px solid #28a745;
    }

    .input-group-text {
        background: white;
        border: 2px solid #e0e0e0;
        border-right: none;
    }

    .form-control {
        border: 2px solid #e0e0e0;
        border-left: none;
    }

    .success-icon {
        color: #28a745;
        font-size: 3rem;
        text-align: center;
        margin-bottom: 15px;
    }

    .footer-note {
        margin-top: 20px;
        padding: 15px;
        background: #fff3cd;
        border-left: 4px solid #ffc107;
        border-radius: 5px;
        font-size: 0.9rem;
        color: #856404;
    }

    .back-link {
        display: block;
        text-align: center;
        margin-top: 20px;
        color: #667eea;
        text-decoration: none;
        font-weight: 600;
    }

    .back-link:hover {
        text-decoration: underline;
    }
    </style>
</head>

<body>
    <div class="setup-container">
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-lock"></i> Admin Setup</h2>
                <p>Create or Update Admin Credentials</p>
            </div>

            <div class="card-body p-5">

                <?php if (!empty($success)): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <div class="success-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h5><?php echo $success; ?></h5>
                    <hr>
                    <p><strong><i class="fas fa-envelope"></i> Admin Email:</strong>
                        <?php echo htmlspecialchars($_POST['username'] ?? ''); ?></p>
                    <p><strong><i class="fas fa-key"></i> Password:</strong>
                        <?php echo htmlspecialchars($_POST['password'] ?? ''); ?></p>
                    <p class="mb-0 mt-3"><small>Keep these credentials safe. You can now login to the admin
                            panel.</small></p>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>

                <div class="alert alert-info">
                    <strong><i class="fas fa-info-circle"></i> Next Steps:</strong>
                    <ol class="mb-0 mt-2">
                        <li><a href="../../backend/pages/login.php" style="color: inherit; font-weight: bold;">Go to
                                Login</a></li>
                        <li>Login with your admin credentials</li>
                        <li><strong>Delete this file (setup_admin.php) for security</strong></li>
                    </ol>
                </div>
                <?php endif; ?>

                <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-circle"></i> <strong>Error:</strong>
                    <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php endif; ?>

                <?php if ($admin_exists && !$admin_created): ?>
                <div class="current-admin">
                    <strong><i class="fas fa-user-check"></i> Current Admin:</strong>
                    <p class="mb-0 mt-2"><?php echo htmlspecialchars($current_admin['email'] ?? ''); ?></p>
                    <small class="text-muted">Submitting this form will update the admin credentials</small>
                </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label for="username" class="form-label">Admin Email</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            <input type="email" class="form-control" id="username" name="username"
                                placeholder="admin@example.com"
                                value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password"
                                placeholder="Enter strong password" required>
                        </div>
                        <small class="form-text text-muted">Minimum 6 characters</small>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                                placeholder="Re-enter password" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-submit">
                        <i class="fas fa-save"></i> <?php echo $admin_exists ? 'Update Admin' : 'Create Admin'; ?>
                    </button>
                </form>

                <div class="footer-note">
                    <strong><i class="fas fa-warning"></i> IMPORTANT SECURITY WARNING:</strong>
                    <p class="mb-0 mt-2">
                        After creating your admin account, <strong>immediately delete this file</strong>
                        (setup_admin.php) to prevent unauthorized access. Anyone with access to this file can create new
                        admin accounts.
                    </p>
                </div>

                <a href="../../frontend/pages/index.php" class="back-link">
                    <i class="fas fa-arrow-left"></i> Back to Home
                </a>
            </div>
        </div>

        <div style="text-align: center; margin-top: 30px; color: white;">
            <p>
                <small>ElectroStore Admin Setup Tool • 2026</small>
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>