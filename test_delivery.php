<?php
session_start();
// Simulate farmer login (replace with a valid farmer ID)
$_SESSION['user'] = ['id' => 1]; // Use an actual farmer ID from your database

$order_id = 15; // Use an existing order ID belonging to that farmer
$new_status = 'shipped';

$_POST['order_id'] = $order_id;
$_POST['status'] = $new_status;

include 'backend/update_delivery.php';
?>