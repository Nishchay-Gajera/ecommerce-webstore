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
    $allowed_statuses = ['Processing', 'Shipped', 'Delivered', 'Cancelled'];

    if (in_array($new_status, $allowed_statuses)) {
        try {
            $stmt = $pdo->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
            $stmt->execute([$new_status, $order_id]);
            $_SESSION['success_message'] = "Order status updated successfully.";
        } catch (PDOException $e) {
            $_SESSION['error_message'] = "Failed to update order status. Error: " . $e->getMessage();
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
