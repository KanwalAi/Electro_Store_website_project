<?php
session_start();
include('../../backend/includes/db.php');

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') { 
    header("location:../../backend/pages/login.php");
    exit;
}

$page_title = 'Manage Users';
include('../../backend/includes/header.php');

// Handle user edits
$error = '';
$success = '';
if (isset($_POST['edit_user'])) {
    $uid = intval($_POST['user_id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);

    if (empty($name)) {
        $error = 'Name cannot be empty.';
    } else {
        if (mysqli_query($conn, "UPDATE users SET name='$name', phone='$phone', city='$city' WHERE id=$uid")) {
            $success = 'User updated successfully.';
        } else {
            $error = 'Failed to update user.';
        }
    }
}

$users = mysqli_query($conn, "SELECT * FROM users WHERE role='customer' ORDER BY created_at DESC");
?>

<div class="container my-5">
    <div class="section-header d-flex justify-content-between align-items-center">
        <h2><i class="fas fa-users"></i> Manage Users</h2>
        <a href="admin_dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Admin Dashboard</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>City</th>
                    <th>Registered</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($user = mysqli_fetch_assoc($users)): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($user['city'] ?? 'N/A'); ?></td>
                        <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                        <td>
                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editUserModal"
                                onclick="loadUser(this)"
                                data-id="<?php echo $user['id']; ?>"
                                data-name="<?php echo htmlspecialchars($user['name'], ENT_QUOTES); ?>"
                                data-email="<?php echo htmlspecialchars($user['email'], ENT_QUOTES); ?>"
                                data-phone="<?php echo htmlspecialchars($user['phone'] ?? '', ENT_QUOTES); ?>"
                                data-city="<?php echo htmlspecialchars($user['city'] ?? '', ENT_QUOTES); ?>">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="user_id" id="edit_user_id">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" id="edit_user_name" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email (readonly)</label>
                        <input type="email" id="edit_user_email" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" id="edit_user_phone" name="phone" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">City</label>
                        <input type="text" id="edit_user_city" name="city" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="edit_user" class="btn btn-warning">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function loadUser(btn) {
    var id = btn.getAttribute('data-id');
    var name = btn.getAttribute('data-name');
    var email = btn.getAttribute('data-email');
    var phone = btn.getAttribute('data-phone');
    var city = btn.getAttribute('data-city');

    document.getElementById('edit_user_id').value = id;
    document.getElementById('edit_user_name').value = name;
    document.getElementById('edit_user_email').value = email;
    document.getElementById('edit_user_phone').value = phone;
    document.getElementById('edit_user_city').value = city;
}
</script>

<?php include('../../backend/includes/footer.php'); ?>