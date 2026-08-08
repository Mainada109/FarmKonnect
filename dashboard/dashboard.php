<?php
session_start();
if(!isset($_SESSION['farmer_id'])){
die("Login first");
}
?>

<form action="../backend/upload.php" method="POST" enctype="multipart/form-data">

<input type="text" name="name" placeholder="Product">
<input type="number" name="price" placeholder="Price">
<input type="number" name="stock" placeholder="Stock">
<input type="file" name="image">

<button>Upload</button>

</form>