<?php
    // Include database connection
    include '../config/db_connection.php'; // Adjust the path as needed

    // Start session
    session_start();

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../login.php");
        exit;
    }

    // Get logged-in user ID
    $user_id = $_SESSION['user_id'];

    // Fetch user and profile data
    $sql = "SELECT u.username, u.email, p.first_name, p.last_name, p.mobile_number, p.address, 
            p.about, p.banner_image, p.profile_picture, p.profile_design 
            FROM users u 
            JOIN profiles p ON u.user_id = p.user_id 
            WHERE u.user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $profile = $result->fetch_assoc();

    // Check if profile exists
    if (!$profile) {
        echo "Profile not found!";
        exit;
    }

    // Set profile design or use a default
    $profile_design = isset($profile['profile_design']) ? $profile['profile_design'] : 'design1';

    // Fetch social media links
    $social_sql = "SELECT * FROM social_links WHERE user_id = ?";
    $social_stmt = $conn->prepare($social_sql);
    $social_stmt->bind_param("i", $user_id);
    $social_stmt->execute();
    $social_result = $social_stmt->get_result();
    $social_links = $social_result->fetch_all(MYSQLI_ASSOC);

    // Fetch gallery images
    $gallery_sql = "SELECT * FROM gallery WHERE user_id = ?";
    $gallery_stmt = $conn->prepare($gallery_sql);
    $gallery_stmt->bind_param("i", $user_id);
    $gallery_stmt->execute();
    $gallery_result = $gallery_stmt->get_result();
    $gallery_images = $gallery_result->fetch_all(MYSQLI_ASSOC);


    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contactForm'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $subject = $_POST['subject'];
        $message = $_POST['message'];
    
        $contact_sql = "INSERT INTO contact_messages (user_id, name, email, subject, message) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($contact_sql);
        $stmt->bind_param("issss", $user_id, $name, $email, $subject, $message);
    
        if ($stmt->execute()) {
            echo "Contact information saved!";
        } else {
            echo "Error: " . $stmt->error;
        }
    }
    
