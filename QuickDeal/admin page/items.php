<?php
// Connect to DB
include("../db/db_conn.php");

// Handle approve/reject action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['item_id'])) {
    $itemId = intval($_POST['item_id']);
    $newStatus = ($_POST['action'] === 'approve') ? 'Available' : 'Rejected';

    $stmt = $conn->prepare("UPDATE items SET status = ?, updated_at = NOW() WHERE item_id = ?");
    $stmt->bind_param("si", $newStatus, $itemId);
    $stmt->execute();
    $stmt->close();
}

// Fetch pending items
$sql = "SELECT * FROM items WHERE status = 'Pending' ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Items - QuickDeal Dashboard</title>
  <link rel="stylesheet" href="items.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
</head>
<body>
  <aside class="sidebar">
    <div class="logo">
      <img src="logo.png" alt="Logo" class="logo-img" />
      <h1>QuickDeal</h1>
    </div>
    <div class="profile">
      <div class="profile-pic">
        <img src="https://m.media-amazon.com/images/M/MV5BNzA1MDBhM2MtMTg0MC00NzUxLWFjZWMtNmE5Y2M3ZDJhYmYyXkEyXkFqcGc@._V1_.jpg" alt="Admin Profile" />
      </div>
      <p>Welcome Admin!</p>
    </div>
    <nav>
      <a href="admin.php">🏠 Dashboard</a>
      <a href="items.php" class="active">📦 Items</a>
      <a href="sales.php">📈 Sales Report</a>
      <a href="transaction-history.php">🛍️ Transaction History</a>
      <a href="settings.php">⚙️ Settings</a>
      <a href="signout.php">🚪 Sign Out</a>
    </nav>
  </aside>

  <main class="main">
    <div class="topbar">
      <h2>Pending Items for Approval</h2>
      <input type="text" placeholder="Search orders..." />
    </div>

      <?php if ($result->num_rows > 0): ?>
  <table class="order-table">
    <thead>
      <tr>
        <th>Item ID</th>
        <th>Title</th>
        <th>Price</th>
        <th>Condition</th>
        <th>Location</th>
        <th>Image</th>
        <th>Created At</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($item = $result->fetch_assoc()): ?>
        <tr>
          <td><?= $item['item_id'] ?></td>
          <td><?= htmlspecialchars($item['title']) ?></td>
          <td>₱<?= number_format($item['price'], 2) ?></td>
          <td><?= htmlspecialchars($item['condition']) ?></td>
          <td><?= htmlspecialchars($item['location']) ?></td>
          <td>
            <?php if (!empty($item['image_url'])): ?>
              <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="Item Image" style="max-width: 80px; height: auto;">
            <?php else: ?>
              No Image
            <?php endif; ?>
          </td>
          <td><?= $item['created_at'] ?></td>
          <td>
            <form method="post" style="display:inline-block;">
              <input type="hidden" name="item_id" value="<?= $item['item_id'] ?>">
              <button type="submit" name="action" value="approve">✅ Approve</button>
            </form>
            <form method="post" style="display:inline-block;">
              <input type="hidden" name="item_id" value="<?= $item['item_id'] ?>">
              <button type="submit" name="action" value="reject">❌ Reject</button>
            </form>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
<?php else: ?>
  <p>No pending items found.</p>
<?php endif; ?>

    <?php $conn->close(); ?>
</body>


 </main>