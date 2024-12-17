<?php
require '../config/db_connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $mobile_number = $_POST['mobile_number'];
    $address = $_POST['address'];
    $about = $_POST['about'];
    $banner_image = $_FILES['banner_image']['name'];
    $profile_picture = $_FILES['profile_picture']['name'];

    // Handle the profile picture upload
    if ($profile_picture) {
        $target_dir = "../assets/uploads/";
        $target_file = $target_dir . basename($profile_picture);
        move_uploaded_file($_FILES['profile_picture']['tmp_name'], $target_file);
    }

    // Handle the banner image upload
    if ($banner_image) {
        $target_banner_dir = "../assets/uploads/";
        $target_banner_file = $target_banner_dir . basename($banner_image);
        move_uploaded_file($_FILES['banner_image']['tmp_name'], $target_banner_file);
    }

    // Update the profile data in the database
    $sql = "UPDATE profiles SET 
            first_name='$first_name', 
            last_name='$last_name', 
            mobile_number='$mobile_number',
            address='$address',
            about='$about'";
    
    if ($banner_image) $sql .= ", banner_image='$banner_image'";
    if ($profile_picture) $sql .= ", profile_picture='$profile_picture'";

    $sql .= " WHERE user_id = $user_id";

    // Execute the update query
    if ($conn->query($sql)) {
        $success = "Profile updated successfully!";
    } else {
        $error = "Error: " . $conn->error;
    }
}

$sql = "SELECT * FROM profiles WHERE user_id = $user_id";
$result = $conn->query($sql);
$profile = $result->fetch_assoc();
?>

<?php include '../includes/header.php'; ?>

<body class="bg-gray-100 dark:bg-gray-700 transition-colors duration-300">

    <div class="flex h-screen">

        <!-- Sidebar -->
        <?php include '../includes/sidebar.php'; ?>

        <!-- Main content -->
        <div class="flex-1 p-8 sm:ml-64">
            <button class="text-gray-800 sm:hidden" id="sidebar-toggle" onclick="toggleSidebar()">☰</button>

            <!-- Theme Toggle Button -->
            <div class="absolute top-6 right-6">
                <button id="theme-toggle" class="p-2 bg-gray-200 dark:bg-gray-800 text-gray-800 dark:text-white rounded-full shadow-lg hover:bg-gray-300 dark:hover:bg-gray-700">
                    <i id="theme-icon" class="ri-moon-line text-xl"></i>
                </button>
            </div>

            <div class="max-w-4xl mx-auto bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8">

                <!-- Profile Header -->
                <div class="flex items-center mb-6">
                    <img src="../assets/uploads/<?php echo $profile['profile_picture'] ?: 'default_profile.png'; ?>" 
                        alt="Profile Picture" 
                        class="w-24 h-24 rounded-full border-4 border-blue-500 shadow-lg">
                    <div class="ml-6">
                        <h1 class="text-3xl font-semibold text-gray-800 dark:text-white"><?php echo htmlspecialchars($profile['first_name'] . ' ' . $profile['last_name']); ?></h1>
                        <p class="text-sm text-gray-500 dark:text-gray-300">@dp<?php echo htmlspecialchars($profile['user_id']); ?></p>
                    </div>
                </div>

                <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-6">Edit Your Profile</h2>

                <?php if (!empty($error)) echo "<p class='text-red-500 mb-4'>$error</p>"; ?>
                <?php if (!empty($success)) echo "<p class='text-green-500 mb-4'>$success</p>"; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="col-span-1">
                            <label for="first_name" class="block text-sm font-medium text-gray-700 dark:text-gray-200">First Name</label>
                            <input type="text" name="first_name" value="<?php echo htmlspecialchars($profile['first_name']); ?>" 
                                placeholder="First Name" class="w-full px-4 py-3 mt-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="col-span-1">
                            <label for="last_name" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Last Name</label>
                            <input type="text" name="last_name" value="<?php echo htmlspecialchars($profile['last_name']); ?>" 
                                placeholder="Last Name" class="w-full px-4 py-3 mt-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-6">
                        <div class="col-span-1">
                            <label for="mobile_number" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Mobile Number</label>
                            <input type="text" name="mobile_number" value="<?php echo htmlspecialchars($profile['mobile_number']); ?>" 
                                placeholder="Mobile Number" class="w-full px-4 py-3 mt-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div class="col-span-1">
                            <label for="banner_image" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Banner Image</label>
                            <input type="file" name="banner_image" class="w-full mt-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">Current Banner Image: 
                                <?php echo $profile['banner_image'] ? "<img src='../assets/uploads/{$profile['banner_image']}' class='w-32 h-32 rounded-md'>" : 'No banner image set.'; ?>
                            </p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Address</label>
                        <textarea name="address" id="address" 
                                class="w-full px-4 py-3 mt-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Enter your address"><?php echo htmlspecialchars($profile['address']); ?></textarea>
                    </div>

                    <div class="mt-6">
                        <label for="about" class="block text-sm font-medium text-gray-700 dark:text-gray-200">About</label>
                        <textarea name="about" id="about" 
                                class="w-full px-4 py-3 mt-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
                                placeholder="Tell us about yourself"><?php echo htmlspecialchars($profile['about']); ?></textarea>
                    </div>

                    <div class="mt-6">
                        <label for="profile_picture" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Profile Picture</label>
                        <input type="file" name="profile_picture" class="w-full mt-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <button type="submit" class="mt-6 bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">Update Profile</button>
                </form>
            </div>
        </div>

    </div>

    <script>
        // Toggle Sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('-translate-x-full');
        }

        // Theme Toggle
        const themeToggleButton = document.getElementById('theme-toggle');
        const themeIcon = document.getElementById('theme-icon');
        const body = document.body;

        // Check if dark mode is enabled in localStorage
        if (localStorage.getItem('theme') === 'dark') {
            body.classList.add('dark');
            themeIcon.classList.replace('ri-moon-line', 'ri-sun-line');
        }

        themeToggleButton.addEventListener('click', () => {
            body.classList.toggle('dark');
            if (body.classList.contains('dark')) {
                localStorage.setItem('theme', 'dark');
                themeIcon.classList.replace('ri-moon-line', 'ri-sun-line');
            } else {
                localStorage.setItem('theme', 'light');
                themeIcon.classList.replace('ri-sun-line', 'ri-moon-line');
            }
        });
    </script>

</body>
