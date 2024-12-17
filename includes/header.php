<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Project</title>
    
    <style>
        #qrcode canvas {
            margin: 0 auto;
            max-width: 200px;
            max-height: 200px;
        }

        #downloadQRCodeButton {
            transition: all 0.3s;
        }

        #downloadQRCodeButton:hover {
            background-color: #38a169; /* Darker green on hover */
            transform: scale(1.05);
        }


        @media print {
            body {
                font-family: Arial, sans-serif;
            }
            #profileContent {
                width: 100%;
                margin: 0 auto;
            }
        }
        /* Include your custom styles here */
        .fade-up { opacity: 0; transform: translateY(20px); animation: fadeUp 1s forwards; }
        @keyframes fadeUp { to { opacity: 1; transform: translateY(0); } }
        /* Typewriter Animation */
        .typewriter h1 {
            overflow: hidden;
            white-space: nowrap;
            margin: 0 auto;
            animation: typing 3s steps(30, end), 0.7s step-end infinite;
        }

        @keyframes typing {
            from {
                width: 0;
            }

            to {
                width: 100%;
            }
        }

        /* Floating Dots Styling */
        .floating-dots {
            position: absolute;
            bottom: -50px;
            left: 90%;
            transform: translateX(-50%);
            display: flex;
            gap: 10px;
            z-index: 9;
        }

        .dot {
            width: 15px;
            height: 15px;
            border-radius: 50%;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        /* Modal Styles */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 10;
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        /* Fade-Up Animation for Images */
        .fade-up {
            opacity: 0;
            l transform: translateY(20px);
            animation: fadeUp 1s forwards;
        }

        @keyframes fadeUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Delay for Profile and Banner Images */
        .fade-up-banner {
            animation-delay: 0.3s;
        }

        .fade-up-profile {
            animation-delay: 0.6s;
        }


        /* Slider Styles */
        .gallery {
            position: relative;
            overflow: hidden;
            max-width: 100%;
            margin: 2rem auto;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .gallery img {
            width: 100%;
            display: none;
            transition: opacity 0.5s;
        }

        .gallery img.active {
            display: block;
            opacity: 1;
        }

        .gallery .pagination {
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 8px;
        }

        .gallery .dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.7);
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .gallery .dot.active {
            background-color: white;
        }
        
    </style>

    <link rel="stylesheet" href="assets/css/tailwind.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <!-- Add Remix Icon CDN -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.9.1/gsap.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/qrcode/build/qrcode.min.js"></script>
</head>