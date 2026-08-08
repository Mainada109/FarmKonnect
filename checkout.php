<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - FarmConnect</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .checkout-container {
            max-width: 550px;
            margin: 100px auto 40px;
            background: var(--white);
            padding: 40px;
            border-radius: 20px;
            text-align: center;
            box-shadow: var(--shadow-lg);
        }
        .payment-method {
            background: #25D36610;
            border: 2px solid #25D366;
            border-radius: 15px;
            padding: 20px;
            margin: 20px 0;
        }
        .phone-input, .email-input, .address-input {
            width: 100%;
            padding: 15px;
            margin: 15px 0;
            border: 2px solid #ddd;
            border-radius: 10px;
            font-size: 16px;
            font-family: inherit;
        }
        .address-input {
            resize: vertical;
        }
        .pay-btn {
            background: var(--primary);
            color: white;
            border: none;
            padding: 15px;
            border-radius: 10px;
            font-size: 18px;
            cursor: pointer;
            width: 100%;
            font-weight: 600;
        }
        .secure-badge {
            margin-top: 20px;
            color: var(--text-light);
            font-size: 14px;
        }
        #checkoutMessage {
            margin-top: 15px;
            font-weight: 500;
        }
        h3 {
            margin-top: 20px;
            margin-bottom: 5px;
            color: var(--primary);
        }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="logo" onclick="location.href='index.php'">
        <img src="assets/images/logo.png" alt="FarmConnect">
        <span>FarmConnect</span>
    </div>
    <div class="nav-links">
        <a href="index.php">Home</a>
        <a href="shop.php">Shop</a>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
        <a href="cart.php" class="cart-icon">🛒 <span class="cart-count">0</span></a>
        <button class="dark-toggle" onclick="toggleDarkMode()">🌙</button>
    </div>
</nav>

<div class="checkout-container">
    <h2>💳 Checkout</h2>
    <div class="payment-method">
        <p>📱 <strong>M-Pesa Payment</strong></p>
        <p>You will receive an STK Push on your phone</p>
    </div>
    <input type="tel" id="phone" class="phone-input" placeholder="Enter M-Pesa Number (2547XXXXXXXX)" required>
    <input type="email" id="email" class="email-input" placeholder="Email Address (for receipt)" required>
    
    <h3>🚚 Delivery Address</h3>
    <textarea id="deliveryAddress" class="address-input" rows="3" placeholder="Enter your full delivery address (e.g., House No., Street, Estate, Town)" required></textarea>
    
    <button class="pay-btn" onclick="proceedToPayment()">Pay with M-Pesa 💰</button>
    <div id="checkoutMessage"></div>
    <div class="secure-badge">🔒 Secure Payment | 📱 STK Push | 🚚 Free Delivery</div>
</div>

<script src="assets/js/main.js"></script>
</body>
</html>