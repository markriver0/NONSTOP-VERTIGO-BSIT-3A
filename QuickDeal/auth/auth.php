<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ./signin/signin.php");
    exit;
}


$fullname = $_SESSION['fullname'];
$email = $_SESSION['email'];
?>
