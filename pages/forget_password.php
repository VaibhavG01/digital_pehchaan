<?php
require '../config/db_connection.php'; // Database connection

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require "../phpmailer/PHPMailer.php";
require "../phpmailer/Exception.php";
require "../phpmailer/SMTP.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reset'])) {
    $email = trim($_POST['email']);
    $responseMessage = '';

    // Check if email exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);  // Bind the email parameter to prevent SQL injection
    $stmt->execute();
    $stmt->store_result();  // Store the result of the SELECT query

    if ($stmt->num_rows > 0) {
        // If user exists, generate OTP and expiry
        $otp = rand(100000, 999999);
        $expiry = date('Y-m-d H:i:s', strtotime('+15 minutes'));

        // Update database with OTP and expiry
        $stmt->close();  // Close the previous statement before executing a new one
        $stmt = $conn->prepare("UPDATE users SET otp = ?, otp_expiry = ? WHERE email = ?");
        $stmt->bind_param("sss", $otp, $expiry, $email);  // Bind parameters for the UPDATE query

        if ($stmt->execute()) {
            // Send OTP email using PHPMailer
            $mail = new PHPMailer(true);

            try {
                // SMTP settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'softbyvaibhav01@gmail.com'; // Your Gmail address
                $mail->Password = 'upuv xxno gwkx xvif'; // Your Gmail app password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Secure SSL/TLS encryption
                $mail->Port = 465;

                // Email settings
                $mail->setFrom('softbyvaibhav01@gmail.com', 'Your Website Name');
                $mail->addAddress($email);
                $mail->Subject = 'Password Reset OTP';
                $mail->Body = "Your OTP code is: $otp. It is valid for 15 minutes.";

                // Send email
                $mail->send();

                // Store email in session and redirect to OTP verification page
                session_start();
                $_SESSION['email'] = $email;
                header("Location: otp_verify.php");
                exit;
            } catch (Exception $e) {
                $responseMessage = '<div class="alert alert-danger">Failed to send OTP. Error: ' . htmlspecialchars($mail->ErrorInfo) . '</div>';
            }
        } else {
            $responseMessage = '<div class="alert alert-danger">Failed to update OTP in the database. Please try again.</div>';
        }
    } else {
        $responseMessage = '<div class="alert alert-danger">Email address not found!</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-container {
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            border-radius: 8px;
        }
        .form-container:hover {
            border: 1px solid #007bff;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 form-container">
                <h2>Forgot Password</h2>
                <!-- Display response message -->
                <?php if (!empty($responseMessage)) { echo $responseMessage; } ?>
                <form action="forget_password.php" method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <button type="submit" name="reset" class="btn btn-primary">Send OTP</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
