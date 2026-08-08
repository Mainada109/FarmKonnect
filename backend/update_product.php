<?php
session_start();
include("db.php");

if (!isset($_SESSION['user'])) {
    die("Login required");
}

$farmer_id = $_SESSION['user']['id'];
$product_id = intval($_POST['product_id']);
$name = $_POST['name'];
$price = floatval($_POST['price']);
$stock = intval($_POST['stock']);
$unit = $_POST['unit'];
$description = $_POST['description'];
$category = $_POST['category'];

// Verify ownership
$check = $conn->prepare("SELECT image FROM products WHERE id = ? AND farmer_id = ?");
$check->bind_param("ii", $product_id, $farmer_id);
$check->execute();
$result = $check->get_result();
$old = $result->fetch_assoc();

if (!$old) {
    die("You don't have permission to edit this product.");
}

$image_name = $old['image'];

// Handle new image upload
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $tmp = $_FILES['image']['tmp_name'];
    $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $new_filename = uniqid() . '.' . $ext;
    $target = "../imgs/" . $new_filename;
    if (move_uploaded_file($tmp, $target)) {
        $old_path = "../imgs/" . $old['image'];
        if (file_exists($old_path)) unlink($old_path);
        $image_name = $new_filename;
    }
}

$stmt = $conn->prepare("UPDATE products SET name=?, price=?, stock=?, unit=?, description=?, category=?, image=? WHERE id=?");
$stmt->bind_param("sdissssi", $name, $price, $stock, $unit, $description, $category, $image_name, $product_id);
if ($stmt->execute()) {
    echo "success";
} else {
    echo "Database error: " . $stmt->error;
}
?>