<?php
require_once 'db_connect.php';

function getNewArrivalProducts($limit = 7) {
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

function getFeaturedProducts($limit = 7) {
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

// Function to get a single product by ID.
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

// Function to get related products from the same category (default 7 products).
function getProductsByCategoryId($categoryId, $limit = 7, $excludeProductId = null) {
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

// Function for header navigation (default 7 categories).
function getNavCategories($limit = 7) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT id, name FROM categories ORDER BY name ASC LIMIT :limit");
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database Error: " . $e->getMessage());
        return [];
    }
}

// Function to get a product's stock count for low-stock alerts.
function getProductStock($id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        error_log("Database Error: " . $e->getMessage());
        return null;
    }
}

// Additional helper function to get any products with a default limit of 7
function getProducts($limit = 7, $orderBy = 'created_at DESC') {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY $orderBy LIMIT :limit");
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database Error: " . $e->getMessage());
        return [];
    }
}
?>