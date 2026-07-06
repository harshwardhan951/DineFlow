<?php
session_start();
include '../db.php';

// Check admin login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: admin_login.php");
    exit;
}

// Check hotel ID
if (!isset($_GET['id'])) {
    header("Location: view_hotels.php");
    exit;
}

$id = intval($_GET['id']);

// Fetch hotel data
$result = mysqli_query($conn, "SELECT * FROM hotels WHERE id=$id");
$hotel = mysqli_fetch_assoc($result);

if (!$hotel) {
    header("Location: view_hotels.php");
    exit;
}
?>
<?php include 'admin_sidebar.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Hotel | Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/admin.css">

<style>
body{
    background:#0f172a;
    font-family:'Poppins', sans-serif;
}

/* MAIN CONTENT ALIGNMENT */
.admin-main{
    margin-left:240px;
    margin-top:80px;
    padding:40px;
    display:flex;
    justify-content:center;
    align-items:flex-start;
    min-height:100vh;
    background:#0f172a;
}

/* FORM CONTAINER */
.form-container{
    max-width:650px;
    background:#020617;
    padding:30px 35px;
    border-radius:18px;
    box-shadow:0 20px 60px rgba(0,0,0,0.6);
}

/* HEADINGS */
.form-container h2{
    color:#fff;
    margin-bottom:25px;
    text-align:center;
}

/* FORM GROUP */
.form-group{
    margin-bottom:18px;
}

.form-group label{
    display:block;
    color:#cbd5f5;
    margin-bottom:6px;
    font-size:14px;
}

.form-group input,
.form-group select{
    width:100%;
    padding:12px 14px;
    border-radius:10px;
    background:#020617;
    border:2px solid #1e293b;
    color:#fff;
    font-size:14px;
    outline:none;
    transition:0.3s;
}

.form-group input:focus,
.form-group input:hover,
.form-group select:focus,
.form-group select:hover{
    border-color:#3b82f6;
}

/* IMAGE */
.hotel-img{
    width:100%;
    height:190px;
    object-fit:cover;
    border-radius:12px;
    border:2px solid #1e293b;
}

/* BUTTON */
.btn{
    width:100%;
    padding:14px;
    border-radius:30px;
    border:none;
    background:linear-gradient(135deg,#2563eb,#3b82f6);
    color:#fff;
    font-size:16px;
    font-weight:500;
    cursor:pointer;
    transition:0.3s;
}

.btn:hover{
    transform:translateY(-2px);
    box-shadow:0 10px 25px rgba(59,130,246,0.5);
}
</style>
</head>

<body>

<!-- SIDEBAR -->
<?php include 'admin_sidebar.php'; ?>

<!-- MAIN -->
<div class="admin-main">

    <div class="form-container">
        <h2>🏨 Edit Hotel</h2>

        <form action="update_hotels.php" method="POST" enctype="multipart/form-data">

            <input type="hidden" name="id" value="<?= $hotel['id']; ?>">

            <div class="form-group">
                <label>Hotel Name</label>
                <input type="text" name="hotel_name"
                       value="<?= htmlspecialchars($hotel['hotel_name']); ?>" required>
            </div>

            <div class="form-group">
                <label>Location</label>
                <input type="text" name="location"
                       value="<?= htmlspecialchars($hotel['location']); ?>" required>
            </div>

            <div class="form-group">
                <label>Current Image</label>
                <?php if(!empty($hotel['image'])): ?>
                    <img src="../uploads/<?= $hotel['image']; ?>" class="hotel-img" alt="Hotel Image">
                <?php else: ?>
                    <p style="color:#f87171;">No image uploaded yet.</p>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Change Image (optional)</label>
                <input type="file" name="image">
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status">
                    <option value="active" <?= $hotel['status']=='active'?'selected':''; ?>>Active</option>
                    <option value="inactive" <?= $hotel['status']=='inactive'?'selected':''; ?>>Inactive</option>
                </select>
            </div>

            <button class="btn" type="submit">Update Hotel</button>
        </form>
    </div>

</div>

</body>
</html>
