<?php
session_start();
require_once 'admin_header.php';
require_once '../includes/db_connect.php';

// Security check
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

$error_message = '';
$success_message = '';

// Handle CSV upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['csv_file'])) {
    if ($_FILES['csv_file']['error'] == 0) {
        $upload_dir = "../uploads/csv/";
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_name = time() . '_' . $_FILES['csv_file']['name'];
        $file_path = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['csv_file']['tmp_name'], $file_path)) {
            // Process CSV file
            $result = processCSVFile($file_path, $pdo);
            
            if ($result['success']) {
                $success_message = "CSV imported successfully! " . $result['imported'] . " products imported, " . $result['skipped'] . " skipped.";
            } else {
                $error_message = "Error importing CSV: " . $result['message'];
            }
            
            // Clean up uploaded file
            unlink($file_path);
        } else {
            $error_message = "Failed to upload file.";
        }
    } else {
        $error_message = "Please select a valid CSV file.";
    }
}

function processCSVFile($file_path, $pdo) {
    $imported = 0;
    $skipped = 0;
    $errors = [];
    
    try {
        $file = fopen($file_path, 'r');
        
        // Read header row
        $headers = fgetcsv($file);
        if (!$headers) {
            return ['success' => false, 'message' => 'Invalid CSV file format'];
        }
        
        // Expected headers
        $expected_headers = ['name', 'description', 'price', 'stock', 'category_name', 'is_featured', 'image_url'];
        
        // Validate headers
        foreach ($expected_headers as $expected) {
            if (!in_array($expected, $headers)) {
                return ['success' => false, 'message' => "Missing required column: $expected"];
            }
        }
        
        // Get category mapping
        $categories = [];
        $stmt = $pdo->query("SELECT id, name FROM categories");
        while ($row = $stmt->fetch()) {
            $categories[strtolower($row['name'])] = $row['id'];
        }
        
        $pdo->beginTransaction();
        
        // Process each row
        while (($row = fgetcsv($file)) !== FALSE) {
            if (count($row) != count($headers)) {
                $skipped++;
                continue;
            }
            
            $data = array_combine($headers, $row);
            
            // Validate required fields
            if (empty($data['name']) || empty($data['price']) || empty($data['stock'])) {
                $skipped++;
                continue;
            }
            
            // Get category ID
            $category_id = null;
            if (!empty($data['category_name'])) {
                $category_key = strtolower(trim($data['category_name']));
                if (isset($categories[$category_key])) {
                    $category_id = $categories[$category_key];
                } else {
                    // Create new category if it doesn't exist
                    $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
                    $stmt->execute([trim($data['category_name'])]);
                    $category_id = $pdo->lastInsertId();
                    $categories[$category_key] = $category_id;
                }
            }
            
            // Sanitize data
            $name = trim($data['name']);
            $description = trim($data['description'] ?? '');
            $price = floatval($data['price']);
            $stock = intval($data['stock']);
            $is_featured = (strtolower(trim($data['is_featured'] ?? '')) === 'yes' || $data['is_featured'] === '1') ? 1 : 0;
            $image_url = trim($data['image_url'] ?? '');
            
            // Check if product already exists
            $stmt = $pdo->prepare("SELECT id FROM products WHERE name = ?");
            $stmt->execute([$name]);
            if ($stmt->fetch()) {
                $skipped++;
                continue;
            }
            
            // Insert product
            $sql = "INSERT INTO products (name, description, price, stock, category_id, is_featured, image_url, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $description, $price, $stock, $category_id, $is_featured, $image_url]);
            
            $imported++;
        }
        
        $pdo->commit();
        fclose($file);
        
        return [
            'success' => true,
            'imported' => $imported,
            'skipped' => $skipped
        ];
        
    } catch (Exception $e) {
        $pdo->rollBack();
        if (isset($file)) fclose($file);
        return ['success' => false, 'message' => $e->getMessage()];
    }
}
?>

<main class="main-content">
    <div class="content-header">
        <h1>Import Products from CSV</h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="manage_products.php">Products</a></li>
                <li class="breadcrumb-item active" aria-current="page">Import CSV</li>
            </ol>
        </nav>
    </div>

    <section class="content-body">
        <?php if ($error_message): ?>
            <div class="error-message-box"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <?php if ($success_message): ?>
            <div class="success-message-box"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>

        <div class="csv-import-layout">
            <div class="csv-upload-section">
                <div class="content-box">
                    <h3 class="meta-box-title">Upload CSV File</h3>
                    <div class="csv-upload-content">
                        <form action="import_products_csv.php" method="POST" enctype="multipart/form-data" class="csv-upload-form">
                            <div class="file-upload-area">
                                <div class="file-upload-icon">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                </div>
                                <div class="file-upload-text">
                                    <h4>Choose CSV File</h4>
                                    <p>Select a CSV file with product data to import</p>
                                </div>
                                <input type="file" name="csv_file" accept=".csv" required class="file-input" id="csvFileInput">
                                <label for="csvFileInput" class="file-input-label">Browse Files</label>
                            </div>
                            <div class="file-info" id="fileInfo" style="display: none;">
                                <p><strong>Selected file:</strong> <span id="fileName"></span></p>
                                <p><strong>File size:</strong> <span id="fileSize"></span></p>
                            </div>
                            <button type="submit" class="btn-publish csv-import-btn">
                                <i class="fas fa-upload"></i> Import Products
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="csv-instructions-section">
                <div class="content-box">
                    <h3 class="meta-box-title">CSV Format Instructions</h3>
                    <div class="instructions-content">
                        <h4>Required Columns:</h4>
                        <ul class="csv-columns-list">
                            <li><strong>name</strong> - Product name (required)</li>
                            <li><strong>description</strong> - Product description</li>
                            <li><strong>price</strong> - Product price (required, numeric)</li>
                            <li><strong>stock</strong> - Stock quantity (required, integer)</li>
                            <li><strong>category_name</strong> - Category name</li>
                            <li><strong>is_featured</strong> - Featured status (yes/no or 1/0)</li>
                            <li><strong>image_url</strong> - Image filename (optional)</li>
                        </ul>
                        
                        <h4>Important Notes:</h4>
                        <ul class="csv-notes-list">
                            <li>First row must contain column headers exactly as shown above</li>
                            <li>Categories will be created automatically if they don't exist</li>
                            <li>Duplicate product names will be skipped</li>
                            <li>Images should be uploaded separately to uploads/product-image/ folder</li>
                            <li>Maximum file size: 10MB</li>
                            <li>Recommended format: UTF-8 encoded CSV</li>
                        </ul>

                        <div class="sample-download">
                            <h4>Need a template?</h4>
                            <a href="download_sample_csv.php" class="btn-secondary-outline">
                                <i class="fas fa-download"></i> Download Sample CSV
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('csvFileInput');
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');

    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            fileName.textContent = file.name;
            fileSize.textContent = formatFileSize(file.size);
            fileInfo.style.display = 'block';
        } else {
            fileInfo.style.display = 'none';
        }
    });

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
});
</script>

<?php require_once 'admin_footer.php'; ?>