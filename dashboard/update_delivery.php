<?php
session_start();
include("db.php");

if (!isset($_SESSION['user'])) {
    die("Login required");
}

$farmer_id = $_SESSION['user']['id'];
$order_id = intval($_POST['order_id'] ?? 0);
$new_status = $_POST['status'] ?? '';

if (!in_array($new_status, ['shipped', 'delivered'])) {
    die("Invalid status");
}

// Verify farmer owns the product in this order
$check = $conn->prepare("SELECT o.id FROM orders o 
                         JOIN products p ON o.product_id = p.id 
                         WHERE o.id = ? AND p.farmer_id = ?");
$check->bind_param("ii", $order_id, $farmer_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows == 0) {
    die("Permission denied");
}

// Update delivery status
$stmt = $conn->prepare("UPDATE orders SET delivery_status = ? WHERE id = ?");
$stmt->bind_param("si", $new_status, $order_id);

if ($stmt->execute()) {
    echo "success";
} else {
    echo "Database error: " . $stmt->error;
}
?>