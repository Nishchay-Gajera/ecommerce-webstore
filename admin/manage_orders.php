<?php
require_once 'admin_header.php';
require_once '../includes/db_connect.php';

// Fetch all orders
try {
    $stmt = $pdo->query("SELECT * FROM orders ORDER BY order_date DESC");
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching orders: " . $e->getMessage());
}
?>

<main class="main-content">
    <div class="content-header">
        <h1>Orders</h1>
        <ol class="breadcrumb">
            <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
            <li class="active">Orders</li>
        </ol>
    </div>

    <section class="content-body">
        <div class="content-box">
            <div class="table-responsive">
                <table class="product-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer Name</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Tracking #</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($orders)): ?>
                            <tr><td colspan="7" style="text-align:center;">No orders found.</td></tr>
                        <?php else: ?>
                            <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>#<?php echo htmlspecialchars($order['id']); ?></td>
                                <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                <td>$<?php echo htmlspecialchars($order['total_amount']); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo strtolower(htmlspecialchars($order['order_status'])); ?>">
                                        <?php echo htmlspecialchars($order['order_status']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($order['tracking_number'] ?? 'N/A'); ?></td>
                                <td><?php echo date("M d, Y", strtotime($order['order_date'])); ?></td>
                                <td>
                                    <div class="action-links">
                                        <a href="view_order.php?id=<?php echo $order['id']; ?>" class="action-edit"><i class="fas fa-eye"></i> View Details</a>
                                    </div>
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
</div> <!-- /.wrapper -->

<?php
require_once 'admin_footer.php';
?>

