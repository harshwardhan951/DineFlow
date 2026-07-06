<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: /DineFlow/login.php");
    exit();
}

include '../db.php';

$user_id = $_SESSION['user_id'];

/* ===== SECURE FETCH USER ===== */
$stmt = $conn->prepare("SELECT username, email, phone, address, profile_image FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    die("User not found.");
}

/* ===== PROFILE IMAGE FIX ===== */
$profileImage = "default.png";

if (!empty($user['profile_image']) && 
    file_exists("../uploads/profile/" . $user['profile_image'])) {
    $profileImage = $user['profile_image'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>DineFlow | Profile</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="../assets/css/customer.css">
<link rel="stylesheet" href="../assets/css/profile.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
.profile-card { display:flex; flex-wrap:wrap; gap:30px; margin-top:30px; }
.profile-left, .profile-right {
    background:#020617;
    padding:20px;
    border-radius:15px;
    box-shadow:0 10px 25px rgba(0,0,0,0.5);
}
.profile-left { flex:1 1 250px; text-align:center; }
.profile-left img {
    width:150px; height:150px;
    border-radius:50%;
    object-fit:cover;
    margin-bottom:15px;
    border:3px solid #38bdf8;
}
.upload-btn {
    display:inline-block;
    padding:8px 15px;
    background:#38bdf8;
    color:#020617;
    border-radius:25px;
    cursor:pointer;
    font-size:14px;
}
.profile-right { flex:2 1 400px; }
.profile-right .field { margin-bottom:20px; }
.profile-right label { display:block; margin-bottom:5px; font-weight:500; }
.profile-right input, .profile-right textarea {
    width:100%; padding:10px 12px;
    border-radius:8px;
    border:1px solid #1e293b;
    background:#0f172a;
    color:#e5e7eb;
}
.profile-right textarea { resize:none; }
.save-btn {
    background:#38bdf8;
    color:#020617;
    padding:10px 25px;
    border:none;
    border-radius:25px;
    cursor:pointer;
    font-size:16px;
}
.error { color:#ef4444; font-size:13px; margin-top:4px; }
@media(max-width:768px){
    .profile-card { flex-direction:column; }
}
</style>

<script>
function validateUsername(){
    const v=document.forms["profileForm"]["username"].value.trim();
    document.getElementById("usernameError").textContent =
        v.length<3?"Username must be at least 3 characters.":"";
}
function validateEmail(){
    const v=document.forms["profileForm"]["email"].value.trim();
    const r=/^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    document.getElementById("emailError").textContent =
        !r.test(v)?"Enter valid email.":"";
}
function validatePhone(){
    const v=document.forms["profileForm"]["phone"].value.trim();
    const r=/^[0-9]{10}$/;
    document.getElementById("phoneError").textContent =
        !r.test(v)?"Phone must be 10 digits.":"";
}
function validateAddress(){
    const v=document.forms["profileForm"]["address"].value.trim();
    document.getElementById("addressError").textContent =
        v.length<5?"Address must be at least 5 characters.":"";
}
function validateProfile(){
    validateUsername();
    validateEmail();
    validatePhone();
    validateAddress();
    const errors=document.querySelectorAll(".error");
    for(let e of errors) if(e.textContent!=="") return false;
    return true;
}
</script>
</head>
<body>

<div class="sidebar">
    <h2>DineFlow</h2>
    <ul>
        <li><a href="dashboard.php">🏠 Dashboard</a></li>
        <li><a href="hotels.php">🏨 Hotels</a></li>
        <li><a href="menu.php">🍽 View Menu</a></li>
        <li><a href="orders.php">📦 My Orders</a></li>
        <li><a class="active" href="profile.php">👤 Profile</a></li>
        <li><a href="/DineFlow/logout.php">🔒 Logout</a></li>
    </ul>
</div>

<div class="main-content">
<header>
    <h1>My Profile</h1>
    <p>Manage your personal information</p>
</header>

<section class="profile-card">

<div class="profile-left">
    <img src="../uploads/profile/<?php echo htmlspecialchars($profileImage); ?>" alt="Profile Image">
    
    <form name="profileForm" action="update_profile.php" 
          method="post" enctype="multipart/form-data"
          onsubmit="return validateProfile();">
        <label class="upload-btn">
            Change Photo
            <input type="file" name="profile_image" hidden>
        </label>
</div>

<div class="profile-right">

    <div class="field">
        <label>Username</label>
        <input type="text" name="username"
            value="<?php echo htmlspecialchars($user['username']); ?>"
            oninput="validateUsername()" required>
        <div class="error" id="usernameError"></div>
    </div>

    <div class="field">
        <label>Email</label>
        <input type="email" name="email"
            value="<?php echo htmlspecialchars($user['email']); ?>"
            oninput="validateEmail()" required>
        <div class="error" id="emailError"></div>
    </div>

    <div class="field">
        <label>Phone</label>
        <input type="text" name="phone"
            value="<?php echo htmlspecialchars($user['phone']); ?>"
            oninput="validatePhone()" required>
        <div class="error" id="phoneError"></div>
    </div>

    <div class="field">
        <label>Address</label>
        <textarea name="address"
            oninput="validateAddress()" required><?php 
            echo htmlspecialchars($user['address']); 
        ?></textarea>
        <div class="error" id="addressError"></div>
    </div>

    <button type="submit" class="save-btn">Save Changes</button>

    </form>
</div>

</section>
</div>

</body>
</html>
