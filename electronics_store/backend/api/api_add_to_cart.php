<?php
session_start();
include('../includes/db.php');

header('Content-Type: application/json');

if(isset($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = isset($_POST['quantity']) ? max(1, intval($_POST['quantity'])) : 1;

    // Fetch product stock from DB
    $pRes = mysqli_query($conn, "SELECT id, stock FROM products WHERE id = $product_id LIMIT 1");
    if(!$pRes || mysqli_num_rows($pRes) == 0) {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
        exit;
    }
    $pRow = mysqli_fetch_assoc($pRes);
    $stock = intval($pRow['stock']);

    if(!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $current = isset($_SESSION['cart'][$product_id]) ? intval($_SESSION['cart'][$product_id]) : 0;
    $requested_total = $current + $quantity;

    if ($requested_total > $stock) {
        if ($stock - $current <= 0) {
            echo json_encode(['success' => false, 'message' => 'Sorry, this product is out of stock']);
        } else {
            $avail = $stock - $current;
            echo json_encode(['success' => false, 'message' => 'Only ' . $avail . ' item(s) available (you already have ' . $current . ' in cart)']);
        }
        exit;
    }

    // Safe to add
    if(isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id] = $requested_total;
    } else {
        $_SESSION['cart'][$product_id] = $quantity;
    }

    echo json_encode(['success' => true, 'message' => 'Product added to cart']);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid product']);
}
?>
