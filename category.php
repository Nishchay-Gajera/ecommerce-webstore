<?php
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// Get category ID from URL
$category_id = $_GET['id'] ?? null;
if (!$category_id || !is_numeric($category_id)) {
    header('Location: index.php');
    exit;
}

// Get category details
try {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$category_id]);
    $category = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$category) {
        header('Location: index.php');
        exit;
    }
} catch (PDOException $e) {
    header('Location: index.php');
    exit;
}

// Get filter parameters
$sort_by = $_GET['sort'] ?? 'newest';
$min_price = $_GET['min_price'] ?? '';
$max_price = $_GET['max_price'] ?? '';
$search = $_GET['search'] ?? '';
$featured_only = isset($_GET['featured']) ? 1 : 0;

// Build query based on filters
$where_conditions = ["p.category_id = ?"];
$params = [$category_id];

// Price filter
if ($min_price !== '' && is_numeric($min_price)) {
    $where_conditions[] = "p.price >= ?";
    $params[] = $min_price;
}
if ($max_price !== '' && is_numeric($max_price)) {
    $where_conditions[] = "p.price <= ?";
    $params[] = $max_price;
}

// Search filter
if ($search !== '') {
    $where_conditions[] = "p.name LIKE ?";
    $params[] = "%$search%";
}

// Featured filter
if ($featured_only) {
    $where_conditions[] = "p.is_featured = 1";
}

// Sort options
$order_clause = match($sort_by) {
    'price_low' => 'ORDER BY p.price ASC',
    'price_high' => 'ORDER BY p.price DESC',
    'name' => 'ORDER BY p.name ASC',
    'oldest' => 'ORDER BY p.created_at ASC',
    default => 'ORDER BY p.created_at DESC' // newest
};

// Build final query
$where_clause = implode(' AND ', $where_conditions);
$sql = "SELECT p.*, c.name AS category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE $where_clause 
        $order_clause";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $products = [];
}

// Get price range for this category
try {
    $stmt = $pdo->prepare("SELECT MIN(price) as min_price, MAX(price) as max_price FROM products WHERE category_id = ?");
    $stmt->execute([$category_id]);
    $price_range = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $price_range = ['min_price' => 0, 'max_price' => 10000];
}

require_once 'includes/header.php';
?>

