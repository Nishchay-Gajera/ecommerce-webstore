<?php
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// If the cart is empty, there's no reason to be here. Redirect to the cart page.
if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit;
}

$cart_items = $_SESSION['cart'];
$cart_total = 0;
foreach ($cart_items as $item) {
    $cart_total += $item['price'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Divine Syncserv</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/checkout_style.css"> <!-- New Stylesheet -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>

    <?php require_once 'includes/header.php'; ?>

    <main class="checkout-page-content">
        <div class="container">
            <h1 class="checkout-title">Checkout</h1>
            
            <form action="place_order.php" method="POST" class="checkout-layout">
                <div class="customer-details">
                    <h2 class="section-heading">Billing Details</h2>
                    <div class="form-group">
                        <label for="customer_name">Full Name *</label>
                        <input type="text" id="customer_name" name="customer_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="customer_email">Email Address *</label>
                        <input type="email" id="customer_email" name="customer_email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="customer_address">Street Address *</label>
                        <textarea id="customer_address" name="customer_address" class="form-control" rows="4" required></textarea>
                    </div>
                </div>

                <div class="order-summary">
                    <h2 class="section-heading">Your Order</h2>
                    <div class="summary-box">
                        <div class="summary-header">
                            <span>Product</span>
                            <span>Subtotal</span>
                        </div>
                        <?php foreach($cart_items as $item): ?>
                        <div class="summary-item">
                            <span><?php echo htmlspecialchars($item['name']); ?> &times; <?php echo $item['quantity']; ?></span>
                            <span>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                        </div>
                        <?php endforeach; ?>
                        <div class="summary-row subtotal">
                            <span>Subtotal</span>
                            <span>$<?php echo number_format($cart_total, 2); ?></span>
                        </div>
                        <div class="summary-row total">
                            <span>Total</span>
                            <strong>$<?php echo number_format($cart_total, 2); ?></strong>
                        </div>
                        <button type="submit" class="btn-primary place-order-btn">Place Order</button>
                    </div>
                </div>
            </form>

        </div>
    </main>

    <?php require_once 'includes/footer.php'; ?>
    <script src="assets/js/script.js"></script>
</body>
</html>
