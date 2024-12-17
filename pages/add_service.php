<?php
require '../config/db_connection.php';
session_start();

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id']; // Get logged-in user ID

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];

    // Handle the image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = $_FILES['image'];
        $image_name = time() . '_' . $image['name']; // Generate a unique name for the image
        $target_path = "../assets/uploads/" . $image_name;

        // Move the uploaded file to the server directory
        if (move_uploaded_file($image['tmp_name'], $target_path)) {
            // Insert the new service into the database
            $sql = "INSERT INTO services (user_id, title, description, image) 
                    VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isss", $user_id, $title, $description, $image_name);

            if ($stmt->execute()) {
                $success_message = "Service added successfully!";
            } else {
                $error_message = "There was an error adding the service. Please try again.";
            }
        } else {
            $error_message = "There was an error uploading the image. Please try again.";
        }
    } else {
        $error_message = "Please select an image for the service.";
    }
}
?>
<body class="bg-white dark:bg-gray-900 text-gray-900 dark:text-white">

<!-- Dark Mode Toggle Button -->
<div class="fixed top-4 right-4 z-50">
    <button id="dark-mode-toggle" class="px-4 py-2 bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-white rounded-full shadow-lg">
        <i id="dark-mode-icon" class="fas fa-moon"></i> <!-- Moon icon for dark mode -->
    </button>
</div>

<?php include '../includes/header.php'; ?>

<div class="container mx-auto p-6 max-w-4xl  rounded-lg shadow-lg">
    <h2 class="text-2xl font-bold mb-6">Add New Service</h2>

    <!-- Display success or error message -->
    <?php if (!empty($success_message)) : ?>
        <p class="text-green-500 mb-4"><?php echo $success_message; ?></p>
    <?php elseif (!empty($error_message)) : ?>
        <p class="text-red-500 mb-4"><?php echo $error_message; ?></p>
    <?php endif; ?>

    <!-- Service Add Form -->
    <form action="add_service.php" method="POST" enctype="multipart/form-data">
        <div class="mb-4">
            <label for="title" class="block text-sm font-medium text-gray-700">Service Title</label>
            <input type="text" id="title" name="title" class="w-full p-3 border border-gray-300 rounded-md mt-2" required>
        </div>

        <div class="mb-4">
            <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
            <textarea id="description" name="description" class="w-full p-3 border border-gray-300 rounded-md mt-2" required></textarea>
        </div>

        <div class="mb-4">
            <label for="image" class="block text-sm font-medium text-gray-700">Service Image</label>
            <input type="file" id="image" name="image" class="w-full p-3 border border-gray-300 rounded-md mt-2" required>
        </div>

        <div class="mt-6">
            <button type="submit" class="w-full sm:w-auto bg-blue-500 text-white px-6 py-3 rounded-lg hover:bg-blue-600 focus:outline-none">Add Service</button>
        </div>
    </form>
</div>

    

</body>
</html>
