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

// The header file now correctly loads all necessary stylesheets
require_once 'includes/header.php'; 
?>

<main class="order-success-page-content">
    <div class="success-container">
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