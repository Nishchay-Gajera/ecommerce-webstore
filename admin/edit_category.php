<?php
// Ensure the database connection and session are started.
require_once '../includes/db_connect.php';
require_once 'admin_header.php';

// Initialize variables
$id = $_GET['id'] ?? null;
$name = '';
$current_image = '';
$error = '';
$success = '';

// Redirect if ID is not provided or invalid
if (!$id || !filter_var($id, FILTER_VALIDATE_INT)) {
    header("Location: manage_categories.php");
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $id = trim($_POST['id']);
    $current_image = trim($_POST['current_image']);

    if (empty($name)) {
        $error = "Category name cannot be empty.";
    } else {
        try {
            $new_image_filename = null;
            // Check if a new image file is uploaded
            if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
                $target_dir = "../uploads/categories/";

                // FIX: Check if the upload directory exists, and create it if it doesn't.
                if (!is_dir($target_dir)) {
                    // The `true` parameter allows for recursive directory creation.
                    if (!mkdir($target_dir, 0777, true)) {
                        $error = "Failed to create the upload directory. Please check server permissions.";
                    }
                }
                
                // Only proceed if the directory exists or was created successfully
                if (empty($error)) {
                    // Basic image validation
                    $image_info = getimagesize($_FILES["image"]["tmp_name"]);
                    if ($image_info === false) {
                        $error = "File is not a valid image.";
                    } else {
                        // Create a unique filename
                        $file_ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                        $new_image_filename = "cat_" . time() . "_" . uniqid() . "." . $file_ext;
                        $target_file = $target_dir . $new_image_filename;

                        // Move the uploaded file
                        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                            // If upload succeeds, delete the old image if it exists
                            if (!empty($current_image)) {
                                $old_image_path = $target_dir . $current_image;
                                if (file_exists($old_image_path)) {
                                    unlink($old_image_path);
                                }
                            }
                        } else {
                            $error = "Sorry, there was an error uploading your file.";
                            $new_image_filename = null; // Reset on failure
                        }
                    }
                }
            }

            // Proceed with database update only if there were no upload errors
            if (empty($error)) {
                if ($new_image_filename) {
                    // If a new image was uploaded, update both name and image_url
                    $stmt = $pdo->prepare("UPDATE categories SET name = :name, image_url = :image_url WHERE id = :id");
                    $stmt->bindParam(':image_url', $new_image_filename);
                } else {
                    // Otherwise, just update the name
                    $stmt = $pdo->prepare("UPDATE categories SET name = :name WHERE id = :id");
                }
                
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                
                if ($stmt->execute()) {
                    $success = "Category updated successfully!";
                    // Update current_image variable for immediate display after success
                    if($new_image_filename) {
                        $current_image = $new_image_filename;
                    }
                } else {
                    $error = "Failed to update category.";
                }
            }

        } catch (PDOException $e) {
            $error = "Database error: " . $e->getMessage();
        }
    }
}

// Fetch the current category data to display in the form
try {
    $stmt = $pdo->prepare("SELECT name, image_url FROM categories WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $category = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($category) {
        $name = $category['name'];
        $current_image = $category['image_url'];
    } else {
        // Redirect if no category is found with that ID
        header("Location: manage_categories.php");
        exit();
    }
} catch (PDOException $e) {
    die("Could not fetch category data: " . $e->getMessage());
}
?>

<div class="main-content">
    <div class="content-header">
        <h1>Edit Category</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="manage_categories.php">Categories</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Category</li>
            </ol>
        </nav>
    </div>

    <div class="content-body">
        <div class="form-container content-box" style="max-width: 600px;">

            <?php if ($error): ?>
                <div class="error-message-box"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="success-message-box"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <form action="edit_category.php?id=<?php echo htmlspecialchars($id); ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
                <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($current_image); ?>">

                <div class="form-group">
                    <label for="name">Category Name</label>
                    <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($name); ?>" required>
                </div>

                <div class="form-group">
                    <label for="image">Category Image</label>
                    <p style="font-size: 12px; color: #777; margin-top: -5px;">Optional. Uploading a new image will replace the current one.</p>
                    <?php if ($current_image): ?>
                        <div class="current-image-preview" style="margin-bottom: 15px;">
                            <p style="font-weight: 600; font-size: 13px; margin-bottom: 5px;">Current Image:</p>
                            <img src="../uploads/categories/<?php echo htmlspecialchars($current_image); ?>" alt="Current Category Image" class="category-thumbnail">
                        </div>
                    <?php endif; ?>
                    <input type="file" id="image" name="image" class="form-control">
                </div>
                
                <div class="form-actions-top" style="flex-direction: row; gap: 10px; justify-content: flex-start;">
                    <button type="submit" class="btn-publish">Update Category</button>
                    <a href="manage_categories.php" class="btn-cancel">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>

