<?php
// Start the session and include necessary files.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'admin_header.php';
require_once '../includes/db_connect.php';

// --- Security: Generate CSRF Token ---
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

$error_message = '';
$success_message = '';

// Check for success/error messages from other pages (like edit/delete)
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}


// --- Logic for Adding a New Category ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_category'])) {
    // CSRF Token Validation
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error_message = "CSRF token mismatch. Action denied.";
    } else {
        $name = trim($_POST['name']);
        $image_url = null;

        if (empty($name)) {
            $error_message = "Category name cannot be empty.";
        } else {
            try {
                // Handle Image Upload
                if (isset($_FILES['category_image']) && $_FILES['category_image']['error'] == UPLOAD_ERR_OK) {
                    $target_dir = "../uploads/categories/";
                    
                    if (!is_dir($target_dir)) {
                        mkdir($target_dir, 0755, true);
                    }
                    
                    $image_file_type = strtolower(pathinfo($_FILES["category_image"]["name"], PATHINFO_EXTENSION));
                    $new_filename = uniqid('cat_', true) . '.' . $image_file_type;
                    $target_file = $target_dir . $new_filename;
                    
                    $check = getimagesize($_FILES["category_image"]["tmp_name"]);
                    if ($check !== false && in_array($image_file_type, ['jpg', 'png', 'jpeg', 'gif'])) {
                        if (move_uploaded_file($_FILES["category_image"]["tmp_name"], $target_file)) {
                            $image_url = $new_filename;
                        } else {
                            $error_message = "Sorry, there was an error uploading your file.";
                        }
                    } else {
                        $error_message = "File is not a valid image (JPG, JPEG, PNG, GIF allowed).";
                    }
                }

                if (empty($error_message)) {
                    $sql = "INSERT INTO categories (name, image_url) VALUES (?, ?)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([$name, $image_url]);
                    $success_message = "Category '" . htmlspecialchars($name) . "' added successfully!";
                }

            } catch (PDOException $e) {
                if ($e->getCode() == 23000) {
                     $error_message = "Error: A category with this name already exists.";
                } else {
                     $error_message = "Database error: " . $e->getMessage();
                }
            }
        }
    }
}


// --- Fetch All Categories with Product Counts ---
try {
    $sql = "SELECT c.id, c.name, c.image_url, COUNT(p.id) as product_count 
            FROM categories c
            LEFT JOIN products p ON c.id = p.category_id
            GROUP BY c.id, c.name, c.image_url
            ORDER BY c.name ASC";
    $stmt = $pdo->query($sql);
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Could not fetch categories: " . $e->getMessage());
}

?>

<main class="main-content">
    <div class="content-header">
        <h1>Categories</h1>
        <ol class="breadcrumb">
            <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
            <li class="active">Categories</li>
        </ol>
    </div>
    
    <section class="content-body">
        
        <?php if ($success_message): ?>
            <div class="success-message-box"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="error-message-box"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <div class="category-manage-layout">
            <!-- Add New Category Form Column -->
            <div class="category-form-container">
                <div class="content-box">
                    <h3 class="meta-box-title">Add New Category</h3>
                    <form action="manage_categories.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                        
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" id="name" name="name" class="form-control" required>
                            <p class="form-help-text">The name is how it appears on your site.</p>
                        </div>

                        <div class="form-group">
                            <label for="category_image">Image</label>
                            <input type="file" id="category_image" name="category_image" class="form-control">
                            <p class="form-help-text">Optional. Upload an image for the category.</p>
                        </div>

                        <button type="submit" name="add_category" class="btn-publish">Add New Category</button>
                    </form>
                </div>
            </div>

            <!-- Existing Categories Table Column -->
            <div class="category-table-container">
                <div class="content-box">
                     <h3 class="meta-box-title">All Categories</h3>
                    <div class="table-responsive">
                        <table class="product-table">
                            <thead>
                                <tr>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Product Count</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($categories)): ?>
                                    <tr>
                                        <td colspan="4" style="text-align: center;">No categories found.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($categories as $category): ?>
                                        <tr>
                                            <td>
                                                <img src="../uploads/categories/<?php echo htmlspecialchars($category['image_url'] ?? 'placeholder.png'); ?>" 
                                                     onerror="this.onerror=null;this.src='../uploads/placeholder.png';"
                                                     class="category-thumbnail" 
                                                     alt="<?php echo htmlspecialchars($category['name']); ?>">
                                            </td>
                                            <td><strong><?php echo htmlspecialchars($category['name']); ?></strong></td>
                                            <td><?php echo $category['product_count']; ?></td>
                                            <td class="action-links">
                                                <a href="edit_category.php?id=<?php echo $category['id']; ?>"><i class="fas fa-edit"></i> Edit</a>
                                                <a href="delete_category.php?id=<?php echo $category['id']; ?>" 
                                                   class="action-delete"
                                                   onclick="return confirm('Are you sure you want to delete this category? This will not delete the products within it.');">
                                                   <i class="fas fa-trash-alt"></i> Delete
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

