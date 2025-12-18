<?php

require_once 'config.php';

// Get popular destinations (by rating)
$popular = mysqli_query($conn, "
    SELECT d.*, c.name as category_name 
    FROM destinations d 
    LEFT JOIN categories c ON d.category_id = c.id 
    ORDER BY d.rating DESC 
    LIMIT 4
");

// Get all destinations for recommendation section
$recommendations = mysqli_query($conn, "
    SELECT d.*, c.name as category_name 
    FROM destinations d 
    LEFT JOIN categories c ON d.category_id = c.id 
    ORDER BY d.created_at DESC 
    LIMIT 3
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tripnesia - Explore Indonesia</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
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

        .user-info {
            color: #333;
            font-weight: 500;
        }

        .btn {
            padding: 10px 25px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }

        .btn-login {
            color: #2196F3;
            border: 2px solid #2196F3;
            background: white;
        }

        .btn-login:hover {
            background: #2196F3;
            color: white;
        }

        .btn-register {
            background: linear-gradient(135deg, #2196F3, #1976D2);
            color: white;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(33, 150, 243, 0.4);
        }

        .btn-logout {
            background: #f44336;
            color: white;
        }

        /* ========== HERO SECTION ========== */
        .hero {
            height: 600px;
            background: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), 
                        url('https://images.unsplash.com/photo-1432889490240-84df33d47091?crop=entropy&cs=srgb&fm=jpg&ixid=M3w3MjAxN3wwfDF8c2VhcmNofDM2fHxiZWFjaCUyMHxlbnwwfHx8fDE3NjU4ODY5MTh8MA&ixlib=rb-4.1.0&q=85&q=85&fmt=jpg&crop=entropy&cs=tinysrgb&w=450') center/cover;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
        }

        .hero-content h1 {
            font-size: 56px;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }

        .hero-content p {
            font-size: 22px;
            margin-bottom: 40px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
        }

        .search-bar {
            background: white;
            padding: 20px;
            border-radius: 50px;
            display: flex;
            gap: 15px;
            max-width: 700px;
            margin: 0 auto;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        .search-bar input {
            flex: 1;
            border: none;
            padding: 12px 25px;
            font-size: 16px;
            border-radius: 25px;
        }

        .search-bar input:focus {
            outline: none;
        }

        .search-bar button {
            background: linear-gradient(135deg, #2196F3, #1976D2);
            color: white;
            border: none;
            padding: 12px 35px;
            border-radius: 25px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .search-bar button:hover {
            transform: scale(1.05);
        }

        /* ========== SECTIONS ========== */
        section {
            padding: 80px 50px;
        }

        .section-header {
            text-align: center;
            margin-bottom: 50px;
        }

        .section-title {
            font-size: 42px;
            margin-bottom: 10px;
            color: #333;
        }

        .section-subtitle {
            color: #666;
            font-size: 18px;
        }

        /* ========== DESTINATION CARDS ========== */
        .destinations-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            margin-top: 40px;
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

        /* ========== RECOMMENDATION SECTION ========== */
        .recommendation-section {
            background: #f8f9fa;
        }

        .recommendation-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 50px;
            align-items: center;
        }

        .recommendation-text h2 {
            font-size: 38px;
            margin-bottom: 20px;
            color: #333;
        }

        .recommendation-text p {
            color: #666;
            margin-bottom: 20px;
            line-height: 1.8;
        }

        .recommendation-text ul {
            list-style: none;
            margin: 25px 0;
        }

        .recommendation-text li {
            margin: 15px 0;
            padding-left: 35px;
            position: relative;
            color: #555;
        }

        .recommendation-text li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: #2196F3;
            font-weight: bold;
            font-size: 20px;
        }

        .recommendation-images {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .recommendation-images img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }

        .recommendation-images img:hover {
            transform: scale(1.05);
        }

        /* ========== FOOTER ========== */
        footer {
            background: linear-gradient(135deg, #2196F3, #1976D2);
            color: white;
            padding: 50px 50px 30px;
        }

        .footer-content {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 50px;
            margin-bottom: 30px;
        }

        .footer-about h3 {
            margin-bottom: 20px;
            font-size: 24px;
        }

        .footer-about p {
            line-height: 1.8;
            opacity: 0.9;
        }

        .footer-links h4 {
            margin-bottom: 20px;
            font-size: 18px;
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
            transition: opacity 0.3s;
        }

        .footer-links a:hover {
            opacity: 1;
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
            transition: all 0.3s;
        }

        .social-icons a:hover {
            background: rgba(255,255,255,0.3);
            transform: translateY(-3px);
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
            <a href="index.php" class="active">Home</a>
            <a href="destination.php">Destination</a>
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

    <!-- ========== HERO SECTION ========== -->
    <section class="hero">
        <div class="hero-content">
            <h1>Explore the Beauty,<br>Live the Adventure</h1>
            <p>Discover amazing destinations across Indonesia</p>
            <form action="destination.php" method="GET" class="search-bar">
                <input type="text" name="search" placeholder="Where do you want to go?">
                <button type="submit">Search</button>
            </form>
        </div>
    </section>

    <!-- ========== POPULAR DESTINATIONS ========== -->
    <section>
        <div class="section-header">
            <h2 class="section-title">Popular Destinations</h2>
            <p class="section-subtitle">in Indonesia</p>
        </div>
        
        <div class="destinations-grid">
            <?php while ($row = mysqli_fetch_assoc($popular)): ?>
            <?php 
            // Set image path - use uploaded image or fallback to default
            $image_src = !empty($row['image']) 
                ? 'uploads/destinations/' . $row['image'] 
                : 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=400';
            ?>
            <div class="destination-card" onclick="location.href='detail.php?id=<?php echo $row['id']; ?>'">
                <img src="<?php echo $image_src; ?>" alt="<?php echo $row['name']; ?>">
                <div class="card-content">
                    <h3><?php echo $row['name']; ?></h3>
                    <p class="location">
                        <span>📍</span>
                        <?php echo $row['location']; ?>
                    </p>
                    <div class="card-footer">
                        <span class="rating">⭐ <?php echo $row['rating']; ?></span>
                        <a href="detail.php?id=<?php echo $row['id']; ?>" class="btn-view" onclick="event.stopPropagation()">View Details</a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </section>

    <!-- ========== RECOMMENDATION SECTION ========== -->
    <section class="recommendation-section">
        <div class="recommendation-content">
            <div class="recommendation-text">
                <h2>Kami Rekomendasikan<br>Wisata Terbaik di<br>Indonesia</h2>
                <p>Indonesia memiliki ribuan destinasi wisata yang menakjubkan. Dari pantai eksotis hingga gunung yang menjulang tinggi, dari kota modern hingga desa tradisional yang memukau.</p>
                <ul>
                    <li>Destinasi wisata terlengkap se-Indonesia</li>
                    <li>Informasi akurat dan terpercaya</li>
                    <li>Rating dari wisatawan lain</li>
                    <li>Mudah dan cepat mencari destinasi</li>
                </ul>
            </div>
            <div class="recommendation-images">
                <img src="https://images.unsplash.com/photo-1703769605297-cc74106244d9?crop=entropy&cs=srgb&fm=jpg&ixid=M3w3MjAxN3wwfDF8c2VhcmNofDN8fHJhamElMjBhbXBhdHxlbnwwfHx8fDE3NjU4ODcwMzB8MA&ixlib=rb-4.1.0&q=85&q=85&fmt=jpg&crop=entropy&cs=tinysrgb&w=450" alt="Indonesia 1">
                <img src="https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=400" alt="Indonesia 2">
                <img src="https://images.unsplash.com/photo-1578469550956-0e16b69c6a3d?crop=entropy&cs=srgb&fm=jpg&ixid=M3w3MjAxN3wwfDF8c2VhcmNofDEwfHxjYW5kaSUyMGJvcm9idWR1cnxlbnwwfHx8fDE3NjU3ODkzNjh8MA&ixlib=rb-4.1.0&q=85&q=85&fmt=jpg&crop=entropy&cs=tinysrgb&w=450" alt="Indonesia 3">
                <img src="https://images.unsplash.com/photo-1505993597083-3bd19fb75e57?crop=entropy&cs=srgb&fm=jpg&ixid=M3w3MjAxN3wwfDF8c2VhcmNofDJ8fGluZG9uZXNpYXxlbnwwfHx8fDE3NjU3ODkyMzh8MA&ixlib=rb-4.1.0&q=85&q=85&fmt=jpg&crop=entropy&cs=tinysrgb&w=450" alt="Indonesia 4">
            </div>
        </div>
    </section>

    <!-- ========== FOOTER ========== -->
    <footer>
        <div class="footer-content">
            <div class="footer-about">
                <h3>Tripnesia</h3>
                <p>Kami adalah platform travel terpercaya yang membantu Anda menemukan dan menjelajahi destinasi wisata terbaik di Indonesia. Jelajahi keindahan nusantara bersama kami.</p>
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
                    <li><a href="#">Blog</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 Tripnesia. All rights reserved. Made with ❤️ in Indonesia</p>
        </div>
    </footer>
</body>
</html>