<?php


require_once '../config.php';


if (!isAdmin()) {
    redirect('../login.php');
}


$total_destinations = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM destinations"))['total'];
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='user'"))['total'];
$total_categories = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM categories"))['total'];
$total_favorites = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM favorites"))['total'];


$recent_destinations = mysqli_query($conn, "
    SELECT d.*, c.name as category_name 
    FROM destinations d 
    LEFT JOIN categories c ON d.category_id = c.id 
    ORDER BY d.created_at DESC 
    LIMIT 5
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Tripnesia</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        /* ========== SIDEBAR ========== */
        .sidebar {
            width: 260px;
            background: linear-gradient(180deg, #1976D2 0%, #1565C0 100%);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }

        .sidebar-header {
            padding: 25px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-header h2 {
            font-size: 24px;
            margin-bottom: 5px;
        }

        .sidebar-header p {
            font-size: 13px;
            opacity: 0.8;
        }

        .sidebar-menu {
            list-style: none;
            padding: 20px 0;
        }

        .sidebar-menu li {
            margin: 5px 0;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 20px;
            color: white;
            text-decoration: none;
            transition: all 0.3s;
            font-size: 15px;
        }

        .sidebar-menu a:hover, .sidebar-menu a.active {
            background: rgba(255,255,255,0.15);
            border-left: 4px solid white;
        }

        /* ========== MAIN CONTENT ========== */
        .main-content {
            flex: 1;
            margin-left: 260px;
            padding: 30px;
        }

        .header {
            background: white;
            padding: 25px 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .header h1 {
            font-size: 32px;
            color: #333;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-info span {
            color: #666;
            font-weight: 500;
        }

        .btn-logout {
            background: #f44336;
            color: white;
            padding: 10px 25px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-logout:hover {
            background: #d32f2f;
            transform: translateY(-2px);
        }

        /* ========== STATS CARDS ========== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }

        .stat-info h3 {
            font-size: 36px;
            color: #333;
            margin-bottom: 8px;
        }

        .stat-info p {
            color: #666;
            font-size: 14px;
            font-weight: 500;
        }

        .stat-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
        }

        .stat-card:nth-child(1) .stat-icon {
            background: linear-gradient(135deg, #E3F2FD, #BBDEFB);
        }

        .stat-card:nth-child(2) .stat-icon {
            background: linear-gradient(135deg, #F3E5F5, #E1BEE7);
        }

        .stat-card:nth-child(3) .stat-icon {
            background: linear-gradient(135deg, #E8F5E9, #C8E6C9);
        }

        .stat-card:nth-child(4) .stat-icon {
            background: linear-gradient(135deg, #FFF3E0, #FFE0B2);
        }

        /* ========== RECENT TABLE ========== */
        .recent-section {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .recent-section h2 {
            margin-bottom: 25px;
            color: #333;
            font-size: 24px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th {
            text-align: left;
            padding: 15px;
            background: #f8f9fa;
            color: #666;
            font-weight: 600;
            font-size: 14px;
            border-bottom: 2px solid #e0e0e0;
        }

        table td {
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
            color: #555;
        }

        table tr:hover {
            background: #fafafa;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .btn-edit {
            background: #2196F3;
            color: white;
            padding: 8px 18px;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-edit:hover {
            background: #1976D2;
        }

        .btn-delete {
            background: #f44336;
            color: white;
            padding: 8px 18px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            border: none;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-delete:hover {
            background: #d32f2f;
        }

        .btn-view-all {
            background: linear-gradient(135deg, #2196F3, #1976D2);
            color: white;
            padding: 12px 30px;
            border-radius: 8px;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-view-all:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(33, 150, 243, 0.4);
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- ========== SIDEBAR ========== -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>🎯 Tripnes Admin</h2>
                <p>Management Panel</p>
            </div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php" class="active">📊 Dashboard</a></li>
                <li><a href="destinations.php">🏖️ Destinations</a></li>
                <li><a href="categories.php">📁 Categories</a></li>
                <li><a href="users.php">👥 Users</a></li>
                <li><a href="../index.php">🏠 Back to Website</a></li>
            </ul>
        </aside>

        <!-- ========== MAIN CONTENT ========== -->
        <main class="main-content">
            <div class="header">
                <h1>📊 Dashboard</h1>
                <div class="user-info">
                    <span>👋 Welcome, <?php echo $_SESSION['username']; ?></span>
                    <a href="../logout.php" class="btn-logout">Logout</a>
                </div>
            </div>

            <!-- ========== STATS ========== -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-info">
                        <h3><?php echo $total_destinations; ?></h3>
                        <p>Total Destinations</p>
                    </div>
                    <div class="stat-icon">🏖️</div>
                </div>

                <div class="stat-card">
                    <div class="stat-info">
                        <h3><?php echo $total_users; ?></h3>
                        <p>Total Users</p>
                    </div>
                    <div class="stat-icon">👥</div>
                </div>

                <div class="stat-card">
                    <div class="stat-info">
                        <h3><?php echo $total_categories; ?></h3>
                        <p>Categories</p>
                    </div>
                    <div class="stat-icon">📁</div>
                </div>

                <div class="stat-card">
                    <div class="stat-info">
                        <h3><?php echo $total_favorites; ?></h3>
                        <p>Total Favorites</p>
                    </div>
                    <div class="stat-icon">❤️</div>
                </div>
            </div>

            <!-- ========== RECENT DESTINATIONS ========== -->
            <div class="recent-section">
                <h2>📍 Recent Destinations</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Location</th>
                            <th>Rating</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($recent_destinations)): ?>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <?php 
                                    $thumb_src = !empty($row['image']) 
                                        ? '../uploads/destinations/' . $row['image'] 
                                        : 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=100';
                                    ?>
                                    <img src="<?php echo $thumb_src; ?>" alt="thumb" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                                    <strong><?php echo $row['name']; ?></strong>
                                </div>
                            </td>
                            <td><?php echo $row['category_name']; ?></td>
                            <td><?php echo $row['location']; ?></td>
                            <td>⭐ <?php echo $row['rating']; ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="edit_destination.php?id=<?php echo $row['id']; ?>" class="btn-edit">Edit</a>
                                    <button class="btn-delete" onclick="confirmDelete(<?php echo $row['id']; ?>)">Delete</button>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <a href="destinations.php" class="btn-view-all">View All Destinations →</a>
            </div>
        </main>
    </div>

    <script>
        function confirmDelete(id) {
            if (confirm('Are you sure you want to delete this destination? This action cannot be undone.')) {
                window.location.href = 'delete_destination.php?id=' + id;
            }
        }
    </script>
</body>
</html>
