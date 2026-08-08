<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}
include("../backend/db.php");

$user = $_SESSION['user'];
$farmer_id = $user['id'];

$products = mysqli_query($conn, "SELECT * FROM products WHERE farmer_id = $farmer_id ORDER BY id DESC");

// Fetch orders for delivery management
$order_query = "SELECT o.*, p.name as product_name, p.image 
                FROM orders o 
                JOIN products p ON o.product_id = p.id 
                WHERE p.farmer_id = $farmer_id 
                ORDER BY o.created_at DESC";
$orders = mysqli_query($conn, $order_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmer Dashboard - FarmConnect</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .dashboard-container { max-width: 1200px; margin: 100px auto 40px; padding: 20px; }
        .welcome-card { background: var(--primary); color: white; padding: 30px; border-radius: 20px; margin-bottom: 30px; }
        .form-card, .products-card { background: var(--white); padding: 25px; border-radius: 20px; margin-bottom: 30px; box-shadow: var(--shadow-md); }
        .form-card input, .form-card select, .form-card textarea { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ddd; border-radius: 10px; }
        .form-card button { background: var(--primary); color: white; padding: 12px; border: none; border-radius: 10px; cursor: pointer; width: 100%; }
        .products-table { width: 100%; border-collapse: collapse; }
        .products-table th, .products-table td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
        .products-table th { background: var(--primary); color: white; }
        .logout-btn { background: var(--danger); color: white; padding: 10px 20px; border: none; border-radius: 10px; cursor: pointer; margin-left: 15px; }
        .edit-btn { background: #2196F3; color: white; border: none; padding: 8px 12px; border-radius: 8px; cursor: pointer; margin-right: 5px; }
        .delete-btn { background: #f44336; color: white; border: none; padding: 8px 12px; border-radius: 8px; cursor: pointer; }
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 2000; justify-content: center; align-items: center; }
        .modal-content { background: white; padding: 30px; border-radius: 20px; max-width: 500px; width: 90%; }
        .modal-content input, .modal-content select, .modal-content textarea { width: 100%; padding: 10px; margin: 10px 0; border-radius: 8px; border: 1px solid #ddd; }
        
        /* Delivery status styles */
        .status-badge { padding: 4px 10px; border-radius: 20px; font-size: 0.9rem; font-weight: 500; }
        .status-pending { background: #FFF3E0; color: #E65100; }
        .status-shipped { background: #E3F2FD; color: #1565C0; }
        .status-delivered { background: #E8F5E9; color: #2E7D32; }
        .status-cancelled { background: #FFEBEE; color: #c62828; }
        .action-btn { border: none; padding: 6px 12px; border-radius: 5px; cursor: pointer; font-weight: 500; margin: 2px; color: white; }
        .btn-ship { background: #2196F3; }
        .btn-deliver { background: #4CAF50; }
        .btn-cancel { background: #f44336; }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="logo" onclick="location.href='../index.php'">
        <img src="../assets/images/logo.png" alt="FarmConnect">
        <span>FarmConnect</span>
    </div>
    <div class="nav-links">
        <a href="../index.php">Home</a>
        <a href="../shop.php">Shop</a>
        <a href="../cart.php">Cart</a>
        <button class="logout-btn" onclick="location.href='../logout.php'">Logout</button>
        <button class="dark-toggle" onclick="toggleDarkMode()">🌙</button>
    </div>
</nav>

<div class="dashboard-container">
    <div class="welcome-card">
        <h2>Welcome, <?php echo htmlspecialchars($user['name']); ?>! 🌾</h2>
        <p>Manage your farm products and track orders</p>
    </div>

    <!-- Upload Form -->
    <div class="form-card">
        <h3>Upload New Product</h3>
        <form action="../backend/upload.php" method="POST" enctype="multipart/form-data">
            <input type="text" name="name" placeholder="Product Name" required>
            <input type="number" name="price" placeholder="Price (Ksh)" required>
            <input type="number" name="stock" placeholder="Stock Quantity" required>
            <select name="unit" required>
                <option value="kg">⚖️ Kilograms (kg)</option>
                <option value="litre">🥛 Litres</option>
                <option value="piece">🧮 Pieces</option>
                <option value="bunch">🌿 Bunches</option>
                <option value="sack">🛍️ Sacks</option>
                <option value="tray">🥚 Trays</option>
                <option value="animal">🐄 Animals / Head</option>
            </select>
            <textarea name="description" rows="3" placeholder="Product Description / Notes (optional)"></textarea>
            <select name="category" required>
                <option value="vegetables">🥬 Vegetables</option>
                <option value="fruits">🍎 Fruits</option>
                <option value="dairy">🥛 Dairy</option>
                <option value="grains">🌾 Grains</option>
                <option value="animals">🐄 Animals / Livestock</option>
            </select>
            <input type="file" name="image" required>
            <button type="submit">Upload Product</button>
        </form>
    </div>

    <!-- Products Table -->
    <div class="products-card">
        <h3>Your Products</h3>
        <table class="products-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Unit</th>
                    <th>Category</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($products)): ?>
                <tr>
                    <td><img src="../imgs/<?php echo htmlspecialchars($row['image']); ?>" width="50" height="50" style="object-fit:cover; border-radius:10px;"></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td>Ksh <?php echo number_format($row['price'], 2); ?></td>
                    <td><?php echo $row['stock']; ?></td>
                    <td><?php echo $row['unit'] ?? 'kg'; ?></td>
                    <td><?php echo ucfirst($row['category']); ?></td>
                    <td>
                        <button class="edit-btn" onclick="openEditModal(<?php echo $row['id']; ?>)">✏️ Edit</button>
                        <button class="delete-btn" onclick="deleteProduct(<?php echo $row['id']; ?>)">🗑️ Delete</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Delivery Management Table -->
    <div class="products-card">
        <h3>🚚 Manage Deliveries</h3>
        <table class="products-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Total</th>
                    <th>Buyer Phone</th>
                    <th>Delivery Address</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="deliveryTableBody">
                <?php if (mysqli_num_rows($orders) == 0): ?>
                    <tr><td colspan="8" style="text-align:center;">No orders yet.</td></tr>
                <?php else: ?>
                    <?php while ($order = mysqli_fetch_assoc($orders)): 
                        $status = $order['delivery_status'] ?? 'pending';
                        $statusClass = '';
                        if ($status == 'pending') $statusClass = 'status-pending';
                        elseif ($status == 'shipped') $statusClass = 'status-shipped';
                        elseif ($status == 'delivered') $statusClass = 'status-delivered';
                        elseif ($status == 'cancelled') $statusClass = 'status-cancelled';
                    ?>
                    <tr id="order-row-<?php echo $order['id']; ?>">
                        <td>#<?php echo $order['id']; ?></td>
                        <td><?php echo htmlspecialchars($order['product_name']); ?></td>
                        <td><?php echo $order['quantity']; ?></td>
                        <td>Ksh <?php echo number_format($order['total'], 2); ?></td>
                        <td><?php echo $order['phone']; ?></td>
                        <td><?php echo htmlspecialchars($order['delivery_address'] ?? 'N/A'); ?></td>
                        <td>
                            <span class="status-badge <?php echo $statusClass; ?>" id="status-<?php echo $order['id']; ?>">
                                <?php echo ucfirst($status); ?>
                            </span>
                        </td>
                        <td id="action-<?php echo $order['id']; ?>">
                            <?php if ($status == 'pending'): ?>
                                <button class="action-btn btn-ship" onclick="updateDeliveryStatus(<?php echo $order['id']; ?>, 'shipped')">📦 Mark Shipped</button>
                                <button class="action-btn btn-cancel" onclick="updateDeliveryStatus(<?php echo $order['id']; ?>, 'cancelled')">❌ Cancel</button>
                            <?php elseif ($status == 'shipped'): ?>
                                <button class="action-btn btn-deliver" onclick="updateDeliveryStatus(<?php echo $order['id']; ?>, 'delivered')">✅ Mark Delivered</button>
                                <button class="action-btn btn-cancel" onclick="updateDeliveryStatus(<?php echo $order['id']; ?>, 'cancelled')">❌ Cancel</button>
                            <?php elseif ($status == 'delivered'): ?>
                                <span style="color:green;">✓ Completed</span>
                            <?php elseif ($status == 'cancelled'): ?>
                                <span style="color:red;">✗ Cancelled</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <h3>Edit Product</h3>
        <form id="editForm" enctype="multipart/form-data">
            <input type="hidden" name="product_id" id="editProductId">
            <input type="text" name="name" id="editName" placeholder="Product Name" required>
            <input type="number" name="price" id="editPrice" placeholder="Price (Ksh)" required>
            <input type="number" name="stock" id="editStock" placeholder="Stock Quantity" required>
            <select name="unit" id="editUnit" required>
                <option value="kg">⚖️ kg</option>
                <option value="litre">🥛 Litre</option>
                <option value="piece">🧮 Piece</option>
                <option value="bunch">🌿 Bunch</option>
                <option value="sack">🛍️ Sack</option>
                <option value="tray">🥚 Tray</option>
                <option value="animal">🐄 Animal / Head</option>
            </select>
            <textarea name="description" id="editDescription" rows="3" placeholder="Description (optional)"></textarea>
            <select name="category" id="editCategory" required>
                <option value="vegetables">🥬 Vegetables</option>
                <option value="fruits">🍎 Fruits</option>
                <option value="dairy">🥛 Dairy</option>
                <option value="grains">🌾 Grains</option>
                <option value="animals">🐄 Animals</option>
            </select>
            <label>Current Image:</label>
            <img id="currentImage" src="" width="100" style="display:block; margin:10px 0; border-radius:8px;">
            <label>New Image (optional):</label>
            <input type="file" name="image" id="editImage" accept="image/*">
            <div style="display:flex; gap:10px; margin-top:20px;">
                <button type="submit" style="background:var(--primary); color:white; padding:12px; border:none; border-radius:8px; cursor:pointer; flex:1;">Save Changes</button>
                <button type="button" onclick="closeEditModal()" style="background:#999; color:white; padding:12px; border:none; border-radius:8px; cursor:pointer; flex:1;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script src="../assets/js/main.js"></script>
<script>
    // Delete Product
    function deleteProduct(productId) {
        if (!confirm('Are you sure you want to delete this product?')) return;
        fetch('../backend/delete_product.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'product_id=' + productId
        })
        .then(response => response.text())
        .then(data => {
            if (data === 'success') {
                alert('Product deleted.');
                location.reload();
            } else {
                alert('Error: ' + data);
            }
        });
    }

    // Edit Modal Functions
    function openEditModal(productId) {
        fetch('../backend/get_product.php?id=' + productId)
            .then(response => response.json())
            .then(product => {
                document.getElementById('editProductId').value = product.id;
                document.getElementById('editName').value = product.name;
                document.getElementById('editPrice').value = product.price;
                document.getElementById('editStock').value = product.stock;
                document.getElementById('editUnit').value = product.unit || 'kg';
                document.getElementById('editDescription').value = product.description || '';
                document.getElementById('editCategory').value = product.category;
                document.getElementById('currentImage').src = '../imgs/' + product.image;
                document.getElementById('editModal').style.display = 'flex';
            });
    }

    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    document.getElementById('editForm').addEventListener('submit', function(e) {
        e.preventDefault();
        fetch('../backend/update_product.php', {
            method: 'POST',
            body: new FormData(this)
        })
        .then(response => response.text())
        .then(data => {
            if (data === 'success') {
                alert('Product updated!');
                location.reload();
            } else {
                alert('Error: ' + data);
            }
        });
    });

    // Reactive Delivery Status Update
    function updateDeliveryStatus(orderId, newStatus) {
        if (!confirm('Change status to ' + newStatus + '?')) return;
        
        const actionCell = document.getElementById('action-' + orderId);
        if (actionCell) {
            actionCell.innerHTML = '<span style="color:#666;">Processing...</span>';
        }
        
        fetch('../backend/update_delivery.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `order_id=${orderId}&status=${newStatus}`
        })
        .then(response => response.text())
        .then(data => {
            if (data === 'success') {
                const statusSpan = document.getElementById('status-' + orderId);
                const actionTd = document.getElementById('action-' + orderId);
                
                if (statusSpan) {
                    statusSpan.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
                    statusSpan.className = 'status-badge';
                    if (newStatus === 'pending') statusSpan.classList.add('status-pending');
                    else if (newStatus === 'shipped') statusSpan.classList.add('status-shipped');
                    else if (newStatus === 'delivered') statusSpan.classList.add('status-delivered');
                    else if (newStatus === 'cancelled') statusSpan.classList.add('status-cancelled');
                }
                
                if (actionTd) {
                    if (newStatus === 'pending') {
                        actionTd.innerHTML = `
                            <button class="action-btn btn-ship" onclick="updateDeliveryStatus(${orderId}, 'shipped')">📦 Mark Shipped</button>
                            <button class="action-btn btn-cancel" onclick="updateDeliveryStatus(${orderId}, 'cancelled')">❌ Cancel</button>
                        `;
                    } else if (newStatus === 'shipped') {
                        actionTd.innerHTML = `
                            <button class="action-btn btn-deliver" onclick="updateDeliveryStatus(${orderId}, 'delivered')">✅ Mark Delivered</button>
                            <button class="action-btn btn-cancel" onclick="updateDeliveryStatus(${orderId}, 'cancelled')">❌ Cancel</button>
                        `;
                    } else if (newStatus === 'delivered') {
                        actionTd.innerHTML = '<span style="color:green;">✓ Completed</span>';
                    } else if (newStatus === 'cancelled') {
                        actionTd.innerHTML = '<span style="color:red;">✗ Cancelled</span>';
                    }
                }
                
                // Show toast notification if available
                if (typeof showToast === 'function') {
                    showToast('Status updated to ' + newStatus, 'success');
                } else {
                    alert('Status updated successfully!');
                }
            } else {
                alert('Error: ' + data);
                location.reload();
            }
        })
        .catch(err => {
            alert('Request failed. Check console.');
            console.error(err);
            location.reload();
        });
    }

    window.onclick = function(event) {
        const modal = document.getElementById('editModal');
        if (event.target === modal) modal.style.display = 'none';
    };
</script>
</body>
</html>