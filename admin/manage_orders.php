<?php
session_start();
require_once 'admin_header.php';
require_once '../includes/db_connect.php';

// Security check
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Handle messages from other scripts
$success_message = $_SESSION['success_message'] ?? null;
unset($_SESSION['success_message']);
$error_message = $_SESSION['error_message'] ?? null;
unset($_SESSION['error_message']);

// Fetch all orders with customer names
try {
    $sql = "SELECT o.*, o.customer_name 
            FROM orders o
            ORDER BY o.order_date DESC";
    $stmt = $pdo->query($sql);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: Could not fetch orders. " . $e->getMessage());
}

?>

<main class="main-content">
    <div class="content-header">
        <h1>Manage Orders</h1>
        <ol class="breadcrumb">
            <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
            <li class="active">Orders</li>
        </ol>
    </div>

    <section class="content-body">
        <?php if ($success_message): ?>
            <div class="success-message-box"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>
        <?php if ($error_message): ?>
            <div class="error-message-box"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <div class="content-box">
            <div class="table-responsive">
                <table class="product-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($orders)): ?>
                            <tr>
                                <td colspan="6" style="text-align: center;">No orders found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td>#<?php echo htmlspecialchars($order['id']); ?></td>
                                    <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                    <td><?php echo date('d M, Y', strtotime($order['order_date'])); ?></td>
                                    <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo strtolower(htmlspecialchars($order['status'])); ?>">
                                            <?php echo htmlspecialchars($order['status']); ?>
                                        </span>
                                    </td>
                                    <td class="action-links">
                                        <a href="view_order.php?id=<?php echo $order['id']; ?>"><i class="fas fa-eye"></i> View</a>
                                        <a href="delete_order.php?id=<?php echo $order['id']; ?>" class="action-delete" onclick="return confirm('Are you sure you want to permanently delete this order and all its items? This cannot be undone.');">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</main>

<?php require_once 'admin_footer.php'; ?>

