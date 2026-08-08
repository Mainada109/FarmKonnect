<?php
function sendSMS($phone,$message){

// Simulated SMS (replace with Africa's Talking)

file_put_contents("sms_log.txt",$phone." - ".$message."\n",FILE_APPEND);

}
?>


