<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart - FarmConnect</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* ========== GLOBAL STYLES ========== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            scroll-behavior: smooth;
        }
        :root {
            --primary: #1b5e20;
            --primary-dark: #0a3d0a;
            --primary-light: #4caf50;
            --accent: #ff8f00;
            --danger: #f44336;
            --dark: #1a1a2e;
            --gray: #f8f9fa;
            --text-dark: #2c3e50;
            --text-light: #6c757d;
            --white: #ffffff;
            --shadow-sm: 0 2px 8px rgba(0,0,0,0.05);
            --shadow-md: 0 8px 20px rgba(0,0,0,0.08);
            --shadow-lg: 0 12px 30px rgba(0,0,0,0.12);
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: var(--gray);
            color: var(--text-dark);
            overflow-x: hidden;
        }
        body.dark {
            --gray: #121212;
            --white: #1e1e2e;
            --text-dark: #f0f0f0;
            --text-light: #b0b0b0;
        }

        /* ========== NAVBAR ========== */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 40px;
            background: rgba(27, 94, 32, 0.85);
            backdrop-filter: blur(12px);
            z-index: 1000;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
        }
        .logo img {
            height: 45px;
        }
        .logo span {
            font-size: 26px;
            font-weight: 700;
            color: white;
        }
        .nav-links {
            display: flex;
            gap: 30px;
            align-items: center;
        }
        .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            font-size: 1.05rem;
        }
        .nav-links a:hover {
            color: var(--accent);
        }
        .cart-icon {
            position: relative;
        }
        .cart-count {
            position: absolute;
            top: -8px;
            right: -12px;
            background: var(--accent);
            color: var(--primary-dark);
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
            font-weight: bold;
        }
        .dark-toggle {
            background: none;
            border: none;
            color: white;
            font-size: 22px;
            cursor: pointer;
        }

        /* ========== CART CONTAINER ========== */
        .cart-container {
            max-width: 1000px;
            margin: 120px auto 40px;
            padding: 20px;
        }
        .cart-container h2 {
            margin-bottom: 30px;
            color: var(--primary);
        }
        .cart-item {
            background: var(--white);
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
            box-shadow: var(--shadow-sm);
        }
        .cart-item-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .cart-item-info img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 12px;
        }
        .cart-item-details h3 {
            margin-bottom: 5px;
        }
        .cart-item-price {
            color: var(--primary);
            font-weight: 600;
        }
        .item-quantity {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .qty-btn {
            background: var(--gray);
            border: 1px solid #ddd;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 18px;
            font-weight: 600;
            transition: all 0.2s;
        }
        .qty-btn:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }
        .item-subtotal {
            font-weight: 700;
            color: var(--primary);
            min-width: 100px;
            text-align: right;
        }
        .btn-remove {
            background: var(--danger);
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 30px;
            cursor: pointer;
            font-weight: 500;
            transition: background 0.2s;
        }
        .btn-remove:hover {
            background: #d32f2f;
        }
        .cart-total {
            background: var(--white);
            border-radius: 20px;
            padding: 25px;
            margin-top: 20px;
            box-shadow: var(--shadow-sm);
        }
        .cart-summary h3 {
            margin-bottom: 20px;
            color: var(--primary);
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
        }
        .summary-total {
            display: flex;
            justify-content: space-between;
            padding: 20px 0;
            margin-top: 10px;
            border-top: 2px solid var(--primary);
            font-size: 24px;
            font-weight: 700;
        }
        .empty-cart {
            text-align: center;
            padding: 60px;
            background: var(--white);
            border-radius: 20px;
        }
        .empty-cart a {
            color: var(--primary);
            font-weight: 600;
        }
        .cart-actions {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        .btn-clear {
            background: #ff9800;
            color: white;
            border: none;
            padding: 14px 28px;
            border-radius: 40px;
            cursor: pointer;
            font-weight: 600;
        }
        .btn-checkout {
            background: var(--primary);
            color: white;
            border: none;
            padding: 14px 28px;
            border-radius: 40px;
            cursor: pointer;
            font-weight: 600;
            flex: 1;
        }
        .btn-checkout:hover {
            background: var(--primary-dark);
        }

        /* ========== FOOTER ========== */
        .footer {
            background: var(--dark);
            color: white;
            padding: 60px 40px 20px;
            margin-top: 60px;
        }
        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 40px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .footer a {
            color: #ddd;
            text-decoration: none;
        }
        .footer-bottom {
            text-align: center;
            padding-top: 40px;
            margin-top: 40px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }

        /* ========== WHATSAPP FLOAT ========== */
        .whatsapp-float {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: #25D366;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: var(--shadow-lg);
            font-size: 30px;
            z-index: 999;
        }

        /* ========== TOAST ========== */
        .toast {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: var(--primary);
            color: white;
            padding: 12px 24px;
            border-radius: 50px;
            z-index: 2000;
            animation: slideInRight 0.3s;
        }
        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(50px); }
            to { opacity: 1; transform: translateX(0); }
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

