<?php
include('../includes/db.php');

// This file is used for AJAX requests to fetch product data
// It's called by fetch-data.php for product filtering

if(isset($_POST["action"])) {
    $query = "SELECT * FROM products WHERE stock > 0";
    
    $search = isset($_POST["search"]) && !empty($_POST["search"]) ? mysqli_real_escape_string($conn, $_POST["search"]) : '';
    if(!empty($search)) {
        $query .= " AND (name LIKE '%$search%' OR description LIKE '%$search%' OR brand LIKE '%$search%')";
    }
    
    $maxPrice = isset($_POST["maxPrice"]) ? intval($_POST["maxPrice"]) : 1000;
    $query .= " AND price <= $maxPrice";
    
    if(isset($_POST["brand"]) && !empty($_POST["brand"]) && is_array($_POST["brand"])) {
        $brands = array_map(function($b) use ($conn) { return "'" . mysqli_real_escape_string($conn, $b) . "'"; }, $_POST["brand"]);
        $brand_filter = implode(",", $brands);
        $query .= " AND brand IN($brand_filter)";
    }
    
    if(isset($_POST["category"]) && !empty($_POST["category"]) && is_array($_POST["category"])) {
        $cats = array_map(function($c) use ($conn) { return "'" . mysqli_real_escape_string($conn, $c) . "'"; }, $_POST["category"]);
        $cat_filter = implode(",", $cats);
        $query .= " AND category IN($cat_filter)";
    }
    
    $query .= " ORDER BY name ASC LIMIT 50";
    $result = mysqli_query($conn, $query);
    
    if(!$result || mysqli_num_rows($result) == 0) {
        echo '<div class="alert alert-info text-center"><i class="fas fa-inbox"></i> No products found matching your filters</div>';
    } else {
        while($row = mysqli_fetch_array($result)) {
            $rating_display = $row['reviews'] > 0 ? round($row['rating'], 1) : 'No ratings';
            echo '
            <div class="product-card">
                <img src="../../frontend/assets/images/'.$row['image'].'" alt="'.$row['name'].'" onerror="this.src=\'https://via.placeholder.com/200?text=No+Image\'">
                <div class="card-body">
                    <h3>'.$row['name'].'</h3>
                    <p class="brand"><strong>'.$row['brand'].'</strong></p>
                    <p class="price">$'.$row['price'].'</p>
                    <p class="rating"><i class="fas fa-star"></i> '.$rating_display.'</p>
                    <p class="stock-info ' . ($row['stock'] > 0 ? 'in-stock' : 'out-of-stock') . '">' . ($row['stock'] > 0 ? 'In Stock (' . $row['stock'] . ')' : 'Out of Stock') . '</p>
                    <div class="card-actions">
                        <button class="btn btn-primary btn-small flex-grow-1" onclick="addToCart('.$row['id'].')">
                            <i class="fas fa-cart-plus"></i> Add
                        </button>
                        <a href="product-details.php?id='.$row['id'].'" class="btn btn-secondary btn-small">View</a>
                    </div>
                </div>
            </div>';
        }
    }
}
?>
url: "fetch_products.php",
method: "POST",
data: {
action: action,
brand: brand,
category: category
},
success: function(data) {
$('#result').html(data);
}
});
}
});
</script>