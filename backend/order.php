<?php
include("db.php");
include("send_notification.php");

$product_id = $_POST['product_id'];
$quantity = intval($_POST['quantity']);
$phone = $_POST['phone'];
$buyer_email = $_POST['email'] ?? '';
$delivery_address = $_POST['delivery_address'] ?? '';

// Get product and farmer info
$stmt = $conn->prepare("SELECT p.*, f.email as farmer_email, f.name as farmer_name 
                        FROM products p 
                        JOIN farmers f ON p.farmer_id = f.id 
                        WHERE p.id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) die("Product not found");

$total = $product['price'] * $quantity;

// Insert order
$insert = $conn->prepare("INSERT INTO orders (product_id, quantity, total, phone, delivery_address, status, delivery_status) VALUES (?, ?, ?, ?, ?, 'pending', 'pending')");
$insert->bind_param("iidss", $product_id, $quantity, $total, $phone, $delivery_address);
$insert->execute();

// Update stock
$update = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
$update->bind_param("ii", $quantity, $product_id);
$update->execute();

// Check if stock became 0
$stock_check = $conn->prepare("SELECT stock, f.email, f.name, p.name as product_name 
                               FROM products p 
                               JOIN farmers f ON p.farmer_id = f.id 
                               WHERE p.id = ?");
$stock_check->bind_param("i", $product_id);
$stock_check->execute();
$stock_result = $stock_check->get_result();
$prod = $stock_result->fetch_assoc();

if ($prod && $prod['stock'] == 0) {
    sendOutOfStockNotification($prod['email'], $prod['name'], $prod['product_name'], $product_id);
}

// Send buyer receipt (optional)
if (!empty($buyer_email)) {
    $order_items = [[
        'name' => $product['name'],
        'quantity' => $quantity,
        'price' => $product['price']
    ]];
    sendBuyerOrderConfirmation($buyer_email, $phone, $order_items, $total);
}

// Alert farmer
sendFarmerOrderAlert($product['farmer_email'], $product['farmer_name'], $product['name'], $quantity, $phone);

echo json_encode(["status" => "success", "total" => $total]);
?>