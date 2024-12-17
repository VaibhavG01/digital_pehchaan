<?php
require '../config/db_connection.php';
session_start();

// Redirect to login if user is not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch user data
$user_id = $_SESSION['user_id'];
$sql_user = "SELECT * FROM users WHERE user_id = $user_id";
$result_user = $conn->query($sql_user);
$user = $result_user->fetch_assoc();

// Fetch user profile details
$sql_user_details = "SELECT * FROM profiles WHERE user_id = $user_id";
$result_user_details = $conn->query($sql_user_details);
$user_details = $result_user_details->fetch_assoc();

// Fetch services for the specific user from the database
$services_query = "SELECT title, description, image FROM services WHERE user_id = ?";
$stmt = $conn->prepare($services_query);
$stmt->bind_param("i", $user_id); // Bind the user_id as an integer parameter
$stmt->execute();
$services_result = $stmt->get_result();
$services = [];
if ($services_result->num_rows > 0) {
    while ($row = $services_result->fetch_assoc()) {
        $services[] = $row;
    }
}

// Fetch gallery images for the specific user from the database
$gallery_query = "SELECT file_path FROM gallery WHERE user_id = ?";
$stmt = $conn->prepare($gallery_query);
$stmt->bind_param("i", $user_id); // Bind the user_id as an integer parameter
$stmt->execute();
$gallery_result = $stmt->get_result();
$gallery_images = [];
if ($gallery_result->num_rows > 0) {
    while ($row = $gallery_result->fetch_assoc()) {
        $gallery_images[] = $row;
    }
}


// Fetch current data
$sql_contacts = "SELECT COUNT(*) AS count FROM contact_messages WHERE user_id = $user_id";
$sql_appointments = "SELECT COUNT(*) AS count FROM appointments WHERE user_id = $user_id";

// Assuming you want to calculate growth compared to last week
// Fetch the current counts
$current_contacts = $conn->query($sql_contacts)->fetch_assoc()['count'];
$current_appointments = $conn->query($sql_appointments)->fetch_assoc()['count'];

// Fetch previous week's data (you need to adjust this query depending on your table structure and dates)
$sql_contacts_prev = "SELECT COUNT(*) AS count FROM contact_messages WHERE user_id = $user_id AND DATE(created_at) >= CURDATE() - INTERVAL 7 DAY";
$sql_appointments_prev = "SELECT COUNT(*) AS count FROM appointments WHERE user_id = $user_id AND DATE(created_at) >= CURDATE() - INTERVAL 7 DAY";

// Fetch the previous counts
$previous_contacts = $conn->query($sql_contacts_prev)->fetch_assoc()['count'];
$previous_appointments = $conn->query($sql_appointments_prev)->fetch_assoc()['count'];

// Calculate the growth percentage (if previous count is 0, we set the growth to 0 to avoid division by 0)
$contacts_growth = $previous_contacts > 0 ? (($current_contacts - $previous_contacts) / $previous_contacts) * 100 : 0;
$appointments_growth = $previous_appointments > 0 ? (($current_appointments - $previous_appointments) / $previous_appointments) * 100 : 0;

// Fetch recent activity (contacts and appointments)
$sql_recent_activity = "
    (SELECT 'Contact' AS activity_type, created_at, message
    FROM contact_messages 
    WHERE user_id = $user_id)
    UNION ALL
    (SELECT 'Appointment' AS activity_type, created_at, status
    FROM appointments 
    WHERE user_id = $user_id)
    ORDER BY created_at DESC LIMIT 5";
$recent_activity = $conn->query($sql_recent_activity);



// Fetch the social links for the logged-in user
$sql = "SELECT * FROM social_links WHERE user_id = ? ORDER BY id DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();


?>

<?php include '../includes/header.php'; ?>

