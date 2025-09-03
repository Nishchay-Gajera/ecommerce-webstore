<?php
session_start();
require_once '../includes/db_connect.php';

// Security check: ensure admin is logged in
if (!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Check for category ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error_message'] = "Invalid request.";
    header("Location: manage_categories.php");
    exit();
}
$category_id = $_GET['id'];

try {
    $pdo->beginTransaction();

    // Step 1: Uncategorize products associated with this category
    $stmt_update = $pdo->prepare("UPDATE products SET category_id = NULL WHERE category_id = ?");
    $stmt_update->execute([$category_id]);

    // Step 2: Delete the category itself
    $stmt_delete = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt_delete->execute([$category_id]);
    
    $pdo->commit();
    
    $_SESSION['success_message'] = "Category deleted successfully.";

} catch (PDOException $e) {
    $pdo->rollBack();
    $_SESSION['error_message'] = "Failed to delete category. Error: " . $e->getMessage();
}

header("Location: manage_categories.php");
exit();
