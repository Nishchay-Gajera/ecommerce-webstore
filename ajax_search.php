<?php
require_once 'includes/db_connect.php';

// Set content type to JSON
header('Content-Type: application/json');

// Get search parameters
$query = trim($_GET['query'] ?? '');
$category = $_GET['category'] ?? '';

// Initialize response
$response = [];

// Validate query
if (strlen($query) < 2) {
    echo json_encode([]);
    exit;
}

try {
    // Base SQL query
    $sql = "SELECT p.id, p.name, p.price, p.image_url, c.name as category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE p.name LIKE ?";
    
    $params = ["%$query%"];
    
    // Add category filter if specified
    if (!empty($category) && is_numeric($category)) {
        $sql .= " AND p.category_id = ?";
        $params[] = $category;
    }
    
    // Limit results for performance
    $sql .= " ORDER BY p.name ASC LIMIT 8";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format the results
    foreach ($products as $product) {
        $response[] = [
            'id' => (int)$product['id'],
            'name' => htmlspecialchars($product['name']),
            'price' => number_format($product['price'], 2),
            'image_url' => htmlspecialchars($product['image_url'] ?? 'placeholder.png'),
            'category_name' => htmlspecialchars($product['category_name'] ?? 'Uncategorized')
        ];
    }
    
} catch (PDOException $e) {
    // Log error and return empty array
    error_log("AJAX Search Error: " . $e->getMessage());
    $response = [];
}

// Return JSON response
echo json_encode($response);
exit;
?>