<?php
session_start();
session_unset();  // Unset all session variables
session_destroy(); // Destroy the session

// Redirect to login or home page
header("Location: ./signin/signin.php?message=logged out");
exit();
?>
