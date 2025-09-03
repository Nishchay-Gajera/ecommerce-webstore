<?php
require_once 'db_connect.php';

function getNewArrivalProducts($limit) {
global $pdo;
try {
$stmt = $pdo->prepare("SELECT * FROM products ORDER BY created_at DESC LIMIT :limit");
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();
return $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
error_log("Database Error: " . $e->getMessage());
return [];
}
}

function getFeaturedProducts($limit) {
global $pdo;
try {
$stmt = $pdo->prepare("SELECT * FROM products WHERE is_featured = 1 ORDER BY created_at DESC LIMIT :limit");
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();
return $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
error_log("Database Error: " . $e->getMessage());
return [];
}
}

function getAllCategories() {
global $pdo;
try {
$stmt = $pdo->query("SELECT * FROM categories WHERE image_url IS NOT NULL ORDER BY name ASC");
return $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
error_log("Database Error: " . $e->getMessage());
return [];
}
}

// NEW function to get a single product by ID.
function getProductById($id) {
global $pdo;
try {
$stmt = $pdo->prepare("SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
$stmt->execute([$id]);
return $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
error_log("Database Error: " . $e->getMessage());
return null;
}
}

// NEW function to get related products from the same category.
function getProductsByCategoryId($categoryId, $limit, $excludeProductId = null) {
global $pdo;
try {
$sql = "SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.category_id = ? AND p.id != ? LIMIT ?";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(1, $categoryId, PDO::PARAM_INT);
$stmt->bindParam(2, $excludeProductId, PDO::PARAM_INT);
$stmt->bindParam(3, $limit, PDO::PARAM_INT);
$stmt->execute();
return $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
error_log("Database Error: " . $e->getMessage());
return [];
}
}

// NEW function for header navigation, in case you need to limit the number of categories.
function getNavCategories($limit) {
global $pdo;
try {
$stmt = $pdo->prepare("SELECT id, name FROM categories ORDER BY name ASC LIMIT :limit");
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();
return $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
error_log("Database Error: " . $e->getMessage());
return [];
}}