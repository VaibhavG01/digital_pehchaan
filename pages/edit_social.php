<?php
// Start the session
session_start();
include '../config/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Get user_id from session

// Check if the ID parameter is provided
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch the current social link details
    $sql = "SELECT * FROM social_links WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        // No link found for this ID, redirect or show an error
        echo "<div class='bg-red-100 text-red-700 p-3 rounded-lg'>Social link not found.</div>";
        exit();
    }

    // Fetch the current data
    $row = $result->fetch_assoc();
    $platform = $row['platform'];
    $url = $row['url'];
    $icon_class = $row['icon_class'];

    // Update the social link if the form is submitted
    if (isset($_POST['submit'])) {
        $platform = $_POST['platform'];
        $url = $_POST['url'];
        $icon_class = $_POST['icon_class'];

        $sql_update = "UPDATE social_links SET platform = ?, url = ?, icon_class = ? WHERE id = ? AND user_id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("sssii", $platform, $url, $icon_class, $id, $user_id);

        if ($stmt_update->execute()) {
            echo "<div class='bg-green-100 text-green-700 p-3 rounded-lg'>Social link updated successfully!</div>";
        } else {
            echo "<div class='bg-red-100 text-red-700 p-3 rounded-lg'>Error updating social link.</div>";
        }
    }
} else {
    // Redirect if no ID is provided
    header("Location: social_links.php");
    exit();
}
?>

<?php include '../includes/header.php'; ?>
<body>
<div class="max-w-4xl mx-auto bg-white shadow-lg rounded-lg p-6">
    <h2 class="text-2xl font-bold text-gray-800 mb-4">Edit Social Link</h2>
    <form method="POST" action="">
        <label for="platform" class="block text-sm font-medium text-gray-700 mb-2">Platform</label>
        <select id="platform" name="platform" class="w-full border border-gray-300 rounded-lg p-2 mb-4 focus:ring-2 focus:ring-purple-400">
            <option value="Instagram" <?php echo $platform == 'Instagram' ? 'selected' : ''; ?> data-icon="ri-instagram-fill">Instagram</option>
            <option value="Facebook" <?php echo $platform == 'Facebook' ? 'selected' : ''; ?> data-icon="ri-facebook-fill">Facebook</option>
            <option value="Twitter" <?php echo $platform == 'Twitter' ? 'selected' : ''; ?> data-icon="ri-twitter-fill">Twitter</option>
            <option value="LinkedIn" <?php echo $platform == 'LinkedIn' ? 'selected' : ''; ?> data-icon="ri-linkedin-fill">LinkedIn</option>
            <option value="YouTube" <?php echo $platform == 'YouTube' ? 'selected' : ''; ?> data-icon="ri-youtube-fill">YouTube</option>
            <option value="GitHub" <?php echo $platform == 'GitHub' ? 'selected' : ''; ?> data-icon="ri-github-fill">GitHub</option>
            <option value="Discord" <?php echo $platform == 'Discord' ? 'selected' : ''; ?> data-icon="ri-discord-fill">Discord</option>
            <option value="Website" <?php echo $platform == 'Website' ? 'selected' : ''; ?> data-icon="ri-global-line">Website</option>
            <option value="WhatsApp" <?php echo $platform == 'WhatsApp' ? 'selected' : ''; ?> data-icon="ri-whatsapp-fill">WhatsApp</option>
            <option value="Mobile" <?php echo $platform == 'Mobile' ? 'selected' : ''; ?> data-icon="ri-phone-fill">Mobile</option>
        </select>

        <label for="url" class="block text-sm font-medium text-gray-700 mb-2">URL</label>
        <input type="text" id="url" name="url" required value="<?php echo $url; ?>" 
               class="w-full border border-gray-300 rounded-lg p-2 mb-4 focus:ring-2 focus:ring-purple-400">

        <label for="icon_class" class="block text-sm font-medium text-gray-700 mb-2">Icon Class</label>
        <input type="text" id="icon_class" name="icon_class" required value="<?php echo $icon_class; ?>" readonly 
               class="w-full border border-gray-300 rounded-lg p-2 mb-4 bg-gray-100 focus:ring-0">

        <button type="submit" name="submit" 
                class="w-full bg-purple-500 hover:bg-purple-600 text-white font-medium rounded-lg p-3">Update Social Link</button>
    </form>
</div>

<script>
    // Update the icon_class input based on the selected platform
    document.getElementById('platform').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const iconClassInput = document.getElementById('icon_class');
        iconClassInput.value = selectedOption.getAttribute('data-icon');
    });

    // Set the default icon class when the page loads
    window.addEventListener('load', function() {
        const platformSelect = document.getElementById('platform');
        const selectedOption = platformSelect.options[platformSelect.selectedIndex];
        const iconClassInput = document.getElementById('icon_class');
        iconClassInput.value = selectedOption.getAttribute('data-icon');
    });
</script>
</body>
</html>
