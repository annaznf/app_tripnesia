<?php

require_once 'config.php';

$error = '';

// Process login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email']);
    $password = md5($_POST['password']);
    
    // Validate input
    if (empty($email) || empty($_POST['password'])) {
        $error = "Email and password are required!";
    } else {
        $sql = "SELECT * FROM users WHERE email='$email' AND password='$password'";
        $result = mysqli_query($conn, $sql);
        
        if (mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);
            
            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            
            // Redirect based on role
            if ($user['role'] == 'admin') {
                redirect('admin/dashboard.php');
            } else {
                redirect('index.php');
            }
        } else {
            $error = "Invalid email or password!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Tripnesia Travel</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            overflow: hidden;
        }

        .container {
            display: flex;
            width: 100%;
            height: 100%;
        }

        .left-section {
            flex: 1;
            background: linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.3)), 
                        url('https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=800') center/cover;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo {
            color: white;
            font-size: 48px;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }

        .right-section {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8f9fa;
        }

        .form-container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }

        h2 {
            text-align: center;
            margin-bottom: 10px;
            color: #333;
            font-size: 28px;
        }

        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-size: 14px;
            font-weight: 500;
        }

        input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
        }

        input:focus {
            outline: none;
            border-color: #2196F3;
            background: #f8f9ff;
        }

        .password-container {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
            font-size: 18px;
            user-select: none;
        }

        .forgot-password {
            text-align: right;
            margin-top: -10px;
            margin-bottom: 15px;
        }

        .forgot-password a {
            color: #2196F3;
            text-decoration: none;
            font-size: 13px;
        }

        .forgot-password a:hover {
            text-decoration: underline;
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #2196F3 0%, #1976D2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(33, 150, 243, 0.4);
        }

        .links {
            text-align: center;
            margin-top: 25px;
            color: #666;
        }

        .links a {
            color: #2196F3;
            text-decoration: none;
            font-weight: 600;
        }

        .links a:hover {
            text-decoration: underline;
        }

        .alert {
            padding: 12px 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            text-align: center;
            background: #ffebee;
            color: #c62828;
            border: 1px solid #ef5350;
            font-size: 14px;
        }

        .demo-accounts {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
        }

        .demo-accounts h4 {
            color: #1976D2;
            margin-bottom: 8px;
        }

        .demo-accounts p {
            margin: 5px 0;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="left-section">
            <div class="logo">Tripnesia</div>
        </div>
        
        <div class="right-section">
            <div class="form-container">
                <h2>Welcome Back!</h2>
                <p class="subtitle">Login to continue your journey</p>
                
                <?php if ($error): ?>
                    <div class="alert"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="demo-accounts">
                    <h4>🔑 Demo Accounts:</h4>
                    <p><strong>User:</strong> user@gmail.com / user123</p>
                </div>

                <form method="POST" action="">
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email" placeholder="Enter your email" required>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <div class="password-container">
                            <input type="password" id="password" name="password" placeholder="Enter your password" required>
                            <span class="toggle-password" onclick="togglePassword()">👁️</span>
                        </div>
                    </div>

                    <div class="forgot-password">
                        <a href="#">Forgot password?</a>
                    </div>

                    <button type="submit" class="btn-submit">Login Now</button>

                    <div class="links">
                        Don't have an account? <a href="register.php">Register here</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.querySelector('.toggle-password');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.textContent = '🙈';
            } else {
                passwordInput.type = 'password';
                toggleIcon.textContent = '👁️';
            }
        }
    </script>
</body>
</html>