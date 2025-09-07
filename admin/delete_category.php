<?php
session_start();
require_once '../includes/db_connect.php';

// Security check: ensure admin is logged in - FIXED session variable name
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Check for category ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error_message'] = "Invalid request.";
    header("Location: manage_categories.php");
    exit();
}

$category_id = (int)$_GET['id']; // Cast to integer for extra safety

try {
    // First, check if the category exists
    $stmt_check = $pdo->prepare("SELECT name, image_url FROM categories WHERE id = ?");
    $stmt_check->execute([$category_id]);
    $category = $stmt_check->fetch(PDO::FETCH_ASSOC);
    
    if (!$category) {
        $_SESSION['error_message'] = "Category not found.";
        header("Location: manage_categories.php");
        exit();
    }
    
    // Check if category has products
    $stmt_count = $pdo->prepare("SELECT COUNT(*) as product_count FROM products WHERE category_id = ?");
    $stmt_count->execute([$category_id]);
    $product_count = $stmt_count->fetchColumn();
    
    $pdo->beginTransaction();

    // Step 1: If there are products, uncategorize them (set category_id to NULL)
    if ($product_count > 0) {
        $stmt_update = $pdo->prepare("UPDATE products SET category_id = NULL WHERE category_id = ?");
        $stmt_update->execute([$category_id]);
    }

    // Step 2: Delete the category image file if it exists
    if (!empty($category['image_url'])) {
        $image_path = '../uploads/categories/' . $category['image_url'];
        if (file_exists($image_path)) {
            unlink($image_path); // Delete the physical file
        }
    }

    // Step 3: Delete the category itself
    $stmt_delete = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $result = $stmt_delete->execute([$category_id]);
    
    if ($stmt_delete->rowCount() > 0) {
        $pdo->commit();
        
        if ($product_count > 0) {
            $_SESSION['success_message'] = "Category '" . htmlspecialchars($category['name']) . "' deleted successfully. {$product_count} products have been uncategorized.";
        } else {
            $_SESSION['success_message'] = "Category '" . htmlspecialchars($category['name']) . "' deleted successfully.";
        }
    } else {
        $pdo->rollBack();
        $_SESSION['error_message'] = "Failed to delete category. Category may not exist.";
    }

} catch (PDOException $e) {
    $pdo->rollBack();
    $_SESSION['error_message'] = "Failed to delete category. Database error: " . $e->getMessage();
    
    // Log the error for debugging (optional)
    error_log("Category deletion error: " . $e->getMessage());
}

header("Location: manage_categories.php");
exit();
?> 'is_featured' => 'no',
        'image_url' => 'palazzo_set_1.jpg'
    ]
];