<?php
session_start();
require '../config/db_connection.php'; // Database connection

if (!isset($_SESSION['email'])) {
    header("Location: register.php");
    exit;
}

$message = ""; // Initialize message variable

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify'])) {
    $email = $_SESSION['email'];
    $otp = $_POST['otp'];

    try {
        // Fetch OTP and expiry time from database
        $stmt = $conn->prepare("SELECT otp, otp_expiry FROM users WHERE email = ?");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user) {
            if ($user['otp'] == $otp && strtotime($user['otp_expiry']) > time()) {
                // OTP is valid
                $stmt = $conn->prepare("UPDATE users SET otp = NULL, otp_expiry = NULL WHERE email = ?");
                $stmt->bind_param('s', $email);
                $stmt->execute();

                unset($_SESSION['email']); // Clear session after successful verification
                $message = "OTP verified successfully. Redirecting to login...";

                header("refresh:2;url=../index.php");
                exit;
            } else {
                // OTP is invalid or expired
                $message = "Incorrect or expired OTP. Please try again.";
            }
        } else {
            $message = "User not found. Please register again.";
            header("refresh:2;url=register.php");
            exit;
        }
    } catch (Exception $e) {
        error_log("OTP Verification Error: " . $e->getMessage());
        $message = "An error occurred. Please try again later.";
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
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2 class="text-center">OTP Verification</h2>

                <?php if (!empty($message)): ?>
                    <div class="alert <?php echo (strpos($message, 'successfully') !== false) ? 'alert-success' : 'alert-danger'; ?>" role="alert">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <form action="otp_page.php" method="POST">
                    <div class="mb-3">
                        <label for="otp" class="form-label">Enter OTP</label>
                        <input type="text" class="form-control" id="otp" name="otp" required>
                    </div>
                    <button type="submit" name="verify" class="btn btn-primary w-100">Verify OTP</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
