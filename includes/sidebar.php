<!-- Sidebar (For mobile and large screens) -->
        <div class="lg:w-1/5 w-full h-auto bg-slate-800 p-6 shadow-lg mb-6 lg:mb-0 relative backdrop-blur-md bg-opacity-40">
            

            <!-- Dashboard Menu -->
            <ul class="mt-6 space-y-4 text-white">
                <li class="flex items-center group">
                    <i class="ri-dashboard-line text-2xl mr-2 group-hover:text-blue-700 transition-colors duration-300"></i>
                    <a href="dashboard.php" class="hover:text-blue-700 transition duration-300 group-hover:pl-2">Dashboard Overview</a>
                </li>
                <li class="flex items-center group">
                    <i class="ri-user-settings-line text-2xl mr-2 group-hover:text-blue-700 transition-colors duration-300"></i>
                    <a href="edit_profile.php" class="hover:text-blue-700 transition duration-300 group-hover:pl-2">Edit Profile</a>
                </li>
                <li class="flex items-center group">
                    <i class="ri-settings-2-line text-2xl mr-2 group-hover:text-blue-700 transition-colors duration-300"></i>
                    <a href="services.php" class="hover:text-blue-700 transition duration-300 group-hover:pl-2">Manage Services</a>
                </li>
                <li class="flex items-center group">
                    <i class="ri-links-line text-2xl mr-2 group-hover:text-blue-700 transition-colors duration-300"></i>
                    <a href="add_social.php" class="hover:text-blue-700 transition duration-300 group-hover:pl-2">Social Links</a>
                </li>
                <li class="flex items-center group">
                    <i class="ri-gallery-line text-2xl mr-2 group-hover:text-blue-700 transition-colors duration-300"></i>
                    <a href="gallery.php" class="hover:text-blue-700 transition duration-300 group-hover:pl-2">Manage Gallery</a>
                </li>
                <li class="flex items-center group">
                    <i class="ri-mail-line text-2xl mr-2 group-hover:text-blue-700 transition-colors duration-300"></i>
                    <a href="admin_contact_message.php" class="hover:text-blue-700 transition duration-300 group-hover:pl-2">View Contacts</a>
                </li>
                <li class="flex items-center group">
                    <i class="ri-calendar-line text-2xl mr-2 group-hover:text-blue-700 transition-colors duration-300"></i>
                    <a href="view_appointments.php" class="hover:text-blue-700 transition duration-300 group-hover:pl-2">View Appointments</a>
                </li>

                <li class="flex items-center group">
                    <i class="ri-user-line text-2xl mr-2 group-hover:text-blue-700 transition-colors duration-300"></i>
                    <a href="view_profile.php" class="hover:text-blue-700 transition duration-300 group-hover:pl-2">View Profile</a>
                </li>
                <!-- <li class="flex items-center group">
                    <i class="ri-user-line text-2xl mr-2 group-hover:text-blue-700 transition-colors duration-300"></i>
                    <a href="save_profile_design.php" class="hover:text-blue-700 transition duration-300 group-hover:pl-2">Select Profile</a>
                </li> -->
                <li class="flex items-center group">
                    <i class="ri-logout-box-line text-red-500 text-2xl mr-2 group-hover:text-red-700 transition-colors duration-300"></i>
                    <a href="logout.php" class="hover:text-red-700 transition duration-300 group-hover:pl-2">Logout</a>
                </li>
            </ul>
        </div>

        <!-- Mobile Sidebar -->
        <div id="mobileSidebar" class="lg:hidden fixed inset-0 bg-slate-800 hidden z-50 backdrop-blur-md">
            <div class="flex justify-end p-4">
                <button id="closeSidebar" class="text-white text-3xl">
                    <i class="ri-close-line"></i>
                </button>
            </div>
            <div class="flex justify-center items-center h-full">
                <ul class="space-y-6 text-white">
                    <li class="flex items-center">
                        <i class="ri-dashboard-line text-2xl mr-2"></i>
                        <a href="dashboard.php" class="hover:text-blue-700 transition duration-300">Dashboard Overview</a>
                    </li>
                    <li class="flex items-center">
                        <i class="ri-user-settings-line text-2xl mr-2"></i>
                        <a href="edit_profile.php" class="hover:text-blue-700 transition duration-300">Edit Profile</a>
                    </li>
                    <li class="flex items-center">
                        <i class="ri-service-line text-2xl mr-2"></i>
                        <a href="add_service.php" class="hover:text-blue-700 transition duration-300">Add Services</a>
                    </li>
                    <li class="flex items-center">
                        <i class="ri-settings-2-line text-2xl mr-2"></i>
                        <a href="services.php" class="hover:text-blue-700 transition duration-300">Manage Services</a>
                    </li>
                    <li class="flex items-center">
                        <i class="ri-links-line text-2xl mr-2"></i>
                        <a href="add_social_links.php" class="hover:text-blue-700 transition duration-300">Social Links</a>
                    </li>
                    <li class="flex items-center">
                        <i class="ri-gallery-line text-2xl mr-2"></i>
                        <a href="gallery.php" class="hover:text-blue-700 transition duration-300">Manage Gallery</a>
                    </li>
                    <li class="flex items-center">
                        <i class="ri-user-line text-2xl mr-2"></i>
                        <a href="view_profile.php" class="hover:text-blue-700 transition duration-300">View Profile</a>
                    </li>
                    <li class="flex items-center">
                        <i class="ri-logout-box-line text-red-500 text-2xl mr-2"></i>
                        <a href="logout.php" class="hover:text-red-700 transition duration-300">Logout</a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Script for Sidebar Toggle -->
        <script>
            const toggleSidebar = document.getElementById('toggleSidebar');
            const mobileSidebar = document.getElementById('mobileSidebar');
            const closeSidebar = document.getElementById('closeSidebar');

            toggleSidebar.addEventListener('click', () => {
                mobileSidebar.classList.remove('hidden');
            });

            closeSidebar.addEventListener('click', () => {
                mobileSidebar.classList.add('hidden');
            });
        </script>
