<?php

require_once '../config.php';

// Check admin access
if (!isAdmin()) {
    redirect('../login.php');
}

// Get all destinations
$destinations = mysqli_query($conn, "
    SELECT d.*, c.name as category_name 
    FROM destinations d 
    LEFT JOIN categories c ON d.category_id = c.id 
    ORDER BY d.created_at DESC
");

$total_destinations = mysqli_num_rows($destinations);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Destinations - Admin</title>
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

        .header-actions {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .btn-add {
            background: linear-gradient(135deg, #4CAF50, #45a049);
            color: white;
            padding: 12px 28px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(76, 175, 80, 0.4);
        }

        .btn-logout {
            background: #f44336;
            color: white;
            padding: 10px 25px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
        }

        /* ========== ALERT ========== */
        .alert {
            padding: 15px 20px;
            margin-bottom: 25px;
            border-radius: 10px;
            background: #e8f5e9;
            color: #2e7d32;
            border-left: 4px solid #4CAF50;
            font-weight: 500;
        }

        /* ========== CONTENT SECTION ========== */
        .content-section {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .section-header h2 {
            font-size: 24px;
            color: #333;
        }

        .total-count {
            background: #E3F2FD;
            color: #2196F3;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
        }

        /* ========== TABLE ========== */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
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

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .empty-state h3 {
            font-size: 24px;
            margin-bottom: 10px;
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
                <li><a href="dashboard.php">📊 Dashboard</a></li>
                <li><a href="destinations.php" class="active">🏖️ Destinations</a></li>
                <li><a href="categories.php">📁 Categories</a></li>
                <li><a href="users.php">👥 Users</a></li>
                <li><a href="../index.php">🏠 Back to Website</a></li>
            </ul>
        </aside>

        <!-- ========== MAIN CONTENT ========== -->
        <main class="main-content">
            <div class="header">
                <h1>🏖️ Manage Destinations</h1>
                <div class="header-actions">
                    <a href="add_destination.php" class="btn-add">+ Add Destination</a>
                    <a href="../logout.php" class="btn-logout">Logout</a>
                </div>
            </div>

            <?php if (isset($_GET['msg'])): ?>
                <div class="alert">
                    ✓ <?php echo htmlspecialchars($_GET['msg']); ?>
                </div>
            <?php endif; ?>

            <div class="content-section">
                <div class="section-header">
                    <h2>All Destinations</h2>
                    <span class="total-count"><?php echo $total_destinations; ?> Total</span>
                </div>

                <?php if ($total_destinations > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Location</th>
                            <th>Rating</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($destinations)): ?>
                        <tr>
                            <td><strong>#<?php echo $row['id']; ?></strong></td>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo $row['category_name']; ?></td>
                            <td><?php echo $row['location']; ?></td>
                            <td>⭐ <?php echo $row['rating']; ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="edit_destination.php?id=<?php echo $row['id']; ?>" class="btn-edit">Edit</a>
                                    <button class="btn-delete" onclick="confirmDelete(<?php echo $row['id']; ?>, '<?php echo addslashes($row['name']); ?>')">Delete</button>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-state">
                    <h3>📭 No destinations found</h3>
                    <p>Start by adding your first destination</p>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
        function confirmDelete(id, name) {
            if (confirm('Are you sure you want to delete "' + name + '"?\n\nThis action cannot be undone and will also remove:\n- All favorites related to this destination\n- All gallery images\n- All user interactions')) {
                window.location.href = 'delete_destination.php?id=' + id;
            }
        }
    </script>
</body>
</html>