<?php
require '../config/db_connection.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../phpmailer/PHPMailer.php';
require '../phpmailer/SMTP.php';
require '../phpmailer/Exception.php';

// Function to generate OTP
function generateOtp()
{
    return rand(100000, 999999);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Sanitize and validate inputs
        $username = isset($_POST['username']) ? htmlspecialchars(trim($_POST['username'])) : null;
        $email = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL) : null;
        $password = isset($_POST['password']) ? trim($_POST['password']) : null;

        if (!$username || !$email || !$password) {
            throw new Exception('Invalid input. Please ensure all fields are filled correctly.');
        }

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Prepare and execute the SQL statement
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $username, $email, $hashedPassword);

        if ($stmt->execute()) {
            $user_id = $stmt->insert_id;
            $stmt->close();

            // Create a profile entry for the user
            $profileStmt = $conn->prepare("INSERT INTO profiles (user_id) VALUES (?)");
            $profileStmt->bind_param('i', $user_id);
            $profileStmt->execute();
            $profileStmt->close();

            // Generate OTP and expiry time
            $otp = generateOtp();
            $expiry = date('Y-m-d H:i:s', strtotime('+15 minutes'));

            // Update the user with OTP and expiry
            $otpStmt = $conn->prepare("UPDATE users SET otp = ?, otp_expiry = ? WHERE email = ?");
            $otpStmt->bind_param('iss', $otp, $expiry, $email);
            $otpStmt->execute();
            $otpStmt->close();

            // Send OTP email using PHPMailer
            $mail = new PHPMailer(true);
            try {
                // SMTP settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'softbyvaibhav01@gmail.com'; // Your Gmail address
                $mail->Password = 'upuv xxno gwkx xvif';       // Your Gmail app password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port = 465;

                // Email settings
                $mail->setFrom('softbyvaibhav01@gmail.com', 'Digital Pachaan');
                $mail->addAddress($email);
                $mail->Subject = 'Your OTP Code';
                $mail->Body = "Your OTP code is: $otp. It is valid for 15 minutes.";

                // Send email
                $mail->send();

                // Start session and redirect to OTP page
                session_start();
                $_SESSION['email'] = $email;
                header("Location: otp_page.php");
                exit;
            } catch (Exception $e) {
                error_log("Email Error: " . $mail->ErrorInfo); // Log the error
                throw new Exception("Registration successful, but failed to send OTP email.");
            }
        } else {
            throw new Exception("Error: " . $conn->error);
        }
    } catch (Exception $e) {
        error_log("Registration Error: " . $e->getMessage()); // Log the error
        echo "Error during registration. Please try again later.";
    }
}
?>