<?php
include("db.php");
include("sms.php");

$res = mysqli_query($conn, "SELECT * FROM farmers");
while($row = mysqli_fetch_assoc($res)){
    sendSMS($row['phone'], "Market Day in Nairobi this weekend!");
}
?>



