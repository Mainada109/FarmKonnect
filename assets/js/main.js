// ========== LOADER ==========
window.addEventListener('load', () => {
    const loader = document.getElementById('loader');
    if (loader) {
        setTimeout(() => {
            loader.style.opacity = '0';
            setTimeout(() => loader.remove(), 500);
        }, 1000);
    }
});

// ========== DARK MODE ==========
function toggleDarkMode() {
    document.body.classList.toggle('dark');
    localStorage.setItem('darkMode', document.body.classList.contains('dark'));
}

if (localStorage.getItem('darkMode') === 'true') {
    document.body.classList.add('dark');
}

// ========== NAVBAR SCROLL ==========
window.addEventListener('scroll', () => {
    const navbar = document.querySelector('.navbar');
    if (navbar && window.scrollY > 50) {
        navbar.classList.add('scrolled');
    } else if (navbar) {
        navbar.classList.remove('scrolled');
    }
});

// ========== TOAST ==========
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.innerHTML = `${type === 'success' ? '✅' : '⚠️'} ${message}`;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.style.animation = 'slideInRight 0.3s reverse';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// ========== CART FUNCTIONS ==========
function addToCart(productId) {
    fetch('backend/cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `product_id=${productId}`
    })
    .then(() => {
        showToast('Added to cart! 🛒');
        updateCartCount();
    })
    .catch(() => showToast('Error adding to cart', 'error'));
}

function buyNow(productId) {
    const phone = prompt("Enter your M-Pesa phone number (2547XXXXXXXX):");
    if (!phone || phone.length < 10) {
        showToast('Valid phone number required', 'error');
        return;
    }
    const email = prompt("Enter your email address for receipt:");
    if (!email || !email.includes('@')) {
        showToast('Valid email required', 'error');
        return;
    }
    const deliveryAddress = prompt("Enter your delivery address:");
    if (!deliveryAddress || deliveryAddress.trim().length < 5) {
        showToast('Valid delivery address required', 'error');
        return;
    }
    
    fetch('backend/order.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `product_id=${productId}&quantity=1&phone=${encodeURIComponent(phone)}&email=${encodeURIComponent(email)}&delivery_address=${encodeURIComponent(deliveryAddress)}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            showToast(`STK Push sent to ${phone}. Check your phone.`, 'success');
        } else {
            showToast(`Order placed. Total: Ksh ${data.total}`, 'success');
        }
        updateCartCount();
        setTimeout(() => window.location.reload(), 2000);
    })
    .catch(() => showToast('Order failed', 'error'));
}

function updateCartCount() {
    fetch('backend/get_cart.php')
        .then(res => res.json())
        .then(data => {
            document.querySelectorAll('.cart-count').forEach(el => {
                el.textContent = data.length;
            });
        })
        .catch(() => {});
}

// ========== PRODUCT DISPLAY ==========
let allProducts = [];

function loadProducts(products, containerId = 'productsGrid') {
    const container = document.getElementById(containerId);
    if (!container) return;
    
    container.innerHTML = '';
    
    products.forEach(product => {
        const stock = parseInt(product.stock) || 0;
        let stockClass, stockText;
        let isOutOfStock = false;
        
        if (stock <= 0) {
            stockClass = 'out';
            stockText = '❌ Out of Stock';
            isOutOfStock = true;
        } else if (stock < 10) {
            stockClass = 'low';
            stockText = `🔥 Only ${stock} left!`;
        } else {
            stockClass = 'high';
            stockText = `✅ ${stock} in stock`;
        }
        
        const unit = product.unit || 'kg';
        const description = product.description ? product.description.substring(0, 50) + (product.description.length > 50 ? '...' : '') : '';
        
        const actionButtons = isOutOfStock ? 
            `<button class="btn-cart" disabled style="opacity:0.5; cursor:not-allowed; background:#999;">Out of Stock</button>` :
            `<button class="btn-cart" onclick="addToCart(${product.id})">🛒 Add to Cart</button>
             <button class="btn-buy" onclick="buyNow(${product.id})">⚡ Buy Now</button>`;
        
        container.innerHTML += `
            <div class="product-card fade-up">
                ${stock < 10 && stock > 0 ? '<div class="product-badge">🔥 Low Stock</div>' : ''}
                ${stock <= 0 ? '<div class="product-badge" style="background:#f44336;">❌ Out of Stock</div>' : ''}
                <img src="imgs/${product.image}" class="product-image" alt="${product.name}" onerror="this.src='assets/images/placeholder.jpg'">
                <div class="product-info">
                    <h3>${product.name}</h3>
                    <div class="product-price">Ksh ${product.price} / ${unit}</div>
                    <div class="product-stock ${stockClass}">${stockText}</div>
                    ${description ? `<p style="font-size:0.9rem; color: var(--text-light); margin:5px 0;">📝 ${description}</p>` : ''}
                    <div class="product-actions">
                        ${actionButtons}
                    </div>
                </div>
            </div>
        `;
    });
}

function searchProducts() {
    const query = document.getElementById('searchInput')?.value.toLowerCase() || '';
    const filtered = allProducts.filter(p => p.name.toLowerCase().includes(query));
    loadProducts(filtered);
}

