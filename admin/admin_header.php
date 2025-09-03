<?php
// This check ensures that a session has been started on the page including this file.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// SECURITY CHECK: Redirect to login page if the admin is not logged in.
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Determine the active page to highlight it in the sidebar.
$active_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <!-- CORRECTED: This now points to the correct admin stylesheet -->
    <link rel="stylesheet" href="../assets/css/admin_style.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</head>
<body>
    <div class="wrapper">
        <header class="top-header">
            <div class="header-left">
                <a href="index.php" class="logo">Admin Panel</a>
            </div>
            <div class="header-right">
                <div class="user-dropdown">
                    <button class="user-name">
                        <i class="fas fa-user-circle"></i>
                        <span><?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?></span>
                        <i class="fas fa-caret-down"></i>
                    </button>
                    <div class="dropdown-content">
                        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            </div>
        </header>

        <aside class="sidebar">
            <nav class="sidebar-nav">
                <ul class="nav">
                    <li class="nav-title">MAIN NAVIGATION</li>
                    <li class="<?php echo ($active_page == 'index.php') ? 'active' : ''; ?>">
                        <a href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    </li>
                    <li class="<?php echo ($active_page == 'manage_products.php' || $active_page == 'add_product.php' || $active_page == 'edit_product.php') ? 'active' : ''; ?>">
                        <a href="manage_products.php"><i class="fas fa-box-open"></i> Products</a>
                    </li>
                    <li class="<?php echo ($active_page == 'manage_categories.php' || $active_page == 'edit_category.php') ? 'active' : ''; ?>">
                        <a href="manage_categories.php"><i class="fas fa-tags"></i> Categories</a>
                    </li>
                    <li class="<?php echo ($active_page == 'manage_orders.php' || $active_page == 'view_order.php') ? 'active' : ''; ?>">
                        <a href="manage_orders.php"><i class="fas fa-shopping-cart"></i> Orders</a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- This wrapper will be closed in admin_footer.php -->
        <div class="content-wrapper-flex">
            <main class="main-content">

