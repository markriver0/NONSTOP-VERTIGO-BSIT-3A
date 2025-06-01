<?php
include './auth/auth.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
      <link rel="icon" type="image/png" href="./assets/logo (1).png">
  <link rel="stylesheet" href="sell.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<button id="dark-mode-toggle" class="dark-mode-toggle">
    <span>🌙</span> 
</button>

<main>
  <p style="color: black;">Welcome, <?php echo htmlspecialchars($fullname); ?>!</p>

<section class="sell-empty-state">
  <div class="empty-container">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="" class="size-6">
    <path d="M3.478 2.404a.75.75 0 0 0-.926.941l2.432 7.905H13.5a.75.75 0 0 1 0 1.5H4.984l-2.432 7.905a.75.75 0 0 0 .926.94 60.519 60.519 0 0 0 18.445-8.986.75.75 0 0 0 0-1.218A60.517 60.517 0 0 0 3.478 2.404Z" />
    </svg>

    <h2>When you start selling, your listings will appear here.</h2>
    <button class="create-listing-btn" onclick="window.location.href='add_listing.php'">+ Create new listing</button>
  </div>
</section>






</main>

<footer><?php include 'footer.php'; ?>
</footer>  


<script src="dark-mode.js"></script>
</body>
</html>