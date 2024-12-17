<?php
session_start();
require 'config/constants.php';
?>

<?php include 'includes/header.php'; ?>

<!-- Navbar -->
<nav class="bg-gray-800 text-white">
    <div class="container mx-auto p-4 flex justify-between items-center">
        <!-- Logo -->
        <div class="flex items-center">
            <img src="assets/logo.jfif" alt="Logo" class="h-16 w-16 rounded-full mr-2">
            <span class="text-lg sm:text-xl font-semibold">Digital Pechaan</span>
        </div>

        <!-- Links -->
        <ul class="hidden md:flex space-x-6">
            <li><a href="#" class="hover:text-blue-400">Home</a></li>
            <li><a href="#about" class="hover:text-blue-400">About</a></li>
            <li><a href="#services" class="hover:text-blue-400">Services</a></li>
            <li><a href="#how-it-works" class="hover:text-blue-400">How It Works</a></li>
            <li><a href="#contact" class="hover:text-blue-400">Contact</a></li>
        </ul>

        <!-- Icons -->
        <div class="hidden md:flex space-x-4 items-center">
            <button id="loginBtn" class="flex items-center space-x-2 hover:text-blue-400">
                <i class="ri-login-circle-line text-lg"></i>
                <span>Login</span>
            </button>
            <button id="signupBtn" class="flex items-center space-x-2 hover:text-green-400">
                <i class="ri-user-add-line text-lg"></i>
                <span>Sign Up</span>
            </button>
        </div>

        <!-- Mobile Menu Toggle -->
        <button id="menuToggle" class="md:hidden text-white">
            <i class="ri-menu-line text-xl"></i>
        </button>
    </div>

    <!-- Mobile Menu -->
    <div id="mobileMenu" class="hidden bg-gray-800 text-white">
        <ul class="flex flex-col space-y-4 p-4">
            <li><a href="#" class="hover:text-blue-400">Home</a></li>
            <li><a href="#about" class="hover:text-blue-400">About</a></li>
            <li><a href="#services" class="hover:text-blue-400">Services</a></li>
            <li><a href="#how-it-works" class="hover:text-blue-400">How It Works</a></li>
            <li><a href="#contact" class="hover:text-blue-400">Contact</a></li>
            <li><button id="loginBtnMobile" class="flex items-center space-x-2 hover:text-blue-400">
                <i class="ri-login-circle-line text-lg"></i> <span>Login</span>
            </button></li>
            <li><button id="signupBtnMobile" class="flex items-center space-x-2 hover:text-green-400">
                <i class="ri-user-add-line text-lg"></i> <span>Sign Up</span>
            </button></li>
        </ul>
    </div>
</nav>

<!-- Login Modal -->
<div id="loginModal" class="hidden fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50">
    <div class="bg-gradient-to-b from-white to-gray-100 p-8 rounded-lg shadow-2xl w-11/12 sm:w-96 relative">
        <!-- Close Button -->
        <button id="closeLoginModal" class="absolute top-4 right-4 text-gray-400 hover:text-red-500">
            <i class="ri-close-circle-line text-2xl"></i>
        </button>

        <!-- Modal Header -->
        <h2 class="text-2xl font-extrabold text-gray-800 mb-6 text-center">
            Welcome Back!
        </h2>
        
        <!-- Form -->
        <form action="pages/login.php" method="POST">
            <!-- Email Input -->
            <div class="mb-4 relative">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" id="email" placeholder="Enter your email address" 
                       class="w-full border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 rounded-lg p-3 text-gray-900 placeholder-gray-400 shadow-sm">
                <i class="ri-mail-line absolute right-3 top-9 text-gray-400"></i>
            </div>
            
            <!-- Password Input -->
            <div class="mb-6 relative">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" id="password" placeholder="Create a secure password" 
                    class="w-full border border-gray-300 focus:ring-2 focus:ring-purple-400 focus:border-purple-400 rounded-lg p-3 text-gray-900 placeholder-gray-400 shadow-sm" require min-length="8">
                    <!-- Add minlength ="8" --> 
                <button type="button" id="togglePassword" class="absolute right-3 top-9 text-gray-400">
                    <i class="ri-eye-off-line" id="toggleIcon"></i>
                </button>
            </div>

            <!-- Forgot Password Link -->
            <div class="mb-6 text-right">
                <a href="pages/forget_password.php" class="text-sm text-blue-500 hover:underline">
                    Forgot Password?
                </a>
            </div>
            
            <!-- Submit Button -->
            <button type="submit" class="w-full bg-gradient-to-r from-blue-400 to-green-500 hover:from-blue-500 hover:to-green-600 text-white font-semibold py-3 rounded-lg shadow-md transition-all">
                <i class="ri-login-circle-line mr-2"></i>Login
            </button>
        </form>

        <!-- Divider
        <div class="my-6 text-center text-gray-500">or</div>

        Social Login
        <div class="flex space-x-4 justify-center">
            <button class="flex items-center space-x-2 bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-lg shadow-md transition-all">
                <i class="ri-google-line text-red-500 text-xl"></i>
                <span>Google</span>
            </button>
            <button class="flex items-center space-x-2 bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-lg shadow-md transition-all">
                <i class="ri-facebook-circle-line text-blue-600 text-xl"></i>
                <span>Facebook</span>
            </button>
        </div> -->
    </div>
