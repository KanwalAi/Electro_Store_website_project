<?php 
$page_title = 'Shop';
include('../../backend/includes/header.php');
?>

<div class="container my-5">
    <div class="section-header">
        <h2><i class="fas fa-shopping-cart"></i> Browse Products</h2>
    </div>

    <div class="search-section">
        <div class="input-group">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
            <input type="text" id="searchInput" class="form-control" placeholder="Search products...">
        </div>
    </div>

    <div class="shop-container">
        <aside class="sidebar">
            <h3>Filters</h3>

            <h4>Brand</h4>
            <div id="brand_filters"></div>

            <h4>Category</h4>
            <div id="category_filters"></div>

            <h4>Price Range</h4>
            <div class="form-group">
                <label for="priceRange" class="form-label">Max Price: $<span id="priceValue">5000</span></label>
                <input type="range" id="priceRange" class="form-range" min="0" max="5000" value="5000">
            </div>

            <button type="button" id="resetFilters" class="btn btn-secondary w-100 mt-3">
                <i class="fas fa-redo"></i> Reset Filters
            </button>
        </aside>

        <section class="main-content">
            <div id="filter_data" class="product-grid">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    loadFilters();
    filter_data();
});

// Load brands and categories dynamically from database
function loadFilters() {
    $.ajax({
        url: "../../backend/pages/fetch_filters.php",
        method: "GET",
        dataType: "json",
        success: function(data) {
            // Load brands
            var brandHtml = '';
            data.brands.forEach(function(brand) {
                var safeId = 'brand_' + brand.toLowerCase().replace(/[^a-z0-9]/gi, '_');
                brandHtml += '<div class="form-check">';
                brandHtml +=
                    '<input class="form-check-input common_selector brand" type="checkbox" value="' +
                    brand + '" id="' + safeId + '">';
                brandHtml += '<label class="form-check-label" for="' + safeId + '">' + brand +
                    '</label>';
                brandHtml += '</div>';
            });
            $('#brand_filters').html(brandHtml);

            // Load categories
            var categoryHtml = '';
            data.categories.forEach(function(category) {
                var safeId = 'cat_' + category.toLowerCase().replace(/[^a-z0-9]/gi, '_');
                categoryHtml += '<div class="form-check">';
                categoryHtml +=
                    '<input class="form-check-input common_selector category" type="checkbox" value="' +
                    category + '" id="' + safeId + '">';
                categoryHtml += '<label class="form-check-label" for="' + safeId + '">' + category +
                    '</label>';
                categoryHtml += '</div>';
            });
            $('#category_filters').html(categoryHtml);

            // Re-attach event listeners after loading
            $('.common_selector').click(function() {
                filter_data();
            });
        },
        error: function() {
            console.error('Failed to load filters');
        }
    });
}

function filter_data() {
    var action = 'fetch_data';
    var brand = get_filter('brand');
    var category = get_filter('category');
    var maxPrice = $('#priceRange').val();
    var search = $('#searchInput').val();

    $.ajax({
        url: "../../backend/pages/fetch-data.php",
        method: "POST",
        data: {
            action: action,
            brand: brand,
            category: category,
            maxPrice: maxPrice,
            search: search
        },
        success: function(data) {
            $('#filter_data').html(data);
        }
    });
}

function get_filter(class_name) {
    var filter = [];
    $('.' + class_name + ':checked').each(function() {
        filter.push($(this).val());
    });
    return filter;
}

$('.common_selector').click(function() {
    filter_data();
});

$('#priceRange').on('input', function() {
    $('#priceValue').text($(this).val());
    filter_data();
});

$('#searchInput').on('keyup', function() {
    filter_data();
});

$('#resetFilters').click(function() {
    $('.common_selector').prop('checked', false);
    $('#priceRange').val(1000);
    $('#priceValue').text('1000');
    $('#searchInput').val('');
    filter_data();
});
</script>

<?php include('../../backend/includes/footer.php'); ?>