<?php
session_start();
require_once 'admin_header.php';
require_once '../includes/db_connect.php';

// Security check
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$order_id = $_GET['id'] ?? null;
if (!$order_id || !is_numeric($order_id)) {
    header("Location: manage_orders.php");
    exit();
}

// Handle messages from the update script
$success_message = $_SESSION['success_message'] ?? null;
unset($_SESSION['success_message']);
$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['error_message']);


// Fetch order details from the orders table
try {
    $sql = "SELECT * FROM orders WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$order_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        header("Location: manage_orders.php");
        exit();
    }

    // Fetch order items (this part remains the same)
    $sql_items = "SELECT oi.*, p.name as product_name, p.image_url
                  FROM order_items oi
                  JOIN products p ON oi.product_id = p.id
                  WHERE oi.order_id = ?";
    $stmt_items = $pdo->prepare($sql_items);
    $stmt_items->execute([$order_id]);
    $order_items = $stmt_items->fetchAll(PDO::FETCH_ASSOC);

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
         <?php if ($success_message): ?>
            <div class="success-message-box"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="error-message-box"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <div class="order-details-layout">
            <div class="order-main-column">
                <div class="content-box">
                    <h3 class="meta-box-title">Items Ordered</h3>
                    <table class="product-table order-items-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th></th>
                                <th style="text-align: right;">Price</th>
                                <th style="text-align: center;">Quantity</th>
                                <th style="text-align: right;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($order_items as $item): ?>
                            <tr>
                                <td>
                                    <?php if (!empty($item['image_url'])): ?>
                                        <img src="../uploads/product-image/<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" class="product-thumbnail">
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                <td style="text-align: right;">$<?php echo number_format($item['price'], 2); ?></td>
                                <td style="text-align: center;"><?php echo htmlspecialchars($item['quantity']); ?></td>
                                <td style="text-align: right;">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" style="text-align: right; font-weight: bold;">Total:</td>
                                <td style="text-align: right; font-weight: bold;">$<?php echo number_format($order['total_amount'], 2); ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="order-side-column">
                <div class="content-box">
                    <h3 class="meta-box-title">Customer Details</h3>
                    <p>
                        <strong>Name:</strong> <?php echo htmlspecialchars($order['customer_name']); ?><br>
                        <strong>Email:</strong> <?php echo htmlspecialchars($order['customer_email']); ?><br>
                        <strong>Address:</strong> <?php echo nl2br(htmlspecialchars($order['customer_address'])); ?>
                    </p>
                </div>
                 <div class="content-box order-management-box">
                    <h3 class="meta-box-title">Order Management</h3>
                    <form action="update_order.php" method="POST">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <div class="form-group">
                            <label for="status">Order Status</label>
                            <select id="status" name="status" class="form-control">
                                <option value="Pending" <?php if($order['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                                <option value="Processing" <?php if($order['status'] == 'Processing') echo 'selected'; ?>>Processing</option>
                                <option value="Shipped" <?php if($order['status'] == 'Shipped') echo 'selected'; ?>>Shipped</option>
                                <option value="Delivered" <?php if($order['status'] == 'Delivered') echo 'selected'; ?>>Delivered</option>
                                <option value="Cancelled" <?php if($order['status'] == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="tracking_number">Tracking Number</label>
                            <input type="text" id="tracking_number" name="tracking_number" class="form-control" value="<?php echo htmlspecialchars($order['tracking_number'] ?? ''); ?>">
                        </div>
                        <button type="submit" name="update_order" class="btn-publish">Update Order</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require_once 'admin_footer.php'; ?>

