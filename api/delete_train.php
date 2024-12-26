<?php
// Include database connection and other necessary files
include('db.php'); // Include your database connection file

// Check if the train ID is provided
if (isset($_GET['id'])) {
    $train_id = $_GET['id'];

    // Delete the train from the database
    $delete_query = "DELETE FROM trains WHERE id = '$train_id'";

    if (mysqli_query($con, $delete_query)) {
        $_SESSION['success'] = 'Train deleted successfully!';
    } else {
        $_SESSION['error'] = 'Failed to delete the train. Please try again.';
    }
} else {
    $_SESSION['error'] = 'No train ID provided.';
}

// Redirect to the all trains page
header('Location: ../all_trains.php');
exit;
?>