?>
<?php include '../includes/header.php'; ?>
<body class="flex justify-center items-center min-h-screen transition-all bg-gray-100">

    <div id="themeCard" class="max-w-md w-full bg-white rounded-lg shadow-xl hover:shadow-2xl transform transition duration-500">
        <!-- Banner Image -->
        <div class="relative fade-up fade-up-banner">
            <img src="../assets/uploads/<?php echo $profile['banner_image'] ?? 'default-banner.jpg'; ?>" alt="Banner" class="w-full h-48 object-cover rounded-t-lg">
        </div>

        <!-- Profile Image and Floating Dots for Theme Change -->
        <div class="absolute top-28 left-36 transform translate-y-10 fade-up fade-up-profile flex justify-center">
            <img src="../assets/uploads/<?php echo $profile['profile_picture'] ?? 'default-avatar.jpg'; ?>" alt="Profile Picture" class="w-40 h-40 rounded-full border-4 border-gray-100 shadow-lg">
        </div>

        <!-- Floating Dots for Theme Change -->
        <div class="floating-dots absolute top-60 right-1 transform -translate-x-1/2 fade-up-profile flex justify-center">
            <div class="dot bg-gray-700" onclick="changeTheme('dark')"></div>
            <div class="dot bg-gray-300" onclick="changeTheme('light')"></div>
        </div>

        <!-- Content Section -->
        <div class="pt-24 px-6 pb-6 text-center">
            <div class="typewriter">
                <h1 id="companyName" class="text-2xl font-bold"><?php echo htmlspecialchars($profile['first_name'] . ' ' . $profile['last_name']); ?></h1>
            </div>
            <p class="mt-4"><?php echo htmlspecialchars($profile['about']); ?></p>

            <!-- Social Media and Contact Icons -->
            <div class="mt-6 grid grid-cols-3 gap-4 text-center">
                <?php foreach ($social_links as $link): ?>
                    <div class="p-2 rounded-lg">
                        <?php 
                        // Set icon color based on platform
                        switch ($link['platform']) {
                            case 'Instagram':
                                $icon_color = 'text-pink-500';
                                break;
                            case 'Facebook':
                                $icon_color = 'text-blue-600';
                                break;
                            case 'Twitter':
                                $icon_color = 'text-blue-400';
                                break;
                            case 'LinkedIn':
                                $icon_color = 'text-blue-700';
                                break;
                            case 'YouTube':
                                $icon_color = 'text-red-600';
                                break;
                            case 'GitHub':
                                $icon_color = 'text-gray-800';
                                break;
                            case 'Discord':
                                $icon_color = 'text-indigo-500';
                                break;
                            case 'Website':
                                $icon_color = 'text-gray-700';
                                break;
                            case 'WhatsApp':
                                $icon_color = 'text-green-500';
                                break;
                            case 'Mobile':
                                $icon_color = 'text-yellow-500';
                                break;
                            default:
                                $icon_color = 'text-gray-500';
                        }
                        ?>
                        <?php if (!empty($link['url'])): ?>
                            <a href="<?php echo $link['url']; ?>" target="_blank" class="text-2xl <?php echo $icon_color; ?>">
                                <i class="<?php echo $link['icon_class']; ?>"></i>
                            </a>
                        <?php else: ?>
                            <div class="text-2xl <?php echo $icon_color; ?>">
                                <i class="<?php echo $link['icon_class']; ?>"></i>
                            </div>
                        <?php endif; ?>
                        <p class="text-sm mt-2"><?php echo $link['platform']; ?></p>
                    </div>
                <?php endforeach; ?>
            </div>



            <!-- Buttons -->
            <div class="flex justify-center space-x-4 mt-6">
                <button id="openModalButton"
                    class="bg-orange-500 text-white px-4 py-2 rounded-lg shadow-md hover:bg-orange-600 transform hover:scale-105 transition">
                    Contact
                </button>
                <button id="openAppointmentModalButton"
                    class="bg-blue-500 text-white px-4 py-2 rounded-lg shadow-md hover:bg-blue-600 transform hover:scale-105 transition">
                    Appointment
                </button>
            </div>

            <!-- Gallery Section -->
            <div class="gallery">
                <?php foreach ($gallery_images as $media): ?>
                    <?php if (strpos($media['file_path'], '.mp4') !== false): ?>
                        <!-- Display video -->
                        <!-- <video controls class="gallery-media">
                            <source src="../assets/gallery/<?php echo $media['file_path']; ?>" type="video/mp4">
                            Your browser does not support the video tag.
                        </video> -->
                    <?php else: ?>
                        <!-- Display image -->
                        <img src="../assets/gallery/<?php echo $media['file_path']; ?>" alt="Gallery Image" class="gallery-image">
                    <?php endif; ?>
                <?php endforeach; ?>

                <!-- Pagination Dots -->
                <div class="pagination">
                    <?php foreach ($gallery_images as $index => $media): ?>
                        <div class="dot <?php echo $index === 0 ? 'active' : ''; ?>" data-index="<?php echo $index; ?>"></div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>

        <!-- Contact Modal -->
        <div id="contactModal" class="modal">
            <div class="modal-content">
                <h2 class="text-2xl font-bold mb-4">Contact Us</h2>
                <form id="contactForm" action="view_profile.php" method="POST">
                    <div class="mb-4">
                        <label for="contactName" class="block text-sm font-medium text-gray-700">
                            <i class="ri-user-line mr-2"></i> Full Name
                        </label>
                        <input type="text" id="contactName" name="name" class="w-full p-2 border border-gray-300 rounded-md focus:ring focus:ring-blue-300" required>
                    </div>
                    <div class="mb-4">
                        <label for="contactEmail" class="block text-sm font-medium text-gray-700">
                            <i class="ri-mail-line mr-2"></i> Email
                        </label>
                        <input type="email" id="contactEmail" name="email" class="w-full p-2 border border-gray-300 rounded-md focus:ring focus:ring-blue-300" required>
                    </div>
                    <div class="mb-4">
                        <label for="Subject" class="block text-sm font-medium text-gray-700">
                            <i class="ri-user-line mr-2"></i> Subject
                        </label>
                        <input type="text" id="Subject" name="subject" class="w-full p-2 border border-gray-300 rounded-md focus:ring focus:ring-blue-300" required>
                    </div>
                    <div class="mb-4">
                        <label for="contactMessage" class="block text-sm font-medium text-gray-700">Message</label>
                        <textarea id="contactMessage" name="message" rows="4" class="w-full p-2 border border-gray-300 rounded-md focus:ring focus:ring-blue-300" required></textarea>
                    </div>
                    <div class="flex justify-between items-center">
                        <button type="button" id="closeModalButton" class="bg-gray-500 text-white px-4 py-2 rounded-lg"><i class="ri-close-circle-line mr-2"></i>Close</button>
                        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-lg"><i class="ri-check-line mr-2"></i>Submit</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Appointment Modal -->
        <div id="appointmentModal" class="modal">
            <div class="modal-content">
                <h2 class="text-2xl font-bold mb-4 flex items-center">
                    <i class="ri-calendar-line mr-2"></i> Book an Appointment
                </h2>
                <form id="appointmentForm">
                    <div class="mb-4">
                        <label for="appointmentName" class="block text-sm font-medium text-gray-700">
                            <i class="ri-user-line mr-2"></i> Full Name
                        </label>
                        <input type="text" id="appointmentName" name="appointmentName" class="w-full p-2 border border-gray-300 rounded-md focus:ring focus:ring-blue-300" required>
                    </div>
                    <div class="mb-4">
                        <label for="appointmentEmail" class="block text-sm font-medium text-gray-700">
                            <i class="ri-mail-line mr-2"></i> Email
                        </label>
                        <input type="email" id="appointmentEmail" name="appointmentEmail" class="w-full p-2 border border-gray-300 rounded-md focus:ring focus:ring-blue-300" required>
                    </div>
                    <div class="mb-4">
                        <label for="appointmentDate" class="block text-sm font-medium text-gray-700">
                            <i class="ri-calendar-event-line mr-2"></i> Preferred Date
                        </label>
                        <input type="date" id="appointmentDate" name="appointmentDate" class="w-full p-2 border border-gray-300 rounded-md focus:ring focus:ring-blue-300" required>
                    </div>
                    <div class="mb-4">
                        <label for="appointmentTime" class="block text-sm font-medium text-gray-700">
                            <i class="ri-time-line mr-2"></i> Preferred Time
                        </label>
                        <input type="time" id="appointmentTime" name="appointmentTime" class="w-full p-2 border border-gray-300 rounded-md focus:ring focus:ring-blue-300" required>
                    </div>
                    <div class="flex justify-between items-center">
                        <button type="button" id="closeAppointmentModalButton" class="bg-gray-500 text-white px-4 py-2 rounded-lg flex items-center">
                            <i class="ri-close-circle-line mr-2"></i> Close
                        </button>
                        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-lg flex items-center">
                            <i class="ri-check-line mr-2"></i> Book Appointment
                        </button>
                    </div>
                </form>
            </div>
        </div>
                        
        

        <div class="text-center">
        <h5>Share Profile</h5>
        <canvas id="qrcode" style="position: relative; z-index: 1;" class="px-28 "></canvas>
        <div class="m-3 flex justify-center md:justify-start">
            <button 
                class="flex items-center space-x-2 px-4 py-2 text-sm sm:text-lg bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-all focus:outline-none focus:ring-2 focus:ring-blue-300"
                onclick="downloadQRCode()">
                <i class="ri-download-2-line text-base sm:text-lg"></i>
                <span class="hidden sm:inline">Download QR Code</span>
            </button>
        </div>


    </div>
    

    <script src="https://cdn.jsdelivr.net/npm/qrious/dist/qrious.min.js"></script>
                    
    <script>

        // Theme
        function changeTheme(theme) {
            const themeCard = document.getElementById('themeCard');
            const contentSection = document.getElementById('contentSection');

            if (theme === 'dark') {
                themeCard.classList.add('bg-gray-900', 'text-white');
                themeCard.classList.remove('bg-white', 'text-gray-900');
                contentSection.classList.add('bg-gray-800');
                contentSection.classList.remove('bg-white');
            } else if (theme === 'light') {
                themeCard.classList.add('bg-white', 'text-gray-900');
                themeCard.classList.remove('bg-gray-900', 'text-white');
                contentSection.classList.add('bg-white');
                contentSection.classList.remove('bg-gray-800');
            }
        }

        // Modal
        document.getElementById('openModalButton').addEventListener('click', () => {
            document.getElementById('contactModal').style.display = 'flex';
        });

        document.getElementById('closeModalButton').addEventListener('click', () => {
            document.getElementById('contactModal').style.display = 'none';
        });

        document.getElementById('openAppointmentModalButton').addEventListener('click', () => {
            document.getElementById('appointmentModal').style.display = 'flex';
        });

        document.getElementById('closeAppointmentModalButton').addEventListener('click', () => {
            document.getElementById('appointmentModal').style.display = 'none';
        });

        // Automatic Slider Functionality
        const images = document.querySelectorAll('.gallery img');
        const dots = document.querySelectorAll('.gallery .dot');
        let currentIndex = 0;
        const totalSlides = images.length;

        function changeSlide(index) {
            images[currentIndex].classList.remove('active');
            dots[currentIndex].classList.remove('active');
            currentIndex = index;
            images[currentIndex].classList.add('active');
            dots[currentIndex].classList.add('active');
        }

        function nextSlide() {
            const nextIndex = (currentIndex + 1) % totalSlides;
            changeSlide(nextIndex);
        }

        // Set interval for automatic slide change
        setInterval(nextSlide,3000);
        // nextSlide();

        // Add event listeners for manual slide change
        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                changeSlide(index);
            });
        });

    // Generate QR Code
    const qrCanvas = document.createElement("canvas");
    const qr = new QRious({
        element: qrCanvas,
        value: '<?php echo htmlspecialchars("http://localhost/testing/pages/view_profile.php"); ?>', // Replace with dynamic profile URL
        size: 200,
    });

    // Add background to QR Code
    const mainCanvas = document.getElementById('qrcode');
    const context = mainCanvas.getContext('2d');
    mainCanvas.width = 220; // Adjust size for background
    mainCanvas.height = 220;

    // Draw background color or image
    context.fillStyle = "#f0f0f0"; // Background color
    context.fillRect(0, 0, mainCanvas.width, mainCanvas.height);

    // Draw QR code onto the background canvas
    context.drawImage(qrCanvas, 10, 10); // Center QR code with padding

    // Download QR Code
    function downloadQRCode() {
        const qrImage = mainCanvas.toDataURL("image/png");
        const link = document.createElement("a");
        link.href = qrImage;
        link.download = "profile_qrcode.png";
        link.click();
    }
    </script>
    

</body>
</html>
