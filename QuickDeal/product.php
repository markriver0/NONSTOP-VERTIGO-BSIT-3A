<?php

include './auth/auth.php';
include 'db/db_conn.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ./signin/signin.php");
    exit;
}

$loggedInUserId = $_SESSION['user_id'] ?? null;
$loggedInUserName = $_SESSION['fullname'] ?? "Guest";

// Validate product ID
if (!isset($_GET['id']) || !ctype_digit($_GET['id'])) {
    die("Invalid or missing product ID.");
}
$itemId = (int) $_GET['id'];

// Fetch product
$sql = "SELECT items.*, users.user_id AS seller_id, users.fullname AS seller_name, users.user_img AS seller_img, categories.name AS category_name, subcategory.name AS subcategory_name, items.image_url 
        FROM items 
        JOIN users ON items.user_id = users.user_id 
        JOIN categories ON items.category_id = categories.category_id 
        JOIN subcategory ON items.subcategory_id = subcategory.subcategory_id 
        WHERE items.item_id = $itemId AND items.status = 'Available'";
$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) === 0) {
    die("Product not found or unavailable.");
}

$product = mysqli_fetch_assoc($result);
$sellerId = $product['seller_id'];
$canMessage = $loggedInUserId !== $sellerId;
$messageSent = false;

// Handle message submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message_content']) && $canMessage) {
    $messageContent = trim($_POST['message_content']);
    if (!empty($messageContent)) {
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, item_id, content, sent_at, is_read) VALUES (?, ?, ?, ?, NOW(), 0)");
        $stmt->bind_param("iiis", $loggedInUserId, $sellerId, $itemId, $messageContent);
        $stmt->execute();
        $stmt->close();
        $messageSent = true;
    }
}

// Fetch conversation
$messages = [];
if ($canMessage) {
    $msgQuery = $conn->prepare("SELECT * FROM messages WHERE 
    ((sender_id = ? AND receiver_id = ?) OR 
     (sender_id = ? AND receiver_id = ?)) AND 
     item_id = ?
    ORDER BY sent_at ASC");
  $msgQuery->bind_param("iiiii", $loggedInUserId, $sellerId, $sellerId, $loggedInUserId, $itemId);
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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= htmlspecialchars($product['title']) ?></title>
  <link rel="stylesheet" href="product.css">
</head>
<body>

<?php include 'navbar.php'; ?>

<button id="dark-mode-toggle" class="dark-mode-toggle"><span>🌙</span></button>

<main>
  <p style="color: black;">Welcome, <?php echo htmlspecialchars($fullname); ?>!</p>
<section class="product-detail">
  <div class="container">
    <div class="product-main">
      <div class="gallery">
        <img class="main-image" src="<?= htmlspecialchars($product['image_url']) ?>" alt="Product">
        <!-- <div class="thumbnails">
          <img src="uploads/<?= $product['item_id'] ?>.jpg" alt="">
        </div> -->
      </div>

      <div class="details">
        <div class="product-header">
          <div class="details-box">
            <h1 class="title"><?= htmlspecialchars($product['title']) ?></h1>
            <p class="price">₱<?= number_format($product['price']) ?></p>
            <p class="category">Category: <?= htmlspecialchars($product['category_name']) ?></p>
            <p class="subcategory">Subcategory: <?= htmlspecialchars($product['subcategory_name']) ?></p>
            <p class="condition">Condition: <?= htmlspecialchars($product['condition']) ?></p>
            <p>Listed on <?= date("F j, Y", strtotime($product['created_at'])) ?></p>
            <?php if ($canMessage) { ?>
              <div class="message-button">
                <button class="message">Message Seller</button>
              </div>
            <?php } ?>
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
            <img 
              src="<?= !empty($product['seller_img']) ? 'uploads/' . htmlspecialchars($product['seller_img']) : 'default-profile.png' ?>" 
              alt="<?= htmlspecialchars($product['seller_name']) ?>" 
              style="width:48px;height:48px;object-fit:cover;border-radius:50%;border:1px solid #eee;"
              onerror="this.src='default-profile.png'">
            <p><?= htmlspecialchars($product['seller_name']) ?></p>
          </div>
          <p>Seller Details</p>
        </div>

        <?php if ($canMessage): ?>
          <div class="chat-box chat-container" id="chatBox">
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
              <img src="https://cdn-icons-png.flaticon.com/128/2190/2190552.png" alt="Messenger" class="msg-icon">
              <span>Send seller a message</span>
            </div>
            <?php if ($messageSent): ?>
              <p style="color: green; font-weight: bold;">✅ Message sent successfully!</p>
            <?php endif; ?>
            <textarea name="message_content" placeholder="Hi, is this still available?" required></textarea>
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

 
  const chatBox = document.getElementById('chatBox');
  if (chatBox) {
    chatBox.scrollTop = chatBox.scrollHeight;
  }
</script>

<footer><?php include 'footer.php'; ?></footer>
<script src="bubble.js"></script>
<script src="dark-mode.js"></script>

</body>
</html>
