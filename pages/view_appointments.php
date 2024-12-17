<?php
require '../config/db_connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch appointments with service details
$sql = "SELECT a.appointment_id, a.appointment_date, a.status, s.title 
        FROM appointments a
        JOIN services s ON a.service_id = s.service_id
        WHERE a.user_id = $user_id";
$result = $conn->query($sql);
?>

<?php include '../includes/header.php'; ?>
<body>
<div class="flex flex-col md:flex-row h-screen">
    <!-- Sidebar -->
    <div class="md:w-1/4 p-6">
        <?php include '../includes/sidebar.php'; ?>
    </div>

    <!-- Main Content -->
    <div class="md:w-3/4 p-6 overflow-auto">
        <h2 class="text-2xl font-bold mb-6">Your Appointments</h2>

        <?php if ($result->num_rows > 0) : ?>
            <table class="min-w-full bg-white border border-gray-300">
                <thead>
                    <tr>
                        <th class="border px-4 py-3 text-left">Service</th>
                        <th class="border px-4 py-3 text-left">Appointment Date</th>
                        <th class="border px-4 py-3 text-left">Status</th>
                        <th class="border px-4 py-3 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($appointment = $result->fetch_assoc()) : ?>
                        <tr>
                            <td class="border px-4 py-3"><?php echo htmlspecialchars($appointment['title']); ?></td>
                            <td class="border px-4 py-3"><?php echo htmlspecialchars($appointment['appointment_date']); ?></td>
                            <td class="border px-4 py-3"><?php echo htmlspecialchars($appointment['status']); ?></td>
                            <td class="border px-4 py-3">
                                <a href="delete_appointment.php?id=<?php echo $appointment['appointment_id']; ?>" 
                                   onclick="return confirm('Are you sure you want to delete this appointment?');" 
                                   class="text-red-500 hover:underline">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else : ?>
            <p class="text-gray-500">You have no appointments yet.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
