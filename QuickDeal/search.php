<?php
include 'db/db_conn.php';

$search = isset($_GET['q']) ? trim($_GET['q']) : '';

$items = [];
if ($search !== '') {
    // Search by item title, description, or category name
    $stmt = $conn->prepare(
        "SELECT items.*, categories.name AS category_name
         FROM items
         LEFT JOIN categories ON items.category_id = categories.category_id
         WHERE items.title LIKE CONCAT('%', ?, '%')
            OR items.description LIKE CONCAT('%', ?, '%')
            OR categories.name LIKE CONCAT('%', ?, '%')"
    );
    $stmt->bind_param('sss', $search, $search, $search);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
    $stmt->close();
}

if (isset($_GET['ajax']) && $_GET['ajax'] == '1') {
    // Only return the results HTML for AJAX
    if (empty($items)) {
        echo '<p>No items found.</p>';
    } else {
        echo '<div class="product-wrapper">';
        foreach ($items as $item) {
            echo '<div class="product-box" data-price="' . htmlspecialchars($item['price']) . '">';
            echo '<img src="' . htmlspecialchars($item['image_url']) . '" alt="' . htmlspecialchars($item['title']) . '" class="offer-img" onerror="this.src=\'default.jpg\'">';
            echo '<div class="offer-content">';
            echo '<h1>' . htmlspecialchars($item['title']) . '</h1>';
            echo '<p>' . htmlspecialchars($item['description']) . '</p>';
            echo '<div class="price-box">';
            echo '<span class="new-price">₱' . number_format($item['price']) . '</span>';
            echo '</div>';
            echo '<p class="product-location" style="color:#888; font-size:1.1rem; margin: 0.5rem 0 0 0;">';
            echo '<i class="fa fa-map-marker-alt"></i> ' . htmlspecialchars($item['location']);
            echo '</p>';
            echo '<button class="buy-now-btn" onclick="location.href=\'product.php?id=' . $item['item_id'] . '\'">Buy Now</button>';
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
    }
    exit;
}

if (isset($_GET['ajax']) && $_GET['ajax'] === 'dropdown') {
    if (empty($items)) {
        echo '<div style="padding:12px;color:#888;">No results found.</div>';
    } else {
        foreach ($items as $item) {
            echo '<div style="display:flex;align-items:flex-start; flex-direction: row;gap:12px;padding:12px 18px;cursor:pointer;border-bottom:1px solid #f0f0f0;background:#fff;" onclick="window.location.href=\'product.php?id=' . $item['item_id'] . '\'">';
            echo '<img src="' . htmlspecialchars($item['image_url']) . '" alt="' . htmlspecialchars($item['title']) . '" style="width:44px;height:44px;object-fit:cover;border-radius:7px;flex-shrink:0;margin-right:8px;" onerror="this.src=\'default.jpg\'">';
            echo '<div style="display:flex;flex-direction:column;gap:2px;">';
            echo '<strong style="font-size:1rem;color:#222;">' . htmlspecialchars($item['title']) . '</strong>';
            echo '<span style="font-size:0.95em;color:#888;">₱' . number_format($item['price']) . ' &middot; ' . htmlspecialchars($item['location']) . '</span>';
            echo '</div>';
            echo '</div>';
        }
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Results</title>
    <link rel="stylesheet" href="search.css">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Add your product card CSS here if not already included -->
</head>
<body>
<?php include 'navbar.php'; ?>
<main>

<section class="products-section">
<div class="search-results">
    <h2>Search Results for "<?= htmlspecialchars($search) ?>"</h2>
    <div class="product-wrapper">
    <?php if (empty($items)): ?>
        <p>No items found.</p>
    <?php else: ?>
        <?php foreach ($items as $item): ?>
            <div class="product-box" data-price="<?= htmlspecialchars($item['price']) ?>">
                <img src="<?= htmlspecialchars($item['image_url']) ?>"
                     alt="<?= htmlspecialchars($item['title']) ?>"
                     class="offer-img"
                     onerror="this.src='default.jpg'">
                <div class="offer-content">
                    <h1><?= htmlspecialchars($item['title']) ?></h1>
                    <p><?= htmlspecialchars($item['description']) ?></p>
                    <div class="price-box">
                        <span class="new-price">₱<?= number_format($item['price']) ?></span>
                    </div>
                    <p class="product-location" style="color:#888; font-size:1.1rem; margin: 0.5rem 0 0 0;">
                        <i class="fa fa-map-marker-alt"></i>
                        <?= htmlspecialchars($item['location']) ?>
                    </p>
                    <button class="buy-now-btn" onclick="location.href='product.php?id=<?= $item['item_id'] ?>'">Buy Now</button>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
    </div>
</div>
</section>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('.searchInput');
    const resultsDiv = document.querySelector('.search-results');
    if (!searchInput) return;

    let timeout = null;
    searchInput.addEventListener('input', function() {
        clearTimeout(timeout);
        timeout = setTimeout(function() {
            const query = searchInput.value.trim();
            if (query.length === 0) {
                resultsDiv.innerHTML = '<p>Please enter a search term.</p>';
                return;
            }
            fetch('search.php?q=' + encodeURIComponent(query) + '&ajax=1')
                .then(response => response.text())
                .then(html => {
                    resultsDiv.innerHTML = html;
                });
        }, 400);
    });
});
</script>
</body>
</html>