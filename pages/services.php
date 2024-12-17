<?php
require '../config/db_connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch Services for the logged-in user
$sql = "SELECT * FROM services WHERE user_id = $user_id";
$result = $conn->query($sql);

// Handle delete action
if (isset($_GET['delete_id'])) {
    $service_id = $_GET['delete_id'];

    // Delete service from database
    $delete_sql = "DELETE FROM services WHERE service_id = $service_id AND user_id = $user_id";
    if ($conn->query($delete_sql) === TRUE) {
        $_SESSION['message'] = "Service deleted successfully!";
    } else {
        $_SESSION['message'] = "Error deleting service!";
    }

    header("Location: services.php");
    exit;
}
?>
<!-- Sidebar -->
<?php include '../includes/header.php'; ?>
<body class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white">


<div class="flex h-screen">
    <!-- Sidebar -->
    <?php include '../includes/sidebar.php'; ?>

    <!-- Main Content -->
    <main class="flex-1 bg-white dark:bg-gray-900">
        <div class="container mx-auto p-6 max-w-7xl">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-3xl font-bold">Your Services</h2>
                <!-- Add Service Button -->
                <a href="add_service.php" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition duration-300">Add Service</a>
            </div>

            <!-- Display Message if any -->
            <?php if (isset($_SESSION['message'])): ?>
                <div class="bg-green-500 text-white p-4 rounded-md mb-6">
                    <?php echo $_SESSION['message']; ?>
                    <?php unset($_SESSION['message']); ?>
                </div>
            <?php endif; ?>

            <!-- Services Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php while ($service = $result->fetch_assoc()) : ?>
                    <div class="bg-white shadow-lg rounded-lg overflow-hidden dark:bg-gray-800 dark:text-white">
                        <img src="../assets/uploads/<?php echo $service['image']; ?>" alt="<?php echo htmlspecialchars($service['title']); ?>" class="w-full h-48 object-cover">
                        <div class="p-4">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100"><?php echo htmlspecialchars($service['title']); ?></h3>
                            <p class="text-gray-600 dark:text-gray-400 mt-2"><?php echo htmlspecialchars($service['description']); ?></p>
                            
                            <!-- Edit and Delete buttons -->
                            <div class="mt-4 flex justify-between items-center">
                                <a href="edit_service.php?id=<?php echo $service['service_id']; ?>" class="text-blue-500 hover:text-blue-600 font-medium">Edit</a>
                                <a href="?delete_id=<?php echo $service['service_id']; ?>" 
                                   onclick="return confirm('Are you sure you want to delete this service?');" 
                                   class="text-red-500 hover:text-red-600 font-medium">Delete</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </main>
</div>

<script>
    const toggleButton = document.getElementById('dark-mode-toggle');
    const darkModeIcon = document.getElementById('dark-mode-icon');

    toggleButton.addEventListener('click', () => {
        if (document.documentElement.classList.contains('dark')) {
            document.documentElement.classList.remove('dark');
            darkModeIcon.classList.remove('fa-sun');
            darkModeIcon.classList.add('fa-moon');
        } else {
            document.documentElement.classList.add('dark');
            darkModeIcon.classList.remove('fa-moon');
            darkModeIcon.classList.add('fa-sun');
        }
    });
</script>
</body>
</html>
