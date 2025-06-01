<?php
include './auth/auth.php';
include './db/db_conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $itemId = $_POST['item_id'] ?? null;
    $userId = $_SESSION['user_id'] ?? null;

    if ($itemId && $userId) {
        // Ensure the item belongs to the logged-in user
        $stmt = $conn->prepare("DELETE FROM items WHERE item_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $itemId, $userId);
        if ($stmt->execute()) {
            $stmt->close();
            header("Location: listing.php"); // or your main listings page
            exit;
        } else {
            echo "Failed to delete item.";
        }
    } else {
        echo "Invalid request.";
    }
} else {
    echo "Unauthorized access.";
}
?>
