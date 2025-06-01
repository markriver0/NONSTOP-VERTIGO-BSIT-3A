<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sign In</title>
<link rel="stylesheet" href="signin.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>
<body>
<main>
<section>
    <h1>Signin your Account</h1>
    <?php
    if (isset($_SESSION['signin_error'])) {
        echo '<div class="error-message" style="color:red; margin-bottom:10px;">' . $_SESSION['signin_error'] . '</div>';
        unset($_SESSION['signin_error']);
    }
    ?>
    <form action="signin-process.php" method="post" class="signup-form">
        <!-- <div class="form-group">
            <input type="text" id="name" name="name" placeholder="Full Name" required>
        </div> -->
        <div class="form-group">
            <input type="email" id="email" name="email" placeholder="Email Address" required>
        </div>
        <div class="form-group">
            <input type="password" id="password" name="password" placeholder="Password" required>
        </div>
        <button type="submit" class="signup-button" name="submit">Sign In</button>
        <div class="social-signup">
            <p>Or Sign In With</p>
            <div class="social-buttons">
                <button type="button" class="social-button google">Google</button>
                <button type="button" class="social-button facebook">Facebook</button>
            </div>
        </div>
        <div class="login-link">
            <p>Don't have an account? <a href="../signup/signup.php">Sign Up</a></p>
        </div>
    </form>
  </section>

  <section>
    
  </section>
</main>
</body>
</html>