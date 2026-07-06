<?php
session_start();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cart | DineFlow</title>
    <style>
        body { font-family: Arial; background:#f0f0f0; }
        h1 { text-align:center; margin-top:20px; color:#0f172a; }
        table { width:70%; margin:auto; border-collapse: collapse; margin-top:30px; background:#fff; border-radius:10px; overflow:hidden; }
        th, td { padding:12px; border:1px solid #ccc; text-align:center; }
        th { background:#38bdf8; color:#fff; }
        input[type=number] { width:50px; }
        button { padding:6px 12px; border:none; border-radius:6px; background:#38bdf8; color:#fff; cursor:pointer;}
        button:hover { background:#0ea5e9; }
        a { text-decoration:none; color:#38bdf8; }
        .actions { text-align:center; margin-top:20px; }
    </style>
</head>
<body>

<h1>🛒 Your Cart</h1>
<div style="text-align:center; margin-bottom:15px;">
    <a href="menu.php">⬅️ Back to Menu</a>
</div>

<?php if(isset($_SESSION['cart']) && count($_SESSION['cart'])>0): ?>
    <form method="POST" action="update_cart.php">
        <table>
            <tr>
                <th>Item</th>
                <th>Price</th>
                <th>Quantity</th>
                <th>Total</th>
                <th>Remove</th>
            </tr>
            <?php 
            $grand_total = 0;
            foreach($_SESSION['cart'] as $index => $item): 
                $total = $item['price'] * $item['quantity'];
                $grand_total += $total;
            ?>
            <tr>
                <td><?php echo htmlspecialchars($item['name']); ?></td>
                <td>₹<?php echo number_format($item['price'],2); ?></td>
                <td><input type="number" name="quantities[<?php echo $index; ?>]" value="<?php echo $item['quantity']; ?>" min="1"></td>
                <td>₹<?php echo number_format($total,2); ?></td>
                <td><a href="remove_cart.php?index=<?php echo $index; ?>">❌</a></td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="3" style="text-align:right;"><strong>Grand Total:</strong></td>
                <td colspan="2">₹<?php echo number_format($grand_total,2); ?></td>
            </tr>
        </table>

        <div class="actions">
            <button type="submit">Update Cart</button>
            <button type="button" onclick="window.location.href='checkout.php'">Checkout</button>
        </div>
    </form>
<?php else: ?>
    <p style="text-align:center; margin-top:50px;">Your cart is empty!</p>
<?php endif; ?>

</body>
</html>
