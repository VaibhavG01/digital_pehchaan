<nav class="bg-gray-800 p-4 text-white">
    <div class="container mx-auto flex justify-between items-center">
        <a href="index.php" class="text-lg font-bold">MyWebsite</a>
        <div class="space-x-6 hidden md:flex">
            <a href="index.php" class="hover:text-blue-400">Home</a>
            <a href="pages/dashboard.php" class="hover:text-blue-400">Dashboard</a>
            <a href="pages/edit_profile.php" class="hover:text-blue-400">Edit Profile</a>
            <a href="pages/logout.php" class="hover:text-red-400">Logout</a>
        </div>
        <div class="md:hidden">
            <button id="menu-btn">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>
    </div>
    <div id="mobile-menu" class="hidden">
        <a href="index.php" class="block p-2 hover:text-blue-400">Home</a>
        <a href="dashboard.php" class="block p-2 hover:text-blue-400">Dashboard</a>
        <a href="edit_profile.php" class="block p-2 hover:text-blue-400">Edit Profile</a>
        <a href="logout.php" class="block p-2 hover:text-red-400">Logout</a>
    </div>
</nav>
<script>
    document.getElementById('menu-btn').addEventListener('click', function () {
        document.getElementById('mobile-menu').classList.toggle('hidden');
    });
</script>
