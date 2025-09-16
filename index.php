<?php
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// Fetch data for the page
$new_arrivals = getNewArrivalProducts(8); // Fetch more products for a good carousel experience
$featured_products = getFeaturedProducts(8);
$all_categories = getAllCategories();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Divine  syncserve - Wedding & Festive Clothing</title>
    <!-- SwiperJS CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css"/>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>

    <?php require_once 'includes/header.php'; ?>

    <main>
        <!-- Hero Carousel Section -->
        <section class="hero-carousel">
            <div class="swiper hero-swiper">
                <div class="swiper-wrapper">
                    <!-- Slide 1x -->
                    <div class="swiper-slide">
                        <div class="hero-slide">
                            <a href="category.php?id=6"><img src="images/1.png"alt="Elegant Wedding Collection"></a>
                        </div>
                    </div>
                    <!-- Slide 2 -->
                    <div class="swiper-slide">
                        <div class="hero-slide">
                            <a href="category.php?id=4"><img src="images/2.png" alt="Festive Traditional Wear"></a>
                        </div>
                    </div>
                    <!-- Slide 3 -->
                    <div class="swiper-slide">
                        <div class="hero-slide">
                            <a href="category.php?id=5"><img src="images/3.png" alt="Luxury Fashion Collection"></a>
                        </div>
                    </div>
                </div>
                
                <!-- Navigation arrows -->
                <div class="swiper-button-next hero-button-next"></div>
                <div class="swiper-button-prev hero-button-prev"></div>
                
                <!-- Pagination -->
                <div class="swiper-pagination hero-pagination"></div>
            </div>
        </section>

        <!-- Brand Promise Section -->
        <section class="brand-promise-section">
            <div class="container">
                <div class="promise-grid">
                    <div class="promise-item">
                        <i class="fas fa-gem"></i>
                        <h3>Exquisite Craftsmanship</h3>
                        <p>Every piece is a work of art, detailed with precision and care.</p>
                    </div>
                    <div class="promise-item">
                        <i class="fas fa-ruler-combined"></i>
                        <h3>Perfect Fit Guarantee</h3>
                        <p>Custom tailoring options to ensure your attire is uniquely yours.</p>
                    </div>
                    <div class="promise-item">
                        <i class="fas fa-leaf"></i>
                        <h3>Premium Fabrics</h3>
                        <p>We source only the finest materials for a luxurious feel and finish.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- New Arrivals Carousel -->
     <section class="product-section">
             <div class="container">
                <h2 class="section-title">New Arrivals</h2>
                 <!-- Swiper -->
                <div class="swiper product-carousel">
                    <div class="swiper-wrapper">
                         <?php foreach ($new_arrivals as $product): ?>
                         <div class="swiper-slide">
                            <div class="product-card">
                                 <div class="product-image-container">
                                     <a href="product_details.php?id=<?php echo $product['id']; ?>">
                                        <img src="uploads/product-image/<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                     </a>
                                     
                                     <!-- Wishlist Button -->
                                     <button class="product-wishlist-btn" title="Add to Wishlist">
                                         <i class="far fa-heart"></i>
                                     </button>                                     
                                     <!-- Hover Overlay -->
                                     <div class="product-hover-overlay">
                                         <a href="product_details.php?id=<?php echo $product['id']; ?>" class="btn-secondary">Quick View</a>
                                     </div>
                                </div>
                                <div class="product-info">
                                    <a href="product_details.php?id=<?php echo $product['id']; ?>" class="product-name"><?php echo htmlspecialchars($product['name']); ?></a>
                                    
                                    <div class="product-price">₹<?php echo number_format($product['price'], 2); ?></div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <!-- Add Navigation -->
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                </div>
                 <div class="view-all-link">
                    <a href="/category.php?id=4" class="btn-secondary-outline">View All Arrivals</a>
                </div>
            </div>
        </section>

        <!-- Featured Collections Carousel -->
        <section class="product-section bg-light">
             <div class="container">
                <h2 class="section-title">Featured Collections</h2>
                 <!-- Swiper -->
                <div class="swiper product-carousel">
                    <div class="swiper-wrapper">
                         <?php foreach ($featured_products as $product): ?>
                         <div class="swiper-slide">
                            <div class="product-card">
                                 <div class="product-image-container">
                                     <a href="product_details.php?id=<?php echo $product['id']; ?>">
                                        <img src="uploads/product-image/<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                     </a>
                                     
                                     <!-- Wishlist Button -->
                                     <button class="product-wishlist-btn" title="Add to Wishlist">
                                         <i class="far fa-heart"></i>
                                     </button>
                                     
                                     <!-- Featured Badge -->
                                     <span class="product-sale-badge">Featured</span>
                                     
                                     <!-- Hover Overlay -->
                                     <div class="product-hover-overlay">
                                         <a href="product_details.php?id=<?php echo $product['id']; ?>" class="btn-secondary">Quick View</a>
                                     </div>
                                </div>
                                <div class="product-info">
                                    <a href="product_details.php?id=<?php echo $product['id']; ?>" class="product-name"><?php echo htmlspecialchars($product['name']); ?></a>
                                    
                                    <div class="product-price">₹<?php echo number_format($product['price'], 2); ?></div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <!-- Add Navigation -->
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                </div>
                 <div class="view-all-link">
                    <a href="category.php?id=5" class="btn-secondary-outline">View All Featured</a>
                </div>
            </div>
        </section>

<!-- Shop by Category Section -->
        <section class="category-section">
            <div class="container">
                <h2 class="section-title">Shop By Category</h2>
                <div class="category-grid">
                    <?php foreach($all_categories as $index => $category): ?>
                    <?php if ($index >= 6) break; ?>
                    <div class="category-card">
                        <img src="uploads/categories/<?php echo htmlspecialchars($category['image_url'] ?? 'placeholder.png'); ?>" alt="<?php echo htmlspecialchars($category['name']); ?>">
                        <a href="category.php?id=<?php echo $category['id']; ?>" class="category-link">
                            <div class="category-name"><?php echo htmlspecialchars($category['name']); ?></div>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
        
    </main>

    <?php require_once 'includes/footer.php'; ?>
    
    <!-- SwiperJS JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>