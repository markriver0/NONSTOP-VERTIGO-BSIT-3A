<?php
include './auth/auth.php';
include './db/db_conn.php';

$loggedInUserId = $_SESSION['user_id'] ?? null;
$user = [
    'fullname' => '',
    'email' => '',
    'user_img' => '',
    'role' => '',
    'date_added' => '',
    'phone' => '',
    'location' => '',
];

if ($loggedInUserId) {
    $stmt = $conn->prepare("SELECT fullname, email, user_img, role, date_added, phone, location FROM users WHERE user_id = ?");
    $stmt->bind_param("i", $loggedInUserId);
    $stmt->execute();
    $stmt->bind_result($fullname, $email, $user_img, $role, $date_added, $phone, $location);
    if ($stmt->fetch()) {
        $user['fullname'] = $fullname;
        $user['email'] = $email;
        $user['user_img'] = $user_img;
        $user['role'] = $role;
        $user['date_added'] = $date_added;
        $user['phone'] = $phone;
        $user['location'] = $location;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Account</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="myaccount.css">
      <link rel="icon" type="image/png" href="./assets/logo (1).png">
</head>
<body>
<?php include 'navbar.php'; ?>

<button id="dark-mode-toggle" class="dark-mode-toggle">
    <span>🌙</span> 
</button>

<main>
  <div class="edit-profile-card">
    <form id="profileForm" method="POST" enctype="multipart/form-data" action="update_profile.php">
      <div class="profile-photo-section">
        <img src="<?php echo 'uploads/' . htmlspecialchars($user['user_img'] ?: 'default.jpg'); ?>" alt="Profile Photo" id="profileImgPreview">

        <label for="profile_picture">Upload new photo</label>
        <input type="file" id="profile_picture" name="profile_picture" accept="image/*" style="display:none;" onchange="previewProfileImg(event)">
        <small>At least 800×800 px recommended.<br>JPG or PNG is allowed</small>
      </div>
      <div class="personal-info-card">
        <div class="personal-info-header">
          <h3>Personal Info</h3>
          <button type="button" class="edit-btn" onclick="toggleEditInfo()">Edit</button>
        </div>
        <div id="infoDisplay">
          <div class="info-row"><span class="info-label">Full Name</span><span class="info-value"><?php echo htmlspecialchars($user['fullname']); ?></span></div>
          <div class="info-row"><span class="info-label">Email</span><span class="info-value"><?php echo htmlspecialchars($user['email']); ?></span></div>
          <div class="info-row"><span class="info-label">Phone</span><span class="info-value"><?php echo htmlspecialchars($user['phone']); ?></span></div>
          <div class="info-row"><span class="info-label">Location</span><span class="info-value"><?php echo htmlspecialchars($user['location']); ?></span></div>
        </div>
        <div id="infoEdit" style="display:none;">
          <div class="info-row"><span class="info-label">Full Name</span><input type="text" name="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>"></div>
          <div class="info-row"><span class="info-label">Email</span><input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>"></div>
          <div class="info-row"><span class="info-label">Phone</span><input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>"></div>
          <div class="info-row"><span class="info-label">Location</span><input type="text" name="location" value="<?php echo htmlspecialchars($user['location']); ?>"></div>
          <div style="text-align:right;">
            <button type="button" class="cancel-btn" onclick="toggleEditInfo()">Cancel</button>
            <button type="submit" class="save-btn">Save changes</button>
          </div>
        </div>
      </div>
    </form>
  </div>
</main>

<footer><?php include 'footer.php'; ?></footer>  

<script>
function previewProfileImg(event) {
  const reader = new FileReader();
  reader.onload = function(){
    document.getElementById('profileImgPreview').src = reader.result;
  };
  reader.readAsDataURL(event.target.files[0]);
}

function toggleEditInfo() {
  const infoDisplay = document.getElementById('infoDisplay');
  const infoEdit = document.getElementById('infoEdit');
  if (infoDisplay.style.display === 'none') {
    infoDisplay.style.display = '';
    infoEdit.style.display = 'none';
  } else {
    infoDisplay.style.display = 'none';
    infoEdit.style.display = '';
  }
}

function enableLocationEdit() {
  document.getElementById('locationInput').disabled = false;
  document.getElementById('editLocationBtn').style.display = 'none';
  document.getElementById('saveLocationBtn').style.display = '';
  document.getElementById('cancelLocationBtn').style.display = '';
}

function cancelLocationEdit() {
  document.getElementById('locationInput').disabled = true;
  document.getElementById('editLocationBtn').style.display = '';
  document.getElementById('saveLocationBtn').style.display = 'none';
  document.getElementById('cancelLocationBtn').style.display = 'none';
}
</script>
<script src="bubble.js"></script>
<script src="dark-mode.js"></script>
</body>
</html>
