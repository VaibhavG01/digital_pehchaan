<?php
require '../config/db_connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch the media details for editing
if (isset($_GET['edit'])) {
    $media_id = $_GET['edit'];

    $sql = "SELECT * FROM gallery WHERE gallery_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $media_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $media_item = $result->fetch_assoc();
    } else {
        header("Location: gallery.php");
        exit;
    }
} else {
    header("Location: gallery.php");
    exit;
}

// Handle update form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_gallery'])) {
    $media_id = $_POST['media_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $file = $_FILES['file'];

    // Check if a new file is uploaded
    if ($file['error'] == 0) {
        $allowed_types = ['photo' => ['jpg', 'jpeg', 'png'], 'video' => ['mp4', 'mov']];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $upload_dir = "../assets/gallery/";
        $file_name = uniqid() . '.' . $extension;
        $file_path = $upload_dir . $file_name;

        // Validate file type
        if (in_array($extension, $allowed_types[$media_item['type']])) {
            if (move_uploaded_file($file['tmp_name'], $file_path)) {
                // Delete the old file
                unlink($upload_dir . $media_item['file_path']);
                $media_item['file_path'] = $file_name;
            } else {
                $gallery_error = "Failed to upload new file.";
            }
        } else {
            $gallery_error = "Invalid file type for the selected media.";
        }
    }

    // Update title, description, and file path
    if (!isset($gallery_error)) {
        $update_sql = "UPDATE gallery SET title = ?, description = ?, file_path = ? WHERE gallery_id = ? AND user_id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("sssii", $title, $description, $media_item['file_path'], $media_id, $user_id);

        if ($stmt->execute()) {
            $gallery_success = "Media updated successfully!";
        } else {
            $gallery_error = "Failed to update media. Please try again.";
        }
    }
}
?>

<?php include '../includes/header.php'; ?>
<body>
<div class="flex">
    <!-- Sidebar -->
    <div class="w-64 h-screen bg-gray-800 text-white flex flex-col">
        <div class="p-4 text-lg font-semibold border-b border-gray-600">Dashboard</div>
        <nav class="flex-1 px-4 py-2">
            <a href="dashboard.php" class="block py-2 px-4 hover:bg-gray-700 rounded">Home</a>
            <a href="gallery.php" class="block py-2 px-4 hover:bg-gray-700 rounded">Gallery</a>
            <a href="profile.php" class="block py-2 px-4 hover:bg-gray-700 rounded">Profile</a>
            <a href="logout.php" class="block py-2 px-4 hover:bg-gray-700 rounded">Logout</a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1 p-6">
        <!-- Edit Gallery Form -->
        <h2 class="text-2xl font-semibold mt-12 mb-6">Edit Media</h2>
        <?php if (isset($gallery_success)) : ?>
            <div class="bg-green-500 text-white p-4 rounded mb-6"><?php echo $gallery_success; ?></div>
        <?php elseif (isset($gallery_error)) : ?>
            <div class="bg-red-500 text-white p-4 rounded mb-6"><?php echo $gallery_error; ?></div>
        <?php endif; ?>

        <form action="edit_gallery.php?edit=<?php echo $media_id; ?>" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow-md">
            <input type="hidden" name="media_id" value="<?php echo $media_item['gallery_id']; ?>">
            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($media_item['title']); ?>" class="w-full px-4 py-2 mt-2 border border-gray-300 rounded-lg" required>
            </div>
            <div class="mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" id="description" class="w-full px-4 py-2 mt-2 border border-gray-300 rounded-lg" rows="4"><?php echo htmlspecialchars($media_item['description']); ?></textarea>
            </div>
            <div class="mb-4">
                <label for="file" class="block text-sm font-medium text-gray-700">Upload New File (optional)</label>
                <input type="file" name="file" id="file" class="w-full px-4 py-2 mt-2 border border-gray-300 rounded-lg">
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Current File</label>
                <?php if ($media_item['type'] == 'photo') : ?>
                    <img src="../assets/gallery/<?php echo htmlspecialchars($media_item['file_path']); ?>" alt="<?php echo htmlspecialchars($media_item['title']); ?>" class="w-64 h-40 object-cover">
                <?php elseif ($media_item['type'] == 'video') : ?>
                    <video controls class="w-64 h-40">
                        <source src="../assets/gallery/<?php echo htmlspecialchars($media_item['file_path']); ?>" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                <?php endif; ?>
            </div>
            <button type="submit" name="update_gallery" class="bg-blue-600 text-white px-6 py-2 rounded-full hover:bg-blue-700 transition duration-300">
                Update
            </button>
        </form>
    </div>
</div>
</body>
</html>