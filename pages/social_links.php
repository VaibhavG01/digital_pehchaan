<?php
require '../config/db_connection.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user and social links details
$sql = "SELECT u.username, sl.facebook, sl.instagram, sl.whatsapp, sl.website, sl.google_reviews, sl.google_map
        FROM users u
        LEFT JOIN social_links sl ON u.user_id = sl.user_id
        WHERE u.user_id = $user_id";

$result = $conn->query($sql);
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="container mx-auto p-6">
    <h2 class="text-2xl font-bold mb-6">Your Social Links</h2>

    <?php if ($result->num_rows > 0) : ?>
        <?php while ($row = $result->fetch_assoc()) : ?>
            <div class="bg-white shadow-lg rounded-lg p-6 mb-4">
                <h3 class="text-xl font-bold mb-4">Social Media Links</h3>
                <p><strong>Facebook:</strong> <a href="<?php echo htmlspecialchars($row['facebook']); ?>" target="_blank" class="text-blue-500"><?php echo htmlspecialchars($row['facebook']); ?></a></p>
                <p><strong>Instagram:</strong> <a href="<?php echo htmlspecialchars($row['instagram']); ?>" target="_blank" class="text-blue-500"><?php echo htmlspecialchars($row['instagram']); ?></a></p>
                <p><strong>WhatsApp:</strong> <a href="<?php echo htmlspecialchars($row['whatsapp']); ?>" target="_blank" class="text-blue-500"><?php echo htmlspecialchars($row['whatsapp']); ?></a></p>
                <p><strong>Website:</strong> <a href="<?php echo htmlspecialchars($row['website']); ?>" target="_blank" class="text-blue-500"><?php echo htmlspecialchars($row['website']); ?></a></p>
                <p><strong>Google Reviews:</strong> <a href="<?php echo htmlspecialchars($row['google_reviews']); ?>" target="_blank" class="text-blue-500"><?php echo htmlspecialchars($row['google_reviews']); ?></a></p>
                <p><strong>Google Map:</strong> <a href="<?php echo htmlspecialchars($row['google_map']); ?>" target="_blank" class="text-blue-500"><?php echo htmlspecialchars($row['google_map']); ?></a></p>
            </div>
        <?php endwhile; ?>
    <?php else : ?>
        <p>No social links available.</p>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
