<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../admin/admin_login.php");
    exit;
}

include '../db.php';

// Fetch stats
$total_hotels = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM hotels"))['total'];
$total_orders = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM orders"))['total'];
$total_customers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role='customer'"))['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin Dashboard | DineFlow</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}

/* BODY & SIDEBAR */
body {
    display: flex;
    min-height: 100vh;
    background: #0f172a;
    color: #e5e7eb;
}

.sidebar {
    width: 240px;
    background: #020617;
    padding: 25px;
    display: flex;
    flex-direction: column;
}

.sidebar h2 {
    text-align: center;
    color: #38bdf8;
    margin-bottom: 40px;
}

.sidebar ul {
    list-style: none;
}

.sidebar ul li {
    margin-bottom: 20px;
}

.sidebar ul li a {
    text-decoration: none;
    color: #cbd5f5;
    display: block;
    padding: 12px;
    border-radius: 8px;
    transition: 0.3s;
}

.sidebar ul li a:hover,
.sidebar ul li a.active {
    background: #1e293b;
    color: #38bdf8;
}

/* MAIN CONTENT */
.main-content {
    flex: 1;
    padding: 30px;
}

header h1 {
    color: whitesmoke;
}

header p {
    color: lightblue;
    margin-top: 5px;
}

/* DASHBOARD CARDS */
.cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 25px;
    margin-top: 20px;
}

.card {
    background: #020617;
    padding: 20px;
    border-radius: 25px;
    box-shadow: 0 15px 30px rgba(0,0,0,0.6);
    transition: 0.3s;
}

.card:hover {
    transform: translateY(-5px);
}

.card h3 {
    font-size: 1.1rem;
    margin-bottom: 10px;
}

.card p {
    font-size: 1.8rem;
    font-weight: 600;
    margin-bottom: 5px;
}

.card span {
    font-size: 0.85rem;
    color: #94a3b8;
}

/* BUTTONS */
.admin-actions {
    margin-top: 30px;
    display: flex;
    gap: 20px;
}

.admin-actions a {
    padding: 12px 25px;
    background: #38bdf8;
    color: #020617;
    border-radius: 8px;
    font-weight: 500;
    text-decoration: none;
    transition: 0.3s;
}

.admin-actions a:hover {
    background: #0ea5e9;
}
</style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>DineFlow Admin</h2>
    <ul>
        <li><a class="active" href="dashboard.php">🏠 Dashboard</a></li>
        <li><a href="add_hotel.php">➕ Add Hotel</a></li>
        <li><a href="view_hotels.php">🏨 View Hotels</a></li>
        <li><a href="view_orders.php">📦 View Orders</a></li>
        <li><a href="../logout.php">🔒 Logout</a></li>
    </ul>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">
    <header>
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></h1>
        <p>Manage hotels, menus, and orders from here.</p>
    </header>

    <!-- DASHBOARD CARDS -->
    <section class="cards">
        <div class="card">
            <h3>Total Hotels</h3>
            <p><?php echo $total_hotels; ?></p>
            <span>Registered hotels</span>
        </div>
        <div class="card">
            <h3>Total Orders</h3>
            <p><?php echo $total_orders; ?></p>
            <span>Orders placed</span>
        </div>
        <div class="card">
            <h3>Total Customers</h3>
            <p><?php echo $total_customers; ?></p>
            <span>Active users</span>
        </div>
    </section>

    <!-- ADMIN ACTIONS -->
    <div class="admin-actions">
        <a href="add_hotel.php">➕ Add New Hotel</a>
        <a href="view_hotels.php">🏨 Manage Hotels</a>
    </div>
</div>

</body>
</html>
