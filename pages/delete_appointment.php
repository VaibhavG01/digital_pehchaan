<?php
require '../config/db_connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $appointment_id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    // Delete the appointment
    $sql = "DELETE FROM appointments WHERE appointment_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $appointment_id, $user_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Appointment deleted successfully.";
    } else {
        $_SESSION['message'] = "Failed to delete appointment.";
    }
}

header("Location: view_appointments.php");
exit;
