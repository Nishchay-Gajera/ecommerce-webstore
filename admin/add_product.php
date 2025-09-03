<?php
session_start();
require_once 'admin_header.php';
require_once '../includes/db_connect.php';

// Security check
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$error_message = '';
$name = '';
$description = '';
$price = '';
$stock = '';
$category_id = '';
$is_featured = 0;

// Fetch categories for the dropdown
try {
    $stmt = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Could not fetch categories: " . $e->getMessage());
}

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
            $image_url = null;
            if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
                $target_dir = "../uploads/product-image/";
                if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);

                $image_file_type = strtolower(pathinfo($_FILES["product_image"]["name"], PATHINFO_EXTENSION));
                $new_filename = uniqid('prod_', true) . '.' . $image_file_type;
                $target_file = $target_dir . $new_filename;

                if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
                    $image_url = $new_filename;
                } else {
                     $error_message = "Sorry, there was an error uploading your file.";
                }
            }
            
            if(empty($error_message)) {
                $sql = "INSERT INTO products (name, description, price, stock, category_id, is_featured, image_url) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$name, $description, $price, $stock, $category_id, $is_featured, $image_url]);
                
                $_SESSION['success_message'] = "Product added successfully!";
                header("Location: manage_products.php");
                exit();
            }

        } catch (PDOException $e) {
            $error_message = "Error: " . $e->getMessage();
        }
    }
}
?>

<main class="main-content">
    <div class="content-header">
        <h1>Add New Product</h1>
    </div>
    
    <section class="content-body">
        <?php if ($error_message): ?>
            <div class="error-message-box"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <form action="add_product.php" method="POST" enctype="multipart/form-data">
            <div class="form-layout-flex">
                <div class="form-main-column">
                    <div class="content-box">
                        <div class="form-group">
                            <label for="name">Product Name</label>
                            <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($name); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea id="description" name="description" class="form-control" rows="8"><?php echo htmlspecialchars($description); ?></textarea>
                        </div>
                    </div>
                     <div class="content-box">
                        <h3 class="meta-box-title">Product Data</h3>
                        <div class="meta-box-content">
                            <div class="form-row">
                                <div class="form-group form-group-half">
                                    <label for="price">Price ($)</label>
                                    <input type="number" step="0.01" id="price" name="price" class="form-control" value="<?php echo htmlspecialchars($price); ?>" required>
                                </div>
                                <div class="form-group form-group-half">
                                    <label for="stock">Stock Quantity</label>
                                    <input type="number" id="stock" name="stock" class="form-control" value="<?php echo htmlspecialchars($stock); ?>" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-side-column">
                     <div class="content-box meta-box">
                         <h3 class="meta-box-title">Publish</h3>
                         <div class="meta-box-content">
                             <button type="submit" class="btn-publish">Publish</button>
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
                                        <option value="<?php echo $cat['id']; ?>" <?php echo ($category_id == $cat['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cat['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                             <div class="form-group">
                                <input type="checkbox" id="is_featured" name="is_featured" value="1" <?php echo ($is_featured == 1) ? 'checked' : ''; ?>>
                                <label for="is_featured">Mark as Featured</label>
                            </div>
                        </div>
                    </div>
                     <div class="content-box meta-box">
                        <h3 class="meta-box-title">Product Image</h3>
                         <div class="meta-box-content">
                            <div class="form-group">
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

