<?php
require_once 'includes/db_connect.php';
require_once 'includes/header.php';

$order_details = null;
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = filter_input(INPUT_POST, 'order_id', FILTER_SANITIZE_NUMBER_INT);
    $customer_email = filter_input(INPUT_POST, 'customer_email', FILTER_VALIDATE_EMAIL);

    if ($order_id && $customer_email) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND customer_email = ?");
            $stmt->execute([$order_id, $customer_email]);
            $order_details = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$order_details) {
                $error_message = "No order found with the provided details. Please check your Order ID and email address and try again.";
            }
        } catch (PDOException $e) {
            $error_message = "We are currently experiencing technical difficulties. Please try again later.";
            // error_log($e->getMessage()); // Log the actual error for debugging
        }
    } else {
        $error_message = "Please provide a valid Order ID and email address.";
    }
}
?>

<main class="static-page-content">
    <div class="container">
        <div class="static-page-header">
            <h1>Order Status</h1>
        </div>

        <div class="order-status-container">
            <?php if ($order_details): ?>
                <div class="status-card success">
                    <h3>Order #<?php echo htmlspecialchars($order_details['id']); ?> Found</h3>
                    <div class="status-details">
                        <p><strong>Status:</strong> <span class="status-badge status-<?php echo strtolower(htmlspecialchars($order_details['status'])); ?>"><?php echo htmlspecialchars($order_details['status']); ?></span></p>
                        <p><strong>Order Date:</strong> <?php echo date('F j, Y', strtotime($order_details['order_date'])); ?></p>
                        <p><strong>Customer:</strong> <?php echo htmlspecialchars($order_details['customer_name']); ?></p>
                        <?php if (!empty($order_details['tracking_number'])): ?>
                            <p><strong>Tracking Number:</strong> <?php echo htmlspecialchars($order_details['tracking_number']); ?></p>
                        <?php else: ?>
                            <p><strong>Tracking Number:</strong> A tracking number will be assigned once the order is shipped.</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php elseif ($error_message): ?>
                 <div class="status-card error">
                    <h3>We couldn't find your order</h3>
                    <p><?php echo $error_message; ?></p>
                    <a href="track_order.php" class="btn-primary">Try Again</a>
                </div>
            <?php else: ?>
                <p>Please use the <a href="track_order.php">Track Order</a> page to find your order status.</p>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php require_once 'includes/footer.php'; ?>

</body>
</html>
