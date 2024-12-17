<?php
session_start();
require '../db/config.php';

if (!isset($_SESSION['email'])) {
    header("Location: forget_password.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reset_password'])) {
    $email = $_SESSION['email'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);

    // Update the password in the database
    $stmt = $conn->prepare("UPDATE users SET password = ?, otp = NULL, otp_expiry = NULL WHERE email = ?");
    if ($stmt->execute([$new_password, $email])) {
        unset($_SESSION['email']);
        header("Location: ../index.php?message=Password reset successful!");
        exit;
    } else {
        $error = "Failed to reset password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .position-relative {
            position: relative;
        }
        .position-absolute {
            position: absolute;
        }

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
                <h2>Reset Password</h2>
                <?php if (isset($error)) { ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php } ?>
                <form action="reset_password.php" method="POST">
                <div class="mb-3 position-relative">
                    <label for="new_password" class="form-label">New Password</label>
                    <input 
                        type="password" 
                        class="form-control" 
                        id="new_password" 
                        name="new_password" 
                        minlength="8" 
                        required>
                    <!-- Icon for show/hide -->
                    <i 
                        id="togglePassword" 
                        class="ri-eye-off-line position-absolute" 
                        style="top: 35px; right: 10px; cursor: pointer;">
                    </i>
                </div>

                    <button type="submit" name="reset_password" class="btn btn-primary">Reset Password</button>
                </form>
            </div>
        </div>
    </div>


    <script>
        document.getElementById("togglePassword").addEventListener("click", function () {
            const passwordField = document.getElementById("new_password");
            const icon = this;
            // Toggle the password field type
            if (passwordField.type === "password") {
                passwordField.type = "text";
                icon.classList.remove("ri-eye-off-line");
                icon.classList.add("ri-eye-line");
            } else {
                passwordField.type = "password";
                icon.classList.remove("ri-eye-line");
                icon.classList.add("ri-eye-off-line");
            }
        });

    </script>
</body>
</html>
