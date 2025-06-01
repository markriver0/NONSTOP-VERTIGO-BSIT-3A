<?php
include 'db/db_conn.php';

$query = "SELECT * FROM categories";
$result = mysqli_query($conn, $query);

$categories = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $categories[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title></title>
  <link rel="stylesheet" href="navbar.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
  <link href="https://fonts.cdnfonts.com/css/futura-std-4" rel="stylesheet">

  <link rel="stylesheet" href="dropdown.css">
  <link rel="icon" type="image/png" href="./assets/logo (1).png">



</head>
<body>
  <nav>
    <div class="navigationBar">
      <img src="/assets/logo.png" alt="logo" class="logo">

      <div class="navList">
        <ul>
          <li><a href="index.php">Home</a></li>
          <li class="dropdown">
            <a href="#">Categories</a>
            <ul class="dropdown-menu"> 
              <?php foreach ($categories as $category): ?> 
                <li>
                  <a href="<?= strtolower(str_replace(' ', '_', htmlspecialchars($category['name']))) ?>.php">
                    <?= htmlspecialchars($category['name']) ?>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          </li>
          <li><a href="listing.php">Sell</a></li>
          <li><a href="inbox.php">Inbox</a></li>
          <li>
            <a href="logout.php" id="logout-link">Logout</a>
          </li>
          <li><a href="myaccount.php">My Account</a></li>
        
        </ul>
      </div>
      
      <div class="searchBar">
        <form action="search.php" method="get" style="display: flex; position: relative;">
          <input type="text" name="q" placeholder="Search..." class="searchInput" autocomplete="off">
          <button type="submit" class="searchButton">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><g fill="" fill-rule="evenodd"><path d="m12.593 23.258l-.011.002l-.071.035l-.02.004l-.014-.004l-.071-.035q-.016-.005-.024.005l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427q-.004-.016-.017-.018m.265-.113l-.013.002l-.185.093l-.01.01l-.003.011l.018.43l.005.012l.008.007l.201.093q.019.005.029-.008l.004-.014l-.034-.614q-.005-.018-.02-.022m-.715.002a.02.02 0 0 0-.027.006l-.006.014l-.034.614q.001.018.017.024l.015-.002l.201-.093l.01-.008l.004-.011l.017-.43l-.003-.012l-.01-.01z"/><path fill="" d="M10.5 2a8.5 8.5 0 1 0 5.262 15.176l3.652 3.652a1 1 0 0 0 1.414-1.414l-3.652-3.652A8.5 8.5 0 0 0 10.5 2M4 10.5a6.5 6.5 0 1 1 13 0a6.5 6.5 0 0 1-13 0"/></g></svg>
          </button>
          <div class="search-dropdown" style="position:absolute;top:100%;left:0;width:100%;z-index:1000;background:#fff;border:1px solid #ccc;display:none;"></div>
        </form>
      </div>
    </div>
  </nav>
  <script>
document.addEventListener('DOMContentLoaded', function() {
  const searchInput = document.querySelector('.searchInput');
  const dropdown = document.querySelector('.search-dropdown');
  if (!searchInput || !dropdown) return;

  let timeout = null;

  searchInput.addEventListener('input', function() {
    clearTimeout(timeout);
    const query = searchInput.value.trim();
    if (query.length === 0) {
      dropdown.style.display = 'none';
      dropdown.innerHTML = '';
      return;
    }
    timeout = setTimeout(function() {
      fetch('search.php?q=' + encodeURIComponent(query) + '&ajax=dropdown')
        .then(response => response.text())
        .then(html => {
          dropdown.innerHTML = html;
          dropdown.style.display = html.trim() ? 'block' : 'none';
        });
    }, 300);
  });

  // Hide dropdown when clicking outside
  document.addEventListener('click', function(e) {
    if (!dropdown.contains(e.target) && e.target !== searchInput) {
      dropdown.style.display = 'none';
    }
  });

  // Modern logout confirmation modal
  const logoutLink = document.getElementById('logout-link');
  const modalOverlay = document.getElementById('logout-modal-overlay');
  const confirmBtn = document.getElementById('logout-confirm-btn');
  const cancelBtn = document.getElementById('logout-cancel-btn');

  if (logoutLink && modalOverlay && confirmBtn && cancelBtn) {
    logoutLink.addEventListener('click', function(e) {
      e.preventDefault();
      modalOverlay.style.display = 'flex';
    });
    confirmBtn.addEventListener('click', function() {
      window.location.href = logoutLink.href;
    });
    cancelBtn.addEventListener('click', function() {
      modalOverlay.style.display = 'none';
    });
    // Optional: close modal on overlay click
    modalOverlay.addEventListener('click', function(e) {
      if (e.target === modalOverlay) modalOverlay.style.display = 'none';
    });
  }
});
</script>

<div id="logout-modal-overlay">
  <div id="logout-modal">
    <h2>Logout Confirmation</h2>
    <p>Are you sure you want to logout?</p>
    <div class="modal-btns">
      <button class="modal-btn confirm" id="logout-confirm-btn">Logout</button>
      <button class="modal-btn cancel" id="logout-cancel-btn">Cancel</button>
    </div>
  </div>
</div>
</body>
</html>