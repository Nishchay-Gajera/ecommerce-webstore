<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the admin is logged in, otherwise redirect to login page
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Before redirecting, destroy the session to be safe
    session_destroy();
    header("Location: login.php");
    exit;
}

// Get the current script name to set the active class
$current_page = basename($_SERVER['SCRIPT_NAME']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="../assets/css/admin_style.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body>
    <header class="top-header">
        <div class="header-left">
            <span class="logo">Master Admin</span>
        </div>
        <div class="user-dropdown">
            <a href="#" class="user-name"><i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?> <i class="fas fa-caret-down"></i></a>
            <div class="dropdown-content">
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </header>
    <div class="wrapper">
        <aside class="sidebar">
            <nav class="sidebar-nav">
                <ul class="nav">
                    <li class="nav-title">MAIN NAVIGATION</li>
                    <li class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>"><a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li class="<?php echo ($current_page == 'manage_orders.php' || $current_page == 'view_order.php') ? 'active' : ''; ?>"><a href="manage_orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                    <li class="<?php echo ($current_page == 'manage_products.php' || $current_page == 'add_product.php' || $current_page == 'edit_product.php') ? 'active' : ''; ?>"><a href="manage_products.php"><i class="fas fa-box-open"></i> Products</a></li>
                    <li class="<?php echo ($current_page == 'manage_categories.php' || $current_page == 'edit_category.php') ? 'active' : ''; ?>"><a href="manage_categories.php"><i class="fas fa-tags"></i> Categories</a></li>
                </ul>
            </nav>
        </aside>
        
        <!-- Opening wrapper for flex layout -->
        <div class="content-wrapper-flex">

