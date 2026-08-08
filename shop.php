<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - FarmConnect</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .filter-section {
            max-width: 1200px;
            margin: 100px auto 20px;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            background: var(--white);
            border-radius: 15px;
        }
        .filter-buttons { display: flex; gap: 10px; flex-wrap: wrap; }
        .filter-btn {
            padding: 10px 20px;
            border: 2px solid var(--primary);
            background: transparent;
            border-radius: 50px;
            cursor: pointer;
            font-weight: 500;
        }
        .filter-btn.active, .filter-btn:hover { background: var(--primary); color: white; }
        .sort-select {
            padding: 10px 20px;
            border: 2px solid var(--primary);
            border-radius: 50px;
            background: transparent;
            cursor: pointer;
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

<div class="filter-section">
    <div class="filter-buttons">
        <button class="filter-btn active" data-category="all">All Products</button>
        <button class="filter-btn" data-category="vegetables">🥬 Vegetables</button>
        <button class="filter-btn" data-category="fruits">🍎 Fruits</button>
        <button class="filter-btn" data-category="dairy">🥛 Dairy</button>
        <button class="filter-btn" data-category="grains">🌾 Grains</button>
    </div>
    <div style="display: flex; gap: 10px;">
        <input type="text" id="shopSearch" placeholder="Search..." style="padding:10px 20px; border-radius:50px; border:2px solid var(--primary);">
        <button onclick="searchShopProducts()" style="padding:10px 20px; background:var(--primary); color:white; border:none; border-radius:50px;">🔍</button>
    </div>
    <select id="sortSelect" class="sort-select" onchange="sortProducts()">
        <option value="default">Sort by</option>
        <option value="price_asc">Price: Low to High</option>
        <option value="price_desc">Price: High to Low</option>
        <option value="name_asc">Name: A to Z</option>
    </select>
</div>

<section class="products" style="padding-top:0;">
    <div class="products-grid" id="productsGrid"></div>
</section>

<footer class="footer">
    <div class="footer-grid">
        <div class="footer-col"><h4>🌾 FarmConnect</h4><p>Connecting farmers to consumers</p></div>
        <div class="footer-col"><h4>Quick Links</h4><p><a href="shop.php" style="color:white;">Shop</a></p></div>
        <div class="footer-col"><h4>Contact</h4><p>📞 +254 700 000 000</p></div>
    </div>
    <div class="footer-bottom"><p>&copy; 2026 FarmConnect</p></div>
</footer>

<div class="whatsapp-float" onclick="contactWhatsApp()">💬</div>

<script src="assets/js/main.js"></script>
<script>
    let shopProducts = [];
    let currentCategory = 'all';
    let currentSort = 'default';
    let currentSearch = '';
    
    function loadShopProducts() {
        fetch('backend/get_products.php')
            .then(res => res.json())
            .then(data => {
                shopProducts = data;
                applyFilters();
                updateCartCount();
            });
    }
    
    function applyFilters() {
        let filtered = [...shopProducts];
        if (currentCategory !== 'all') filtered = filtered.filter(p => p.category === currentCategory);
        if (currentSearch) filtered = filtered.filter(p => p.name.toLowerCase().includes(currentSearch.toLowerCase()));
        if (currentSort === 'price_asc') filtered.sort((a,b) => a.price - b.price);
        else if (currentSort === 'price_desc') filtered.sort((a,b) => b.price - a.price);
        else if (currentSort === 'name_asc') filtered.sort((a,b) => a.name.localeCompare(b.name));
        displayShopProducts(filtered);
    }
    
    function displayShopProducts(products) {
        const container = document.getElementById('productsGrid');
        if (!container) return;
        container.innerHTML = '';
        if (products.length === 0) { container.innerHTML = '<div style="text-align:center;padding:60px;">No products found</div>'; return; }
        
        products.forEach(p => {
            container.innerHTML += `
                <div class="product-card fade-up">
                    ${p.stock < 10 ? '<div class="product-badge">🔥 Low Stock</div>' : ''}
                    <img src="imgs/${p.image}" class="product-image" onerror="this.src='assets/images/placeholder.jpg'">
                    <div class="product-info">
                        <h3>${p.name}</h3>
                        <div class="product-price">Ksh ${p.price}</div>
                        <div class="product-stock ${p.stock < 10 ? 'low' : 'high'}">${p.stock < 10 ? `🔥 Only ${p.stock} left!` : `✅ ${p.stock} in stock`}</div>
                        <div class="product-actions">
                            <button class="btn-cart" onclick="addToCart(${p.id})">🛒 Add to Cart</button>
                            <button class="btn-buy" onclick="buyNow(${p.id})">⚡ Buy Now</button>
                        </div>
                    </div>
                </div>
            `;
        });
    }
    
    function searchShopProducts() { currentSearch = document.getElementById('shopSearch').value; applyFilters(); }
    function sortProducts() { currentSort = document.getElementById('sortSelect').value; applyFilters(); }
    
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentCategory = this.dataset.category;
            applyFilters();
        });
    });
    
    loadShopProducts();
</script>
</body>
</html>