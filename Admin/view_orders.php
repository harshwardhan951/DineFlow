<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: admin_login.php");
    exit;
}

include '../db.php';

// Fetch orders with customer username and dishes
$sql = "
SELECT 
    o.id AS order_id,
    u.username,
    o.dishes,
    o.total_amount,
    o.status,
    o.order_date
FROM orders o
JOIN users u ON o.user_id = u.id
ORDER BY o.order_date DESC
";

$orders = mysqli_query($conn, $sql);

if (!$orders) {
    die("<h2 style='color:red'>Database query failed: " . mysqli_error($conn) . "</h2>");
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Orders | DineFlow Admin</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

<style>
* { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Poppins', sans-serif; }
body { display: flex; min-height: 100vh; background: #0f172a; color: #e5e7eb; }
.sidebar { width: 240px; background: #020617; padding: 30px 20px; display: flex; flex-direction: column; position: fixed; height: 100%; }
.sidebar h2 { color: #38bdf8; text-align: center; margin-bottom: 40px; }
.sidebar ul { list-style: none; padding: 0; }
.sidebar ul li { margin-bottom: 20px; }
.sidebar ul li a { display: block; color: #cbd5f5; text-decoration: none; padding: 12px 15px; border-radius: 8px; transition: 0.3s; }
.sidebar ul li a:hover, .sidebar ul li a.active { background: #1e293b; color: #38bdf8; }
.main-content { margin-left: 240px; flex: 1; padding: 40px 20px; }
header h1 { color: #38bdf8; font-size: 2rem; }
header p { color: #94a3b8; margin-top: 5px; }
table { width: 100%; border-collapse: collapse; margin-top: 30px; background: #020617; border-radius: 12px; overflow: hidden; }
table th, table td { padding: 12px 15px; text-align: left; }
.btn-edit{
    padding:8px 14px;
    background:#28a745;
    color:#fff;
    border-radius:20px;
    font-size:14px;
}
.btn-edit:hover{
    background:#1e7e34;
}

table th { background: #1e293b; color: #38bdf8; }
table tr { border-bottom: 1px solid #334155; transition: transform 0.2s, background 0.3s; }
table tr:hover { background: #1e293b; transform: scale(1.01); }
.status { padding: 5px 10px; border-radius: 12px; font-size: 0.85rem; font-weight: 500; }
.status.pending { background: #facc15; color: #020617; }
.status.completed { background: #22c55e; color: #020617; }
.status.cancelled { background: #ef4444; color: #020617; }
@media(max-width: 768px){ .main-content { margin-left: 0; padding: 20px; } table th, table td { font-size: 0.9rem; } }
</style>
</head>
<body>

<div class="sidebar">
    <h2>DineFlow Admin</h2>
    <ul>
        <li><a href="dashboard.php">🏠 Dashboard</a></li>
        <li><a href="add_hotel.php">➕ Add Hotel</a></li>
        <li><a href="view_hotels.php">🏨 View Hotels</a></li>
        <li><a class="active" href="view_orders.php">📦 View Orders</a></li>
        <li><a href="../logout.php">🔒 Logout</a></li>
    </ul>
</div>

<div class="main-content">
<header>
    <h1>All Orders</h1>
    <p>Track all customer orders and their statuses</p>
</header>

<table>
    <thead>
        <tr>
            <th>Order ID</th>
            <th>Customer ID</th>
            <th>Dishes</th>
            <th>Total (₹)</th>
            <th>Status</th>
            <th>Order Date</th>
        </tr>
    </thead>
    <tbody>
        <?php if(mysqli_num_rows($orders) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($orders)): ?>
            <tr>
                <td><?php echo $row['order_id']; ?></td>
                <td><?php echo htmlspecialchars($row['username']); ?></td>
                <td><?php echo htmlspecialchars($row['dishes']); ?></td>
                <td>₹<?php echo $row['total_amount']; ?></td>
                <td><span class="status <?php echo strtolower($row['status']); ?>"><?php echo ucfirst($row['status']); ?></span></td>
                <td><?php echo $row['order_date']; ?></td>
            </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="6" style="text-align:center;">No orders found!</td></tr>
        <?php endif; ?>
    </tbody>
</table>

</div>
</body>
</html>
