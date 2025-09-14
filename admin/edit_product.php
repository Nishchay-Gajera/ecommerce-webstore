<?php
session_start();
require_once 'admin_header.php';
require_once '../includes/db_connect.php';

// Security check
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$product_id = $_GET['id'] ?? null;
if (!$product_id || !is_numeric($product_id)) {
    header("Location: manage_products.php");
    exit();
}

$error_message = '';

// Fetch categories for the dropdown
try {
    $stmt = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Could not fetch categories: " . $e->getMessage());
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = trim($_POST['price']);
    $stock = trim($_POST['stock']);
    $category_id = $_POST['category_id'] ?? null;
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    
    if (empty($name) || empty($price) || empty($stock)) {
        $error_message = "Please fill in all required fields.";
    } else {
        try {
            // Get old image URL for potential deletion
            $stmt = $pdo->prepare("SELECT image_url FROM products WHERE id = ?");
            $stmt->execute([$product_id]);
            $old_image_url = $stmt->fetchColumn();
            
            $image_url_to_update = $old_image_url;

            // Image Upload Logic
            if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
                $target_dir = "../uploads/product-image/";
                if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);

                $image_file_type = strtolower(pathinfo($_FILES["product_image"]["name"], PATHINFO_EXTENSION));
                $new_filename = uniqid('prod_', true) . '.' . $image_file_type;
                $target_file = $target_dir . $new_filename;
                
                if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
                    $image_url_to_update = $new_filename;
                    if ($old_image_url && file_exists($target_dir . $old_image_url)) {
                        unlink($target_dir . $old_image_url);
                    }
                } else {
                    $error_message = "Sorry, there was an error uploading your file.";
                }
            }

            if (empty($error_message)) {
                $sql = "UPDATE products SET name = ?, description = ?, price = ?, stock = ?, category_id = ?, is_featured = ?, image_url = ? WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$name, $description, $price, $stock, $category_id, $is_featured, $image_url_to_update, $product_id]);
                
                $_SESSION['success_message'] = "Product updated successfully!";
                header("Location: manage_products.php");
                exit();
            }
        } catch (PDOException $e) {
            $error_message = "Error updating product: " . $e->getMessage();
        }
    }
}

// Fetch current product data for the form
try {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$product) {
        header("Location: manage_products.php");
        exit();
    }
} catch (PDOException $e) {
    die("Could not fetch product: " . $e->getMessage());
}
?>

<main class="main-content">
    <div class="content-header">
        <h1>Edit Product</h1>
    </div>
    
    <section class="content-body">
        <?php if ($error_message): ?>
            <div class="error-message-box"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <form action="edit_product.php?id=<?php echo $product_id; ?>" method="POST" enctype="multipart/form-data">
            <div class="form-layout-flex">
                <div class="form-main-column">
                    <div class="content-box">
                        <div class="form-group">
                            <label for="name">Product Name</label>
                            <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" class="form-control" rows="8"><?php echo htmlspecialchars($product['description']); ?></textarea>
                        </div>
                    </div>
                     <div class="content-box">
                        <h3 class="meta-box-title">Product Data</h3>
                        <div class="meta-box-content">
                            <div class="form-row">
                                <div class="form-group form-group-half">
                                    <label for="price">Price (₹)</label>
                                    <input type="number" step="0.01" id="price" name="price" class="form-control" value="<?php echo htmlspecialchars($product['price']); ?>" required>
                                </div>
                                <div class="form-group form-group-half">
                                    <label for="stock">Stock Quantity</label>
                                    <input type="number" id="stock" name="stock" class="form-control" value="<?php echo htmlspecialchars($product['stock']); ?>" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-side-column">
                     <div class="content-box meta-box">
                         <h3 class="meta-box-title">Publish</h3>
                         <div class="meta-box-content">
                             <button type="submit" class="btn-publish">Update</button>
                             <a href="manage_products.php" class="btn-cancel">Cancel</a>
                         </div>
                    </div>
                    <div class="content-box meta-box">
                        <h3 class="meta-box-title">Category</h3>
                        <div class="meta-box-content">
                            <div class="form-group">
                                <label for="category_id" class="sr-only">Product Category</label>
                                <select id="category_id" name="category_id" class="form-control">
                                    <option value="">Uncategorized</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?php echo $cat['id']; ?>" <?php echo ($product['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cat['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                             <div class="form-group">
                                <input type="checkbox" id="is_featured" name="is_featured" value="1" <?php echo ($product['is_featured'] == 1) ? 'checked' : ''; ?>>
                                <label for="is_featured">Mark as Featured</label>
                            </div>
                        </div>
                    </div>
                     <div class="content-box meta-box">
                        <h3 class="meta-box-title">Product Image</h3>
                         <div class="meta-box-content">
                            <div class="form-group">
                                <?php if (!empty($product['image_url'])): ?>
                                    <img src="../uploads/product-image/<?php echo htmlspecialchars($product['image_url']); ?>" alt="Current Image" class="product-thumbnail" style="max-width: 100%; height: auto; margin-bottom: 10px; border-radius: 4px;">
                                <?php endif; ?>
                                <label for="product_image" class="sr-only">Upload Image</label>
                                <input type="file" id="product_image" name="product_image" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
</main>

<?php require_once 'admin_footer.php'; ?>

