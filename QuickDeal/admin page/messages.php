<?php
  include '../db/db_conn.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Messages - QuickDeal Dashboard</title>
  <link rel="stylesheet" href="messages.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

  <script>
    if (performance.navigation.type === 1) {
      window.location.href = 'admin.html';
    }
  </script>
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
      <a href="items.php">📦 Items</a>
      <a href="orders.php">🛒 Order</a>
      <a href="sales.php">📈 Sales Report</a>
      <a href="messages.php" class="active">✉️ Messages</a>
      <a href="transaction-history.php">🛍️ Transaction History</a>
      <a href="settings.php">⚙️ Settings</a>
      <a href="signout.php">🚪 Sign Out</a>
    </nav>
  </aside>

  <main class="main">
    <div class="topbar">
      <h2>Messages</h2>
      <input type="text" placeholder="Search messages..." />
    </div>

    <section class="dashboard messages">
      <div class="card chat-list">
        <h3>Conversations</h3>
        <ul>
          <li class="active">🧑‍💼 Anthony Edwards</li>
          <li>👩‍💼 Jayson Tatum</li>
          <li>🧑‍💼 Tyrese Haliburton</li>
          <li>👩‍💼 Syndey Sweeney</li>
          <li>👩‍💼 Madelyn Cline</li>
          <li>🧑‍💼 LeBron James</li>
          <li>👩‍💼 Ana De Armas</li>
          <li>100🧑‍💼 VS Gorilla🦍 </li>
        </ul>
      </div>

      <div class="card chat-box">
        <h3>Chat with John Buyer</h3>
        <div class="messages-window">
          <div class="message from-user">Hi, is the iPhone still available?</div>
          <div class="message from-admin">Yes, it's available. Nature Lover ka ba?</div>
          <div class="message from-user">Oo naman! Gus2 mu mag six tau sa nityur.</div>
        </div>
        <div class="chat-input">
          <input type="text" placeholder="Type your message..." />
          <button>Send</button>
        </div>
      </div>
    </section>
  </main>

  <script>
    const current = window.location.pathname.split('/').pop();
    const links = document.querySelectorAll('.sidebar nav a');
    links.forEach(link => {
      if (link.getAttribute('href') === current) {
        link.classList.add('active');
      } else {
        link.classList.remove('active');
      }
    });
  </script>
</body>
</html>
