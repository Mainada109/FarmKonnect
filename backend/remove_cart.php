<?php
include("db.php");

$id = $_POST['id'];

$stmt = $conn->prepare("DELETE FROM cart WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
echo "removed";
?>




