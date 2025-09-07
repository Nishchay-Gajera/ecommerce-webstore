<?php
session_start();

// Security check
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

// Sample product data
$sample_products = [
    [
        'name' => 'Elegant Wedding Lehenga',
        'description' => 'Beautiful red and gold wedding lehenga with intricate embroidery and beadwork. Perfect for wedding ceremonies.',
        'price' => '45000',
        'stock' => '10',
        'category_name' => 'Lehenga',
        'is_featured' => 'yes',
        'image_url' => 'wedding_lehenga_1.jpg'
    ],
    [
        'name' => 'Designer Silk Saree',
        'description' => 'Premium silk saree with traditional border design. Ideal for festive occasions and special events.',
        'price' => '8500',
        'stock' => '25',
        'category_name' => 'Saree',
        'is_featured' => 'no',
        'image_url' => 'silk_saree_1.jpg'
    ],
    [
        'name' => 'Bridal Sharara Set',
        'description' => 'Exquisite bridal sharara set in emerald green with heavy zardozi work and matching dupatta.',
        'price' => '35000',
        'stock' => '5',
        'category_name' => 'Sharara',
        'is_featured' => 'yes',
        'image_url' => 'bridal_sharara_1.jpg'
    ],
    [
        'name' => 'Party Wear Anarkali',
        'description' => 'Stunning purple anarkali dress with sequin work. Perfect for parties and celebrations.',
        'price' => '12000',
        'stock' => '15',
        'category_name' => 'Anarkali',
        'is_featured' => 'no',
        'image_url' => 'anarkali_purple_1.jpg'
    ],
    [
        'name' => 'Traditional Banarasi Saree',
        'description' => 'Authentic Banarasi silk saree with gold zari work. Classic piece for traditional occasions.',
        'price' => '15000',
        'stock' => '8',
        'category_name' => 'Saree',
        'is_featured' => 'yes',
        'image_url' => 'banarasi_saree_1.jpg'
    ],
    [
        'name' => 'Designer Palazzo Set',
        'description' => 'Contemporary palazzo set with kurti in pastel pink. Comfortable and stylish for casual events.',
        'price' => '4500',
        'stock' => '30',
        'category_name' => 'Palazzo',
        'is_featured' => 'no',
        'image_url' => 'palazzo_pink_1.jpg'
    ],
    [
        'name' => 'Heavy Bridal Lehenga',
        'description' => 'Luxurious bridal lehenga with extensive mirror work and pearl embellishments. Dream wedding outfit.',
        'price' => '75000',
        'stock' => '3',
        'category_name' => 'Lehenga',
        'is_featured' => 'yes',
        'image_url' => 'heavy_bridal_lehenga_1.jpg'
    ],
    [
        'name' => 'Festive Cotton Kurti',
        'description' => 'Comfortable cotton kurti with block print design. Perfect for daily wear and casual occasions.',
        'price' => '1800',
        'stock' => '50',
        'category_name' => 'Kurti',
        'is_featured' => 'no',
        'image_url' => 'cotton_kurti_1.jpg'
    ],
    [
        'name' => 'Wedding Guest Saree',
        'description' => 'Elegant chiffon saree in royal blue with delicate border. Ideal for wedding functions.',
        'price' => '6500',
        'stock' => '20',
        'category_name' => 'Saree',
        'is_featured' => 'no',
        'image_url' => 'chiffon_saree_blue_1.jpg'
    ],
    [
        'name' => 'Indo Western Gown',
        'description' => 'Modern indo-western gown with traditional embroidery. Perfect fusion of contemporary and classic styles.',
        'price' => '18000',
        'stock' => '12',
        'category_name' => 'Gown',
        'is_featured' => 'yes',
        'image_url' => 'indo_western_gown_1.jpg'
    ]
];

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="sample_products.csv"');
header('Pragma: no-cache');
header('Expires: 0');

// Create file pointer connected to the output stream
$output = fopen('php://output', 'w');

// Write CSV headers
$headers = ['name', 'description', 'price', 'stock', 'category_name', 'is_featured', 'image_url'];
fputcsv($output, $headers);

// Write sample data
foreach ($sample_products as $product) {
    fputcsv($output, $product);
}

// Close the file pointer
fclose($output);
exit();
?>