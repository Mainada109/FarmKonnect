<?php
include("db.php");

$product_id = $_POST['product_id'];

// Check if product already in cart
$check = $conn->prepare("SELECT id FROM cart WHERE product_id = ?");
$check->bind_param("i", $product_id);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    $update = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE product_id = ?");
    $update->bind_param("i", $product_id);
    $update->execute();
} else {
    $insert = $conn->prepare("INSERT INTO cart (product_id, quantity) VALUES (?, 1)");
    $insert->bind_param("i", $product_id);
    $insert->execute();
}
echo "success";
?>






