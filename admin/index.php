<?php
// Start the session right at the beginning.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Now, include the header which contains the security check.
require_once 'admin_header.php';
require_once '../includes/db_connect.php';

// Fetch stats for the dashboard. This will now work since the 'status' column has been added.
$total_products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$total_orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$total_categories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$featured_products = $pdo->query("SELECT COUNT(*) FROM products WHERE is_featured = 1")->fetchColumn();
$pending_orders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'Pending'")->fetchColumn();

?>

<main class="main-content">
    <div class="content-header">
        <h1>Dashboard</h1>
        <ol class="breadcrumb">
            <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
            <li class="active">Dashboard</li>
        </ol>
    </div>
    
    <section class="content-body">
        <div class="row">
            <!-- Stat Boxes -->
            <div class="col-lg-3 col-sm-6">
                <div class="stat-box bg-aqua">
                    <div class="inner">
                        <h3><?php echo $total_products; ?></h3>
                        <p>Total Products</p>
                    </div>
                    <div class="icon"><i class="fas fa-box-open"></i></div>
                    <a href="manage_products.php" class="stat-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="stat-box bg-green">
                    <div class="inner">
                        <h3><?php echo $total_orders; ?></h3>
                        <p>Total Orders</p>
                    </div>
                    <div class="icon"><i class="fas fa-shopping-cart"></i></div>
                    <a href="manage_orders.php" class="stat-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="stat-box bg-orange">
                    <div class="inner">
                        <h3><?php echo $pending_orders; ?></h3>
                        <p>Pending Orders</p>
                    </div>
                    <div class="icon"><i class="fas fa-clock"></i></div>
                    <a href="manage_orders.php" class="stat-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
            <div class="col-lg-3 col-sm-6">
                <div class="stat-box bg-red">
                    <div class="inner">
                        <h3><?php echo $total_categories; ?></h3>
                        <p>Product Categories</p>
                    </div>
                    <div class="icon"><i class="fas fa-tags"></i></div>
                    <a href="manage_categories.php" class="stat-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
             <div class="col-lg-3 col-sm-6">
                <div class="stat-box bg-purple">
                    <div class="inner">
                        <h3><?php echo $featured_products; ?></h3>
                        <p>Featured Products</p>
                    </div>
                    <div class="icon"><i class="fas fa-star"></i></div>
                    <a href="manage_products.php" class="stat-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                </div>
            </div>
        </div>
    </section>
</main>

