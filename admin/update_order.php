<?php
session_start();
require_once '../includes/db_connect.php';

// Security check: ensure admin is logged in
if (!isset($_SESSION["admin_loggedin"]) || $_SESSION["admin_loggedin"] !== true) {
    header("location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['order_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['order_status'];
    $tracking_number = isset($_POST['tracking_number']) ? trim($_POST['tracking_number']) : null;

    // Set tracking number to NULL if it's an empty string
    if ($tracking_number === '') {
        $tracking_number = null;
    }

    $allowed_statuses = ['Processing', 'Shipped', 'Delivered', 'Cancelled'];

    if (in_array($new_status, $allowed_statuses)) {
        try {
            // Prepare query to update both status and tracking number
            $sql = "UPDATE orders SET order_status = :status, tracking_number = :tracking WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            
            $stmt->bindParam(':status', $new_status, PDO::PARAM_STR);
            $stmt->bindParam(':tracking', $tracking_number, PDO::PARAM_STR);
            $stmt->bindParam(':id', $order_id, PDO::PARAM_INT);
            
            $stmt->execute();
            
            $_SESSION['success_message'] = "Order updated successfully.";
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "Failed to update order. Error: " . $e->getMessage();
        }
    } else {
        $_SESSION['error_message'] = "Invalid status selected.";
    }
    // Redirect back to the order details page
    header("Location: view_order.php?id=" . $order_id);
    exit();
} else {
    // Redirect if accessed directly
    header("Location: manage_orders.php");
    exit();
}
