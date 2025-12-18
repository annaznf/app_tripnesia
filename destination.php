<?php

require_once 'config.php';

// Get all categories for filter
$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name");

// Get filter parameters
$category_filter = isset($_GET['category']) ? intval($_GET['category']) : '';
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

// Build query with filters
$query = "SELECT d.*, c.name as category_name 
          FROM destinations d 
          LEFT JOIN categories c ON d.category_id = c.id 
          WHERE 1=1";

if ($category_filter) {
    $query .= " AND d.category_id = $category_filter";
}

if ($search) {
    $query .= " AND (d.name LIKE '%$search%' OR d.location LIKE '%$search%' OR d.description LIKE '%$search%')";
}

$query .= " ORDER BY d.rating DESC, d.created_at DESC";

$destinations = mysqli_query($conn, $query);
$total_found = mysqli_num_rows($destinations);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Destinations - Tripnesia</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
        }

        /* ========== HEADER ========== */
        header {
            background: white;
            padding: 15px 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .logo {
            font-size: 28px;
            font-weight: bold;
            background: linear-gradient(135deg, #2196F3, #1976D2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        nav {
            display: flex;
            gap: 35px;
            align-items: center;
        }

        nav a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: color 0.3s;
            position: relative;
        }

        nav a:hover, nav a.active {
            color: #2196F3;
        }

        nav a.active::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 100%;
            height: 2px;
            background: #2196F3;
        }

        .auth-buttons {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .btn {
            padding: 10px 25px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-login {
            color: #2196F3;
            border: 2px solid #2196F3;
        }

        .btn-register {
            background: linear-gradient(135deg, #2196F3, #1976D2);
            color: white;
        }

        .btn-logout {
            background: #f44336;
            color: white;
            border: none;
            cursor: pointer;
        }

        .user-info {
            color: #333;
            font-weight: 500;
        }

        /* ========== HERO ========== */
        .hero {
            height: 400px;
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), 
                        url('https://images.unsplash.com/photo-1528214968864-8dd00782fa9e?crop=entropy&cs=srgb&fm=jpg&ixid=M3w3MjAxN3wwfDF8c2VhcmNofDMwfHxpbmRvbmVzaWF8ZW58MHx8fHwxNzY1Nzg5MjM4fDA&ixlib=rb-4.1.0&q=85&q=85&fmt=jpg&crop=entropy&cs=tinysrgb&w=450') center/cover;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
        }

        .hero-content h1 {
            font-size: 52px;
            margin-bottom: 15px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }

        .hero-content p {
            font-size: 20px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
        }

        /* ========== SEARCH & FILTER ========== */
        .search-filter {
            background: white;
            padding: 30px 50px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        }

        .search-filter form {
            display: flex;
            gap: 20px;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
        }

        .search-filter input,
        .search-filter select {
            padding: 14px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s;
        }

        .search-filter input {
            flex: 1;
        }

        .search-filter input:focus,
        .search-filter select:focus {
            outline: none;
            border-color: #2196F3;
        }

        .search-filter select {
            min-width: 200px;
        }

        .search-filter button {
            background: linear-gradient(135deg, #2196F3, #1976D2);
            color: white;
            border: none;
            padding: 14px 35px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .search-filter button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(33, 150, 243, 0.4);
        }

        /* ========== CONTENT ========== */
        .content {
            padding: 60px 50px;
            max-width: 1400px;
            margin: 0 auto;
        }

        .content-header {
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .content-header h2 {
            color: #333;
            font-size: 28px;
        }

        .results-count {
            color: #666;
            font-size: 16px;
        }

        .destinations-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 30px;
        }

        .destination-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: all 0.3s;
            cursor: pointer;
        }

        .destination-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.2);
        }

        .destination-card img {
            width: 100%;
            height: 220px;
            object-fit: cover;
        }

        .card-content {
            padding: 25px;
        }

        .category-badge {
            display: inline-block;
            background: #E3F2FD;
            color: #2196F3;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .card-content h3 {
            margin-bottom: 10px;
            color: #333;
            font-size: 22px;
        }

        .location {
            color: #666;
            font-size: 15px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
        }

        .rating {
            color: #FFB300;
            font-weight: 600;
            font-size: 16px;
        }

        .btn-view {
            background: linear-gradient(135deg, #2196F3, #1976D2);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            margin-top: 15px;
            transition: all 0.3s;
        }

        .btn-view:hover {
            transform: scale(1.05);
        }

        /* ========== EMPTY STATE ========== */
        .no-results {
            text-align: center;
            padding: 80px 20px;
            background: white;
            border-radius: 15px;
        }

        .no-results h2 {
            color: #666;
            margin-bottom: 15px;
            font-size: 28px;
        }

        .no-results p {
            color: #999;
            font-size: 16px;
        }

        /* ========== FOOTER ========== */
        footer {
            background: linear-gradient(135deg, #2196F3, #1976D2);
            color: white;
            padding: 50px 50px 30px;
            margin-top: 60px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 50px;
            margin-bottom: 30px;
        }

        .footer-about h3 {
            margin-bottom: 20px;
        }

        .footer-links h4 {
            margin-bottom: 20px;
        }

        .footer-links ul {
            list-style: none;
        }

        .footer-links li {
            margin: 12px 0;
        }

        .footer-links a {
            color: white;
            text-decoration: none;
            opacity: 0.9;
        }

        .social-icons {
            display: flex;
            gap: 15px;
            margin-top: 25px;
        }

        .social-icons a {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            color: white;
            text-decoration: none;
        }

        .footer-bottom {
            text-align: center;
            padding-top: 30px;
            border-top: 1px solid rgba(255,255,255,0.2);
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <!-- ========== HEADER ========== -->
    <header>
        <div class="logo">Tripnesia</div>
        <nav>
            <a href="index.php">Home</a>
            <a href="destination.php" class="active">Destination</a>
            <a href="favorite.php">Favorite</a>
            <a href="about.php">About</a>
        </nav>
        <div class="auth-buttons">
            <?php if (isLoggedIn()): ?>
                <span class="user-info">👋 Hi, <?php echo $_SESSION['username']; ?></span>
                <?php if (isAdmin()): ?>
                    <a href="admin/dashboard.php" class="btn btn-login">Admin Panel</a>
                <?php endif; ?>
                <a href="logout.php" class="btn btn-logout">Logout</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-login">Login</a>
                <a href="register.php" class="btn btn-register">Register</a>
            <?php endif; ?>
        </div>
    </header>

    <!-- ========== HERO ========== -->
    <section class="hero">
        <div class="hero-content">
            <h1>Explore Now the Wonders</h1>
            <p>That exist in Indonesia</p>
        </div>
    </section>

    <!-- ========== SEARCH & FILTER ========== -->
    <div class="search-filter">
        <form method="GET" action="">
            <input type="text" name="search" placeholder="🔍 Search destinations..." value="<?php echo htmlspecialchars($search); ?>">
            <select name="category">
                <option value="">All Categories</option>
                <?php 
                mysqli_data_seek($categories, 0);
                while ($cat = mysqli_fetch_assoc($categories)): 
                ?>
                <option value="<?php echo $cat['id']; ?>" <?php echo $category_filter == $cat['id'] ? 'selected' : ''; ?>>
                    <?php echo $cat['name']; ?>
                </option>
                <?php endwhile; ?>
            </select>
            <button type="submit">Search</button>
        </form>
    </div>

    <!-- ========== CONTENT ========== -->
    <div class="content">
        <div class="content-header">
            <h2>All Destinations</h2>
            <span class="results-count"><?php echo $total_found; ?> destinations found</span>
        </div>

        <?php if ($total_found > 0): ?>
        <div class="destinations-grid">
            <?php while ($row = mysqli_fetch_assoc($destinations)): ?>
            <?php 
            // Set image path - use uploaded image or fallback to default
            $image_src = !empty($row['image']) 
                ? 'uploads/destinations/' . $row['image'] 
                : 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=400';
            ?>
            <div class="destination-card" onclick="location.href='detail.php?id=<?php echo $row['id']; ?>'">
                <img src="<?php echo $image_src; ?>" alt="<?php echo $row['name']; ?>">
                <div class="card-content">
                    <span class="category-badge"><?php echo $row['category_name']; ?></span>
                    <h3><?php echo $row['name']; ?></h3>
                    <p class="location">
                        <span>📍</span>
                        <?php echo $row['location']; ?>
                    </p>
                    <div class="card-footer">
                        <span class="rating">⭐ <?php echo $row['rating']; ?></span>
                    </div>
                    <a href="detail.php?id=<?php echo $row['id']; ?>" class="btn-view" onclick="event.stopPropagation()">View Details</a>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <?php else: ?>
        <div class="no-results">
            <h2>🔍 No destinations found</h2>
            <p>Try adjusting your search or filter criteria</p>
        </div>
        <?php endif; ?>
    </div>

    <!-- ========== FOOTER ========== -->
    <footer>
        <div class="footer-content">
            <div class="footer-about">
                <h3>Tripnesia</h3>
                <p>Kami adalah platform travel terpercaya yang membantu Anda menemukan destinasi wisata terbaik di Indonesia.</p>
                <div class="social-icons">
                    <a href="https://www.facebook.com/Anna Zaidatul" target="_blank" title="Facebook">
                   <i class="fab fa-facebook-f"></i>
                   </a>
                   <a href="https://www.instagram.com/xxaznf_" target="_blank" title="Instagram">
                   <i class="fab fa-instagram"></i>
                   </a>
                   <a href="https://www.tiktok.com/@annazaidatul" target="_blank" title="TikTok">
                   <i class="fab fa-tiktok"></i>
                   </a>
                </div>
            </div>
            <div class="footer-links">
                <h4>Company</h4>
                <ul>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="#">(+62)8122927498</a></li>
                    <li><a href="#">Jakarta, Indonesia</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                </ul>
            </div>
            <div class="footer-links">
                <h4>Support</h4>
                <ul>
                    <li><a href="#">Help Center</a></li>
                    <li><a href="#">FAQ</a></li>
                    <li><a href="#">Community</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 Tripnesia. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>