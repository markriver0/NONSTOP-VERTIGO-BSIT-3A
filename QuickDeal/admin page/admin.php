<?php
include '../db/db_conn.php';

// Total Income (from sold items)
$totalIncomeQuery = "SELECT SUM(amount) AS total_income FROM transactions";
$totalIncomeResult = mysqli_query($conn, $totalIncomeQuery);
$totalIncome = mysqli_fetch_assoc($totalIncomeResult)['total_income'] ?? 0;

// Total Users
$totalUsersQuery = "SELECT COUNT(*) AS user_count FROM users";
$totalUsersResult = mysqli_query($conn, $totalUsersQuery);
$totalUsers = mysqli_fetch_assoc($totalUsersResult)['user_count'] ?? 0;

// Completed Transactions
$transactionsQuery = "SELECT COUNT(*) AS sold_count FROM items WHERE status = 'Sold'";
$transactionsResult = mysqli_query($conn, $transactionsQuery);
$soldCount = mysqli_fetch_assoc($transactionsResult)['sold_count'] ?? 0;

// Top Categories (by item count)
$topCategoriesQuery = "
SELECT c.name, COUNT(i.item_id) AS item_count
FROM categories c
LEFT JOIN items i ON c.category_id = i.category_id
GROUP BY c.category_id
ORDER BY item_count DESC
LIMIT 5
";
$topCategoriesResult = mysqli_query($conn, $topCategoriesQuery);
$categories = [];
while ($row = mysqli_fetch_assoc($topCategoriesResult)) {
  $categories[] = $row;
}

// Top Users (email list for demo)
$topUsersQuery = "SELECT fullname, email FROM users LIMIT 5";
$topUsersResult = mysqli_query($conn, $topUsersQuery);
$topUsers = [];
while ($row = mysqli_fetch_assoc($topUsersResult)) {
  $topUsers[] = $row;
}

// User Plans (Free vs Premium count)
$plansQuery = "SELECT plan, COUNT(*) AS count FROM subscriptions WHERE is_active = 1 GROUP BY plan";
$plansResult = mysqli_query($conn, $plansQuery);
$plans = ['Free' => 0, 'Premium' => 0];
while ($row = mysqli_fetch_assoc($plansResult)) {
  $key = ucfirst(strtolower($row['plan'])); // ensures 'premium' becomes 'Premium', etc.
  if (array_key_exists($key, $plans)) {
    $plans[$key] = $row['count'];
  }
}

$freePercent = $plans['Free'] / max(array_sum($plans), 1) * 100;
$premiumPercent = $plans['Premium'] / max(array_sum($plans), 1) * 100;


// Customer satisfaction (average feedback rating)
$satisfactionQuery = "SELECT AVG(rating) as avg_rating FROM feedback";
$satisfactionResult = mysqli_query($conn, $satisfactionQuery);
$avgRating = round(mysqli_fetch_assoc($satisfactionResult)['avg_rating'] ?? 0, 2);



?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>QuickDeal Dashboard</title>
  <link rel="stylesheet" href="style.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
  <aside class="sidebar">
    <div class="logo">
      <img src="logo.png" alt="Logo" class="logo-img" />
      <h1>QuickDeal</h1>
    </div>
    <div class="profile">
      <div class="profile-pic">
        <img src="https://m.media-amazon.com/images/M/MV5BNzA1MDBhM2MtMTg0MC00NzUxLWFjZWMtNmE5Y2M3ZDJhYmYyXkEyXkFqcGc@._V1_.jpg" alt="Admin Profile">
      </div>
      <p>Welcome Admin!</p>
    </div>
    <nav>
      <a href="admin.php" class="active">🏠 Dashboard</a>
      <a href="items.php">📦 Items</a>
      <a href="sales.php">📈 Sales Report</a>
      <a href="transaction-history.php">🛍️ Transaction History</a>
      <a href="settings.php">⚙️ Settings</a>
      <a href="../signin/signin.php">🚪 Sign Out</a>
    </nav>
  </aside>

  <main class="main">
    <div class="topbar">
      <h2>Admin Dashboard</h2>
      <input type="text" placeholder="Search here..." style="padding: 8px 12px; border-radius: 8px; border: 1px solid #ddd; width: 200px;">
    </div>
    <section class="dashboard">
      <div class="card" style="background-color:#fee2e2">
        <h3>Total Income</h3>
        <p>$<?php echo number_format($totalIncome, 2); ?></p>
      </div>
      <div class="card" style="background-color:#fef3c7">
        <h3>New Users</h3>
        <p><?php echo $totalUsers; ?></p>
      </div>
      <div class="card" style="background-color:#b5f6ef">
        <h3>Completed Transactions</h3>
        <p><?php echo $soldCount; ?></p>
      </div>
      <div class="card upgrade-card">
        <h3>Invite Users to Premium</h3>
        <p>Get access to exclusive deals, faster support, and advanced analytics with the Premium Plan.</p>
        <button class="upgrade-btn">Invite Them!</button>
      </div>
      <div class="card grid-2">
        <h3>Top Categories</h3>
        <table class="top-categories">
          <tr><th>#</th><th>Name</th><th>Popularity</th><th>Sales</th></tr>
          <?php foreach ($categories as $i => $cat): ?>
          <tr>
            <td><?php echo str_pad($i + 1, 2, '0', STR_PAD_LEFT); ?></td>
            <td><?php echo $cat['name']; ?></td>
            <td><div class="progress-bar"><div class="progress-fill" style="width: <?php echo $cat['item_count'] * 5; ?>%"></div></div></td>
            <td><?php echo $cat['item_count']; ?></td>
          </tr>
          <?php endforeach; ?>
        </table>
      </div>
      <div class="card">
        <h3>Customer Satisfaction</h3>
        <canvas id="satisfactionChart" width="500" height="600"></canvas>
      </div>
      <div class="card">
        <h3>Top Users</h3>
        <div class="top-users">
          <?php foreach ($topUsers as $user): ?>
          <div class="user-tile">
            <h4><?php echo htmlspecialchars($user['fullname']); ?></h4>
            <p><?php echo htmlspecialchars($user['email']); ?></p>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="card">
  <h3>Users Plan</h3>
  <div class="bar-chart">
    <div class="bar-label">
      Free Plan - <?php echo round($freePercent); ?>% (<?php echo $plans['Free']; ?> users)
    </div>
    <div class="bar-container">
      <div class="bar-fill" style="width: <?php echo round($freePercent); ?>%; background-color: #022d5e;"></div>
    </div>

    <div class="bar-label">
      Premium Plan - <?php echo round($premiumPercent); ?>% (<?php echo $plans['Premium']; ?> users)
    </div>
    <div class="bar-container">
      <div class="bar-fill" style="width: <?php echo round($premiumPercent); ?>%; background-color: #fb691b;"></div>
    </div>
  </div>
</div>

    </section>
  </main>
  <script>
  const satisfactionPercent = <?php echo $avgRating * 20; ?>;
  const ctx = document.getElementById('satisfactionChart').getContext('2d');
  const satisfactionChart = new Chart(ctx, {
    type: 'line',
    data: {
      labels: ['Satisfaction'],
      datasets: [{
        label: 'Customer Satisfaction (%)',
        data: [satisfactionPercent],
        fill: false,
        borderColor: '#fb691b',
        tension: 0.3
      }]
    },
    options: {
      scales: {
        y: {
          min: 0,
          max: 100,
          ticks: { stepSize: 10 }
        }
      }
    }
  });
</script>
</body>
</html>
