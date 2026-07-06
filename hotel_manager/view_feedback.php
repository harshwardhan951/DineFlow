<?php
session_start();
include '../db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'manager') {
    header("Location: ../login.php");
    exit();
}

$hotel_id = $_SESSION['hotel_id'];

$result = $conn->query(
    "SELECT f.rating, f.message, f.created_at, u.username
     FROM feedback f
     JOIN users u ON f.customer_id = u.id
     WHERE f.hotel_id = $hotel_id
     ORDER BY f.created_at DESC"
);
?>

<!DOCTYPE html>
<html>
<head>
<title>Hotel Feedback</title>
<style>
body{background:#0f172a;color:#fff;font-family:Poppins}
.box{background:#020617;padding:15px;margin:15px;border-radius:10px}
.star{color:#facc15}
</style>
</head>
<body>

<h2>Customer Feedback</h2>

<?php while($row = $result->fetch_assoc()) { ?>
<div class="box">
    <strong><?= $row['username'] ?></strong><br>
    Rating:
    <?php for($i=0;$i<$row['rating'];$i++) echo "<span class='star'>★</span>"; ?>
    <p><?= $row['message'] ?></p>
    <small><?= $row['created_at'] ?></small>
</div>
<?php } ?>

</body>
</html>