<div class="cart-container">
    <h2>🛒 Your Shopping Cart</h2>
    <div id="cartItems"></div>
    <div id="cartTotal" class="cart-total"></div>
    <div class="cart-actions">
        <button class="btn-clear" onclick="clearCart()">Clear Cart</button>
        <button class="btn-checkout" onclick="proceedToCheckout()">Proceed to Checkout →</button>
    </div>
</div>

<footer class="footer">
    <div class="footer-grid">
        <div class="footer-col"><h4>🌾 FarmConnect</h4><p>Connecting farmers to consumers</p></div>
        <div class="footer-col"><h4>Quick Links</h4><p><a href="shop.php">Continue Shopping</a></p></div>
        <div class="footer-col"><h4>Contact</h4><p>📞 +254 700 000 000</p></div>
    </div>
    <div class="footer-bottom"><p>&copy; 2026 FarmConnect</p></div>
</footer>

<div class="whatsapp-float" onclick="contactWhatsApp()">💬</div>

<script>
    // ========== DARK MODE ==========
    function toggleDarkMode() {
        document.body.classList.toggle('dark');
        localStorage.setItem('darkMode', document.body.classList.contains('dark'));
    }
    if (localStorage.getItem('darkMode') === 'true') document.body.classList.add('dark');

    // ========== TOAST ==========
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.innerHTML = `${type === 'success' ? '✅' : '⚠️'} ${message}`;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }

    // ========== CART FUNCTIONS ==========
    let cartItems = [];

    function loadCart() {
        fetch('backend/get_cart.php')
            .then(res => res.json())
            .then(data => {
                cartItems = data;
                displayCart();
                updateCartCount();
            });
    }

    function displayCart() {
        const container = document.getElementById('cartItems');
        const totalContainer = document.getElementById('cartTotal');

        if (cartItems.length === 0) {
            container.innerHTML = '<div class="empty-cart">🛒 Your cart is empty. <a href="shop.php">Start shopping</a></div>';
            totalContainer.innerHTML = '';
            return;
        }

        let subtotal = 0;
        container.innerHTML = '';

        cartItems.forEach(item => {
            const itemTotal = item.price * (item.quantity || 1);
            subtotal += itemTotal;
            const imageUrl = item.image ? `imgs/${item.image}` : 'assets/images/placeholder.jpg';

            container.innerHTML += `
                <div class="cart-item">
                    <div class="cart-item-info">
                        <img src="${imageUrl}" alt="${item.name}" onerror="this.src='assets/images/placeholder.jpg'">
                        <div class="cart-item-details">
                            <h3>${item.name}</h3>
                            <p class="cart-item-price">Ksh ${item.price} each</p>
                        </div>
                    </div>
                    <div class="item-quantity">
                        <button class="qty-btn" onclick="updateQuantity(${item.id}, ${(item.quantity || 1) - 1})">−</button>
                        <span style="min-width:30px; text-align:center;">${item.quantity || 1}</span>
                        <button class="qty-btn" onclick="updateQuantity(${item.id}, ${(item.quantity || 1) + 1})">+</button>
                    </div>
                    <div class="item-subtotal">Ksh ${itemTotal}</div>
                    <button class="btn-remove" onclick="removeFromCart(${item.id})">Remove</button>
                </div>
            `;
        });

        const deliveryFee = subtotal > 500 ? 0 : 100;
        const total = subtotal + deliveryFee;

        totalContainer.innerHTML = `
            <div class="cart-summary">
                <h3>Order Summary</h3>
                <div class="summary-row"><span>Subtotal</span><span>Ksh ${subtotal}</span></div>
                <div class="summary-row"><span>Delivery Fee</span><span>Ksh ${deliveryFee}</span></div>
                <div class="summary-total"><span>Total</span><span>Ksh ${total}</span></div>
            </div>
        `;
    }

    function updateQuantity(id, newQuantity) {
        if (newQuantity < 1) {
            removeFromCart(id);
            return;
        }
        fetch('backend/update_cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${id}&quantity=${newQuantity}`
        }).then(() => loadCart());
    }

    function removeFromCart(id) {
        fetch('backend/remove_cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${id}`
        }).then(() => {
            showToast('Item removed', 'success');
            loadCart();
        });
    }

    function clearCart() {
        if (!confirm('Clear entire cart?')) return;
        fetch('backend/clear_cart.php').then(() => {
            showToast('Cart cleared', 'success');
            loadCart();
        });
    }

    function proceedToCheckout() {
        if (cartItems.length === 0) {
            showToast('Cart is empty', 'error');
            return;
        }
        window.location.href = 'checkout.php';
    }

    function updateCartCount() {
        fetch('backend/get_cart.php')
            .then(res => res.json())
            .then(data => {
                document.querySelectorAll('.cart-count').forEach(el => el.textContent = data.length);
            });
    }

    function contactWhatsApp() {
        window.open('https://wa.me/254700000000?text=Hello%20FarmConnect', '_blank');
    }

    // Initial load
    loadCart();
</script>
</body>
</html>