<?php
// --- Database Credentials ---
// Replace with your actual database details.
define('DB_HOST', 'localhost');
define('DB_NAME', 'u730228770_ecomme_web'); // Your database name
define('DB_USER', 'u730228770_ecomme_web_db'); // Your database username
define('DB_PASS', 'Admin@41212!!!'); // Your database password

// --- Establish a Database Connection (PDO) ---
try {
    // Create a new PDO instance
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);

    // Set the PDO error mode to exception for better error handling
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Set the default fetch mode to associative array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // If connection fails, stop the script and display an error message.
    // In a real-world application, you would log this error and show a generic message.
    die("ERROR: Could not connect to the database. " . $e->getMessage());
}
?>
