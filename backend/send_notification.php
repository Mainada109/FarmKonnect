<?php
// backend/send_notification.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load PHPMailer and configuration
require __DIR__ . '/phpmailer/src/Exception.php';
require __DIR__ . '/phpmailer/src/PHPMailer.php';
require __DIR__ . '/phpmailer/src/SMTP.php';
require __DIR__ . '/mail_config.php';

// =============================================
// 1. WELCOME EMAIL – AFTER REGISTRATION
// =============================================
function sendRegistrationNotification($farmer_email, $farmer_name) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = MAIL_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_USERNAME;
        $mail->Password   = MAIL_PASSWORD;
        $mail->SMTPSecure = MAIL_ENCRYPTION;
        $mail->Port       = MAIL_PORT;
        
        $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
        $mail->addAddress($farmer_email, $farmer_name);
        
        $mail->isHTML(true);
        $mail->Subject = 'Welcome to FarmConnect! Start Selling Your Produce';
        $mail->Body    = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: #2E7D32; color: white; padding: 25px; text-align: center; border-radius: 10px 10px 0 0; }
                    .content { background: #f9f9f9; padding: 30px; }
                    .button { display: inline-block; padding: 12px 30px; background: #ff8f00; color: #1b5e20; text-decoration: none; border-radius: 50px; font-weight: bold; margin: 20px 0; }
                    .footer { text-align: center; color: #666; margin-top: 20px; font-size: 12px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>🌾 Welcome, {$farmer_name}!</h1>
                    </div>
                    <div class='content'>
                        <p>Thank you for joining FarmConnect as a farmer.</p>
                        <p><strong>What you can do:</strong></p>
                        <ul>
                            <li>📸 Upload your farm products</li>
                            <li>💰 Set your own prices</li>
                            <li>📊 Reach customers without middlemen</li>
                            <li>📱 Receive orders instantly</li>
                        </ul>
                        <div style='text-align:center;'>
                            <a href='http://localhost/Farmconnect/login.php' class='button'>Go to Dashboard →</a>
                        </div>
                        <p>Happy farming!<br><strong>The FarmConnect Team</strong></p>
                    </div>
                    <div class='footer'>
                        <p>© " . date('Y') . " FarmConnect. All rights reserved.</p>
                    </div>
                </div>
            </body>
            </html>
        ";
        $mail->AltBody = "Welcome to FarmConnect, {$farmer_name}! Log in at http://localhost/Farmconnect/login.php to start selling.";
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Registration email failed: {$mail->ErrorInfo}");
        return false;
    }
}

// =============================================
// 2. PRODUCT UPLOAD CONFIRMATION – TO FARMER
// =============================================
function sendProductUploadNotification($farmer_email, $farmer_name, $product_name, $product_price, $product_category) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = MAIL_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_USERNAME;
        $mail->Password   = MAIL_PASSWORD;
        $mail->SMTPSecure = MAIL_ENCRYPTION;
        $mail->Port       = MAIL_PORT;
        
        $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
        $mail->addAddress($farmer_email, $farmer_name);
        
        $mail->isHTML(true);
        $mail->Subject = 'Your Product Has Been Uploaded Successfully!';
        $mail->Body    = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: #2E7D32; color: white; padding: 25px; text-align: center; border-radius: 10px 10px 0 0; }
                    .content { background: #f9f9f9; padding: 30px; }
                    .product-details { background: white; padding: 15px; border-radius: 8px; margin: 20px 0; }
                    .footer { text-align: center; color: #666; margin-top: 20px; font-size: 12px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>✅ Product Uploaded</h1>
                    </div>
                    <div class='content'>
                        <p>Dear {$farmer_name},</p>
                        <p>Your product is now live on FarmConnect!</p>
                        <div class='product-details'>
                            <h3>Product Details</h3>
                            <table style='width:100%;'>
                                <tr><td><strong>Name:</strong></td><td>{$product_name}</td></tr>
                                <tr><td><strong>Price:</strong></td><td>Ksh " . number_format($product_price, 2) . "</td></tr>
                                <tr><td><strong>Category:</strong></td><td>" . ucfirst($product_category) . "</td></tr>
                            </table>
                        </div>
                        <p>Customers can now see and purchase your product.</p>
                    </div>
                    <div class='footer'>
                        <p>© " . date('Y') . " FarmConnect</p>
                    </div>
                </div>
            </body>
            </html>
        ";
        $mail->AltBody = "Your product '{$product_name}' is now live on FarmConnect!";
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Upload email failed: {$mail->ErrorInfo}");
        return false;
    }
}

// =============================================
// 3. PRODUCT UPDATE NOTIFICATION – TO FARMER
// =============================================
function sendProductUpdateNotification($farmer_email, $farmer_name, $product_name, $product_price, $product_category) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = MAIL_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_USERNAME;
        $mail->Password   = MAIL_PASSWORD;
        $mail->SMTPSecure = MAIL_ENCRYPTION;
        $mail->Port       = MAIL_PORT;
        
        $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
        $mail->addAddress($farmer_email, $farmer_name);
        
        $mail->isHTML(true);
        $mail->Subject = 'Your Product Has Been Updated';
        $mail->Body    = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: #2196F3; color: white; padding: 25px; text-align: center; border-radius: 10px 10px 0 0; }
                    .content { background: #f9f9f9; padding: 30px; }
                    .footer { text-align: center; color: #666; margin-top: 20px; font-size: 12px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>✏️ Product Updated</h1>
                    </div>
                    <div class='content'>
                        <p>Dear {$farmer_name},</p>
                        <p>Your product <strong>{$product_name}</strong> has been updated successfully.</p>
                        <ul>
                            <li>New Price: Ksh " . number_format($product_price, 2) . "</li>
                            <li>Category: " . ucfirst($product_category) . "</li>
                        </ul>
                        <p>Changes are now reflected on the marketplace.</p>
                    </div>
                    <div class='footer'>
                        <p>© " . date('Y') . " FarmConnect</p>
                    </div>
                </div>
            </body>
            </html>
        ";
        $mail->AltBody = "Your product '{$product_name}' has been updated. New price: Ksh {$product_price}.";
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Update email failed: {$mail->ErrorInfo}");
        return false;
    }
}

// =============================================
// 4. PRODUCT DELETE NOTIFICATION – TO FARMER
// =============================================
function sendProductDeleteNotification($farmer_email, $farmer_name, $product_name) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = MAIL_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_USERNAME;
        $mail->Password   = MAIL_PASSWORD;
        $mail->SMTPSecure = MAIL_ENCRYPTION;
        $mail->Port       = MAIL_PORT;
        
        $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
        $mail->addAddress($farmer_email, $farmer_name);
        
        $mail->isHTML(true);
        $mail->Subject = 'Your Product Has Been Deleted';
        $mail->Body    = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: #f44336; color: white; padding: 25px; text-align: center; border-radius: 10px 10px 0 0; }
                    .content { background: #f9f9f9; padding: 30px; }
                    .footer { text-align: center; color: #666; margin-top: 20px; font-size: 12px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>🗑️ Product Deleted</h1>
                    </div>
                    <div class='content'>
                        <p>Dear {$farmer_name},</p>
                        <p>Your product <strong>{$product_name}</strong> has been permanently deleted from FarmConnect.</p>
                        <p>If this was a mistake, you can re-upload the product anytime from your dashboard.</p>
                    </div>
                    <div class='footer'>
                        <p>© " . date('Y') . " FarmConnect</p>
                    </div>
                </div>
            </body>
            </html>
        ";
        $mail->AltBody = "Your product '{$product_name}' has been deleted.";
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Delete email failed: {$mail->ErrorInfo}");
        return false;
    }
}

// =============================================
// 5. ORDER CONFIRMATION – TO BUYER
// =============================================
function sendBuyerOrderConfirmation($buyer_email, $buyer_phone, $order_items, $total) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = MAIL_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_USERNAME;
        $mail->Password   = MAIL_PASSWORD;
        $mail->SMTPSecure = MAIL_ENCRYPTION;
        $mail->Port       = MAIL_PORT;
        
        $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
        $mail->addAddress($buyer_email);
        
        // Build items table
        $items_html = '';
        foreach ($order_items as $item) {
            $subtotal = $item['price'] * $item['quantity'];
            $items_html .= "<tr>
                <td>{$item['name']}</td>
                <td>{$item['quantity']}</td>
                <td>Ksh " . number_format($item['price'], 2) . "</td>
                <td>Ksh " . number_format($subtotal, 2) . "</td>
            </tr>";
        }
        
        $mail->isHTML(true);
        $mail->Subject = 'Your FarmConnect Order Confirmation';
        $mail->Body    = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: #2E7D32; color: white; padding: 25px; text-align: center; border-radius: 10px 10px 0 0; }
                    .content { background: #f9f9f9; padding: 30px; }
                    table { width: 100%; border-collapse: collapse; }
                    th, td { padding: 10px; border-bottom: 1px solid #ddd; text-align: left; }
                    .total { font-size: 1.2em; font-weight: bold; text-align: right; margin-top: 20px; }
                    .footer { text-align: center; color: #666; margin-top: 20px; font-size: 12px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>🛒 Order Confirmed!</h1>
                    </div>
                    <div class='content'>
                        <p>Thank you for shopping with FarmConnect!</p>
                        <h3>Order Summary</h3>
                        <table>
                            <thead><tr><th>Product</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr></thead>
                            <tbody>{$items_html}</tbody>
                        </table>
                        <div class='total'>Total: Ksh " . number_format($total, 2) . "</div>
                        <p><strong>Delivery Phone:</strong> {$buyer_phone}</p>
                        <p>You will receive an M-Pesa prompt shortly.</p>
                    </div>
                    <div class='footer'>
                        <p>© " . date('Y') . " FarmConnect</p>
                    </div>
                </div>
            </body>
            </html>
        ";
        $mail->AltBody = "Order Confirmed! Total: Ksh {$total}. Thank you for shopping with FarmConnect.";
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Buyer email failed: {$mail->ErrorInfo}");
        return false;
    }
}

// =============================================
// 6. NEW ORDER ALERT – TO FARMER
// =============================================
function sendFarmerOrderAlert($farmer_email, $farmer_name, $product_name, $quantity, $buyer_phone) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = MAIL_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_USERNAME;
        $mail->Password   = MAIL_PASSWORD;
        $mail->SMTPSecure = MAIL_ENCRYPTION;
        $mail->Port       = MAIL_PORT;
        
        $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
        $mail->addAddress($farmer_email, $farmer_name);
        
        $mail->isHTML(true);
        $mail->Subject = "📦 New Order: {$product_name}";
        $mail->Body    = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: #ff8f00; color: #1b5e20; padding: 25px; text-align: center; border-radius: 10px 10px 0 0; }
                    .content { background: #f9f9f9; padding: 30px; }
                    .footer { text-align: center; color: #666; margin-top: 20px; font-size: 12px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>🎉 New Order Received!</h1>
                    </div>
                    <div class='content'>
                        <p>Hello {$farmer_name},</p>
                        <p>Someone just ordered your product:</p>
                        <h2>{$product_name}</h2>
                        <ul>
                            <li><strong>Quantity:</strong> {$quantity}</li>
                            <li><strong>Buyer Phone:</strong> {$buyer_phone}</li>
                        </ul>
                        <p>Please prepare the product for delivery.</p>
                    </div>
                    <div class='footer'>
                        <p>© " . date('Y') . " FarmConnect</p>
                    </div>
                </div>
            </body>
            </html>
        ";
        $mail->AltBody = "New order for {$product_name} (Qty: {$quantity}). Buyer phone: {$buyer_phone}";
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Farmer alert email failed: {$mail->ErrorInfo}");
        return false;
    }
}

// =============================================
// 7. OUT OF STOCK ALERT – TO FARMER
// =============================================
function sendOutOfStockNotification($farmer_email, $farmer_name, $product_name, $product_id) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = MAIL_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = MAIL_USERNAME;
        $mail->Password   = MAIL_PASSWORD;
        $mail->SMTPSecure = MAIL_ENCRYPTION;
        $mail->Port       = MAIL_PORT;
        
        $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
        $mail->addAddress($farmer_email, $farmer_name);
        
        $mail->isHTML(true);
        $mail->Subject = "⚠️ Out of Stock Alert: {$product_name}";
        $mail->Body    = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: #f44336; color: white; padding: 25px; text-align: center; border-radius: 10px 10px 0 0; }
                    .content { background: #f9f9f9; padding: 30px; }
                    .button { display: inline-block; padding: 12px 30px; background: #2E7D32; color: white; text-decoration: none; border-radius: 50px; font-weight: bold; margin: 20px 0; }
                    .footer { text-align: center; color: #666; margin-top: 20px; font-size: 12px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h1>⚠️ Out of Stock</h1>
                    </div>
                    <div class='content'>
                        <p>Dear {$farmer_name},</p>
                        <p>Your product <strong>{$product_name}</strong> has just sold out and is now out of stock.</p>
                        <p>Customers can no longer purchase this item until you restock.</p>
                        <div style='text-align:center;'>
                            <a href='http://localhost/Farmconnect/dashboard/farmer_dashboard.php' class='button'>Update Stock →</a>
                        </div>
                        <p>Log in to your dashboard to update the stock quantity.</p>
                    </div>
                    <div class='footer'>
                        <p>© " . date('Y') . " FarmConnect</p>
                    </div>
                </div>
            </body>
            </html>
        ";
        $mail->AltBody = "Your product '{$product_name}' is out of stock. Please restock soon.";
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Out of stock email failed: {$mail->ErrorInfo}");
        return false;
    }
}
?>