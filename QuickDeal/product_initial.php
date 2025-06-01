<?php
include 'db/db_conn.php';
include './auth/auth.php';

include 'navbar.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p>Invalid product ID.</p>";
    exit;
}

$item_id = intval($_GET['id']);

$sql = "SELECT items.*, categories.name AS category_name
        FROM items 
        JOIN categories ON items.category_id = categories.category_id
        WHERE items.item_id = $item_id";

$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    echo "<p>Product not found.</p>";
    exit;
}

$product = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($product['title']) ?> | Quickie Deal</title>
  <link rel="stylesheet" href="product.css"> <!-- Add your styling here -->
</head>
<body>

<p style="color: black;">Welcome, <?php echo htmlspecialchars($fullname); ?>!</p>
<main class="product-detail">
  <div class="product-image">
    <img src="uploads/<?= htmlspecialchars($product['item_id']) ?>.jpg" 
         alt="<?= htmlspecialchars($product['title']) ?>" 
         onerror="this.src='default.jpg'">
  </div>
  <div class="product-info">
    <h1><?= htmlspecialchars($product['title']) ?></h1>
    <p class="category">Category: <?= htmlspecialchars($product['category_name']) ?></p>
    <p class="description"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
    <p class="price">Price: ₱<?= number_format($product['price'], 2) ?></p>
    <p class="status">Status: <?= htmlspecialchars($product['status']) ?></p>
    <button class="buy-now">Contact Seller</button> <!-- You can later link this to messaging -->
  </div>
</main>

<footer>
  <?php include 'footer.php'; ?>
</footer>
</body>
</html>
