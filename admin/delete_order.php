<?php
// CRITICAL: Start the session to access login credentials.
session_start();

// Include the database connection.
require_once '../includes/db_connect.php';

// SECURITY CHECK: Ensure the user is a logged-in admin.
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Get the Order ID from the URL and validate it.
$order_id = $_GET['id'] ?? null;
if (!$order_id || !is_numeric($order_id)) {
    $_SESSION['error_message'] = "Invalid Order ID specified for deletion.";
    header('Location: manage_orders.php');
    exit();
}

try {
    // Use a transaction to ensure data integrity.
    // This means both deletions must succeed, or neither will.
    $pdo->beginTransaction();

    // Step 1: Delete the items associated with the order from the 'order_items' table.
    $sql_items = "DELETE FROM order_items WHERE order_id = ?";
    $stmt_items = $pdo->prepare($sql_items);
    $stmt_items->execute([$order_id]);

    // Step 2: Delete the main order from the 'orders' table.
    $sql_order = "DELETE FROM orders WHERE id = ?";
    $stmt_order = $pdo->prepare($sql_order);
    $stmt_order->execute([$order_id]);

    // If both queries were successful, commit the changes to the database.
    $pdo->commit();

    // Set a success message to be displayed on the orders page.
    $_SESSION['success_message'] = "Order #{$order_id} has been permanently deleted.";

} catch (PDOException $e) {
    // If any part of the transaction fails, roll back all changes.
    $pdo->rollBack();
    // Set an error message.
    $_SESSION['error_message'] = "Error: Could not delete the order. " . $e->getMessage();
}

// Redirect back to the main orders list.
header('Location: manage_orders.php');
exit();
?>
