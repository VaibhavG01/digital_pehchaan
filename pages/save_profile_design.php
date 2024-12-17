<?php
// Start the session at the very beginning of the file
session_start();

// Check if user is logged in (ensure user_id exists in session)
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if the user is not logged in
    header('Location: login.php');
    exit();
}

require_once '../config/db_connection.php'; // Assuming you have a database connection file

// Get the user_id from the session
$user_id = $_SESSION['user_id'];

// Fetch user profile from the database
$query = "SELECT * FROM profiles WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();

// Handle form submission for design update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['design_option'])) {
    $selected_design = $_POST['design_option'];

    // Save selected design in the database
    $update_design_sql = "UPDATE profiles SET profile_design = ? WHERE user_id = ?";
    $stmt = $conn->prepare($update_design_sql);
    $stmt->bind_param("si", $selected_design, $user_id);
    if ($stmt->execute()) {
        $design_success = "Profile design updated successfully!";
        header('location:view_profile.php');
    } else {
        $design_error = "Failed to update profile design. Please try again.";
    }
}

// Fetch selected design from the database (use default if not set)
$profile_design = $profile['profile_design'] ?? 'design1'; 
?>

<?php include '../includes/header.php'; ?>
<body>
    <!-- Responsive Navbar -->
    <nav class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-lg sticky top-0 z-50">
        <div class="container mx-auto flex items-center justify-between p-4">
            <!-- Logo -->
            <a href="index.php" class="flex items-center text-2xl font-bold">
                <img src="logo.png" alt="Digital Pehchaan" class="w-8 h-8 mr-2">
                Digital Pehchaan
            </a>

            <!-- Nav Links -->
            <ul class="hidden md:flex space-x-6">
                <li><a href="dashboard.php" class="hover:underline">Dashboard</a></li>
                <li><a href="profile_design.php" class="hover:underline">Profile</a></li>
                <li><a href="settings.php" class="hover:underline">Settings</a></li>
            </ul>

            <!-- Logout Button -->
            <a href="logout.php" class="px-4 py-2 bg-red-500 rounded-full shadow-md hover:shadow-lg transition">Logout</a>

            <!-- Mobile Menu Button -->
            <button id="mobile-menu-toggle" class="block md:hidden text-white">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                </svg>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-blue-700 text-white">
            <ul class="space-y-2 p-4">
                <li><a href="dashboard.php" class="block hover:underline">Dashboard</a></li>
                <li><a href="profile_design.php" class="block hover:underline">Profile</a></li>
                <li><a href="settings.php" class="block hover:underline">Settings</a></li>
                <li><a href="logout.php" class="block hover:underline">Logout</a></li>
            </ul>
        </div>
    </nav>

    <!-- Profile Design Selection Section -->
    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-semibold mb-6">Choose Your Profile Design</h2>

        <?php if (isset($design_success)) : ?>
            <div class="bg-green-500 text-white p-4 rounded mb-6"><?php echo $design_success; ?></div>
        <?php elseif (isset($design_error)) : ?>
            <div class="bg-red-500 text-white p-4 rounded mb-6"><?php echo $design_error; ?></div>
        <?php endif; ?>

        <form action="save_profile_design.php" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Card Design 1 -->
            <div class="relative p-6 rounded-xl shadow-lg border bg-gradient-to-br from-yellow-300 to-yellow-500">
                <div class="absolute top-4 right-4">
                    <input type="radio" name="design_option" value="design1" class="accent-black scale-125" <?php echo $profile_design === 'design1' ? 'checked' : ''; ?>>
                </div>
                <div class="text-center">
                    <div class="w-20 h-20 rounded-full bg-yellow-200 border-4 border-white mx-auto mb-4 flex items-center justify-center">
                        <img src="profile1.jpg" alt="Classic" class="rounded-full w-full h-full object-cover">
                    </div>
                    <h3 class="text-xl font-bold text-white">Classic Design</h3>
                    <p class="text-sm text-gray-800">Minimalist and clean layout.</p>
                </div>
            </div>

            <!-- Card Design 2 -->
            <div class="relative p-6 rounded-xl shadow-lg border bg-gradient-to-br from-pink-400 to-pink-600">
                <div class="absolute top-4 right-4">
                    <input type="radio" name="design_option" value="design2" class="accent-black scale-125" <?php echo $profile_design === 'design2' ? 'checked' : ''; ?>>
                </div>
                <div class="text-center">
                    <div class="w-20 h-20 rounded-full bg-pink-200 border-4 border-white mx-auto mb-4 flex items-center justify-center">
                        <img src="profile2.jpg" alt="Creative" class="rounded-full w-full h-full object-cover">
                    </div>
                    <h3 class="text-xl font-bold text-white">Creative Design</h3>
                    <p class="text-sm text-gray-800">Vibrant and colorful elements.</p>
                </div>
            </div>

            <!-- Card Design 3 -->
            <div class="relative p-6 rounded-xl shadow-lg border bg-gradient-to-br from-blue-500 to-blue-700">
                <div class="absolute top-4 right-4">
                    <input type="radio" name="design_option" value="design3" class="accent-black scale-125" <?php echo $profile_design === 'design3' ? 'checked' : ''; ?>>
                </div>
                <div class="text-center">
                    <div class="w-20 h-20 rounded-full bg-blue-200 border-4 border-white mx-auto mb-4 flex items-center justify-center">
                        <img src="profile3.jpg" alt="Professional" class="rounded-full w-full h-full object-cover">
                    </div>
                    <h3 class="text-xl font-bold text-white">Professional Design</h3>
                    <p class="text-sm text-gray-800">Sleek and professional layout.</p>
                </div>
            </div>

            <!-- Save Button -->
            <div class="col-span-1 md:col-span-3 mt-6 text-center">
                <button type="submit" class="px-6 py-2 bg-gradient-to-r from-purple-500 to-indigo-600 text-white font-semibold rounded-full shadow-md hover:shadow-lg transition">Save Design</button>
            </div>
        </form>
    </div>

    <script>
        const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');

        mobileMenuToggle.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    </script>
</body>
</html>