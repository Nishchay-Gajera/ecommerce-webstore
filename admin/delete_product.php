<?php
// CRITICAL: session_start() must be the very first thing to run.
session_start();

// Include the database connection.
require_once '../includes/db_connect.php';

// --- Security Check ---
// Redirect to login if the admin is not logged in.
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

// --- Input Validation ---
// Get the product ID from the URL.
$product_id = $_GET['id'] ?? null;

// If the ID is missing or not a number, redirect with an error.
if (!$product_id || !is_numeric($product_id)) {
    $_SESSION['error_message'] = "Invalid request. Product ID was not provided.";
    header('Location: manage_products.php');
    exit;
}

try {
    // --- Step 1: Get the image filename BEFORE deleting the record ---
    // We need this to delete the physical image file from the server.
    $stmt = $pdo->prepare("SELECT image_url FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $image_filename = $stmt->fetchColumn();

    // --- Step 2: Delete the product record from the database ---
    $delete_stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $delete_stmt->execute([$product_id]);

    // --- Step 3: Delete the physical image file ---
    // Check if a filename exists and the file is actually on the server.
    if ($image_filename) {
        // Correct path from /admin/ to /uploads/
        $file_path = '../uploads/product-image/' . $image_filename;
        if (file_exists($file_path)) {
            unlink($file_path); // This deletes the file
        }
    }

    // --- Success ---
    // Set a success message and redirect back to the product list.
    $_SESSION['success_message'] = "Product has been deleted successfully.";

} catch (PDOException $e) {
    // --- Error Handling ---
    // If something goes wrong with the database, catch the error.
    // This can happen if the product is linked in an order (foreign key constraint).
    $_SESSION['error_message'] = "Error: Could not delete product. It may be part of an existing order.";
}

// Redirect back to the products page.
header('Location: manage_products.php');
exit;
?>

