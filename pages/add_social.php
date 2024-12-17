<?php
// Start the session
session_start();
include '../config/db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Get user_id from session

// Set the number of items per page
$items_per_page = 5;

// Get the current page number from the URL, default is 1
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $items_per_page;

// Check if the form is submitted
if (isset($_POST['submit'])) {
    $platform = $_POST['platform'];
    $url = $_POST['url'];
    $icon_class = $_POST['icon_class'];

    // Insert the new social link into the database
    $sql = "INSERT INTO social_links (user_id, platform, url, icon_class) 
            VALUES (?, ?, ?, ?);";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $user_id, $platform, $url, $icon_class);
    if ($stmt->execute()) {
        header('location: dashboard.php');
    } else {
        echo "<div class='bg-red-100 text-red-700 p-3 rounded-lg'>Error adding social link.</div>";
    }
}

// Delete the social link if 'delete' is set
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $sql = "DELETE FROM social_links WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $delete_id);
    if ($stmt->execute()) {
        echo "<div class='bg-red-100 text-red-700 p-3 rounded-lg'>Social link deleted successfully!</div>";
    } else {
        echo "<div class='bg-red-100 text-red-700 p-3 rounded-lg'>Error deleting social link.</div>";
    }
}

// Fetch the total number of social links
$sql_total = "SELECT COUNT(*) AS total FROM social_links WHERE user_id = ?";
$stmt_total = $conn->prepare($sql_total);
$stmt_total->bind_param("i", $user_id);
$stmt_total->execute();
$total_result = $stmt_total->get_result();
$total_row = $total_result->fetch_assoc();
$total_items = $total_row['total'];

// Calculate the total number of pages
$total_pages = ceil($total_items / $items_per_page);

// Fetch the social links with pagination
$sql = "SELECT * FROM social_links WHERE user_id = ? LIMIT ?, ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $user_id, $offset, $items_per_page);
$stmt->execute();
$result = $stmt->get_result();
?>

<?php include '../includes/header.php'; ?>
<body>
<div class="flex flex-wrap min-h-screen">
    <!-- Sidebar -->
    <?php include '../includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="flex-1 p-6">
        <div class="max-w-4xl mx-auto bg-white shadow-lg rounded-lg p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Add Social Link</h2>
            <form method="POST" action="">
                <label for="platform" class="block text-sm font-medium text-gray-700 mb-2">Platform</label>
                <select id="platform" name="platform" class="w-full border border-gray-300 rounded-lg p-2 mb-4 focus:ring-2 focus:ring-purple-400">
                    <option value="Instagram" data-icon="ri-instagram-fill">Instagram</option>
                    <option value="Facebook" data-icon="ri-facebook-fill">Facebook</option>
                    <option value="Twitter" data-icon="ri-twitter-fill">Twitter</option>
                    <option value="LinkedIn" data-icon="ri-linkedin-fill">LinkedIn</option>
                    <option value="YouTube" data-icon="ri-youtube-fill">YouTube</option>
                    <option value="GitHub" data-icon="ri-github-fill">GitHub</option>
                    <option value="Discord" data-icon="ri-discord-fill">Discord</option>
                    <option value="Website" data-icon="ri-global-line">Website</option>
                    <option value="WhatsApp" data-icon="ri-whatsapp-fill">WhatsApp</option>
                    <option value="Mobile" data-icon="ri-phone-fill">Mobile</option>
                </select>

                <label for="url" class="block text-sm font-medium text-gray-700 mb-2">URL</label>
                <input type="text" id="url" name="url" required 
                       class="w-full border border-gray-300 rounded-lg p-2 mb-4 focus:ring-2 focus:ring-purple-400">

                <label for="icon_class" class="block text-sm font-medium text-gray-700 mb-2">Icon Class</label>
                <input type="text" id="icon_class" name="icon_class" required readonly 
                       class="w-full border border-gray-300 rounded-lg p-2 mb-4 bg-gray-100 focus:ring-0">

                <button type="submit" name="submit" 
                        class="w-full bg-purple-500 hover:bg-purple-600 text-white font-medium rounded-lg p-3">Add Social Link</button>
            </form>

            <!-- Display Social Links Table -->
            <h3 class="text-xl font-bold text-gray-800 mt-8 mb-4">Your Social Links</h3>
            <table class="min-w-full border-collapse">
                <thead>
                    <tr>
                        <th class="border-b text-left py-2 px-4">Platform</th>
                        <th class="border-b text-left py-2 px-4">URL</th>
                        <th class="border-b text-left py-2 px-4">Icon</th>
                        <th class="border-b text-left py-2 px-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td class="border-b py-2 px-4"><?php echo $row['platform']; ?></td>
                        <td class="border-b py-2 px-4"><?php echo $row['url']; ?></td>
                        <td class="border-b py-2 px-4"><i class="<?php echo $row['icon_class']; ?>"></i></td>
                        <td class="border-b py-2 px-4">
                            <a href="edit_social.php?id=<?php echo $row['id']; ?>" class="text-blue-500">Edit</a> |
                            <a href="?delete_id=<?php echo $row['id']; ?>" class="text-red-500">Delete</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>

            <!-- Pagination Links -->
            <div class="mt-4 flex justify-between items-center">
                <div>
                    <span class="text-sm text-gray-600">Page <?php echo $page; ?> of <?php echo $total_pages; ?></span>
                </div>
                <div class="flex space-x-2">
                    <?php if ($page > 1) { ?>
                        <a href="?page=1" class="bg-purple-500 text-white p-2 rounded-lg">First</a>
                        <a href="?page=<?php echo $page - 1; ?>" class="bg-purple-500 text-white p-2 rounded-lg">Prev</a>
                    <?php } ?>
                    <?php if ($page < $total_pages) { ?>
                        <a href="?page=<?php echo $page + 1; ?>" class="bg-purple-500 text-white p-2 rounded-lg">Next</a>
                        <a href="?page=<?php echo $total_pages; ?>" class="bg-purple-500 text-white p-2 rounded-lg">Last</a>
                    <?php } ?>
                </div>
            </div>
        </div>
    </main>
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