fetch('backend/get_products.php')
    .then(res => res.json())
    .then(data => {
        allProducts = data;
        loadProducts(data);
        updateCartCount();
    });

// ========== COUNTER ANIMATION ==========
function animateCounter(elementId, target) {
    let current = 0;
    const increment = Math.ceil(target / 50);
    const interval = setInterval(() => {
        current += increment;
        if (current >= target) {
            current = target;
            clearInterval(interval);
        }
        const el = document.getElementById(elementId);
        if (el) el.textContent = current;
    }, 30);
}

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            animateCounter('farmersCount', 150);
            animateCounter('productsCount', 500);
            animateCounter('ordersCount', 1200);
            observer.disconnect();
        }
    });
});

const countersSection = document.querySelector('.counters');
if (countersSection) observer.observe(countersSection);

// ========== LIVE ACTIVITY ==========
const buyerNames = ['John Mwangi', 'Mary Wanjiku', 'Peter Omondi', 'Faith Achieng', 'James Kamau'];
const productNames = ['Fresh Maize', 'Organic Tomatoes', 'Farm Milk', 'Green Veggies', 'Free Eggs'];

setInterval(() => {
    const name = buyerNames[Math.floor(Math.random() * buyerNames.length)];
    const product = productNames[Math.floor(Math.random() * productNames.length)];
    
    const popup = document.createElement('div');
    popup.className = 'activity-popup';
    popup.innerHTML = `🛍️ ${name} just bought ${product}`;
    document.body.appendChild(popup);
    setTimeout(() => popup.remove(), 4000);
}, 10000);

// ========== WHATSAPP ==========
function contactWhatsApp() {
    window.open('https://wa.me/2547XXXXXXXX?text=Hello%20FarmConnect%2C%20I%20want%20to%20buy%20farm%20products', '_blank');
}

function scrollToProducts() {
    document.getElementById('productsSection')?.scrollIntoView({ behavior: 'smooth' });
}

// ========== DEAL TIMER ==========
function updateDealTimer() {
    const timerElement = document.getElementById('dealTimer');
    if (!timerElement) return;
    
    const targetDate = new Date();
    targetDate.setDate(targetDate.getDate() + 7);
    targetDate.setHours(23, 59, 59, 999);
    
    function update() {
        const now = new Date();
        const diff = targetDate - now;
        
        if (diff <= 0) {
            document.getElementById('dealTimer').innerHTML = 'Deal Expired!';
            return;
        }
        
        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
        const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((diff % (1000 * 60)) / 1000);
        
        document.getElementById('days').textContent = days;
        document.getElementById('hours').textContent = hours;
        document.getElementById('minutes').textContent = minutes;
        document.getElementById('seconds').textContent = seconds;
    }
    
    update();
    setInterval(update, 1000);
}

if (document.getElementById('dealTimer')) updateDealTimer();

// ========== CHECKOUT (UPDATED WITH DELIVERY ADDRESS) ==========
function proceedToPayment() {
    const phone = document.getElementById('phone')?.value;
    const email = document.getElementById('email')?.value;
    const deliveryAddress = document.getElementById('deliveryAddress')?.value;
    const messageDiv = document.getElementById('checkoutMessage');
    
    if (!phone || phone.length < 10) {
        if (messageDiv) messageDiv.innerHTML = '<span style="color:red;">Enter valid phone number (2547XXXXXXXX)</span>';
        showToast('Enter valid phone number', 'error');
        return;
    }
    if (!email || !email.includes('@')) {
        if (messageDiv) messageDiv.innerHTML = '<span style="color:red;">Enter a valid email address</span>';
        showToast('Enter valid email', 'error');
        return;
    }
    if (!deliveryAddress || deliveryAddress.trim().length < 5) {
        if (messageDiv) messageDiv.innerHTML = '<span style="color:red;">Please enter your delivery address</span>';
        showToast('Enter delivery address', 'error');
        return;
    }
    
    if (messageDiv) messageDiv.innerHTML = 'Processing payment... Please wait.';
    
    fetch('backend/checkout.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `phone=${encodeURIComponent(phone)}&email=${encodeURIComponent(email)}&delivery_address=${encodeURIComponent(deliveryAddress)}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            if (messageDiv) messageDiv.innerHTML = '<span style="color:green;">✅ STK Push sent! Check your phone and enter PIN.</span>';
            showToast(`STK Push sent to ${phone}`, 'success');
        } else {
            if (messageDiv) messageDiv.innerHTML = `<span style="color:red;">❌ M-Pesa Error: ${data.message}</span>`;
            showToast(`Error: ${data.message}`, 'error');
        }
        updateCartCount();
        setTimeout(() => window.location.href = 'shop.php', 5000);
    })
    .catch(err => {
        if (messageDiv) messageDiv.innerHTML = '<span style="color:red;">Payment request failed. Try again.</span>';
        showToast('Payment request failed', 'error');
    });
}

// ========== CART PAGE SPECIFIC ==========
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
    
    if (!container) return;
    
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
                    <img src="${imageUrl}" alt="${item.name}" onerror="this.src='assets/images/placeholder.jpg'" style="width:80px; height:80px; object-fit:cover; border-radius:12px;">
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
    fetch('backend/clear_cart.php').then(() => loadCart());
}

if (document.getElementById('cartItems')) {
    loadCart();
}