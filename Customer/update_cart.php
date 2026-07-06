<?php
session_start();

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['quantities'])){
    foreach($_POST['quantities'] as $index => $qty){
        $qty = intval($qty);
        if($qty > 0 && isset($_SESSION['cart'][$index])){
            $_SESSION['cart'][$index]['quantity'] = $qty;
        }
    }
}

header("Location: cart.php");
exit();
?>
