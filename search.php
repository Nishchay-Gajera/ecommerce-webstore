<?php
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// Get search parameters from URL
$search_query = $_GET['query'] ?? '';
$category_id = $_GET['category'] ?? '';

// Sanitize the search query
$search_query = trim(filter_var($search_query, FILTER_SANITIZE_STRING));

// Base SQL query
$sql = "SELECT p.*, c.name AS category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.name LIKE ?";
$params = ["%$search_query%"];

// Add category filter if a specific category is selected
if (!empty($category_id) && is_numeric($category_id)) {
    $sql .= " AND p.category_id = ?";
    $params[] = $category_id;
}

$sql .= " ORDER BY p.created_at DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Gracefully handle database errors
    $products = [];
    error_log("Search Error: " . $e->getMessage()); // Log error for debugging
}

// Include the site header
require_once 'includes/header.php';
?>

<main class="search-page-content">
    <div class="container">
        <div class="search-header">
            <h1>Search Results for "<?php echo htmlspecialchars($search_query); ?>"</h1>
            <p><?php echo count($products); ?> results found.</p>
        </div>

        <div class="products-section">
            <?php if (empty($products)): ?>
                <div class="no-products">
                    <div class="no-products-icon">
                        <i class="fas fa-search-minus"></i>
                    </div>
                    <h3>No Products Found</h3>
                    <p>We couldn't find any products matching your search. Please try different keywords or browse our categories.</p>
                    <a href="index.php" class="btn-primary">Back to Home</a>
                </div>
            <?php else: ?>
                <div class="products-grid">
                    <?php foreach ($products as $product): ?>
                        <div class="product-card">
                            <div class="product-image-container">
                                <a href="product_details.php?id=<?php echo $product['id']; ?>">
                                    <img src="uploads/product-image/<?php echo htmlspecialchars($product['image_url'] ?? 'placeholder.png'); ?>" 
                                         alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                         class="product-image">
                                </a>
                                <?php if ($product['is_featured']): ?>
                                    <span class="product-badge featured-badge">Featured</span>
                                <?php endif; ?>
                                <div class="product-hover-overlay">
                                    <a href="product_details.php?id=<?php echo $product['id']; ?>" class="btn-secondary">Quick View</a>
                                </div>
                            </div>
                            <div class="product-info">
                                <a href="product_details.php?id=<?php echo $product['id']; ?>" class="product-name">
                                    <?php echo htmlspecialchars($product['name']); ?>
                                </a>
                                <div class="product-price">₹<?php echo number_format($product['price'], 2); ?></div>
                                <?php if ($product['stock'] > 0): ?>
                                    <div class="stock-status in-stock">In Stock</div>
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
</main>

<?php require_once 'includes/footer.php'; ?>

</body>
</html>
