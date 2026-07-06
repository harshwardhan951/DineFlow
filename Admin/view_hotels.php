<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: admin_login.php");
    exit;
}

include '../db.php';

// Fetch all hotels
$hotels = mysqli_query($conn, "SELECT * FROM hotels ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Hotels | DineFlow Admin</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}

body {
    display: flex;
    min-height: 100vh;
    background: #0f172a;
    color: #e5e7eb;
}

/* SIDEBAR */
.sidebar {
    width: 240px;
    background: #020617;
    padding: 30px 20px;
    display: flex;
    flex-direction: column;
    position: fixed;
    height: 100%;
}

.sidebar h2 {
    color: #38bdf8;
    text-align: center;
    margin-bottom: 40px;
}

.sidebar ul {
    list-style: none;
    padding: 0;
}

.sidebar ul li {
    margin-bottom: 20px;
}

.sidebar ul li a {
    display: block;
    color: #cbd5f5;
    text-decoration: none;
    padding: 12px 15px;
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
    margin-left: 240px; /* sidebar width */
    flex: 1;
    padding: 40px 20px;
}

/* HOTEL GRID */
.hotel-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 25px;
    margin-top: 30px;
}

/* HOTEL CARD */
.hotel-card {
    background: #020617;
    border-radius: 15px;
    overflow: hidden;
    transition: transform 0.3s, box-shadow 0.3s;
    cursor: pointer;
}

.hotel-card img {
    width: 100%;
    height: 160px;
    object-fit: cover;
}

.hotel-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 15px 30px rgba(56, 189, 248, 0.5);
}

.hotel-info {
    padding: 15px;
    text-align: center;
}

.hotel-info h3 {
    margin-bottom: 8px;
    color: #38bdf8;
}

.hotel-info p {
    font-size: 0.9rem;
    color: #94a3b8;
    margin-bottom: 12px;
}

/* SELECT BUTTON */
.select-btn {
    display: inline-block;
    padding: 10px 20px;
    background: #38bdf8;
    color: #020617;
    border-radius: 25px;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.3s;
}

.select-btn:hover {
    background: #0ea5e9;
    transform: scale(1.05);
}

/* STATUS BADGE */
.status {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 500;
    margin-top: 5px;
}

.status.active {
    background: #22c55e;
    color: #020617;
}

.status.inactive {
    background: #ef4444;
    color: #020617;
}

/* HEADER */
header h1 {
    color: #38bdf8;
    font-size: 2rem;
}

header p {
    color: #94a3b8;
    margin-top: 5px;
}

.action-col {
    text-align: center;
    vertical-align: middle;
}

.btn-edit {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;

    padding: 8px 14px;
    background: #4f46e5;   /* premium blue */
    color: #fff;
    text-decoration: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;

    transition: all 0.3s ease;
}

.btn-delete {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 8px 14px;
    background: #ef4444; /* red */
    color: #fff;
    text-decoration: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-delete:hover {
    background: #dc2626;
    transform: translateY(-1px);
}


.action-buttons {
    margin-top: 15px;
    display: flex;
    justify-content: center;
}

.main-content {
    position: relative;
    z-index: 1;
}

/* Buttons container */
.action-buttons {
    display: flex;
    justify-content: center; /* center under the card */
    gap: 10px;               /* space between buttons */
    margin-top: 12px;
}

/* Edit Button */
.btn-edit {
    padding: 8px 14px;
    background: #4f46e5;
    color: #fff;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.3s ease;
}

.btn-edit:hover {
    background: #4338ca;
    transform: translateY(-1px);
}

/* Delete Button */
.btn-delete {
    padding: 8px 14px;
    background: #ef4444;
    color: #fff;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.3s ease;
}

.btn-delete:hover {
    background: #dc2626;
    transform: translateY(-1px);
}



.btn-edit:hover {
    background: #4338ca;
    transform: translateY(-1px);
}


/* RESPONSIVE */
@media(max-width: 768px){
    .main-content {
        margin-left: 0;
        padding: 20px;
    }
    .hotel-container {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    }
}
</style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>DineFlow Admin</h2>
    <ul>
        <li><a href="admin_dashboard.php">🏠 Dashboard</a></li>
        <li><a href="add_hotel.php">➕ Add Hotel</a></li>
        <li><a class="active" href="view_hotels.php">🏨 View Hotels</a></li>
        <li><a href="view_orders.php">📦 View Orders</a></li>
        <li><a href="../logout.php">🔒 Logout</a></li>
    </ul>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">
<header>
    <h1>All Hotels</h1>
    <p>Check all hotels registered in DineFlow</p>
</header>

<section class="hotel-container">
<?php if(mysqli_num_rows($hotels) > 0): ?>
    <?php while($row = mysqli_fetch_assoc($hotels)): ?>
    
    <div class="hotel-card">
    <img src="../uploads/<?php echo $row['image']; ?>" alt="Hotel Image">
    <div class="hotel-info">
        <h3><?php echo htmlspecialchars($row['hotel_name']); ?></h3>
        <p>📍 <?php echo htmlspecialchars($row['location']); ?></p>
        <span class="status <?php echo $row['status']; ?>">
            <?php echo ucfirst($row['status']); ?>
        </span>
        
        <!-- BUTTONS WRAPPER -->
        <div class="action-buttons">
            <a href="edit_hotels.php?id=<?= $row['id']; ?>" class="btn-edit">✏️ Edit</a>
            <a href="delete_hotel.php?id=<?= $row['id']; ?>" 
               class="btn-delete" 
               onclick="return confirm('Are you sure you want to delete this hotel?');">
               🗑 Delete
            </a>
        </div>
    </div>
</div>

    <?php endwhile; ?>
<?php else: ?>
    <p>No hotels found!</p>
<?php endif; ?>
</section>

</div>

</body>
</html>
