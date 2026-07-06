<?php
include 'db.php';

$order_id = $_POST['order_id'];
$status = $_POST['status'];

$sql = "UPDATE orders SET order_status=? WHERE order_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $status, $order_id);
$stmt->execute();

header("Location: manager_view_orders.php");
exit();
