<?php
session_start();
include '../db.php';

// Only admin can delete
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: admin_login.php");
    exit;
}

// Check ID
if (!isset($_GET['id'])) {
    header("Location: view_hotels.php");
    exit;
}

$hotel_id = intval($_GET['id']);

// Delete manager first
mysqli_query($conn, "DELETE FROM managers WHERE hotel_id = $hotel_id");

// Delete hotel image
$result = mysqli_query($conn, "SELECT image FROM hotels WHERE id = $hotel_id");
if ($row = mysqli_fetch_assoc($result)) {
    $imagePath = "../uploads/" . $row['image'];
    if (file_exists($imagePath)) {
        unlink($imagePath); // remove image file
    }
}

// Delete hotel
if (mysqli_query($conn, "DELETE FROM hotels WHERE id = $hotel_id")) {
    header("Location: view_hotels.php?success=deleted");
    exit;
} else {
    echo "Error deleting hotel: " . mysqli_error($conn);
}
?>
