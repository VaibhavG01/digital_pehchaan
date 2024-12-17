<?php
// Include database connection
include '../config/db_connection.php'; // Adjust the path as needed

// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Get logged-in user ID
$user_id = $_SESSION['user_id'];

// Check if profile design is submitted
if (isset($_POST['profile_design'])) {
    $profile_design = $_POST['profile_design'];

    // Update the profile design in the database
    $sql = "UPDATE profiles SET profile_design = ? WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $profile_design, $user_id);

    if ($stmt->execute()) {
        // Redirect back to view_profile.php with a success message
        header("Location: view_profile.php?status=success");
        exit;
    } else {
        // Redirect back with an error message
        header("Location: view_profile.php?status=error");
        exit;
    }
} else {
    // Redirect back if no design is selected
    header("Location: view_profile.php?status=notselected");
    exit;
}
?>
