<?php
include './auth/auth.php';
include './db/db_conn.php';

$loggedInUserId = $_SESSION['user_id'] ?? null;
$fullname = $_SESSION['fullname'] ?? 'Guest';

$listings = [];
if ($loggedInUserId) {
    $stmt = $conn->prepare("SELECT * FROM items WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $loggedInUserId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $listings[] = $row;
    }
    $stmt->close();

    // Redirect to sell.php if no listings
    if (count($listings) === 0) {
        header("Location: sell.php");
        exit;
    }
}

$totalSales = 0;
$totalIncome = 0.0;

if ($loggedInUserId) {
    $salesQuery = $conn->prepare("SELECT COUNT(*) as total_sales, COALESCE(SUM(price), 0) as total_income FROM items WHERE user_id = ? AND status = 'sold'");
    $salesQuery->bind_param("i", $loggedInUserId);
    $salesQuery->execute();
    $salesResult = $salesQuery->get_result();
    if ($salesRow = $salesResult->fetch_assoc()) {
        $totalSales = $salesRow['total_sales'];
        $totalIncome = $salesRow['total_income'];
    }
    $salesQuery->close();
}

$salesByMonth = [];
if ($loggedInUserId) {
    $stmt = $conn->prepare("
        SELECT DATE_FORMAT(updated_at, '%Y-%m') AS month, SUM(price) AS total_sales
        FROM items
        WHERE user_id = ? AND status = 'sold'
        GROUP BY month
        ORDER BY month ASC
    ");
    $stmt->bind_param("i", $loggedInUserId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $salesByMonth[$row['month']] = $row['total_sales'];
    }
    $stmt->close();
}

// Convert PHP array to JavaScript arrays
$labels = json_encode(array_keys($salesByMonth));
$data = json_encode(array_values($salesByMonth));


$stmt = $conn->prepare("SELECT plan FROM subscriptions WHERE user_id = ? AND is_active = 1 ORDER BY start_date DESC LIMIT 1");
$stmt->bind_param("i", $loggedInUserId);
$stmt->execute();
$result = $stmt->get_result();
$subscription = $result->fetch_assoc();
$plan = $subscription['plan'] ?? 'free';


$currentMonth = date('Y-m');
$stmt = $conn->prepare("
    SELECT COUNT(*) as listing_count 
    FROM items 
    WHERE user_id = ? 
      AND DATE_FORMAT(created_at, '%Y-%m') = ?
");
$stmt->bind_param("is", $loggedInUserId, $currentMonth);
$stmt->execute();
$countResult = $stmt->get_result();
$row = $countResult->fetch_assoc();
$currentCount = $row['listing_count'];


if ($plan === 'free' && $currentCount >= 10) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            const createListingButton = document.querySelector('.create-listing');
            if (createListingButton) {
                createListingButton.disabled = true;
                createListingButton.style.cursor = 'not-allowed';
                createListingButton.title = 'You have reached the 10 listing limit for this month. Upgrade to Premium to create more listings.';
            }
        });
    </script>";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="listing.css">
      <link rel="icon" type="image/png" href="./assets/logo (1).png">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


</head>
<body>
<?php include 'navbar.php'; ?>

<button id="dark-mode-toggle" class="dark-mode-toggle">
    <span>🌙</span> 
</button>


  <main>
    <p style="color: black;">Welcome, <?php echo htmlspecialchars($fullname); ?>!</p>

    <section class="user-profile">
      <img src="" alt="profile">
      <h1>Name</h1>
</section>

<div class="sales-wrapper">
<section class="sales-summary">
  <h2 style="margin: 0 0 10px;">Sales Summary</h2>
  <p><strong>Total Sales:</strong> <?php echo $totalSales; ?></p>
  <p><strong>Total Income:</strong> ₱<?php echo number_format($totalIncome, 2); ?></p>
</section>

<section class="sales-chart">
  <h2>Sales Graph</h2>
  <canvas id="salesChart" width="400" height="200"></canvas>
</section>
</div>

  <section class="listings-section">

  <div class="header">
  <h1>Your Listings</h1>
  
  <div class="search-bar">
    <input type="text" placeholder="Search your listings..." />
    <button class="search-btn">
      <i class="fas fa-search"></i>
    </button>
  </div>
</div>

<div class="actions-bar">
  <button class="btn create-listing" onclick="window.location.href='add_listing.php'">
    <i class="fas fa-plus"></i> Create New Listing
  </button>

    <button class="btn create-listing" onclick="window.location.href='add_advertisement.php'">
    <i class="fas fa-plus"></i> Add Advertisement
  </button>
</div>



  <div class="listing-wrapper">
