<?php
include("../db/db_conn.php");

if (isset($_POST['submit'])) {
    $fullname = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $location = $_POST['location'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm-password'];

    // Check if passwords match
    if ($confirmPassword != $password) {
        header("Location: signup.php?message=password_do_not_match");
        exit();
    }

    // Check if user already exists
    $check_user_query  = "SELECT * FROM users WHERE email = ?";
    $statement = mysqli_prepare($conn, $check_user_query);
    mysqli_stmt_bind_param($statement, "s", $email);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);

    if (mysqli_num_rows($result) > 0) {
        header("Location: signup.php?message=user_already_exist");
        exit();
    }

    // Handle image upload
    $imageName = $_FILES['student_image']['name'];
    $imageTmp = $_FILES['student_image']['tmp_name'];
    $imageFolder = '../uploads/'; // Make sure this folder exists and is writable
    $imagePath = $imageFolder . basename($imageName);

    if (move_uploaded_file($imageTmp, $imagePath)) {
        // Save user data and image path, including phone and location
        $sql = "INSERT INTO users (fullname, email, phone, location, userpass, user_img) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssssss", $fullname, $email, $phone, $location, $password, $imageName);
        mysqli_stmt_execute($stmt);

        header("Location: ../signin/signin.php?user_created_successfully");
        exit();
    } else {
        header("Location: signup.php?message=image_upload_failed");
        exit();
    }
}

mysqli_close($conn);
?>
