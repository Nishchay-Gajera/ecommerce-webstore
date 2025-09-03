<?php
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// Security check to ensure this page is accessed after placing an order.
if (!isset($_SESSION['last_order_id'])) {
    header('Location: index.php');
    exit;
}

$order_id = $_SESSION['last_order_id'];
// Unset the session variable so the user can't just refresh this page.
unset($_SESSION['last_order_id']);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Successful - Aurelie</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>

    <?php require_once 'includes/header.php'; ?>

    <main class="order-success-page-content">
        <div class="container text-center">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h1 class="success-title">Thank You For Your Order!</h1>
            <p class="success-message">Your order has been placed successfully. Your order number is <strong>#<?php echo htmlspecialchars($order_id); ?></strong>.</p>
            <p>You will receive an email confirmation shortly.</p>
            <a href="index.php" class="btn-primary">Continue Shopping</a>
        </div>
    </main>

    <?php require_once 'includes/footer.php'; ?>
    <script src="assets/js/script.js"></script>
</body>
</html>
