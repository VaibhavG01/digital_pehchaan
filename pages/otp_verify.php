<?php
session_start();
require '../config/db_connection.php'; // Database connection

// Ensure email exists in the session
if (!isset($_SESSION['email'])) {
    header("Location: forget_password.php"); // Redirect to forget password page if email is not set
    exit;
}

$email = $_SESSION['email']; // Get the email from the session
$error = $success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['verify'])) {
    $otp = trim($_POST['otp']);

    if (!empty($otp)) {
        // Validate OTP
        $stmt = $conn->prepare("SELECT otp, otp_expiry FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            if ($user['otp'] == $otp && strtotime($user['otp_expiry']) > time()) {
                // OTP is valid
                $_SESSION['verified'] = true; // Mark as verified
                header("Location: reset_password.php"); // Redirect to reset password page
                exit;
            } else {
                $error = "Invalid or expired OTP. Please try again.";
            }
        } else {
            $error = "User not found.";
        }
    } else {
        $error = "OTP field cannot be empty.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
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
                <h2>OTP Verification</h2>
                <!-- Display error or success messages -->
                <?php if (!empty($error)) { ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php } ?>
                <?php if (!empty($success)) { ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php } ?>
                <form action="otp_verify.php" method="POST">
                    <div class="mb-3">
                        <label for="otp" class="form-label">Enter OTP</label>
                        <input type="text" class="form-control" id="otp" name="otp" maxlength="6" pattern="\d{6}" 
                               placeholder="Enter 6-digit OTP" required>
                    </div>
                    <button type="submit" name="verify" class="btn btn-primary">Verify OTP</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
