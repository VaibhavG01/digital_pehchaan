<?php
// Start the session
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Get user_id from session

// Fetch all social media links for the logged-in user
$sql = "SELECT platform, url, icon_class FROM social_links WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

echo "<h2>Your Social Links</h2>";
echo "<ul>";
while ($row = $result->fetch_assoc()) {
    echo "<li><a href='" . $row['url'] . "'><i class='" . $row['icon_class'] . "'></i> " . $row['platform'] . "</a></li>";
}
echo "</ul>";
?>
    