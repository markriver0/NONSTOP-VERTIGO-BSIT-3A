<?php
include '../db/db_conn.php';

// Get total premium and free users
$subscriptionQuery = "SELECT plan, COUNT(*) AS count FROM subscriptions WHERE is_active = 1 GROUP BY plan";
$subscriptionResult = mysqli_query($conn, $subscriptionQuery);
$plans = ['free' => 0, 'premium' => 0];
while ($row = mysqli_fetch_assoc($subscriptionResult)) {
  $plans[strtolower($row['plan'])] = $row['count'];
}

// Monthly sales summary (based on transactions)
$salesQuery = "
  SELECT DATE_FORMAT(transaction_date, '%M %Y') AS month,
         COUNT(*) AS orders,
         SUM(amount) AS revenue
  FROM transactions
  WHERE type != 'refund'
  GROUP BY MONTH(transaction_date), YEAR(transaction_date)
  ORDER BY transaction_date DESC
  LIMIT 3
";
$salesResult = mysqli_query($conn, $salesQuery);
$monthlySales = [];
while ($row = mysqli_fetch_assoc($salesResult)) {
  $monthlySales[] = [
    'month' => $row['month'],
    'orders' => $row['orders'],
    'revenue' => $row['revenue'],
    'returns' => 0, // Placeholder, unless we track returns separately
    'net_sales' => $row['revenue'] // Same as revenue for now
  ];
}

// Top-selling categories (based on sum of sold item prices)
$categoryQuery = "
  SELECT c.name AS category, SUM(i.price) AS total
  FROM items i
  JOIN categories c ON i.category_id = c.category_id
  WHERE i.status = 'Sold'
  GROUP BY c.category_id
  ORDER BY total DESC
  LIMIT 5
";
$categoryResult = mysqli_query($conn, $categoryQuery);
$topCategories = [];
while ($row = mysqli_fetch_assoc($categoryResult)) {
  $topCategories[] = $row;
}
?>

<!-- HTML part -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Sales Report - QuickDeal Dashboard</title>
  <link rel="stylesheet" href="sales.css" />
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
      <a href="items.php">📦 Items</a>
      <a href="sales.php" class="active">📈 Sales Report</a>
      <a href="transaction-history.php">🛍️ Transaction History</a>
      <a href="settings.php">⚙️ Settings</a>
      <a href="signout.php">🚪 Sign Out</a>
    </nav>
  </aside>

  <main class="main">
    <div class="topbar">
      <h2>Sales Report</h2>
      <input type="text" placeholder="Search report..." style="padding: 8px 12px; border-radius: 8px; border: 1px solid #ddd; width: 200px;" />
    </div>

    <section class="dashboard">
      <div class="card" style="background-color:#e0f2fe">
        <h3>Total Premium Plan Users</h3>
        <p><?php echo $plans['premium']; ?></p>
      </div>
      <div class="card" style="background-color:#f0fdf4">
        <h3>Total Free Plan Users</h3>
        <p><?php echo $plans['free']; ?></p>
      </div>

      <div class="card grid-2">
        <h3>Monthly Sales Summary</h3>
        <div class="table-container">
          <table class="order-table">
            <thead>
              <tr>
                <th>Month</th>
                <th>Orders</th>
                <th>Revenue</th>
                <th>Returns</th>
                <th>Net Sales</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($monthlySales as $month): ?>
              <tr>
                <td><?php echo $month['month']; ?></td>
                <td><?php echo $month['orders']; ?></td>
                <td>$<?php echo number_format($month['revenue'], 2); ?></td>
                <td>$<?php echo number_format($month['returns'], 2); ?></td>
                <td>$<?php echo number_format($month['net_sales'], 2); ?></td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="card">
        <h3>Top Selling Categories</h3>
        <ul>
          <?php foreach ($topCategories as $cat): ?>
            <li><?php echo $cat['category']; ?> – $<?php echo number_format($cat['total'], 2); ?></li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div class="card">
        <h3>Performance Insights</h3>
        <p>Revenue is tracking well. Premium adoption is strong. Optimize top categories for greater conversion.</p>
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
