<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /DineFlow/login.php");
    exit();
}

include '../db.php';

$stmt = $conn->prepare("SELECT id, hotel_name, location, image FROM hotels WHERE status = 'active'");
$stmt->execute();
$result = $stmt->get_result();

$hotels = [];
while ($row = $result->fetch_assoc()) {
    $hotels[] = $row;
}

$stmt->close();
$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>DineFlow | Hotels</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="../assets/css/customer.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
.hotel-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 25px;
    margin-top: 30px;
}

.hotel-card {
    background: #020617;
    color: #e5e7eb;
    border-radius: 15px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.6);
    overflow: hidden;
    transition: 0.3s;
}

.hotel-card:hover {
    transform: translateY(-6px);
}

.hotel-card img {
    width: 100%;
    height: 160px;
    object-fit: cover;
}

.hotel-info {
    padding: 15px;
}

.hotel-info h3 {
    margin-bottom: 8px;
}

.hotel-info p {
    font-size: 0.9rem;
    color: #94a3b8;
}

.select-btn {
    display: block;
    margin-top: 12px;
    padding: 10px;
    text-align: center;
    background: #38bdf8;
    color: #020617;
    border-radius: 25px;
    font-weight: 500;
    text-decoration: none;
    transition: 0.3s;
}

.select-btn:hover {
    background: #0ea5e9;
    color: #fff;
}

@media(max-width: 480px) {
    .hotel-container {
        grid-template-columns: 1fr;
        gap: 15px;
    }
}

.main-content p {
    text-align: center;
    margin-top: 30px;
    font-size: 1.1rem;
    color: #cbd5f5;
}
</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>DineFlow</h2>
    <ul>
        <li><a href="dashboard.php">🏠 Dashboard</a></li>
        <li><a class="active" href="hotels.php">🏨 Hotels</a></li>
        <li><a href="menu.php">🍽 View Menu</a></li>
        <li><a href="orders.php">📦 My Orders</a></li>
        <li><a href="profile.php">👤 Profile</a></li>
        <li><a href="/DineFlow/logout.php">🔒 Logout</a></li>
    </ul>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">

<header>
    <h1>Select a Hotel</h1>
    <p>Choose your favorite hotel and explore delicious menus.</p>
</header>

<section class="hotel-container">

<?php if (!empty($hotels)): ?>
    
    <?php foreach ($hotels as $row): ?>
        <div class="hotel-card">
            <img src="../uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="Hotel Image">
            <div class="hotel-info">
                <h3><?php echo htmlspecialchars($row['hotel_name']); ?></h3>
                <p>📍 <?php echo htmlspecialchars($row['location']); ?></p>
                <a href="menu.php?hotel_id=<?php echo $row['id']; ?>" class="select-btn">
                    View Menu
                </a>
            </div>
        </div>
    <?php endforeach; ?>

<?php else: ?>
    <p>No hotels available right now.</p>
<?php endif; ?>


</section>

</div>

</body>
</html>