<?php if (count($listings) > 0): ?>
  <?php foreach ($listings as $item): ?>
    <div class="listing-box">
      <img src="<?php echo htmlspecialchars($item['image_url'] ?: 'default.jpg'); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" />
      <div class="listing-info">
        <h1><?php echo htmlspecialchars($item['title']); ?></h1>
        <p>₱<?php echo htmlspecialchars($item['price']); ?> · <span class="status"><?php echo htmlspecialchars($item['status']); ?></span></p>

        <?php if (strtolower($item['status']) === 'pending'): ?>
          <p style="color: red; font-weight: bold;">This product is awaiting admin approval.</p>
          <div class="listing-actions">
            <button class="btn" disabled>Mark as Sold</button>
            <button class="btn" disabled>Edit</button>
            <form action="delete_listing.php" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this listing?');">
              <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
              <button type="submit" class="btn" style="background-color: red; color: white;">
                <i class="fas fa-trash"></i> Delete
              </button>
            </form>
          </div>
        <?php elseif (strtolower($item['status']) === 'rejected'): ?>
          <p style="color: red; font-weight: bold;">This product was rejected by admin.</p>
          <div class="listing-actions">
            <button class="btn" disabled>Mark as Sold</button>
            <button class="btn" disabled>Edit</button>
            <form action="delete_listing.php" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this listing?');">
              <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
              <button type="submit" class="btn" style="background-color: red; color: white;">
                <i class="fas fa-trash"></i> Delete
              </button>
            </form>
            <?php if (!$item['is_featured']): ?>
              <button class="btn" disabled style="background-color: gold; color: black;">
                <i class="fas fa-star"></i> Mark as Featured
              </button>
            <?php else: ?>
              <button class="btn" disabled style="background-color: gold; color: black;">
                <i class="fas fa-star"></i> Featured
              </button>
            <?php endif; ?>
          </div>
        <?php else: ?>
          <div class="listing-actions">
            <?php if (strtolower($item['status']) !== 'sold'): ?>
              <form action="mark_as_sold.php" method="POST" style="display:inline;">
                <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
                <button type="submit" class="btn primary" onclick="return confirm('Mark this item as sold?');">
                  Mark as Sold
                </button>
              </form>
            <?php else: ?>
              <button class="btn" disabled>Sold</button>
            <?php endif; ?>

            <button class="btn" onclick="window.location.href='edit_listing.php?id=<?php echo $item['item_id']; ?>'">Edit</button>
            <form action="delete_listing.php" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this listing?');">
              <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
              <button type="submit" class="btn" style="background-color: red; color: white;">
                <i class="fas fa-trash"></i> Delete
              </button>
            </form>

            <?php if (!$item['is_featured']): ?>
              <form action="mark_featured.php" method="POST" style="display:inline;">
                <input type="hidden" name="item_id" value="<?php echo $item['item_id']; ?>">
                <button type="submit" class="btn" style="background-color: gold; color: black;">
                  <i class="fas fa-star"></i>Mark as Featured
                </button>
              </form>
            <?php else: ?>
              <button class="btn" disabled style="background-color: gold; color: black;">
                <i class="fas fa-star"></i> Featured
              </button>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  <?php endforeach; ?>
<?php else: ?>
  <p>You have no listings yet. Click "Create New Listing" to get started.</p>
<?php endif; ?>
    </div>

<?php
include './db/db_conn.php';

$loggedInUserId = $_SESSION['user_id'] ?? null;
$plan = 'free';

if ($loggedInUserId) {
    $stmt = $conn->prepare("SELECT plan FROM subscriptions WHERE user_id = ? AND is_active = 1 AND end_date >= CURDATE() ORDER BY start_date DESC LIMIT 1");
    $stmt->bind_param("i", $loggedInUserId);
    $stmt->execute();
    $result = $stmt->get_result();
    $subscription = $result->fetch_assoc();
    $plan = $subscription['plan'] ?? 'free';
    $stmt->close();
}
?>

<section class="subscription-section plan">
  <h2>Paid Seller Subscriptions</h2>
  <p class="subtitle">Choose a plan that fits your selling needs</p>

  <div class="subscription-grid">
    <!-- Free Plan Card -->
    <div class="subscription-card">
      <h3>Free Plan</h3>
      <p class="price">₱0 / month</p>
      <ul>
        <li>✔️ Up to 10 listings/month</li>
        <li>❌ No featured slots</li>
        <li>❌ Pang Bakla</li>
      </ul>
    </div>

    <!-- Premium Plan Card -->
    <div class="subscription-card premium plan">
      <h3>Premium Plan</h3>
      <p class="price">₱999 / month</p>
      <ul>
        <li>✔️ Unlimited listings</li>
        <li>✔️ Featured product slots</li>
        <li>✔️ Pang Macho & Pogi</li>
      </ul>

      <?php if ($plan === 'premium'): ?>
        <button class="select-plan" disabled>You are already a Premium Seller</button>
        <div class="active-badge">Your Current Plan</div>
      <?php else: ?>
        <button class="select-plan" onclick="window.location.href='upgrade.php';">Avail Premium</button>
      <?php endif; ?>
    </div>
  </div>
</section>

  </main>

  <footer><?php include 'footer.php'; ?>

  <script>
  const ctx = document.getElementById('salesChart').getContext('2d');

  const salesChart = new Chart(ctx, {
    type: 'bar', // or 'line'
    data: {
      labels: <?php echo $labels; ?>,
      datasets: [{
        label: 'Total Sales (₱)',
        data: <?php echo $data; ?>,
        backgroundColor: 'rgba(235, 172, 54, 0.5)',
        borderColor: 'rgb(235, 193, 54)',
        borderWidth: 1,
        borderRadius: 5
      }]
    },
    options: {
      scales: {
        y: {
          beginAtZero: true,
          ticks: {
            callback: function(value) {
              return '₱' + value;
            }
          }
        }
      },
      plugins: {
        legend: {
          display: true,
          position: 'top'
        },
        tooltip: {
          callbacks: {
            label: function(context) {
              return '₱' + context.parsed.y;
            }
          }
        }
      }
    }
  });
</script>


  <script src="dark-mode.js"></script>
</body>
</html>