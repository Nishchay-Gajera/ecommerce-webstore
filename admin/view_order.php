<?php
// Start session and include necessary files
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'admin_header.php';
require_once '../includes/db_connect.php';

// Validate Order ID
$order_id = $_GET['id'] ?? null;
if (!$order_id || !is_numeric($order_id)) {
    header("Location: manage_orders.php");
    exit();
}

// Check for success/error messages from other pages
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

try {
    // Fetch order details
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        $_SESSION['error_message'] = "Order not found.";
        header("Location: manage_orders.php");
        exit();
    }

    // Fetch order items along with product details
    $items_stmt = $pdo->prepare("
        SELECT oi.*, p.name as product_name, p.image_url as product_image
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id = ?
    ");
    $items_stmt->execute([$order_id]);
    $order_items = $items_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<main class="main-content">
    <div class="content-header">
        <h1>Order Details #<?php echo htmlspecialchars($order['id']); ?></h1>
        <ol class="breadcrumb">
            <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="manage_orders.php">Orders</a></li>
            <li class="active">Order Details</li>
        </ol>
    </div>

    <section class="content-body">

        <?php if (!empty($success_message)): ?>
            <div class="success-message-box"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if (!empty($error_message)): ?>
            <div class="error-message-box"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <div class="order-details-layout">
            <!-- Main Column -->
            <div class="order-main-column">
                <!-- Items Ordered Box -->
                <div class="content-box">
                    <h3 class="meta-box-title">Items Ordered</h3>
                    <div class="table-responsive">
                        <table class="product-table">
                            <thead>
                                <tr>
                                    <th colspan="2">Product</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($order_items as $item): ?>
                                    <tr>
                                        <td>
                                            <img src="../uploads/products/<?php echo htmlspecialchars($item['product_image'] ?? 'placeholder.png'); ?>" 
                                                 onerror="this.onerror=null;this.src='../uploads/placeholder.png';"
                                                 class="product-thumbnail">
                                        </td>
                                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                        <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" style="text-align: right; font-weight: bold;">Total:</td>
                                    <td style="font-weight: bold;">$<?php echo number_format($order['total_amount'], 2); ?></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Customer Details Box -->
                 <div class="content-box">
                    <h3 class="meta-box-title">Customer Details</h3>
                    <p><strong>Name:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($order['customer_email']); ?></p>
                    <p><strong>Address:</strong><br><?php echo nl2br(htmlspecialchars($order['customer_address'])); ?></p>
                </div>
            </div>

            <!-- Side Column -->
            <div class="order-side-column">
                <!-- Order Management Box -->
                <div class="content-box">
                     <h3 class="meta-box-title">Order Management</h3>
                     <form action="update_order.php" method="POST">
                         <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                         <div class="form-group">
                             <label for="status">Order Status</label>
                             <select name="status" id="status" class="form-control">
                                 <option value="Pending" <?php echo ($order['status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                                 <option value="Processing" <?php echo ($order['status'] == 'Processing') ? 'selected' : ''; ?>>Processing</option>
                                 <option value="Shipped" <?php echo ($order['status'] == 'Shipped') ? 'selected' : ''; ?>>Shipped</option>
                                 <option value="Delivered" <?php echo ($order['status'] == 'Delivered') ? 'selected' : ''; ?>>Delivered</option>
                                 <option value="Cancelled" <?php echo ($order['status'] == 'Cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                             </select>
                         </div>
                         <div class="form-group">
                            <label for="tracking_number">Tracking Number</label>
                            <input type="text" name="tracking_number" id="tracking_number" class="form-control" value="<?php echo htmlspecialchars($order['tracking_number'] ?? ''); ?>">
                         </div>
                         <button type="submit" class="btn-publish">Update Order</button>
                     </form>
                </div>
            </div>

        </div>
    </section>
</main>

