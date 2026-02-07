<?php
session_start();
include('../../backend/includes/db.php');

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') { 
    header("location:../../backend/pages/login.php");
    exit;
}

$page_title = 'Manage Products';
include('../../backend/includes/header.php');

$error = '';
$success = '';

// --- 1. LOGIC TO FETCH DYNAMIC LISTS ---
// Define defaults
$default_brands = ['Intel', 'AMD', 'NVIDIA', 'Corsair', 'Samsung', 'Generic'];
$default_categories = ['Microchip', 'Resistor', 'Capacitor'];

// Get existing unique brands from DB
$db_brands = [];
$brand_query = mysqli_query($conn, "SELECT DISTINCT brand FROM products WHERE brand != ''");
while($row = mysqli_fetch_assoc($brand_query)) {
    $db_brands[] = $row['brand'];
}

// Get existing unique categories from DB
$db_categories = [];
$cat_query = mysqli_query($conn, "SELECT DISTINCT category FROM products WHERE category != ''");
while($row = mysqli_fetch_assoc($cat_query)) {
    $db_categories[] = $row['category'];
}

// Merge and sort unique lists (Defaults + DB values)
$all_brands = array_unique(array_merge($default_brands, $db_brands));
sort($all_brands);

$all_categories = array_unique(array_merge($default_categories, $db_categories));
sort($all_categories);
// ---------------------------------------

