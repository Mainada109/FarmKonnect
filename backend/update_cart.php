<?php
include("db.php");

$id = $_POST['id'];
$quantity = $_POST['quantity'];

$stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
$stmt->bind_param("ii", $quantity, $id);
$stmt->execute();
echo "updated";
?>




