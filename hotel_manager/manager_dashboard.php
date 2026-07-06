<?php
session_start();
include '../db.php';

// Check if logged in as manager
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'manager'){
    header("Location: ../manager_login.php");
    exit;
}

// Get assigned hotel
if(!isset($_SESSION['hotel_id'])){
    die("No hotel assigned to this manager!");
}

$hotel_id = $_SESSION['hotel_id'];

// Fetch hotel info
$hotel = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM hotels WHERE id=$hotel_id"));

// Fetch order stats
$total_orders   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM orders WHERE hotel_id=$hotel_id"))['total'];
$pending_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM orders WHERE hotel_id=$hotel_id AND order_status='pending'"))['total'];
$total_earnings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_amount) as total FROM orders WHERE hotel_id=$hotel_id"))['total'];

// Fetch top 5 most ordered menu items
$chartDataQuery = $conn->query("
    SELECT m.item_name, SUM(oi.quantity) as total_quantity
    FROM order_items oi
    JOIN menu m ON oi.menu_id = m.id
    JOIN orders o ON oi.order_id = o.id
    WHERE o.hotel_id = $hotel_id
    GROUP BY m.item_name
    ORDER BY total_quantity DESC
    LIMIT 5
");

$foodNames = [];
$foodQtys  = [];

while($row = $chartDataQuery->fetch_assoc()){
    $foodNames[] = $row['item_name'];
    $foodQtys[]  = $row['total_quantity'];
}

$foodNamesJson = json_encode($foodNames);
$foodQtysJson  = json_encode($foodQtys);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manager Dashboard | <?= htmlspecialchars($hotel['hotel_name']); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}
body{background:#0f172a;color:#e5e7eb;display:flex;min-height:100vh;}
.sidebar{width:220px;background:#020617;padding:25px 15px;display:flex;flex-direction:column;position:fixed;height:100%;}
.sidebar h2{text-align:center;color:#38bdf8;margin-bottom:40px;}
.sidebar ul{list-style:none;padding:0;}
.sidebar ul li{margin-bottom:18px;}
.sidebar ul li a{display:block;color:#cbd5f5;text-decoration:none;padding:10px 12px;border-radius:8px;transition:0.3s;}
.sidebar ul li a:hover, .sidebar ul li a.active{background:#1e293b;color:#38bdf8;}
.main-content{margin-left:220px;padding:40px 25px;flex:1;}
header h1{color:#38bdf8;font-size:2rem;}
header p{color:#94a3b8;margin-top:5px;}
.cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px;margin-top:30px;}
.card{background:#020617;padding:20px;border-radius:12px;box-shadow:0 10px 25px rgba(0,0,0,0.3);}
.card h3{color:#38bdf8;margin-bottom:12px;}
.card p{font-size:1.4rem;font-weight:600;color:#e5e7eb;}
.card span{font-size:0.85rem;color:#94a3b8;}
.btn{display:inline-block;padding:10px 18px;background:#38bdf8;color:#020617;border-radius:8px;text-decoration:none;margin-top:10px;transition:0.3s;}
.btn:hover{background:#0ea5e9;transform:scale(1.05);}
.chart-card{margin-top:30px;padding:20px;background:#020617;border-radius:12px;box-shadow:0 10px 25px rgba(0,0,0,0.3);}
@media(max-width:768px){.main-content{margin-left:0;padding:20px;}.cards{grid-template-columns:1fr;}}
</style>
</head>
<body>

<div class="sidebar">
    <h2><?= htmlspecialchars($hotel['hotel_name']); ?></h2>
    <ul>
        <li><a class="active" href="manager_dashboard.php">🏠 Dashboard</a></li>
        <li><a href="view_order.php">📦 View Orders</a></li>
        <li><a href="manager_menu.php">🍽 Manage Menu</a></li>
        <li><a href="manager_profile.php">👤 Profile</a></li>
        <li><a href="../logout.php">🔒 Logout</a></li>
    </ul>
</div>

<div class="main-content">
<header>
    <h1>Welcome, <?= htmlspecialchars($_SESSION['username']); ?></h1>
    <p>Manage your hotel efficiently</p>
</header>

<section class="cards">
    <div class="card">
        <h3>Total Orders</h3>
        <p><?= $total_orders ?: 0 ?></p>
        <span>All orders placed</span>
    </div>
    <div class="card">
        <h3>Pending Orders</h3>
        <p><?= $pending_orders ?: 0 ?></p>
        <span>Currently in process</span>
    </div>
    <div class="card">
        <h3>Total Earnings</h3>
        <p>₹<?= $total_earnings ?: 0 ?></p>
        <span>Revenue from this hotel</span>
    </div>
    <div class="card">
        <h3>Hotel Status</h3>
        <p><?= ucfirst($hotel['status']) ?></p>
        <span>Active / Inactive</span>
    </div>
</section>

<div class="chart-card">
    <h3>📊 Top Ordered Items</h3>
    <canvas id="topItemsChart" style="width:100%; max-width:700px; height:350px;"></canvas>
</div>

<a href="view_order.php" class="btn">View Orders</a>
<a href="manager_menu.php" class="btn">Manage Menu</a>
</div>

<script>
const ctx = document.getElementById('topItemsChart').getContext('2d');
const topItemsChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= $foodNamesJson; ?>,
        datasets: [{
            label: 'Quantity Ordered',
            data: <?= $foodQtysJson; ?>,
            backgroundColor: 'rgba(14, 165, 233, 0.7)',
            borderColor: 'rgba(14, 165, 233, 1)',
            borderWidth: 1,
            borderRadius: 6
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: { mode: 'index', intersect: false }
        },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1 } }
        }
    }
});
</script>

</body>
</html>