// Handle Add New Brand via AJAX (Optional utility)
if(isset($_POST['add_brand'])) {
    $brand_name = mysqli_real_escape_string($conn, $_POST['brand_name']);
    if(!empty($brand_name)) {
        echo json_encode(['success' => true, 'brand' => $brand_name]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Brand name cannot be empty']);
    }
    exit;
}

// Handle Add New Category via AJAX (Optional utility)
if(isset($_POST['add_category'])) {
    $category_name = mysqli_real_escape_string($conn, $_POST['category_name']);
    if(!empty($category_name)) {
        echo json_encode(['success' => true, 'category' => $category_name]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Category name cannot be empty']);
    }
    exit;
}

// Add Product
if(isset($_POST['add_product'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    
    // Check if the user typed a new brand manually in the text input, or selected one
    // Note: The JS below effectively adds the new option to the select and selects it, 
    // so $_POST['brand'] usually carries the new value. 
    $brand = mysqli_real_escape_string($conn, $_POST['brand']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);

    // Fallback: If for some reason the JS didn't update the select value, check inputs
    if($brand == '__add_new__' && !empty($_POST['new_brand_input'])) {
        $brand = mysqli_real_escape_string($conn, $_POST['new_brand_input']);
    }
    if($category == '__add_new__' && !empty($_POST['new_category_input'])) {
        $category = mysqli_real_escape_string($conn, $_POST['new_category_input']);
    }

    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);

    // Server-side validation
    if ($stock < 0) {
        $error = 'Stock cannot be negative.';
    } elseif ($price < 0) {
        $error = 'Price cannot be negative.';
    } else {
        $img = '';
        // Image validation
        if(isset($_FILES['image']) && $_FILES['image']['name']) {
            $allowed = ['image/jpeg', 'image/png', 'image/gif'];
            $maxSize = 2 * 1024 * 1024; // 2MB
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $_FILES['image']['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mime, $allowed)) {
                $error = 'Invalid image type. Only JPG, PNG and GIF are allowed.';
            } elseif ($_FILES['image']['size'] > $maxSize) {
                $error = 'Image too large. Maximum size is 2MB.';
            } else {
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $img = uniqid('p_', true) . '.' . $ext;
                if(!move_uploaded_file($_FILES['image']['tmp_name'], "../../frontend/assets/images/".$img)) {
                    $error = 'Failed to upload image.';
                }
            }
        }

        if (empty($error)) {
            $result = mysqli_query($conn, "INSERT INTO products (name, description, category, brand, price, image, stock) 
                                     VALUES ('$name', '$description', '$category', '$brand', $price, '$img', $stock)");
            if($result) {
                $success = 'Product added successfully!';
                // Refresh the page to update the lists with the new brand/category immediately
                echo "<meta http-equiv='refresh' content='1'>";
            } else {
                $error = 'Failed to add product';
            }
        }
    }
}

// Edit Product
if(isset($_POST['edit_product'])) {
    $id = intval($_POST['product_id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    
    $brand = mysqli_real_escape_string($conn, $_POST['brand']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    
    // Fallback for custom input in edit mode
    if($brand == '__add_new__' && !empty($_POST['new_brand_input'])) {
        $brand = mysqli_real_escape_string($conn, $_POST['new_brand_input']);
    }
    if($category == '__add_new__' && !empty($_POST['new_category_input'])) {
        $category = mysqli_real_escape_string($conn, $_POST['new_category_input']);
    }

    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);

    // Validation
    if ($stock < 0) {
        $error = 'Stock cannot be negative.';
    } elseif ($price < 0) {
        $error = 'Price cannot be negative.';
    } else {
        $query = "UPDATE products SET name='$name', description='$description', category='$category', brand='$brand', price=$price, stock=$stock WHERE id=$id";

        if(isset($_FILES['image']) && $_FILES['image']['name']) {
            $allowed = ['image/jpeg', 'image/png', 'image/gif'];
            $maxSize = 2 * 1024 * 1024; // 2MB
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $_FILES['image']['tmp_name']);
            finfo_close($finfo);

            if (!in_array($mime, $allowed)) {
                $error = 'Invalid image type. Only JPG, PNG and GIF are allowed.';
            } elseif ($_FILES['image']['size'] > $maxSize) {
                $error = 'Image too large. Maximum size is 2MB.';
            } else {
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $img = uniqid('p_', true) . '.' . $ext;
                if(!move_uploaded_file($_FILES['image']['tmp_name'], "../../frontend/assets/images/".$img)) {
                    $error = 'Failed to upload image.';
                } else {
                    $query = "UPDATE products SET name='$name', description='$description', category='$category', brand='$brand', price=$price, stock=$stock, image='$img' WHERE id=$id";
                }
            }
        }

        if (empty($error)) {
            if(mysqli_query($conn, $query)) {
                $success = 'Product updated successfully!';
                echo "<meta http-equiv='refresh' content='1'>";
            } else {
                $error = 'Failed to update product';
            }
        }
    }
}

// Delete Product
if(isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    
    // Check if product has items in pending orders
    $pending_check = mysqli_query($conn, "SELECT COUNT(*) as pending_count FROM order_items oi JOIN orders o ON oi.order_id = o.id WHERE oi.product_id = " . $id . " AND o.status = 'pending'");
    $pending_result = mysqli_fetch_assoc($pending_check);
    
    if($pending_result['pending_count'] > 0) {
        $error = 'Cannot delete product! It has ' . $pending_result['pending_count'] . ' item(s) in pending orders.';
    } else {
        mysqli_query($conn, "DELETE FROM order_items WHERE product_id = " . $id);
        if(mysqli_query($conn, "DELETE FROM products WHERE id = " . $id)) {
            $success = 'Product deleted successfully!';
        } else {
            $error = 'Failed to delete product. ' . mysqli_error($conn);
        }
    }
}

$products = mysqli_query($conn, "SELECT * FROM products ORDER BY created_at DESC");
?>

<div class="container my-5">
    <div class="section-header d-flex justify-content-between align-items-center">
        <h2><i class="fas fa-box"></i> Manage Products</h2>
        <div>
            <a href="admin_dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Admin
                Dashboard</a>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                <i class="fas fa-plus"></i> Add Product
            </button>
        </div>
    </div>

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

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Brand</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($product = mysqli_fetch_assoc($products)): ?>
                <tr>
                    <td>
                        <img src="../../frontend/assets/images/<?php echo $product['image']; ?>" alt=""
                            style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;"
                            onerror="this.src='https://via.placeholder.com/50?text=No+Image'">
                    </td>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo htmlspecialchars($product['brand']); ?></td>
                    <td><span class="badge bg-info"><?php echo $product['category']; ?></span></td>
                    <td>$<?php echo $product['price']; ?></td>
                    <td><?php echo $product['stock']; ?></td>
                    <td>
                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editProductModal"
                            onclick="loadProductData(this)" data-id="<?php echo $product['id']; ?>"
                            data-name="<?php echo htmlspecialchars($product['name'], ENT_QUOTES); ?>"
                            data-description="<?php echo htmlspecialchars($product['description'], ENT_QUOTES); ?>"
                            data-category="<?php echo htmlspecialchars($product['category'], ENT_QUOTES); ?>"
                            data-brand="<?php echo htmlspecialchars($product['brand'], ENT_QUOTES); ?>"
                            data-price="<?php echo $product['price']; ?>" data-stock="<?php echo $product['stock']; ?>"
                            data-image="<?php echo htmlspecialchars($product['image'], ENT_QUOTES); ?>">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <a href="manage_products.php?delete=<?php echo $product['id']; ?>" class="btn btn-sm btn-danger"
                            onclick="return confirm('Delete this product?')">
                            <i class="fas fa-trash"></i> Delete
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-plus"></i> Add New Product</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="form-label">Product Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Brand</label>
                        <div class="input-group">
                            <select name="brand" id="add_brand" class="form-control" required>
                                <option value="">-- Select Brand --</option>
                                <?php foreach($all_brands as $brand_opt): ?>
                                <option value="<?php echo htmlspecialchars($brand_opt); ?>">
                                    <?php echo htmlspecialchars($brand_opt); ?></option>
                                <?php endforeach; ?>
                                <option value="__add_new__">+ Add New Brand</option>
                            </select>
                        </div>
                        <small class="text-muted" id="add_brand_help" style="display:none;">Enter new brand name and
                            press Tab or click outside</small>
                        <input type="text" id="add_brand_input" name="new_brand_input" class="form-control mt-2"
                            placeholder="New brand name" style="display:none;">
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Category</label>
                        <div class="input-group">
                            <select name="category" id="add_category" class="form-control" required>
                                <option value="">-- Select Category --</option>
                                <?php foreach($all_categories as $cat_opt): ?>
                                <option value="<?php echo htmlspecialchars($cat_opt); ?>">
                                    <?php echo htmlspecialchars($cat_opt); ?></option>
                                <?php endforeach; ?>
                                <option value="__add_new__">+ Add New Category</option>
                            </select>
                        </div>
                        <small class="text-muted" id="add_category_help" style="display:none;">Enter new category name
                            and press Tab or click outside</small>
                        <input type="text" id="add_category_input" name="new_category_input" class="form-control mt-2"
                            placeholder="New category name" style="display:none;">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Price</label>
                            <input type="number" name="price" class="form-control" step="0.01" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stock</label>
                            <input type="number" name="stock" class="form-control" value="10" required>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Product Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_product" class="btn btn-primary"><i class="fas fa-save"></i> Add
                        Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editProductModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="product_id" id="edit_product_id">
                    <div class="form-group mb-3">
                        <label class="form-label">Product Name</label>
                        <input type="text" id="edit_name" name="name" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Description</label>
                        <textarea id="edit_description" name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Brand</label>
                        <select id="edit_brand" name="brand" class="form-control" required>
                            <option value="">-- Select Brand --</option>
                            <?php foreach($all_brands as $brand_opt): ?>
                            <option value="<?php echo htmlspecialchars($brand_opt); ?>">
                                <?php echo htmlspecialchars($brand_opt); ?></option>
                            <?php endforeach; ?>
                            <option value="__add_new__">+ Add New Brand</option>
                        </select>
                        <small class="text-muted" id="edit_brand_help" style="display:none;">Enter new brand name and
                            press Tab or click outside</small>
                        <input type="text" id="edit_brand_input" name="new_brand_input" class="form-control mt-2"
                            placeholder="New brand name" style="display:none;">
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Category</label>
                        <select id="edit_category" name="category" class="form-control" required>
                            <option value="">-- Select Category --</option>
                            <?php foreach($all_categories as $cat_opt): ?>
                            <option value="<?php echo htmlspecialchars($cat_opt); ?>">
                                <?php echo htmlspecialchars($cat_opt); ?></option>
                            <?php endforeach; ?>
                            <option value="__add_new__">+ Add New Category</option>
                        </select>
                        <small class="text-muted" id="edit_category_help" style="display:none;">Enter new category name
                            and press Tab or click outside</small>
                        <input type="text" id="edit_category_input" name="new_category_input" class="form-control mt-2"
                            placeholder="New category name" style="display:none;">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Price</label>
                            <input type="number" id="edit_price" name="price" class="form-control" step="0.01" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stock</label>
                            <input type="number" id="edit_stock" name="stock" class="form-control" required>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Product Image (leave empty to keep current)</label>
                        <input type="file" id="edit_image" name="image" class="form-control" accept="image/*">
                        <div class="mt-2">
                            <img id="edit_image_preview" src="" alt=""
                                style="max-width:120px; max-height:80px; object-fit:cover; display:none;" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="edit_product" class="btn btn-warning"><i class="fas fa-save"></i> Save
                        Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function loadProductData(btn) {
    var id = btn.getAttribute('data-id');
    var name = btn.getAttribute('data-name');
    var description = btn.getAttribute('data-description');
    var category = btn.getAttribute('data-category');
    var brand = btn.getAttribute('data-brand');
    var price = btn.getAttribute('data-price');
    var stock = btn.getAttribute('data-stock');
    var image = btn.getAttribute('data-image');

    document.getElementById('edit_product_id').value = id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_description').value = description;

    // Set Brand (Check if it exists in the list, if not, append it dynamically)
    var brandSelect = document.getElementById('edit_brand');
    var brandExists = Array.from(brandSelect.options).some(option => option.value === brand);
    if (!brandExists && brand) {
        var option = document.createElement('option');
        option.value = brand;
        option.text = brand;
        brandSelect.insertBefore(option, brandSelect.querySelector('option[value="__add_new__"]'));
    }
    brandSelect.value = brand;

    // Set Category (Check if it exists in the list, if not, append it dynamically)
    var catSelect = document.getElementById('edit_category');
    var catExists = Array.from(catSelect.options).some(option => option.value === category);
    if (!catExists && category) {
        var option = document.createElement('option');
        option.value = category;
        option.text = category;
        catSelect.insertBefore(option, catSelect.querySelector('option[value="__add_new__"]'));
    }
    catSelect.value = category;

    document.getElementById('edit_price').value = price;
    document.getElementById('edit_stock').value = stock;

    var preview = document.getElementById('edit_image_preview');
    if (image) {
        preview.src = '../../frontend/assets/images/' + image;
        preview.style.display = 'inline-block';
    } else {
        preview.style.display = 'none';
    }
}

// Preview new image when chosen
document.addEventListener('DOMContentLoaded', function() {
    var imgInput = document.getElementById('edit_image');
    if (imgInput) {
        imgInput.addEventListener('change', function(e) {
            var file = e.target.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(ev) {
                    var preview = document.getElementById('edit_image_preview');
                    preview.src = ev.target.result;
                    preview.style.display = 'inline-block';
                }
                reader.readAsDataURL(file);
            }
        });
    }

    // --- Helper function for handling "Add New" toggle ---
    function setupAddNewHandler(selectId, inputId, helpId) {
        var select = document.getElementById(selectId);
        var input = document.getElementById(inputId);
        var help = document.getElementById(helpId);

        if (!select) return;

        select.addEventListener('change', function() {
            if (this.value === '__add_new__') {
                input.style.display = 'block';
                help.style.display = 'block';
                input.focus();
            } else {
                input.style.display = 'none';
                help.style.display = 'none';
                input.value = '';
            }
        });

        input.addEventListener('blur', function() {
            if (this.value.trim() && select.value === '__add_new__') {
                var newValue = this.value.trim();

                // Add to current select
                var option = document.createElement('option');
                option.value = newValue;
                option.text = newValue;
                select.insertBefore(option, select.querySelector('option[value="__add_new__"]'));
                select.value = newValue;

                // Hide inputs but KEEP value in hidden input just in case
                input.style.display = 'none';
                help.style.display = 'none';

                // Sync with other dropdowns on page (Edit <-> Add)
                var otherSelectId = selectId.startsWith('add') ? selectId.replace('add', 'edit') :
                    selectId.replace('edit', 'add');
                var otherSelect = document.getElementById(otherSelectId);
                if (otherSelect) {
                    var otherOption = document.createElement('option');
                    otherOption.value = newValue;
                    otherOption.text = newValue;
                    otherSelect.insertBefore(otherOption, otherSelect.querySelector(
                        'option[value="__add_new__"]'));
                }
            }
        });
    }

    // Setup all handlers
    setupAddNewHandler('add_brand', 'add_brand_input', 'add_brand_help');
    setupAddNewHandler('edit_brand', 'edit_brand_input', 'edit_brand_help');
    setupAddNewHandler('add_category', 'add_category_input', 'add_category_help');
    setupAddNewHandler('edit_category', 'edit_category_input', 'edit_category_help');
});
</script>

<?php include('../../backend/includes/footer.php'); ?>