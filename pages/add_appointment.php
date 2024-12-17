<?php
require '../config/db_connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch available services for the dropdown
$services_sql = "SELECT * FROM services WHERE user_id = $user_id";
$services_result = $conn->query($services_sql);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $service_id = $_POST['service_id'];
    $appointment_date = $_POST['appointment_date'];

    // Validate inputs
    if (empty($service_id) || empty($appointment_date)) {
        $error_message = "All fields are required!";
    } else {
        // Insert into appointments table
        $sql = "INSERT INTO appointments (user_id, service_id, appointment_date) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iis", $user_id, $service_id, $appointment_date);

        if ($stmt->execute()) {
            $success_message = "Appointment added successfully!";
        } else {
            $error_message = "Failed to add appointment. Please try again.";
        }
    }
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="container mx-auto p-6">
    <h2 class="text-2xl font-bold mb-6">Add Appointment</h2>

    <!-- Success or Error Messages -->
    <?php if (isset($success_message)) : ?>
        <p class="text-green-500"><?php echo $success_message; ?></p>
    <?php elseif (isset($error_message)) : ?>
        <p class="text-red-500"><?php echo $error_message; ?></p>
    <?php endif; ?>

    <!-- Appointment Form -->
    <form action="add_appointment.php" method="POST">
        <div class="mb-4">
            <label for="service_id" class="block text-gray-700">Select Service</label>
            <select id="service_id" name="service_id" class="w-full p-3 border border-gray-300 rounded mt-2" required>
                <option value="">-- Select a Service --</option>
                <?php while ($service = $services_result->fetch_assoc()) : ?>
                    <option value="<?php echo $service['service_id']; ?>">
                        <?php echo htmlspecialchars($service['title']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-4">
            <label for="appointment_date" class="block text-gray-700">Appointment Date</label>
            <input type="datetime-local" id="appointment_date" name="appointment_date" class="w-full p-3 border border-gray-300 rounded mt-2" required>
        </div>

        <button type="submit" class="bg-blue-500 text-white px-6 py-2 rounded">Add Appointment</button>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
