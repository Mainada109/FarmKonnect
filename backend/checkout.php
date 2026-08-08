<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("db.php");
require_once 'mpesa_functions.php';
include("send_notification.php");

$phone = $_POST['phone'] ?? '';
$buyer_email = $_POST['email'] ?? '';
$delivery_address = $_POST['delivery_address'] ?? '';

if (!preg_match('/^254[0-9]{9}$/', $phone)) {
    echo json_encode(["status" => "error", "message" => "Invalid phone number"]);
    exit();
}
if (empty($delivery_address)) {
    echo json_encode(["status" => "error", "message" => "Delivery address required"]);
    exit();
}

$cart_query = "
    SELECT c.quantity, p.id as product_id, p.name, p.price, p.farmer_id, 
           f.email as farmer_email, f.name as farmer_name
    FROM cart c
    JOIN products p ON c.product_id = p.id
    JOIN farmers f ON p.farmer_id = f.id
";
$cart_result = mysqli_query($conn, $cart_query);

if (mysqli_num_rows($cart_result) == 0) {
    echo json_encode(["status" => "error", "message" => "Cart is empty"]);
    exit();
}

$order_items = [];
$total = 0;

while ($item = mysqli_fetch_assoc($cart_result)) {
    $subtotal = $item['price'] * $item['quantity'];
    $total += $subtotal;
    $order_items[] = $item;
    
    $insert = $conn->prepare("INSERT INTO orders (product_id, quantity, total, phone, delivery_address, status, delivery_status) VALUES (?, ?, ?, ?, ?, 'pending', 'pending')");
    $item_total = $item['price'] * $item['quantity'];
    $insert->bind_param("iidss", $item['product_id'], $item['quantity'], $item_total, $phone, $delivery_address);
    $insert->execute();
    
    $update = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
    $update->bind_param("ii", $item['quantity'], $item['product_id']);
    $update->execute();
    
    // Check if stock became 0
    $stock_check = $conn->prepare("SELECT stock, f.email, f.name, p.name as product_name 
                                   FROM products p 
                                   JOIN farmers f ON p.farmer_id = f.id 
                                   WHERE p.id = ?");
    $stock_check->bind_param("i", $item['product_id']);
    $stock_check->execute();
    $stock_result = $stock_check->get_result();
    $prod = $stock_result->fetch_assoc();
    if ($prod && $prod['stock'] == 0) {
        sendOutOfStockNotification($prod['email'], $prod['name'], $prod['product_name'], $item['product_id']);
    }
    
    sendFarmerOrderAlert($item['farmer_email'], $item['farmer_name'], $item['name'], $item['quantity'], $phone);
}

mysqli_query($conn, "DELETE FROM cart");

// M-Pesa STK Push
$mpesa_response = lipaNaMpesaOnline($phone, $total, "FARM" . time(), "FarmConnect Order");
file_put_contents('mpesa_log.txt', date('Y-m-d H:i:s') . " STK Response: " . json_encode($mpesa_response) . PHP_EOL, FILE_APPEND);

if (isset($mpesa_response['ResponseCode']) && $mpesa_response['ResponseCode'] == "0") {
    if (!empty($buyer_email)) {
        $items_for_email = [];
        foreach ($order_items as $it) {
            $items_for_email[] = ['name' => $it['name'], 'quantity' => $it['quantity'], 'price' => $it['price']];
        }
        sendBuyerOrderConfirmation($buyer_email, $phone, $items_for_email, $total);
    }
    echo json_encode(["status" => "success", "message" => "STK Push sent", "total" => $total]);
} else {
    $error = $mpesa_response['errorMessage'] ?? 'Unknown error';
    echo json_encode(["status" => "error", "message" => $error, "total" => $total]);
}
?>