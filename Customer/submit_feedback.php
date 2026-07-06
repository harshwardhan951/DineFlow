<?php
session_start();
include '../db.php';

if (!isset($_POST['submit_feedback'])) {
    header("Location: dashboard.php");
    exit();
}

$customer_id = $_SESSION['user_id'];
$hotel_id = intval($_POST['hotel_id']);
$rating = intval($_POST['rating']);
$message = trim($_POST['message']);

$stmt = $conn->prepare(
    "INSERT INTO feedback (customer_id, hotel_id, rating, message)
     VALUES (?, ?, ?, ?)"
);
$stmt->bind_param("iiis", $customer_id, $hotel_id, $rating, $message);

if ($stmt->execute()) {
    header("Location: dashboard.php?success=feedback");
} else {
    echo "Error submitting feedback";
}
