<?php
include '../db/db_conn.php';

$query = "
SELECT 
  t.transaction_id,
  t.transaction_date,
  u.fullname AS customer,
  t.amount,
  t.type,
  CASE 
    WHEN t.type = 'subscription' OR t.type = 'advertisement' THEN 'Completed'
    ELSE 'Pending'
  END AS status,
  CASE 
    WHEN t.type = 'subscription' THEN 'Credit Card'
    WHEN t.type = 'advertisement' THEN 'PayPal'
    ELSE 'GCash'
  END AS method
FROM transactions t
JOIN users u ON t.user_id = u.user_id
ORDER BY t.transaction_date DESC
";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Transaction History - QuickDeal Dashboard</title>
  <link rel="stylesheet" href="transaction.css" />
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
      <a href="sales.php">📈 Sales Report</a>
      <a href="transaction-history.php" class="active">🛍️ Transaction History</a>
      <a href="settings.php">⚙️ Settings</a>
      <a href="signout.php">🚪 Sign Out</a>
    </nav>
  </aside>

  <main class="main">
    <div class="topbar">
      <h2>Transaction History</h2>
      <input type="text" placeholder="Search transactions..." style="padding: 8px 12px; border-radius: 8px; border: 1px solid #ddd; width: 200px;">
    </div>

    <section class="dashboard">
      <div class="card">
        <h3>Filter Transactions</h3>
        <div class="filter-bar">
          <label>
            Date Range:
            <select>
              <option>All Time</option>
              <option>Last 7 Days</option>
              <option>Last 30 Days</option>
              <option>Custom Range</option>
            </select>
          </label>
          <label>
            Status:
            <select>
              <option>All</option>
              <option>Completed</option>
              <option>Pending</option>
              <option>Failed</option>
            </select>
          </label>
          <label>
            Payment Method:
            <select>
              <option>All</option>
              <option>Credit Card</option>
              <option>PayPal</option>
              <option>GCash</option>
              <option>COD</option>
            </select>
          </label>
          <input type="text" placeholder="Search by customer..." />
          <button class="btn-filter">Apply Filters</button>
        </div>
      </div>

      <div class="card grid-2">
        <h3>Recent Transactions</h3>
        <table class="transaction-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Date</th>
              <th>Customer</th>
              <th>Amount</th>
              <th>Status</th>
              <th>Method</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
              <td><?php echo str_pad($row['transaction_id'], 3, '0', STR_PAD_LEFT); ?></td>
              <td><?php echo date('Y-m-d', strtotime($row['transaction_date'])); ?></td>
              <td><?php echo htmlspecialchars($row['customer']); ?></td>
              <td>$<?php echo number_format($row['amount'], 2); ?></td>
              <td><span class="status <?php echo strtolower($row['status']); ?>"><?php echo $row['status']; ?></span></td>
              <td><?php echo $row['method']; ?></td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
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
