<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Database connection
require_once '../config/db_connection.php';

$user_id = $_SESSION['user_id'];

// Fetch current profile design from the database
$query = "SELECT profile_design FROM profiles WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();

// If there's no design, default to 'design1'
$profile_design = $profile['profile_design'] ?? 'design1';

// Handle form submission to update profile design
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['design_option'])) {
    $selected_design = $_POST['design_option'];

    // Update the selected design in the database
    $update_design_sql = "UPDATE profiles SET profile_design = ? WHERE user_id = ?";
    $stmt = $conn->prepare($update_design_sql);
    $stmt->bind_param("si", $selected_design, $user_id);

    if ($stmt->execute()) {
        // Redirect to the same page with a success message
        header('Location: profile_design.php?success=1');
        exit();
    } else {
        // Handle error
        $error = "Failed to update profile design. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Design</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">

    <!-- Navbar -->
    <nav class="bg-blue-600 p-4 text-white">
        <div class="container mx-auto flex justify-between items-center">
            <a href="index.php" class="text-xl font-semibold">Digital Pehchaan</a>
            <ul class="space-x-4">
                <li><a href="dashboard.php" class="hover:underline">Dashboard</a></li>
                <li><a href="profile.php" class="hover:underline">Profile</a></li>
                <li><a href="settings.php" class="hover:underline">Settings</a></li>
                <li><a href="logout.php" class="hover:underline">Logout</a></li>
            </ul>
        </div>
    </nav>

    <!-- Profile Design Section -->
    <div class="container mx-auto p-6 max-w-4xl">
        <h2 class="text-2xl font-semibold mb-6">Choose Your Profile Design</h2>

        <?php if (isset($_GET['success'])) : ?>
            <div class="bg-green-500 text-white p-4 rounded mb-6">
                Profile design updated successfully!
            </div>
        <?php elseif (isset($error)) : ?>
            <div class="bg-red-500 text-white p-4 rounded mb-6">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="profile_design.php" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Design 1 -->
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

            <!-- Design 2 -->
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

            <!-- Design 3 -->
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

</body>
</html>
