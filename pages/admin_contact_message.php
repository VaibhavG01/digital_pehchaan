<?php
session_start();
require '../config/db_connection.php';

// Fetch contact messages for a specific user (e.g., admin or logged-in user)
$user_id = $_SESSION['user_id'];  // Use session to get user ID
$sql = "SELECT * FROM contact_messages WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<?php include '../includes/header.php'; ?>
<body>
<div class="flex h-screen">
    <!-- Sidebar -->
    <?php include '../includes/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="flex-1 overflow-y-auto p-6">
        <h2 class="text-2xl font-semibold mb-6">Contact Messages</h2>

        <?php if ($result->num_rows > 0) : ?>
            <div class="overflow-x-auto bg-white rounded-lg shadow-md">
                <table class="min-w-full table-auto">
                    <thead>
                        <tr class="bg-gray-100 text-left">
                            <th class="px-4 py-2">Name</th>
                            <th class="px-4 py-2">Email</th>
                            <th class="px-4 py-2">Subject</th>
                            <th class="px-4 py-2">Message</th>
                            <th class="px-4 py-2">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($message = $result->fetch_assoc()) : ?>
                            <tr class="border-t">
                                <td class="px-4 py-2"><?php echo htmlspecialchars($message['name']); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($message['email']); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($message['subject']); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($message['message']); ?></td>
                                <td class="px-4 py-2"><?php echo htmlspecialchars($message['created_at']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else : ?>
            <p class="text-gray-500">No messages yet.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>