</div>



<!-- Sign Up Modal -->
<div id="signupModal" class="hidden fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50">
    <div class="bg-gradient-to-b from-white to-gray-100 p-8 rounded-lg shadow-2xl w-11/12 sm:w-96 relative">
        <!-- Close Button -->
        <button id="closeSignupModal" class="absolute top-4 right-4 text-gray-400 hover:text-red-500">
            <i class="ri-close-circle-line text-2xl"></i>
        </button>

        <!-- Modal Header -->
        <h2 class="text-2xl font-extrabold text-gray-800 mb-6 text-center">
            Create Your Account
        </h2>
        
        <!-- Form -->
        <form action="pages/register.php" method="POST">
            <!-- Name Input -->
            <div class="mb-4 relative">
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Userame</label>
                <input type="text" name="username" id="username" placeholder="Enter your full name" 
                       class="w-full border border-gray-300 focus:ring-2 focus:ring-green-400 focus:border-green-400 rounded-lg p-3 text-gray-900 placeholder-gray-400 shadow-sm">
                <i class="ri-user-line absolute right-3 top-9 text-gray-400"></i>
            </div>
            
            <!-- Email Input -->
            <div class="mb-4 relative">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" id="email" placeholder="Enter your email address" 
                       class="w-full border border-gray-300 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 rounded-lg p-3 text-gray-900 placeholder-gray-400 shadow-sm">
                <i class="ri-mail-line absolute right-3 top-9 text-gray-400"></i>
            </div>
            
            <!-- Password Input -->
            <div class="mb-6 relative">
                <label for="spassword" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" id="spassword" placeholder="Create a secure password" 
                    class="w-full border border-gray-300 focus:ring-2 focus:ring-purple-400 focus:border-purple-400 rounded-lg p-3 text-gray-900 placeholder-gray-400 shadow-sm">
                <button type="button" id="stogglePassword" class="absolute right-3 top-9 text-gray-400">
                    <i class="ri-eye-off-line" id="stoggleIcon"></i>
                </button>
            </div>

            
            <!-- Submit Button -->
            <button type="submit" class="w-full bg-gradient-to-r from-green-400 to-blue-500 hover:from-green-500 hover:to-blue-600 text-white font-semibold py-3 rounded-lg shadow-md transition-all">
                <i class="ri-login-circle-line mr-2"></i>Sign Up
            </button>
        </form>

        <!-- Divider
        <div class="my-6 text-center text-gray-500">or</div>

        Social Sign-Up
        <div class="flex space-x-4 justify-center">
            <button class="flex items-center space-x-2 bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-lg shadow-md transition-all">
                <i class="ri-google-line text-red-500 text-xl"></i>
                <span>Google</span>
            </button>
            <button class="flex items-center space-x-2 bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-lg shadow-md transition-all">
                <i class="ri-facebook-circle-line text-blue-600 text-xl"></i>
                <span>Facebook</span>
            </button>
        </div> -->
    </div>
</div>




<script>

    document.addEventListener('DOMContentLoaded', () => {
        const passwordInput = document.getElementById('password');
        const toggleButton = document.getElementById('togglePassword');
        const toggleIcon = document.getElementById('toggleIcon');

        toggleButton.addEventListener('click', () => {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';

            // Update the icon
            toggleIcon.className = isPassword ? 'ri-eye-line' : 'ri-eye-off-line';
        });
    });

    document.addEventListener('DOMContentLoaded', () => {
        const passwordInput = document.getElementById('spassword');
        const toggleButton = document.getElementById('stogglePassword');
        const toggleIcon = document.getElementById('stoggleIcon');

        toggleButton.addEventListener('click', () => {
            const isPassword = passwordInput.type === 'password';
            passwordInput.type = isPassword ? 'text' : 'password';

            // Update the icon
            toggleIcon.className = isPassword ? 'ri-eye-line' : 'ri-eye-off-line';
        });
    });

    // Mobile Menu Toggle
    const menuToggle = document.getElementById('menuToggle');
    const mobileMenu = document.getElementById('mobileMenu');
    menuToggle.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
    });

    // Modals
    const loginBtn = document.getElementById('loginBtn');
    const signupBtn = document.getElementById('signupBtn');
    const loginBtnMobile = document.getElementById('loginBtnMobile');
    const signupBtnMobile = document.getElementById('signupBtnMobile');
    const loginModal = document.getElementById('loginModal');
    const signupModal = document.getElementById('signupModal');
    const closeLoginModal = document.getElementById('closeLoginModal');
    const closeSignupModal = document.getElementById('closeSignupModal');

    const openModal = (modal) => modal.classList.remove('hidden');
    const closeModal = (modal) => modal.classList.add('hidden');

    loginBtn.addEventListener('click', () => openModal(loginModal));
    signupBtn.addEventListener('click', () => openModal(signupModal));
    loginBtnMobile.addEventListener('click', () => openModal(loginModal));
    signupBtnMobile.addEventListener('click', () => openModal(signupModal));
    closeLoginModal.addEventListener('click', () => closeModal(loginModal));
    closeSignupModal.addEventListener('click', () => closeModal(signupModal));
