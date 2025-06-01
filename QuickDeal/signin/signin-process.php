<?php
session_start(); // <-- ADD THIS LINE
include("../db/db_conn.php");

if(isset($_POST['submit'])){
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = '$email' AND userpass = '$password'";
    $get_result = mysqli_query($conn, $sql);
    $count_result = mysqli_num_rows($get_result);

    if($count_result > 0){
        $row = mysqli_fetch_assoc($get_result);

        // Create session variables
        $_SESSION['user_id'] = $row['user_id'];            // <- match this with auth.php
        $_SESSION['fullname'] = $row['fullname'];          // <- ensure 'fullname' column exists
        $_SESSION['email'] = $row['email'];
        if($row['role'] == "user"){
            header("Location: ../index.php?message=login successfully");
        }
        else{
            header("Location: ../admin page/admin.php");
        }
        exit();
    } 
    else {
        // Set error message in session and redirect
        $_SESSION['signin_error'] = "Invalid username or password. Please try again.";
        header("Location: ../signin/signin.php");
        exit();
    }

    mysqli_close($conn);
}
?>
