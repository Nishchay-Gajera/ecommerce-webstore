<?php require_once 'includes/header.php'; ?>

<main class="static-page-content">
    <div class="container">
        <div class="static-page-header">
            <h1>Track Your Order</h1>
            <p>Enter your order details below to see its status.</p>
        </div>

        <div class="track-order-container">
            <form action="order_status.php" method="POST" class="track-order-form">
                <div class="form-group">
                    <label for="order_id">Order ID</label>
                    <input type="text" id="order_id" name="order_id" class="form-control" placeholder="e.g., 123" required>
                </div>
                <div class="form-group">
                    <label for="customer_email">Email Address</label>
                    <input type="email" id="customer_email" name="customer_email" class="form-control" placeholder="The email you used for the order" required>
                </div>
                <button type="submit" class="btn-primary">Track Order</button>
            </form>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>

</body>
</html>
