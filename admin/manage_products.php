<?php
// CRITICAL: session_start() must be the very first thing to run.
session_start();

// Now, include the header which contains the security check.
require_once 'admin_header.php';
require_once '../includes/db_connect.php';

// Handle any success/error messages passed via session
$success_message = $_SESSION['success_message'] ?? null;
unset($_SESSION['success_message']);

$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['error_message']);

// Fetch all products with their category names
try {
    $sql = "SELECT p.*, c.name AS category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            ORDER BY p.created_at DESC";
    $stmt = $pdo->query($sql);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: Could not fetch products. " . $e->getMessage());
}
?>

<main class="main-content">
    <div class="content-header">
        <h1>Products</h1>
        <div class="header-actions">
            <a href="import_products_csv.php" class="btn-import-csv"><i class="fas fa-file-csv"></i> Import CSV</a>
            <a href="add_product.php" class="btn-add-new"><i class="fas fa-plus"></i> Add New</a>
        </div>
    </div>
    
    <section class="content-body">
        <?php if ($success_message): ?>
            <div class="success-message-box"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>
        
        <?php if ($error_message): ?>
            <div class="error-message-box"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        
        <div class="content-box">
            <div class="table-responsive">
                <table class="product-table">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Status</th>
                            <th>Stock</th>
                            <th>Price</th>
                            <th>Category</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($products)): ?>
                            <tr>
                                <td colspan="8" style="text-align: center;">No products found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($product['image_url'])): ?>
                                            <!-- UPDATED PATH: Points to the new 'product-image' directory -->
                                            <img src="../uploads/product-image/<?php echo htmlspecialchars($product['image_url']); ?>" 
                                                 alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                                 class="product-thumbnail">
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                                    <td>
                                        <?php if ($product['is_featured'] == 1): ?>
                                            <span class="badge badge-featured">Featured</span>
                                        <?php else: ?>
                                            <span class="text-muted">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($product['stock'] ?? 0); ?></td>
                                    <td>₹<?php echo htmlspecialchars(number_format($product['price'], 2)); ?></td>
                                    <td><?php echo htmlspecialchars($product['category_name'] ?? 'Uncategorized'); ?></td>
                                    <td><?php echo date('d M, Y', strtotime($product['created_at'])); ?></td>
                                    <td class="action-links">
                                        <a href="edit_product.php?id=<?php echo $product['id']; ?>"><i class="fas fa-edit"></i> Edit</a>
                                        <a href="delete_product.php?id=<?php echo $product['id']; ?>" class="action-delete" onclick="return confirm('Are you sure you want to delete this product?');"><i class="fas fa-trash"></i> Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</main>

<?php require_once 'admin_footer.php'; ?>