<?php
session_start();
include("db.php");

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Login required']);
    exit();
}

$farmer_id = $_SESSION['user']['id'];
$product_id = intval($_GET['id'] ?? 0);

$stmt = $conn->prepare("SELECT * FROM products WHERE id = ? AND farmer_id = ?");
$stmt->bind_param("ii", $product_id, $farmer_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    http_response_code(404);
    echo json_encode(['error' => 'Product not found']);
    exit();
}

echo json_encode($product);
?>