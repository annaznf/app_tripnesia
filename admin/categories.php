<?php

require_once '../config.php';

// Check admin access
if (!isAdmin()) {
    redirect('../login.php');
}

$message = '';
$message_type = '';

// Handle add category
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_category'])) {
    $name = sanitize($_POST['name']);
    $description = sanitize($_POST['description']);
    
    if (empty($name)) {
        $message = "Category name is required!";
        $message_type = "error";
    } else {
        mysqli_query($conn, "INSERT INTO categories (name, description) VALUES ('$name', '$description')");
        $message = "Category added successfully!";
        $message_type = "success";
    }
}

// Handle delete category
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM categories WHERE id=$id");
    $message = "Category deleted successfully!";
    $message_type = "success";
}

// Get all categories with destination count
$categories = mysqli_query($conn, "
    SELECT c.*, COUNT(d.id) as total_destinations 
    FROM categories c 
    LEFT JOIN destinations d ON c.id = d.category_id 
    GROUP BY c.id
    ORDER BY c.name
");

$total_categories = mysqli_num_rows($categories);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories - Admin</title>
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
            font-weight: 500;
        }

        .alert-success {
            background: #e8f5e9;
            color: #2e7d32;
            border-left: 4px solid #4CAF50;
        }

        .alert-error {
            background: #ffebee;
            color: #c62828;
            border-left: 4px solid #f44336;
        }

        /* ========== CONTENT GRID ========== */
        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        .section {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .section h2 {
            margin-bottom: 25px;
            color: #333;
            font-size: 24px;
        }

        /* ========== FORM ========== */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            color: #555;
            font-weight: 600;
            font-size: 14px;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            font-family: inherit;
            transition: all 0.3s;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #2196F3;
            background: #f8f9ff;
        }

        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }

        .btn-primary {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #2196F3, #1976D2);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(33, 150, 243, 0.4);
        }

        /* ========== CATEGORY LIST ========== */
        .category-list {
            list-style: none;
        }

        .category-item {
            padding: 20px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s;
        }

        .category-item:hover {
            background: #fafafa;
        }

        .category-info h3 {
            color: #333;
            margin-bottom: 8px;
            font-size: 18px;
        }

        .category-info p {
            color: #666;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .category-stats {
            background: #E3F2FD;
            color: #2196F3;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
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
            padding: 40px 20px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- ========== SIDEBAR ========== -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>🎯 Triflers Admin</h2>
                <p>Management Panel</p>
            </div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php">📊 Dashboard</a></li>
                <li><a href="destinations.php">🏖️ Destinations</a></li>
                <li><a href="categories.php" class="active">📁 Categories</a></li>
                <li><a href="users.php">👥 Users</a></li>
                <li><a href="../index.php">🏠 Back to Website</a></li>
            </ul>
        </aside>

        <!-- ========== MAIN CONTENT ========== -->
        <main class="main-content">
            <div class="header">
                <h1>📁 Manage Categories</h1>
                <a href="../logout.php" class="btn-logout">Logout</a>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?>">
                    <?php echo $message_type == 'success' ? '✓' : '⚠️'; ?> <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <div class="content-grid">
                <!-- ========== ADD CATEGORY FORM ========== -->
                <div class="section">
                    <h2>➕ Add New Category</h2>
                    <form method="POST" action="">
                        <div class="form-group">
                            <label>Category Name *</label>
                            <input type="text" name="name" placeholder="e.g. Beach, Mountain, City" required>
                        </div>

                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" placeholder="Brief description about this category..."></textarea>
                        </div>

                        <button type="submit" name="add_category" class="btn-primary">
                            Add Category
                        </button>
                    </form>
                </div>

                <!-- ========== CATEGORY LIST ========== -->
                <div class="section">
                    <h2>📋 All Categories (<?php echo $total_categories; ?>)</h2>
                    <ul class="category-list">
                        <?php if ($total_categories > 0): ?>
                            <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
                            <li class="category-item">
                                <div class="category-info">
                                    <h3><?php echo $cat['name']; ?></h3>
                                    <p><?php echo $cat['description'] ?: 'No description'; ?></p>
                                    <span class="category-stats">
                                        <?php echo $cat['total_destinations']; ?> destinations
                                    </span>
                                </div>
                                <button class="btn-delete" onclick="confirmDelete(<?php echo $cat['id']; ?>, '<?php echo addslashes($cat['name']); ?>')">
                                    Delete
                                </button>
                            </li>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <li class="category-item">
                                <div class="empty-state">
                                    <p>No categories found</p>
                                </div>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </main>
    </div>

    <script>
        function confirmDelete(id, name) {
            if (confirm('Are you sure you want to delete "' + name + '"?\n\nAll destinations in this category will lose their category assignment.')) {
                window.location.href = 'categories.php?delete=' + id;
            }
        }
    </script>
</body>
</html>