<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FarmConnect</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
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
            --shadow-md: 0 8px 20px rgba(0,0,0,0.08);
            --shadow-lg: 0 12px 30px rgba(0,0,0,0.12);
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: var(--gray);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }
        body.dark {
            --gray: #121212;
            --white: #1e1e2e;
            --text-dark: #f0f0f0;
            --text-light: #b0b0b0;
        }
        .navbar {
            position: fixed;
            top: 0;
            left: 0;
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
            transition: transform 0.3s;
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
            transition: all 0.2s;
        }
        .nav-links a:hover {
            color: var(--accent);
        }
        .dark-toggle {
            background: none;
            border: none;
            color: white;
            font-size: 22px;
            cursor: pointer;
        }
        .form-container {
            max-width: 480px;
            width: 100%;
            margin: 120px auto 40px;
            background: var(--white);
            padding: 40px;
            border-radius: 28px;
            box-shadow: var(--shadow-lg);
        }
        .form-container h2 {
            text-align: center;
            margin-bottom: 30px;
            color: var(--primary);
            font-weight: 600;
        }
        .form-container input {
            width: 100%;
            padding: 14px;
            margin: 10px 0;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        .form-container input:focus {
            border-color: var(--primary);
            outline: none;
        }
        .form-container button {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 20px;
            transition: background 0.3s;
        }
        .form-container button:hover {
            background: var(--primary-dark);
        }
        .form-container p {
            text-align: center;
            margin-top: 20px;
        }
        .form-container a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }
        .form-container a:hover {
            text-decoration: underline;
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

<div class="form-container">
    <h2>Welcome Back</h2>
    <form action="backend/login.php" method="POST">
        <input type="email" name="email" placeholder="Email Address" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
    <p>Don't have an account? <a href="register.php">Register here</a></p>
</div>

<script>
    function toggleDarkMode() {
        document.body.classList.toggle('dark');
        localStorage.setItem('darkMode', document.body.classList.contains('dark'));
    }
    if (localStorage.getItem('darkMode') === 'true') document.body.classList.add('dark');
    
    fetch('backend/get_cart.php')
        .then(res => res.json())
        .then(data => {
            document.querySelectorAll('.cart-count').forEach(el => el.textContent = data.length);
        });
</script>
</body>
</html>