<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200">

    <div class="flex h-screen">
        
        <!-- Sidebar -->
        <?php include '../includes/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col p-6">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold">Welcome, <?php echo htmlspecialchars($user['username']); ?>!</h1>
            </div>

            <!-- Account Overview -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-6 mb-4">
                <h1 class="text-8xl font-bold mb-2">Get Started</h1>
                <p class="text-lg font-bold mb-2">Make your <span class="font-bold text-2xl text-blue-600">Business Profile</span> with in a minutes.</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-4">
                <?php
                $overview = [
                    ['title' => 'Complete your Profile', 'desc' => 'Step 1'],
                    ['title' => 'Add Services', 'desc' => 'Step 2'],
                    ['title' => 'Add Social Links', 'desc' => 'Step 3'],
                    ['title' => 'Add Gallery', 'desc' => 'Step 4'],
                    ['title' => 'View Profile', 'desc' => 'Step 5']
                ];
                foreach ($overview as $item) {
                    echo "<div class='bg-gray-100 dark:bg-gray-700 p-4 rounded-md shadow-lg hover:shadow-2xl transition-shadow duration-300'>
                            <h4 class='font-semibold text-blue-800 dark:text-blue-200'>{$item['title']}</h4>
                            <p class='mt-2 text-gray-600 dark:text-gray-400'>{$item['desc']}</p>
                        </div>";
                }
                ?>
            </div>

            <!-- Statistics Cards with Data Analysis -->
            <h1 class="text-4xl mb-2 font-bold">Enquiries</h1>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                
                <?php
                $stats = [
                    ['title' => 'Contacts', 'count' => $current_contacts, 'growth' => $contacts_growth, 'bg' => 'blue'],
                    ['title' => 'Appointments', 'count' => $current_appointments, 'growth' => $appointments_growth, 'bg' => 'green']
                ];

                foreach ($stats as $stat) {
                    $growth_class = $stat['growth'] >= 0 ? 'text-green-500' : 'text-red-500';
                    $growth_icon = $stat['growth'] >= 0 ? '↑' : '↓';
                    $growth_sign = $stat['growth'] >= 0 ? '+' : '';

                    echo "
                        <div class='bg-{$stat['bg']}-100 dark:bg-{$stat['bg']}-900 p-6 rounded-lg shadow-lg'>
                            <h3 class='text-xl font-semibold text-{$stat['bg']}-800 dark:text-{$stat['bg']}-300'>{$stat['title']}</h3>
                            <p class='text-lg font-bold'>{$stat['count']}</p>
                            <p class='text-sm {$growth_class}'>
                                <span class='font-bold'>{$growth_sign}{$stat['growth']}%</span> {$growth_icon} Last Week
                            </p>
                        </div>";
                }
                ?>
            </div>


            <!-- Recent Activity Table -->
            <div class="mb-6">
                <h2 class="text-2xl font-semibold mb-4">Recent Activity</h2>
                <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow-lg">
                    <table class="min-w-full table-auto text-left">
                        <thead class="bg-gray-200 dark:bg-gray-700">
                            <tr>
                                <th class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300">Activity</th>
                                <th class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300">Date</th>
                                <th class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300">Status / Messages</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($activity = $recent_activity->fetch_assoc()): ?>
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-800 dark:text-gray-200"><?php echo htmlspecialchars($activity['activity_type']); ?></td>
                                    <td class="px-4 py-2 text-sm text-gray-800 dark:text-gray-200"><?php echo htmlspecialchars($activity['created_at']); ?></td>
                                    <td class="px-4 py-2 text-sm text-gray-800 dark:text-gray-200"><?php echo htmlspecialchars($activity['message']); ?></td>
                                    
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>


            <!-- Profile Section -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-1 mb-6">
               
                <!-- Profile Section -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg max-w-sm w-full p-6 relative">
                    <!-- Banner Image -->
                    <div class="h-32 rounded-t-lg overflow-hidden bg-gray-200 dark:bg-gray-700">
                        <?php if (!empty($user_details['banner_image'])): ?>
                            <img src="../assets/uploads/<?php echo htmlspecialchars($user_details['banner_image']); ?>" 
                                alt="Banner" class="w-full h-full object-cover">
                        <?php else: ?>
                            <div class="flex items-center justify-center h-full text-gray-500 dark:text-gray-400">
                                No Banner Image
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Profile Image -->
                    <div class="absolute top-24 left-1/2 transform -translate-x-1/2 w-24 h-24 rounded-full overflow-hidden border-4 border-white dark:border-gray-800 shadow-lg">
                        <?php if (!empty($user_details['profile_picture'])): ?>
                            <img src="../assets/uploads/<?php echo htmlspecialchars($user_details['profile_picture']); ?>" 
                                alt="Profile" class="w-full h-full object-cover">
                        <?php else: ?>
                            <div class="flex items-center justify-center h-full bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                                No Image
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Profile Name and Email -->
                    <div class="mt-16 text-center">
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                            <?php echo htmlspecialchars($user_details['first_name']) . ' ' . htmlspecialchars($user_details['last_name']); ?>
                        </h2>
                        <p class="text-gray-600 dark:text-gray-400"><?php echo htmlspecialchars($user['email']); ?></p>
                    </div>

                    <!-- Profile Details -->
                    <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Email -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                            <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase">Email</h3>
                            <p class="text-gray-800 dark:text-gray-200"><?php echo htmlspecialchars($user['email']); ?></p>
                        </div>
                        <!-- Phone -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                            <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase">Phone</h3>
                            <p class="text-gray-800 dark:text-gray-200"><?php echo htmlspecialchars($user_details['mobile_number']); ?></p>
                        </div>
                        <!-- Address -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                            <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase">Address</h3>
                            <p class="text-gray-800 dark:text-gray-200"><?php echo htmlspecialchars($user_details['address']); ?></p>
                        </div>
                        <!-- About -->
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                            <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase">About</h3>
                            <p class="text-gray-800 dark:text-gray-200"><?php echo htmlspecialchars($user_details['about']); ?></p>
                        </div>
                    </div>
                </div>


                <!-- Service Section -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg max-w-sm w-full p-6 relative">
                    <!-- Service Section -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4 mb-6">
                        <h3 class="text-2xl font-semibold text-gray-800 dark:text-gray-200 uppercase text-center">Our Services</h3>
                        <ul class="list-disc list-inside text-gray-800 dark:text-gray-200">
                            <?php foreach ($services as $service): ?>
                                <li class="flex items-center m-4">
                                    <!-- Display the image if it exists -->
                                    <img src="../assets/uploads/<?php echo htmlspecialchars($service['image']); ?>" alt="Service Image" class="w-20 h-20 object-cover rounded-full mr-4">
                                    <span><?php echo htmlspecialchars($service['title']); ?>
                                    <!-- - <?php echo htmlspecialchars($service['description']); ?></span> -->
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>


                <!-- Gallery Section -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg max-w-auto w-full p-6 relative">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                        <h3 class="text-2xl text-center mb-4 font-semibold text-gray-800 dark:text-gray-200 uppercase">Gallery</h3>

                        <!-- Slider Container -->
                        <div class="relative h-96 overflow-hidden">
                            <div id="gallerySlider" class="flex flex-col overflow-hidden gap-4 h-full scroll-smooth">
                                <?php foreach ($gallery_images as $index => $image): ?>
                                    <div class="gallery-slide w-full flex-shrink-0" data-index="<?php echo $index; ?>">
                                        <?php if (strpos($image['file_path'], '.mp4') !== false): ?>
                                            <!-- Display video -->
                                            <video controls class="w-full h-40 rounded-md shadow-lg">
                                                <source src="../assets/gallery/<?php echo $image['file_path']; ?>" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                        <?php else: ?>
                                            <!-- Display image -->
                                            <img src="../assets/gallery/<?php echo $image['file_path']; ?>" alt="Gallery Image" class="w-full h-40 object-cover rounded-md shadow-lg">
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Pagination Dots -->
                        <div id="paginationDots" class="flex justify-center mt-4 space-x-2">
                            <?php foreach ($gallery_images as $index => $image): ?>
                                <button class="dot w-3 h-3 bg-gray-400 rounded-full" data-index="<?php echo $index; ?>"></button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- JavaScript for Dot Pagination -->
                <script>
                    const slider = document.getElementById('gallerySlider');
                    const dots = document.querySelectorAll('.dot');

                    // Initialize active slide
                    let activeIndex = 0;

                    // Function to show the specific slide
                    const showSlide = (index) => {
                        const slideHeight = slider.children[0].offsetHeight;
                        slider.scrollTo({
                            top: slideHeight * index,
                            behavior: 'smooth',
                        });
                        updateDots(index);
                    };

                    // Update active dot
                    const updateDots = (index) => {
                        dots.forEach(dot => dot.classList.remove('bg-blue-500'));
                        dots[index].classList.add('bg-blue-500');
                    };

                    // Event listeners for dots
                    dots.forEach((dot, index) => {
                        dot.addEventListener('click', () => {
                            activeIndex = index;
                            showSlide(index);
                        });
                    });

                    // Initialize the first slide and dot
                    showSlide(activeIndex);
                </script>



                <!-- Social Media Section -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg max-w-auto w-full p-6 relative">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                        <h3 class="text-2xl text-center mb-6 font-semibold text-gray-800 dark:text-gray-200 uppercase">Social Media</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                            <?php while ($row = $result->fetch_assoc()) { ?>
                                <div class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg p-2 shadow-md">
                                    <div class="flex items-center justify-center">
                                        <i class="<?php echo $row['icon_class']; ?> text-3xl text-gray-700 dark:text-gray-300"></i>
                                        <!-- <div class="flex items-center">
                                            <span class="font-semibold text-gray-800 dark:text-white"><?php echo $row['platform']; ?></span>
                                        </div> -->
                                    </div>
                                    <!-- <p class="text-sm text-gray-600 dark:text-gray-400 mb-4"><?php echo $row['url']; ?></p> -->
                                    <!-- Optional: Links for editing and deleting -->
                                    <!--
                                    <div class="flex justify-between">
                                        <a href="edit_social.php?id=<?php echo $row['id']; ?>" class="text-blue-500 hover:text-blue-700">Edit</a>
                                        <a href="?delete_id=<?php echo $row['id']; ?>" class="text-red-500 hover:text-red-700">Delete</a>
                                    </div>
                                    -->
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>

                
            </div>

            
        </div>
        
        <!-- Floating Profile Icon with Name -->
        <a href="view_profile.php" class="fixed bottom-6 right-6 flex items-center bg-blue-500 text-white px-4 py-2 rounded-full shadow-lg hover:bg-blue-600 transition-colors duration-300">
            <i class="ri-user-line text-2xl mr-2"></i> <!-- Remix Icon for Profile -->
            <span class="text-sm font-semibold"><?php echo htmlspecialchars($user['username']); ?></span>
        </a>
        
    </div>
               
</body>
</html>
