<?php
session_start();
include("db.php");
include("send_notification.php");

error_reporting(E_ALL);
ini_set('display_errors', 1);

$log_file = __DIR__ . '/delete_errors.log';
file_put_contents($log_file, date('Y-m-d H:i:s') . " - Delete started\n", FILE_APPEND);

if (!isset($_SESSION['user'])) {
    file_put_contents($log_file, "ERROR: User not logged in\n", FILE_APPEND);
    die("Login required");
}

$farmer_id = $_SESSION['user']['id'];
$farmer_name = $_SESSION['user']['name'];
$farmer_email = $_SESSION['user']['email'];

if (!isset($_POST['product_id'])) {
    file_put_contents($log_file, "ERROR: No product_id provided\n", FILE_APPEND);
    die("Product ID missing");
}

$product_id = intval($_POST['product_id']);

// Get product details for image deletion and email
$check = $conn->prepare("SELECT name, image FROM products WHERE id = ? AND farmer_id = ?");
$check->bind_param("ii", $product_id, $farmer_id);
$check->execute();
$result = $check->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    file_put_contents($log_file, "ERROR: Product not found or permission denied\n", FILE_APPEND);
    die("Product not found or you don't have permission.");
}

// Delete image file
$image_path = "../imgs/" . $product['image'];
if (file_exists($image_path)) {
    if (!unlink($image_path)) {
        file_put_contents($log_file, "WARNING: Could not delete image: $image_path\n", FILE_APPEND);
    }
}

// Delete record
$stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
if ($stmt->execute()) {
    file_put_contents($log_file, "SUCCESS: Product deleted, ID: $product_id\n", FILE_APPEND);
    if (function_exists('sendProductDeleteNotification')) {
        sendProductDeleteNotification($farmer_email, $farmer_name, $product['name']);
    }
    echo "success";
} else {
    file_put_contents($log_file, "ERROR: Delete failed: " . $stmt->error . "\n", FILE_APPEND);
    echo "Database error: " . $stmt->error;
}
?>