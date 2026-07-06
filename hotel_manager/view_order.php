<?php
session_start();
include '../db.php';

if (!isset($_SESSION['hotel_id'])) {
    header("Location: ../login.php");
    exit();
}

$hotel_id = $_SESSION['hotel_id'];

/* ==============================
   UPDATE ORDER STATUS
================================= */
if(isset($_POST['update_status'])){
    $order_id = intval($_POST['order_id']);
    $new_status = $_POST['status'];

    $stmt_update = $conn->prepare("UPDATE orders SET status=? WHERE id=? AND hotel_id=?");
    $stmt_update->bind_param("sii", $new_status, $order_id, $hotel_id);
    $stmt_update->execute();
}

/* ==============================
   DELETE ORDER
================================= */
if(isset($_GET['delete'])){
    $order_id = intval($_GET['delete']);

    $stmt_item = $conn->prepare("DELETE FROM order_items WHERE order_id=?");
    $stmt_item->bind_param("i", $order_id);
    $stmt_item->execute();

    $stmt_order = $conn->prepare("DELETE FROM orders WHERE id=? AND hotel_id=?");
    $stmt_order->bind_param("ii", $order_id, $hotel_id);
    $stmt_order->execute();

    header("Location: view_order.php");
    exit();
}

/* ==============================
   FETCH ORDERS
================================= */
$sql = "SELECT o.*, u.username AS customer_name
        FROM orders o
        JOIN users u ON o.user_id = u.id
        WHERE o.hotel_id = ?
        ORDER BY o.order_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $hotel_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
<title>Manager | View Orders</title>

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif;}
body{background:#0f172a;color:#fff;}
.layout{display:flex;}

.sidebar{
    width:240px;height:100vh;background:#020617;
    padding:20px;position:fixed;left:0;top:0;
}
.sidebar h2{text-align:center;color:#38bdf8;margin-bottom:30px;}
.sidebar a{
    display:block;padding:12px 15px;margin-bottom:10px;
    text-decoration:none;color:#cbd5f5;border-radius:8px;
}
.sidebar a:hover,.sidebar a.active{
    background:#1e293b;color:#38bdf8;
}

.content{
    margin-left:240px;padding:30px;width:100%;
}

h1{margin-bottom:20px;}

.order-card{
    background:#020617;
    padding:20px;
    border-radius:12px;
    margin-bottom:25px;
    box-shadow:0 5px 15px rgba(0,0,0,0.4);
}

.order-header{
    display:flex;
    justify-content:space-between;
    margin-bottom:15px;
}

.status{
    padding:6px 12px;
    border-radius:20px;
    font-size:14px;
}
.Pending{background:#facc15;color:#000;}
.Completed{background:#22c55e;}
.Cancelled{background:#ef4444;}

.item-box{
    display:flex;
    align-items:center;
    background:#1e293b;
    padding:10px;
    margin-bottom:8px;
    border-radius:8px;
}

.item-box img{
    width:70px;
    height:60px;
    object-fit:cover;
    border-radius:8px;
    margin-right:12px;
}

.item-details{
    flex:1;
}

.status-form select{
    padding:6px 8px;
    border-radius:6px;
    background:#1e293b;
    color:#fff;
    border:none;
}

.status-form button{
    padding:6px 10px;
    background:#38bdf8;
    border:none;
    border-radius:6px;
    color:#020617;
    cursor:pointer;
    margin-left:5px;
}

.delete-btn{
    padding:6px 10px;
    background:#ef4444;
    border:none;
    border-radius:6px;
    color:#fff;
    cursor:pointer;
    margin-left:10px;
}
.delete-btn:hover{background:#dc2626;}
</style>
</head>

<body>

<div class="layout">

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>DineFlow</h2>
    <a href="manager_dashboard.php">🏠 Dashboard</a>
    <a href="view_order.php" class="active">📦 View Orders</a>
    <a href="manager_menu.php">🍽 Manage Menu</a>
    <a href="manager_profile.php">👤 Profile</a>
    <a href="../logout.php">🚪 Logout</a>
</div>

<!-- CONTENT -->
<div class="content">
<h1>📦 Orders Received</h1>

<?php if($result->num_rows > 0): ?>
<?php while($row = $result->fetch_assoc()): ?>

<div class="order-card">

    <div class="order-header">
        <div>
            <strong>Order #<?php echo $row['id']; ?></strong><br>
            Customer: <?php echo htmlspecialchars($row['customer_name']); ?><br>
            Total: ₹<?php echo number_format($row['total_amount'],2); ?><br>
            Date: <?php echo $row['order_date']; ?>
        </div>

        <div>
            <span class="status <?php echo $row['status']; ?>">
                <?php echo $row['status']; ?>
            </span>
        </div>
    </div>

    <h4 style="margin-bottom:10px;">Items Ordered:</h4>

    <?php
    $order_id = $row['id'];

    $items_stmt = $conn->prepare("
        SELECT m.item_name, m.image, oi.quantity, oi.price
        FROM order_items oi
        JOIN menu m ON oi.menu_id = m.id
        WHERE oi.order_id = ?
    ");
    $items_stmt->bind_param("i", $order_id);
    $items_stmt->execute();
    $items_result = $items_stmt->get_result();

    if($items_result->num_rows > 0):
        while($item = $items_result->fetch_assoc()):
    ?>
        <div class="item-box">
            <img src="../uploads/<?php echo htmlspecialchars($item['image']); ?>" alt="">
            
            <div class="item-details">
                <strong><?php echo htmlspecialchars($item['item_name']); ?></strong><br>
                Qty: <?php echo $item['quantity']; ?> |
                ₹<?php echo number_format($item['price'],2); ?> |
                Total: ₹<?php echo number_format($item['quantity'] * $item['price'],2); ?>
            </div>
        </div>
    <?php endwhile; else: ?>
        <p>No items found.</p>
    <?php endif; ?>

    <div style="margin-top:15px;">
        <form class="status-form" method="POST" style="display:inline;">
            <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
            <select name="status">
                <option value="Pending" <?php if($row['status']=="Pending") echo "selected"; ?>>Pending</option>
                <option value="Completed" <?php if($row['status']=="Completed") echo "selected"; ?>>Completed</option>
                <option value="Cancelled" <?php if($row['status']=="Cancelled") echo "selected"; ?>>Cancelled</option>
            </select>
            <button type="submit" name="update_status">Update</button>
        </form>

        <a href="view_order.php?delete=<?php echo $row['id']; ?>" 
           onclick="return confirm('Delete this order?');">
            <button class="delete-btn">Delete</button>
        </a>
    </div>

</div>

<?php endwhile; ?>
<?php else: ?>
<p>No orders found.</p>
<?php endif; ?>

</div>
</div>

</body>
</html>
