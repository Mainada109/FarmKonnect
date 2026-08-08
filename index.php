<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FarmConnect - Fresh from Farm to Table</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div id="loader">
    <div class="loader-content">
        <div class="loader-spinner"></div>
        <p>🌾 FarmConnect 🌾</p>
        <p style="font-size:14px;margin-top:10px;">Connecting Farmers to You</p>
    </div>
</div>

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

<section class="hero">
    <div class="hero-content">
        <h1>Fresh from Farm<br>to Your Table</h1>
        <p>Connect directly with local farmers. Fresh produce, fair prices, no middlemen.</p>
        <div class="search-box" style="margin:20px auto; display:flex; justify-content:center; gap:10px; max-width:500px;">
            <input type="text" id="searchInput" placeholder="Search fresh produce..." onkeyup="searchProducts()" style="flex:1; padding:14px; border-radius:50px; border:none;">
            <button onclick="searchProducts()" style="background: var(--accent); border:none; border-radius:50px; padding:14px 20px; cursor:pointer;">🔍 Search</button>
        </div>
        <div class="hero-buttons">
            <button class="btn btn-primary" onclick="scrollToProducts()">Shop Now</button>
            <button class="btn btn-outline" onclick="location.href='register.php'">Join as Farmer</button>
        </div>
    </div>
</section>

<?php
include('backend/db.php');

// -------------------- DEAL OF THE WEEK --------------------
$dealQuery = mysqli_query($conn, "SELECT * FROM products WHERE stock > 0 ORDER BY RAND() LIMIT 1");
$dealProduct = mysqli_fetch_assoc($dealQuery);
$dealDiscount = 30; // 30% off

// Set fallback values if no product exists
if ($dealProduct) {
    $dealName = $dealProduct['name'];
    $dealPrice = $dealProduct['price'] - ($dealProduct['price'] * $dealDiscount / 100);
    $dealOriginalPrice = $dealProduct['price'];
    $dealImage = $dealProduct['image'];
} else {
    // Dummy data for when the database is empty
    $dealName = "Fresh Organic Maize";
    $dealPrice = 350;
    $dealOriginalPrice = 500;
    $dealImage = "placeholder.jpg";
}

// -------------------- KENYAN HOLIDAY DEALS --------------------
$holidays = [
    '01-01' => ['name' => 'New Year\'s Day', 'icon' => '🎉', 'discount' => 20],
    '05-01' => ['name' => 'Labour Day', 'icon' => '👷', 'discount' => 10],
    '06-01' => ['name' => 'Madaraka Day', 'icon' => '🇰🇪', 'discount' => 25],
    '10-20' => ['name' => 'Mashujaa Day', 'icon' => '🦁', 'discount' => 30],
    '12-12' => ['name' => 'Jamhuri Day', 'icon' => '🎊', 'discount' => 35],
    '12-25' => ['name' => 'Christmas', 'icon' => '🎄', 'discount' => 40],
];

$today = date('m-d');
$currentHoliday = $holidays[$today] ?? null;
?>

<!-- DEAL OF THE WEEK -->
<section class="deal-week">
    <div class="deal-container">
        <div class="deal-info">
            <div class="deal-badge">🌟 DEAL OF THE WEEK</div>
            <h2 class="deal-title"><?php echo htmlspecialchars($dealName); ?></h2>
            <p>Get fresh farm produce directly from farmers. Limited time offer!</p>
            <div class="deal-price">
                Ksh <?php echo number_format($dealPrice, 0); ?>
                <span class="deal-old-price">Ksh <?php echo number_format($dealOriginalPrice, 0); ?></span>
            </div>
            <div class="deal-timer" id="dealTimer">
                <div class="timer-box"><div class="timer-number" id="days">0</div><div class="timer-label">Days</div></div>
                <div class="timer-box"><div class="timer-number" id="hours">0</div><div class="timer-label">Hours</div></div>
                <div class="timer-box"><div class="timer-number" id="minutes">0</div><div class="timer-label">Mins</div></div>
                <div class="timer-box"><div class="timer-number" id="seconds">0</div><div class="timer-label">Secs</div></div>
            </div>
            <button class="btn btn-primary" onclick="location.href='shop.php'">Shop Now →</button>
        </div>
        <div class="deal-image">
            <img src="imgs/<?php echo htmlspecialchars($dealImage); ?>" alt="Deal Product" onerror="this.src='assets/images/placeholder.jpg'">
        </div>
    </div>
</section>

<!-- AI HOLIDAY DEALS -->
<?php if ($currentHoliday): ?>
<section class="holiday-deals">
    <div class="holiday-banner">
        <div class="holiday-icon"><?php echo $currentHoliday['icon']; ?></div>
        <h2 class="holiday-title"><?php echo $currentHoliday['name']; ?> Special!</h2>
        <p>🎉 Get <?php echo $currentHoliday['discount']; ?>% OFF on all products today!</p>
        <button class="btn btn-primary" onclick="location.href='shop.php'">Claim Discount →</button>
    </div>
</section>
<?php endif; ?>

<!-- FEATURES -->
<section class="features">
    <h2 class="section-title">Why Choose FarmConnect?</h2>
    <div class="features-grid">
        <div class="feature-card"><div class="feature-icon">🌾</div><h3>Direct from Farmers</h3><p>No middlemen, better prices</p></div>
        <div class="feature-card"><div class="feature-icon">💳</div><h3>Secure Payments</h3><p>Pay with M-Pesa</p></div>
        <div class="feature-card"><div class="feature-icon">🚚</div><h3>Fast Delivery</h3><p>Fresh to your doorstep</p></div>
        <div class="feature-card"><div class="feature-icon">🤖</div><h3>AI-Powered Deals</h3><p>Smart holiday discounts</p></div>
    </div>
</section>

<!-- COUNTERS -->
<section class="counters">
    <div class="counters-grid">
        <div class="counter-item"><h3 id="farmersCount">0</h3><p>Active Farmers</p></div>
        <div class="counter-item"><h3 id="productsCount">0</h3><p>Products Listed</p></div>
        <div class="counter-item"><h3 id="ordersCount">0</h3><p>Orders Delivered</p></div>
    </div>
</section>

<!-- PRODUCTS -->
<section class="products" id="productsSection">
    <h2 class="section-title">Popular Products</h2>
    <div class="products-grid" id="productsGrid"></div>
</section>

<footer class="footer">
    <div class="footer-grid">
        <div class="footer-col"><h4>🌾 FarmConnect</h4><p>Connecting farmers to consumers</p></div>
        <div class="footer-col"><h4>Quick Links</h4><p><a href="shop.php" style="color:white;">Shop</a></p><p><a href="register.php" style="color:white;">Become a Farmer</a></p></div>
        <div class="footer-col"><h4>Contact</h4><p>📞 +254 700 000 000</p><p>📧 info@farmconnect.co.ke</p></div>
    </div>
    <div class="footer-bottom"><p>&copy; 2026 FarmConnect. All rights reserved.</p></div>
</footer>

<div class="whatsapp-float" onclick="contactWhatsApp()">💬</div>

<script src="assets/js/main.js"></script>
</body>
</html>


