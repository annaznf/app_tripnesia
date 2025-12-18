<?php

require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Tripnesia</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
            height: 450px;
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), 
                        url('https://images.unsplash.com/photo-1528214968864-8dd00782fa9e?crop=entropy&cs=srgb&fm=jpg&ixid=M3w3MjAxN3wwfDF8c2VhcmNofDMwfHxpbmRvbmVzaWF8ZW58MHx8fHwxNzY1Nzg5MjM4fDA&ixlib=rb-4.1.0&q=85&q=85&fmt=jpg&crop=entropy&cs=tinysrgb&w=450') center/cover;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-align: center;
        }

        .hero h1 {
            font-size: 56px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }

        /* ========== CONTENT ========== */
        .content {
            max-width: 1100px;
            margin: 80px auto;
            padding: 0 50px;
        }

        .about-section {
            margin-bottom: 70px;
        }

        .about-section h2 {
            font-size: 38px;
            margin-bottom: 25px;
            color: #333;
        }

        .about-section p {
            line-height: 1.9;
            color: #555;
            font-size: 18px;
            margin-bottom: 20px;
        }

        /* ========== FEATURES ========== */
        .features {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 40px;
            margin-top: 60px;
        }

        .feature-card {
            text-align: center;
            padding: 40px 30px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.15);
        }

        .feature-icon {
            font-size: 60px;
            margin-bottom: 20px;
        }

        .feature-card h3 {
            margin: 20px 0 15px;
            color: #333;
            font-size: 24px;
        }

        .feature-card p {
            color: #666;
            line-height: 1.7;
            font-size: 16px;
        }

        /* ========== STATS ========== */
        .stats-section {
            background: linear-gradient(135deg, #2196F3, #1976D2);
            color: white;
            padding: 80px 50px;
            margin: 80px 0;
            text-align: center;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 50px;
            max-width: 1200px;
            margin: 50px auto 0;
        }

        .stat-item h3 {
            font-size: 48px;
            margin-bottom: 10px;
        }

        .stat-item p {
            font-size: 18px;
            opacity: 0.9;
        }

        /* ========== TEAM ========== */
        .team-section {
            margin: 80px 0;
        }

        .team-section h2 {
            text-align: center;
            font-size: 42px;
            margin-bottom: 60px;
            color: #333;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 40px;
        }

        .team-card {
            text-align: center;
            padding: 30px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .team-avatar {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #2196F3, #1976D2);
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            color: white;
        }

        .team-card h3 {
            margin-bottom: 10px;
            color: #333;
        }

        .team-card p {
            color: #666;
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
            <a href="destination.php">Destination</a>
            <a href="favorite.php">Favorite</a>
            <a href="about.php" class="active">About</a>
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
    <div class="hero">
        <h1>Tentang Kami</h1>
    </div>

    <!-- ========== CONTENT ========== -->
    <div class="content">
        <div class="about-section">
            <h2>🌏 Visi Kami</h2>
            <p>Tripnesia adalah platform travel terpercaya yang membantu wisatawan menemukan dan menjelajahi destinasi wisata terbaik di Indonesia. Kami berkomitmen untuk memberikan pengalaman wisata yang tak terlupakan dengan menyediakan informasi lengkap dan akurat tentang berbagai destinasi wisata di nusantara.</p>
            <p>Indonesia memiliki kekayaan alam dan budaya yang luar biasa. Dari Sabang sampai Merauke, setiap sudut negeri ini menyimpan keindahan yang menakjubkan. Misi kami adalah menjadi jembatan yang menghubungkan wisatawan dengan keindahan Indonesia.</p>
        </div>

        <div class="about-section">
            <h2>🎯 Misi Kami</h2>
            <p>Memudahkan setiap wisatawan untuk merencanakan perjalanan mereka dengan menyediakan informasi destinasi yang lengkap, akurat, dan terpercaya. Kami ingin menjadi bagian dari setiap petualangan Anda di Indonesia.</p>
            <p>Kami percaya bahwa setiap orang berhak merasakan keindahan Indonesia. Oleh karena itu, kami menyediakan platform yang mudah digunakan dengan informasi yang komprehensif untuk membantu Anda membuat keputusan yang tepat dalam memilih destinasi wisata.</p>
        </div>

        <div class="features">
            <div class="feature-card">
                <div class="feature-icon">🌏</div>
                <h3>Destinasi Lengkap</h3>
                <p>Ratusan destinasi wisata dari Sabang sampai Merauke dengan informasi detail dan akurat</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">⭐</div>
                <h3>Rating Terpercaya</h3>
                <p>Review dan rating dari wisatawan lain untuk membantu Anda membuat keputusan</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔍</div>
                <h3>Mudah Dicari</h3>
                <p>Sistem pencarian dan filter yang powerful untuk menemukan destinasi impian Anda</p>
            </div>
        </div>
    </div>

    <!-- ========== STATS ========== -->
    <div class="stats-section">
        <h2 style="font-size: 42px; margin-bottom: 20px;">Pencapaian Kami</h2>
        <p style="font-size: 18px; opacity: 0.9;">Dipercaya oleh ribuan wisatawan di seluruh Indonesia</p>
        <div class="stats-grid">
            <div class="stat-item">
                <h3>500+</h3>
                <p>Destinasi</p>
            </div>
            <div class="stat-item">
                <h3>100K+</h3>
                <p>Pengguna Aktif</p>
            </div>
            <div class="stat-item">
                <h3>50K+</h3>
                <p>Reviews</p>
            </div>
            <div class="stat-item">
                <h3>4.8</h3>
                <p>Rating</p>
            </div>
        </div>
    </div>

    <!-- ========== TEAM ========== -->
    <div class="content">
        <div class="team-section">
            <h2>👥 Tim Kami</h2>
            <div class="team-grid">
                <div class="team-card">
                    <div class="team-avatar">👨‍💼</div>
                    <h3>John Doe</h3>
                    <p>Founder & CEO</p>
                </div>
                <div class="team-card">
                    <div class="team-avatar">👩‍💻</div>
                    <h3>Jane Smith</h3>
                    <p>CTO</p>
                </div>
                <div class="team-card">
                    <div class="team-avatar">👨‍🎨</div>
                    <h3>Nicholas Alister</h3>
                    <p>Head of Design</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ========== FOOTER ========== -->
    <footer>
        <div class="footer-content">
            <div class="footer-about">
                <h3>Tripnesia</h3>
                <p>Platform travel terpercaya untuk menjelajahi keindahan Indonesia. Temukan destinasi impian Anda bersama kami.</p>
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
            <p>&copy; 2024 Tripnesia. All rights reserved. Made with ❤️ in Indonesia</p>
        </div>
    </footer>
</body>
</html>