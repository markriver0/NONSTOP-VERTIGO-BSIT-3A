<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Settings - QuickDeal Dashboard</title>
  <link rel="stylesheet" href="settings.css" />
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
      <a href="sales.php">📈 Sales Report</a>
      <a href="transaction-history.php">🛍️ Transaction History</a>
      <a href="settings.php">⚙️ Settings</a>
      <a href="signout.php">🚪 Sign Out</a>
    </nav>
  </aside>

  <main class="main">
    <div class="topbar">
      <h2>Settings</h2>
      <input type="text" placeholder="Search settings..." />
    </div>

    <section class="dashboard">

      <div class="card grid-2">
        <h3>Account Information</h3>
        <form class="settings-form">
          <label>
            Full Name:
            <input type="text" placeholder="John Admin" />
          </label>
          <label>
            Email:
            <input type="email" placeholder="admin@quickdeal.com" />
          </label>
          <label>
            Phone:
            <input type="tel" placeholder="+1234567890" />
          </label>
          <button type="submit">Update Info</button>
        </form>
      </div>

      <div class="card grid-2">
        <h3>Change Password</h3>
        <form class="settings-form">
          <label>
            Current Password:
            <input type="password" />
          </label>
          <label>
            New Password:
            <input type="password" />
          </label>
          <label>
            Confirm Password:
            <input type="password" />
          </label>
          <button type="submit">Update Password</button>
        </form>
      </div>

      <div class="card grid-2">
        <h3>Notifications</h3>
        <form class="settings-form">
          <label>
            <input type="checkbox" checked /> Email Notifications
          </label>
          <label>
            <input type="checkbox" /> SMS Alerts
          </label>
          <label>
            <input type="checkbox" checked /> Weekly Summary
          </label>
          <button type="submit">Save Preferences</button>
        </form>
      </div>

      <div class="card grid-2">
        <h3>Marketplace Controls</h3>
        <form class="settings-form">
          <label>
            <input type="checkbox" checked />
            Allow new product listings
          </label>
          <label>
            Max items per seller:
            <input type="number" value="50" min="1" style="width: 80px; margin-left: 10px;" />
          </label>
          <label>
            <input type="checkbox" />
            Require admin approval for each listing
          </label>
          <button type="submit">Save Marketplace Settings</button>
        </form>
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
