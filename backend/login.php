<?php
session_start();
include("db.php");

$email = $_POST['email'];
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT * FROM farmers WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user'] = $user;
    if ($user['role'] == 'admin') {
        header("Location: ../admin.php");
    } else {
        header("Location: ../dashboard/farmer_dashboard.php");
    }
} else {
    echo "Invalid login credentials";
}
?>


