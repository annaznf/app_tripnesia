<?php

 //Menambah/menghapus destinasi dari favorite//
 
require_once 'config.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect('login.php');
}

// Process toggle favorite
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['destination_id'])) {
    $user_id = $_SESSION['user_id'];
    $destination_id = intval($_POST['destination_id']);
    
    // Check if already favorited
    $check = mysqli_query($conn, "SELECT * FROM favorites WHERE user_id=$user_id AND destination_id=$destination_id");
    
    if (mysqli_num_rows($check) > 0) {
        // Remove from favorites
        mysqli_query($conn, "DELETE FROM favorites WHERE user_id=$user_id AND destination_id=$destination_id");
        $message = "Removed from favorites!";
        $type = "success";
    } else {
        // Add to favorites
        mysqli_query($conn, "INSERT INTO favorites (user_id, destination_id) VALUES ($user_id, $destination_id)");
        $message = "Added to favorites!";
        $type = "success";
    }
    
    // Redirect back to previous page
    $return_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'destination.php';
    $separator = strpos($return_url, '?') !== false ? '&' : '?';
    
    header("Location: " . $return_url . $separator . "msg=" . urlencode($message) . "&type=" . $type);
    exit();
}

redirect('destination.php');
?>