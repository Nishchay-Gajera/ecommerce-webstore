<?php
// Include necessary files
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';
require_once 'includes/header.php';

// Check if a product ID is provided in the URL
$product_id = $_GET['id'] ?? null;
if (!$product_id || !is_numeric($product_id)) {
    // Redirect to the homepage or a product list page if no valid ID is provided
    header('Location: index.php');
    exit;
}

// Fetch the product details from the database
$product = getProductById($product_id);

// If the product doesn't exist, redirect
if (!$product) {
    header('Location: index.php');
    exit;
}

// Fetch related products from the same category with a limit of 7
$related_products = getProductsByCategoryId($product['category_id'], 7, $product_id);

// Fetch the product stock for the alert
$product_stock = getProductStock($product_id);

?>

<link rel="stylesheet" href="assets/css/product_details.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css"/>


<main class="product-page-content">
    <div class="container product-details-container">
        <div class="product-image-section">
            <img src="uploads/product-image/<?php echo htmlspecialchars($product['image_url'] ?? 'placeholder.png'); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="main-product-image">
        </div>
        <div class="product-info-section">
            <h1 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h1>
            <p class="product-price">$<?php echo htmlspecialchars(number_format($product['price'], 2)); ?></p>
            <?php if ($product_stock !== null && $product_stock < 5): ?>
                <div class="low-stock-alert">Low Stock! Only <?php echo htmlspecialchars($product_stock); ?> left.</div>
            <?php endif; ?>
            <div class="short-description">
                <?php echo nl2br(htmlspecialchars($product['description'])); ?>
            </div>
            
            <form action="add_to_cart.php" method="POST" class="add-to-cart-form">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <div class="quantity-selector">
                    <button type="button" class="quantity-btn decrease-btn">-</button>
                    <input type="number" name="quantity" value="1" min="1" class="quantity-input">
                    <button type="button" class="quantity-btn increase-btn">+</button>
                </div>
                <button type="submit" class="btn-primary add-to-cart-btn">Add to Cart</button>
            </form>

            <div class="product-meta">
                <div class="meta-item">
                    <span>Category: </span>
                    <a href="category.php?id=<?php echo $product['category_id']; ?>" class="meta-link">
                        <?php echo htmlspecialchars($product['category_name']); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Related Products Carousel Section -->
    <section class="related-products-section">
        <div class="container">
            <h2 class="section-title">You Might Also Like</h2>
            
            <!-- Swiper Carousel structure -->
            <div class="swiper related-products-carousel">
                <div class="swiper-wrapper">
                    <?php if (!empty($related_products)): ?>
                        <?php foreach ($related_products as $related_product): ?>
                            <div class="swiper-slide">
                                <div class="product-card">
                                    <a href="product_details.php?id=<?php echo $related_product['id']; ?>">
                                        <img src="uploads/product-image/<?php echo htmlspecialchars($related_product['image_url'] ?? 'placeholder.png'); ?>" 
                                             alt="<?php echo htmlspecialchars($related_product['name']); ?>" 
                                             class="product-card-image">
                                    </a>
                                    <div class="product-card-content">
                                        <a href="product_details.php?id=<?php echo $related_product['id']; ?>" class="product-card-title"><?php echo htmlspecialchars($related_product['name']); ?></a>
                                        <div class="product-card-price">$<?php echo htmlspecialchars(number_format($related_product['price'], 2)); ?></div>
                                        <div class="product-card-actions">
                                            <a href="product_details.php?id=<?php echo $related_product['id']; ?>" class="view-btn">View</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="swiper-slide">
                            <p class="text-center w-full">No related products found.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Add Navigation and Pagination -->
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
                <div class="swiper-pagination"></div>
            </div>

        </div>
    </section>
</main>

<?php require_once 'includes/footer.php'; ?>

<!-- The global script comes first -->
<script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
<script src="assets/js/script.js"></script>
<!-- The product details script comes second -->
<script src="assets/js/product_details.js"></script>
