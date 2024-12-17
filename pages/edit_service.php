<?php
require '../config/db_connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch service data
if (isset($_GET['id'])) {
    $service_id = $_GET['id'];
    $sql = "SELECT * FROM services WHERE service_id = $service_id AND user_id = $user_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $service = $result->fetch_assoc();
    } else {
        header("Location: services.php");
        exit;
    }
} else {
    header("Location: services.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];

    // Check if a new image is uploaded
    if ($_FILES['image']['name']) {
        $image = $_FILES['image']['name'];
        $target_dir = "../assets/uploads/";
        $target_file = $target_dir . basename($image);
        move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
    } else {
        $image = $service['image']; // Keep existing image if not updated
    }

    // Update service in the database
    $update_sql = "UPDATE services SET title = ?, description = ?, image = ? WHERE service_id = ? AND user_id = ?";
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("sssii", $title, $description, $image, $service_id, $user_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Service updated successfully!";
        header("Location: services.php");
        exit;
    } else {
        $_SESSION['message'] = "Error updating service!";
    }
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="container mx-auto p-6">
    <h2 class="text-2xl font-bold mb-6">Edit Service</h2>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="bg-green-500 text-white p-3 mb-4 rounded">
            <?php echo $_SESSION['message']; ?>
            <?php unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-4">
            <label for="title" class="block text-sm font-bold mb-2">Title</label>
            <input type="text" name="title" id="title" class="w-full p-2 border border-gray-300 rounded" value="<?php echo htmlspecialchars($service['title']); ?>" required>
        </div>

        <div class="mb-4">
            <label for="description" class="block text-sm font-bold mb-2">Description</label>
            <textarea name="description" id="description" class="w-full p-2 border border-gray-300 rounded" required><?php echo htmlspecialchars($service['description']); ?></textarea>
        </div>

        <div class="mb-4">
            <label for="image" class="block text-sm font-bold mb-2">Image (Optional)</label>
            <input type="file" name="image" id="image" class="w-full p-2 border border-gray-300 rounded">
            <img src="../assets/uploads/<?php echo $service['image']; ?>" alt="Current Image" class="mt-2 w-32">
        </div>

        <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded">Update Service</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
