<?php
include("db.php");
mysqli_query($conn, "DELETE FROM cart");
echo "cleared";
?>



