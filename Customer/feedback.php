<?php
session_start();
include '../db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['hotel_id'])) {
    die("Hotel not selected");
}

$hotel_id = intval($_GET['hotel_id']);
?>

<!DOCTYPE html>
<html>
<head>
<title>Give Feedback</title>
<style>
body{
    background:#020617;
    color:#e5e7eb;
    font-family:Poppins;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}
.feedback-box{
    background:#0f172a;
    padding:30px;
    border-radius:15px;
    width:350px;
    text-align:center;
    box-shadow:0 0 25px rgba(56,189,248,0.3);
}
.stars input{display:none}
.stars label{
    font-size:30px;
    color:#334155;
    cursor:pointer;
    transition:0.3s;
}
.stars input:checked ~ label,
.stars label:hover,
.stars label:hover ~ label{
    color:#facc15;
}
textarea{
    width:100%;
    margin-top:15px;
    padding:10px;
    border-radius:8px;
    background:#020617;
    color:#fff;
    border:1px solid #38bdf8;
}
button{
    margin-top:15px;
    padding:10px;
    width:100%;
    background:#38bdf8;
    border:none;
    border-radius:8px;
    font-weight:bold;
    cursor:pointer;
}
</style>
</head>
<body>

<div class="feedback-box">
<h2>Rate Your Experience ⭐</h2>

<form method="POST" action="submit_feedback.php">
    <input type="hidden" name="hotel_id" value="<?= $hotel_id ?>">

    <div class="stars">
        <input type="radio" name="rating" value="5" id="s5" required><label for="s5">★</label>
        <input type="radio" name="rating" value="4" id="s4"><label for="s4">★</label>
        <input type="radio" name="rating" value="3" id="s3"><label for="s3">★</label>
        <input type="radio" name="rating" value="2" id="s2"><label for="s2">★</label>
        <input type="radio" name="rating" value="1" id="s1"><label for="s1">★</label>
    </div>

    <textarea name="message" placeholder="Write your feedback..." required></textarea>
    <button type="submit" name="submit_feedback">Submit</button>
</form>
</div>

</body>
</html>
