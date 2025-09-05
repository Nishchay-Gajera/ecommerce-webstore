<?php
// Start the session if it's not already started to access session variables.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Calculate the total number of items in the cart by summing quantities.
$cart_item_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $cart_item_count = array_sum(array_column($_SESSION['cart'], 'quantity'));
}

// Ensure the getAllCategories function is available.
if (!function_exists('getAllCategories')) {
    require_once 'functions.php';
}
$all_categories = getAllCategories();
$active_page = basename($_SERVER['PHP_SELF']); // Get the current page filename
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Divine Syncserv - Wedding & Festive Clothing</title>
    
    <!-- Main Stylesheet for the entire site -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <!-- CONDITIONAL: Load page-specific stylesheets -->
    <?php if ($active_page == 'product_details.php'): ?>
        <link rel="stylesheet" href="assets/css/product_details.css">
    <?php elseif ($active_page == 'cart.php'): ?>
        <link rel="stylesheet" href="assets/css/cart_style.css">
    <?php endif; ?>

    <!-- External Libraries & Fonts -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
</head>
<body>

<header class="main-header">
    <div class="container header-content">
        <a href="index.php" class="logo">
            <img src="images/logo.png" alt="Divine Syncserv Logo">
        </a>
        
        <div class="search-container">
            <input type="text" class="search-input" placeholder="Search For Products...">
            <select class="search-category">
                <option>All Products</option>
                <?php foreach($all_categories as $category): ?>
                    <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                <?php endforeach; ?>
            </select>
            <button class="search-button"><i class="fas fa-search"></i></button>
        </div>

        <div class="header-icons">
            <a href="#" class="account-link">
                <i class="far fa-user"></i>
                <div class="account-text">
                    <span>Hello, New User</span>
                    <strong>Your Account</strong>
                </div>
            </a>
            <a href="#" class="icon-link wishlist-icon"><i class="far fa-heart"></i></a>
            <a href="cart.php" class="icon-link cart-icon-link">
                <i class="fas fa-shopping-bag"></i>
                <?php if ($cart_item_count > 0): ?>
                    <span class="cart-count-badge"><?php echo $cart_item_count; ?></span>
                <?php endif; ?>
            </a>
            <a href="#" class="icon-link mobile-search-icon"><i class="fas fa-search"></i></a>
            <button class="hamburger-menu"><i class="fas fa-bars"></i></button>
        </div>
    </div>
</header>

<nav class="desktop-nav">
    <div class="container">
        <ul>
            <li><a href="index.php">Home</a></li>
            <?php foreach ($all_categories as $category): ?>
                <li><a href="category.php?id=<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></a></li>
            <?php endforeach; ?>
            <li><a href="#">Contact</a></li>
        </ul>
    </div>
</nav>

<div class="nav-overlay"></div>
<nav class="main-nav">
     <ul>
         <li><a href="index.php">Home</a></li>
         <?php foreach ($all_categories as $category): ?>
             <li><a href="category.php?id=<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></a></li>
         <?php endforeach; ?>
         <li><a href="#">Contact</a></li>
     </ul>
 </nav>

