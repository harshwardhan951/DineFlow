<?php
session_start();
include '../db.php';

if(!isset($_SESSION['user_id'])){
    header("Location: /DineFlow/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id       = $_POST['id'];
    $name     = $_POST['name'];
    $price    = $_POST['price'];
    $quantity = $_POST['quantity'];
    $hotel_id = $_POST['hotel_id']; // ensure this is passed from menu.php

    $item_total = $price * $quantity;

    // 1️⃣ Check if a pending order already exists for this user & hotel
    $check_order = $conn->query("SELECT * FROM orders WHERE user_id=$user_id AND hotel_id=$hotel_id AND status='Pending' LIMIT 1");

    if($check_order->num_rows > 0){
        $order = $check_order->fetch_assoc();
        $order_id = $order['id'];
        $new_total = $order['total_amount'] + $item_total;

        // Update total_amount
        $stmt = $conn->prepare("UPDATE orders SET total_amount=? WHERE id=?");
        $stmt->bind_param("di", $new_total, $order_id);
        $stmt->execute();
    } else {
        // Create new pending order
        $stmt = $conn->prepare("INSERT INTO orders (user_id, hotel_id, total_amount, status, order_date) VALUES (?, ?, ?, 'Pending', NOW())");
        $stmt->bind_param("iid", $user_id, $hotel_id, $item_total);
        $stmt->execute();
        $order_id = $stmt->insert_id;
    }

    // 2️⃣ Insert into order_items table
    // 2️⃣ Insert into order_items table (FIXED)
    $stmt_item = $conn->prepare("
        INSERT INTO order_items (order_id, menu_id, quantity, price) 
        VALUES (?, ?, ?, ?)
    ");
    $stmt_item->bind_param("iiid", $order_id, $id, $quantity, $price);
    $stmt_item->execute();

    // 3️⃣ Optional: add to session cart
    $item = [
        'id' => $id,
        'name' => $name,
        'price' => $price,
        'quantity' => $quantity,
        'hotel_id' => $hotel_id
    ];

    if(isset($_SESSION['cart'])){
        $found = false;
        foreach($_SESSION['cart'] as &$cart_item){
            if($cart_item['id'] == $id){
                $cart_item['quantity'] += $quantity;
                $found = true;
                break;
            }
        }
        if(!$found){
            $_SESSION['cart'][] = $item;
        }
    } else {
        $_SESSION['cart'] = [$item];
    }

    // Redirect to orders page
    header("Location: orders.php?added=1");
    exit();
}
