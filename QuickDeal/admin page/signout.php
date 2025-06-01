<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Sign Out - QuickDeal</title>
  <link rel="stylesheet" href="style.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
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
      <a href="sales.php">📈 Sales Report</a>
      <a href="transaction-history.php">🛍️ Transaction History</a>
      <a href="settings.php">⚙️ Settings</a>
      <a href="signout.php">🚪 Sign Out</a>
    </nav>
  </aside>

  <main class="main">
    <div class="popup-container">
      <div class="popup-card">
        <h2>Are you sure you want to sign out?</h2>
        <div class="popup-actions">
          <button class="cancel-btn" onclick="window.location.href='admin.html'">Cancel</button>
          <button class="signout-btn" onclick="signOut()">Sign Out</button>
        </div>
      </div>
    </div>
  </main>

  <script>
    function signOut() {
      // Add sign-out logic here (e.g. clear session, redirect)
      alert("You have been signed out.");
      window.location.href = "admin.html"; // adjust as needed
    }
  </script>
</body>
</html>
