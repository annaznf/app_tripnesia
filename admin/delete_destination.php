<?php


require_once '../config.php';

// Check admin access
if (!isAdmin()) {
    redirect('../login.php');
}

// Check if ID is provided
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Get destination name for confirmation message
    $dest_query = mysqli_query($conn, "SELECT name FROM destinations WHERE id = $id");
    
    if (mysqli_num_rows($dest_query) > 0) {
        $dest = mysqli_fetch_assoc($dest_query);
        $dest_name = $dest['name'];
        
        // Delete related favorites first
        mysqli_query($conn, "DELETE FROM favorites WHERE destination_id = $id");
        
        // Delete related gallery images
        mysqli_query($conn, "DELETE FROM gallery WHERE destination_id = $id");
        
        // Delete the destination
        if (mysqli_query($conn, "DELETE FROM destinations WHERE id = $id")) {
            redirect('destinations.php?msg=Destination "' . urlencode($dest_name) . '" deleted successfully!');
        } else {
            redirect('destinations.php?msg=Failed to delete destination&type=error');
        }
    } else {
        redirect('destinations.php?msg=Destination not found&type=error');
    }
} else {
    redirect('destinations.php');
}
?>