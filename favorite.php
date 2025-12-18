<?php

require_once 'config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

$user_id = $_SESSION['user_id'];

// Get user's favorite destinations
$favorites = mysqli_query($conn, "
    SELECT d.*, c.name as category_name, f.created_at as favorited_at
    FROM favorites f 
    JOIN destinations d ON f.destination_id = d.id 
    LEFT JOIN categories c ON d.category_id = c.id 
    WHERE f.user_id = $user_id 
    ORDER BY f.created_at DESC
");

$total_favorites = mysqli_num_rows($favorites);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Favorites - Tripnesia</title>
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

        .btn-logout {
            background: #f44336;
            color: white;
            padding: 10px 25px;
            border-radius: 8px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-weight: 500;
        }

        .btn-login {
            color: #2196F3;
            border: 2px solid #2196F3;
            padding: 10px 25px;
            border-radius: 8px;
            text-decoration: none;
        }

        .user-info {
            color: #333;
            font-weight: 500;
        }

        /* ========== CONTENT ========== */
        .content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 60px 50px;
        }

        .page-header {
            margin-bottom: 40px;
        }

        .page-header h1 {
            font-size: 42px;
            color: #333;
            margin-bottom: 10px;
        }

        .page-header p {
            color: #666;
            font-size: 18px;
        }

        .favorites-count {
            display: inline-block;
            background: #E3F2FD;
            color: #2196F3;
            padding: 8px 20px;
            border-radius: 25px;
            font-weight: 600;
            margin-top: 15px;
        }

        /* ========== DESTINATION GRID ========== */
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
            position: relative;
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

        .favorite-icon {
            position: absolute;
            top: 15px;
            right: 15px;
            background: white;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        /* ========== EMPTY STATE ========== */
        .empty-state {
            text-align: center;
            padding: 100px 20px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }

        .empty-state-icon {
            font-size: 80px;
            margin-bottom: 20px;
        }

        .empty-state h2 {
            margin-bottom: 15px;
            color: #666;
            font-size: 32px;
        }

        .empty-state p {
            color: #999;
            font-size: 18px;
            margin-bottom: 30px;
        }

        .btn-browse {
            background: linear-gradient(135deg, #2196F3, #1976D2);
            color: white;
            padding: 14px 35px;
            border-radius: 30px;
            text-decoration: none;
            display: inline-block;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-browse:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(33, 150, 243, 0.4);
        }

        /* ========== FOOTER ========== */
        footer {
            background: linear-gradient(135deg, #2196F3, #1976D2);
            color: white;
            padding: 50px 50px 30px;
            margin-top: 80px;
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
            <a href="favorite.php" class="active">Favorite</a>
            <a href="about.php">About</a>
        </nav>
        <div class="auth-buttons">
            <span class="user-info">👋 Hi, <?php echo $_SESSION['username']; ?></span>
            <?php if (isAdmin()): ?>
                <a href="admin/dashboard.php" class="btn-login">Admin Panel</a>
            <?php endif; ?>
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>
    </header>

    <!-- ========== CONTENT ========== -->
    <div class="content">
        <div class="page-header">
            <h1>❤️ My Favorite Destinations</h1>
            <p>Your collection of amazing places to visit</p>
            <span class="favorites-count"><?php echo $total_favorites; ?> Favorites</span>
        </div>

        <?php if ($total_favorites > 0): ?>
        <div class="destinations-grid">
            <?php while ($row = mysqli_fetch_assoc($favorites)): ?>
            <?php 
            // Set image path - use uploaded image or fallback to default
            $image_src = !empty($row['image']) 
                ? 'uploads/destinations/' . $row['image'] 
                : 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=400';
            ?>
            <div class="destination-card" onclick="location.href='detail.php?id=<?php echo $row['id']; ?>'">
                <div class="favorite-icon">❤️</div>
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
        <div class="empty-state">
            <div class="empty-state-icon">💔</div>
            <h2>No favorites yet</h2>
            <p>Start exploring amazing destinations and add them to your favorites!</p>
            <a href="destination.php" class="btn-browse">Browse Destinations</a>
        </div>
        <?php endif; ?>
    </div>

    <!-- ========== FOOTER ========== -->
    <footer>
        <div class="footer-content">
            <div class="footer-about">
                <h3>Tripnesia</h3>
                <p>Kami adalah platform travel terpercaya yang membantu Anda menemukan destinasi wisata terbaik di Indonesia.</p>
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