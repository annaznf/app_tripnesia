<?php

session_start();


$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'db_travel';

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// ============================================
// HELPER FUNCTIONS
// ============================================

/**
 * Check if user is logged in
 * @return boolean
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if user is admin
 * @return boolean
 */
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

/**
 * Redirect to another page
 * @param string $page - Page to redirect
 */
function redirect($page) {
    header("Location: " . $page);
    exit();
}

/**
 * Sanitize input data
 * @param string $data - Data to sanitize
 * @return string
 */
function sanitize($data) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($data));
}

/**
 * Display alert message
 * @param string $message - Message to display
 * @param string $type - Type of alert (success/error)
 */
function showAlert($message, $type = 'success') {
    $bgColor = $type === 'success' ? '#e8f5e9' : '#ffebee';
    $textColor = $type === 'success' ? '#2e7d32' : '#c62828';
    $borderColor = $type === 'success' ? '#66bb6a' : '#ef5350';
    
    echo "
    <div style='padding: 15px; margin-bottom: 20px; border-radius: 5px; 
         background: $bgColor; color: $textColor; border: 1px solid $borderColor; text-align: center;'>
        $message
    </div>";
}
?>