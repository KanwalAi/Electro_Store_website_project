<?php
include('../includes/db.php');

header('Content-Type: application/json');

// Fetch unique brands from products table
$brands_query = "SELECT DISTINCT brand FROM products WHERE brand IS NOT NULL AND brand != '' ORDER BY brand ASC";
$brands_result = mysqli_query($conn, $brands_query);
$brands = [];
while($row = mysqli_fetch_assoc($brands_result)) {
    $brands[] = $row['brand'];
}

// Fetch unique categories from products table
$categories_query = "SELECT DISTINCT category FROM products WHERE category IS NOT NULL AND category != '' ORDER BY category ASC";
$categories_result = mysqli_query($conn, $categories_query);
$categories = [];
while($row = mysqli_fetch_assoc($categories_result)) {
    $categories[] = $row['category'];
}

echo json_encode([
    'brands' => $brands,
    'categories' => $categories
]);
?>