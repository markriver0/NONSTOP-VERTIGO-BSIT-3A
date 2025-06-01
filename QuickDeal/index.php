<?php
include './auth/auth.php';
include("db/db_conn.php");


// $_SESSION['user_info_id'];
// if(!isset($_SESSION['user_info_id'])){
//   header("Location: ./signin/signin.php");
//   die();
// }

$category_query = "SELECT * FROM categories";
$category_result = mysqli_query($conn, $category_query);

$categories = [];
if ($category_result && mysqli_num_rows($category_result) > 0) {
    while ($row = mysqli_fetch_assoc($category_result)) {
        $categories[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include './db/db_conn.php';

    $userId = $_SESSION['user_id'] ?? null;
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $message = $_POST['message'] ?? '';
    $rating = $_POST['rating'] ?? null;

    if ($userId && $message && $rating) {
        $stmt = $conn->prepare("INSERT INTO feedback (user_id, message, rating, submitted_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("isi", $userId, $message, $rating);

        if ($stmt->execute()) {
            echo "<p>Thank you for your feedback!</p>";
        } else {
            echo "<p>Failed to submit feedback. Please try again later.</p>";
        }

        $stmt->close();
    } else {
        echo "<p>All fields are required.</p>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>QuickDeal</title>
  <link rel="stylesheet" href="style.css">
  <link href="https://fonts.cdnfonts.com/css/futura-std-4" rel="stylesheet">
      <link rel="icon" type="image/png" href="./assets/logo (1).png">
                
</head>
<body>
<?php include 'navbar.php'; ?>

<button id="dark-mode-toggle" class="dark-mode-toggle">
    <span>🌙</span> 
</button>

<main>

<p style="color: black;">Welcome, <?php echo htmlspecialchars($fullname); ?>!</p>

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

<section class="section-1">
<canvas id="bubble-canvas"></canvas>


  <div class="container1">
  <h1>Shop Your Best Needs</h1>
  <p>Discover unbeatable deals and one-of-a-kind treasures from real people just like you. Whether you're hunting for everyday essentials, stylish upgrades, or rare finds, our marketplace connects you directly to trusted sellers in your community. Shop smart, sell easy, and experience a new way to buy and sell with confidence — all in one simple, seamless platform.</p>

  <button>View More</button>
  </div>
  <div class="container2">

  </div>
</section>

<section class="tagline-section">
  <p style="font-size: 1.6rem; letter-spacing: 10px; font-weight: 300;">NAKAKAVERTIGO</p>
  <h1>Quickie Deal</h1>
  <p>Let's Deal Though! Fast Finds, Real Steals</p>
</section>

<section class="section-2">
  <!-- <div class="box1">
    <img src="" alt="profile">
    <div class="group_text"> <h1>Welcome to QuickDeal!</h1>
    <p>Recommendation's for you👍</p></div>
</div>   -->


<?php foreach ($categories as $category): 
$className = 'category-' . strtolower(
  preg_replace('/[^a-z0-9]+/', '-', trim($category['name']))
);
$className = rtrim($className, '-');

?>
  <div class="box <?= $className ?>">
    <div class="category-img"></div>
    <div class="wrap">
      <h1><?= htmlspecialchars($category['name']) ?></h1>
      <p>"Explore great deals in <?= htmlspecialchars($category['name']) ?>."</p>
    </div>
  </div>
  

<?php endforeach; ?>



</section>


<!-- ADS HERE -->
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


<?php
include './db/db_conn.php';

$stmt = $conn->prepare("
    SELECT * FROM items 
    WHERE is_featured = 1 
      AND featured_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
    ORDER BY featured_at DESC
    LIMIT 6
");
$stmt->execute();
$result = $stmt->get_result();
?>

<section class="section-3">
  <h1>Featured Products</h1>
  <div class="container-wrapper">
    <?php while ($row = $result->fetch_assoc()): ?>
      <div class="container" style="background-image: url('<?php echo htmlspecialchars($row['image_url']); ?>'); background-size: cover; background-position: center; filter: brightness(80%); filter:blur()">
        <div class="wrap">
          <p><?php echo htmlspecialchars($row['title']); ?></p>
          <h1>₱<?php echo number_format($row['price'], 2); ?></h1>
        </div>
        <button onclick="window.location.href='product.php?id=<?php echo $row['item_id']; ?>'">View Details</button>
      </div>
    <?php endwhile; ?>
  </div>
</section>


<h1 style="font-size: 4rem; color: var(--color--primary--500);">Customer Feedback</h1>
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

<section class="feedback-section">
  <div class="feedback-container">
    <div class="feedback-info">
      <h1>We'd love your feedback!</h1>
      <p>Help us improve by telling us what you think.</p>
    </div>
    <form class="feedback-form" method="POST" action="">
      <div class="form-grid">
        <!-- <input type="text" name="name" placeholder="Your Name" required />
        <input type="email" name="email" placeholder="Your Email" required /> -->
        <textarea name="message" rows="5" placeholder="Your Feedback..." required></textarea>
        <input type="number" name="rating" placeholder="Rating (1-5)" min="1" max="5" required />
      </div>
      <button type="submit" class="submit-btn">Send Feedback</button>
    </form>
  </div>
</section>
</main>

<footer><?php include 'footer.php'; ?>
</footer>  

<script src="bubble.js"></script>
<script src="dark-mode.js"></script>
</body>
</html>