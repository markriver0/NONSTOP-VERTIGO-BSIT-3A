<?php
include 'db/db_conn.php'; 
include './auth/auth.php';
include 'navbar.php';

// $subcategory = isset($_GET['subcategory']) ? strtolower($_GET['subcategory']) : null;

$subcategory = isset($_GET['subcategory']) ? strtolower(trim($_GET['subcategory'])) : null;

// Base SQL query
$sql = "SELECT items.* 
        FROM items 
        JOIN categories ON items.category_id = categories.category_id 
        LEFT JOIN subcategory ON items.subcategory_id = subcategory.subcategory_id
        WHERE LOWER(categories.name) = 'sports & outdoors' 
          AND items.status = 'Available'";

// If a subcategory filter is applied
if ($subcategory) {
    $subcategory_escaped = mysqli_real_escape_string($conn, $subcategory);
    $sql .= " AND LOWER(subcategory.name) = '$subcategory_escaped'";
}


$result = mysqli_query($conn, $sql);
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Quickie Deal - Sports & Outdoors</title>
  <link rel="stylesheet" href="sports_outdoor.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<button id="dark-mode-toggle" class="dark-mode-toggle">
  <span>🌙</span> 
</button>

<main>
<p style="color: black;">Welcome, <?php echo htmlspecialchars($fullname); ?>!</p>

  <!-- Promo Carousel -->
<section class="promo-carousel-section">
  <div class="promo-carousel-wrapper">
    <div class="promo-carousel">
      <div class="promo-slide">
        <h2>🔥 Avail Premium Seller Subscription!</h2>
        <p>Avail Premium Seller Subscription for P299 only</p>
      </div>
      <div class="promo-slide">
        <h2>🎁 Lets Deal</h2>
        <p>Message the seller about the item you want</p>
      </div>
      <div class="promo-slide">
        <h2>📣 Display Your Advertisement</h2>
        <p>Display Your Ads For Only P100 per Day</p>
      </div>
    </div>
  </div>
</section>


  <!-- Banner Section -->
  <section class="section-1">
    <div class="banner"></div>
  </section>

  <!-- Tagline Section -->
  <section class="tagline-section">
    <p style="font-size: 1.6rem; letter-spacing: 10px; font-weight: 300;">WELCOME TO</p>
    <h1>Quickie Deal <br><span> Sports & Outdoor </span></h1>
    <p>Let's Deal Though! Fast Finds, Real Steals</p>
  </section>

  <!-- Special Offers -->
<?php
$stmt = $conn->prepare("SELECT * FROM sponsored_ads WHERE is_active = 1 AND start_date <= NOW() AND end_date >= NOW() ORDER BY created_at DESC");
$stmt->execute();
$adsResult = $stmt->get_result();
?>
<div class="ad-slideshow">
  <?php while ($ad = $adsResult->fetch_assoc()): ?>
    <div class="ad-slide">
      <a href="<?php echo htmlspecialchars($ad['link_url']); ?>" target="_blank">
        <img src="<?php echo htmlspecialchars($ad['image_url']); ?>" alt="<?php echo htmlspecialchars($ad['business_name']); ?>">
      </a>
      <p><?php echo htmlspecialchars($ad['business_name']); ?></p>
    </div>
  <?php endwhile; ?>
<button class="prev" onclick="changeSlide(-1)">&#10094;</button>
<button class="next" onclick="changeSlide(1)">&#10095;</button>
</div>

  <!-- Product Display -->
  <section class="products-section">
    <div class="flex-spacearound">
      <h1 style="font-size: 4rem;">Sports & Outdoors</h1>

      <div class="filter-bar">
      <form method="get" action="sports_&_outdoors.php" style="display: flex; align-items: center;">
        <?php if ($subcategory): ?>
        <input type="hidden" name="subcategory" value="<?= htmlspecialchars($subcategory) ?>">
        <?php endif; ?>
        <label for="price-filter">Filter by price:</label>
        <select id="price-filter" name="price">
        <option value="all" <?= (!isset($_GET['price']) || $_GET['price'] == 'all') ? 'selected' : '' ?>>All</option>
        <option value="below5000" <?= (isset($_GET['price']) && $_GET['price'] == 'below5000') ? 'selected' : '' ?>>Below ₱5,000</option>
        <option value="5000to10000" <?= (isset($_GET['price']) && $_GET['price'] == '5000to10000') ? 'selected' : '' ?>>₱5,000 – ₱10,000</option>
        <option value="above10000" <?= (isset($_GET['price']) && $_GET['price'] == 'above10000') ? 'selected' : '' ?>>Above ₱10,000</option>
        </select>
        <button type="submit" style="margin-left: 0.5rem; background-color: var(--color--primary--400); border: none; padding: .4em; font-family: var(--ff--primary); color: var(--color--primary--100); border-radius: 8px;">Apply</button>
      </form>
      </div>
    </div>


    <div class="category-section">
  <h2>Browse by Category</h2>
  <div class="category-container">


    <div class="category-card">
    <a href="sports_&_outdoors.php?subcategory=Fitness">
      <img src="https://cdn-icons-png.flaticon.com/128/8881/8881792.png" alt="Fitness">
      <p>Fitness</p></a>
    </div>

    <div class="category-card">
      <a href="sports_&_outdoors.php?subcategory=Sports">
      <img src="https://cdn-icons-png.flaticon.com/128/4645/4645268.png" alt="Sports">
      <p>Sports</p></a>
    </div>

    <div class="category-card">
      <a href="sports_&_outdoors.php?subcategory=Active Wear">
      <img src="https://cdn-icons-png.flaticon.com/128/16815/16815989.png" alt="Active Wear">
      <p>Active Wear</p></a>
    </div>

    <div class="category-card">
      <a href="sports_&_outdoors.php?subcategory=Outdoor">
      <img src="https://cdn-icons-png.flaticon.com/128/366/366922.png" alt="Outdoor">
      <p>Outdoor</p></a>
    </div>

    <div class="category-card">
      <a href="sports_&_outdoors.php?subcategory=Cycling">
      <img src="https://cdn-icons-png.flaticon.com/128/983/983534.png" alt="Cycling">
      <p>Cycling</p></a>
    </div>

    </div>
    </div>


  

    <?php if ($subcategory): ?>
  <h2 style="margin-left: 2rem;">Showing: <?= htmlspecialchars(ucwords($subcategory)) ?></h2>
