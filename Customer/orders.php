<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /DineFlow/login.php");
    exit;
}

include '../db.php';
$user_id = $_SESSION['user_id'];

// Handle delete specific order item
if(isset($_GET['delete_item'])){
    $order_item_id = intval($_GET['delete_item']);

    $item_q = mysqli_query($conn, "
        SELECT * FROM order_items WHERE id=$order_item_id LIMIT 1
    ");

    if($item_q && mysqli_num_rows($item_q) > 0){
        $item = mysqli_fetch_assoc($item_q);
        $order_id = $item['order_id'];
        $item_total = $item['quantity'] * $item['price'];

        // Delete item
        $del_stmt = $conn->prepare("DELETE FROM order_items WHERE id=?");
        $del_stmt->bind_param("i", $order_item_id);
        $del_stmt->execute();

        // Update total
        $update_stmt = $conn->prepare("
            UPDATE orders 
            SET total_amount = total_amount - ? 
            WHERE id=?
        ");
        $update_stmt->bind_param("di", $item_total, $order_id);
        $update_stmt->execute();

        // Delete order if empty
        $check_items = mysqli_query($conn, "
            SELECT COUNT(*) as cnt 
            FROM order_items 
            WHERE order_id=$order_id
        ");
        $count = mysqli_fetch_assoc($check_items)['cnt'];

        if($count == 0){
            $conn->query("DELETE FROM orders WHERE id=$order_id");
        }

        header("Location: orders.php");
        exit();
    }
}

// Fetch all orders of this user
$query = mysqli_query($conn, "
    SELECT o.*, h.hotel_name 
    FROM orders o
    JOIN hotels h ON o.hotel_id = h.id
    WHERE o.user_id = $user_id
    ORDER BY o.id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>My Orders | DineFlow</title>
<link rel="stylesheet" href="../assets/css/customer.css">
<style>
.order-items { margin-top: 10px; border-collapse: collapse; width: 100%; }
.order-items th, .order-items td { border: 1px solid #ccc; padding: 8px 10px; font-size: 14px; text-align: left; }
.order-items th { background: #f3f4f6; color: #000; }
.card { background: #020617; border-radius: 16px; padding: 20px; margin-bottom: 30px; box-shadow: 0 8px 20px rgba(0,0,0,0.4); color: #e5e7eb; }
.card h3 { margin-bottom: 5px; }
.card p, .card span, .card small { display: block; margin-bottom: 5px; }
.card table { margin-top: 10px; background: #1e293b; border-radius: 8px; overflow: hidden; }
.card table th, .card table td { background: #1e293b; color: #e5e7eb; border: 1px solid #334155; }
.card table th { background: #0ea5e9; color: #fff; }
.delete-item-btn { padding: 4px 8px; border-radius: 6px; background: #ef4444; color: #fff; text-decoration:none; font-size:12px; }
.delete-item-btn:hover { background:#dc2626; }
.sidebar { width:200px; position:fixed; top:0; left:0; height:100%; background:#020617; padding:20px; }
.sidebar h2 { color:#38bdf8; text-align:center; margin-bottom:40px; }
.sidebar ul { list-style:none; }
.sidebar ul li { margin-bottom:12px; }
.sidebar ul li a { color:#cbd5f5; text-decoration:none; padding:8px; display:block; border-radius:8px; }
.sidebar ul li a.active, .sidebar ul li a:hover { background:#1e293b; color:#38bdf8; }
.main-content { margin-left:220px; padding:40px; }
</style>
</head>

<body>
<div class="sidebar">
    <h2>DineFlow</h2>
    <ul>
        <li><a href="dashboard.php">🏠 Dashboard</a></li>
        <li><a href="hotels.php">🏨 Hotels</a></li>
        <li><a href="menu.php">🍽 View Menu</a></li>
        <li><a class="active" href="orders.php">📦 My Orders</a></li>
        <li><a href="profile.php">👤 Profile</a></li>
        <li><a href="/DineFlow/logout.php">🔒 Logout</a></li>
    </ul>
</div>

<div class="main-content">
<header>
    <h1>My Orders</h1>
    <p>Track your food orders</p>
</header>

<section class="cards">

<?php if (mysqli_num_rows($query) > 0): ?>
    <?php while ($row = mysqli_fetch_assoc($query)): ?>
        <div class="card">
            <h3><?php echo htmlspecialchars($row['hotel_name']); ?></h3>
            <p><strong>Total: ₹<?php echo number_format($row['total_amount'],2); ?></strong></p>
            <span>Status: <?php echo htmlspecialchars($row['status']); ?></span>
            <small>Ordered on: <?php echo $row['order_date']; ?></small>

            <?php
            $order_id = $row['id'];
            // Join order_items with menu to get item name
            $items = mysqli_query($conn, "
                SELECT oi.id as order_item_id,
                       oi.quantity,
                       oi.price,
                       m.item_name,
                       m.image
                FROM order_items oi
                JOIN menu m ON oi.menu_id = m.id
                WHERE oi.order_id = $order_id
            ");


            if(mysqli_num_rows($items) > 0):
            ?>
            <table class="order-items">
                <tr>
                    <th>Image</th>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                    <th>Remove</th>
                </tr>

                <?php while($item = mysqli_fetch_assoc($items)): ?>
                <tr>
                    <td>
                        <img src="../uploads/<?php echo htmlspecialchars($item['image']); ?>" 
                             style="width:60px;height:50px;object-fit:cover;border-radius:6px;">
                    </td>
                    <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td>₹<?php echo number_format($item['price'],2); ?></td>
                    <td>₹<?php echo number_format($item['quantity'] * $item['price'],2); ?></td>
                    <td>
                        <a href="orders.php?delete_item=<?php echo $item['order_item_id']; ?>"
                           onclick="return confirm('Remove this item?');" 
                           class="delete-item-btn">❌</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </table>

            <?php endif; ?>

        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>No orders placed yet.</p>
<?php endif; ?>

</section>
</div>
</body>
</html>
