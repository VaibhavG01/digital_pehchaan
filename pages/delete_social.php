<?php
// Start the session
session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Get user_id from session

// Check if the delete action is triggered
if (isset($_GET['delete'])) {
    $platform = $_GET['delete'];

    // Delete the social link for the logged-in user
    $sql = "DELETE FROM social_links WHERE user_id = ? AND platform = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $user_id, $platform);
    if ($stmt->execute()) {
        echo "Social link deleted successfully!";
    } else {
        echo "Error deleting social link.";
    }
}
?>

<!-- Example list of social media links with delete option -->
<?php
$sql = "SELECT platform FROM social_links WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

echo "<h2>Your Social Links</h2>";
echo "<ul>";
while ($row = $result->fetch_assoc()) {
    echo "<li>" . $row['platform'] . " <a href='?delete=" . $row['platform'] . "'>Delete</a></li>";
}
echo "</ul>";
?>