<?php endif; ?>



    <div class="product-wrapper">
    <?php if (mysqli_num_rows($result) > 0): ?>
      <?php while($row = mysqli_fetch_assoc($result)): ?>
        <div class="product-box" data-price="<?= $row['price'] ?>">
          <img src="<?= htmlspecialchars($row['image_url']) ?>" 
               alt="<?= htmlspecialchars($row['title']) ?>" 
               class="offer-img" 
               onerror="this.src='default.jpg'">
          <div class="offer-content">
            <h1><?= htmlspecialchars($row['title']) ?></h1>
            <p><?= htmlspecialchars($row['description']) ?></p>
            <div class="price-box">
              <span class="new-price">₱<?= number_format($row['price']) ?></span>
            </div>
            <p class="product-location" style="color:#888; font-size:1.1rem; margin: 0.5rem 0 0 0;">
              <i class="fa fa-map-marker-alt"></i>
              <?= htmlspecialchars($row['location']) ?>
            </p>
            <button class="buy-now-btn" onclick="location.href='product.php?id=<?= $row['item_id'] ?>'">Buy Now</button>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p style="color: red; font-size: 1.2rem; margin-top: 2rem;">No products found in the Sports And Outdoors category.</p>
    <?php endif; ?>
    </div>

  </section>

  <h1 style=" font-size: 4rem;  color: var(--color--primary--500);">Customer Feedback</h1>
<section class="section-4">
  <?php
  include './db/db_conn.php';

  // Fetch feedbacks from the database, including user_img
  $stmt = $conn->prepare("
      SELECT f.message, f.rating, f.submitted_at, u.fullname, u.user_img 
      FROM feedback f
      JOIN users u ON f.user_id = u.user_id
      ORDER BY f.submitted_at DESC
      LIMIT 10
  ");
  $stmt->execute();
  $feedbackResult = $stmt->get_result();

  if ($feedbackResult->num_rows > 0):
      while ($feedback = $feedbackResult->fetch_assoc()):
          // Use user_img if available, otherwise fallback to default
          $profileImg = !empty($feedback['user_img']) ? 'uploads/' . htmlspecialchars($feedback['user_img']) : 'svg/default-profile.svg';
  ?>
      <div class="feedback-box">
        <img src="<?php echo $profileImg; ?>" alt="profile" style="width:60px;height:60px;border-radius:50%;">
        <h1><?php echo htmlspecialchars($feedback['fullname']); ?></h1>
        <p><?php echo htmlspecialchars($feedback['message']); ?></p>
        <p>Rating: <?php echo htmlspecialchars($feedback['rating']); ?>/5</p>
        <p><small>Submitted on: <?php echo htmlspecialchars(date('F j, Y', strtotime($feedback['submitted_at']))); ?></small></p>
      </div>
  <?php
      endwhile;
  else:
  ?>
      <p>No feedback available yet. Be the first to leave a review!</p>
  <?php
  endif;

  $stmt->close();
  ?>
</section>

</main>

<footer>
  <?php include 'footer.php'; ?>
</footer>

<script>
let currentSlide = 0;

function showSlides() {
  const slides = document.querySelectorAll('.ad-slide');
  slides.forEach((slide, index) => {
    slide.style.display = index === currentSlide ? 'block' : 'none';
  });
}

function changeSlide(direction) {
  const slides = document.querySelectorAll('.ad-slide');
  currentSlide = (currentSlide + direction + slides.length) % slides.length;
  showSlides();
}

// Initialize the slideshow
showSlides();

// Auto-slide every 5 seconds
setInterval(() => changeSlide(1), 5000);
</script>

<script src="dark-mode.js"></script>
<script>
  const filter = document.getElementById("price-filter");
  const productBoxes = document.querySelectorAll(".product-box");

  filter.addEventListener("change", function () {
    const value = this.value;

    productBoxes.forEach(box => {
      const price = parseInt(box.getAttribute("data-price"));

      if (
        value === "all" ||
        (value === "below5000" && price < 5000) ||
        (value === "5000to10000" && price >= 5000 && price <= 10000) ||
        (value === "above10000" && price > 10000)
      ) {
        box.style.display = "block";
      } else {
        box.style.display = "none";
      }
    });
  });
</script>
</body>
</html>
