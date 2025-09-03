<?php
// This file assumes `functions.php` which contains `getAllCategories()` has been included before it.
// Typically in index.php or any other page template.
if (function_exists('getAllCategories')) {
    $nav_categories = getAllCategories(); 
} else {
    $nav_categories = []; // Fallback to an empty array if the function doesn't exist
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=Montserrat:wght@300;400;500;600&display=swap" rel="stylesheet">
    <!-- Main Stylesheet -->
    <link rel="stylesheet" href="assets/css/style.css">
    <title>Aurelie - Wedding & Festive Clothing</title>
</head>
<body>

<header class="main-header">
    <div class="container header-content">
        <!-- UPDATED LOGO SECTION -->
        <a href="index.php" class="logo">
            <img src="images/logo.png" alt="Aurelie Logo">
        </a>

        <div class="search-container">
            <input type="text" class="search-input" placeholder="Search For Products...">
            <select class="search-category">
                <option>All Products</option>
                <?php foreach($nav_categories as $category): ?>
                    <option value="<?php echo htmlspecialchars($category['id']); ?>">
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
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
            <a href="#" class="icon-link mobile-search-icon"><i class="fas fa-search"></i></a>
            <a href="#" class="icon-link"><i class="fas fa-shopping-bag"></i></a>
            <button class="hamburger-menu">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </div>
</header>

<nav class="desktop-nav">
    <div class="container">
        <ul>
            <li><a href="index.php">Home</a></li>
            <?php foreach($nav_categories as $category): ?>
                <li><a href="category.php?id=<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></a></li>
            <?php endforeach; ?>
            <li><a href="contact.php">Contact</a></li>
        </ul>
    </div>
</nav>

<!-- Mobile Navigation Menu -->
<div class="nav-overlay"></div>
<nav class="main-nav">
    <ul>
        <li><a href="index.php">Home</a></li>
        <?php foreach($nav_categories as $category): ?>
            <li><a href="category.php?id=<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></a></li>
        <?php endforeach; ?>
        <li><a href="contact.php">Contact</a></li>
    </ul>
</nav>

