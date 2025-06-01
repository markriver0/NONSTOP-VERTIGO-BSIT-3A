<?php
session_start();
include 'db/db_conn.php'; // Ensure this includes your DB connection

$loggedInUserId = $_SESSION['user_id'] ?? null;
$loggedInUserName = $_SESSION['fullname'] ?? "Guest";

// Validate product ID
if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    die("Invalid or missing product ID.");
}
$itemId = (int) $_GET['id'];

// Fetch product from DB
$sql = "SELECT items.*, users.user_id AS seller_id, users.fullname AS seller_name, categories.name AS category_name 
        FROM items 
        JOIN users ON items.user_id = users.user_id 
        JOIN categories ON items.category_id = categories.category_id 
        WHERE items.item_id = $itemId AND items.status = 'Available'";

$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) === 0) {
    die("Product not found or unavailable.");
}

$product = mysqli_fetch_assoc($result);
$sellerId = $product['seller_id'];

// Handle message send
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message_content']) && $loggedInUserId) {
    $messageContent = trim($_POST['message_content']);
    if (!empty($messageContent)) {
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, content, sent_at, is_read) VALUES (?, ?, ?, NOW(), 0)");
        $stmt->bind_param("iis", $loggedInUserId, $sellerId, $messageContent);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch conversation between buyer and seller
$messages = [];
if ($loggedInUserId) {
    $msgQuery = $conn->prepare("SELECT * FROM messages WHERE 
        (sender_id = ? AND receiver_id = ?) OR 
        (sender_id = ? AND receiver_id = ?)
        ORDER BY sent_at ASC");
    $msgQuery->bind_param("iiii", $loggedInUserId, $sellerId, $sellerId, $loggedInUserId);
    $msgQuery->execute();
    $messagesResult = $msgQuery->get_result();
    while ($row = $messagesResult->fetch_assoc()) {
        $messages[] = $row;
    }
    $msgQuery->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($product['title']) ?></title>
  <link rel="stylesheet" href="product.css">
  <style>
    .chat-box { border: 1px solid #ccc; padding: 10px; margin: 15px 0; max-height: 300px; overflow-y: auto; background: #f9f9f9; }
    .chat-message { margin: 5px 0; padding: 8px; border-radius: 6px; max-width: 70%; }
    .sent { background: #dcf8c6; align-self: flex-end; margin-left: auto; }
    .received { background: #ffffff; align-self: flex-start; margin-right: auto; }
    .chat-container { display: flex; flex-direction: column; }
  </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<button id="dark-mode-toggle" class="dark-mode-toggle"><span>🌙</span></button>

<main>
<section class="product-detail">
  <div class="container">
    <div class="product-main">
      <div class="gallery">
        <img class="main-image" src="uploads/<?= $product['item_id'] ?>.jpg" alt="Product">
        <div class="thumbnails">
          <img src="uploads/<?= $product['item_id'] ?>.jpg" alt="">
        </div>
      </div>

      <div class="details">
        <div class="product-header">
          <div class="details-box">
            <h1 class="title"><?= htmlspecialchars($product['title']) ?></h1>
            <p class="price">₱<?= number_format($product['price']) ?></p>
            <p class="category"><?= htmlspecialchars($product['category_name']) ?></p>
            <p class="condition"><?= htmlspecialchars($product['condition']) ?></p>
            <p>Listed on <?= date("F j, Y", strtotime($product['created_at'])) ?></p>
            <div class="message-button">
              <?php if ($loggedInUserId): ?>
                <button class="message">Message Seller</button>
              <?php else: ?>
                <p><a href="login.php">Log in</a> to message the seller.</p>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <div class="details-box">
          <div class="product-info">
            <h2>More Details</h2>
            <p class="description"><?= htmlspecialchars($product['description']) ?></p>
            <p class="location"><?= htmlspecialchars($product['location']) ?></p>
          </div>
        </div>

        <div class="seller-information">
          <div class="seller-box">
            <img src="" alt="">
            <p><?= htmlspecialchars($product['seller_name']) ?></p>
          </div>
          <p>Seller Details</p>
        </div>

        <?php if ($loggedInUserId): ?>
        <div class="chat-box chat-container">
          <?php foreach ($messages as $msg): ?>
            <div class="chat-message <?= $msg['sender_id'] == $loggedInUserId ? 'sent' : 'received' ?>">
              <strong><?= $msg['sender_id'] == $loggedInUserId ? 'You' : htmlspecialchars($product['seller_name']) ?>:</strong>
              <p><?= htmlspecialchars($msg['content']) ?></p>
              <small><?= date("M d, H:i", strtotime($msg['sent_at'])) ?></small>
            </div>
          <?php endforeach; ?>
        </div>

        <form class="send-seller-message" method="POST">
          <div class="msg-header">
            <img src="https://upload.wikimedia.org/wikipedia/commons/8/83/Facebook_Messenger_4_Logo.svg" alt="Messenger" class="msg-icon">
            <span>Send seller a message</span>
          </div>
          <textarea name="message_content" placeholder="Hi, is this available?"></textarea>
          <button type="submit" class="send">Send</button>
        </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
</main>

<script>
  const messageBtn = document.querySelector('.message-button button');
  const sendForm = document.querySelector('.send-seller-message');
  if (messageBtn && sendForm) {
    messageBtn.addEventListener('click', () => {
      sendForm.scrollIntoView({ behavior: 'smooth' });
    });
  }

  const thumbs = document.querySelectorAll('.thumbnails img');
  const mainImg = document.querySelector('.main-image');
  thumbs.forEach(thumb => {
    thumb.addEventListener('click', () => {
      mainImg.src = thumb.src;
    });
  });
</script>

<footer><?php include 'footer.php'; ?></footer>  
<script src="bubble.js"></script>
<script src="dark-mode.js"></script>

</body>
</html>
