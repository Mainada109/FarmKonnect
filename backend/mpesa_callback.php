<?php
// backend/mpesa_callback.php

$callbackJSONData = file_get_contents('php://input');
$callbackData = json_decode($callbackJSONData, true);

// Log everything for debugging
file_put_contents('mpesa_callback_log.txt', date('Y-m-d H:i:s') . " Callback: " . $callbackJSONData . PHP_EOL, FILE_APPEND);

if (isset($callbackData['Body']['stkCallback'])) {
    $stkCallback = $callbackData['Body']['stkCallback'];
    $result_code = $stkCallback['ResultCode'];
    
    if ($result_code == 0) {
        // Payment successful – update order status in DB here
    }
}

header("HTTP/1.1 200 OK");
echo "OK";
?>


