<?php
session_start();

$product_id = $_GET['id'] ?? null;

if ($product_id && isset($_SESSION['cart'][$product_id])) {
    unset($_SESSION['cart'][$product_id]);
}

header('Location: cart.php');
exit();
?>