<main class="category-page-content">
    <div class="container">
        <!-- Category Header -->
        <div class="category-header">
            <div class="category-banner">
                <?php if ($category['image_url']): ?>
                    <img src="uploads/categories/<?php echo htmlspecialchars($category['image_url']); ?>" alt="<?php echo htmlspecialchars($category['name']); ?>" class="category-banner-image">
                <?php endif; ?>
                <div class="category-banner-content">
                    <h1 class="category-title"><?php echo htmlspecialchars($category['name']); ?></h1>
                    <p class="category-description">Discover our exquisite collection of <?php echo strtolower(htmlspecialchars($category['name'])); ?></p>
                </div>
            </div>
        </div>

        <!-- Filters and Sort Section -->
        <div class="category-controls">
            <div class="filters-section">
                <button class="filter-toggle-btn" id="filterToggle">
                    <i class="fas fa-filter"></i>
                    Filters
                </button>
                
                <div class="sort-section">
                    <label for="sortSelect">Sort by:</label>
                    <select id="sortSelect" class="sort-select">
                        <option value="newest" <?php echo $sort_by === 'newest' ? 'selected' : ''; ?>>Newest First</option>
                        <option value="oldest" <?php echo $sort_by === 'oldest' ? 'selected' : ''; ?>>Oldest First</option>
                        <option value="price_low" <?php echo $sort_by === 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                        <option value="price_high" <?php echo $sort_by === 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                        <option value="name" <?php echo $sort_by === 'name' ? 'selected' : ''; ?>>Name A-Z</option>
                    </select>
                </div>
                
                <div class="results-count">
                    Showing <?php echo count($products); ?> products
                </div>
            </div>
        </div>

        <div class="category-layout">
            <!-- Sidebar Filters -->
            <aside class="filters-sidebar" id="filtersSidebar">
                <div class="filters-container">
                    <form method="GET" action="category.php" id="filtersForm">
                        <input type="hidden" name="id" value="<?php echo $category_id; ?>">
                        <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort_by); ?>">
                        
                        <!-- Search Filter -->
                        <div class="filter-group">
                            <h3 class="filter-title">Search Products</h3>
                            <div class="search-box">
                                <input type="text" name="search" placeholder="Search in <?php echo htmlspecialchars($category['name']); ?>..." 
                                       value="<?php echo htmlspecialchars($search); ?>" class="search-input">
                                <button type="submit" class="search-btn">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Price Range Filter -->
                        <div class="filter-group">
                            <h3 class="filter-title">Price Range</h3>
                            <div class="price-range">
                                <div class="price-inputs">
                                    <input type="number" name="min_price" placeholder="Min" 
                                           value="<?php echo htmlspecialchars($min_price); ?>" 
                                           min="<?php echo $price_range['min_price']; ?>" 
                                           max="<?php echo $price_range['max_price']; ?>" 
                                           class="price-input">
                                    <span class="price-separator">to</span>
                                    <input type="number" name="max_price" placeholder="Max" 
                                           value="<?php echo htmlspecialchars($max_price); ?>" 
                                           min="<?php echo $price_range['min_price']; ?>" 
                                           max="<?php echo $price_range['max_price']; ?>" 
                                           class="price-input">
                                </div>
                                <div class="price-range-info">
                                    Range: ₹<?php echo number_format($price_range['min_price']); ?> - ₹<?php echo number_format($price_range['max_price']); ?>
                                </div>
                            </div>
                        </div>

                        <!-- Featured Filter -->
                        <div class="filter-group">
                            <h3 class="filter-title">Product Type</h3>
                            <div class="checkbox-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="featured" value="1" <?php echo $featured_only ? 'checked' : ''; ?>>
                                    <span class="checkbox-custom"></span>
                                    Featured Products Only
                                </label>
                            </div>
                        </div>

                        <!-- Filter Actions -->
                        <div class="filter-actions">
                            <button type="submit" class="apply-filters-btn">Apply Filters</button>
                            <a href="category.php?id=<?php echo $category_id; ?>" class="clear-filters-btn">Clear All</a>
                        </div>
                    </form>
                </div>
            </aside>

            <!-- Products Grid -->
            <div class="products-section">
                <?php if (empty($products)): ?>
                    <div class="no-products">
                        <div class="no-products-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <h3>No products found</h3>
                        <p>Try adjusting your filters or search terms</p>
                        <a href="category.php?id=<?php echo $category_id; ?>" class="btn-primary">View All Products</a>
                    </div>
                <?php else: ?>
                    <div class="products-grid" id="productsGrid">
                        <?php foreach ($products as $product): ?>
                            <div class="product-card" data-product-id="<?php echo $product['id']; ?>">
                                <div class="product-image-container">
                                    <a href="product_details.php?id=<?php echo $product['id']; ?>">
                                        <img src="uploads/product-image/<?php echo htmlspecialchars($product['image_url'] ?? 'placeholder.png'); ?>" 
                                             alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                             class="product-image">
                                    </a>
                                    
                                    <?php if ($product['is_featured']): ?>
                                        <span class="product-badge featured-badge">Featured</span>
                                    <?php endif; ?>
                                    
                                    <!-- Hover Overlay -->
                                    <div class="product-hover-overlay">
                                        <a href="product_details.php?id=<?php echo $product['id']; ?>" class="btn-secondary">Quick View</a>
                                        <button class="quick-add-cart" data-product-id="<?php echo $product['id']; ?>">
                                            <i class="fas fa-shopping-cart"></i> Add to Cart
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="product-info">
                                    <a href="product_details.php?id=<?php echo $product['id']; ?>" class="product-name">
                                        <?php echo htmlspecialchars($product['name']); ?>
                                    </a>
                                    <div class="product-price">₹<?php echo number_format($product['price'], 2); ?></div>
                                    
                                    <?php if ($product['stock'] > 0): ?>
                                        <?php if ($product['stock'] < 5): ?>
                                            <div class="stock-status low-stock">Only <?php echo $product['stock']; ?> left!</div>
                                        <?php else: ?>
                                            <div class="stock-status in-stock">In Stock</div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <div class="stock-status out-of-stock">Out of Stock</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
<script src="assets/js/script.js"></script>
<script src="assets/js/category.js"></script>
</body>
</html>