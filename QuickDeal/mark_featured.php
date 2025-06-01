<?php
session_start();
include './db/db_conn.php';

$loggedInUserId = $_SESSION['user_id'] ?? null;
$itemId = $_POST['item_id'] ?? null;

if (!$loggedInUserId || !$itemId) {
    die("Unauthorized access.");
}

// Check if user has premium subscription
$stmt = $conn->prepare("SELECT * FROM subscriptions WHERE user_id = ? AND plan = 'premium' AND end_date >= CURDATE()");
$stmt->bind_param("i", $loggedInUserId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("You are not a premium user.");
}

// Count currently active featured listings (less than 7 days old)
$stmt = $conn->prepare("
    SELECT COUNT(*) as featured_count 
    FROM items 
    WHERE user_id = ? AND is_featured = 1 AND featured_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
");
$stmt->bind_param("i", $loggedInUserId);
$stmt->execute();
$countResult = $stmt->get_result()->fetch_assoc();

if ($countResult['featured_count'] >= 6) {
    die("You can only feature up to 6 items at a time.");
}

// Mark item as featured
$stmt = $conn->prepare("UPDATE items SET is_featured = 1, featured_at = NOW() WHERE item_id = ? AND user_id = ?");
$stmt->bind_param("ii", $itemId, $loggedInUserId);
if ($stmt->execute()) {
    header("Location: index.php?featured=success");
    exit;
} else {
    echo "Failed to feature item.";
}
