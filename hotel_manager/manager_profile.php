<?php
session_start();
include '../db.php';

if (!isset($_SESSION['hotel_id'])) {
    header("Location: ../login.php");
    exit();
}

$hotel_id = $_SESSION['hotel_id'];

// Fetch manager details
$stmt = $conn->prepare("SELECT * FROM managers WHERE hotel_id = ?");
if(!$stmt){
    die("Prepare failed (managers): (" . $conn->errno . ") " . $conn->error);
}
$stmt->bind_param("i", $hotel_id);
$stmt->execute();
$result = $stmt->get_result();
$manager = $result->fetch_assoc();

// Fetch hotel info
$stmt2 = $conn->prepare("SELECT hotel_name, location, image FROM hotels WHERE id = ?");
if(!$stmt2){
    die("Prepare failed (hotels): (" . $conn->errno . ") " . $conn->error);
}
$stmt2->bind_param("i", $hotel_id);
$stmt2->execute();
$hotel_result = $stmt2->get_result();
$hotel = $hotel_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manager | Profile</title>
<style>
* { margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI', sans-serif; }
body { background:#0b1220; color:#e5e7eb; }
.layout { display:flex; }
/* Sidebar */
.sidebar {
    width:240px; height:100vh; background:#020617; padding:20px; position:fixed;
    box-shadow: 2px 0 10px rgba(0,0,0,0.5);
}
.sidebar h2 { text-align:center; color:#38bdf8; margin-bottom:40px; font-size:22px; }
.sidebar a { display:block; padding:14px 16px; margin-bottom:12px; color:#cbd5f5; text-decoration:none; border-radius:10px; font-size:15px; }
.sidebar a:hover, .sidebar a.active { background:#1e293b; color:#38bdf8; }
/* Main Content */
.content { margin-left:240px; width:100%; padding:40px 50px; max-width:900px; }
/* Page Header */
.page-header { margin-bottom:30px; }
.page-header h1 { font-size:28px; margin-bottom:6px; }
.page-header p { color:#94a3b8; }
/* Card */
.card {
    background:#0f172a; border-radius:16px; padding:28px; margin-bottom:40px;
    box-shadow:0 10px 30px rgba(0,0,0,0.6); border:1px solid #1e293b;
    display:flex; flex-direction: column; align-items: center;
}
/* Profile Image */
.profile-img {
    width:130px; height:130px; border-radius:50%; object-fit:cover;
    border:3px solid #38bdf8; margin-bottom:20px;
}
/* Profile Info */
.profile-info { width:100%; margin-top:10px; }
.profile-info h2 { color:#38bdf8; margin-bottom:6px; }
.profile-info p { margin-bottom:12px; color:#cbd5f5; }
.profile-info span { font-weight:600; color:#94a3b8; }
/* Edit Button */
.edit-btn {
    margin-top:20px; background:#38bdf8; color:#020617; font-weight:600;
    padding:12px 24px; border:none; border-radius:12px; cursor:pointer; transition:0.3s;
}
.edit-btn:hover { background:#0ea5e9; }
/* Responsive */
@media(max-width:768px){
    .content { padding:30px 20px; }
    .card { padding:20px; }
}
</style>
</head>
<body>

<div class="layout">

    <div class="sidebar">
        <h2>DineFlow</h2>
        <a href="manager_dashboard.php">🏠 Dashboard</a>
        <a href="view_order.php">📦 View Orders</a>
        <a href="manager_menu.php">🍽 Manage Menu</a>
        <a href="profile.php" class="active">👤 Profile</a>
        <a href="../logout.php">🚪 Logout</a>
    </div>

    <div class="content">
        <div class="page-header">
            <h1>👤 Manager Profile</h1>
            <p>View your account details</p>
        </div>

        <div class="card">
            <!-- Profile Image -->
            <img src="../uploads/<?php echo $hotel['image'] ?? 'default.png'; ?>" alt="Profile Image" class="profile-img">

            <!-- Manager Info -->
            <div class="profile-info">
                <h2><?php echo $manager['username'] ?? 'Manager'; ?></h2>
                <p>Hotel: <span><?php echo $hotel['hotel_name'] ?? 'N/A'; ?></span></p>
                <p>Location: <span><?php echo $hotel['location'] ?? 'N/A'; ?></span></p>
                <p>Account Created: <span><?php echo $manager['created_at'] ?? 'N/A'; ?></span></p>
            </div>

            <button class="edit-btn" onclick="window.location.href='edit_profile_manager.php'">✏️ Edit Profile</button>
        </div>
    </div>

</div>

</body>
</html>
