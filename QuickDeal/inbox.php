<?php
include './auth/auth.php';
include 'db/db_conn.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ./signin/signin.php");
    exit;
}

$loggedInUserId = $_SESSION['user_id'];
$loggedInUserName = $_SESSION['fullname'] ?? "User";

// Handle reply
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['receiver_id'], $_POST['message_content'])) {
    $receiverId = (int)$_POST['receiver_id'];
    $messageContent = trim($_POST['message_content']);
    $itemId = isset($_POST['item_id']) ? (int)$_POST['item_id'] : null;

    if ($receiverId && $messageContent !== '') {
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, content, sent_at, is_read, item_id) VALUES (?, ?, ?, NOW(), 0, ?)");
        $stmt->bind_param("iisi", $loggedInUserId, $receiverId, $messageContent, $itemId);
        $stmt->execute();
        $stmt->close();
    }
}

// Get all users this user has chatted with
$convoQuery = "
    SELECT u.user_id, u.fullname 
    FROM users u 
    WHERE u.user_id IN (
        SELECT DISTINCT CASE 
            WHEN sender_id = ? THEN receiver_id 
            WHEN receiver_id = ? THEN sender_id 
        END
        FROM messages
        WHERE sender_id = ? OR receiver_id = ?
    ) AND u.user_id != ?
";
$stmt = $conn->prepare($convoQuery);
$stmt->bind_param("iiiii", $loggedInUserId, $loggedInUserId, $loggedInUserId, $loggedInUserId, $loggedInUserId);
$stmt->execute();
$convoResult = $stmt->get_result();
$contacts = $convoResult->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Load conversation with selected user
$activeUserId = isset($_GET['user']) && ctype_digit($_GET['user']) ? (int)$_GET['user'] : null;
$messages = [];
$activeUserName = '';
$activeUserItem = '';

if ($activeUserId) {
    $msgStmt = $conn->prepare("SELECT m.*, i.title 
        FROM messages m 
        LEFT JOIN items i ON m.item_id = i.item_id
        WHERE 
            (m.sender_id = ? AND m.receiver_id = ?) OR 
            (m.sender_id = ? AND m.receiver_id = ?) 
        ORDER BY m.sent_at ASC");
    $msgStmt->bind_param("iiii", $loggedInUserId, $activeUserId, $activeUserId, $loggedInUserId);
    $msgStmt->execute();
    $msgResult = $msgStmt->get_result();
    while ($row = $msgResult->fetch_assoc()) {
        $messages[] = $row;
        if ($row['item_id'] && !$activeUserItem) {  // Set the item title for the first item in the conversation
            $activeUserItem = $row['title'];
        }
    }
    $msgStmt->close();

    // Get name of active user
    $nameQuery = $conn->prepare("SELECT fullname FROM users WHERE user_id = ?");
    $nameQuery->bind_param("i", $activeUserId);
    $nameQuery->execute();
    $nameQuery->bind_result($activeUserName);
    $nameQuery->fetch();
    $nameQuery->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Inbox - QuickDeal</title>
  <link rel="stylesheet" href="inbox.css">
  <style>
      .chat-message {
  margin-bottom: 10px;
  padding: 10px;
  border-radius: 10px;
}

.sent {
  background-color: #daf1da;  /* Light background for sent messages */
  text-align: right;
  color: #333;  /* Darker text color for better contrast */
}

.received {
  background-color: #f0f0f0;  /* Lighter background for received messages */
}

.chat-box {
  max-height: 400px;
  overflow-y: auto;
  padding: 10px;
  border: 1px solid #ccc;
  margin-bottom: 15px;
}

.username-item {
  font-weight: bold;
  color: #333; /* Darker text color for user names */
}

textarea {
  background-color: #f9f9f9;
  color: #333;
}

button.send {
  background-color: #4CAF50; /* Button color */
  color: white;
  border: none;
  padding: 10px;
  border-radius: 5px;
}

button.send:hover {
  background-color: #45a049; /* Hover effect */
}

small {
  display: block;
  color: black;
  font-size: 0.9em;  /* Slightly smaller text */
  margin-top: 5px;  /* Add a little space from the message */
  font-style: italic;  /* Make the date stand out with italics */
}

  </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<button id="dark-mode-toggle" class="dark-mode-toggle">
  <span>🌙</span> 
</button>

<main>
<p style="color: black;">Welcome, <?= htmlspecialchars($loggedInUserName); ?>!</p>

<h1>INBOX</h1>
<section>
<div class="container">
  <div class="sidebar">
    <h3>Conversations</h3>
    <?php foreach ($contacts as $user): ?>
      <a class="user-link" href="inbox.php?user=<?= $user['user_id'] ?>">
        <?= htmlspecialchars($user['fullname']) ?>
      </a>
    <?php endforeach; ?>
  </div>

  <div class="conversation">
    <?php if ($activeUserId): ?>
      <h3>Conversation with <?= htmlspecialchars($activeUserName) ?><?php if ($activeUserItem): ?> (<?= htmlspecialchars($activeUserItem) ?>)<?php endif; ?></h3>
      <div class="chat-box" id="chatBox">
        <?php foreach ($messages as $msg): ?>
          <div class="chat-message <?= $msg['sender_id'] == $loggedInUserId ? 'sent' : 'received' ?>">
            <div class="username-item">
              <strong><?= $msg['sender_id'] == $loggedInUserId ? 'You' : htmlspecialchars($activeUserName) ?>:</strong>
              <?php if (!empty($msg['item_id'])): ?>
              <?php endif; ?>
            </div>
            <div>
              <?= htmlspecialchars($msg['content']) ?>
            </div>
            <small style="color:black"><?= date("M d, H:i", strtotime($msg['sent_at'])) ?></small>
          </div>
        <?php endforeach; ?>
      </div>

      <form class="ind" method="POST">
        <input type="hidden" name="receiver_id" value="<?= $activeUserId ?>">
        <?php if (!empty($messages)) : ?>
          <!-- Use the last message's item_id if replying -->
          <input type="hidden" name="item_id" value="<?= end($messages)['item_id'] ?? '' ?>">
        <?php endif; ?>
        <textarea name="message_content" placeholder="Type your reply..." required></textarea>
        <button class="send" type="submit">Send</button>
      </form>
    <?php else: ?>
      <p>Select a conversation to begin.</p>
    <?php endif; ?>
  </div>
</div>
</section>
</main>

<footer><?php include 'footer.php'; ?></footer>  

<script src="bubble.js"></script>
<script src="dark-mode.js"></script>
<script>
  const chatBox = document.getElementById('chatBox');
  if (chatBox) {
    chatBox.scrollTop = chatBox.scrollHeight;
  }
</script>

</body>
</html>
