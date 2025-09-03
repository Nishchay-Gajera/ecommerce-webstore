<?php
// Always start the session at the beginning of any script that needs session data.
session_start();

// Include necessary files for database connection and functions.
require_once 'includes/db_connect.php';
require_once 'includes/functions.php';

// We only process POST requests to this script.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Sanitize and validate the product ID and quantity from the form.
    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

    // Proceed only if we have a valid product ID and a quantity greater than 0.
    if ($product_id && $quantity > 0) {
        
        // Initialize the cart in the session if it doesn't already exist.
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Fetch the product's details to ensure it's a valid product.
        $product = getProductById($product_id);

        if ($product) {
            // If the product is already in the cart, just update its quantity.
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]['quantity'] += $quantity;
            } else {
                // If it's a new product, add it to the cart with its details.
                $_SESSION['cart'][$product_id] = [
                    'name'      => $product['name'],
                    'price'     => $product['price'],
                    'image_url' => $product['image_url'],
                    'quantity'  => $quantity
                ];
            }
            // Set a success message to be shown to the user.
            $_SESSION['success_message'] = "<strong>" . htmlspecialchars($product['name']) . "</strong> has been added to your cart.";
        } else {
            $_SESSION['error_message'] = "Sorry, the selected product could not be found.";
        }
    } else {
        $_SESSION['error_message'] = "Invalid data provided. Please try again.";
    }
}

// Redirect the user back to the page they came from (the product page).
header("Location: " . $_SERVER['HTTP_REFERER']);
exit();
?>

