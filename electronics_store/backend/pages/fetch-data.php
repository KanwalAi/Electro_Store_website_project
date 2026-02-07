<?php
include('../includes/db.php');

if(isset($_POST["action"])) {
    $query = "SELECT * FROM products WHERE 1=1";
    
    $search = isset($_POST["search"]) && !empty($_POST["search"]) ? mysqli_real_escape_string($conn, $_POST["search"]) : '';
    if(!empty($search)) {
        $query .= " AND (name LIKE '%$search%' OR description LIKE '%$search%' OR brand LIKE '%$search%')";
    }
    
    $maxPrice = isset($_POST["maxPrice"]) ? intval($_POST["maxPrice"]) : 5000;
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
    
    $query .= " ORDER BY name ASC LIMIT 500";
    $result = mysqli_query($conn, $query);
    
    if(!$result || mysqli_num_rows($result) == 0) {
        echo '<div class="alert alert-info text-center"><i class="fas fa-inbox"></i> No products found matching your filters</div>';
    } else {
        while($row = mysqli_fetch_array($result)) {
            // compute aggregate from reviews table to ensure up-to-date values
            $pid = intval($row['id']);
            $aggRes = mysqli_query($conn, "SELECT COUNT(*) AS cnt, AVG(rating) AS avg_rating FROM reviews WHERE product_id={$pid}");
            $reviews_count = 0;
            $rating_val = 0;
            if ($aggRes && mysqli_num_rows($aggRes) > 0) {
                $aggRow = mysqli_fetch_assoc($aggRes);
                $reviews_count = intval($aggRow['cnt']);
                $rating_val = $aggRow['avg_rating'] !== null ? floatval($aggRow['avg_rating']) : 0;
            }
            if ($reviews_count > 0) {
                $rounded = round($rating_val);
                $stars = '';
                for ($i = 1; $i <= 5; $i++) {
                    $stars .= ($i <= $rounded) ? '<i class="fas fa-star text-warning"></i>' : '<i class="far fa-star text-muted"></i>';
                }
                $rating_display = $stars . ' <span class="ms-1">' . round($rating_val, 1) . ' (' . $reviews_count . ')</span>';
            } else {
                $rating_display = '<span class="text-muted">No ratings</span>';
            }
            echo '
            <div class="product-card">
                <img src="../../frontend/assets/images/'.$row['image'].'" alt="'.$row['name'].'" onerror="this.src=\'https://via.placeholder.com/200?text=No+Image\'">
                <div class="card-body">
                    <h3>'.$row['name'].'</h3>
                    <p class="brand"><strong>'.$row['brand'].'</strong></p>
                    <p class="price">$'.$row['price'].'</p>
                    <p class="rating">'.$rating_display.'</p>
                    <p class="stock-info ' . ($row['stock'] > 0 ? 'in-stock' : 'out-of-stock') . '">' . ($row['stock'] > 0 ? 'In Stock (' . $row['stock'] . ')' : 'Out of Stock') . '</p>
                    <div class="card-actions">
                        ' . ($row['stock'] > 0 ? 
                            '<button class="btn btn-primary btn-small flex-grow-1" onclick="addToCart('.$row['id'].')">
                                <i class="fas fa-cart-plus"></i> Add
                            </button>' 
                            : 
                            '<button class="btn btn-warning btn-small flex-grow-1" onclick="(function(b,i){b.disabled=true;b.dataset._oldhtml=b.innerHTML;b.innerHTML=\'<i class=\\\'fas fa-spinner fa-spin\\\'></i> Adding...\';fetch(\'../../backend/api/wishlist.php\',{method:\'POST\',headers:{\'Content-Type\':\'application/x-www-form-urlencoded\'},body:\'action=add&product_id=\'+i}).then(function(res){if(res.status===401){alert(\'Please log in to use wishlist\');return res.text().then(function(){throw new Error(\'unauth\');});}if(!res.ok){return res.text().then(function(t){throw new Error(t||\'HTTP \'+res.status);});}return res.json();}).then(function(data){if(data.status===\'success\'){b.innerHTML=\'<i class=\\\'fas fa-heart\\\'></i> Added\';b.classList.remove(\'btn-warning\');b.classList.add(\'btn-success\');b.disabled=true;}else{alert(data.message||\'Error adding to wishlist\');b.disabled=false;b.innerHTML=b.dataset._oldhtml;}}).catch(function(e){if(e.message===\'unauth\') return;alert(\'Error adding to wishlist: \'+e);b.disabled=false;b.innerHTML=b.dataset._oldhtml;});})(this, '.$row['id'].')">
                                <i class="fas fa-heart"></i> Add To Wishlist
                            </button>'
                        ) . '
                        <a href="product-details.php?id='.$row['id'].'" class="btn btn-secondary btn-small">View</a>
                    </div>
                </div>
            </div>';
        }
    }
}
?>