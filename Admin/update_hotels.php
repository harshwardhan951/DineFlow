<?php
session_start();
include '../db.php';

if (!isset($_POST['id'])) {
    header("Location: view_hotels.php");
    exit;
}

$id         = intval($_POST['id']);
$hotel_name = mysqli_real_escape_string($conn, $_POST['hotel_name']);
$location   = mysqli_real_escape_string($conn, $_POST['location']);
$status     = mysqli_real_escape_string($conn, $_POST['status']);

// IMAGE HANDLING
$image_sql = "";
if (!empty($_FILES['image']['name'])) {
    $image_name = time() . "_" . $_FILES['image']['name'];
    $tmp_name   = $_FILES['image']['tmp_name'];
    $folder     = "../uploads/";

    // delete old image
    $oldImg = mysqli_query($conn, "SELECT image FROM hotels WHERE id=$id");
    $oldRow = mysqli_fetch_assoc($oldImg);
    if ($oldRow && !empty($oldRow['image']) && file_exists($folder . $oldRow['image'])) {
        unlink($folder . $oldRow['image']);
    }

    move_uploaded_file($tmp_name, $folder . $image_name);
    $image_sql = ", image='$image_name'";
}

// UPDATE HOTEL ONLY
$hotel_query = "UPDATE hotels 
                SET hotel_name='$hotel_name', location='$location', status='$status' $image_sql 
                WHERE id=$id";

if(mysqli_query($conn, $hotel_query)){
    header("Location: view_hotels.php?success=updated");
    exit;
} else {
    die("Error updating hotel: " . mysqli_error($conn));
}
?>
