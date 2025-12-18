<?php

require_once '../config.php';

if (!isAdmin()) {
    redirect('../login.php');
}


$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name");

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = sanitize($_POST['name']);
    $category_id = intval($_POST['category_id']);
    $location = sanitize($_POST['location']);
    $description = sanitize($_POST['description']);
    $rating = floatval($_POST['rating']);
    
    $image_name = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        if (in_array(strtolower($filetype), $allowed)) {
            
            $upload_dir = '../uploads/destinations/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            
            $image_name = time() . '_' . uniqid() . '.' . $filetype;
            $upload_path = $upload_dir . $image_name;
            
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $error = "Failed to upload image!";
            }
        } else {
            $error = "Invalid file type. Only JPG, JPEG, PNG & GIF allowed!";
        }
    }
    
    
    if (empty($error)) {
        if (empty($name) || empty($location) || empty($description)) {
            $error = "All fields are required!";
        } elseif ($rating < 0 || $rating > 5) {
            $error = "Rating must be between 0 and 5!";
        } else {
            $sql = "INSERT INTO destinations (category_id, name, description, location, image, rating) 
                    VALUES ($category_id, '$name', '$description', '$location', '$image_name', $rating)";
            
            if (mysqli_query($conn, $sql)) {
                redirect('destinations.php?msg=Destination added successfully!');
            } else {
                $error = "Failed to add destination. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Destination - Admin</title>
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

        /* ========== FORM CONTAINER ========== */
        .form-container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            max-width: 900px;
        }

        .form-header {
            margin-bottom: 30px;
        }

        .form-header h2 {
            font-size: 24px;
            color: #333;
            margin-bottom: 8px;
        }

        .form-header p {
            color: #666;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            color: #555;
            font-weight: 600;
            font-size: 14px;
        }

        .form-group label span {
            color: #f44336;
        }

        .form-group input,
        .form-group select,
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
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #2196F3;
            background: #f8f9ff;
        }

        .form-group textarea {
            min-height: 150px;
            resize: vertical;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }

        /* ========== IMAGE UPLOAD ========== */
        .image-upload-container {
            border: 3px dashed #e0e0e0;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            transition: all 0.3s;
            cursor: pointer;
            background: #fafafa;
        }

        .image-upload-container:hover {
            border-color: #2196F3;
            background: #f8f9ff;
        }

        .image-upload-container.dragover {
            border-color: #2196F3;
            background: #e3f2fd;
        }

        .upload-icon {
            font-size: 48px;
            margin-bottom: 15px;
            color: #2196F3;
        }

        .upload-text h4 {
            color: #333;
            margin-bottom: 8px;
            font-size: 18px;
        }

        .upload-text p {
            color: #666;
            font-size: 14px;
        }

        #image {
            display: none;
        }

        .image-preview {
            margin-top: 20px;
            display: none;
        }

        .image-preview img {
            max-width: 100%;
            max-height: 300px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }

        .image-preview .remove-image {
            display: inline-block;
            margin-top: 15px;
            padding: 8px 20px;
            background: #f44336;
            color: white;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            border: none;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 35px;
            padding-top: 25px;
            border-top: 2px solid #f0f0f0;
        }

        .btn {
            padding: 14px 35px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #2196F3, #1976D2);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(33, 150, 243, 0.4);
        }

        .btn-secondary {
            background: #e0e0e0;
            color: #666;
        }

        .btn-secondary:hover {
            background: #d0d0d0;
        }

        /* ========== ALERT ========== */
        .alert {
            padding: 15px 20px;
            margin-bottom: 25px;
            border-radius: 10px;
            font-weight: 500;
        }

        .alert-error {
            background: #ffebee;
            color: #c62828;
            border-left: 4px solid #f44336;
        }

        .helper-text {
            font-size: 13px;
            color: #999;
            margin-top: 8px;
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
                <li><a href="destinations.php" class="active">🏖️ Destinations</a></li>
                <li><a href="categories.php">📁 Categories</a></li>
                <li><a href="users.php">👥 Users</a></li>
                <li><a href="../index.php">🏠 Back to Website</a></li>
            </ul>
        </aside>

        <!-- ========== MAIN CONTENT ========== -->
        <main class="main-content">
            <div class="header">
                <h1>➕ Add New Destination</h1>
                <a href="../logout.php" class="btn-logout">Logout</a>
            </div>

            <div class="form-container">
                <div class="form-header">
                    <h2>Destination Information</h2>
                    <p>Fill in the details below to add a new destination</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-error">⚠️ <?php echo $error; ?></div>
                <?php endif; ?>

                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Destination Name <span>*</span></label>
                        <input type="text" name="name" placeholder="e.g. Nusa Dua Beach" required>
                        <p class="helper-text">Enter the full name of the destination</p>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Category <span>*</span></label>
                            <select name="category_id" required>
                                <option value="">Select Category</option>
                                <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
                                <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Location <span>*</span></label>
                            <input type="text" name="location" placeholder="e.g. Bali, Indonesia" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Description <span>*</span></label>
                        <textarea name="description" placeholder="Describe the destination, its attractions, and what makes it special..." required></textarea>
                        <p class="helper-text">Provide a detailed description (minimum 50 characters)</p>
                    </div>

                    <div class="form-group">
                        <label>Rating (0-5) <span>*</span></label>
                        <input type="number" name="rating" step="0.1" min="0" max="5" placeholder="e.g. 4.5" required>
                        <p class="helper-text">Enter a rating between 0.0 and 5.0</p>
                    </div>

                    <!-- ========== IMAGE UPLOAD ========== -->
                    <div class="form-group">
                        <label>Destination Image</label>
                        <div class="image-upload-container" onclick="document.getElementById('image').click()">
                            <div class="upload-icon">📸</div>
                            <div class="upload-text">
                                <h4>Click to upload or drag and drop</h4>
                                <p>PNG, JPG, GIF up to 10MB</p>
                            </div>
                            <input type="file" id="image" name="image" accept="image/*">
                        </div>
                        <div class="image-preview" id="imagePreview">
                            <img src="" alt="Preview" id="previewImg">
                            <br>
                            <button type="button" class="remove-image" onclick="removeImage()">Remove Image</button>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">💾 Save Destination</button>
                        <a href="destinations.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        // Image preview functionality
        const imageInput = document.getElementById('image');
        const imagePreview = document.getElementById('imagePreview');
        const previewImg = document.getElementById('previewImg');
        const uploadContainer = document.querySelector('.image-upload-container');

        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    imagePreview.style.display = 'block';
                    uploadContainer.style.display = 'none';
                }
                reader.readAsDataURL(file);
            }
        });

        function removeImage() {
            imageInput.value = '';
            imagePreview.style.display = 'none';
            uploadContainer.style.display = 'block';
        }

        // Drag and drop functionality
        uploadContainer.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('dragover');
        });

        uploadContainer.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
        });

        uploadContainer.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                imageInput.files = files;
                const event = new Event('change', { bubbles: true });
                imageInput.dispatchEvent(event);
            }
        });
    </script>
</body>
</html>
