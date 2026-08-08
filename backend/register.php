<?php
include("db.php");
include("send_notification.php");

$name = $_POST['name'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$phone = $_POST['phone'];

// Check existing email
$check = $conn->prepare("SELECT id FROM farmers WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();
$check->store_result();
if ($check->num_rows > 0) {
    die("Email already registered");
}
$check->close();

// Insert new farmer
$stmt = $conn->prepare("INSERT INTO farmers (name, email, password, phone) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $password, $phone);
if ($stmt->execute()) {
    // Send welcome email
    sendRegistrationNotification($email, $name);
    header("Location: ../login.php?registered=success");
} else {
    echo "Registration failed: " . $stmt->error;
}
?>




