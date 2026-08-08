<?php
include("backend/db.php");
?>

<h2>Admin Panel</h2>

<h3>Farmers</h3>
<?php
$res=mysqli_query($conn,"SELECT * FROM farmers");
while($r=mysqli_fetch_assoc($res)){
echo $r['name']."<br>";
}
?>

<h3>Products</h3>
<?php
$res=mysqli_query($conn,"SELECT * FROM products");
while($r=mysqli_fetch_assoc($res)){
echo $r['name']."<br>";
}
?>

<h3>Orders</h3>
<?php
$res=mysqli_query($conn,"SELECT * FROM orders");
while($r=mysqli_fetch_assoc($res)){
echo "Ksh ".$r['total']."<br>";
}
?>