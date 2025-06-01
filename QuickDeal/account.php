<?php
  include("./db/db_conn.php");
  include("./auth/auth.php");
  $user_id = $_SESSION['user_id'];

  $sql = "SELECT * FROM users WHERE user_id = $user_id";
  $result = mysqli_query($conn, $sql);
  $user = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>My Account</title>
</head>
<body>
  <?php include("./navbar.php"); ?>

  <div class="dashboard-container">
    <div class="img-container">
      <img src="./uploads/<?php echo htmlspecialchars($user['user_img']); ?>" alt="User Profile">
    </div>
    <h1><?php echo htmlspecialchars($user['fullname']); ?></h1>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
    <p><strong>Role:</strong> <?php echo htmlspecialchars($user['role']); ?></p>
  </div>
</body>
</html>