<?php
session_start();
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// Get cart items from session
$cart_items = $_SESSION['cart'] ?? [];
$cart_total = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Shopping Cart - Divine Syncserv</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/cart_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>

    <?php require_once 'includes/header.php'; ?>

    <main class="cart-page-content">
        <div class="container">
            <h1 class="cart-title">Your Shopping Cart</h1>

            <?php if (empty($cart_items)): ?>
                <div class="cart-empty-message">
                    <p>Your cart is currently empty.</p>
                    <a href="index.php" class="btn-primary">Continue Shopping</a>
                </div>
            <?php else: ?>
                <div class="cart-layout">
                    <div class="cart-items-list">
                        <div class="cart-header">
                            <div class="header-product">Product</div>
                            <div class="header-price">Price</div>
                            <div class="header-quantity">Quantity</div>
                            <div class="header-subtotal">Subtotal</div>
                        </div>
                        <?php foreach ($cart_items as $product_id => $item): ?>
                            <?php 
                                $subtotal = $item['price'] * $item['quantity'];
                                $cart_total += $subtotal;
                            ?>
                            <div class="cart-item">
                                <div class="cart-item-product">
                                    <img src="uploads/product-image/<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                    <div>
                                        <a href="product_details.php?id=<?php echo $product_id; ?>" class="item-name"><?php echo htmlspecialchars($item['name']); ?></a>
                                        <a href="remove_from_cart.php?id=<?php echo $product_id; ?>" class="remove-link">Remove</a>
                                    </div>
                                </div>
                                <!-- DATA-LABELS ADDED HERE FOR RESPONSIVE STYLES -->
                                <div class="cart-item-price" data-label="Price">$<?php echo number_format($item['price'], 2); ?></div>
                                <div class="cart-item-quantity" data-label="Quantity">
                                    <form action="update_cart.php" method="POST" class="cart-quantity-form">
                                        <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                        <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" class="quantity-input-cart">
                                        <button type="submit" class="update-btn">Update</button>
                                    </form>
                                </div>
                                <div class="cart-item-subtotal" data-label="Subtotal">$<?php echo number_format($subtotal, 2); ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="cart-summary">
                        <h2 class="summary-title">Cart Totals</h2>
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span>$<?php echo number_format($cart_total, 2); ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Shipping</span>
                            <span>Calculated at checkout</span>
                        </div>
                        <div class="summary-total">
                            <span>Total</span>
                            <span>$<?php echo number_format($cart_total, 2); ?></span>
                        </div>
                        <a href="checkout.php" class="btn-primary checkout-btn">Proceed to Checkout</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php require_once 'includes/footer.php'; ?>
    <script src="assets/js/script.js"></script>
</body>
</html>

