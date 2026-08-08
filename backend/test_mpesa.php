<?php
require_once 'C:/xampp/htdocs/Farmconnect/backend/mpesa_functions.php';

$response = lipaNaMpesaOnline("254708374149", "10", "TEST123", "Test Payment");
echo "<pre>";
print_r($response);
echo "</pre>";
?>