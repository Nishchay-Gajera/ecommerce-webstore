<?php
// CRITICAL: Start the session to access login credentials.
session_start();

// Include the database connection.
require_once '../includes/db_connect.php';

// SECURITY CHECK: Ensure the user is a logged-in admin.
// This uses the session key we have established throughout the project.
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Check if the form was submitted correctly via POST.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_order'])) {
    
    // Sanitize and retrieve the input data from the form.
    $order_id = $_POST['order_id'] ?? null;
    $new_status = $_POST['status'] ?? ''; // Corrected from 'order_status' to 'status'
    $tracking_number = trim($_POST['tracking_number'] ?? '');

    // Basic validation.
    if (!$order_id || !is_numeric($order_id)) {
        $_SESSION['error_message'] = "Invalid Order ID.";
        header('Location: manage_orders.php');
        exit();
    }

    try {
        // Use a prepared statement to securely update the database.
        // CORRECTED: Updates the 'status' column, not 'order_status'.
        $sql = "UPDATE orders SET status = ?, tracking_number = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        
        // Execute the query with the sanitized data.
        $stmt->execute([$new_status, $tracking_number, $order_id]);

        // Set a success message.
        $_SESSION['success_message'] = "Order #{$order_id} has been updated successfully!";

    } catch (PDOException $e) {
        // If there's an error, store it in the session to be displayed.
        $_SESSION['error_message'] = "Database Error: Could not update the order.";
        // For debugging, you could log the full error: error_log($e->getMessage());
    }

    // Redirect the user back to the order details page they were just on.
    header("Location: view_order.php?id=" . $order_id);
    exit();

} else {
    // If the script is accessed directly, redirect to the main orders page.
    header('Location: manage_orders.php');
    exit();
}
?>

