<?php
session_start();
include('../includes/db.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Please log in to use wishlist']);
    exit;
}

$user_id = intval($_SESSION['user_id']);
$action = $_POST['action'] ?? '';
$product_id = intval($_POST['product_id'] ?? 0);

// Create wishlist table if it doesn't exist
$createTableSQL = "CREATE TABLE IF NOT EXISTS wishlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    added_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_product (user_id, product_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
mysqli_query($conn, $createTableSQL);

if ($action === 'add') {
    // Add to wishlist
    $insert = "INSERT INTO wishlist (user_id, product_id) VALUES ($user_id, $product_id)"
               . " ON DUPLICATE KEY UPDATE added_at = CURRENT_TIMESTAMP";
    if (mysqli_query($conn, $insert)) {
        echo json_encode(['status' => 'success', 'message' => 'Added to wishlist']);
    } else {
        http_response_code(500);
        $err = mysqli_error($conn);
        echo json_encode(['status' => 'error', 'message' => 'Failed to add to wishlist: ' . $err]);
    }
} elseif ($action === 'remove') {
    // Remove from wishlist
    $delete = "DELETE FROM wishlist WHERE user_id=$user_id AND product_id=$product_id";
    if (mysqli_query($conn, $delete)) {
        echo json_encode(['status' => 'success', 'message' => 'Removed from wishlist']);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Failed to remove from wishlist']);
    }
} elseif ($action === 'check') {
    // Check if product is in wishlist
    $check = mysqli_query($conn, "SELECT id FROM wishlist WHERE user_id=$user_id AND product_id=$product_id LIMIT 1");
    $in_wishlist = mysqli_num_rows($check) > 0;
    echo json_encode(['status' => 'success', 'in_wishlist' => $in_wishlist]);
} else {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
}
?>