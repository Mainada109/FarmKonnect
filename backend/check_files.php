<?php
echo "<h3>Files in backend folder:</h3>";
$files = scandir(__DIR__);
echo "<pre>";
print_r($files);
echo "</pre>";

echo "<h3>Full path expected:</h3>";
echo __DIR__ . DIRECTORY_SEPARATOR . "mpesa_functions.php";
?>