</script>

<section class="hero bg-gradient-to-r text-black to-teal-400 py-16 px-6 md:px-20">
  <div class="container mx-auto flex flex-col md:flex-row items-center space-y-8 md:space-y-0">
    <!-- Left Section -->
    <div class="text-center md:text-left w-full md:w-1/2 animate__animated animate__fadeInLeft">
      <h1 class="text-4xl sm:text-5xl font-extrabold  mb-4 animate__animated animate__fadeIn animate__delay-1s">
        Transform Your Business with Our Solutions
      </h1>
      <p class="text-lg  mb-6 animate__animated animate__fadeIn animate__delay-2s">
        Our innovative tools and strategies will take your business to the next level, providing a seamless experience to your customers.
      </p>
      <button class="bg-gradient-to-r from-blue-500 to-teal-500 text-white px-6 py-3 rounded-md shadow-lg hover:shadow-2xl transition-all animate__animated animate__fadeIn animate__delay-3s">
        Get Started
      </button>
    </div>

    <!-- Right Section (Video) -->
    <div class="w-full md:w-1/2 animate__animated animate__fadeIn animate__delay-2s">
      <div class="relative overflow-hidden rounded-xl shadow-xl animate__animated animate__zoomIn">
        <video class="w-full h-full object-cover" controls>  <!-- Remove Controls on it -->
          <source src="path_to_video.mp4" type="video/mp4">
          Your browser does not support the video tag.
        </video>
      </div>
    </div>
  </div>
</section>



<section class="about-section py-20 px-4 ">
    <div class="container mx-auto flex flex-col lg:flex-row items-center justify-between gap-10">
        <!-- Left Section: Image with Animation -->
        <div class="w-full lg:w-1/2 relative animate__animated animate__fadeIn animate__delay-1s">
            <img src="your-image.jpg" alt="About Us" class="w-full h-auto rounded-xl shadow-lg transform hover:scale-105 transition-all">
        </div>

        <!-- Right Section: Text with Animation -->
        <div class="w-full lg:w-1/2 text-gray-800 text-center lg:text-left space-y-6 animate__animated animate__fadeIn animate__delay-1s">
            <h2 class="text-4xl lg:text-5xl font-semibold text-gray-900">
                About Us
            </h2>
            <p class="text-lg lg:text-xl text-gray-700">
                We are a team of dedicated professionals committed to delivering cutting-edge solutions that drive digital transformation for businesses across industries. With years of experience, we provide tailored strategies to help you grow in an ever-evolving digital world.
            </p>
            <ul class="list-disc list-inside text-lg text-gray-600 space-y-4">
                <li>Innovative Digital Solutions</li>
                <li>Passionate Team of Experts</li>
                <li>Transforming Ideas into Reality</li>
                <li>Commitment to Excellence</li>
            </ul>
            <div class="flex gap-6 justify-center lg:justify-start">
                <button class="bg-blue-500 hover:bg-blue-600 transition-all text-white py-3 px-8 rounded-full shadow-md transform hover:scale-105 focus:outline-none animate__animated animate__fadeIn animate__delay-2s">
                    Join Us
                </button>
                <button class="border-2 border-gray-500 text-gray-700 py-3 px-8 rounded-full shadow-md transform hover:scale-105 focus:outline-none animate__animated animate__fadeIn animate__delay-3s">
                    Learn More
                </button>
            </div>
        </div>
    </div>
</section>

<style>
    
    /* Media Queries for Responsiveness */
    @media (max-width: 768px) {
        .about-section {
            padding-top: 12px;
            padding-bottom: 12px;
        }

        .about-section .container {
            flex-direction: column;
        }

        .about-section h2 {
            font-size: 2.5rem;
        }

        .about-section p {
            font-size: 1rem;
        }

        .about-section .image-section {
            width: 100%;
            margin-top: 20px;
        }
    }
</style>
