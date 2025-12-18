<?php


require_once '../config.php';

// Check admin access
if (!isAdmin()) {
    redirect('../login.php');
}

$message = '';
$message_type = '';

// Handle delete user
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    
    // Prevent admin from deleting themselves
    if ($id == $_SESSION['user_id']) {
        $message = "You cannot delete your own account!";
        $message_type = "error";
    } else {
        // Get username before delete
        $user_query = mysqli_query($conn, "SELECT username FROM users WHERE id=$id");
        if (mysqli_num_rows($user_query) > 0) {
            $user = mysqli_fetch_assoc($user_query);
            $username = $user['username'];
            
            // Delete user (favorites will be deleted automatically by CASCADE)
            if (mysqli_query($conn, "DELETE FROM users WHERE id=$id")) {
                $message = "User '$username' deleted successfully!";
                $message_type = "success";
            } else {
                $message = "Failed to delete user!";
                $message_type = "error";
            }
        }
    }
}

// Handle change role
if (isset($_GET['change_role'])) {
    $id = intval($_GET['change_role']);
    $new_role = $_GET['role'] == 'admin' ? 'user' : 'admin';
    
    // Prevent admin from changing their own role
    if ($id == $_SESSION['user_id']) {
        $message = "You cannot change your own role!";
        $message_type = "error";
    } else {
        if (mysqli_query($conn, "UPDATE users SET role='$new_role' WHERE id=$id")) {
            $message = "User role updated to $new_role!";
            $message_type = "success";
        }
    }
}

// Get all users with statistics
$users = mysqli_query($conn, "
    SELECT u.*, 
           COUNT(DISTINCT f.id) as total_favorites,
           DATE_FORMAT(u.created_at, '%d %M %Y') as joined_date
    FROM users u
    LEFT JOIN favorites f ON u.id = f.user_id
    GROUP BY u.id
    ORDER BY u.created_at DESC
");

$total_users = mysqli_num_rows($users);

// Count by role
$admin_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='admin'"))['total'];
$user_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='user'"))['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin</title>
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

        /* ========== STATS CARDS ========== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .stat-card h3 {
            font-size: 36px;
            color: #2196F3;
            margin-bottom: 8px;
        }

        .stat-card p {
            color: #666;
            font-size: 14px;
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

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #2196F3, #1976D2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 18px;
        }

        .user-details h4 {
            color: #333;
            margin-bottom: 3px;
            font-size: 15px;
        }

        .user-details p {
            color: #999;
            font-size: 13px;
        }

        .role-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
        }

        .role-admin {
            background: #E8F5E9;
            color: #2e7d32;
        }

        .role-user {
            background: #E3F2FD;
            color: #1976D2;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
        }

        .btn-change-role {
            background: #FF9800;
            color: white;
            padding: 8px 18px;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.3s;
            border: none;
        }

        .btn-change-role:hover {
            background: #F57C00;
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

        .btn-disabled {
            background: #e0e0e0;
            color: #999;
            cursor: not-allowed;
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
                <h2>🎯 Triflers Admin</h2>
                <p>Management Panel</p>
            </div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php">📊 Dashboard</a></li>
                <li><a href="destinations.php">🏖️ Destinations</a></li>
                <li><a href="categories.php">📁 Categories</a></li>
                <li><a href="users.php" class="active">👥 Users</a></li>
                <li><a href="../index.php">🏠 Back to Website</a></li>
            </ul>
        </aside>

        <!-- ========== MAIN CONTENT ========== -->
        <main class="main-content">
            <div class="header">
                <h1>👥 Manage Users</h1>
                <a href="../logout.php" class="btn-logout">Logout</a>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-<?php echo $message_type; ?>">
                    <?php echo $message_type == 'success' ? '✓' : '⚠️'; ?> <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <!-- ========== STATS ========== -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h3><?php echo $total_users; ?></h3>
                    <p>Total Users</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $admin_count; ?></h3>
                    <p>Administrators</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo $user_count; ?></h3>
                    <p>Regular Users</p>
                </div>
            </div>

            <div class="content-section">
                <div class="section-header">
                    <h2>All Users</h2>
                </div>

                <?php if ($total_users > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Role</th>
                            <th>Favorites</th>
                            <th>Joined Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($users)): ?>
                        <tr>
                            <td>
                                <div class="user-info">
                                    <div class="user-avatar">
                                        <?php echo strtoupper(substr($row['username'], 0, 1)); ?>
                                    </div>
                                    <div class="user-details">
                                        <h4><?php echo $row['username']; ?></h4>
                                        <p><?php echo $row['email']; ?></p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="role-badge role-<?php echo $row['role']; ?>">
                                    <?php echo ucfirst($row['role']); ?>
                                </span>
                            </td>
                            <td>❤️ <?php echo $row['total_favorites']; ?></td>
                            <td><?php echo $row['joined_date']; ?></td>
                            <td>
                                <div class="action-buttons">
                                    <?php if ($row['id'] == $_SESSION['user_id']): ?>
                                        <button class="btn-change-role btn-disabled" disabled>You</button>
                                        <button class="btn-delete btn-disabled" disabled>Delete</button>
                                    <?php else: ?>
                                        <a href="?change_role=<?php echo $row['id']; ?>&role=<?php echo $row['role']; ?>" 
                                           class="btn-change-role"
                                           onclick="return confirm('Change role to <?php echo $row['role'] == 'admin' ? 'User' : 'Admin'; ?>?')">
                                            Change Role
                                        </a>
                                        <button class="btn-delete" onclick="confirmDelete(<?php echo $row['id']; ?>, '<?php echo addslashes($row['username']); ?>')">
                                            Delete
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-state">
                    <h3>👥 No users found</h3>
                    <p>There are no registered users in the system</p>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
        function confirmDelete(id, username) {
            if (confirm('Are you sure you want to delete user "' + username + '"?\n\nThis will also remove:\n- All their favorites\n- All their interactions\n\nThis action cannot be undone!')) {
                window.location.href = '?delete=' + id;
            }
        }
    </script>
</body>
</html>