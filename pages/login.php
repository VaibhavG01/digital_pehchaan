<?php
require '../config/db_connection.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid credentials!";
            header("Location: ../index.php");
        }
    } else {
        $error = "Invalid credentials!";
        header("Location: ../index.php");
    }
}
?>