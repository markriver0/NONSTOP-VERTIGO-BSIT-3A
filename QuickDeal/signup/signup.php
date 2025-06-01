<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up</title>
  <link rel="stylesheet" href="signup.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
</head>
<body>
<main>
  <section>

  </section>

  <section>
    <h1>Create Account</h1>
   <form action="signup-process.php" method="post" class="signup-form" enctype="multipart/form-data">
        <div class="form-group">
        <label for="student_image" class="custom-file-upload">Input your image here</label>
        <input type="file" id="student_image" name="student_image" accept="image/*" required>
 <br>
        </div>
        <div class="form-group">
            <input type="text" id="name" name="name" placeholder="Full Name" required>
        </div>
        <div class="form-group">
            <input type="email" id="email" name="email" placeholder="Email Address" required>
        </div>
        <div class="form-group">
            <input type="text" id="phone" name="phone" placeholder="Phone Number" required>
        </div>
        <div class="form-group">
            <input type="text" id="location" name="location" placeholder="Location" required>
        </div>
        <div class="form-group">
            <input type="password" id="password" name="password" placeholder="Password" required>
        </div>
        <div class="form-group">
            <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm Password" required>
        </div>
        <div class="form-group checkbox">
            <input type="checkbox" id="terms" name="terms" required>
            <label for="terms">I agree to the <a href=""> terms of service </a> and <a href=""> privacy policy </a></label>
        </div>
        <button type="submit" class="signup-button" name="submit">Sign Up</button>
        <div class="social-signup">
            <p>Or Sign Up With</p>
            <div class="social-buttons">
                <button type="button" class="social-button google">Google</button>
                <button type="button" class="social-button facebook">Facebook</button>
            </div>
        </div>
        <div class="login-link">
            <p>Already have an account? <a href="../signin/signin.php">Sign In</a></p>
        </div>
    </form>
  </section>
  <script src="script.js"></script>
</main>
</body>
</html>