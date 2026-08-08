<?php
include("db.php");

$result = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
$products = [];

while ($row = mysqli_fetch_assoc($result)) {
    $products[] = $row;
}

header('Content-Type: application/json');
echo json_encode($products);
?>