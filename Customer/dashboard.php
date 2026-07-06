<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /DineFlow/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

/* ===============================
   1️⃣ Total Orders
================================ */
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM orders WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$totalOrders = $row['total'] ?? 0;
$stmt->close();

/* ===============================
   2️⃣ Pending Orders
================================ */
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM orders WHERE user_id = ? AND status = 'Pending'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$pendingOrders = $row['total'] ?? 0;
$stmt->close();

/* ===============================
   3️⃣ Total Spend
================================ */
$stmt = $conn->prepare("SELECT SUM(total_amount) as total FROM orders WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$totalSpend = $row['total'] ?? 0;
$stmt->close();

/* ===============================
   4️⃣ Favourite Hotel
================================ */
$stmt = $conn->prepare("
    SELECT h.hotel_name, COUNT(o.id) as cnt
    FROM orders o
    JOIN hotels h ON o.hotel_id = h.id
    WHERE o.user_id = ?
    GROUP BY o.hotel_id
    ORDER BY cnt DESC
    LIMIT 1
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$favHotel = $row['hotel_name'] ?? 'No orders yet';
$stmt->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>DineFlow | Customer Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../assets/css/customer.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
/* MAIN CONTENT */
.main-content {
    margin-left: 260px;
    padding: 30px;
    color: #e5e7eb;
}

header h1 {
    color: #38bdf8;
}

header h1 span {
    color: #facc15;
}

header p {
    color: #94a3b8;
    margin-top: 8px;
}

/* DASHBOARD CARDS */
.cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 25px;
    margin-top: 25px;
}

.card {
    background: #020617;
    padding: 20px;
    border-radius: 15px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.6);
    transition: transform 0.3s;
}

.card:hover {
    transform: translateY(-6px);
}

.card h3 {
    margin-bottom: 12px;
    font-size: 1.2rem;
    color: #38bdf8;
}

.card p {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 5px;
}

.card span {
    font-size: 0.85rem;
    color: #94a3b8;
}

/* INFO SECTION */
.info-box {
    margin-top: 40px;
    background: #020617;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.6);
}

.info-box h2 {
    color: #38bdf8;
    margin-bottom: 12px;
}

.info-box p {
    color: #cbd5f5;
    line-height: 1.6;
}

/* Responsive */
@media(max-width: 768px) {
    .cards {
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    }
}
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>DineFlow</h2>
    <ul>
        <li><a class="active" href="dashboard.php">🏠 Dashboard</a></li>
        <li><a href="hotels.php">🏨 Hotels</a></li>
        <li><a href="menu.php">🍽 View Menu</a></li>
        <li><a href="orders.php">📦 My Orders</a></li>
        <li><a href="profile.php">👤 Profile</a></li>
        <li><a href="/DineFlow/logout.php">🔒 Logout</a></li>
    </ul>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">

    <!-- HEADER -->
    <header>
        <h1>Welcome back, <span><?php echo htmlspecialchars($username); ?></span></h1>
        <p>Manage your orders, explore menus & enjoy seamless dining.</p>
    </header>

    <!-- DASHBOARD CARDS -->
    <section class="cards">

        <div class="card">
            <h3>Total Orders</h3>
            <p><?php echo $totalOrders; ?></p>
            <span>Orders placed till now</span>
        </div>

        <div class="card">
            <h3>Pending Orders</h3>
            <p><?php echo $pendingOrders; ?></p>
            <span>Currently processing</span>
        </div>

        <div class="card">
            <h3>Total Spend</h3>
            <p>₹<?php echo number_format($totalSpend,2); ?></p>
            <span>Overall spending</span>
        </div>

        <div class="card">
            <h3>Favourite Hotel</h3>
            <p><?php echo htmlspecialchars($favHotel); ?></p>
            <span>Most ordered from</span>
        </div>

    </section>

    <!-- INFO SECTION -->
    <section class="info-box">
        <h2>Why DineFlow?</h2>
        <p>
            DineFlow provides a smart and seamless digital dining experience.
            Browse menus, place orders, track status, and enjoy a modern
            restaurant interaction system.
        </p>
    </section>

</div>

</body>
</html>
