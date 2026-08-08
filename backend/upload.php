<?php
session_start();
include("db.php");
include("send_notification.php");

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log file for debugging
$log_file = __DIR__ . '/upload_errors.log';
file_put_contents($log_file, date('Y-m-d H:i:s') . " - Upload started\n", FILE_APPEND);

if (!isset($_SESSION['user'])) {
    file_put_contents($log_file, "ERROR: User not logged in\n", FILE_APPEND);
    die("Login required");
}

$farmer_id = $_SESSION['user']['id'];
$farmer_name = $_SESSION['user']['name'];
$farmer_email = $_SESSION['user']['email'];

// Check required fields
if (empty($_POST['name']) || empty($_POST['price']) || empty($_POST['stock']) || empty($_POST['unit']) || empty($_POST['category'])) {
    file_put_contents($log_file, "ERROR: Missing required fields\n", FILE_APPEND);
    die("All fields are required.");
}

$name = $_POST['name'];
$price = floatval($_POST['price']);
$stock = intval($_POST['stock']);
$unit = $_POST['unit'];
$description = $_POST['description'] ?? '';
$category = $_POST['category'];

// Check image upload
if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    file_put_contents($log_file, "ERROR: Image upload failed. Code: " . ($_FILES['image']['error'] ?? 'No file') . "\n", FILE_APPEND);
    die("Image upload failed. Error code: " . ($_FILES['image']['error'] ?? 'unknown'));
}

$image = $_FILES['image']['name'];
$tmp = $_FILES['image']['tmp_name'];
$target_dir = "../imgs/";

// Check if imgs folder exists and is writable
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
    file_put_contents($log_file, "Created imgs folder\n", FILE_APPEND);
}
if (!is_writable($target_dir)) {
    file_put_contents($log_file, "ERROR: imgs folder not writable\n", FILE_APPEND);
    die("Server error: Cannot write to imgs folder.");
}

$imageFileType = strtolower(pathinfo($image, PATHINFO_EXTENSION));

// Validate image
$check = getimagesize($tmp);
if ($check === false) {
    file_put_contents($log_file, "ERROR: File is not a valid image\n", FILE_APPEND);
    die("File is not a valid image.");
}
if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
    file_put_contents($log_file, "ERROR: Invalid file type: $imageFileType\n", FILE_APPEND);
    die("Only JPG, PNG & GIF are allowed.");
}

$new_filename = uniqid() . "." . $imageFileType;
$target_file = $target_dir . $new_filename;

if (!move_uploaded_file($tmp, $target_file)) {
    file_put_contents($log_file, "ERROR: move_uploaded_file failed\n", FILE_APPEND);
    die("Failed to save image file.");
}

// Insert into database
$stmt = $conn->prepare("INSERT INTO products (farmer_id, name, price, stock, unit, description, image, category) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
if (!$stmt) {
    file_put_contents($log_file, "ERROR: Prepare failed: " . $conn->error . "\n", FILE_APPEND);
    die("Database error: " . $conn->error);
}

$stmt->bind_param("isdissss", $farmer_id, $name, $price, $stock, $unit, $description, $new_filename, $category);

if ($stmt->execute()) {
    file_put_contents($log_file, "SUCCESS: Product inserted, ID: " . $stmt->insert_id . "\n", FILE_APPEND);
    // Send email (optional, if fails we still continue)
    if (function_exists('sendProductUploadNotification')) {
        sendProductUploadNotification($farmer_email, $farmer_name, $name, $price, $category);
    }
    header("Location: ../dashboard/farmer_dashboard.php?upload=success");
    exit();
} else {
    file_put_contents($log_file, "ERROR: Execute failed: " . $stmt->error . "\n", FILE_APPEND);
    die("Database error: " . $stmt->error);
}
?>