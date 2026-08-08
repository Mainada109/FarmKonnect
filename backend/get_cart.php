<?php
include("db.php");

$result = mysqli_query($conn, "
    SELECT cart.id, cart.quantity, products.name, products.price, products.image 
    FROM cart 
    JOIN products ON cart.product_id = products.id
");

$cart = [];
while ($row = mysqli_fetch_assoc($result)) {
    $cart[] = $row;
}

echo json_encode($cart);
?>