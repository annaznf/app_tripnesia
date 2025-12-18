<?php

require_once 'config.php';

// Get destination ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id == 0) {
    redirect('destination.php');
}

// Get destination data
$query = "SELECT d.*, c.name as category_name 
          FROM destinations d 
          LEFT JOIN categories c ON d.category_id = c.id 
          WHERE d.id = $id";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    redirect('destination.php');
}

$destination = mysqli_fetch_assoc($result);

// Check if favorited
$is_favorited = false;
if (isLoggedIn()) {
    $user_id = $_SESSION['user_id'];
    $fav_check = mysqli_query($conn, "SELECT * FROM favorites WHERE user_id=$user_id AND destination_id=$id");
    $is_favorited = mysqli_num_rows($fav_check) > 0;
}

// Get gallery images
$gallery = mysqli_query($conn, "SELECT * FROM gallery WHERE destination_id = $id LIMIT 4");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $destination['name']; ?> - Tripnesia</title>
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
        }

        nav a:hover {
            color: #2196F3;
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

        /* ========== HERO IMAGE ========== */
        .hero-image {
            height: 500px;
            background-size: cover;
            background-position: center;
            position: relative;
        }

        .hero-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.3));
        }

        /* ========== DETAIL CONTENT ========== */
        .detail-content {
            max-width: 1200px;
            margin: -100px auto 60px;
            padding: 0 50px;
            position: relative;
            z-index: 10;
        }

        .detail-card {
            background: white;
            border-radius: 20px;
            padding: 50px;
            box-shadow: 0 15px 50px rgba(0,0,0,0.1);
        }

        .detail-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 40px;
            gap: 30px;
        }

        .detail-title h1 {
            font-size: 42px;
            color: #333;
            margin-bottom: 15px;
        }

        .location {
            color: #666;
            font-size: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .favorite-btn {
            background: #fff;
            border: 2px solid #2196F3;
            color: #2196F3;
            padding: 14px 28px;
            border-radius: 30px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
        }

        .favorite-btn:hover {
            background: #2196F3;
            color: white;
            transform: translateY(-2px);
        }

        .favorite-btn.active {
            background: #2196F3;
            color: white;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 40px;
            margin: 40px 0;
            padding: 40px 0;
            border-top: 2px solid #f0f0f0;
            border-bottom: 2px solid #f0f0f0;
        }

        .info-item {
            text-align: center;
        }

        .info-item .label {
            color: #999;
            font-size: 14px;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .info-item .value {
            font-size: 26px;
            font-weight: bold;
            color: #2196F3;
        }

        .description {
            margin: 40px 0;
            line-height: 1.9;
            color: #555;
        }

        .description h2 {
            margin-bottom: 25px;
            color: #333;
            font-size: 32px;
        }

        .description p {
            font-size: 17px;
            margin-bottom: 15px;
        }

        /* ========== GALLERY ========== */
        .gallery-section {
            margin-top: 60px;
        }

        .gallery-section h2 {
            margin-bottom: 30px;
            color: #333;
            font-size: 32px;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .gallery-grid img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 15px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .gallery-grid img:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        /* ========== ALERT ========== */
        .alert {
            padding: 15px 20px;
            margin-bottom: 30px;
            border-radius: 10px;
            text-align: center;
            font-weight: 500;
        }

        .alert-success {
            background: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #66bb6a;
        }

        .alert-error {
            background: #ffebee;
            color: #c62828;
            border: 1px solid #ef5350;
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
            <a href="index.php">Home</a>
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
            <?php endif; ?>
        </div>
    </header>

    <!-- ========== HERO IMAGE ========== -->
    <?php 
    // Set hero image - use uploaded image or fallback to default
    $hero_image = !empty($destination['image']) 
        ? 'uploads/destinations/' . $destination['image'] 
        : 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=1200';
    ?>
    <div class="hero-image" style="background-image: url('<?php echo $hero_image; ?>')"></div>

    <!-- ========== DETAIL CONTENT ========== -->
    <div class="detail-content">
        <div class="detail-card">
            <?php if (isset($_GET['msg'])): ?>
                <div class="alert alert-<?php echo htmlspecialchars($_GET['type'] ?? 'success'); ?>">
                    <?php echo htmlspecialchars($_GET['msg']); ?>
                </div>
            <?php endif; ?>

            <div class="detail-header">
                <div class="detail-title">
                    <h1><?php echo $destination['name']; ?></h1>
                    <p class="location">
                        <span>📍</span>
                        <?php echo $destination['location']; ?>
                    </p>
                </div>
                
                <?php if (isLoggedIn()): ?>
                <form method="POST" action="toggle_favorite.php">
                    <input type="hidden" name="destination_id" value="<?php echo $id; ?>">
                    <button type="submit" class="favorite-btn <?php echo $is_favorited ? 'active' : ''; ?>">
                        <span><?php echo $is_favorited ? '❤️' : '🤍'; ?></span>
                        <?php echo $is_favorited ? 'Favorited' : 'Add to Favorite'; ?>
                    </button>
                </form>
                <?php else: ?>
                <a href="login.php" class="favorite-btn">
                    <span>🤍</span>
                    Login to Favorite
                </a>
                <?php endif; ?>
            </div>

            <div class="info-grid">
                <div class="info-item">
                    <div class="label">Category</div>
                    <div class="value" style="font-size: 20px;"><?php echo $destination['category_name']; ?></div>
                </div>
                <div class="info-item">
                    <div class="label">Rating</div>
                    <div class="value">⭐ <?php echo $destination['rating']; ?></div>
                </div>
                <div class="info-item">
                    <div class="label">Location</div>
                    <div class="value" style="font-size: 18px;"><?php echo explode(',', $destination['location'])[0]; ?></div>
                </div>
            </div>

            <div class="description">
                <h2>Detail Wisata</h2>
                <p><?php echo nl2br($destination['description']); ?></p>
            </div>

            <?php if (mysqli_num_rows($gallery) > 0): ?>
            <div class="gallery-section">
                <h2>Our Gallery</h2>
                <div class="gallery-grid">
                    <?php while ($img = mysqli_fetch_assoc($gallery)): ?>
                    <img src="https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=400" alt="Gallery">
                    <?php endwhile; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
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
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 Tripnesia. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>