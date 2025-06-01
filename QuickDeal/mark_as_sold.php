<?php
include './auth/auth.php';
include './db/db_conn.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $itemId = $_POST['item_id'] ?? null;
    $userId = $_SESSION['user_id'] ?? null;

    if ($itemId && $userId) {
        $stmt = $conn->prepare("UPDATE items SET status = 'Sold', updated_at = NOW() WHERE item_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $itemId, $userId);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            // Success
            header("Location: listing.php"); // or wherever your listing page is
            exit;
        } else {
            echo "Failed to update status or unauthorized access.";
        }
        $stmt->close();
    } else {
        echo "Invalid request.";
    }
} else {
    echo "Invalid request method.";
}
?>
