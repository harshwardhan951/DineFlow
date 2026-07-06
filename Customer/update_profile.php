<?php
session_start();
include '../db.php';

$user_id = $_SESSION['user_id'];

$username = trim(mysqli_real_escape_string($conn, $_POST['username']));
$email    = trim(mysqli_real_escape_string($conn, $_POST['email']));
$phone    = trim(mysqli_real_escape_string($conn, $_POST['phone']));
$address  = trim(mysqli_real_escape_string($conn, $_POST['address']));

// PHP Validation
if(strlen($username) < 3 || !filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/^[0-9]{10}$/', $phone) || strlen($address) < 5){
    die("❌ Invalid input detected. Please go back and correct the form.");
}

// IMAGE UPLOAD
if(isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0){
    $image_name = time() . "_" . basename($_FILES['profile_image']['name']);
    $target_dir = "../uploads/profile/";
    $target_file = $target_dir . $image_name;

    if(move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)){
        $sql = "UPDATE users SET 
                username='$username', 
                email='$email', 
                phone='$phone', 
                address='$address', 
                profile_image='$image_name' 
                WHERE id=$user_id";
    } else {
        die("❌ Image upload failed. Check folder permissions.");
    }
} else {
    $sql = "UPDATE users SET 
            username='$username', 
            email='$email', 
            phone='$phone', 
            address='$address' 
            WHERE id=$user_id";
}

mysqli_query($conn, $sql);

// Update session
$_SESSION['username'] = $username;

header("Location: profile.php");
exit;
