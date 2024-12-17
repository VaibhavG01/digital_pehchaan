<?php
require '../config/db_connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle gallery form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['gallery_form'])) {
    $type = $_POST['type'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $file = $_FILES['file'];

    // Validate input
    if (empty($type) || empty($title) || $file['error'] != 0) {
        $gallery_error = "Please fill all required fields and upload a valid file!";
    } else {
        // File upload handling
        $allowed_types = ['photo' => ['jpg', 'jpeg', 'png'], 'video' => ['mp4', 'mov']];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $upload_dir = "../assets/gallery/";
        $file_name = uniqid() . '.' . $extension;
        $file_path = $upload_dir . $file_name;

        if (in_array($extension, $allowed_types[$type])) {
            if (move_uploaded_file($file['tmp_name'], $file_path)) {
                // Insert into database
                $sql = "INSERT INTO gallery (user_id, type, file_path, title, description) VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("issss", $user_id, $type, $file_name, $title, $description);

                if ($stmt->execute()) {
                    $gallery_success = "Media uploaded successfully!";
                } else {
                    $gallery_error = "Failed to upload media. Please try again.";
                }
            } else {
                $gallery_error = "File upload failed!";
            }
        } else {
            $gallery_error = "Invalid file type for the selected media!";
        }
    }
}

// Handle edit functionality
if (isset($_GET['edit'])) {
    $media_id = $_GET['edit'];
    $edit_sql = "SELECT * FROM gallery WHERE gallery_id = ? AND user_id = ?";
    $stmt = $conn->prepare($edit_sql);
    $stmt->bind_param("ii", $media_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $media_item = $result->fetch_assoc();
}

// Handle update form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_gallery'])) {
    $media_id = $_POST['media_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];

    // Update media details in the database
    $update_sql = "UPDATE gallery SET title = ?, description = ? WHERE gallery_id = ? AND user_id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("ssii", $title, $description, $media_id, $user_id);

    if ($stmt->execute()) {
        $gallery_success = "Media details updated successfully!";
    } else {
        $gallery_error = "Failed to update media details. Please try again.";
    }
}

// Handle delete functionality
if (isset($_GET['delete'])) {
    $media_id = $_GET['delete'];
    $delete_sql = "SELECT * FROM gallery WHERE gallery_id = ? AND user_id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("ii", $media_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $media_item = $result->fetch_assoc();

    if ($media_item) {
        $file_path = "../assets/gallery/" . $media_item['file_path'];

        // Delete the file from the server
        if (unlink($file_path)) {
            // Delete from database
            $delete_sql = "DELETE FROM gallery WHERE gallery_id = ? AND user_id = ?";
            $stmt = $conn->prepare($delete_sql);
            $stmt->bind_param("ii", $media_id, $user_id);

            if ($stmt->execute()) {
                $gallery_success = "Media deleted successfully!";
            } else {
                $gallery_error = "Failed to delete media from the database.";
            }
        } else {
            $gallery_error = "Failed to delete media file.";
        }
    }
}

// Fetch gallery items
$gallery_sql = "SELECT * FROM gallery WHERE user_id = $user_id ORDER BY created_at DESC";
$gallery_result = $conn->query($gallery_sql);
?>

<?php include '../includes/header.php'; ?>
<body>
<div class="flex bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-100">
    
    <!-- Sidebar -->
    <?php include '../includes/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="flex-1 p-6">
        <!-- Gallery Upload Form -->
        <h2 class="text-3xl font-bold mb-8">Upload Media to Gallery</h2>

        <?php if (isset($gallery_success)) : ?>
            <div class="bg-green-100 text-green-700 border border-green-500 p-4 rounded-lg mb-6 dark:bg-green-900 dark:text-green-300">
                <?php echo $gallery_success; ?>
            </div>
        <?php elseif (isset($gallery_error)) : ?>
            <div class="bg-red-100 text-red-700 border border-red-500 p-4 rounded-lg mb-6 dark:bg-red-900 dark:text-red-300">
                <?php echo $gallery_error; ?>
            </div>
        <?php endif; ?>

        <form action="gallery.php" method="POST" enctype="multipart/form-data" class="bg-gray-50 p-8 rounded-xl shadow-md dark:bg-gray-800">
            <input type="hidden" name="gallery_form" value="1">

            <div class="mb-6">
                <label for="type" class="block text-sm font-medium">Media Type</label>
                <select name="type" id="type" class="w-full mt-2 px-4 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100" required>
                    <option value="">-- Select Type --</option>
                    <option value="photo">Photo</option>
                    <option value="video">Video</option>
                </select>
            </div>

            <div class="mb-6">
                <label for="title" class="block text-sm font-medium">Title</label>
                <input type="text" name="title" id="title" class="w-full mt-2 px-4 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100" required>
            </div>

            <div class="mb-6">
                <label for="description" class="block text-sm font-medium">Description</label>
                <textarea name="description" id="description" class="w-full mt-2 px-4 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100" rows="4"></textarea>
            </div>

            <div class="mb-6">
                <label for="file" class="block text-sm font-medium">Upload File</label>
                <input type="file" name="file" id="file" class="w-full mt-2 px-4 py-2 border rounded-md focus:ring-2 focus:ring-blue-500 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:text-gray-100" required>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg hover:bg-blue-700 transition duration-300 dark:bg-blue-500 dark:hover:bg-blue-600">
                Upload
            </button>
        </form>

        <!-- Gallery Display -->
        <h2 class="text-3xl font-bold mt-12 mb-8">Your Gallery</h2>

        <?php if ($gallery_result->num_rows > 0) : ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                <?php while ($media = $gallery_result->fetch_assoc()) : ?>
                    <div class="bg-white shadow-lg rounded-lg overflow-hidden dark:bg-gray-800">
                        <?php if ($media['type'] == 'photo') : ?>
                            <img src="../assets/gallery/<?php echo htmlspecialchars($media['file_path']); ?>" alt="<?php echo htmlspecialchars($media['title']); ?>" class="w-full h-52 object-cover">
                        <?php elseif ($media['type'] == 'video') : ?>
                            <video controls class="w-full h-52 object-cover">
                                <source src="../assets/gallery/<?php echo htmlspecialchars($media['file_path']); ?>" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        <?php endif; ?>

                        <div class="p-4">
                            <h4 class="text-lg font-semibold"><?php echo htmlspecialchars($media['title']); ?></h4>
                            <p class="text-sm mt-2 text-gray-500 dark:text-gray-400"><?php echo htmlspecialchars($media['description']); ?></p>

                            <div class="mt-4 flex justify-between items-center">
                                <a href="edit_gallery.php?edit=<?php echo $media['gallery_id']; ?>" class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-500">Edit</a>
                                <a href="gallery.php?delete=<?php echo $media['gallery_id']; ?>" class="text-red-500 hover:text-red-700 dark:text-red-400 dark:hover:text-red-500">Delete</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else : ?>
            <p class="text-gray-500 dark:text-gray-400">You have not uploaded any media yet.</p>
        <?php endif; ?>
    </div>
</div>

<script>
    const themeToggle = document.getElementById('theme-toggle');
    const htmlElement = document.documentElement;

    themeToggle.addEventListener('click', () => {
        if (htmlElement.classList.contains('dark')) {
            htmlElement.classList.remove('dark');
            localStorage.setItem('theme', 'light');
        } else {
            htmlElement.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        }
    });

    // Apply saved theme on load
    if (localStorage.getItem('theme') === 'dark') {
        htmlElement.classList.add('dark');
    }
</script>
</body>
</html>