<?php
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// Security: Only process POST requests and ensure the cart is not empty.
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_SESSION['cart'])) {
    header('Location: index.php');
    exit;
}

// Sanitize and validate customer details from the form
$customer_name = filter_input(INPUT_POST, 'customer_name', FILTER_SANITIZE_STRING);
$customer_email = filter_input(INPUT_POST, 'customer_email', FILTER_VALIDATE_EMAIL);
$customer_address = filter_input(INPUT_POST, 'customer_address', FILTER_SANITIZE_STRING);

if (!$customer_name || !$customer_email || !$customer_address) {
    // If validation fails, redirect back with an error message
    $_SESSION['error_message'] = "Please fill in all required fields.";
    header('Location: checkout.php');
    exit;
}

$cart_items = $_SESSION['cart'];
$total_amount = 0;
foreach ($cart_items as $item) {
    $total_amount += $item['price'] * $item['quantity'];
}

try {
    // Use a transaction to ensure all queries succeed or none do.
    $pdo->beginTransaction();

    // Step 1: Insert the main order into the `orders` table.
    $sql_order = "INSERT INTO orders (customer_name, customer_email, customer_address, total_amount, status, order_date) VALUES (?, ?, ?, ?, 'Pending', NOW())";
    $stmt_order = $pdo->prepare($sql_order);
    $stmt_order->execute([$customer_name, $customer_email, $customer_address, $total_amount]);
    $order_id = $pdo->lastInsertId();

    // Step 2: Insert each item from the cart into the `order_items` table.
    $sql_item = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
    $stmt_item = $pdo->prepare($sql_item);

    foreach ($cart_items as $product_id => $item) {
        $stmt_item->execute([$order_id, $product_id, $item['quantity'], $item['price']]);
    }

    // If everything was successful, commit the transaction.
    $pdo->commit();

    // Clear the cart and store the order ID for the success page.
    unset($_SESSION['cart']);
    $_SESSION['last_order_id'] = $order_id;

    // Redirect to the order success page.
    header('Location: order_success.php');
    exit();

} catch (PDOException $e) {
    // If any part of the transaction failed, roll back all changes.
    $pdo->rollBack();
    $_SESSION['error_message'] = "There was an error placing your order. Please try again. " . $e->getMessage();
    header('Location: checkout.php');
    exit;
}
?>
