<?php
session_start();
include '../db.php';

if(!isset($_SESSION['user_id']) || !isset($_SESSION['cart']) || count($_SESSION['cart']) == 0){
    header("Location: cart.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$hotel_id = $_SESSION['cart'][0]['hotel_id']; // assuming all items from same hotel

$total_amount = 0;
foreach($_SESSION['cart'] as $item){
    $total_amount += $item['price'] * $item['quantity'];
}

// Create order
$stmt = $conn->prepare("INSERT INTO orders (user_id, hotel_id, total_amount, status, order_date) VALUES (?, ?, ?, 'Pending', NOW())");
$stmt->bind_param("iid", $user_id, $hotel_id, $total_amount);
$stmt->execute();
$order_id = $stmt->insert_id;

// Insert order items
$stmt_item = $conn->prepare("INSERT INTO order_items (order_id, food_name, quantity, price) VALUES (?, ?, ?, ?)");
foreach($_SESSION['cart'] as $item){
    $stmt_item->bind_param("isid", $order_id, $item['name'], $item['quantity'], $item['price']);
    $stmt_item->execute();
}

// Clear cart
unset($_SESSION['cart']);

// Redirect to orders page
header("Location: orders.php?success=1");
exit();
?>
